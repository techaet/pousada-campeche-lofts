# Campeche Lofts — site da pousada

Site estático (HTML/CSS/JS puro, sem build) da Pousada Campeche Lofts, Praia do Campeche, Florianópolis.
Produção: **https://www.campechelofts.floripa.br** (domínio único e canônico; `pousada.aetsolidez.com.br` e variantes só redirecionam via `.htaccess`).

## Regra de ouro

**Antes de todo commit:** `python3 scripts/check_site.py` precisa terminar com `0 erro(s)`.
O mesmo script roda no GitHub Actions e **bloqueia o deploy** se falhar.

## Deploy

- Push na `main` → `.github/workflows/deploy.yml` → verificação → FTP (FTPS) para a hospedagem (cPanel). Não há staging: push na main = no ar em ~30 s.
- Arquivos que não sobem: `CLAUDE.md`, `.claude/`, `scripts/`, `template_loft.html`, `.git*`. Novo arquivo interno → adicionar ao `exclude` do workflow.
- Sempre `git pull` antes de começar: há commits feitos por automação (autor "Tech AET").

## Mapa do site

| Caminho | O que é |
|---|---|
| `index.html` | Home (prova social, disponibilidade, FAQ `#faq`, catálogo `#lofts`, `#localizacao`, `#contato`) |
| `loft_03.html` … `loft_09.html` | Uma página por loft (fotos em `images/loftNN/`). `template_loft.html` é só o modelo com placeholders `{{...}}` |
| `galeria_geral.html`, `planejador.html` | Galeria geral e planejador de viagem |
| `blog/` | Índice (`blog/index.html`), RSS (`blog/feed.xml`) e um artigo por pasta `blog/<slug>/index.html` |
| `guia-floripa-argentinos/` | Guia público em espanhol (isca para argentinos) |
| `guia/` | **Guia do Hóspede protegido por senha (PHP).** Contém Wi‑Fi e instruções internas. Não copiar conteúdo dele para páginas públicas; não mexer em `session.php` (hash da senha) sem pedido explícito. Bloqueado no `robots.txt` |
| `guia-do-hospede.html`, `guest-guide.html`, `guia-del-huesped.html` | Redirecionamentos para `/guia/` |
| `hub.css`, `blog/blog.js` | Estilos e filtro do hub de conteúdo (chips de tema, "Comece por aqui", bloco "Do blog" da home). `campeche.css` é minificado: o que for novo do blog vai em `hub.css` |
| `scripts/site.json`, `scripts/sincronizar.py` | Fonte única: marca, **menu pt/es**, **temas pt/es** e "Comece por aqui". O `sincronizar.py` gera o menu de todas as páginas, o `<head>` padrão (RSS, CSS, `?v=` dos CSS), o tema de cada artigo, os chips e cards do blog e o bloco "Do blog" da home. **Não edite à mão** esses trechos |
| `campeche.css` / `campeche.js` | CSS e JS únicos do site (minificados, editar com cuidado) |

## Dados fixos (não inventar outros)

- WhatsApp: `554861369146` (robô de atendimento, Sra. Constância) → `https://wa.me/554861369146?text=...` (mensagem pré-preenchida no idioma da página). O número humano `5548991223600` só continua no Guia do Hóspede (`guia/`).
- **Texto único dos links de WhatsApp** (botão flutuante, contato, lofts, blog, 404…; só o formulário de cotação e o `guia/` ficam de fora). Nunca inventar outro, nem frase por artigo ou por loft:
  - pt: `Olá! Vim pelo site do Campeche Lofts e gostaria de mais informações.`
  - es: `¡Hola! Vine desde el sitio de Campeche Lofts y quisiera más información.`
  - Em `href`, codificado: `https://wa.me/554861369146?text=Ol%C3%A1!%20Vim%20pelo%20site%20do%20Campeche%20Lofts%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es.` (es: `%C2%A1Hola!%20Vine%20desde%20el%20sitio%20de%20Campeche%20Lofts%20y%20quisiera%20m%C3%A1s%20informaci%C3%B3n.`). O robô reconhece esse texto exato como "veio do site, ainda não disse o que quer"; o `check_site.py` dá erro se um `href="https://wa.me/…"` tiver outro texto.
