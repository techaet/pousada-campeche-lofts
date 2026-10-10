/**
 * Recebe o PDF do comprovante (enviado pelo site, /admin/), salva na pasta do Drive e envia por e-mail ao hóspede.
 * Roda na conta lsf.loft@gmail.com (é dela o Gmail e o Drive usados).
 *
 * Instalação (uma vez):
 *  1. script.google.com, logado em lsf.loft@gmail.com -> Novo projeto -> colar este arquivo.
 *  2. Troque SEGREDO abaixo por um texto longo e aleatório (o mesmo vai em /admin/config.php).
 *  3. Implantar -> Nova implantação -> Tipo "App da Web" -> Executar como: Eu -> Quem tem acesso: Qualquer pessoa -> Implantar.
 *  4. Autorize (Gmail e Drive) e copie o endereço terminado em /exec para /admin/config.php.
 * Mudou este código? Implantar -> Gerenciar implantações -> editar -> Nova versão (o endereço continua o mesmo).
 */
const SEGREDO = 'TROQUE-ESTE-TEXTO';
const NOME_PASTA = 'Comprovantes de Reserva - Campeche Lofts';

function doPost(e) {
  try {
    const d = JSON.parse(e.postData.contents);
    if (d.segredo !== SEGREDO) return resposta({ ok: false, erro: 'segredo inválido' });
    const pdf = Utilities.newBlob(Utilities.base64Decode(d.pdf), 'application/pdf', d.arquivo);
    const arquivo = pastaComprovantes().createFile(pdf);
    let enviado = false;
    if (d.email) {
      GmailApp.sendEmail(d.email, d.assunto, d.corpo, { attachments: [pdf], name: 'Campeche Lofts' });
      enviado = true;
    }
    return resposta({ ok: true, enviado: enviado, drive: pastaComprovantes().getUrl(), arquivo: arquivo.getUrl() });
  } catch (err) {
    return resposta({ ok: false, erro: String(err) });
  }
}

function pastaComprovantes() {
  const it = DriveApp.getFoldersByName(NOME_PASTA);
  return it.hasNext() ? it.next() : DriveApp.createFolder(NOME_PASTA);
}

function resposta(o) {
  return ContentService.createTextOutput(JSON.stringify(o)).setMimeType(ContentService.MimeType.JSON);
}
