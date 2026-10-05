#!/usr/bin/env python3
"""Verifica o site antes do deploy. Uso: python3 scripts/check_site.py
Sai com código 1 se houver ERRO (bloqueia o deploy no GitHub Actions)."""
import os, re, subprocess, sys, urllib.parse

BASE = "https://www.campechelofts.floripa.br"
GA4 = "G-E857NMXM15"
WHATSAPP = "554861369146"
# Páginas que não são conteúdo indexável (redirecionamentos, verificação, modelo)
NAO_PUBLICAS = {"404.html", "google7ce71e8fb9f4b842.html", "template_loft.html",
                "guia-do-hospede.html", "guest-guide.html", "guia-del-huesped.html"}
IMG_LIMITE_KB = 500
# Texto único dos links de WhatsApp (menos o formulário de cotação e o guia/). Ver CLAUDE.md
WA_TEXTOS = {"Olá! Vim pelo site do Campeche Lofts e gostaria de mais informações.",
             "¡Hola! Vine desde el sitio de Campeche Lofts y quisiera más información."}

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import sincronizar as sync  # fonte do menu, dos temas e do ?v= dos CSS
os.chdir(os.path.join(os.path.dirname(os.path.abspath(__file__)), ".."))
erros, avisos = [], []
ler = lambda f: open(f, encoding="utf-8", errors="ignore").read()
arquivos = subprocess.check_output(["git", "ls-files", "--cached", "--others", "--exclude-standard"]).decode().split("\n")
htmls = [f for f in arquivos if f.endswith((".html", ".php")) and not f.startswith("scripts/")]


def url_de(f):
    if f == "index.html":
        return BASE + "/"
    return BASE + "/" + (f[:-len("index.html")] if f.endswith("/index.html") else f)


def existe(origem, ref):
    p = urllib.parse.unquote(ref)
    p = p.lstrip("/") if p.startswith("/") else os.path.join(os.path.dirname(origem), p)
    p = os.path.normpath(p)
    if os.path.isdir(p):
        return any(os.path.exists(os.path.join(p, i)) for i in ("index.html", "index.php"))
    return os.path.exists(p)


for f in htmls:
    s = ler(f)
    if "aetsolidez.com.br" in s:
        erros.append(f"{f}: ainda cita o domínio antigo aetsolidez.com.br")
    for n in set(re.findall(r"wa\.me/(\d+)", s)) - {WHATSAPP}:
        erros.append(f"{f}: WhatsApp {n} diferente de {WHATSAPP}")
    if not f.startswith("guia/"):
        for q in re.findall(r'href="https://wa\.me/\d+([^"]*)"', s):
            if urllib.parse.unquote(q[len("?text="):] if q.startswith("?text=") else "?") not in WA_TEXTOS:
                erros.append(f"{f}: link de WhatsApp fora do texto padrão -> {urllib.parse.unquote(q) or '(sem texto)'}")
    for ref in re.findall(r'(?:src|href)="([^"#?]+)', s):
        if "${" in ref or "{{" in ref:
            continue
        if ref.startswith(BASE):
            ref = ref[len(BASE):] or "/"
        elif re.match(r"^(https?:|mailto:|tel:|data:|//|javascript:)", ref):
            continue
        if not existe(f, ref):
            erros.append(f"{f}: link/imagem quebrado -> {ref}")
    if f.endswith(".php") or f in NAO_PUBLICAS:
        continue
    m = re.search(r'rel="canonical" href="([^"]+)"', s)
    if not m or m.group(1) != url_de(f):
        erros.append(f"{f}: canonical deveria ser {url_de(f)} (está {m.group(1) if m else 'ausente'})")
    if GA4 not in s:
        erros.append(f"{f}: sem Google Analytics ({GA4})")

# Hub de conteúdo: menu igual em todas as páginas com menu e CSS versionado (rode scripts/sincronizar.py se falhar)
for p in sync.paginas():
    f, s = str(p.relative_to(sync.RAIZ)), ler(str(p))
    if 'class="site-nav"' in s and f not in sync.CFG["sem_menu"] and sync.nav(sync.idioma(s), f) not in s:
        erros.append(f"{f}: menu fora do padrão — rode python3 scripts/sincronizar.py")
    if sync.versionar_css(p, s) != s:
        erros.append(f"{f}: ?v= dos CSS desatualizado — rode python3 scripts/sincronizar.py")

# Blog: cada artigo precisa estar no padrão e nas 3 listagens
sitemap, feed, indice = ler("sitemap.xml"), ler("blog/feed.xml"), ler("blog/index.html")
capas = {}
for f in sorted(x for x in htmls if re.fullmatch(r"blog/[^/]+/index\.html", x)):
    slug, s = f.split("/")[1], ler(f)
    url = f"{BASE}/blog/{slug}/"
    for nome, conteudo, alvo in (("sitemap.xml", sitemap, url), ("blog/feed.xml", feed, url), ("blog/index.html", indice, f'href="{slug}/"')):
        if alvo not in conteudo:
            erros.append(f"{f}: falta em {nome}")
    for trecho, o_que in (('class="site-header"', "cabeçalho padrão"), ('class="site-footer"', "rodapé padrão"),
                          ("campeche.css", "campeche.css"), ("campeche.js", "campeche.js"),
                          ('"@type":"BlogPosting"', "JSON-LD BlogPosting"), ('class="article-cover"', "imagem de capa"),
                          ('class="article-tema"', "tema no kicker (rode scripts/sincronizar.py; o kicker deve ser um tema de scripts/site.json)")):
        if trecho not in s:
            erros.append(f"{f}: sem {o_que} (copie a estrutura de um artigo existente)")
    if re.search(r"<p>\s*[-*] ", s) or "**" in s:
        erros.append(f"{f}: markdown solto no HTML ('- ' ou '**'); use <ul><li>/<strong>")
    capa = re.search(r'class="article-cover"><img src="([^"]+)"', s)
    if capa:
        nome = os.path.basename(capa.group(1))
        if nome in capas:
            erros.append(f"{f}: capa {nome} já usada em {capas[nome]}")
        capas[nome] = f
for loc in re.findall(r"<loc>([^<]+)</loc>", sitemap):
    if not loc.startswith(BASE) or not existe("", loc[len(BASE):] or "/"):
        erros.append(f"sitemap.xml: URL sem página correspondente -> {loc}")

for f in arquivos:
    if f.lower().endswith((".jpg", ".jpeg", ".png", ".webp")) and os.path.exists(f) and os.path.getsize(f) > IMG_LIMITE_KB * 1024:
        avisos.append(f"{f}: {os.path.getsize(f) // 1024} KB (ideal < {IMG_LIMITE_KB} KB, WebP)")

for a in avisos:
    print("AVISO", a)
for e in erros:
    print("ERRO ", e)
print(f"\n{len(erros)} erro(s), {len(avisos)} aviso(s).")
sys.exit(1 if erros else 0)
