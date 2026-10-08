#!/usr/bin/env python3
"""Registra um artigo já escrito em blog/index.html, blog/feed.xml e sitemap.xml.
Uso: python3 scripts/publicar_artigo.py <slug>
Lê título, descrição, tema (kicker), capa e idioma do próprio artigo e usa a data de hoje
como data de publicação. No fim roda sincronizar.py (tema, chips, home, menu). Rodar de novo para o mesmo slug não duplica nada."""
import html, re, sys
from email.utils import format_datetime
from datetime import date, datetime, timedelta, timezone
from pathlib import Path
from xml.sax.saxutils import escape

sys.path.insert(0, str(Path(__file__).parent))
from comprovante import MESES
import sincronizar

RAIZ = Path(__file__).resolve().parent.parent
BASE = "https://www.campechelofts.floripa.br"


def meta(s, padrao):
    m = re.search(padrao, s, re.S)
    if not m:
        sys.exit(f"ERRO: não achei {padrao!r} no artigo")
    return html.unescape(re.sub(r"<[^>]+>", "", m.group(1))).strip()


def main(slug):
    artigo = RAIZ / "blog" / slug / "index.html"
    s = artigo.read_text(encoding="utf-8")
    hoje = date.today()
    s = re.sub(r'"(datePublished|dateModified)":"[0-9-]+"', lambda m: f'"{m.group(1)}":"{hoje}"', s)
    artigo.write_text(s, encoding="utf-8")

    es = meta(s, r'<html lang="([^"]+)"').startswith("es")
    idioma = "es" if es else "pt"
    titulo = meta(s, r'property="og:title" content="([^"]+)"')
    descricao = meta(s, r'property="og:description" content="([^"]+)"')
    kicker = meta(s, r'class="article-kicker">(.*?)</p>')  # com ou sem o <a class="article-tema">: meta() tira as tags
    capa = meta(s, r'class="article-cover"><img src="(?:\.\./\.\./|/|' + re.escape(BASE) + r'/)([^"]+)"')
    alt = meta(s, r'class="article-cover"><img [^>]*alt="([^"]*)"')
    url = f"{BASE}/blog/{slug}/"
    data_txt = f"{hoje.day} de {MESES[idioma].split()[hoje.month - 1]} de {hoje.year}"
    agora = format_datetime(datetime.now(timezone(timedelta(hours=-3))).replace(microsecond=0))
    h = lambda t: html.escape(t, quote=True)

    indice = RAIZ / "blog/index.html"
    s = indice.read_text(encoding="utf-8")
    if f'href="{slug}/"' not in s:
        s = s.replace('fetchpriority="high"', 'loading="lazy"', 1)  # só o 1º card é prioritário
        hreflang = ' hreflang="es"' if es else ""
        rotulo = f"Ler artigo (em espanhol): {titulo}" if es else f"Ler artigo sobre {titulo}"
        card = (f'          <a class="article-card" href="{slug}/" aria-label="{h(rotulo)}"{hreflang}>\n'
                f'            <div class="article-card-media"><img src="../{capa}" alt="{h(alt)}" width="1600" height="1067" fetchpriority="high" decoding="async"></div>\n'
                f'            <div class="article-card-body"><span class="article-card-kicker">{"🇦🇷 " if es else ""}{h(kicker)}</span><h2>{h(titulo)}</h2>'
                f'<p>{h(descricao)}</p><div class="article-card-footer"><span>{data_txt}</span><span>{"Leer artículo" if es else "Ler artigo"}</span></div></div>\n'
                f'          </a>\n')
        grade = '<div class="blog-grid" id="lista-artigos">\n'
        assert grade in s, "blog/index.html sem a grade #lista-artigos"
        s = s.replace(grade, grade + card, 1)
        indice.write_text(s, encoding="utf-8")

    feed = RAIZ / "blog/feed.xml"
    s = feed.read_text(encoding="utf-8")
    if f"<link>{url}</link>" not in s:
        item = (f"    <item>\n      <title>{escape(titulo)}</title>\n      <link>{url}</link>\n"
                f'      <guid isPermaLink="true">{url}</guid>\n      <pubDate>{agora}</pubDate>\n'
                f"      <description>{escape(descricao)}</description>\n    </item>\n")
        s = re.sub(r"<lastBuildDate>[^<]*</lastBuildDate>\n", f"<lastBuildDate>{agora}</lastBuildDate>\n" + item, s, count=1)
        feed.write_text(s, encoding="utf-8")

    sitemap = RAIZ / "sitemap.xml"
    s = sitemap.read_text(encoding="utf-8")
    s = re.sub(r"(<loc>" + re.escape(BASE) + r"/blog/</loc>\s*<lastmod>)[0-9-]+", rf"\g<1>{hoje}", s)
    if f"<loc>{url}</loc>" not in s:
        s = s.replace("</urlset>", f"  <url>\n    <loc>{url}</loc>\n    <lastmod>{hoje}</lastmod>\n"
                                   f"    <changefreq>yearly</changefreq>\n    <priority>0.5</priority>\n  </url>\n</urlset>")
    sitemap.write_text(s, encoding="utf-8")
    sincronizar.main()
    print(f"Registrado: {url}")


if __name__ == "__main__":
    if len(sys.argv) != 2:
        sys.exit(__doc__)
    main(sys.argv[1].strip("/"))
