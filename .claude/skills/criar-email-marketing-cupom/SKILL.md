---
name: criar-email-marketing-cupom
description: Cria, prepara e publica uma campanha de e-mail marketing com cupom de desconto para a Pousada Campeche Lofts, do texto ao envio no Brevo (listas de leads em PT/ES, HTML com a cara do site, cupom no formulário de cotação, plano de envio). Use sempre que o Leonardo pedir e-mail marketing, newsletter, campanha de e-mail, disparo para leads, cupom ou código promocional, promoção de temporada, "mandar e-mail para a lista", reativar leads da planilha, importar contatos no Brevo, ou mensagem de divulgação do cupom para grupo de WhatsApp, mesmo que ele não diga "skill" nem "Brevo".
---

# E-mail marketing com cupom — Campeche Lofts

Esta skill repete o fluxo que levou ao primeiro envio (campanha **LOFTS10**, out/2026). O que mais pesa aqui não é o HTML, e sim três riscos: **mandar e-mail para quem não consentiu** (reputação do remetente e LGPD), **prometer o que o comercial não confirmou** e **perder o lead no meio do caminho** (por isso o cupom é digitado no formulário do site e segue pelo robô do WhatsApp, e não por resposta de e-mail).

Leia `CLAUDE.md` do projeto antes de começar: ele tem os dados fixos (WhatsApp, endereço, texto único dos links de WhatsApp) e a regra de ouro (`python3 scripts/check_site.py` com 0 erros antes de todo commit).

## O que perguntar ao Leonardo antes de escrever (por questionário)

Preços, descontos, prazos e disponibilidade **nunca são inventados**. Pergunte só o que ainda não foi dito, com `AskUserQuestion` (ele prefere responder clicando):

1. **Código e desconto** (ex.: `LOFTS10`, 10%) e se há **prazo do código**.
2. **Período de estadia** que vale (ex.: 23/12/2026 a 31/03/2027, usando a tabela de tarifas como referência).
3. **Urgência**: se "disponibilidade esgotando" é verdade hoje. Números só se ele fornecer (ex.: "restam 3 lofts na virada").
4. **Público**: quais abas da planilha entram e se alguém já reservou e deve sair.
5. **Remetente**: hoje `constancia@campechelofts.floripa.br`, assinatura "Constância · Campeche Lofts".

## Passo a passo

### 1. Listas (dados pessoais ficam fora do repositório)
- Fonte: planilha **Campeche Automation** (abas `leads_brasil`, `leads_site`, `leads_robo`) e CSVs do CRM em Downloads. Leia pelo conector do Google Sheets.
- Rode `scripts/email_mkt/gerar_lista.py` (veja o cabeçalho do arquivo): junta as fontes, tira e-mails repetidos/inválidos, **separa por idioma pelo DDI do telefone** (+55 = PT; +54 e vizinhos = ES) e grava `EMAIL,FIRSTNAME` em `iCloud Drive/Documents/Pousada/Email Marketing/`.
- **Dados de leads nunca entram no repo** (é público). Não repita nomes/e-mails no chat além do necessário.
- O plano grátis do Brevo envia **300 e-mails/dia**: divida a lista em lotes de ≤300 (`lista_es_1.csv`, `_2`, `_3`), um lote por dia. Não dá para agendar para o dia seguinte.
- Contatos que **não pediram informações** (agenda do celular, fornecedores, empresas) não entram sem revisão: o Brevo exige certificação de consentimento explícito e reclamações de spam derrubam a entrega de todos os próximos envios. Se o Leonardo trouxer um arquivo assim, avise, sugira lista separada revisada por ele e **não faça a importação por ele**.

