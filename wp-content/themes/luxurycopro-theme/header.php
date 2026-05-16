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
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/logo.png">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<script>if(localStorage.getItem('ez-theme')==='light')document.body.classList.add('light')</script>

<!-- LOADER -->
<div class="loader" id="loader">
  <div class="loader-logo" id="loaderLogo"></div>
  <div class="loader-line"></div>
</div>

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
      <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <?php else : ?>
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/logo.png" alt="Luxury Copro">
    <?php endif; ?>
    <div class="logo-text"><?php echo esc_html(strtoupper(get_bloginfo('name'))); ?><small>Gestion &amp; Immobilier</small></div>
  </a>
  <ul class="nav-menu">
    <li><a href="#presentation"><?php esc_html_e('À Propos', 'luxurycopro'); ?></a></li>
    <li><a href="#biens"><?php esc_html_e('Nos Biens', 'luxurycopro'); ?></a></li>
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

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
  <a href="#accueil" class="mm-link"><?php esc_html_e('Accueil', 'luxurycopro'); ?></a>
  <a href="#presentation" class="mm-link"><?php esc_html_e('À Propos', 'luxurycopro'); ?></a>
  <a href="#biens" class="mm-link"><?php esc_html_e('Nos Biens', 'luxurycopro'); ?></a>
  <a href="#services" class="mm-link"><?php esc_html_e('Services', 'luxurycopro'); ?></a>
  <a href="#apropos" class="mm-link"><?php esc_html_e('Engagements', 'luxurycopro'); ?></a>
  <a href="#contact" class="mm-link mm-cta"><?php esc_html_e('Nous Contacter', 'luxurycopro'); ?></a>
</div>
