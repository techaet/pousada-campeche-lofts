# Atualização de cabeçalhos — Pousada Campeche Lofts

Este pacote reúne a atualização de navegação dos nove HTMLs enviados em 26 de agosto de 2026 e uma correção complementar para a página pública do **Blog**. Nos nove arquivos de origem, foram preservados o conteúdo, galerias, scripts, mapas, botões e rodapés; somente o primeiro cabeçalho e a folha complementar de navegação foram alterados. Na página do Blog, a única alteração é a inclusão do item **Planeje sua viagem** no início do cabeçalho.

## Navegação do cabeçalho

| Ordem | Item | Destino na raiz do domínio |
|---:|---|---|
| 1 | Planeje sua viagem | `/planejador.html` |
| 2 | Lofts | `/#lofts` |
| 3 | Galeria | `/galeria_geral.html` |
| 4 | Guia do Hóspede | `/guia/` |
| 5 | Blog | `/blog/` |
| 6 | Localização | `/#localizacao` |
| 7 | Contato | `/#contato` |

## Arquivos incluídos

| Arquivo ou pasta | Ação |
|---|---|
| `loft_03.html` a `loft_09.html` | Substituir os equivalentes na raiz, caso ainda não tenham recebido a navegação unificada. |
| `galeria_geral.html` | Substituir o arquivo equivalente na raiz. |
| `template_loft.html` | Substituir o template de referência na raiz, se estiver no servidor. |
| `navegacao-unificada.css` | Enviar à raiz. É a folha complementar usada pelos nove HTMLs acima. |
| `blog/index.html` | Substituir o índice do Blog; acrescenta Planeje sua viagem ao cabeçalho. |

> Extraia o conteúdo deste ZIP na **raiz do site**, a mesma pasta que contém o `index.html` da Pousada. Não extraia a pasta inteira dentro de `/blog/`.

## Observações

A capa atual do Blog foi preservada neste pacote. A troca de capa deve ser feita separadamente no `blog/index.html`, ajustando a imagem da hero conforme o arquivo que será escolhido. O Guia do Hóspede, que é protegido e trilíngue, não é modificado por este ZIP.

A regra responsiva dos nove HTMLs está em `navegacao-unificada.css`: abaixo de 1080 px, os itens aparecem no menu expansível já controlado pelo `campeche.js` existente.