### 2. E-mail (HTML)
- Base: `scripts/email_mkt/email_pt.html` e `email_es.html` (600 px, tabelas, estilos inline, cores do site: `#124c57` fundo do cupom, `#c99868` botão, `#f7f3ec` fundo). **Copie e troque**, não escreva do zero (cliente de e-mail quebra fácil).
- Troque: código e % (caixa azul, botão, passo 1, pré-título, rodapé), período de validade (rodapé), `utm_campaign` (um nome por campanha, em minúsculas), título e assunto.
- Variáveis do Brevo desta conta: nome é **`{{ contact.NOME }}`** (a conta está em português; `FIRSTNAME` não funciona) com `{% if contact.NOME %}` para não sair "Olá, !"; descadastro é `{{ unsubscribe }}`.
- CTA = link para o site com o cupom preenchido: `https://www.campechelofts.floripa.br/?codigo=CODIGO&idioma=pt|es&utm_source=brevo&utm_medium=email&utm_campaign=NOME#cotacao`. **Não** peça "responda este e-mail": a caixa não é monitorada e o robô não vê a resposta.
- Fotos de e-mail precisam ser JPG/PNG (Outlook não abre WebP) em `images/email/`, 600–1200 px, <200 KB, publicadas no site **antes** de enviar (senão aparecem quebradas).
- Texto: só fatos do site/FAQ (150 m da praia, 35–40 m², até 3 adultos ou 2+2, ar-condicionado, fibra, estacionamento, sinal de 50% via Pix). ES com voseo ("pedí", "hacé clic"). Tom do site: acolhedor, direto, sem exageros.
- Teste visual: gere cópia com variáveis substituídas, sirva em `python3 -m http.server` e confira a 600 px e a 375 px.

### 3. Formulário do site (só se o código for novo)
- O campo **Código promocional** já existe no `#cotacao` e abre sob demanda ("Tem um código promocional?"). Qualquer código vale: ele vira a linha `Código Promocional: XXX` no fim da mensagem para o robô e entra em `leads_site`.
- **O robô precisa saber o código novo.** Confirme com o Leonardo que ele foi avisado antes de disparar. O site não calcula nem valida desconto; quem cota é o robô.
- Link com `?codigo=…&idioma=…` abre o formulário já preenchido (o JS limpa para letras e números maiúsculos).
- Mexeu no site? `python3 scripts/sincronizar.py`, `python3 scripts/check_site.py`, atualize o `<lastmod>` no `sitemap.xml`, e **só dê push com o OK do Leonardo** (push na main = no ar em ~30 s).

### 4. Brevo
Os detalhes técnicos (domínio, remetente, importação, campanha, armadilhas) estão em `references/brevo.md`. Resumo: domínio já autenticado, remetente cadastrado, lista `pousada-PT` e `pousada-ES-1/2/3`. Importar e **enviar são ações do Leonardo**: a certificação de consentimento e o botão de envio são dele. Prepare tudo até a tela de confirmação e pare.

### 5. Antes de enviar (checklist)
- [ ] `check_site.py` com 0 erros e imagens do e-mail no ar (`curl -sI` retorna 200)
- [ ] Código novo conhecido pelo robô
- [ ] Teste enviado para o Leonardo: logo, foto, nome, botão (abre o site com o código), descadastro, não caiu em spam
- [ ] Assunto e pré-título conferidos (PT e ES já prontos nos arquivos)
- [ ] Lote ≤300 e lista certa

### 6. Depois do envio
- No dia seguinte: devoluções (**pare se passar de 5%**), reclamações de spam, aberturas, cliques. Só então libere o próximo lote.
- Leads da campanha aparecem em `leads_site` com o `utm_campaign` e "código XXX" junto do grupo.
- Para divulgar no grupo de WhatsApp (hoje argentino): mensagem em ES com o link do cotizador (`utm_source=whatsapp&utm_medium=grupo`), 3–7 artigos do blog e a guia `guia-floripa-argentinos/`. Link do grupo está no `CLAUDE.md`.

## Quando algo der errado
Veja `references/brevo.md`, seção "Armadilhas encontradas". Se a automação do navegador for bloqueada (dados pessoais, senhas, certificações), pare e passe o passo a passo ao Leonardo em vez de contornar.
