<?php get_header(); ?>

<?php
$phone1   = esc_html(lc_get_option('lc_phone1', '07 00 72 71 65'));
$phone2   = esc_html(lc_get_option('lc_phone2', '06 53 64 83 82'));
$email    = sanitize_email(lc_get_option('lc_email', 'ezzine.surgar@gmail.com'));
$whatsapp = esc_attr(lc_get_option('lc_whatsapp', '212700727165'));
$address1 = esc_html(lc_get_option('lc_address_1', 'Mg Rdc Imm A, Résidence Amira'));
$address2 = esc_html(lc_get_option('lc_address_2', 'Avenue 4ème DMM, Camp El Ghoul'));
$city     = esc_html(lc_get_option('lc_city', 'Marrakech'));
$maps     = esc_url(lc_get_option('lc_maps_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3396.5!2d-8.0135!3d31.6305!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xdafee8d985a5bef%3A0x1c3e4a3b8e9f1d2a!2sR%C3%A9sidence%20Amira%2C%20Avenue%204%C3%A8me%20DMM%2C%20Camp%20El%20Ghoul%2C%20Marrakech!5e0!3m2!1sfr!2sma!4v1700000000000'));

$hero_tag   = esc_html(lc_get_option('lc_hero_tag', 'Copropriété & Immobilier — Marrakech'));
$hero_title = wp_kses_post(lc_get_option('lc_hero_title', 'Votre Patrimoine,<br><em>Notre</em><br><span class="stroke">Expertise</span>'));
$hero_desc  = esc_html(lc_get_option('lc_hero_desc', 'Gestion de copropriété, location, achat et vente de biens immobiliers. Un accompagnement professionnel et transparent à Marrakech.'));
$hero_btn1  = esc_html(lc_get_option('lc_hero_btn1', 'Voir Nos Biens'));
$hero_btn2  = esc_html(lc_get_option('lc_hero_btn2', 'Nos Services'));

$srv = [];
for ($i = 1; $i <= 4; $i++) {
    $defaults = [
        1 => ['Gestion de Copropriété', 'Gestion administrative, technique et financière des copropriétés. Suivi des charges, budgets et assemblées générales.'],
        2 => ['Location', 'Mise en location, sélection des locataires, suivi des contrats et gestion quotidienne de vos biens locatifs.'],
        3 => ['Achat & Vente', 'Accompagnement personnalisé pour l\'achat et la vente. Valorisation, promotion et assistance jusqu\'à la finalisation.'],
        4 => ['Travaux & Maintenance', 'Entretien des équipements communs, suivi des prestataires et travaux techniques divers pour vos résidences.'],
    ];
    $srv[$i] = [
        'title' => esc_html(lc_get_option("lc_srv{$i}_title", $defaults[$i][0])),
        'desc'  => esc_html(lc_get_option("lc_srv{$i}_desc", $defaults[$i][1])),
    ];
}

$prop_data = lc_get_properties();
$properties = $prop_data['items'];
$is_fallback = $prop_data['source'] === 'fallback';
?>