- Formulário de cotação no hero da home (`#cotacao`, JS inline no `index.html`): monta a mensagem que o robô entende (`Check In`, `Check Out` ou `Mês`, `Nº Adultos (+18 anos)`, `Nº Crianças (+12 anos)`, `Nº Crianças (até 12 anos)`, e `Lofts necessários` quando o grupo não cabe num loft) em PT/ES/EN. O site não calcula preço nem lê o tarifário: quem cota é o robô. Mudou o formato aceito pelo robô? Mudar o formulário.
- Janela flutuante: qualquer `<a href="#cotacao">` da home (botão do banner da virada, link da FAQ) abre o mesmo formulário do hero dentro de um `<dialog>` (o JS move o formulário para a janela e o devolve ao hero ao fechar). Novo botão de cotação na home = só apontar para `#cotacao`.
- Leads do formulário: ao clicar em "Enviar pelo WhatsApp" abre uma janelinha pedindo nome, WhatsApp e e-mail; os dados vão por POST para um Google Apps Script (URL `/exec` no JS inline do `index.html`; código em `scripts/leads_site.gs`, colado na planilha Campeche Automation) que grava na aba `leads_site` (mesmos cabeçalhos da `leads_brasil`, ligados pelo nome) e depois o WhatsApp abre na mesma aba. Se o envio falhar, o WhatsApp abre do mesmo jeito. Mudou o script? Reimplantar no Apps Script e, se o URL mudar, atualizar o `index.html`. Dados de leads nunca entram neste repositório.
- Endereço: Rua das Corticeiras, 270 · Campeche · Florianópolis/SC · CEP 88063-160
- Instagram: `https://www.instagram.com/campechelofts/`
- GA4: `G-E857NMXM15` (bloco gtag em toda página pública) · `fb:app_id` `1058264569998928`
- og:image padrão: `https://www.campechelofts.floripa.br/images/logo_campeche_lofts_quadrada.png`
- Reserva: sinal de 50% via Pix, saldo no check-in.
- Preços, disponibilidade e regras comerciais: **nunca inventar** — perguntar ao Leonardo.
- Fatos de marca/tom de voz vêm da pasta da marca em "Negócios AET" no Google Drive (usada pelas skills `criar-artigo-blog` e `criar-guia-html`).
  - `FAQ - Pousada Campeche Lofts.md` (corrigido para 50% em 04/10/2026; a versão antiga com 15% foi para a lixeira).
  - Planilha `Campeche Lofts - Tarifario/FAQ` (abas `tarifario`, `faq`, `lofts`): tarifas por temporada e respostas do atendimento automático ("Sra. Constância"). Se o site mudar de domínio, contato ou regra, atualizar também a aba `faq`.
  - Se Drive e site divergirem, o site/CLAUDE.md vale e a divergência deve ser avisada ao Leonardo.

## Idiomas

- `pt-BR` (padrão) e `es-AR` (público argentino: voseo — "planificá", "escribinos"). `<html lang>` e `og:locale` coerentes com o idioma.
- No `blog/index.html`, cards em espanhol levam `hreflang="es"`, kicker com 🇦🇷 e "Leer artículo".
- `translate="no"` + `<meta name="google" content="notranslate">` em todas as páginas (evita páginas espelho translate.goog). Manter.

## Publicar um artigo novo no blog

Conteúdo: usar a skill `criar-artigo-blog` (marca "Campeche Lofts"). Publicação neste repo:

1. **Copiar um artigo existente do mesmo idioma** como base (`blog/pago-pix-tarjetas-argentinos/` para es, `blog/baleias-franca-litoral-santa-catarina/` para pt). Nunca escrever `<head>`/header/footer do zero — já quebrou o layout uma vez.
2. Slug curto, sem acento, em minúsculas com hífens (evitar slugs gigantes). Pasta `blog/<slug>/index.html`.
2b. **Tema:** o `<p class="article-kicker">` é o nome EXATO de um tema de `scripts/site.json` no idioma do artigo (pt: Planejamento e logística · Passeios e natureza · Temporada e clima · Estadias e economia; es: Planificación y logística · Paseos y naturaleza · Temporada y clima · Estadías y ahorro). Repita-o no último item da migalha. Não crie tema novo sem editar o `site.json`; o `sincronizar.py` recusa. Ele transforma o kicker em link, cuida dos chips do blog e do bloco da home.
3. Atualizar no `<head>`: `title` (≤ 60 caracteres, termina em `| Campeche Lofts`), `description` (≤ 160), `canonical` e `og:url` = `https://www.campechelofts.floripa.br/blog/<slug>/`, `og:title`, `og:description`, JSON-LD `BlogPosting` (headline, description, image, datePublished, dateModified, inLanguage), `BreadcrumbList` e `FAQPage` (mesmas perguntas do FAQ visível).
4. Corpo: breadcrumb, kicker, `h1`, capa, sumário (`article-toc`) com âncoras para cada `h2`, CTA de WhatsApp (`article-cta`, link com o texto único acima, sem frase própria do artigo), FAQ (`<details>`), fontes consultadas com data, "Última revisão", "Leia também" com 2–3 artigos, aside com checklist. HTML de verdade: listas em `<ul><li>`, negrito em `<strong>` — nada de `- ` ou `**` de markdown.
5. **Capa própria e exclusiva:** `images/blog/<slug>.webp`, 1600×1067 (3:2), < 200 KB. Converter com Pillow: `python3 -c "from PIL import Image; Image.open('in.png').convert('RGB').save('out.webp', quality=80, method=6)"` (o `cwebp` desta máquina está quebrado). Nunca reaproveitar a capa de outro artigo. **Sempre 3:2 exato** (recortar, nunca esticar): os cards do blog usam `aspect-ratio: 3/2` em `hub.css`; capa de outra proporção é cortada e foge do padrão.
   Capa de banco gratuito (Unsplash/Pexels): crédito ao fotógrafo na seção de fontes ("Foto de capa: Nome / Unsplash", com link).
