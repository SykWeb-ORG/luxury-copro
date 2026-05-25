<?php
defined('ABSPATH') || exit;

const LC_PROD_CF7_SEED_VERSION = '2026-05-25.1';

function lc_prod_cf7_log($message) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log($message);
        return;
    }

    echo $message . PHP_EOL;
}

function lc_prod_cf7_error($message) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::error($message);
    }

    wp_die(esc_html($message));
}

function lc_prod_cf7_env_bool($name, $default) {
    $value = getenv($name);

    if ($value === false || $value === '') {
        return (bool) $default;
    }

    return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
}

function lc_prod_cf7_env_text($name, $default) {
    $value = getenv($name);

    if ($value === false || trim((string) $value) === '') {
        return $default;
    }

    return trim((string) $value);
}

function lc_prod_cf7_template() {
    return trim(<<<'HTML'
<div class="c-row">
  <div class="c-group">
    <label for="cfName">Nom Complet</label>
    [text* your-name id:cfName placeholder "Votre nom"]
  </div>
  <div class="c-group">
    <label for="cfPhone">Téléphone</label>
    [tel* your-phone id:cfPhone placeholder "06 00 00 00 00"]
  </div>
</div>
<div class="c-row">
  <div class="c-group">
    <label for="cfType">Type de Projet</label>
    [select project-type id:cfType "Syndic de copropriété" "Je veux acheter" "Je veux vendre" "Je veux louer" "Travaux & maintenance"]
  </div>
  <div class="c-group">
    <label for="cfBudget">Budget</label>
    [select budget id:cfBudget "Moins de 500 000 MAD" "500 000 — 1 000 000 MAD" "1 000 000 — 3 000 000 MAD" "Plus de 3 000 000 MAD"]
  </div>
</div>
<div class="c-group">
  <label for="cfMsg">Message</label>
  [textarea your-message id:cfMsg placeholder "Décrivez votre projet immobilier..."]
</div>
<div class="c-form-actions">
  <button type="button" class="btn-wa" id="cf7WhatsAppBtn" style="border:none;font-family:'Inter',sans-serif">Envoyer via WhatsApp</button>
  [submit class:btn-email "Envoyer par E-mail"]
</div>
<p class="c-form-note">Votre message sera envoyé directement sur notre WhatsApp ou par e-mail.</p>
HTML);
}

function lc_prod_cf7_recipient() {
    $recipient = lc_prod_cf7_env_text('LC_CF7_RECIPIENT', '');

    if ($recipient === '') {
        if (function_exists('lc_get_option')) {
            $recipient = (string) lc_get_option('lc_email', '');
        } else {
            $recipient = (string) get_theme_mod('lc_email', '');
        }
    }

    if ($recipient === '') {
        $recipient = (string) get_option('admin_email');
    }

    $recipient = sanitize_email($recipient);

    if (!is_email($recipient)) {
        lc_prod_cf7_error('Unable to seed CF7: LC_CF7_RECIPIENT, lc_email, and admin_email are all invalid.');
    }

    return $recipient;
}

function lc_prod_cf7_find_form($slug, $title) {
    $form = get_page_by_path($slug, OBJECT, 'wpcf7_contact_form');

    if ($form instanceof WP_Post) {
        return $form;
    }

    $forms = get_posts([
        'post_type'      => 'wpcf7_contact_form',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'all',
    ]);

    foreach ($forms as $candidate) {
        if ($candidate instanceof WP_Post && $candidate->post_title === $title) {
            return $candidate;
        }
    }

    return null;
}

function lc_prod_cf7_shortcode($post_id, $title) {
    return sprintf(
        '[contact-form-7 id="%d" title="%s" html_id="lcCf7ContactForm" html_class="c-form lc-cf7-form"]',
        absint($post_id),
        esc_attr($title)
    );
}

