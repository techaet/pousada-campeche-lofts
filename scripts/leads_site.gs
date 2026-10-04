// Recebe os leads do formulário de cotação do site e grava na aba "leads_site".
// Cole em: planilha "Campeche Automation" > Extensões > Apps Script.
// Os valores são ligados pelo NOME do cabeçalho (linha 1), então a ordem das colunas não importa
// e colunas que o site não preenche ficam em branco.

var ABA = 'leads_site';

function doPost(e) {
  var p = e.parameter || {};
  if (p.website) return ok_(); // campo-isca invisível: só robôs preenchem

  var nome = limpa_(p.nome), fone = limpa_(p.whatsapp), email = limpa_(p.email);
  if (!nome || fone.replace(/\D/g, '').length < 8 || !/^\S+@\S+\.\S+$/.test(email)) return ok_();

  var semUtm = !p.utm_source && !p.utm_medium && !p.utm_campaign;
  var linha = {
    id: 'site-' + new Date().getTime(),
    created_time: Utilities.formatDate(new Date(), 'America/Sao_Paulo', "yyyy-MM-dd'T'HH:mm:ssXXX"),
    campaign_name: limpa_(p.utm_campaign),
    adset_name: limpa_(p.utm_medium),
    ad_name: limpa_(p.utm_content),
    form_name: 'Site - cotação (' + limpa_(p.pagina) + ')',
    is_organic: semUtm ? 'true' : 'false',
    platform: limpa_(p.utm_source) || 'site',
    'quando_você_pensa_em_viajar_neste_verão?': limpa_(p.quando),
    'como_é_o_seu_grupo?': limpa_(p.grupo),
    full_name: nome,
    phone_number: fone,
    email: email,
    lead_status: 'CREATED'
  };

  var lock = LockService.getScriptLock();
  lock.waitLock(10000);
  try {
    var sh = SpreadsheetApp.getActive().getSheetByName(ABA);
    var cab = sh.getRange(1, 1, 1, sh.getLastColumn()).getValues()[0];
    var vals = cab.map(function (h) { return h in linha ? linha[h] : ''; });
    // formato texto: o "+55..." do telefone não vira fórmula e nada digitado vira conta
    sh.getRange(sh.getLastRow() + 1, 1, 1, vals.length).setNumberFormat('@').setValues([vals]);
  } finally {
    lock.releaseLock();
  }
  return ok_();
}

function limpa_(v) { return String(v || '').trim().substring(0, 200); }
function ok_() { return ContentService.createTextOutput('ok'); }