<!-- HERO -->
<section class="hero" id="accueil">
  <div class="hero-video">
    <video autoplay muted loop playsinline preload="auto" poster="">
      <source src="<?php echo esc_url(get_template_directory_uri() . '/assets/video/hero-web.mp4'); ?>" type="video/mp4">
    </video>
  </div>
  <div class="hero-parallax-bg"></div>
  <div class="hero-grid"></div>
  <div class="hero-glow hero-glow-1"></div>
  <div class="hero-glow hero-glow-2"></div>

  <div class="hero-content">
    <div class="hero-tag"><span class="pulse"></span> <?php echo $hero_tag; ?></div>
    <h1><?php echo $hero_title; ?></h1>
    <p class="hero-desc"><?php echo $hero_desc; ?></p>
    <div class="hero-actions">
      <a href="#biens" class="btn-gold"><?php echo $hero_btn1; ?></a>
      <a href="#services" class="btn-ghost"><?php echo $hero_btn2; ?></a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat">
        <span class="hero-stat-num">150+</span>
        <span class="hero-stat-lbl"><?php esc_html_e('Biens Gérés', 'luxurycopro'); ?></span>
      </div>
      <div class="hero-stat">
        <span class="hero-stat-num">98%</span>
        <span class="hero-stat-lbl"><?php esc_html_e('Clients Satisfaits', 'luxurycopro'); ?></span>
      </div>
      <div class="hero-stat">
        <span class="hero-stat-num">10+</span>
        <span class="hero-stat-lbl"><?php esc_html_e('Ans d\'Expérience', 'luxurycopro'); ?></span>
      </div>
    </div>
  </div>

  <div class="hero-showcase">
    <?php
    $hero_pool = $properties;
    shuffle($hero_pool);
    $hero_cards = array_slice($hero_pool, 0, 3);
    foreach ($hero_cards as $ci => $hc) :
      $fc_class = 'fc-' . ($ci + 1);
      $thumb = !empty($hc['has_thumb']) && !empty($hc['thumb_url']) ? $hc['thumb_url'] : '';
    ?>
    <div class="float-card <?php echo $fc_class; ?>">
      <?php if ($thumb) : ?>
        <div class="fc-img" style="background-image:url('<?php echo esc_url($thumb); ?>')"></div>
      <?php else : ?>
        <div class="fc-img fc-img-placeholder">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" opacity=".3"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
      <?php endif; ?>
      <div class="fc-body">
        <div class="fc-price"><?php echo esc_html($hc['price']); ?></div>
        <div class="fc-loc"><?php echo esc_html($hc['badge']); ?> · <?php echo esc_html($hc['location']); ?></div>
      </div>
      <div class="fc-shine"></div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="hero-scroll">
    <span>Scroll</span>
    <div class="scroll-bar"></div>
  </div>
</section>

<?php if (lc_get_option('lc_about_visible', true)) : ?>
<!-- ABOUT INTRO -->
<section class="about-intro" id="presentation">
  <div class="sec-label rv"><?php echo esc_html(lc_get_option('lc_about_label', 'Qui Sommes-Nous')); ?></div>
  <h2 class="sec-title rv rv-d1"><?php echo wp_kses_post(lc_get_option('lc_about_title', 'Notre <span style="color:var(--gold)">Société</span>')); ?></h2>
  <div class="about-inner">
    <p class="rv rv-d2"><?php echo wp_kses_post(lc_get_option('lc_about_p1', 'Notre société est une entreprise à responsabilité limitée, expérimentée dans la gestion de copropriété ainsi que dans la gestion et la valorisation des biens immobiliers. Forte d\'une approche professionnelle et rigoureuse, elle accompagne les copropriétaires dans l\'administration, la location, l\'achat et la vente de leurs biens immobiliers.')); ?></p>
    <p class="rv rv-d3"><?php echo wp_kses_post(lc_get_option('lc_about_p2', 'Grâce à une organisation fondée sur la transparence, la proximité et la qualité de service, nous veillons à assurer une gestion efficace des résidences et à répondre aux attentes de notre clientèle dans le respect des dispositions réglementaires en vigueur.')); ?></p>
  </div>
</section>
<?php endif; ?>

<?php if (lc_get_option('lc_refs_visible', true)) :
  $refs = lc_get_references();
  if (!empty($refs)) : ?>
<!-- REFERENCES -->
<section class="refs" id="references">
  <div class="sec-label rv"><?php echo esc_html(lc_get_option('lc_refs_label', 'Références')); ?></div>
  <h2 class="sec-title rv rv-d1"><?php echo wp_kses_post(lc_get_option('lc_refs_title', 'Ils nous font <span style="color:var(--gold)">confiance</span>')); ?></h2>
  <p class="sec-sub rv rv-d2"><?php echo wp_kses_post(lc_get_option('lc_refs_intro', 'Nous accompagnons différentes résidences et clients dans la gestion, la valorisation et le suivi de leurs biens immobiliers.')); ?></p>
  <div class="refs-grid">
    <?php foreach ($refs as $ri => $ref) :
      $delay = $ri > 0 ? ' rv-d' . min($ri, 4) : '';
    ?>
    <div class="ref-card rv<?php echo esc_attr($delay); ?>">
      <div class="ref-visual">
        <?php if ($ref['has_thumb'] && $ref['thumb_url']) : ?>
          <img src="<?php echo esc_url($ref['thumb_url']); ?>" alt="<?php echo esc_attr($ref['name']); ?>">
        <?php else : ?>
          <div class="ref-placeholder"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" opacity=".25"><path d="M3 21h18M3 7v14m6-14v14m6-14v14m6-14v14M3 7l9-4 9 4"/></svg></div>
        <?php endif; ?>
      </div>
      <div class="ref-body">
        <h3 class="ref-name"><?php echo esc_html($ref['name']); ?></h3>
        <span class="ref-service"><?php echo esc_html($ref['service']); ?></span>
        <?php if ($ref['location']) : ?>
          <span class="ref-loc"><svg width="12" height="12" viewBox="0 0 24 24" fill="var(--gold)" stroke="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3" fill="var(--surface)"/></svg> <?php echo esc_html($ref['location']); ?></span>
        <?php endif; ?>
        <?php if ($ref['desc']) : ?>
          <p class="ref-desc"><?php echo esc_html($ref['desc']); ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; endif; ?>

