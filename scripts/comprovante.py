#!/usr/bin/env python3
"""Gera o comprovante de reserva em PDF e registra no CSV.

Exemplo:
  python3 scripts/comprovante.py --nome "Maria Silva" --doc 123.456.789-00 --loft 7 \
      --hospedes "2 adultos" --checkin 2026-10-16 --checkout 2026-10-18 --total 900
Opções: --idioma es (argentinos; --doc vira DNI/Pasaporte) · --sinal 450 (padrão: 50%)
        --forcar (gera mesmo com aviso de data estranha)
"""
import argparse, csv, html, os, subprocess, sys, tempfile, unicodedata
from datetime import date
from pathlib import Path
from string import Template

PASTA = Path.home() / "Library/Mobile Documents/com~apple~CloudDocs/Documents/Pousada/Comprovantes"
REGISTRO = PASTA / "registro_comprovantes.csv"
CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
AQUI = Path(__file__).resolve().parent
LOGO = (AQUI.parent / "images/logo_campeche_lofts_horizontal.webp").as_uri()

MESES = {"pt": "janeiro fevereiro março abril maio junho julho agosto setembro outubro novembro dezembro",
         "es": "enero febrero marzo abril mayo junio julio agosto septiembre octubre noviembre diciembre"}
TEXTOS = {
    "pt": dict(lang="pt-BR", eyebrow="Documento de hospedagem", titulo="Comprovante de reserva",
               confirmada="Reserva confirmada", sec_hospede="Hóspede responsável", l_nome="Nome completo",
               l_doc="CPF", l_acomodacao="Acomodação", l_hospedes="Hóspedes", sec_estadia="Detalhes da estadia",
               l_duracao="Duração", l_endereco="Endereço", sec_recibo="Recibo de pagamento", l_total="Valor total",
               l_sinal="Sinal recebido", l_saldo="Saldo no check-in", sec_recebido="Recebido por",
               socio="Sócio-proprietário", rodape="Campeche Lofts · Hospedagem tranquila em Florianópolis",
               intro="Este documento confirma a reserva do Loft {loft} e registra o recebimento do sinal informado para a estadia no Campeche Lofts.",
               recibo="Recebemos o valor de {sinal} referente ao sinal{pct} da reserva. O saldo remanescente deverá ser pago no check-in.",
               quitado="Recebemos o valor de {sinal} referente ao pagamento integral da reserva. Não há saldo a pagar no check-in.",
               emitido="Documento emitido em <strong>{data}</strong>.", noite="noite", noites="noites"),
    "es": dict(lang="es-AR", eyebrow="Documento de alojamiento", titulo="Comprobante de reserva",
               confirmada="Reserva confirmada", sec_hospede="Huésped titular", l_nome="Nombre completo",
               l_doc="DNI / Pasaporte", l_acomodacao="Alojamiento", l_hospedes="Huéspedes", sec_estadia="Detalles de la estadía",
               l_duracao="Duración", l_endereco="Dirección", sec_recibo="Recibo de pago", l_total="Valor total",
               l_sinal="Seña recibida", l_saldo="Saldo en el check-in", sec_recebido="Recibido por",
               socio="Socio propietario", rodape="Campeche Lofts · Alojamiento tranquilo en Florianópolis",
               intro="Este documento confirma la reserva del Loft {loft} y registra el pago de la seña para la estadía en Campeche Lofts.",
               recibo="Recibimos el valor de {sinal} correspondiente a la seña{pct} de la reserva. El saldo restante se abona en el check-in.",
               quitado="Recibimos el valor de {sinal} correspondiente al pago total de la reserva. No hay saldo a pagar en el check-in.",
               emitido="Documento emitido el <strong>{data}</strong>.", noite="noche", noites="noches"),
}
CAMPOS_CSV = ["numero", "emitido_em", "idioma", "nome", "documento", "loft", "hospedes",
              "checkin", "checkout", "noites", "total", "sinal", "saldo", "arquivo"]


def brl(v):
    return "R$ " + f"{v:,.2f}".replace(",", "X").replace(".", ",").replace("X", ".")


def data_extenso(d, idioma):
    return f"{d.day} de {MESES[idioma].split()[d.month - 1]} de {d.year}"


def proximo_numero(hoje):
    ano, n = str(hoje.year), 0
    if REGISTRO.exists():
        with open(REGISTRO, encoding="utf-8") as f:
            n = sum(1 for r in csv.DictReader(f) if r["numero"].startswith(ano + "-"))
    return f"{ano}-{n + 1:03d}"


