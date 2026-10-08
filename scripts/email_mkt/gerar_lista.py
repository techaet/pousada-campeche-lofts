#!/usr/bin/env python3
"""Junta os leads (planilha + CSV do CRM) e gera as listas PT e ES para importar no Brevo.

Uso:
  python3 scripts/email_mkt/gerar_lista.py --planilha planilha_leads.csv --crm ~/Downloads/leads-pousada-2025.csv --saida "<pasta>"

--planilha: CSV com colunas origem,nome,email,telefone,idioma (cópia das abas do Google Sheets).
--crm:      CSV exportado do CRM (colunas Nome, Email, Telefone, Formulário).
Saída: lista_pt.csv e lista_es.csv (colunas EMAIL,FIRSTNAME) + resumo.txt. Nada disso vai para o repositório.
"""
import argparse, csv, re
from pathlib import Path

EMAIL = re.compile(r"[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}")
INTERNOS = {"campechelofts.floripa.br", "aetsolidez.com.br"}  # nossos próprios endereços
NOMES_RUINS = {"yo", "negro", "autoescuela", "unassigned"}
PREFIXOS_ES = ("54", "59", "56", "51", "57", "58")  # AR, UY/PY, CL, PE, CO, VE


def primeiro_nome(nome):
    """Primeiro nome só se parecer nome de pessoa; senão vazio (o e-mail cai em 'Olá,')."""
    t = (nome or "").split()
    if not t or not re.fullmatch(r"[A-Za-zÀ-ÿ'\-]{3,}", t[0]) or t[0].lower() in NOMES_RUINS:
        return ""
    return t[0].capitalize()


def idioma(tel, fallback):
    d = re.sub(r"\D", "", tel or "")
    if d.startswith("55") and len(d) >= 12:
        return "pt"
    if d.startswith(PREFIXOS_ES):
        return "es"
    return fallback


def coletar(planilha, crm):
    """Fontes por ordem de qualidade de nome: formulários (planilha brasil/site, CRM) antes do robô."""
    linhas = []
    for r in csv.DictReader(open(planilha, encoding="utf-8-sig")):
        linhas.append((r["origem"] == "robo", r["nome"], r["email"], r["telefone"], "es" if r["idioma"] in ("es", "en") else "pt"))
    for r in csv.DictReader(open(crm, encoding="utf-8-sig")):
        fb = "es" if "Argentina" in r["Formulário"] else "pt"
        linhas.append((False, r["Nome"], r["Email"], r["Telefone"], fb))
    return sorted(linhas, key=lambda x: x[0])  # estável: formulários primeiro


def montar(linhas):
    vistos, saida, descartados = set(), {"pt": [], "es": []}, 0
    for _, nome, bruto, tel, fb in linhas:
        m = EMAIL.search(bruto or "")  # tira lixo como "x@gmail.com 8 adultos"
        if not m:
            descartados += 1
            continue
        email = m.group(0).lower()
        if email in vistos or email.split("@")[1] in INTERNOS:
            continue
        vistos.add(email)
        saida[idioma(tel, fb)].append((email, primeiro_nome(nome)))
    return saida, descartados


def demo():
    s, d = montar([(False, "ana lima", "Ana@Gmail.com 8 adultos", "5554999999999", "pt"),
                   (True, "Yo", "ana@gmail.com", "5493815946198", "es"),
                   (True, "Belu", "b@hotmail.com", "5493401433157", "es"),
                   (False, "Leo", "leonardo@campechelofts.floripa.br", "554891223600", "pt"),
                   (False, "Sem", "somos 4 adultos", "5493544556567", "es")])
    assert s == {"pt": [("ana@gmail.com", "Ana")], "es": [("b@hotmail.com", "Belu")]} and d == 1, (s, d)


if __name__ == "__main__":
    demo()
    ap = argparse.ArgumentParser()
    ap.add_argument("--planilha", required=True)
    ap.add_argument("--crm", required=True)
    ap.add_argument("--saida", required=True)
    a = ap.parse_args()
    out = Path(a.saida).expanduser()
    out.mkdir(parents=True, exist_ok=True)
    listas, descartados = montar(coletar(a.planilha, Path(a.crm).expanduser()))
    for lg, rows in listas.items():
        with open(out / f"lista_{lg}.csv", "w", newline="", encoding="utf-8") as f:
            w = csv.writer(f)
            w.writerow(["EMAIL", "FIRSTNAME"])
            w.writerows(rows)
    (out / "resumo.txt").write_text(
        f"PT: {len(listas['pt'])} contatos\nES: {len(listas['es'])} contatos\nSem e-mail válido (descartados): {descartados}\n")
    print(open(out / "resumo.txt").read())