<?php if (lc_get_option('lc_biens_visible', true)) : ?>
<!-- PROPERTIES -->
<section class="properties" id="biens">
  <div class="prop-header">
    <div>
      <div class="sec-label rv"><?php echo esc_html(lc_get_option('lc_biens_label', 'Notre Portefeuille')); ?></div>
      <h2 class="sec-title rv rv-d1"><?php echo $is_fallback ? wp_kses_post(lc_get_option('lc_biens_title_fallback', 'Exemples de Biens<br><span style="color:var(--gold)">Disponibles</span>')) : wp_kses_post(lc_get_option('lc_biens_title', 'Nos Biens<br><span style="color:var(--gold)">Disponibles</span>')); ?></h2>
      <p class="sec-sub rv rv-d1" style="margin-bottom:1rem"><?php echo $is_fallback ? wp_kses_post(lc_get_option('lc_biens_desc_fallback', 'Les biens présentés ci-dessous sont des exemples illustratifs. Pour consulter nos offres réelles et actualisées, veuillez nous contacter directement.')) : wp_kses_post(lc_get_option('lc_biens_desc', 'Découvrez notre sélection de biens immobiliers à Marrakech. Contactez-nous pour plus d\'informations.')); ?></p>
    </div>
    <div class="prop-filters rv rv-d2">
      <button class="active" data-filter="all"><?php esc_html_e('Tout', 'luxurycopro'); ?></button>
      <button data-filter="Vente"><?php esc_html_e('Vente', 'luxurycopro'); ?></button>
      <button data-filter="Location"><?php esc_html_e('Location', 'luxurycopro'); ?></button>
      <button data-filter="Exclusif"><?php esc_html_e('Exclusif', 'luxurycopro'); ?></button>
    </div>
  </div>
  <div class="prop-grid">
    <?php foreach ($properties as $idx => $p) :
      $delay_class = $idx > 0 ? ' rv-d' . $idx : '';
    ?>
    <div class="prop-card tilt rv<?php echo esc_attr($delay_class); ?>" data-type="<?php echo esc_attr($p['badge']); ?>">
      <?php if (!empty($p['has_thumb']) && !empty($p['thumb_url'])) : ?>
        <div class="p-img" style="background:url('<?php echo esc_url($p['thumb_url']); ?>') center/cover">
          <span class="p-badge <?php echo esc_attr($p['badge_class']); ?>"><?php echo esc_html($p['badge']); ?></span>
        </div>
      <?php else : ?>
        <div class="p-img">
          <span class="p-icon"><?php echo esc_html($p['icon']); ?></span>
          <span class="p-badge <?php echo esc_attr($p['badge_class']); ?>"><?php echo esc_html($p['badge']); ?></span>
        </div>
      <?php endif; ?>
      <div class="p-body">
        <div class="p-price"><?php echo esc_html($p['price']); ?></div>
        <div class="p-title"><?php echo esc_html($p['title']); ?></div>
        <div class="p-loc"><svg width="12" height="12" viewBox="0 0 24 24" fill="var(--gold)" stroke="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3" fill="var(--surface)"/></svg> <?php echo esc_html($p['location']); ?></div>
        <div class="p-specs">
          <div class="p-spec"><span class="val"><?php echo esc_html($p['bedrooms']); ?></span><span class="lbl"><?php esc_html_e('Chambres', 'luxurycopro'); ?></span></div>
          <div class="p-spec"><span class="val"><?php echo esc_html($p['bathrooms']); ?></span><span class="lbl">SDB</span></div>
          <div class="p-spec"><span class="val"><?php echo esc_html($p['area']); ?></span><span class="lbl">m²</span></div>
          <div class="p-spec"><span class="val"><?php echo esc_html($p['land']); ?></span><span class="lbl"><?php echo esc_html($p['land_label']); ?></span></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- SERVICES -->
