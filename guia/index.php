<?php
require __DIR__ . '/session.php';
if (empty($_SESSION['guide_authenticated'])) {
    header('Location: login.php', true, 302);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" translate="no">
<head>
  <meta charset="UTF-8">
  <meta name="google" content="notranslate">
 <!-- Google tag (gtag.js) -->
 <script async src="https://www.googletagmanager.com/gtag/js?id=G-E857NMXM15"></script>
 <script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-E857NMXM15');
 </script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Guia do hóspede do Campeche Lofts: convivência, estacionamento, portão, Wi-Fi e orientações práticas.">
  <meta name="robots" content="noindex, nofollow">
  <title>Guia do Hóspede | Campeche Lofts</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:opsz,wght@5..1200,500;5..1200,600;5..1200,700&display=swap"><link rel="stylesheet" href="campeche.css?v=20261010-trilhas">
</head>
<body>
  <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>

  <header class="site-header guide-header">
    <div class="header-inner">
      <a class="brand" href="../index.html" aria-label="Campeche Lofts — página inicial">
        <span class="brand-logo-frame">
          <img class="brand-logo" src="images/logo_campeche_lofts_horizontal.webp" alt="Campeche Lofts" width="460" height="307">
        </span>
      </a>
      <div class="guide-header-actions">
        <a class="guide-language" href="english.php" lang="en" hreflang="en" aria-label="View this guide in English">EN</a>
        <a class="guide-language" href="espanol.php" lang="es" hreflang="es" aria-label="Ver esta guía en español">ES</a>
        <a class="guide-contact" data-short-label="Ajuda" href="https://api.whatsapp.com/send?phone=5548991223600&text=Ol%C3%A1%2C%20estou%20com%20uma%20d%C3%BAvida%20sobre%20o%20Guia%20do%20H%C3%B3spede." target="_blank" rel="noopener">Precisa de ajuda?</a>
      </div>
    </div>
  </header>

  <main id="conteudo">
    <section class="guide-hero">
      <div class="container">
        <p class="eyebrow">Campeche Lofts · Florianópolis/SC</p>
        <h1>Guia do<br><em>hóspede.</em></h1>
        <p>Informações práticas para que sua estadia seja tranquila, confortável e segura. Guarde este link para consultar sempre que precisar.</p>
        <span class="guide-hero-badge">Leitura rápida para a sua estadia</span>
      </div>
    </section>

    <nav class="guide-nav" aria-label="Navegação do guia">
      <div class="guide-nav-inner">
        <a href="#convivencia">Convivência</a>
        <a href="#portao">Portão e vagas</a>
        <a href="#wifi">Wi‑Fi</a>
        <a href="#delivery">Delivery</a>
        <a href="#churrasqueira">Churrasqueira</a>
        <a href="#lixo">Lixo</a>
        <a href="#lavanderia">Lavanderia</a>
        <a href="#praia">Praia</a><a href="#trilhas">Trilhas</a><a href="#telefones">Telefones</a><a href="#tv">TV Sky</a>
        <a href="#grupo">Grupo</a>
        <a href="#ajuda">Ajuda</a>
      </div>
    </nav>

    <section class="guide-section section-paper">
      <div class="container guide-intro">
        <div class="guide-intro-copy">
          <p class="eyebrow">Seja bem-vindo</p>
          <h2>Que bom ter você no Campeche Lofts.</h2>
          <p>Nosso objetivo é que você aproveite a autonomia do loft com a tranquilidade de um ambiente familiar. Este guia reúne os cuidados que ajudam a estadia de todos a fluir bem.</p>
        </div>
        <aside class="guide-alert" aria-label="Informação importante">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 1 21h22L12 2Zm1 16h-2v-2h2v2Zm0-4h-2v-4h2v4Z"/></svg>
          <strong>Em caso de dúvida, fale conosco antes de agir.</strong>
          <p>Uma mensagem rápida pelo WhatsApp evita imprevistos e nos permite ajudar você da melhor forma.</p>
        </aside>
      </div>
    </section>

    <section id="convivencia" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Boa convivência</p>
            <h2>Cuidados essenciais.</h2>
          </div>
          <p>Os lofts acomodam até <strong>3 hóspedes</strong>. Para o bem-estar de todos, pedimos atenção a estas regras de convivência durante a estadia.</p>
        </div>
        <div class="guide-rules">
          <article class="guide-rule">
            <span class="guide-rule-number">01</span>
            <strong>Silêncio</strong>
            <p>Respeite o horário de silêncio das <strong>22h às 9h</strong>.</p>
          </article>
          <article class="guide-rule">
            <span class="guide-rule-number">02</span>
            <strong>Visitas e segurança</strong>
            <p>Não são permitidas visitas na pousada. Não permita a entrada de pessoas desconhecidas.</p>
          </article>
          <article class="guide-rule">
            <span class="guide-rule-number">03</span>
            <strong>Ambiente sem fumaça</strong>
            <p>Não fume dentro do loft nem em locais onde a fumaça possa incomodar outras pessoas.</p>
          </article>
          <article class="guide-rule">
            <span class="guide-rule-number">04</span>
            <strong>Ao sair do loft</strong>
            <p>Desligue o ar-condicionado e verifique se o gás do fogão está fechado.</p>
          </article>
          <article class="guide-rule">
            <span class="guide-rule-number">05</span>
            <strong>Voltagem</strong>
            <p>As tomadas são <strong>220 V</strong>. Verifique seus aparelhos antes de conectá-los.</p>
          </article>
          <article class="guide-rule">
            <span class="guide-rule-number">06</span>
            <strong>Bom senso</strong>
            <p>Cuide do loft e das áreas compartilhadas como se fossem sua casa.</p>
          </article>
        </div>
      </div>
    </section>

    <section id="portao" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Atenção ao entrar e sair</p>
            <h2>Portão e estacionamento.</h2>
          </div>
          <p>O portão eletrônico e as vagas são áreas compartilhadas. Pequenos cuidados fazem grande diferença para a segurança e para que todos estacionem com facilidade.</p>
        </div>
        <ol class="guide-step-list">
          <li><strong>Conte com 30 segundos</strong>O portão fecha automaticamente em aproximadamente 30 segundos. Ao sair, considere esse tempo enquanto aguarda o movimento da rua.</li>
          <li><strong>Precisa de mais tempo?</strong>Se for necessário, dê uma ré, acione o portão novamente e ganhe mais 30 segundos para sair com segurança.</li>
          <li><strong>Feche logo após passar</strong>Ao entrar ou sair, aperte o botão imediatamente para iniciar o fechamento. Não deixe o portão aberto além do necessário.</li>
          <li><strong>Cuidado com o controle</strong>Dentro do loft, evite apertar o controle sem querer. O portão pode acionar enquanto um carro estiver passando.</li>
          <li><strong>Respeite as linhas amarelas</strong>Para que todos tenham uma vaga boa, estacione sempre dentro das marcações amarelas do pátio.</li>
          <li><strong>Consulte o mapa da vaga</strong>Há um mapa dentro de cada loft mostrando as formas corretas e incorretas de estacionar. Na dúvida, fale conosco.</li>
        </ol>
        <p class="guide-note">Cada apartamento possui direito a uma vaga, desde que o veículo esteja corretamente posicionado. Obrigado por colaborar para que sempre haja uma vaga prática e segura para todos.</p>
        <figure class="guide-parking-map">
          <img src="images/mapa-estacionamento.png" alt="Mapa do estacionamento da pousada com exemplos de posicionamento correto e incorreto dos carros" loading="lazy">
          <figcaption>Consulte o mapa antes de estacionar. Ele mostra as posições corretas e as que devem ser evitadas para preservar as vagas de todos.</figcaption>
        </figure>
      </div>
    </section>

    <section id="wifi" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Conexão</p>
            <h2>Internet Wi‑Fi.</h2>
          </div>
          <p>Escolha a rede correspondente ao seu loft. A senha é a mesma para todas e também está disponível na folha explicativa dentro do apartamento.</p>
        </div>
        <div class="guide-wifi">
          <aside class="wifi-password">
            <span>Senha de todas as redes</span>
            <strong>32349758</strong>
            <p>Use a rede indicada para o número do seu loft.</p>
          </aside>
          <div class="guide-table-wrap">
            <table class="guide-table">
              <thead>
                <tr><th scope="col">Rede Wi‑Fi</th><th scope="col">Lofts atendidos</th></tr>
              </thead>
              <tbody>
                <tr><td><strong>Pousada C01</strong></td><td>Lofts 02 e 03</td></tr>
                <tr><td><strong>P002</strong></td><td>Lofts 04 e 05</td></tr>
                <tr><td><strong>P003</strong></td><td>Lofts 07 e 09</td></tr>
                <tr><td><strong>P004</strong></td><td>Lofts 06 e 08</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <section id="delivery" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Segurança e praticidade</p>
            <h2>Delivery e entregas.</h2>
          </div>
          <p>Ao pedir delivery, informe o número do loft e peça para o entregador tocar o interfone. Ele funciona apenas como campainha.</p>
        </div>
        <div class="guide-split">
          <article class="guide-card">
            <span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 8h-3V4H3v12h2a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h1v-4l-2-4Zm-12 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm9-7V6.5L19.5 10H17Zm1 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/></svg></span>
            <h3>Retire no portão.</h3>
            <p>Busque o pedido no portão principal. Por segurança, motoboys e entregadores não devem entrar na pousada.</p>
          </article>
          <article class="guide-card">
            <span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm5 17a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm-4-5h8V5H8v9Z"/></svg></span>
            <h3>Informe seu loft.</h3>
            <p>Ao solicitar a entrega, indique o número correto do apartamento e peça para o entregador tocar o interfone.</p>
          </article>
        </div>
      </div>
    </section>

    <section id="churrasqueira" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Uso compartilhado</p>
            <h2>Churrasqueira.</h2>
          </div>
          <p>A churrasqueira pode ser usada até as <strong>24h</strong>. Antes de utilizá-la, solicite a liberação pelo WhatsApp.</p>
        </div>
        <div class="guide-split">
          <article class="guide-card">
            <span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3h8v2h2v2h-2v3a4 4 0 0 1-3 3.87V17h2v2H9v-2h2v-3.13A4 4 0 0 1 8 10V7H6V5h2V3Zm2 4v3a2 2 0 0 0 4 0V7h-4Z"/></svg></span>
            <h3>Peça a liberação.</h3>
            <p>Fale conosco pelo WhatsApp antes do uso para confirmar a disponibilidade do espaço.</p>
          </article>
          <article class="guide-card">
            <span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h10l1 18H6L7 3Zm3 3v3h4V6h-4Zm0 6v6h4v-6h-4Z"/></svg></span>
            <h3>Deixe como encontrou.</h3>
            <p>Após usar, limpe a churrasqueira e o balcão. Caso o espaço seja deixado sujo, poderá ser aplicada uma taxa de limpeza.</p>
          </article>
        </div>
      </div>
    </section>

    <section id="lixo" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Organização do pátio</p>
            <h2>Coleta de lixo.</h2>
          </div>
          <p>Coloque todo o lixo devidamente ensacado no recipiente adequado, localizado ao lado do portão de acesso.</p>
        </div>
        <div class="guide-table-wrap">
          <table class="guide-table">
            <thead><tr><th scope="col">Recipiente</th><th scope="col">Descarte correto</th></tr></thead>
            <tbody>
              <tr><td><strong>Lixeira de madeira</strong></td><td>Lixo seco, que não atraia moscas, larvas ou baratas.</td></tr>
              <tr><td><strong>Contêineres plásticos</strong></td><td>Todos os outros tipos de lixo, sempre bem ensacados.</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <section id="lavanderia" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Uso compartilhado</p>
            <h2>Lavanderia.</h2>
          </div>
          <p>A pousada tem uma <strong>lavanderia coletiva</strong>, de uso <strong>gratuito</strong> para os hóspedes. Você só precisa comprar o seu sabão.</p>
        </div>
        <figure class="guide-parking-map" style="max-width:560px">
          <img src="images/lavanderia.webp" alt="Lavanderia coletiva da pousada, com máquinas de lavar roupa e bancada" width="1086" height="1448" loading="lazy">
          <figcaption>Lavanderia coletiva da pousada: uso gratuito, basta levar o seu sabão.</figcaption>
        </figure>
      </div>
    </section>

    <section id="praia" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">A pé até o mar</p><h2>Caminho até a praia.</h2></div><p>Da pousada até a beira da praia são poucos minutos a pé. O trajeto está marcado em vermelho no mapa.</p></div>
        <figure class="guide-parking-map"><img src="images/caminho-praia.webp" alt="Mapa aéreo com o caminho a pé da pousada até a Praia do Campeche, marcado em vermelho" width="1200" height="693" loading="lazy"><figcaption>Caminho a pé da pousada até a beira da praia (escala de 100 m no mapa).</figcaption></figure>
      </div>
    </section>

    <section id="trilhas" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Natureza ao redor</p><h2>Trilhas.</h2></div><p>Sugestões de trilhas e passeios a partir do Campeche. Toque em “Abrir no Waze” para traçar a rota até o ponto de partida.</p></div>
        <div class="guide-trails"><article class="guide-trail"><img src="images/trilhas/morro-do-lampiao.webp" alt="Vista aérea do Campeche e da Ilha do Campeche a partir do Morro do Lampião" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Morro do Lampião</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.66506710%2C-48.48798890&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir no Waze →</a><small>Foto: Thayran Melo / <a href="https://unsplash.com/photos/ldd7qEB4UO0" target="_blank" rel="noopener">Unsplash</a></small></div></article><article class="guide-trail"><img src="images/trilhas/morro-das-pedras.webp" alt="Rochas à beira-mar no Morro das Pedras, com morros ao fundo" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Mirante do Morro das Pedras</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.72027187%2C-48.50332044&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir no Waze →</a><small>Foto: Jeferson Felix / <a href="https://commons.wikimedia.org/wiki/File:Morro_das_Pedras,_Florian%C3%B3polis_-_Brasil_-_panoramio.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/lagoa-do-peri.webp" alt="Cachoeira entre pedras e mata na Lagoa do Peri" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Lagoa do Peri / Cachoeira</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.72598956%2C-48.50712885&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir no Waze →</a><small>Foto: Sovernigo / <a href="https://commons.wikimedia.org/wiki/File:Cachoeira_da_Lagoa_do_Peri.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY-SA 4.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/matadeiro.webp" alt="Vista do alto da praia do Matadeiro e da Armação" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Armação / Ponta das Campanhas / Matadeiro</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.75062217%2C-48.50271961&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir no Waze →</a><small>Foto: Marco Rosa / <a href="https://commons.wikimedia.org/wiki/File:Matadeiro_e_Arma%C3%A7%C3%A3o_-_panoramio.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/lagoinha-do-leste.webp" alt="Praia da Lagoinha do Leste vista do alto, entre morros verdes" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Lagoinha do Leste / Pedra da Coroa</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.77882809%2C-48.50727912&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir no Waze →</a><small>Foto: Paulo Cameli / <a href="https://commons.wikimedia.org/wiki/File:Morro_Da_Coroa_Praia_Lagoinha_Do_Leste_(242401955).jpeg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/solidao.webp" alt="Costa rochosa e mar perto da Praia do Saquinho" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Solidão / Praia do Saquinho / Cachoeira da Solidão</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.79477451%2C-48.53468962&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir no Waze →</a><small>Foto: Papa Pic / <a href="https://commons.wikimedia.org/wiki/File:Alto_saquinho_(16771033008).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC0)</small></div></article><article class="guide-trail"><img src="images/trilhas/costa-de-dentro.webp" alt="Barcos e píer na baía do Ribeirão da Ilha" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Estrada da Costa de Dentro até o Ribeirão da Ilha</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.78544424%2C-48.53216880&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir no Waze →</a><small>Foto: Andreia Reis / <a href="https://commons.wikimedia.org/wiki/File:Ribeir%C3%A3o_da_Ilha_(5418356405).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 2.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/naufragados.webp" alt="Riacho entre pedras na trilha para Naufragados" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Trilha p/ Praia de Naufragados</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.81624846%2C-48.56095677&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir no Waze →</a><small>Foto: Andreia Reis / <a href="https://commons.wikimedia.org/wiki/File:A_caminho_da_Praia_dos_Naufragados_(5375587795).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 2.0)</small></div></article></div>
      </div>
    </section>

    <section id="telefones" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Quando precisar</p><h2>Telefones úteis.</h2></div><p>Contatos para o que você precisar durante a estadia. Toque no número para abrir direto no seu WhatsApp.</p></div>
        <div class="guide-table-wrap"><table class="guide-table"><tbody><tr><td><strong>Farmácia Tele-entrega</strong></td><td><a href="https://api.whatsapp.com/send?phone=5548988559010" target="_blank" rel="noopener">+55 48 98855-9010</a> · Chamar no WhatsApp</td></tr></tbody></table></div>
      </div>
    </section>

    <section id="tv" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Televisão</p><h2>Canais Sky (pacote Smart).</h2></div><p>A TV dos lofts tem o pacote Smart da Sky. Estes são os principais canais e seus números no controle.</p></div>
        <div class="guide-table-wrap"><table class="guide-table"><thead><tr><th scope="col">Categoria</th><th scope="col">Canais</th></tr></thead><tbody><tr><td><strong>Abertos</strong></td><td>TV Cultura 2 · Rit 3 · Rede Vida 6 · Rede Record 7 · Canção Nova 8 · SBT 9 · TV Aparecida 11 · Band 13 · CNT 14 · Rede TV 15 · Record News 19</td></tr><tr><td><strong>Notícias</strong></td><td>Globo News 40 · Climatempo 170</td></tr><tr><td><strong>Filmes e séries</strong></td><td>Megapix 107 · TNT 108 · Cinemax 112 · Canal Brasil 113 · Sony Channel 137 · Warner 139 · Universal 140 · Fox 141</td></tr><tr><td><strong>Variedades</strong></td><td>GNT 41 · Multishow 42 · Viva 43 · +Globosat 44</td></tr><tr><td><strong>Esporte</strong></td><td>SporTV 2 38</td></tr><tr><td><strong>Infantil</strong></td><td>Discovery Kids 50 · Disney Channel 55 · Gloob 56 · Cartoon Network 60</td></tr><tr><td><strong>Públicos</strong></td><td>TV Câmara 22 · TV Justiça 24 · TV Brasil 23 · TV Senado 26</td></tr><tr><td><strong>Música e rádios</strong></td><td>Canais de música 702–763 · Rádios 776–796</td></tr></tbody></table></div>
        <p class="guide-note">Os números podem variar. Consulte o guia de canais na própria TV.</p>
      </div>
    </section>

    <section id="grupo" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Comunicação durante a estadia</p><h2>Grupo da pousada no WhatsApp.</h2></div><p>Durante a estadia, entre no grupo da pousada para receber avisos e falar com a gente e com outros hóspedes.</p></div>
        <a class="button" href="https://chat.whatsapp.com/ECTD6I0cV2C0iAoRbQn3o3" target="_blank" rel="noopener">Entrar no grupo</a>
      </div>
    </section>

    <section id="ajuda" class="guide-section section-paper">
      <div class="container">
        <div class="guide-contact-panel">
          <div>
            <p class="eyebrow">Estamos por perto</p>
            <h2>Ficou com alguma dúvida?</h2>
            <p>Chame o Leo pelo WhatsApp. Estamos à disposição para ajudar com estacionamento, churrasqueira, orientações da pousada ou qualquer outra necessidade durante a estadia.</p>
          </div>
          <a class="button button-light" href="https://api.whatsapp.com/send?phone=5548991223600&text=Ol%C3%A1%2C%20estou%20hospedado(a)%20no%20Campeche%20Lofts%20e%20preciso%20de%20ajuda." target="_blank" rel="noopener">Falar no WhatsApp</a>
        </div>
      </div>
    </section>
  </main>

  <footer class="guide-footer">
    <div class="guide-footer-inner">
      <span>Campeche Lofts · Rua das Corticeiras, 270 · Florianópolis/SC</span>
      <span>Obrigado pela colaboração e tenha uma excelente estadia.</span>
    </div>
  </footer>

  <a class="whatsapp-float" href="https://api.whatsapp.com/send?phone=5548991223600&text=Ol%C3%A1%2C%20estou%20hospedado(a)%20no%20Campeche%20Lofts%20e%20preciso%20de%20ajuda." target="_blank" rel="noopener" aria-label="Falar com o Campeche Lofts pelo WhatsApp">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.88 11.88 0 0 0 12.07 0C5.5 0 .16 5.34.16 11.91c0 2.1.55 4.15 1.6 5.95L.06 24l6.3-1.65a11.9 11.9 0 0 0 5.7 1.45h.01c6.57 0 11.91-5.34 11.91-11.91 0-3.18-1.24-6.16-3.46-8.41ZM12.07 21.8a9.86 9.86 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.38a9.84 9.84 0 1 1 8.37 4.64Zm5.4-7.37c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.18.2-.36.23-.66.08-.3-.15-1.28-.47-2.43-1.5a9.13 9.13 0 0 1-1.68-2.1c-.18-.3-.02-.46.13-.6.14-.14.3-.36.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.03-.53-.08-.15-.68-1.63-.93-2.23-.24-.58-.49-.5-.68-.5h-.58c-.2 0-.53.08-.8.38-.28.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.56.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.08-.13-.28-.2-.58-.35Z"/></svg>
  </a>
</body>
</html>
