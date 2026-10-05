#!/usr/bin/env python3
"""Mantém o que se repete no site a partir de uma fonte só (scripts/site.json). Idempotente.
Uso: python3 scripts/sincronizar.py   (publicar_artigo.py já chama)
Faz, em português e espanhol (pelo <html lang>):
- menu (nav.site-nav) igual em todas as páginas com menu, com busca (Pagefind) e aria-current no blog;
- <head> padrão: RSS, CSS do hub e do menu unificado, busca, ?v=<hash> nos CSS locais;
- em cada artigo: tema (kicker) como link, marcação do Pagefind;
- no blog: chips de tema, "Comece por aqui" e data-tema nos cards; na home: bloco "Do blog" com os 3 mais recentes."""
import hashlib, html, json, re, sys
from pathlib import Path

RAIZ = Path(__file__).resolve().parent.parent
CFG = json.loads((RAIZ / "scripts/site.json").read_text(encoding="utf-8"))
TEMAS = {l: {t["nome"][l]: t["slug"] for t in CFG["temas"]} for l in ("pt", "es")}
h = lambda t: html.escape(t, quote=True)
ler = lambda p: p.read_text(encoding="utf-8")


def gravar(p, s):
    if ler(p) != s:
        p.write_text(s, encoding="utf-8")


def idioma(s):
    return "es" if re.search(r'<html lang="es', s) else "pt"


def trocar_bloco(s, marca, conteudo):
    return re.sub(rf"<!--{marca}-->.*?<!--/{marca}-->", lambda _: f"<!--{marca}-->{conteudo}<!--/{marca}-->", s, count=1, flags=re.S)


# ---------- menu e <head> ----------
def nav(lang, atual):
    m = CFG["menu"][lang]
    links = "".join(f'<a href="{u}"' + (' aria-current="page"' if u == "/blog/" and atual == "blog/index.html" else "") + f">{n}</a>"
                    for n, u in m["itens"])
    busca = f'<div class="nav-busca"><pagefind-modal-trigger placeholder="{h(m["busca"])}" hide-shortcut></pagefind-modal-trigger></div>'
    return (f'<nav class="site-nav" aria-label="{m["aria"]}">{links}{busca}'
            f'<a class="nav-reserve" href="{m["cta"][1]}">{m["cta"][0]}</a></nav>')


HEAD_TODAS = [  # (prova de que já existe, linha) — páginas com og:title
    ('type="application/rss+xml"', f'<link rel="alternate" type="application/rss+xml" title="{h(CFG["feed_titulo"])}" href="/blog/feed.xml">'),
]
HEAD_MENU = [   # páginas com menu
    ("navegacao-unificada.css", '<link rel="stylesheet" href="/navegacao-unificada.css">'),
    ("/hub.css", '<link rel="stylesheet" href="/hub.css">'),
    ("pagefind-component-ui.css", '<link href="/pagefind/pagefind-component-ui.css" rel="stylesheet">'),
    ("pagefind-component-ui.js", '<script src="/pagefind/pagefind-component-ui.js" type="module"></script>'),
]


def versionar_css(p, s):
    """?v=<hash> nos CSS locais: o navegador baixa de novo quando o CSS muda (HTML novo + CSS velho em cache quebra o layout)."""
    def v(m):
        href = m.group(2)
        alvo = (RAIZ / href.lstrip("/")) if href.startswith("/") else (p.parent / href)
        if href.startswith(("/pagefind/", "http")) or not alvo.exists():
            return m.group(0)
        return f'{m.group(1)}{href}?v={hashlib.md5(alvo.read_bytes()).hexdigest()[:8]}{m.group(3)}'
    return re.sub(r'(<link [^>]*?href=")([^"?]+\.css)(?:\?v=[0-9a-f]+)?(")', v, s)


def paginas():
    ign = set(CFG.get("paginas_ignoradas", []))
    return [p for p in sorted(RAIZ.glob("**/*.html"))
            if not any(x in p.parts for x in ("scripts", "guia", ".git", "pagefind", "node_modules"))
            and not p.name.startswith("google") and str(p.relative_to(RAIZ)) not in ign
            and p.name not in ("guia-do-hospede.html", "guest-guide.html", "guia-del-huesped.html")]


def pagina(p):
    s, rel = ler(p), str(p.relative_to(RAIZ))
    if 'class="site-nav"' in s and rel not in CFG["sem_menu"]:
        s = re.sub(r'<nav class="site-nav".*?</nav>', lambda _: nav(idioma(s), rel), s, count=1, flags=re.S)
        if "<pagefind-modal>" not in s:
            s = s.replace("<main", "<pagefind-modal></pagefind-modal>\n  <main", 1)
        for prova, linha in HEAD_MENU:
            if prova not in s:
                s = re.sub(r'(<link rel="stylesheet" href="[^"]*campeche\.css[^"]*">)', lambda m: m.group(1) + "\n  " + linha, s, count=1) \
                    if linha.startswith('<link rel="stylesheet"') else s.replace("</head>", f"  {linha}\n</head>", 1)
    if 'property="og:title"' in s:
        for prova, linha in HEAD_TODAS:
            if prova not in s:
                s = s.replace("</head>", f"  {linha}\n</head>", 1)
    gravar(p, versionar_css(p, s))