6. **Não editar `blog/index.html`, `blog/feed.xml` nem `sitemap.xml` à mão.** Quem registra o artigo nos três é `python3 scripts/publicar_artigo.py <slug>` (card no topo, item no feed, sitemap, datas = dia da publicação).
7. **Nunca publicar direto na `main`.** Commitar só a pasta do artigo + a capa num branch `artigo/<slug>` e dar push do branch. Para validar antes: rodar `publicar_artigo.py <slug>` + `check_site.py` e depois `git checkout -- .` (o `sincronizar.py` também mexe na home, no menu e nos CSS; só desfaz arquivos já versionados, a pasta nova do artigo e a capa ficam).

### Aprovação por e-mail

- Push em `artigo/**` → workflow `artigo-revisao.yml` abre uma issue (label `artigo`) mencionando @LSFcamp → GitHub envia e-mail com link de prévia (raw.githack).
- Leonardo responde o e-mail com `PUBLICAR` ou `EXCLUIR` como primeira palavra (o resto, como assinatura e e-mail citado, é ignorado) → `artigo-decisao.yml` (só aceita LSFcamp/techaet): publicar = merge do branch na main + `publicar_artigo.py` + `check_site.py` + push + dispara `deploy.yml`; excluir = apaga o branch. Push de novo no mesmo branch = comentário "rascunho atualizado" na mesma issue.
- Automação semanal: tarefa agendada `artigos-semanais-campeche-lofts` no app Claude deste Mac (`~/.claude/scheduled-tasks/artigos-semanais-campeche-lofts/SKILL.md`), quintas 8h, 1 artigo PT + 1 ES. Só roda com o app aberto (senão, roda ao abrir). Primeira execução em 04/10/2026 publicou `ilha-do-campeche-como-visitar` e `documentos-viajar-brasil-argentinos` — fluxo PUBLICAR validado de ponta a ponta.
- E-mails chegam na conta GitHub **LSFcamp** (Leonardo; tem permissão de escrita no repo `techaet/pousada-campeche-lofts`, que é público).

## Guias (iscas digitais / lead magnets)

Conteúdo: skill `criar-guia-html`. Publicação: pasta própria na raiz (`/<slug>/index.html`, modelo: `guia-floripa-argentinos/`), canonical/og/GA4 como qualquer página pública, entrada no `sitemap.xml`, e link a partir de pelo menos uma página existente (home, blog ou artigo relacionado) para não ficar órfão.

## Comprovantes de reserva (não é parte do site)

Quando o Leonardo pedir um comprovante ("comprovante pra Fulana, CPF …, loft 5, 2 adultos, 10 a 14/01, R$ 1.800"):

```bash
python3 scripts/comprovante.py --nome "Fulana de Tal" --doc 000.000.000-00 --loft 5 --hospedes "2 adultos" --checkin 2027-01-10 --checkout 2027-01-14 --total 1800
```

- `--idioma es` para argentinos (`--doc` vira DNI/Pasaporte, "seña"); `--sinal` só se não for 50%; `--forcar` só depois de confirmar com ele uma data que gerou AVISO.
- Faltou dado (CPF/DNI, nº de hóspedes, valor total, ano das datas)? Perguntar por questionário, nunca inventar.
- Saída: PDF numerado (`2026-001…`) + linha no `registro_comprovantes.csv`, em `iCloud Drive/Documents/Pousada/Comprovantes/`. Modelo visual: `scripts/comprovante.html`.
- **Dados de hóspedes nunca entram neste repositório (é público).** O `.gitignore` bloqueia `*.pdf` e `comprovante*`; não repetir CPF/nome no chat além do necessário.

## Convenções técnicas

- Toda página pública: favicons, GA4, `canonical` absoluto no domínio novo, `og:*`, `twitter:card`, `meta description`, `viewport`.
- Imagens: WebP para fotos novas; sempre `width`/`height`, `alt` descritivo no idioma da página, `loading="lazy"` fora da primeira dobra. Fotos > 500 KB geram aviso no verificador — redimensionar para no máx. 1920 px.
- Menu (mesma ordem em todas as páginas, rótulos traduzidos): Planeje sua viagem · Lofts · Galeria · Blog · Guia do Hóspede · FAQ · Localização · Contato. **Gerado por `scripts/sincronizar.py` a partir de `scripts/site.json`** (menu pt/es): mudou o menu? Edite o `site.json` e rode `python3 scripts/sincronizar.py`; o `check_site.py` reprova menu fora do padrão. Ficam sem o menu padrão, de propósito: `404.html`, `planejador.html` e `guia-floripa-argentinos/` (lista `sem_menu`).
- Links internos relativos (`../../`) como nas páginas existentes.
- Ao mudar uma página, atualizar o `<lastmod>` dela no `sitemap.xml`.
- `.htaccess`: redirecionamentos 301 para o domínio canônico, headers de segurança, cache e 404. Testar com `curl -sI` depois do deploy.
