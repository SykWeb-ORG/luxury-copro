<?php
$phone1    = esc_html(lc_get_option('lc_phone1', '07 00 72 71 65'));
$phone2    = esc_html(lc_get_option('lc_phone2', '06 53 64 83 82'));
$email     = sanitize_email(lc_get_option('lc_email', 'ezzine.surgar@gmail.com'));
$address1  = esc_html(lc_get_option('lc_address_1', 'Mg Rdc Imm A, Résidence Amira'));
$address2  = esc_html(lc_get_option('lc_address_2', 'Avenue 4ème DMM, Camp El Ghoul'));
$city      = esc_html(lc_get_option('lc_city', 'Marrakech'));
$whatsapp  = esc_attr(lc_get_option('lc_whatsapp', '212700727165'));
$rc   = esc_html(lc_get_option('lc_rc', '138059'));
$tp   = esc_html(lc_get_option('lc_tp', '64261180'));
$if   = esc_html(lc_get_option('lc_if', '53830046'));
$cnss = esc_html(lc_get_option('lc_cnss', '4910963'));
$ice  = esc_html(lc_get_option('lc_ice', '003295207000042'));

$logo_url = get_template_directory_uri() . '/assets/img/logo.png';
if (has_custom_logo()) {
    $logo_id  = get_theme_mod('custom_logo');
    $logo_url = wp_get_attachment_image_url($logo_id, 'full');
}
?>

<!-- WHATSAPP WIDGET -->
<div class="wa-widget" id="waWidget">
  <div class="wa-popup">
    <div class="wa-popup-header">
      <div class="wa-avatar">LC</div>
      <div>
        <div class="wa-name"><?php echo esc_html(strtoupper(get_bloginfo('name'))); ?></div>
        <div class="wa-status"><?php esc_html_e('En ligne', 'luxurycopro'); ?></div>
      </div>
    </div>
    <div class="wa-popup-body">
      <div class="wa-bubble">
        <?php esc_html_e('Bonjour ! Bienvenue chez LUXURY COPRO. Comment puis-je vous aider dans votre projet immobilier ou de copropriété ?', 'luxurycopro'); ?>
        <span class="wa-time"><?php esc_html_e('Maintenant', 'luxurycopro'); ?></span>
      </div>
    </div>
    <div class="wa-popup-footer">
      <input class="wa-input" type="text" placeholder="<?php esc_attr_e('Tapez un message...', 'luxurycopro'); ?>" id="waInput">
      <button class="wa-send" id="waSend">
        <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
      </button>
    </div>
  </div>
  <button class="wa-fab" id="waFab">
    <span class="wa-ping"></span>
    <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
  </button>
</div>

<!-- FOOTER -->
<footer>
  <div class="foot-top">
    <div class="foot-brand">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="foot-logo">
        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
        <span><?php echo esc_html(strtoupper(get_bloginfo('name'))); ?></span>
      </a>
      <p><?php esc_html_e('Gestion de Copropriété — Travaux Techniques Divers — Intérim — Immobilier. Votre partenaire de confiance à', 'luxurycopro'); ?> <?php echo $city; ?>.</p>
    </div>
    <div class="foot-col">
      <h4><?php esc_html_e('Navigation', 'luxurycopro'); ?></h4>
      <ul class="foot-links">
        <li><a href="#accueil"><?php esc_html_e('Accueil', 'luxurycopro'); ?></a></li>
        <li><a href="#presentation"><?php esc_html_e('À Propos', 'luxurycopro'); ?></a></li>
        <li><a href="#biens"><?php esc_html_e('Nos Biens', 'luxurycopro'); ?></a></li>
        <li><a href="#services"><?php esc_html_e('Services', 'luxurycopro'); ?></a></li>
        <li><a href="#apropos"><?php esc_html_e('Engagements', 'luxurycopro'); ?></a></li>
        <li><a href="#contact"><?php esc_html_e('Contact', 'luxurycopro'); ?></a></li>
      </ul>
    </div>
    <div class="foot-col">
      <h4><?php esc_html_e('Contact', 'luxurycopro'); ?></h4>
      <ul class="foot-contact-list">
        <li><?php echo $address1; ?></li>
        <li><?php echo $address2; ?></li>
        <li><?php echo $city; ?></li>
        <li style="margin-top:.4rem"><a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone1)); ?>"><?php echo $phone1; ?></a></li>
        <li><a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone2)); ?>"><?php echo $phone2; ?></a></li>
        <li style="margin-top:.4rem"><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></li>
      </ul>
    </div>
  </div>
  <div class="foot-bottom">
    <p>&copy; <?php echo esc_html(date('Y')); ?> <?php echo esc_html(strtoupper(get_bloginfo('name'))); ?> — EZZINE SURGAR S.A.R.L. <?php esc_html_e('Tous droits réservés.', 'luxurycopro'); ?></p>
    <div class="foot-legal">RC: <?php echo $rc; ?> · TP: <?php echo $tp; ?> · IF: <?php echo $if; ?> · CNSS: <?php echo $cnss; ?> · ICE: <?php echo $ice; ?></div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