# ---------- artigos ----------
def artigo(p):
    s, slug = ler(p), p.parent.name
    lang = idioma(s)
    m = re.search(r'<p class="article-kicker">(.*?)</p>', s, re.S)
    nome = html.unescape(re.sub(r"<[^>]+>", "", m.group(1))).strip() if m else ""
    if nome not in TEMAS[lang]:
        sys.exit(f"ERRO {slug}: o kicker ({nome!r}) deve ser um tema de scripts/site.json em {lang}: {' | '.join(TEMAS[lang])}")
    tema = f'<p class="article-kicker"><a class="article-tema" href="/blog/?tema={TEMAS[lang][nome]}" data-pagefind-filter="Tema">{h(nome)}</a></p>'
    s = re.sub(r'<p class="article-kicker">.*?</p>', lambda _: tema, s, count=1, flags=re.S)
    s = s.replace('<main id="conteudo">', '<main id="conteudo" data-pagefind-body>', 1)
    for velho in ('<nav class="article-breadcrumb"', '<nav class="article-toc"', '<div class="article-cta"',
                  '<section class="article-related"', '<aside class="article-aside"'):
        s = s.replace(velho, velho + " data-pagefind-ignore", 1) if velho in s and (velho + " data-pagefind-ignore") not in s else s
    capa = re.search(r'class="article-cover"><img src="(?:\.\./\.\./|/)([^"]+)"', s)
    meta = f'<meta data-pagefind-meta="image[content]" content="/{capa.group(1)}">' if capa else ""
    if meta and meta not in s:
        s = s.replace('data-pagefind-body>', 'data-pagefind-body>' + meta, 1)
    gravar(p, s)
    return slug, lang, TEMAS[lang][nome]


def chips(total, contagem):
    itens = [f'<a class="tema-chip" href="/blog/" data-tema="" aria-current="true">Todos <span>{total}</span></a>']
    itens += [f'<a class="tema-chip" href="/blog/?tema={t["slug"]}" data-tema="{t["slug"]}">{h(t["nome"]["pt"])} <span>{contagem[t["slug"]]}</span></a>'
              for t in CFG["temas"] if contagem.get(t["slug"])]
    return '<nav class="tema-chips" aria-label="Filtrar por tema">' + "".join(itens) + "</nav>"


def blog_e_home(info):
    contagem = {}
    for _, (_, tema) in info.items():
        contagem[tema] = contagem.get(tema, 0) + 1
    total = len(info)
    nome_tema = {t["slug"]: t["nome"] for t in CFG["temas"]}

    indice = RAIZ / "blog/index.html"
    t = ler(indice)

    def card(x):
        c = x.group(0)
        slug = re.search(r'href="([a-z0-9-]+)/"', c).group(1)
        lang, tema = info[slug]
        c = re.sub(r'<a class="article-card"( data-tema="[^"]*")?', f'<a class="article-card" data-tema="{tema}"', c, count=1)
        rotulo = ("🇦🇷 " if lang == "es" else "") + h(nome_tema[tema][lang])
        return re.sub(r'(<span class="article-card-kicker">).*?(</span>)', lambda y: y.group(1) + rotulo + y.group(2), c, count=1)
    t = re.sub(r'<a class="article-card"[^>]*>.*?</a>', card, t, flags=re.S)
    cartoes = {re.search(r'href="([a-z0-9-]+)/"', c).group(1): c for c in re.findall(r'<a class="article-card"[^>]*>.*?</a>', t, re.S)}

    t = trocar_bloco(t, "chips", chips(total, contagem))
    destaques = "".join(cartoes[s].replace('fetchpriority="high"', 'loading="lazy"') for s in CFG["comece_por_aqui"])
    t = trocar_bloco(t, "destaques", f'<div class="blog-destaques"><p class="eyebrow">Comece por aqui</p>'
                                     f'<div class="blog-grid blog-grid--3">{destaques}</div></div>')
    gravar(indice, t)

    recentes = list(cartoes.values())[:3]
    para_home = "".join(re.sub(r'(src|href)="\.\./', r'\1="', re.sub(r'href="([a-z0-9-]+)/"', r'href="blog/\1/"', c)).replace('fetchpriority="high"', 'loading="lazy"')
                        for c in recentes)
    H = CFG["home"]
    bloco = ('<section class="section section-sand home-blog" id="blog" aria-labelledby="titulo-blog"><div class="container">'
             f'<div class="blog-intro"><div><p class="eyebrow">{h(H["rotulo"])}</p><h2 class="section-heading" id="titulo-blog">{h(H["titulo"])}</h2></div>'
             f'<p class="section-copy">{h(H["texto"])}</p></div>'
             + chips(total, contagem).replace('aria-current="true"', "")
             + f'<div class="blog-grid blog-grid--3">{para_home}</div>'
             f'<p class="home-blog-link"><a class="button" href="blog/">{h(H["ver_todos"])}</a></p></div></section>')
    home = RAIZ / "index.html"
    gravar(home, trocar_bloco(ler(home), "blog-home", bloco))


def main():
    for p in paginas():
        pagina(p)
    info = {}
    for p in sorted((RAIZ / "blog").glob("*/index.html")):
        slug, lang, tema = artigo(p)
        info[slug] = (lang, tema)
    blog_e_home(info)
    print(f"Sincronizado: {len(info)} artigos.")


if __name__ == "__main__":
    main()
