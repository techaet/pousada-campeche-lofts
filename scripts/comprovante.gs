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
const ID_TARIFARIO = '169jgZZLCsf2lCq3PYF3VvLz59h8yWeRScUwlUKHHsdE'; // planilha Campeche Automation (abas tarifario e configuracao)
const NOME_PLANILHA = 'Manutenção - Custos Campeche Lofts';
const CABECALHO = ['Nº', 'Executada em', 'Prioridade', 'Descrição', 'Valor (R$)', 'Status', 'Pago em', 'Anexos'];

function doPost(e) {
  try {
    const d = JSON.parse(e.postData.contents);
    if (d.segredo !== SEGREDO) return resposta({ ok: false, erro: 'segredo inválido' });
    if (d.acao === 'tarifario') {
      const t = SpreadsheetApp.openById(ID_TARIFARIO);
      return resposta({ ok: true, tarifario: t.getSheetByName('tarifario').getDataRange().getDisplayValues(),
                       configuracao: t.getSheetByName('configuracao').getDataRange().getDisplayValues() });
    }
    if (d.acao === 'manutencao') return resposta({ ok: true, planilha: registrarManutencao(d.itens) });
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

/** Planilha de custos da manutenção: uma linha por tarefa (atualizada pelo Nº quando muda de status). */
function planilhaManutencao() {
  const props = PropertiesService.getScriptProperties();
  const id = props.getProperty('PLANILHA_ID');
  if (id) { try { return SpreadsheetApp.openById(id); } catch (err) { /* apagada: cria outra */ } }
  const it = DriveApp.getFilesByName(NOME_PLANILHA);
  const ss = it.hasNext() ? SpreadsheetApp.open(it.next()) : SpreadsheetApp.create(NOME_PLANILHA);
  props.setProperty('PLANILHA_ID', ss.getId());
  const sh = ss.getSheets()[0];
  if (sh.getLastRow() === 0) {
    sh.appendRow(CABECALHO);
    sh.setFrozenRows(1);
    sh.getRange(1, 1, 1, CABECALHO.length).setFontWeight('bold');
    sh.getRange('E2:E').setNumberFormat('R$ #,##0.00');
  }
  return ss;
}

function registrarManutencao(itens) {
  const lock = LockService.getScriptLock();
  lock.waitLock(20000);
  try {
    const ss = planilhaManutencao();
    const sh = ss.getSheets()[0];
    const n = sh.getLastRow() - 1;
    const ids = n > 0 ? sh.getRange(2, 1, n, 1).getValues().map(function (r) { return String(r[0]); }) : [];
    itens.forEach(function (t) {
      const linha = [t.id, t.executada_em, t.prioridade, t.descricao, t.valor, t.status, t.paga_em, t.anexos];
      const i = ids.indexOf(String(t.id));
      if (i >= 0) {
        sh.getRange(i + 2, 1, 1, linha.length).setValues([linha]);
      } else {
        sh.appendRow(linha);
        ids.push(String(t.id));
      }
    });
    return ss.getUrl();
  } finally {
    lock.releaseLock();
  }
}
