<?php
require __DIR__ . '/session.php';
if (empty($_SESSION['guide_authenticated'])) {
    header('Location: login.php', true, 302);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es" translate="no">
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
  <meta name="description" content="Guía del huésped de Campeche Lofts: convivencia, estacionamiento, portón, Wi-Fi e información práctica.">
  <meta name="robots" content="noindex, nofollow">
  <title>Guía del huésped | Campeche Lofts</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:opsz,wght@5..1200,500;5..1200,600;5..1200,700&display=swap"><link rel="stylesheet" href="campeche.css?v=20261010-trilhas">
</head>
<body>
  <a class="skip-link" href="#contenido">Ir al contenido</a>

  <header class="site-header guide-header">
    <div class="header-inner">
      <a class="brand" href="../index.html" aria-label="Campeche Lofts — página inicial">
        <span class="brand-logo-frame"><img class="brand-logo" src="images/logo_campeche_lofts_horizontal.webp" alt="Campeche Lofts" width="460" height="307"></span>
      </a>
      <div class="guide-header-actions">
        <a class="guide-language" href="index.php" lang="pt-BR" hreflang="pt-BR" aria-label="Ver este guia em português">PT</a>
        <a class="guide-language" href="english.php" lang="en" hreflang="en" aria-label="View this guide in English">EN</a>
        <a class="guide-contact" data-short-label="Ayuda" href="https://api.whatsapp.com/send?phone=5548991223600&text=Hola%2C%20tengo%20una%20consulta%20sobre%20la%20Gu%C3%ADa%20del%20hu%C3%A9sped." target="_blank" rel="noopener">¿Necesita ayuda?</a>
      </div>
    </div>
  </header>

  <main id="contenido">
    <section class="guide-hero">
      <div class="container">
        <p class="eyebrow">Campeche Lofts · Florianópolis/SC</p>
        <h1>Guía del<br><em>huésped.</em></h1>
        <p>Información práctica para que su estadía sea tranquila, cómoda y segura. Guarde este enlace para consultarlo siempre que lo necesite.</p>
        <span class="guide-hero-badge">Lectura rápida para su estadía</span>
      </div>
    </section>

    <nav class="guide-nav" aria-label="Navegación de la guía">
      <div class="guide-nav-inner">
        <a href="#convivencia">Convivencia</a><a href="#porton">Portón y estacionamiento</a><a href="#wifi">Wi‑Fi</a><a href="#delivery">Delivery</a><a href="#parrilla">Parrilla</a><a href="#residuos">Residuos</a><a href="#lavanderia">Lavandería</a><a href="#playa">Playa</a><a href="#senderos">Senderos</a><a href="#telefonos">Teléfonos</a><a href="#tv">TV Sky</a><a href="#ayuda">Ayuda</a>
      </div>
    </nav>

    <section class="guide-section section-paper">
      <div class="container guide-intro">
        <div class="guide-intro-copy">
          <p class="eyebrow">Bienvenido</p>
          <h2>Qué bueno tenerlo en Campeche Lofts.</h2>
          <p>Queremos que disfrute la autonomía de su loft con la tranquilidad de un ambiente familiar. Esta guía reúne los cuidados simples que ayudan a que la estadía de todos transcurra bien.</p>
        </div>
        <aside class="guide-alert" aria-label="Información importante">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 1 21h22L12 2Zm1 16h-2v-2h2v2Zm0-4h-2v-4h2v4Z"/></svg>
          <strong>Si tiene alguna duda, contáctenos antes de actuar.</strong>
          <p>Un mensaje rápido por WhatsApp evita imprevistos y nos permite ayudarlo de la mejor manera.</p>
        </aside>
      </div>
    </section>

    <section id="convivencia" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Buena convivencia</p><h2>Cuidados esenciales.</h2></div><p>Los lofts alojan hasta <strong>3 huéspedes</strong>. Para el bienestar de todos, le pedimos atención a estas normas durante su estadía.</p></div>
        <div class="guide-rules">
          <article class="guide-rule"><span class="guide-rule-number">01</span><strong>Silencio</strong><p>Respete el horario de silencio entre las <strong>22:00 y las 9:00</strong>.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">02</span><strong>Visitas y seguridad</strong><p>No se permiten visitas en la posada. Por favor, no permita el ingreso de personas desconocidas.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">03</span><strong>Ambiente libre de humo</strong><p>No fume dentro del loft ni en lugares donde el humo pueda molestar a otros huéspedes.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">04</span><strong>Al salir del loft</strong><p>Apague el aire acondicionado y compruebe que la llave de gas de la cocina esté cerrada.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">05</span><strong>Voltaje</strong><p>Los enchufes son de <strong>220 V</strong>. Verifique sus aparatos antes de conectarlos.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">06</span><strong>Buen criterio</strong><p>Cuide el loft y las áreas compartidas como si fueran su propia casa.</p></article>
        </div>
      </div>
    </section>

    <section id="porton" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Al entrar y salir</p><h2>Portón y estacionamiento.</h2></div><p>El portón electrónico y las cocheras son áreas compartidas. Algunos cuidados simples ayudan a mantener la seguridad y facilitan el estacionamiento para todos.</p></div>
        <ol class="guide-step-list">
          <li><strong>Cuente con 30 segundos</strong>El portón se cierra automáticamente en aproximadamente 30 segundos. Al salir, tenga esto presente mientras espera que pase el tránsito de la calle.</li>
          <li><strong>¿Necesita más tiempo?</strong>Si es necesario, retroceda un poco, active el portón nuevamente y tendrá otros 30 segundos para salir con seguridad.</li>
          <li><strong>Ciérrelo enseguida</strong>Al entrar o salir, pulse el botón inmediatamente para iniciar el cierre. No deje el portón abierto más tiempo del necesario.</li>
          <li><strong>Cuidado con el control</strong>Dentro del loft, evite pulsar el control remoto sin querer. El portón puede activarse mientras uno de nuestros autos está en su camino.</li>
          <li><strong>Respete las líneas amarillas</strong>Para que todos tengan una buena cochera, estacione siempre dentro de las demarcaciones amarillas del patio.</li>
          <li><strong>Consulte el plano de estacionamiento</strong>En cada loft hay un plano con las formas correctas e incorrectas de estacionar. Si tiene dudas, contáctenos.</li>
        </ol>
        <p class="guide-note">Cada apartamento tiene derecho a una cochera, siempre que el vehículo esté correctamente posicionado. Gracias por colaborar para que todos tengan una plaza práctica y segura.</p>
        <figure class="guide-parking-map">
          <img src="images/mapa-estacionamento.png" alt="Mapa del estacionamiento con ejemplos de posicionamiento correcto e incorrecto de los vehículos" loading="lazy">
          <figcaption>Consulte este plano antes de estacionar. Muestra las posiciones correctas y las que se deben evitar para que todos puedan usar su cochera correctamente.</figcaption>
        </figure>
      </div>
    </section>

    <section id="wifi" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Conexión</p><h2>Internet Wi‑Fi.</h2></div><p>Elija la red correspondiente a su loft. La contraseña es la misma para todas las redes y también está disponible en la hoja informativa dentro del apartamento.</p></div>
        <div class="guide-wifi">
          <aside class="wifi-password"><span>Contraseña de todas las redes</span><strong>32349758</strong><p>Utilice la red indicada para su loft.</p></aside>
          <div class="guide-table-wrap"><table class="guide-table"><thead><tr><th scope="col">Red Wi‑Fi</th><th scope="col">Apartamentos atendidos</th></tr></thead><tbody><tr><td><strong>Pousada C01</strong></td><td>Apartamentos 02 y 03</td></tr><tr><td><strong>P002</strong></td><td>Lofts 04 y 05</td></tr><tr><td><strong>P003</strong></td><td>Lofts 07 y 09</td></tr><tr><td><strong>P004</strong></td><td>Lofts 06 y 08</td></tr></tbody></table></div>
        </div>
      </div>
    </section>

    <section id="delivery" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Seguridad y practicidad</p><h2>Delivery y entregas.</h2></div><p>Al pedir delivery, informe el número de su loft y pida que el repartidor toque el intercomunicador. El intercomunicador funciona solo como timbre.</p></div>
        <div class="guide-split">
          <article class="guide-card"><span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 8h-3V4H3v12h2a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h1v-4l-2-4Zm-12 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm9-7V6.5L19.5 10H17Zm1 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/></svg></span><h3>Retire el pedido en el portón.</h3><p>Busque el pedido en el portón principal. Por seguridad, repartidores y mensajeros no deben entrar a la posada.</p></article>
          <article class="guide-card"><span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm5 17a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm-4-5h8V5H8v9Z"/></svg></span><h3>Informe su número de loft.</h3><p>Al hacer el pedido, indique el número correcto del apartamento y pida que el repartidor toque el intercomunicador.</p></article>
        </div>
      </div>
    </section>

    <section id="parrilla" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Uso compartido</p><h2>Parrilla.</h2></div><p>La parrilla puede usarse hasta las <strong>24:00</strong>. Antes de usarla, solicite autorización por WhatsApp.</p></div>
        <div class="guide-split">
          <article class="guide-card"><span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3h8v2h2v2h-2v3a4 4 0 0 1-3 3.87V17h2v2H9v-2h2v-3.13A4 4 0 0 1 8 10V7H6V5h2V3Zm2 4v3a2 2 0 0 0 4 0V7h-4Z"/></svg></span><h3>Pida autorización.</h3><p>Contáctenos por WhatsApp antes de usarla para confirmar que el espacio está disponible.</p></article>
          <article class="guide-card"><span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h10l1 18H6L7 3Zm3 3v3h4V6h-4Zm0 6v6h4v-6h-4Z"/></svg></span><h3>Déjela como la encontró.</h3><p>Después de usarla, limpie la parrilla y el mostrador. Se podrá aplicar una tasa de limpieza si el espacio queda sucio.</p></article>
        </div>
      </div>
    </section>

    <section id="residuos" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Orden en el patio</p><h2>Recolección de residuos.</h2></div><p>Coloque todos los residuos en bolsas y deséchelos en el recipiente adecuado, situado junto al portón de acceso.</p></div>
        <div class="guide-table-wrap"><table class="guide-table"><thead><tr><th scope="col">Recipiente</th><th scope="col">Descarte correcto</th></tr></thead><tbody><tr><td><strong>Cesto de madera</strong></td><td>Residuos secos que no atraigan moscas, larvas ni cucarachas.</td></tr><tr><td><strong>Contenedores de plástico</strong></td><td>Todos los demás tipos de residuos, siempre bien embolsados.</td></tr></tbody></table></div>
      </div>
    </section>

    <section id="lavanderia" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Uso compartido</p>
            <h2>Lavandería.</h2>
          </div>
          <p>La posada cuenta con una <strong>lavandería colectiva</strong>, de uso <strong>gratuito</strong> para los huéspedes. Solo necesitás comprar tu jabón.</p>
        </div>
        <figure class="guide-parking-map" style="max-width:560px">
          <img src="images/lavanderia.webp" alt="Lavandería colectiva de la posada, con lavarropas y mesada" width="1086" height="1448" loading="lazy">
          <figcaption>Lavandería colectiva de la posada: uso gratuito, solo tenés que traer tu jabón.</figcaption>
        </figure>
      </div>
    </section>

    <section id="playa" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">A pie hasta el mar</p><h2>Camino a la playa.</h2></div><p>De la posada a la orilla de la playa son pocos minutos a pie. El recorrido está marcado en rojo en el mapa.</p></div>
        <figure class="guide-parking-map"><img src="images/caminho-praia.webp" alt="Mapa aéreo con el camino a pie de la posada hasta la Playa de Campeche, marcado en rojo" width="1200" height="693" loading="lazy"><figcaption>Camino a pie de la posada hasta la orilla de la playa (escala de 100 m en el mapa).</figcaption></figure>
      </div>
    </section>

    <section id="senderos" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Naturaleza alrededor</p><h2>Senderos.</h2></div><p>Sugerencias de senderos y paseos desde Campeche. Tocá “Abrir en Waze” para trazar la ruta hasta el punto de partida.</p></div>
        <div class="guide-trails"><article class="guide-trail"><div class="guide-trail-body"><h3>Morro do Lampião</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.66506710%2C-48.48798890&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir en Waze →</a></div></article><article class="guide-trail"><img src="images/trilhas/morro-das-pedras.webp" alt="Rocas junto al mar en Morro das Pedras, con cerros al fondo" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Mirador del Morro das Pedras</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.72027187%2C-48.50332044&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir en Waze →</a><small>Foto: Jeferson Felix / <a href="https://commons.wikimedia.org/wiki/File:Morro_das_Pedras,_Florian%C3%B3polis_-_Brasil_-_panoramio.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/lagoa-do-peri.webp" alt="Cascada entre piedras y selva en la Lagoa do Peri" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Lagoa do Peri / Cascada</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.72598956%2C-48.50712885&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir en Waze →</a><small>Foto: Sovernigo / <a href="https://commons.wikimedia.org/wiki/File:Cachoeira_da_Lagoa_do_Peri.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY-SA 4.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/matadeiro.webp" alt="Vista desde lo alto de la playa de Matadeiro y Armação" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Armação / Ponta das Campanhas / Matadeiro</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.75062217%2C-48.50271961&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir en Waze →</a><small>Foto: Marco Rosa / <a href="https://commons.wikimedia.org/wiki/File:Matadeiro_e_Arma%C3%A7%C3%A3o_-_panoramio.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/lagoinha-do-leste.webp" alt="Playa Lagoinha do Leste vista desde arriba, entre cerros verdes" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Lagoinha do Leste / Pedra da Coroa</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.77882809%2C-48.50727912&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir en Waze →</a><small>Foto: Paulo Cameli / <a href="https://commons.wikimedia.org/wiki/File:Morro_Da_Coroa_Praia_Lagoinha_Do_Leste_(242401955).jpeg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/solidao.webp" alt="Costa rocosa y mar cerca de la Playa do Saquinho" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Solidão / Playa do Saquinho / Cascada da Solidão</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.79477451%2C-48.53468962&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir en Waze →</a><small>Foto: Papa Pic / <a href="https://commons.wikimedia.org/wiki/File:Alto_saquinho_(16771033008).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC0)</small></div></article><article class="guide-trail"><img src="images/trilhas/costa-de-dentro.webp" alt="Barcos y muelle en la bahía de Ribeirão da Ilha" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Estrada da Costa de Dentro hasta Ribeirão da Ilha</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.78544424%2C-48.53216880&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir en Waze →</a><small>Foto: Andreia Reis / <a href="https://commons.wikimedia.org/wiki/File:Ribeir%C3%A3o_da_Ilha_(5418356405).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 2.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/naufragados.webp" alt="Arroyo entre piedras en el sendero a Naufragados" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Sendero a la Playa de Naufragados</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.81624846%2C-48.56095677&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Abrir en Waze →</a><small>Foto: Andreia Reis / <a href="https://commons.wikimedia.org/wiki/File:A_caminho_da_Praia_dos_Naufragados_(5375587795).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 2.0)</small></div></article></div>
      </div>
    </section>

    <section id="telefonos" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Cuando lo necesites</p><h2>Teléfonos útiles.</h2></div><p>Contactos para lo que necesites durante tu estadía. Tocá el número para abrirlo directo en tu WhatsApp.</p></div>
        <div class="guide-table-wrap"><table class="guide-table"><tbody><tr><td><strong>Farmacia Tele-entrega</strong></td><td><a href="https://api.whatsapp.com/send?phone=5548988559010" target="_blank" rel="noopener">+55 48 98855-9010</a> · Escribir por WhatsApp</td></tr></tbody></table></div>
      </div>
    </section>

    <section id="tv" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Televisión</p><h2>Canales Sky (paquete Smart).</h2></div><p>El TV de los lofts tiene el paquete Smart de Sky. Estos son los principales canales y su número en el control remoto.</p></div>
        <div class="guide-table-wrap"><table class="guide-table"><thead><tr><th scope="col">Categoría</th><th scope="col">Canales</th></tr></thead><tbody><tr><td><strong>Abiertos</strong></td><td>TV Cultura 2 · Rit 3 · Rede Vida 6 · Rede Record 7 · Canção Nova 8 · SBT 9 · TV Aparecida 11 · Band 13 · CNT 14 · Rede TV 15 · Record News 19</td></tr><tr><td><strong>Noticias</strong></td><td>Globo News 40 · Climatempo 170</td></tr><tr><td><strong>Películas y series</strong></td><td>Megapix 107 · TNT 108 · Cinemax 112 · Canal Brasil 113 · Sony Channel 137 · Warner 139 · Universal 140 · Fox 141</td></tr><tr><td><strong>Variedades</strong></td><td>GNT 41 · Multishow 42 · Viva 43 · +Globosat 44</td></tr><tr><td><strong>Deportes</strong></td><td>SporTV 2 38</td></tr><tr><td><strong>Infantil</strong></td><td>Discovery Kids 50 · Disney Channel 55 · Gloob 56 · Cartoon Network 60</td></tr><tr><td><strong>Públicos</strong></td><td>TV Câmara 22 · TV Justiça 24 · TV Brasil 23 · TV Senado 26</td></tr><tr><td><strong>Música y radios</strong></td><td>Canales de música 702–763 · Radios 776–796</td></tr></tbody></table></div>
        <p class="guide-note">Los números pueden variar. Consultá la guía de canales en el propio TV.</p>
      </div>
    </section>

    <section id="ayuda" class="guide-section section-paper">
      <div class="container"><div class="guide-contact-panel"><div><p class="eyebrow">Estamos cerca</p><h2>¿Tiene alguna duda?</h2><p>Escríbale a Leo por WhatsApp. Estamos disponibles para ayudar con el estacionamiento, la parrilla, las orientaciones de la posada o cualquier otra necesidad durante su estadía.</p></div><a class="button button-light" href="https://api.whatsapp.com/send?phone=5548991223600&text=Hola%2C%20estoy%20hospedado(a)%20en%20Campeche%20Lofts%20y%20necesito%20ayuda." target="_blank" rel="noopener">Hablar por WhatsApp</a></div></div>
    </section>
  </main>

  <footer class="guide-footer"><div class="guide-footer-inner"><span>Campeche Lofts · Rua das Corticeiras, 270 · Florianópolis/SC</span><span>Gracias por su colaboración. Le deseamos una excelente estadía.</span></div></footer>
  <a class="whatsapp-float" href="https://api.whatsapp.com/send?phone=5548991223600&text=Hola%2C%20estoy%20hospedado(a)%20en%20Campeche%20Lofts%20y%20necesito%20ayuda." target="_blank" rel="noopener" aria-label="Hablar con Campeche Lofts por WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.88 11.88 0 0 0 12.07 0C5.5 0 .16 5.34.16 11.91c0 2.1.55 4.15 1.6 5.95L.06 24l6.3-1.65a11.9 11.9 0 0 0 5.7 1.45h.01c6.57 0 11.91-5.34 11.91-11.91 0-3.18-1.24-6.16-3.46-8.41ZM12.07 21.8a9.86 9.86 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.38a9.84 9.84 0 1 1 8.37 4.64Zm5.4-7.37c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.18.2-.36.23-.66.08-.3-.15-1.28-.47-2.43-1.5a9.13 9.13 0 0 1-1.68-2.1c-.18-.3-.02-.46.13-.6.14-.14.3-.36.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.03-.53-.08-.15-.68-1.63-.93-2.23-.24-.58-.49-.5-.68-.5h-.58c-.2 0-.53.08-.8.38-.28.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.56.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.08-.13-.28-.2-.58-.35Z"/></svg></a>
</body>
</html>
