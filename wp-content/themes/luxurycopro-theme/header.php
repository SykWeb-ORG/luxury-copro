<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php echo esc_attr(get_bloginfo('description')); ?>">
  <meta property="og:title" content="<?php echo esc_attr(get_bloginfo('name')); ?> — Gestion de Copropriété & Immobilier à Marrakech">
  <meta property="og:description" content="Gestion de copropriété, location, achat et vente de biens immobiliers à Marrakech. Accompagnement professionnel et transparent.">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="fr_MA">
  <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
  <meta property="og:image" content="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logo.png'); ?>">
  <meta property="og:image:width" content="512">
  <meta property="og:image:height" content="512">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
  <meta name="twitter:description" content="<?php echo esc_attr(get_bloginfo('description')); ?>">
  <meta name="twitter:image" content="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logo.png'); ?>">
  <link rel="canonical" href="<?php echo esc_url(function_exists('wp_get_canonical_url') ? (wp_get_canonical_url() ?: get_permalink()) : get_permalink()); ?>">
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/logo.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/logo.png">
  <link rel="manifest" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/site.webmanifest">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a href="#accueil" class="skip-link">Aller au contenu</a>
<script>if(localStorage.getItem('ez-theme')==='light')document.body.classList.add('light')</script>

<!-- LOADER -->
<div class="loader" id="loader">
  <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logo.png'); ?>" alt="Luxury Copro" class="loader-img">
  <div class="loader-logo" id="loaderLogo"></div>
  <div class="loader-line"></div>
</div>

<!-- SCROLL PROGRESS -->
<div class="scroll-progress" id="scrollProgress"></div>

<!-- CURSOR -->
<div class="cur-dot" id="cDot"></div>
<div class="cur-ring" id="cRing"></div>

<!-- NAV -->
<nav id="nav">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
    <?php if (has_custom_logo()) :
      $logo_id  = get_theme_mod('custom_logo');
      $logo_url = wp_get_attachment_image_url($logo_id, 'full');
    ?>
      <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="140" height="50">
    <?php else : ?>
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/logo.png" alt="Luxury Copro" width="140" height="50">
    <?php endif; ?>
  </a>
  <ul class="nav-menu">
    <li><a href="#presentation"><?php esc_html_e('À Propos', 'luxurycopro'); ?></a></li>
    <?php if (lc_get_option('lc_biens_visible', false)) : ?>
    <li><a href="#biens"><?php esc_html_e('Nos Biens', 'luxurycopro'); ?></a></li>
    <?php endif; ?>
    <li><a href="#services"><?php esc_html_e('Services', 'luxurycopro'); ?></a></li>
    <li><a href="#apropos"><?php esc_html_e('Engagements', 'luxurycopro'); ?></a></li>
    <li><a href="#contact" class="nav-btn"><?php esc_html_e('Nous Contacter', 'luxurycopro'); ?></a></li>
  </ul>
  <div style="display:flex;align-items:center;gap:.8rem">
    <div class="theme-toggle" id="themeToggle" title="<?php esc_attr_e('Changer le thème', 'luxurycopro'); ?>" role="button" aria-label="<?php esc_attr_e('Basculer entre mode clair et sombre', 'luxurycopro'); ?>" tabindex="0">
      <span class="t-icon t-moon">☽</span>
      <span class="t-icon t-sun">☼</span>
    </div>
    <div class="hamburger" id="hamburger" role="button" aria-label="<?php esc_attr_e('Ouvrir le menu', 'luxurycopro'); ?>" tabindex="0">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>
<script>document.documentElement.style.setProperty('--nav-h',document.getElementById('nav').offsetHeight+'px')</script>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
  <button class="mm-close" id="mmClose" aria-label="<?php esc_attr_e('Fermer le menu', 'luxurycopro'); ?>"></button>
  <a href="#accueil" class="mm-link"><?php esc_html_e('Accueil', 'luxurycopro'); ?></a>
  <a href="#presentation" class="mm-link"><?php esc_html_e('À Propos', 'luxurycopro'); ?></a>
  <?php if (lc_get_option('lc_biens_visible', false)) : ?>
  <a href="#biens" class="mm-link"><?php esc_html_e('Nos Biens', 'luxurycopro'); ?></a>
  <?php endif; ?>
  <a href="#services" class="mm-link"><?php esc_html_e('Services', 'luxurycopro'); ?></a>
  <a href="#apropos" class="mm-link"><?php esc_html_e('Engagements', 'luxurycopro'); ?></a>
  <a href="#contact" class="mm-link mm-cta"><?php esc_html_e('Nous Contacter', 'luxurycopro'); ?></a>
</div>