function lc_prod_cf7_mail() {
    $mail = WPCF7_ContactFormTemplate::get_default('mail');
    $mail['recipient'] = lc_prod_cf7_recipient();
    $mail['subject'] = lc_prod_cf7_env_text('LC_CF7_SUBJECT', 'Nouveau contact - [project-type] - [your-name]');
    $mail['sender'] = '[_site_title] <' . WPCF7_ContactFormTemplate::from_email() . '>';
    $mail['additional_headers'] = '';
    $mail['body'] = trim(<<<'MAIL'
Nouveau message depuis le site Luxury Copro.

Nom: [your-name]
Téléphone: [your-phone]
Projet: [project-type]
Budget: [budget]

Message:
[your-message]

--
Formulaire envoyé depuis [_site_title] ([_site_url]).
MAIL);

    return $mail;
}

function lc_prod_cf7_messages() {
    $messages = WPCF7_ContactFormTemplate::get_default('messages');
    $messages['mail_sent_ok'] = 'Merci, votre message a bien été envoyé.';
    $messages['mail_sent_ng'] = 'Une erreur est survenue pendant l’envoi. Veuillez réessayer.';
    $messages['validation_error'] = 'Veuillez vérifier les champs indiqués.';
    $messages['invalid_required'] = 'Veuillez renseigner ce champ.';

    return $messages;
}

function lc_prod_cf7_seed() {
    if (!class_exists('WPCF7_ContactForm')) {
        lc_prod_cf7_error('Contact Form 7 is not active. Run scripts/seed-cf7-production.sh so WP-CLI can install and activate it first.');
    }

    if (!post_type_exists('wpcf7_contact_form') && function_exists('wpcf7_register_post_types')) {
        wpcf7_register_post_types();
    }

    $title = lc_prod_cf7_env_text('LC_CF7_FORM_TITLE', 'Luxury Copro Contact');
    $slug = sanitize_title(lc_prod_cf7_env_text('LC_CF7_FORM_SLUG', 'luxury-copro-contact'));
    $existing = lc_prod_cf7_find_form($slug, $title);
    $overwrite = lc_prod_cf7_env_bool('LC_CF7_OVERWRITE_EXISTING', true);

    if ($existing instanceof WP_Post) {
        $contact_form = WPCF7_ContactForm::get_instance($existing->ID);

        if (!$overwrite) {
            set_theme_mod('lc_cf7_shortcode', lc_prod_cf7_shortcode($existing->ID, $existing->post_title));
            update_option('luxury_copro_cf7_production_seed_version', LC_PROD_CF7_SEED_VERSION, false);
            lc_prod_cf7_log(sprintf('Existing CF7 form "%s" found; content preserved because LC_CF7_OVERWRITE_EXISTING=0.', $existing->post_title));
            return;
        }
    } else {
        $contact_form = WPCF7_ContactForm::get_template(['title' => $title]);
    }

    if (!$contact_form instanceof WPCF7_ContactForm) {
        lc_prod_cf7_error('Unable to prepare the Contact Form 7 form.');
    }

    $contact_form->set_title($title);
    $contact_form->set_properties([
        'form' => lc_prod_cf7_template(),
        'mail' => lc_prod_cf7_mail(),
        'mail_2' => WPCF7_ContactFormTemplate::get_default('mail_2'),
        'messages' => lc_prod_cf7_messages(),
        'additional_settings' => '',
    ]);

    $post_id = $contact_form->save();

    if (!$post_id) {
        lc_prod_cf7_error('Unable to save the Contact Form 7 form.');
    }

    $updated = wp_update_post([
        'ID'        => $post_id,
        'post_name' => $slug,
    ], true);

    if (is_wp_error($updated)) {
        lc_prod_cf7_error(sprintf('Unable to set the CF7 form slug: %s', $updated->get_error_message()));
    }

    set_theme_mod('lc_cf7_shortcode', lc_prod_cf7_shortcode($post_id, $title));
    update_option('luxury_copro_cf7_production_seed_version', LC_PROD_CF7_SEED_VERSION, false);

    $action = $existing instanceof WP_Post ? 'Updated' : 'Created';
    lc_prod_cf7_log(sprintf('%s Contact Form 7 form "%s" with ID %d.', $action, $title, $post_id));
    lc_prod_cf7_log(sprintf('Stored lc_cf7_shortcode for theme "%s".', get_option('stylesheet')));
}

lc_prod_cf7_seed();
