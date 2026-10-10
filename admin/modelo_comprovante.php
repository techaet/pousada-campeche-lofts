<?php
// Modelo HTML do comprovante (compatível com dompdf: tabelas, sem flex/grid). Placeholders {{chave}}.
return <<<'HTML'
<!DOCTYPE html>
<html lang="{{lang}}"><head><meta charset="UTF-8"><title>{{titulo}} {{numero}}</title>
<style>
  @page { margin: 0; }
  body { font-family: Helvetica, Arial, sans-serif; color: #173234; font-size: 10pt; margin: 0; background: #fbfaf6; }
  table { border-collapse: collapse; width: 100%; }
  td { vertical-align: top; }
  .bar { height: 6px; background: #1d4038; }
  .serif { font-family: "Times", Georgia, serif; font-weight: normal; }
  .eyebrow { font-size: 7.5pt; letter-spacing: 2px; font-weight: bold; text-transform: uppercase; color: #1d4038; margin: 0; }
  h1 { font-family: "Times", Georgia, serif; font-size: 22pt; font-weight: normal; margin: 6px 0 4px; }
  h2 { font-family: "Times", Georgia, serif; font-size: 13pt; font-weight: normal; margin: 22px 0 8px; }
  .muted { color: #5d6b6b; }
  .badge { display: inline-block; margin: 20px 0 10px; padding: 6px 14px; border: 1px solid #b9cdb8; background: #e9f0e6; color: #1d4038; font-weight: bold; font-size: 8.5pt; }
  .intro { color: #5d6b6b; font-family: "Times", Georgia, serif; font-size: 11pt; line-height: 1.5; margin: 0; }
  .box { border: 1px solid #d9d3c6; background: #fff; }
  .box td { padding: 10px 12px; width: 50%; }
  .label { font-size: 6.5pt; letter-spacing: 1.5px; font-weight: bold; text-transform: uppercase; color: #5d6b6b; margin: 0 0 3px; }
  .value { font-size: 9.5pt; margin: 0; } .b { font-weight: bold; }
  .pay { background: #1d4038; color: #fff; }
  .pay td { padding: 14px 26px; }
  .pay h2 { color: #fff; margin-top: 4px; }
  .pay p { font-size: 8.5pt; line-height: 1.5; color: #d6e0dc; margin: 0; }
  .tot td { padding: 8px 10px; font-size: 8.5pt; color: #fff; background: #4f6e66; border-bottom: 1px solid #5d7a70; }
  .tot .t td { background: #67806a; font-weight: bold; font-size: 10pt; }
  .tot .s td { color: #f0d9a8; font-weight: bold; }
  .by td { width: 50%; font-size: 8.5pt; line-height: 1.5; color: #5d6b6b; }
  .foot { background: #1d4038; color: #d6e0dc; font-size: 7.5pt; }
  .foot td { padding: 12px 26px; }
</style></head><body>
<div class="bar"></div>
<table style="border-bottom:1px solid #d9d3c6"><tr>
  <td style="padding:14px 26px 16px"><img src="logo_comprovante.png" style="width:150px"></td>
  <td style="padding:22px 26px 16px;text-align:right"><p class="eyebrow">{{eyebrow}}</p><h1>{{titulo}}</h1><p class="muted" style="font-size:9pt;margin:0">Nº {{numero}}</p></td>
</tr></table>
<div style="padding:0 26px">
  <span class="badge">&bull; {{confirmada}}</span>
  <p class="intro">{{intro}}</p>

  <h2>{{sec_hospede}}</h2>
  <table class="box">
    <tr><td><p class="label">{{l_nome}}</p><p class="value b">{{nome}}</p></td><td><p class="label">{{l_doc}}</p><p class="value">{{documento}}</p></td></tr>
    <tr><td><p class="label">{{l_acomodacao}}</p><p class="value b">Loft {{loft}}</p></td><td><p class="label">{{l_hospedes}}</p><p class="value">{{hospedes}}</p></td></tr>
  </table>

  <h2>{{sec_estadia}}</h2>
  <table class="box">
    <tr><td><p class="label">Check-in</p><p class="value b">{{checkin}}</p></td><td><p class="label">Check-out</p><p class="value b">{{checkout}}</p></td></tr>
    <tr><td><p class="label">{{l_duracao}}</p><p class="value">{{noites}}</p></td><td><p class="label">{{l_endereco}}</p><p class="value">Rua das Corticeiras, 270<br>Campeche — Florianópolis/SC</p></td></tr>
  </table>
</div>

<table class="pay" style="margin-top:22px"><tr>
  <td style="width:50%"><h2>{{sec_recibo}}</h2><p>{{texto_recibo}}</p></td>
  <td style="width:50%;padding-top:20px"><table class="tot">
    <tr class="t"><td>{{l_total}}</td><td style="text-align:right">{{total}}</td></tr>
    <tr><td>{{l_sinal}}</td><td style="text-align:right">{{sinal}}</td></tr>
    <tr class="s"><td>{{l_saldo}}</td><td style="text-align:right">{{saldo}}</td></tr>
  </table></td>
</tr></table>

<div style="padding:0 26px">
  <p class="muted" style="font-size:8pt;padding:20px 0 14px;border-bottom:1px solid #d9d3c6;margin:0">{{emitido}}</p>
  <h2>{{sec_recebido}}</h2>
  <table class="by"><tr>
    <td><strong style="color:#173234">AET SOLIDEZ ADM IMOVEIS LTDA</strong><br>CNPJ: 53.910.643/0001-39<br>{{socio}}: Leonardo S. Fioravanso</td>
    <td>lsf.camp@gmail.com<br>(48) 99122-3600<br>Campeche Lofts · Florianópolis/SC</td>
  </tr></table>
</div>
<div style="position:absolute;bottom:0;left:0;width:100%"><table class="foot"><tr><td>{{rodape}}</td><td style="text-align:right">www.campechelofts.floripa.br</td></tr></table></div>
</body></html>
HTML;