def main():
    p = argparse.ArgumentParser(description="Gera comprovante de reserva em PDF")
    p.add_argument("--nome", required=True)
    p.add_argument("--doc", required=True, help="CPF (pt) ou DNI/Passaporte (es)")
    p.add_argument("--loft", required=True, type=int, choices=range(3, 10))
    p.add_argument("--hospedes", required=True, help='ex.: "2 adultos" ou "2 adultos e 1 criança"')
    p.add_argument("--checkin", required=True, type=date.fromisoformat, help="AAAA-MM-DD")
    p.add_argument("--checkout", required=True, type=date.fromisoformat, help="AAAA-MM-DD")
    p.add_argument("--total", required=True, type=float)
    p.add_argument("--sinal", type=float, help="padrão: 50%% do total")
    p.add_argument("--idioma", choices=["pt", "es"], default="pt")
    p.add_argument("--forcar", action="store_true")
    a = p.parse_args()

    hoje = date.today()
    sinal = round(a.total / 2, 2) if a.sinal is None else a.sinal
    noites = (a.checkout - a.checkin).days
    if noites <= 0 or a.total <= 0 or not 0 < sinal <= a.total:
        sys.exit("ERRO: confira as datas (check-out depois do check-in) e os valores.")
    avisos = []
    if a.checkin < hoje:
        avisos.append(f"check-in {a.checkin} já passou")
    if (a.checkin - hoje).days > 300:
        avisos.append(f"check-in {a.checkin} está a mais de 300 dias (ano digitado certo?)")
    if noites > 60:
        avisos.append(f"{noites} noites")
    if avisos and not a.forcar:
        sys.exit("AVISO: " + "; ".join(avisos) + ". Se estiver certo, rode de novo com --forcar.")

    t, saldo = TEXTOS[a.idioma], round(a.total - sinal, 2)
    pct = round(sinal / a.total * 100)
    numero = proximo_numero(hoje)
    recibo = (t["quitado"] if saldo == 0 else t["recibo"]).format(sinal=brl(sinal), pct=f" de {pct}%")
    valores = dict(t, numero=numero, logo=LOGO, nome=html.escape(a.nome), documento=html.escape(a.doc),
                   loft=f"{a.loft:02d}", hospedes=html.escape(a.hospedes),
                   checkin=data_extenso(a.checkin, a.idioma), checkout=data_extenso(a.checkout, a.idioma),
                   noites=f"{noites} {t['noite'] if noites == 1 else t['noites']}",
                   total=brl(a.total), sinal=brl(sinal), saldo=brl(saldo),
                   intro=t["intro"].format(loft=f"{a.loft:02d}"), texto_recibo=recibo,
                   emitido=t["emitido"].format(data=data_extenso(hoje, a.idioma)))
    pagina = Template((AQUI / "comprovante.html").read_text(encoding="utf-8")).substitute(valores)

    PASTA.mkdir(parents=True, exist_ok=True)
    slug = "_".join(unicodedata.normalize("NFKD", a.nome).encode("ascii", "ignore").decode().lower().split())
    pdf = PASTA / f"{numero}_comprovante_loft{a.loft:02d}_{slug}.pdf"
    with tempfile.NamedTemporaryFile("w", suffix=".html", encoding="utf-8", delete=False) as f:
        f.write(pagina)
    try:
        subprocess.run([CHROME, "--headless", "--disable-gpu", "--no-pdf-header-footer",
                        "--allow-file-access-from-files", f"--print-to-pdf={pdf}", Path(f.name).as_uri()],
                       check=True, capture_output=True, timeout=60)
    finally:
        os.unlink(f.name)

    novo = not REGISTRO.exists()
    with open(REGISTRO, "a", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, CAMPOS_CSV)
        if novo:
            w.writeheader()
        w.writerow(dict(numero=numero, emitido_em=hoje.isoformat(), idioma=a.idioma, nome=a.nome,
                        documento=a.doc, loft=a.loft, hospedes=a.hospedes, checkin=a.checkin,
                        checkout=a.checkout, noites=noites, total=f"{a.total:.2f}",
                        sinal=f"{sinal:.2f}", saldo=f"{saldo:.2f}", arquivo=pdf.name))
    print(f"Comprovante {numero} gerado: {pdf}")


if __name__ == "__main__":
    main()
