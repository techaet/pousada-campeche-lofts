# Brevo — detalhes técnicos da conta Campeche Lofts

Estado em 08/10/2026 (conta "TECH AET", plano grátis, interface em português). Se algo divergir da tela, confie na tela e atualize este arquivo.

## Já configurado (não refazer)
- **Domínio `campechelofts.floripa.br` autenticado** (Código Brevo TXT, 2 DKIM CNAME, DMARC). Registros no **cPanel da HostGator** (Editor de Zona DNS), porque os nameservers são `ns1110/ns1111.hostgator.com.br`. O e-mail do domínio é **Titan** (MX `mx1/mx2.titan.email`, SPF `include:spf.titan.email`): **não mexer** nesses registros.
- **Remetente:** `Campeche Lofts <constancia@campechelofts.floripa.br>` (verificado, DKIM ✔, DMARC ✔).
- **Listas:** `pousada-PT` (264), `pousada-ES-1` (300), `pousada-ES-2` (300), `pousada-ES-3` (72). `pousada-ES` está vazia (pode apagar). `Sua primeira lista` tem só o contato do dono.
- **Atributo do nome:** `NOME` (e `SOBRENOME`), não `FIRSTNAME`.

## Limites do plano grátis
- **300 e-mails/dia**. Campanha com mais contatos: só 300 saem; não dá para agendar para o dia seguinte.
- Selo "Sent with Brevo" no rodapé (não removível sem pagar). Estatísticas básicas.

## Fluxo de importação de contatos
Contatos → Importar contatos → Importar de um arquivo → CSV → mapear → lista → certificação → Confirmar.
- **Mapeamento:** `EMAIL`→EMAIL e `FIRSTNAME`→**NOME**. A sugestão da IA às vezes marca a coluna do nome como "Não importar": confira e corrija em "Editar" antes de confirmar.
- **Lista:** use "Criar uma lista" dentro do próprio fluxo.
- **Certificação opt-in:** declara consentimento explícito (últimos 2 anos), contatos não emprestados nem comprados. É declaração legal do Leonardo: **ele marca e confirma**; Claude não marca.
- Pelo Chrome automatizado, o envio do CSV é feito pelo campo de arquivo da página (`file_upload`), com o arquivo numa pasta que a sessão lê (ex.: o scratchpad). Apague a cópia depois. Se o Chrome bloquear por dados pessoais, passe o passo a passo ao Leonardo.

## Criar a campanha
Campanhas → E-mail → nome (`Verão CODIGO - PT`) → Remetente (trocar o remetente padrão da conta (um Gmail) por `constancia@campechelofts.floripa.br`) → Destinatários (lista) → Assunto + texto de pré-visualização → Criação: **Criar do zero → Código HTML personalizado** → colar o HTML → Salvar e Sair → Pré-visualizar e testar → Enviar agora.
- O editor de HTML não tem "importar arquivo": cole o texto (⌘V). Dica: `pbcopy < arquivo.html`.
- **Configurações adicionais:** deixar padrão. GA do Brevo **desligado** (os links já têm `utm_*`; ligar pode sobrescrever o `utm_campaign`), reply-to igual ao remetente, rastreio de abertura/clique ligado.

## Armadilhas encontradas
- **cPanel recusa TXT com TTL diferente** do TXT já existente no mesmo nome (`TTL values mismatched`): use o TTL do registro existente (3600 no `@`).
- **Nome da variável:** `{{ contact.FIRSTNAME }}` não preenche nesta conta; use `{{ contact.NOME }}`.
- **Gmail como remetente** cai em spam/DMARC quando enviado por terceiros: por isso o remetente é do domínio.
- **Rotas diretas** (`/marketing-campaign/add/email`) não existem; entre por Campanhas → E-mail.
- **A tela de certificação** pode mostrar a caixa já marcada depois da primeira vez; ainda assim a declaração é do Leonardo. Se a importação já constar como concluída, ele a confirmou.
- **Extensão do Chrome** pode ficar "não conectada" por instantes: tente de novo antes de concluir que falhou.

## Fontes
- Autenticar domínio: https://help.brevo.com/hc/en-us/articles/12163873383186
- Limites do plano grátis: https://help.brevo.com/hc/en-us/articles/208580669
- Arquivo de contatos: https://help.brevo.com/hc/en-us/articles/208729849