<section class="services-row" id="services">
  <div class="sec-label rv" style="padding:0"><?php esc_html_e('Nos Services', 'luxurycopro'); ?></div>
  <h2 class="sec-title rv rv-d1"><?php esc_html_e('Un Accompagnement', 'luxurycopro'); ?><br><span style="color:var(--gold)"><?php esc_html_e('Complet', 'luxurycopro'); ?></span></h2>
  <div class="srv-grid">
    <?php
    $srv_icons = [
        1 => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M3 7v14m6-14v14m6-14v14m6-14v14M3 7l9-4 9 4M6 11h.01M6 15h.01M12 11h.01M12 15h.01M18 11h.01M18 15h.01"/></svg>',
        2 => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>',
        3 => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        4 => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    ];
    for ($i = 1; $i <= 4; $i++) :
      $delay = $i > 1 ? ' rv-d' . ($i - 1) : '';
    ?>
    <div class="srv-item tilt rv<?php echo esc_attr($delay); ?>">
      <div class="srv-num">0<?php echo $i; ?></div>
      <div class="srv-icon"><?php echo $srv_icons[$i]; ?></div>
      <h3><?php echo $srv[$i]['title']; ?></h3>
      <p><?php echo $srv[$i]['desc']; ?></p>
    </div>
    <?php endfor; ?>
  </div>
</section>

