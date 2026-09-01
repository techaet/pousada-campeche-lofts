<?php
require __DIR__ . '/session.php';
if (empty($_SESSION['guide_authenticated'])) {
    header('Location: login.php', true, 302);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Guia do hóspede do Campeche Lofts: convivência, estacionamento, portão, Wi-Fi e orientações práticas.">
  <meta name="robots" content="noindex, nofollow">
  <title>Guia do Hóspede | Campeche Lofts</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="campeche.css?v=20260820-guia-protegido-v1">
</head>
<body>
  <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>

  <header class="site-header guide-header">
    <div class="header-inner">
      <a class="brand" href="../index.html" aria-label="Campeche Lofts — página inicial">
        <span class="brand-logo-frame">
          <img class="brand-logo" src="images/logo_campeche_lofts_horizontal.webp" alt="Campeche Lofts">
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

    <section id="ajuda" class="guide-section section-white">
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
