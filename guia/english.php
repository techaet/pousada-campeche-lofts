<?php
require __DIR__ . '/session.php';
if (empty($_SESSION['guide_authenticated'])) {
    header('Location: login.php', true, 302);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" translate="no">
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
  <meta name="description" content="Campeche Lofts Guest Guide: house rules, parking, gate, Wi-Fi and practical information.">
  <meta name="robots" content="noindex, nofollow">
  <title>Guest Guide | Campeche Lofts</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:opsz,wght@5..1200,500;5..1200,600;5..1200,700&display=swap"><link rel="stylesheet" href="campeche.css?v=20261010-trilhas">
</head>
<body>
  <a class="skip-link" href="#content">Skip to content</a>

  <header class="site-header guide-header">
    <div class="header-inner">
      <a class="brand" href="../index.html" aria-label="Campeche Lofts — home page">
        <span class="brand-logo-frame"><img class="brand-logo" src="images/logo_campeche_lofts_horizontal.webp" alt="Campeche Lofts" width="460" height="307"></span>
      </a>
      <div class="guide-header-actions">
        <a class="guide-language" href="index.php" lang="pt-BR" hreflang="pt-BR" aria-label="Ver este guia em português">PT</a>
        <a class="guide-language" href="espanol.php" lang="es" hreflang="es" aria-label="Ver esta guía en español">ES</a>
        <a class="guide-contact" data-short-label="Help" href="https://api.whatsapp.com/send?phone=5548991223600&text=Hello%2C%20I%20have%20a%20question%20about%20the%20Guest%20Guide." target="_blank" rel="noopener">Need help?</a>
      </div>
    </div>
  </header>

  <main id="content">
    <section class="guide-hero">
      <div class="container">
        <p class="eyebrow">Campeche Lofts · Florianópolis/SC</p>
        <h1>Guest<br><em>Guide.</em></h1>
        <p>Practical information for a calm, comfortable and safe stay. Save this link so you can check it whenever you need to.</p>
        <span class="guide-hero-badge">A quick guide for your stay</span>
      </div>
    </section>

    <nav class="guide-nav" aria-label="Guide navigation">
      <div class="guide-nav-inner">
        <a href="#house-rules">House rules</a><a href="#gate">Gate &amp; parking</a><a href="#wifi">Wi‑Fi</a><a href="#deliveries">Deliveries</a><a href="#barbecue">Barbecue</a><a href="#rubbish">Rubbish</a><a href="#laundry">Laundry</a><a href="#beach">Beach</a><a href="#trails">Trails</a><a href="#phones">Phones</a><a href="#tv">Sky TV</a><a href="#group">Group</a><a href="#help">Help</a>
      </div>
    </nav>

    <section class="guide-section section-paper">
      <div class="container guide-intro">
        <div class="guide-intro-copy">
          <p class="eyebrow">Welcome</p>
          <h2>We are delighted to have you at Campeche Lofts.</h2>
          <p>We want you to enjoy the independence of your loft with the peace of mind of a friendly, family-oriented environment. This guide brings together the simple precautions that help everyone have a smooth stay.</p>
        </div>
        <aside class="guide-alert" aria-label="Important information">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 1 21h22L12 2Zm1 16h-2v-2h2v2Zm0-4h-2v-4h2v4Z"/></svg>
          <strong>If you are unsure about anything, please contact us before taking action.</strong>
          <p>A quick WhatsApp message prevents unexpected situations and lets us help you properly.</p>
        </aside>
      </div>
    </section>

    <section id="house-rules" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Living well together</p><h2>Essential care.</h2></div><p>Each loft accommodates up to <strong>3 guests</strong>. For everyone’s comfort, please observe these house rules throughout your stay.</p></div>
        <div class="guide-rules">
          <article class="guide-rule"><span class="guide-rule-number">01</span><strong>Quiet hours</strong><p>Please respect quiet hours from <strong>10 pm to 9 am</strong>.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">02</span><strong>Visitors and safety</strong><p>Visitors are not allowed on the property. Please do not let unknown people in.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">03</span><strong>Smoke-free environment</strong><p>Please do not smoke inside the loft or where smoke may disturb other guests.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">04</span><strong>When leaving the loft</strong><p>Turn off the air conditioning and make sure the stove gas valve is closed.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">05</span><strong>Voltage</strong><p>All outlets are <strong>220 V</strong>. Please check your devices before plugging them in.</p></article>
          <article class="guide-rule"><span class="guide-rule-number">06</span><strong>Consideration</strong><p>Please take care of the loft and common areas as if they were your own home.</p></article>
        </div>
      </div>
    </section>

    <section id="gate" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">When entering and leaving</p><h2>Gate and parking.</h2></div><p>The electronic gate and parking spaces are shared areas. A few simple precautions help keep everyone safe and make parking easier for all guests.</p></div>
        <ol class="guide-step-list">
          <li><strong>Allow 30 seconds</strong>The gate closes automatically in approximately 30 seconds. When leaving, keep this in mind while waiting for traffic on the street to clear.</li>
          <li><strong>Need more time?</strong>If necessary, reverse slightly, activate the gate again and you will have another 30 seconds to leave safely.</li>
          <li><strong>Close it right away</strong>When entering or leaving, press the button immediately to begin closing the gate. Please do not leave it open longer than necessary.</li>
          <li><strong>Take care with the remote</strong>Inside the loft, avoid pressing the remote unintentionally. The gate may activate while one of our cars is in its path.</li>
          <li><strong>Respect the yellow lines</strong>To make sure everyone has a good parking space, always park within the yellow markings in the courtyard.</li>
          <li><strong>Check the parking map</strong>Each loft has a parking map showing the correct and incorrect ways to park. Please contact us if you have any questions.</li>
        </ol>
        <p class="guide-note">Each apartment is entitled to one parking space, provided the vehicle is correctly positioned. Thank you for helping to keep parking simple and safe for everyone.</p>
        <figure class="guide-parking-map">
          <img src="images/mapa-estacionamento.png" alt="Parking map with correct and incorrect vehicle-positioning examples" loading="lazy">
          <figcaption>Check this map before parking. It shows the correct positions and the ones to avoid so that everyone can use their space properly.</figcaption>
        </figure>
      </div>
    </section>

    <section id="wifi" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Connection</p><h2>Wi‑Fi Internet.</h2></div><p>Choose the network assigned to your loft. The password is the same for every network and is also available on the information sheet inside the apartment.</p></div>
        <div class="guide-wifi">
          <aside class="wifi-password"><span>Password for all networks</span><strong>32349758</strong><p>Please use the network indicated for your loft.</p></aside>
          <div class="guide-table-wrap"><table class="guide-table"><thead><tr><th scope="col">Wi‑Fi network</th><th scope="col">Apartments served</th></tr></thead><tbody><tr><td><strong>Pousada C01</strong></td><td>Apartments 02 and 03</td></tr><tr><td><strong>P002</strong></td><td>Lofts 04 and 05</td></tr><tr><td><strong>P003</strong></td><td>Lofts 07 and 09</td></tr><tr><td><strong>P004</strong></td><td>Lofts 06 and 08</td></tr></tbody></table></div>
        </div>
      </div>
    </section>

    <section id="deliveries" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Safety and convenience</p><h2>Food deliveries.</h2></div><p>When placing a delivery order, provide your loft number and ask the driver to ring the intercom. The intercom works only as a doorbell.</p></div>
        <div class="guide-split">
          <article class="guide-card"><span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 8h-3V4H3v12h2a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h1v-4l-2-4Zm-12 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm9-7V6.5L19.5 10H17Zm1 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/></svg></span><h3>Collect it at the gate.</h3><p>Please meet the driver at the main gate. For everyone’s safety, delivery drivers must not enter the property.</p></article>
          <article class="guide-card"><span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm5 17a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm-4-5h8V5H8v9Z"/></svg></span><h3>Provide your loft number.</h3><p>When ordering, give the correct apartment number and ask the driver to ring the intercom.</p></article>
        </div>
      </div>
    </section>

    <section id="barbecue" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Shared facility</p><h2>Barbecue area.</h2></div><p>The barbecue area can be used until <strong>midnight</strong>. Please ask for permission via WhatsApp before using it.</p></div>
        <div class="guide-split">
          <article class="guide-card"><span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3h8v2h2v2h-2v3a4 4 0 0 1-3 3.87V17h2v2H9v-2h2v-3.13A4 4 0 0 1 8 10V7H6V5h2V3Zm2 4v3a2 2 0 0 0 4 0V7h-4Z"/></svg></span><h3>Ask for permission.</h3><p>Please contact us on WhatsApp before use so we can confirm the area is available.</p></article>
          <article class="guide-card"><span class="guide-card-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h10l1 18H6L7 3Zm3 3v3h4V6h-4Zm0 6v6h4v-6h-4Z"/></svg></span><h3>Leave it as you found it.</h3><p>After use, clean the barbecue and countertop. A cleaning fee may apply if the area is left dirty.</p></article>
        </div>
      </div>
    </section>

    <section id="rubbish" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Keeping the courtyard tidy</p><h2>Rubbish collection.</h2></div><p>Please place all rubbish in bags and dispose of it in the appropriate container next to the entrance gate.</p></div>
        <div class="guide-table-wrap"><table class="guide-table"><thead><tr><th scope="col">Container</th><th scope="col">Correct disposal</th></tr></thead><tbody><tr><td><strong>Wooden bin</strong></td><td>Dry waste that will not attract flies, larvae or cockroaches.</td></tr><tr><td><strong>Plastic bins</strong></td><td>All other types of rubbish, always properly bagged.</td></tr></tbody></table></div>
      </div>
    </section>

    <section id="laundry" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head">
          <div>
            <p class="eyebrow">Shared space</p>
            <h2>Laundry.</h2>
          </div>
          <p>The guesthouse has a <strong>shared laundry</strong> that guests can use <strong>free of charge</strong>. You only need to buy your own detergent.</p>
        </div>
        <figure class="guide-parking-map" style="max-width:560px">
          <img src="images/lavanderia.webp" alt="Shared laundry at the guesthouse, with washing machines and a countertop" width="1086" height="1448" loading="lazy">
          <figcaption>Shared laundry: free to use, just bring your own detergent.</figcaption>
        </figure>
      </div>
    </section>

    <section id="beach" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">A walk to the sea</p><h2>Way to the beach.</h2></div><p>It is just a few minutes on foot from the guesthouse to the water’s edge. The route is marked in red on the map.</p></div>
        <figure class="guide-parking-map"><img src="images/caminho-praia.webp" alt="Aerial map showing the walking route from the guesthouse to Campeche Beach, marked in red" width="1200" height="693" loading="lazy"><figcaption>Walking route from the guesthouse to the beach (100 m scale on the map).</figcaption></figure>
      </div>
    </section>

    <section id="trails" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Nature around us</p><h2>Trails.</h2></div><p>Trail and outing suggestions starting from Campeche. Tap “Open in Waze” to get directions to the starting point.</p></div>
        <div class="guide-trails"><article class="guide-trail"><img src="images/trilhas/morro-do-lampiao.webp" alt="Aerial view of Campeche and Campeche Island from Morro do Lampião" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Morro do Lampião</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.66506710%2C-48.48798890&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Open in Waze →</a><small>Photo: Thayran Melo / <a href="https://unsplash.com/photos/ldd7qEB4UO0" target="_blank" rel="noopener">Unsplash</a></small></div></article><article class="guide-trail"><img src="images/trilhas/morro-das-pedras.webp" alt="Rocks by the sea at Morro das Pedras, with hills in the background" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Morro das Pedras viewpoint</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.72027187%2C-48.50332044&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Open in Waze →</a><small>Photo: Jeferson Felix / <a href="https://commons.wikimedia.org/wiki/File:Morro_das_Pedras,_Florian%C3%B3polis_-_Brasil_-_panoramio.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/lagoa-do-peri.webp" alt="Waterfall among rocks and forest at Lagoa do Peri" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Lagoa do Peri / Waterfall</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.72598956%2C-48.50712885&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Open in Waze →</a><small>Photo: Sovernigo / <a href="https://commons.wikimedia.org/wiki/File:Cachoeira_da_Lagoa_do_Peri.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY-SA 4.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/matadeiro.webp" alt="View from above of Matadeiro and Armação beaches" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Armação / Ponta das Campanhas / Matadeiro</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.75062217%2C-48.50271961&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Open in Waze →</a><small>Photo: Marco Rosa / <a href="https://commons.wikimedia.org/wiki/File:Matadeiro_e_Arma%C3%A7%C3%A3o_-_panoramio.jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/lagoinha-do-leste.webp" alt="Lagoinha do Leste beach seen from above, between green hills" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Lagoinha do Leste / Pedra da Coroa</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.77882809%2C-48.50727912&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Open in Waze →</a><small>Photo: Paulo Cameli / <a href="https://commons.wikimedia.org/wiki/File:Morro_Da_Coroa_Praia_Lagoinha_Do_Leste_(242401955).jpeg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 3.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/solidao.webp" alt="Rocky coast and sea near Saquinho Beach" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Solidão / Saquinho Beach / Solidão Waterfall</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.79477451%2C-48.53468962&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Open in Waze →</a><small>Photo: Papa Pic / <a href="https://commons.wikimedia.org/wiki/File:Alto_saquinho_(16771033008).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC0)</small></div></article><article class="guide-trail"><img src="images/trilhas/costa-de-dentro.webp" alt="Boats and a pier in the Ribeirão da Ilha bay" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Costa de Dentro road to Ribeirão da Ilha</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.78544424%2C-48.53216880&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Open in Waze →</a><small>Photo: Andreia Reis / <a href="https://commons.wikimedia.org/wiki/File:Ribeir%C3%A3o_da_Ilha_(5418356405).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 2.0)</small></div></article><article class="guide-trail"><img src="images/trilhas/naufragados.webp" alt="Stream among rocks on the trail to Naufragados" width="800" height="533" loading="lazy"><div class="guide-trail-body"><h3>Trail to Naufragados Beach</h3><a class="guide-trail-go" href="https://ul.waze.com/ul?ll=-27.81624846%2C-48.56095677&amp;navigate=yes&amp;zoom=17" target="_blank" rel="noopener">Open in Waze →</a><small>Photo: Andreia Reis / <a href="https://commons.wikimedia.org/wiki/File:A_caminho_da_Praia_dos_Naufragados_(5375587795).jpg" target="_blank" rel="noopener">Wikimedia Commons</a> (CC BY 2.0)</small></div></article></div>
      </div>
    </section>

    <section id="phones" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">When you need it</p><h2>Useful phone numbers.</h2></div><p>Contacts for anything you need during your stay. Tap the number to open it straight in your WhatsApp.</p></div>
        <div class="guide-table-wrap"><table class="guide-table"><tbody><tr><td><strong>Pharmacy (home delivery)</strong></td><td><a href="https://api.whatsapp.com/send?phone=5548988559010" target="_blank" rel="noopener">+55 48 98855-9010</a> · Chat on WhatsApp</td></tr></tbody></table></div>
      </div>
    </section>

    <section id="tv" class="guide-section section-white">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Television</p><h2>Sky channels (Smart package).</h2></div><p>The lofts’ TV has Sky’s Smart package. These are the main channels and their numbers on the remote.</p></div>
        <div class="guide-table-wrap"><table class="guide-table"><thead><tr><th scope="col">Category</th><th scope="col">Channels</th></tr></thead><tbody><tr><td><strong>Free-to-air</strong></td><td>TV Cultura 2 · Rit 3 · Rede Vida 6 · Rede Record 7 · Canção Nova 8 · SBT 9 · TV Aparecida 11 · Band 13 · CNT 14 · Rede TV 15 · Record News 19</td></tr><tr><td><strong>News</strong></td><td>Globo News 40 · Climatempo 170</td></tr><tr><td><strong>Movies and series</strong></td><td>Megapix 107 · TNT 108 · Cinemax 112 · Canal Brasil 113 · Sony Channel 137 · Warner 139 · Universal 140 · Fox 141</td></tr><tr><td><strong>Variety</strong></td><td>GNT 41 · Multishow 42 · Viva 43 · +Globosat 44</td></tr><tr><td><strong>Sports</strong></td><td>SporTV 2 38</td></tr><tr><td><strong>Kids</strong></td><td>Discovery Kids 50 · Disney Channel 55 · Gloob 56 · Cartoon Network 60</td></tr><tr><td><strong>Public</strong></td><td>TV Câmara 22 · TV Justiça 24 · TV Brasil 23 · TV Senado 26</td></tr><tr><td><strong>Music and radio</strong></td><td>Music channels 702–763 · Radio 776–796</td></tr></tbody></table></div>
        <p class="guide-note">Numbers may vary. Check the channel guide on the TV itself.</p>
      </div>
    </section>

    <section id="group" class="guide-section section-paper">
      <div class="container">
        <div class="guide-section-head"><div><p class="eyebrow">Communication during your stay</p><h2>Guesthouse WhatsApp group.</h2></div><p>During your stay, join the guesthouse group to get notices and chat with us and other guests.</p></div>
        <a class="button" href="https://chat.whatsapp.com/ECTD6I0cV2C0iAoRbQn3o3" target="_blank" rel="noopener">Join the group</a>
      </div>
    </section>

    <section id="help" class="guide-section section-paper">
      <div class="container"><div class="guide-contact-panel"><div><p class="eyebrow">We are nearby</p><h2>Do you have any questions?</h2><p>Please message Leo on WhatsApp. We are available to help with parking, the barbecue area, property guidance or anything else you may need during your stay.</p></div><a class="button button-light" href="https://api.whatsapp.com/send?phone=5548991223600&text=Hello%2C%20I%20am%20staying%20at%20Campeche%20Lofts%20and%20need%20help." target="_blank" rel="noopener">Message us on WhatsApp</a></div></div>
    </section>
  </main>

  <footer class="guide-footer"><div class="guide-footer-inner"><span>Campeche Lofts · Rua das Corticeiras, 270 · Florianópolis/SC</span><span>Thank you for your cooperation. We wish you a wonderful stay.</span></div></footer>
  <a class="whatsapp-float" href="https://api.whatsapp.com/send?phone=5548991223600&text=Hello%2C%20I%20am%20staying%20at%20Campeche%20Lofts%20and%20need%20help." target="_blank" rel="noopener" aria-label="Message Campeche Lofts on WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.88 11.88 0 0 0 12.07 0C5.5 0 .16 5.34.16 11.91c0 2.1.55 4.15 1.6 5.95L.06 24l6.3-1.65a11.9 11.9 0 0 0 5.7 1.45h.01c6.57 0 11.91-5.34 11.91-11.91 0-3.18-1.24-6.16-3.46-8.41ZM12.07 21.8a9.86 9.86 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.38a9.84 9.84 0 1 1 8.37 4.64Zm5.4-7.37c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.18.2-.36.23-.66.08-.3-.15-1.28-.47-2.43-1.5a9.13 9.13 0 0 1-1.68-2.1c-.18-.3-.02-.46.13-.6.14-.14.3-.36.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.03-.53-.08-.15-.68-1.63-.93-2.23-.24-.58-.49-.5-.68-.5h-.58c-.2 0-.53.08-.8.38-.28.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.56.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.08-.13-.28-.2-.58-.35Z"/></svg></a>
</body>
</html>