<!-- SERVICES DETAIL -->
<div class="srv-detail">
  <div class="srv-detail-grid">
    <div class="srv-block rv">
      <h4><?php esc_html_e('Gestion de Copropriété', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Nous assurons une gestion administrative, technique et financière des copropriétaires afin de garantir le bon fonctionnement des parties communes et la préservation du patrimoine immobilier.', 'luxurycopro'); ?></p>
      <ul class="srv-list">
        <li><?php esc_html_e('Le suivi administratif et réglementaire', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('L\'entretien et la maintenance des équipements communs', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Le suivi des prestataires et des contrats', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('La gestion des charges et des budgets', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('L\'accompagnement des copropriétaires', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('L\'organisation et le suivi des assemblées générales', 'luxurycopro'); ?></li>
      </ul>
    </div>
    <div class="srv-block rv rv-d1">
      <h4><?php esc_html_e('Gestion des Biens Immobiliers', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Nous proposons des solutions adaptées à la gestion et la valorisation des biens immobiliers destinés à l\'habitation ou à l\'investissement.', 'luxurycopro'); ?></p>
      <ul class="srv-list">
        <li><?php esc_html_e('Valorisation et promotion des biens', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Assistance dans les démarches administratives et commerciales', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Conseil et suivi, jusqu\'à la finalisation des opérations', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Accompagnement personnalisé pour l\'achat et la vente de biens immobiliers', 'luxurycopro'); ?></li>
      </ul>
    </div>
    <div class="srv-block rv rv-d2">
      <h4><?php esc_html_e('Location', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Un service complet pour la mise en location et la gestion quotidienne de vos biens locatifs.', 'luxurycopro'); ?></p>
      <ul class="srv-list">
        <li><?php esc_html_e('Mise en location des biens', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Recherche et sélection des locataires', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Suivi administratif des contrats', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Gestion quotidienne des locataires, vente et achat', 'luxurycopro'); ?></li>
      </ul>
    </div>
    <div class="srv-block rv rv-d3">
      <h4><?php esc_html_e('Achat & Vente', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Un accompagnement de A à Z pour concrétiser votre projet immobilier en toute sérénité.', 'luxurycopro'); ?></p>
      <ul class="srv-list">
        <li><?php esc_html_e('Accompagnement personnalisé pour l\'achat et la vente de biens immobiliers', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Valorisation et promotion des biens', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Assistance dans les démarches administratives et commerciales', 'luxurycopro'); ?></li>
        <li><?php esc_html_e('Conseil et suivi, jusqu\'à la finalisation des opérations', 'luxurycopro'); ?></li>
      </ul>
    </div>
  </div>
</div>

<!-- MARQUEE -->
<div class="marquee" id="marqueeSection">
  <div class="marquee-bg-text">LUXURY COPRO</div>
  <div class="marquee-track">
    <span class="mq-item"><?php esc_html_e('Copropriété', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Location', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Achat & Vente', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Gestion Immobilière', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Travaux', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php echo $city; ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Copropriété', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Location', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Achat & Vente', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Gestion Immobilière', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php esc_html_e('Travaux', 'luxurycopro'); ?></span><span class="mq-sep">&loz;</span>
    <span class="mq-item"><?php echo $city; ?></span><span class="mq-sep">&loz;</span>
  </div>
</div>

<!-- ENGAGEMENTS -->
<section class="why" id="apropos">
  <div class="sec-label rv"><?php esc_html_e('Nos Engagements', 'luxurycopro'); ?></div>
  <h2 class="sec-title rv rv-d1"><?php esc_html_e('Votre Partenaire', 'luxurycopro'); ?><br><?php esc_html_e('de', 'luxurycopro'); ?> <span style="color:var(--gold)"><?php esc_html_e('Confiance', 'luxurycopro'); ?></span></h2>

  <div class="engage-grid">
    <div class="engage-card rv">
      <div class="e-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
      <h4><?php esc_html_e('Transparence', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Professionnalisme et clarté dans toutes nos démarches et nos rapports.', 'luxurycopro'); ?></p>
    </div>
    <div class="engage-card rv rv-d1">
      <div class="e-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>
      <h4><?php esc_html_e('Qualité de Service', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Des prestations rigoureuses et adaptées aux plus hautes exigences.', 'luxurycopro'); ?></p>
    </div>
    <div class="engage-card rv rv-d2">
      <div class="e-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
      <h4><?php esc_html_e('Réactivité & Proximité', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Une écoute attentive et des réponses rapides à chacune de vos demandes.', 'luxurycopro'); ?></p>
    </div>
    <div class="engage-card rv rv-d3">
      <div class="e-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
      <h4><?php esc_html_e('Respect des Délais', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Respect des engagements pris et des échéances convenues avec nos clients.', 'luxurycopro'); ?></p>
    </div>
    <div class="engage-card rv rv-d4">
      <div class="e-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
      <h4><?php esc_html_e('Patrimoine Préservé', 'luxurycopro'); ?></h4>
      <p><?php esc_html_e('Préservation et valorisation durable de votre patrimoine immobilier.', 'luxurycopro'); ?></p>
    </div>
  </div>

  <div class="ambition-box rv rv-d2">
    <h3><?php esc_html_e('Notre Ambition', 'luxurycopro'); ?></h3>
    <p><?php esc_html_e('Développer une relation de confiance durable avec nos clients en proposant des services modernes, fiables et adaptés aux exigences du secteur immobilier et de la gestion de copropriété.', 'luxurycopro'); ?></p>
  </div>
</section>

<!-- STATS -->
<div class="stats">
  <div class="stats-grid">
    <div class="st rv"><div class="st-num" style="font-size:2.2rem">SARL</div><div class="st-label"><?php esc_html_e('Société enregistrée', 'luxurycopro'); ?></div></div>
    <div class="st rv rv-d1"><div class="st-num" style="font-size:2.2rem"><?php echo $city; ?></div><div class="st-label"><?php esc_html_e('Basés à', 'luxurycopro'); ?> <?php echo $city; ?></div></div>
    <div class="st rv rv-d2"><div class="st-num" style="font-size:2.2rem">5+</div><div class="st-label"><?php esc_html_e('Domaines d\'activité', 'luxurycopro'); ?></div></div>
    <div class="st rv rv-d3"><div class="st-num" style="font-size:1.6rem"><?php esc_html_e('Conformité', 'luxurycopro'); ?></div><div class="st-label"><?php esc_html_e('Respect du cadre réglementaire', 'luxurycopro'); ?></div></div>
  </div>
</div>

<!-- CTA -->
<section class="cta">
  <h2 class="rv"><?php esc_html_e('Vous Avez un Projet', 'luxurycopro'); ?><br><em><?php esc_html_e('Immobilier ?', 'luxurycopro'); ?></em></h2>
  <p class="rv rv-d1"><?php esc_html_e('Copropriété, location, achat ou vente — contactez-nous pour un accompagnement sur mesure.', 'luxurycopro'); ?></p>
  <div class="cta-btns rv rv-d2">
    <a href="<?php echo esc_url('https://wa.me/' . $whatsapp . '?text=' . rawurlencode('Bonjour Luxury Copro, je souhaite des informations sur vos services.')); ?>" target="_blank" class="btn-wa">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
      WhatsApp
    </a>
    <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone1)); ?>" class="btn-gold"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg><?php esc_html_e('Appeler', 'luxurycopro'); ?></a>
  </div>
</section>

<!-- CONTACT -->
<section class="contact" id="contact">
  <div class="sec-label rv"><?php esc_html_e('Contact', 'luxurycopro'); ?></div>
  <h2 class="sec-title rv rv-d1"><?php esc_html_e('Parlons de Votre', 'luxurycopro'); ?><br><span style="color:var(--gold)"><?php esc_html_e('Projet', 'luxurycopro'); ?></span></h2>

  <div class="ct-location-card rv rv-d2">
    <div class="ct-loc-info">
      <span class="ct-badge"><?php esc_html_e('Notre Bureau', 'luxurycopro'); ?></span>
      <h3 class="ct-loc-title">Luxury Copro — <?php echo $city; ?></h3>
      <div class="ct-loc-items">
        <div class="ct-loc-item">
          <div class="ct-loc-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
          <div><h4><?php esc_html_e('Adresse', 'luxurycopro'); ?></h4><p><?php echo $address1; ?><br><?php echo $address2; ?>, <?php echo $city; ?></p></div>
        </div>
        <div class="ct-loc-item">
          <div class="ct-loc-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
          <div><h4><?php esc_html_e('Téléphone', 'luxurycopro'); ?></h4><p><a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone1)); ?>"><?php echo $phone1; ?></a><br><a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone2)); ?>"><?php echo $phone2; ?></a></p></div>
        </div>
        <div class="ct-loc-item">
          <div class="ct-loc-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
          <div><h4><?php esc_html_e('Horaires', 'luxurycopro'); ?></h4><p><?php esc_html_e('Lundi - Samedi : 9h00 - 19h00', 'luxurycopro'); ?></p></div>
        </div>
      </div>
      <div class="ct-loc-actions">
        <a href="<?php echo esc_url('https://wa.me/' . $whatsapp); ?>" target="_blank" class="btn-wa" style="border:none;font-family:'Inter',sans-serif;text-decoration:none;display:inline-flex;align-items:center;gap:8px">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          <?php esc_html_e('Contacter via WhatsApp', 'luxurycopro'); ?>
        </a>
        <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone1)); ?>" class="btn-gold" style="text-decoration:none;display:inline-flex;align-items:center;gap:8px">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <?php esc_html_e('Appelez-nous', 'luxurycopro'); ?>
        </a>
      </div>
    </div>
    <?php if ($maps) : ?>
    <div class="ct-loc-map">
      <a href="https://www.google.com/maps/search/R%C3%A9sidence+Amira+Avenue+4%C3%A8me+DMM+Camp+El+Ghoul+Marrakech" target="_blank" rel="noopener" class="c-map-link">Open in Maps <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a>
      <iframe src="<?php echo esc_url($maps); ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <?php endif; ?>
  </div>

  <div class="ct-form-card rv rv-d3">
    <div class="ct-form-header">
      <h3><?php esc_html_e('Envoyez-nous un message', 'luxurycopro'); ?></h3>
      <p><?php esc_html_e('Décrivez votre projet et nous vous répondrons dans les plus brefs délais.', 'luxurycopro'); ?></p>
    </div>
    <form class="c-form" id="contactForm" aria-label="<?php esc_attr_e('Formulaire de contact', 'luxurycopro'); ?>">
      <div class="c-row">
        <div class="c-group"><label><?php esc_html_e('Nom Complet', 'luxurycopro'); ?></label><input type="text" id="cfName" placeholder="<?php esc_attr_e('Votre nom', 'luxurycopro'); ?>" required></div>
        <div class="c-group"><label><?php esc_html_e('Téléphone', 'luxurycopro'); ?></label><input type="tel" id="cfPhone" placeholder="06 00 00 00 00" required></div>
      </div>
      <div class="c-row">
        <div class="c-group">
          <label><?php esc_html_e('Type de Projet', 'luxurycopro'); ?></label>
          <select id="cfType">
            <option><?php esc_html_e('Gestion de copropriété', 'luxurycopro'); ?></option>
            <option><?php esc_html_e('Je veux acheter', 'luxurycopro'); ?></option>
            <option><?php esc_html_e('Je veux vendre', 'luxurycopro'); ?></option>
            <option><?php esc_html_e('Je veux louer', 'luxurycopro'); ?></option>
            <option><?php esc_html_e('Travaux & maintenance', 'luxurycopro'); ?></option>
          </select>
        </div>
        <div class="c-group">
          <label><?php esc_html_e('Budget', 'luxurycopro'); ?></label>
          <select id="cfBudget">
            <option><?php esc_html_e('Moins de 500 000 MAD', 'luxurycopro'); ?></option>
            <option>500 000 — 1 000 000 MAD</option>
            <option>1 000 000 — 3 000 000 MAD</option>
            <option><?php esc_html_e('Plus de 3 000 000 MAD', 'luxurycopro'); ?></option>
          </select>
        </div>
      </div>
      <div class="c-group"><label><?php esc_html_e('Message', 'luxurycopro'); ?></label><textarea id="cfMsg" placeholder="<?php esc_attr_e('Décrivez votre projet immobilier...', 'luxurycopro'); ?>"></textarea></div>
      <div class="c-form-actions">
        <button type="submit" class="btn-wa" style="border:none;font-family:'Inter',sans-serif">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          <?php esc_html_e('Envoyer via WhatsApp', 'luxurycopro'); ?>
        </button>
        <button type="button" class="btn-email" id="cfEmailBtn"><?php esc_html_e('Envoyer par E-mail', 'luxurycopro'); ?></button>
      </div>
      <p class="c-form-note"><?php esc_html_e('Votre message sera envoyé directement sur notre WhatsApp ou par e-mail.', 'luxurycopro'); ?></p>
    </form>
  </div>
</section>

<!-- PROPERTY MODAL -->
<div class="modal-overlay" id="modalOverlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Détails du bien', 'luxurycopro'); ?>">
  <div class="modal">
    <button class="modal-close" id="modalClose" aria-label="<?php esc_attr_e('Fermer', 'luxurycopro'); ?>">&times;</button>
    <div class="modal-hero">
      <span class="m-icon" id="modalIcon">V</span>
      <span class="m-badge badge-exclu" id="modalBadge">Exclusif</span>
    </div>
    <div class="modal-body">
      <div class="modal-top">
        <div>
          <div class="m-price" id="modalPrice"></div>
          <div class="m-title" id="modalTitle"></div>
          <div class="m-loc" id="modalLoc"></div>
        </div>
        <div style="text-align:right">
          <div style="font-size:.65rem;letter-spacing:2px;text-transform:uppercase;color:var(--muted)"><?php esc_html_e('Référence', 'luxurycopro'); ?></div>
          <div style="font-weight:700;color:var(--gold)" id="modalRef"></div>
        </div>
      </div>
      <div class="modal-specs" id="modalSpecs"></div>
      <div class="modal-desc">
        <h3><?php esc_html_e('Description', 'luxurycopro'); ?></h3>
        <p id="modalDesc"></p>
      </div>
      <div class="modal-features" id="modalFeatures"></div>
      <div class="modal-actions">
        <a href="#" target="_blank" class="btn-wa" id="modalWaBtn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          <?php esc_html_e('Contacter via WhatsApp', 'luxurycopro'); ?>
        </a>
        <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone1)); ?>" class="btn-gold"><?php esc_html_e('Planifier une Visite', 'luxurycopro'); ?></a>
        <button class="btn-ghost" style="font-family:'Inter',sans-serif" onclick="document.getElementById('modalOverlay').classList.remove('open')"><?php esc_html_e('Fermer', 'luxurycopro'); ?></button>
      </div>
    </div>
  </div>
</div>

<?php
$prop_json = [];
foreach ($properties as $p) {
    $prop_json[] = [
        'icon'       => $p['icon'],
        'badge'      => $p['badge'],
        'badgeClass' => $p['badge_class'],
        'price'      => $p['price'],
        'title'      => $p['title'],
        'loc'        => $p['location'],
        'ref'        => $p['ref'] ?? '',
        'specs'      => [$p['bedrooms'], __('Chambres', 'luxurycopro'), $p['bathrooms'], __('Salles de Bain', 'luxurycopro'), $p['area'] . ' m²', __('Surface', 'luxurycopro'), $p['land'], $p['land_label']],
        'desc'       => wp_strip_all_tags($p['desc'] ?? ''),
        'features'   => $p['features'] ?? [],
    ];
}
?>
<script>var lcProperties = <?php echo wp_json_encode($prop_json); ?>;</script>

<?php get_footer(); ?>
