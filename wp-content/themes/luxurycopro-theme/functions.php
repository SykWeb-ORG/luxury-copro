<?php
defined('ABSPATH') || exit;

define('LC_VERSION', '2.5.2');

/* ── HIDE ADMIN BAR ON FRONT END ── */
add_filter('show_admin_bar', '__return_false');

/* ── THEME SETUP ── */
function lc_setup() {
    add_theme_support('title-tag');
    add_theme_support('custom-logo', [
        'height'      => 84,
        'width'       => 280,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => __('Menu principal', 'luxurycopro'),
        'mobile'  => __('Menu mobile', 'luxurycopro'),
    ]);
}
add_action('after_setup_theme', 'lc_setup');

/* ── PRECONNECT HINTS ── */
function lc_preconnect_hints() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>' . "\n";
    if (has_custom_logo()) {
        $logo_id  = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        if ($logo_url) {
            echo '<link rel="preload" as="image" href="' . esc_url($logo_url) . '">' . "\n";
        }
    }
    if (is_front_page()) {
        echo '<link rel="preload" as="video" href="' . esc_url(get_template_directory_uri() . '/assets/video/hero-web.mp4') . '" media="(min-width:769px)">' . "\n";
    }
}
add_action('wp_head', 'lc_preconnect_hints', 1);

/* ── ENQUEUE ASSETS ── */
function lc_enqueue_assets() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap', [], null);
    $css_file = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? 'main.css' : 'main.min.css';
    wp_enqueue_style('lc-main', get_template_directory_uri() . '/assets/css/' . $css_file, ['google-fonts'], LC_VERSION);

    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', [], null, true);
    wp_enqueue_script('gsap-st', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', ['gsap'], null, true);
    $js_file = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? 'main.js' : 'main.min.js';
    wp_enqueue_script('lc-main', get_template_directory_uri() . '/assets/js/' . $js_file, ['gsap', 'gsap-st'], LC_VERSION, true);

    $whatsapp = lc_get_option('lc_whatsapp', '212700727165');
    $email    = lc_get_option('lc_email', 'ezzine.surgar@gmail.com');
    wp_localize_script('lc-main', 'lcData', [
        'whatsapp' => esc_attr($whatsapp),
        'email'    => sanitize_email($email),
    ]);
}
add_action('wp_enqueue_scripts', 'lc_enqueue_assets');

function lc_async_styles($tag, $handle) {
    if (in_array($handle, ['google-fonts', 'lc-main'], true)) {
        return str_replace("media='all'", "media='print' onload=\"this.media='all'\"", $tag);
    }
    return $tag;
}
add_filter('style_loader_tag', 'lc_async_styles', 10, 2);

function lc_critical_css() {
    ?>
    <style id="critical-css">
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
    :root{--bg:#060b14;--surface:#0d1520;--surface-2:#131e2e;--gold:#2968A0;--gold-light:#4A90C4;--gold-dim:rgba(41,104,160,.12);--text:#eef1f5;--muted:#7a8896;--glass:rgba(255,255,255,.04);--border:rgba(41,104,160,.15);--nav-bg:rgba(6,11,20,.65);--nav-bg-solid:rgba(6,11,20,.92);--shadow-color:rgba(0,0,0,.4);--stroke-color:#eef1f5;--ghost-border:rgba(255,255,255,.12)}
    .light{--bg:#f5f7fa;--surface:#ffffff;--surface-2:#edf1f6;--gold:#1B3D5C;--gold-light:#143050;--gold-dim:rgba(27,61,92,.08);--text:#1a1a2e;--muted:#4f5b6e;--glass:rgba(0,0,0,.03);--border:rgba(27,61,92,.15);--nav-bg:rgba(245,247,250,.75);--nav-bg-solid:rgba(245,247,250,.95);--shadow-color:rgba(0,0,0,.08);--stroke-color:#1a1a2e;--ghost-border:rgba(0,0,0,.12)}
    html{scroll-behavior:smooth;overflow-x:hidden}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);transition:background .5s,color .5s}
    .loader{position:fixed;inset:0;background:var(--bg);z-index:10000;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:1.5rem}
    .loader.done{opacity:0;visibility:hidden;pointer-events:none}
    .loader-img{height:80px;width:auto}
    .loader-logo{font-family:'Playfair Display',serif;font-size:2.4rem;color:var(--gold);letter-spacing:8px;overflow:hidden}
    .loader-line{width:180px;height:1px;background:var(--border);position:relative;overflow:hidden}
    nav{position:sticky;top:0;z-index:500;display:flex;justify-content:space-between;align-items:center;padding:1.4rem 5%;backdrop-filter:blur(24px);background:var(--nav-bg);border-bottom:1px solid var(--border)}
    .logo{display:flex;align-items:center;text-decoration:none}
    .logo img{height:50px;width:auto;object-fit:contain}
    .nav-menu{display:flex;gap:2.2rem;list-style:none;align-items:center}
    .nav-menu a{color:var(--muted);text-decoration:none;font-size:.78rem;font-weight:500;letter-spacing:1.8px;text-transform:uppercase}
    .hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;z-index:600}
    .hamburger span{width:24px;height:1.5px;background:var(--gold);display:block}
    .hero{position:relative;min-height:calc(100vh - var(--nav-h,80px));display:flex;align-items:center;overflow:visible;padding-top:3rem;padding-bottom:3rem}
    .hero-content{position:relative;z-index:2;padding:0 5%;max-width:680px}
    .hero h1{font-family:'Playfair Display',serif;font-size:clamp(2.8rem,7vw,5.5rem);font-weight:700;line-height:1.1;margin-bottom:1.5rem}
    .hero h1 em{font-style:italic;color:var(--gold);font-weight:500}
    .hero h1 .stroke{-webkit-text-stroke:1.5px var(--stroke-color);color:transparent}
    .hero-desc{font-size:1.05rem;color:var(--muted);line-height:1.8;max-width:460px;margin-bottom:2.5rem}
    .skip-link{position:absolute;top:-100%;left:50%;transform:translateX(-50%);z-index:100000}
    .cur-dot,.cur-ring{position:fixed;border-radius:50%;pointer-events:none;z-index:9999}
    @media(max-width:768px){.cur-dot,.cur-ring{display:none}.hamburger{display:flex}.nav-menu{display:none}}
    </style>
    <?php
}
add_action('wp_head', 'lc_critical_css', 2);

function lc_defer_scripts($tag, $handle) {
    if (in_array($handle, ['gsap', 'gsap-st', 'lc-main'], true)) {
        return str_replace(' src=', ' defer src=', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'lc_defer_scripts', 10, 2);

/* ── POLYLANG INTEGRATION ── */
function lc_polylang_string_keys() {
    return [
        'lc_address_1' => 'Adresse ligne 1',
        'lc_address_2' => 'Adresse ligne 2',
        'lc_city' => 'Ville',
        'lc_hero_tag' => 'Hero - petit label',
        'lc_hero_title' => 'Hero - titre',
        'lc_hero_desc' => 'Hero - description',
        'lc_hero_btn1' => 'Hero - bouton principal',
        'lc_hero_btn2' => 'Hero - bouton secondaire',
        'lc_about_label' => 'Notre société - petit label',
        'lc_about_title' => 'Notre société - titre',
        'lc_about_p1' => 'Notre société - paragraphe 1',
        'lc_about_p2' => 'Notre société - paragraphe 2',
        'lc_refs_label' => 'Références - petit label',
        'lc_refs_title' => 'Références - titre',
        'lc_refs_intro' => 'Références - introduction',
        'lc_biens_label' => 'Biens - petit label',
        'lc_biens_title' => 'Biens - titre',
        'lc_biens_title_fallback' => 'Biens - titre exemples',
        'lc_biens_desc' => 'Biens - introduction',
        'lc_biens_desc_fallback' => 'Biens - introduction exemples',
        'lc_srv1_title' => 'Service 1 - titre',
        'lc_srv1_desc' => 'Service 1 - description',
        'lc_srv2_title' => 'Service 2 - titre',
        'lc_srv2_desc' => 'Service 2 - description',
        'lc_srv3_title' => 'Service 3 - titre',
        'lc_srv3_desc' => 'Service 3 - description',
        'lc_srv4_title' => 'Service 4 - titre',
        'lc_srv4_desc' => 'Service 4 - description',
    ];
}

function lc_register_polylang_strings() {
    if (!function_exists('pll_register_string')) {
        return;
    }

    foreach (lc_polylang_string_keys() as $key => $label) {
        $value = get_theme_mod($key);

        if (!is_string($value) || $value === '') {
            continue;
        }

        pll_register_string('Luxury Copro - ' . $label, $value, 'Luxury Copro', true);
    }
}
add_action('init', 'lc_register_polylang_strings');

function lc_translate_theme_text_with_polylang($translation, $text, $domain) {
    if ($domain !== 'luxurycopro' || !function_exists('pll__')) {
        return $translation;
    }

    $polylang_translation = pll__($text);

    return $polylang_translation !== $text ? $polylang_translation : $translation;
}
add_filter('gettext', 'lc_translate_theme_text_with_polylang', 20, 3);

function lc_refresh_polylang_rewrite_rules_on_language_change() {
    if (!function_exists('pll_languages_list')) {
        return;
    }

    $languages = pll_languages_list(['fields' => 'slug']);

    if (!is_array($languages) || empty($languages)) {
        return;
    }

    sort($languages);
    $signature = implode('|', $languages);

    if ($signature === get_option('lc_polylang_language_signature', '')) {
        return;
    }

    update_option('lc_polylang_language_signature', $signature, false);
    delete_option('rewrite_rules');
}
add_action('wp_loaded', 'lc_refresh_polylang_rewrite_rules_on_language_change', 20);

function lc_language_switcher($context = 'desktop') {
    if (!function_exists('pll_the_languages')) {
        return;
    }

    $languages = pll_the_languages([
        'raw' => 1,
        'hide_if_empty' => 0,
        'hide_if_no_translation' => 0,
    ]);

    if (!is_array($languages) || count($languages) < 2) {
        return;
    }

    $classes = 'lc-lang-switcher lc-lang-switcher--' . sanitize_html_class($context);
    echo '<ul class="' . esc_attr($classes) . '" aria-label="' . esc_attr__('Choisir la langue', 'luxurycopro') . '">';

    foreach ($languages as $language) {
        $slug = isset($language['slug']) ? strtoupper((string) $language['slug']) : '';
        $name = isset($language['name']) ? (string) $language['name'] : $slug;
        $label = $slug !== '' ? $slug : mb_strtoupper(mb_substr($name, 0, 2));
        $is_current = !empty($language['current_lang']);
        $item_class = $is_current ? ' class="is-current"' : '';

        $url = $language['url'] ?? '';
        $locale = isset($language['locale']) ? str_replace('_', '-', (string) $language['locale']) : '';

        echo '<li' . $item_class . '>';
        if ($is_current || empty($url)) {
            echo '<span' . ($is_current ? ' aria-current="true"' : '') . '>' . esc_html($label) . '</span>';
        } else {
            echo '<a href="' . esc_url($url) . '" lang="' . esc_attr($locale) . '">' . esc_html($label) . '</a>';
        }
        echo '</li>';
    }

    echo '</ul>';
}

/* ── CPT: PROPERTIES ── */
function lc_register_properties_cpt() {
    $labels = [
        'name'               => __('Biens Immobiliers', 'luxurycopro'),
        'singular_name'      => __('Bien', 'luxurycopro'),
        'add_new'            => __('Ajouter un bien', 'luxurycopro'),
        'add_new_item'       => __('Ajouter un nouveau bien', 'luxurycopro'),
        'edit_item'          => __('Modifier le bien', 'luxurycopro'),
        'view_item'          => __('Voir le bien', 'luxurycopro'),
        'all_items'          => __('Tous les biens', 'luxurycopro'),
        'search_items'       => __('Rechercher un bien', 'luxurycopro'),
        'not_found'          => __('Aucun bien trouvé', 'luxurycopro'),
        'not_found_in_trash' => __('Aucun bien dans la corbeille', 'luxurycopro'),
        'menu_name'          => __('Biens', 'luxurycopro'),
    ];
    register_post_type('property', [
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'biens'],
        'menu_icon'    => 'dashicons-building',
        'supports'     => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'lc_register_properties_cpt');

/* ── PROPERTY META BOXES ── */
function lc_property_meta_boxes() {
    add_meta_box('lc_property_details', __('Détails du bien', 'luxurycopro'), 'lc_property_meta_html', 'property', 'normal', 'high');
}
add_action('add_meta_boxes', 'lc_property_meta_boxes');

function lc_property_meta_html($post) {
    wp_nonce_field('lc_property_meta', 'lc_property_nonce');
    $fields = [
        'price'     => ['label' => 'Prix (ex: 3 200 000 MAD)',  'type' => 'text'],
        'location'  => ['label' => 'Localisation',               'type' => 'text'],
        'ref'       => ['label' => 'Référence (ex: LC-2024-001)','type' => 'text'],
        'type'      => ['label' => 'Type de transaction',         'type' => 'select', 'options' => ['Vente', 'Location', 'Exclusif']],
        'bedrooms'  => ['label' => 'Chambres',                    'type' => 'text'],
        'bathrooms' => ['label' => 'Salles de bain',              'type' => 'text'],
        'area'      => ['label' => 'Surface (m²)',                'type' => 'text'],
        'land'      => ['label' => 'Terrain / 4ème valeur',       'type' => 'text'],
        'land_label'=> ['label' => 'Label 4ème valeur (ex: Terrain, Étage)', 'type' => 'text'],
        'features'  => ['label' => 'Caractéristiques (une par ligne)', 'type' => 'textarea'],
    ];
    echo '<table class="form-table">';
    foreach ($fields as $key => $f) {
        $val = get_post_meta($post->ID, '_lc_' . $key, true);
        echo '<tr><th><label for="lc_' . esc_attr($key) . '">' . esc_html($f['label']) . '</label></th><td>';
        if ($f['type'] === 'textarea') {
            echo '<textarea id="lc_' . esc_attr($key) . '" name="lc_' . esc_attr($key) . '" rows="4" class="large-text">' . esc_textarea($val) . '</textarea>';
        } elseif ($f['type'] === 'select') {
            echo '<select id="lc_' . esc_attr($key) . '" name="lc_' . esc_attr($key) . '">';
            foreach ($f['options'] as $opt) {
                echo '<option value="' . esc_attr($opt) . '"' . selected($val, $opt, false) . '>' . esc_html($opt) . '</option>';
            }
            echo '</select>';
        } else {
            echo '<input type="text" id="lc_' . esc_attr($key) . '" name="lc_' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="regular-text">';
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

function lc_save_property_meta($post_id) {
    if (!isset($_POST['lc_property_nonce']) || !wp_verify_nonce($_POST['lc_property_nonce'], 'lc_property_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $keys = ['price', 'location', 'ref', 'type', 'bedrooms', 'bathrooms', 'area', 'land', 'land_label', 'features'];
    foreach ($keys as $key) {
        if (isset($_POST['lc_' . $key])) {
            update_post_meta($post_id, '_lc_' . $key, sanitize_textarea_field($_POST['lc_' . $key]));
        }
    }
}
add_action('save_post_property', 'lc_save_property_meta');

/* ── CUSTOMIZER: EDITABLE SITE OPTIONS ── */
function lc_customize_register($wp_customize) {
    // --- Panel ---
    $wp_customize->add_panel('lc_panel', [
        'title'    => __('Luxury Copro', 'luxurycopro'),
        'priority' => 30,
    ]);

    // --- Section: Contact Info ---
    $wp_customize->add_section('lc_contact', [
        'title' => __('Coordonnées', 'luxurycopro'),
        'panel' => 'lc_panel',
    ]);
    $contact_fields = [
        'lc_phone1'   => ['label' => 'Téléphone 1',   'default' => '07 00 72 71 65'],
        'lc_phone2'   => ['label' => 'Téléphone 2',   'default' => '06 53 64 83 82'],
        'lc_whatsapp'  => ['label' => 'WhatsApp (format international, sans +)', 'default' => '212700727165'],
        'lc_email'     => ['label' => 'E-mail',        'default' => 'ezzine.surgar@gmail.com'],
        'lc_address_1' => ['label' => 'Adresse ligne 1', 'default' => 'Mg Rdc Imm A, Résidence Amira'],
        'lc_address_2' => ['label' => 'Adresse ligne 2', 'default' => 'Avenue 4ème DMM, Camp El Ghoul'],
        'lc_city'      => ['label' => 'Ville',         'default' => 'Marrakech'],
        'lc_maps_embed'=> ['label' => 'Google Maps Embed URL', 'default' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3396.5!2d-8.0135!3d31.6305!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xdafee8d985a5bef%3A0x1c3e4a3b8e9f1d2a!2sR%C3%A9sidence%20Amira%2C%20Avenue%204%C3%A8me%20DMM%2C%20Camp%20El%20Ghoul%2C%20Marrakech!5e0!3m2!1sfr!2sma!4v1700000000000'],
    ];
    foreach ($contact_fields as $id => $f) {
        $wp_customize->add_setting($id, ['default' => $f['default'], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
        $wp_customize->add_control($id, ['label' => $f['label'], 'section' => 'lc_contact', 'type' => 'text']);
    }

    // --- Section: Legal ---
    $wp_customize->add_section('lc_legal', [
        'title' => __('Informations légales', 'luxurycopro'),
        'panel' => 'lc_panel',
    ]);
    $legal_fields = [
        'lc_rc'   => ['label' => 'RC',   'default' => '138059'],
        'lc_tp'   => ['label' => 'TP',   'default' => '64261180'],
        'lc_if'   => ['label' => 'IF',   'default' => '53830046'],
        'lc_cnss' => ['label' => 'CNSS', 'default' => '4910963'],
        'lc_ice'  => ['label' => 'ICE',  'default' => '003295207000042'],
    ];
    foreach ($legal_fields as $id => $f) {
        $wp_customize->add_setting($id, ['default' => $f['default'], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
        $wp_customize->add_control($id, ['label' => $f['label'], 'section' => 'lc_legal', 'type' => 'text']);
    }

    // --- Section: Hero ---
    $wp_customize->add_section('lc_hero', [
        'title' => __('Section Hero', 'luxurycopro'),
        'panel' => 'lc_panel',
    ]);
    $hero_fields = [
        'lc_hero_tag'   => ['label' => 'Tagline hero',          'default' => 'Copropriété & Immobilier — Marrakech'],
        'lc_hero_title' => ['label' => 'Titre hero (HTML)',      'default' => 'Votre Patrimoine,<br><em>Notre</em><br><span class="stroke">Expertise</span>'],
        'lc_hero_desc'  => ['label' => 'Description hero',       'default' => 'Gestion de copropriété, location, achat et vente de biens immobiliers. Un accompagnement professionnel et transparent à Marrakech.'],
        'lc_hero_btn1'  => ['label' => 'Bouton 1 (texte)',       'default' => 'Voir Nos Biens'],
        'lc_hero_btn2'  => ['label' => 'Bouton 2 (texte)',       'default' => 'Nos Services'],
    ];
    foreach ($hero_fields as $id => $f) {
        $sanitize = ($id === 'lc_hero_title') ? 'wp_kses_post' : 'sanitize_text_field';
        $wp_customize->add_setting($id, ['default' => $f['default'], 'sanitize_callback' => $sanitize, 'transport' => 'refresh']);
        $wp_customize->add_control($id, ['label' => $f['label'], 'section' => 'lc_hero', 'type' => 'text']);
    }

    // --- Section: Services ---
    $wp_customize->add_section('lc_services', [
        'title' => __('Services', 'luxurycopro'),
        'panel' => 'lc_panel',
    ]);
    $services = [
        1 => ['title' => 'Gestion de Copropriété', 'desc' => 'Gestion administrative, technique et financière des copropriétés. Suivi des charges, budgets et assemblées générales.'],
        2 => ['title' => 'Location', 'desc' => 'Mise en location, sélection des locataires, suivi des contrats et gestion quotidienne de vos biens locatifs.'],
        3 => ['title' => 'Achat & Vente', 'desc' => 'Accompagnement personnalisé pour l\'achat et la vente. Valorisation, promotion et assistance jusqu\'à la finalisation.'],
        4 => ['title' => 'Travaux & Maintenance', 'desc' => 'Entretien des équipements communs, suivi des prestataires et travaux techniques divers pour vos résidences.'],
    ];
    foreach ($services as $i => $s) {
        $wp_customize->add_setting("lc_srv{$i}_title", ['default' => $s['title'], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("lc_srv{$i}_title", ['label' => "Service {$i} — Titre", 'section' => 'lc_services', 'type' => 'text']);
        $wp_customize->add_setting("lc_srv{$i}_desc", ['default' => $s['desc'], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("lc_srv{$i}_desc", ['label' => "Service {$i} — Description", 'section' => 'lc_services', 'type' => 'textarea']);
    }
    // --- Section: Notre Société ---
    $wp_customize->add_section('lc_about', [
        'title' => __('Notre Société', 'luxurycopro'),
        'panel' => 'lc_panel',
    ]);
    $wp_customize->add_setting('lc_about_visible', ['default' => true, 'sanitize_callback' => 'wp_validate_boolean']);
    $wp_customize->add_control('lc_about_visible', ['label' => 'Afficher la section', 'section' => 'lc_about', 'type' => 'checkbox']);
    $about_fields = [
        'lc_about_label' => ['label' => 'Petit label', 'default' => 'Qui Sommes-Nous', 'sanitize' => 'sanitize_text_field', 'type' => 'text'],
        'lc_about_title' => ['label' => 'Titre principal (HTML)', 'default' => 'Notre <span style="color:var(--gold)">Société</span>', 'sanitize' => 'wp_kses_post', 'type' => 'text'],
        'lc_about_p1'    => ['label' => 'Premier paragraphe', 'default' => 'Notre société est une entreprise à responsabilité limitée, expérimentée dans la gestion de copropriété ainsi que dans la gestion et la valorisation des biens immobiliers. Forte d\'une approche professionnelle et rigoureuse, elle accompagne les copropriétaires dans l\'administration, la location, l\'achat et la vente de leurs biens immobiliers.', 'sanitize' => 'wp_kses_post', 'type' => 'textarea'],
        'lc_about_p2'    => ['label' => 'Deuxième paragraphe', 'default' => 'Grâce à une organisation fondée sur la transparence, la proximité et la qualité de service, nous veillons à assurer une gestion efficace des résidences et à répondre aux attentes de notre clientèle dans le respect des dispositions réglementaires en vigueur.', 'sanitize' => 'wp_kses_post', 'type' => 'textarea'],
    ];
    foreach ($about_fields as $id => $f) {
        $wp_customize->add_setting($id, ['default' => $f['default'], 'sanitize_callback' => $f['sanitize'], 'transport' => 'refresh']);
        $wp_customize->add_control($id, ['label' => $f['label'], 'section' => 'lc_about', 'type' => $f['type']]);
    }

    // --- Section: Références ---
    $wp_customize->add_section('lc_refs', [
        'title' => __('Nos Références', 'luxurycopro'),
        'panel' => 'lc_panel',
    ]);
    $wp_customize->add_setting('lc_refs_visible', ['default' => true, 'sanitize_callback' => 'wp_validate_boolean']);
    $wp_customize->add_control('lc_refs_visible', ['label' => 'Afficher la section', 'section' => 'lc_refs', 'type' => 'checkbox']);
    $refs_fields = [
        'lc_refs_label' => ['label' => 'Petit label', 'default' => 'Références', 'sanitize' => 'sanitize_text_field', 'type' => 'text'],
        'lc_refs_title' => ['label' => 'Titre principal (HTML)', 'default' => 'Ils nous font <span style="color:var(--gold)">confiance</span>', 'sanitize' => 'wp_kses_post', 'type' => 'text'],
        'lc_refs_intro' => ['label' => 'Texte d\'introduction', 'default' => 'Nous accompagnons différentes résidences et clients dans la gestion, la valorisation et le suivi de leurs biens immobiliers.', 'sanitize' => 'wp_kses_post', 'type' => 'textarea'],
    ];
    foreach ($refs_fields as $id => $f) {
        $wp_customize->add_setting($id, ['default' => $f['default'], 'sanitize_callback' => $f['sanitize'], 'transport' => 'refresh']);
        $wp_customize->add_control($id, ['label' => $f['label'], 'section' => 'lc_refs', 'type' => $f['type']]);
    }

    // --- Section: Nos Biens ---
    $wp_customize->add_section('lc_biens', [
        'title' => __('Nos Biens', 'luxurycopro'),
        'panel' => 'lc_panel',
    ]);
    $wp_customize->add_setting('lc_biens_visible', ['default' => true, 'sanitize_callback' => 'wp_validate_boolean']);
    $wp_customize->add_control('lc_biens_visible', ['label' => 'Afficher la section', 'section' => 'lc_biens', 'type' => 'checkbox']);
    $biens_fields = [
        'lc_biens_label' => ['label' => 'Petit label', 'default' => 'Notre Portefeuille', 'sanitize' => 'sanitize_text_field', 'type' => 'text'],
        'lc_biens_title' => ['label' => 'Titre (HTML)', 'default' => 'Nos Biens<br><span style="color:var(--gold)">Disponibles</span>', 'sanitize' => 'wp_kses_post', 'type' => 'text'],
        'lc_biens_title_fallback' => ['label' => 'Titre mode exemples (HTML)', 'default' => 'Exemples de Biens<br><span style="color:var(--gold)">Disponibles</span>', 'sanitize' => 'wp_kses_post', 'type' => 'text'],
        'lc_biens_desc'  => ['label' => 'Texte d\'introduction', 'default' => 'Découvrez notre sélection de biens immobiliers à Marrakech. Contactez-nous pour plus d\'informations.', 'sanitize' => 'wp_kses_post', 'type' => 'textarea'],
        'lc_biens_desc_fallback' => ['label' => 'Texte mode exemples', 'default' => 'Les biens présentés ci-dessous sont des exemples illustratifs. Pour consulter nos offres réelles et actualisées, veuillez nous contacter directement.', 'sanitize' => 'wp_kses_post', 'type' => 'textarea'],
    ];
    foreach ($biens_fields as $id => $f) {
        $wp_customize->add_setting($id, ['default' => $f['default'], 'sanitize_callback' => $f['sanitize'], 'transport' => 'refresh']);
        $wp_customize->add_control($id, ['label' => $f['label'], 'section' => 'lc_biens', 'type' => $f['type']]);
    }
}
add_action('customize_register', 'lc_customize_register');

/* ── HELPER: Get Customizer option ── */
function lc_get_option($key, $default = '') {
    $value = get_theme_mod($key, $default);

    if (is_string($value) && function_exists('pll__') && array_key_exists($key, lc_polylang_string_keys())) {
        return pll__($value);
    }

    return $value;
}

function lc_translate_content_value($value) {
    if (!is_string($value) || $value === '' || !function_exists('pll__')) {
        return $value;
    }

    return pll__($value);
}

function lc_translate_property_item(array $property) {
    foreach (['badge', 'title', 'location', 'land_label', 'desc'] as $key) {
        if (isset($property[$key])) {
            $property[$key] = lc_translate_content_value($property[$key]);
        }
    }

    if (isset($property['features']) && is_array($property['features'])) {
        $property['features'] = array_map('lc_translate_content_value', $property['features']);
    }

    return $property;
}

function lc_translate_reference_item(array $reference) {
    foreach (['name', 'service', 'location', 'desc'] as $key) {
        if (isset($reference[$key])) {
            $reference[$key] = lc_translate_content_value($reference[$key]);
        }
    }

    return $reference;
}

function lc_get_translated_page_url($path) {
    $path = trim($path, '/');
    $page = get_page_by_path($path);

    if ($page instanceof WP_Post && function_exists('pll_get_post')) {
        $translated_id = pll_get_post($page->ID);

        if ($translated_id) {
            return get_permalink($translated_id);
        }
    }

    return home_url('/' . $path . '/');
}

/* ── HELPER: Get properties (CPT or fallback) ── */
function lc_get_properties() {
    $query = new WP_Query([
        'post_type'      => 'property',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
    ]);

    if ($query->have_posts()) {
        $properties = [];
        while ($query->have_posts()) {
            $query->the_post();
            $id = get_the_ID();
            $property_type = get_post_meta($id, '_lc_type', true) ?: 'Vente';
            $properties[] = lc_translate_property_item([
                'id'         => $id,
                'icon'       => mb_substr(get_the_title(), 0, 1),
                'badge'      => $property_type,
                'badge_class'=> lc_badge_class($property_type),
                'price'      => get_post_meta($id, '_lc_price', true),
                'title'      => get_the_title(),
                'location'   => get_post_meta($id, '_lc_location', true),
                'ref'        => get_post_meta($id, '_lc_ref', true),
                'bedrooms'   => get_post_meta($id, '_lc_bedrooms', true),
                'bathrooms'  => get_post_meta($id, '_lc_bathrooms', true),
                'area'       => get_post_meta($id, '_lc_area', true),
                'land'       => get_post_meta($id, '_lc_land', true),
                'land_label' => get_post_meta($id, '_lc_land_label', true) ?: 'Terrain',
                'desc'       => get_the_content(),
                'features'   => array_filter(explode("\n", get_post_meta($id, '_lc_features', true) ?: '')),
                'has_thumb'  => has_post_thumbnail(),
                'thumb_url'  => get_the_post_thumbnail_url($id, 'large'),
            ]);
        }
        wp_reset_postdata();
        return ['source' => 'cpt', 'items' => $properties];
    }

    return ['source' => 'fallback', 'items' => lc_fallback_properties()];
}

function lc_badge_class($type) {
    $map = ['Vente' => 'badge-vente', 'Location' => 'badge-location', 'Exclusif' => 'badge-exclu'];
    return $map[$type] ?? 'badge-vente';
}

function lc_fallback_properties() {
    $properties = [
        ['icon'=>'V','badge'=>'Exclusif','badge_class'=>'badge-exclu','price'=>'3 200 000 MAD','title'=>'Villa Contemporaine avec Piscine','location'=>'Route de l\'Ourika, Marrakech','ref'=>'LC-2024-001','bedrooms'=>'4','bathrooms'=>'3','area'=>'320','land'=>'800','land_label'=>'Terrain','desc'=>'Magnifique villa contemporaine située dans un quartier résidentiel prisé de Marrakech.','features'=>['Piscine privée','Jardin paysager','Garage double','Climatisation centrale','Cuisine équipée','Vue sur l\'Atlas','Sécurité 24h/24','Proche commodités']],
        ['icon'=>'A','badge'=>'Vente','badge_class'=>'badge-vente','price'=>'1 450 000 MAD','title'=>'Appartement Standing Guéliz','location'=>'Av. Mohammed V, Guéliz','ref'=>'LC-2024-002','bedrooms'=>'3','bathrooms'=>'2','area'=>'140','land'=>'3è','land_label'=>'Étage','desc'=>'Superbe appartement de standing au cœur de Guéliz.','features'=>['Balcon terrasse','Ascenseur','Parking sous-sol','Résidence sécurisée','Double vitrage','Parquet massif','Cuisine américaine','Proche tramway']],
        ['icon'=>'R','badge'=>'Vente','badge_class'=>'badge-vente','price'=>'4 800 000 MAD','title'=>'Riad Rénové — Médina','location'=>'Derb Jdid, Médina','ref'=>'LC-2024-003','bedrooms'=>'5','bathrooms'=>'5','area'=>'280','land'=>'Patio','land_label'=>'+ Terrasse','desc'=>'Riad d\'exception entièrement rénové dans les règles de l\'art.','features'=>['Patio avec fontaine','Terrasse panoramique','Zellige traditionnel','Hammam privé','Cuisine marocaine','Vue Koutoubia','Bois de cèdre sculpté','Rentabilité locative']],
        ['icon'=>'D','badge'=>'Location','badge_class'=>'badge-location','price'=>'8 500 MAD /mois','title'=>'Duplex Meublé Hivernage','location'=>'Av. Echouhada, Hivernage','ref'=>'LC-2024-004','bedrooms'=>'2','bathrooms'=>'2','area'=>'110','land'=>'Meublé','land_label'=>'Équipé','desc'=>'Duplex entièrement meublé et équipé dans le quartier prestigieux de l\'Hivernage.','features'=>['Entièrement meublé','Piscine résidence','Climatisation','Internet fibre','Machine à laver','Quartier calme','Proche centre','Disponible immédiatement']],
        ['icon'=>'V','badge'=>'Exclusif','badge_class'=>'badge-exclu','price'=>'6 500 000 MAD','title'=>'Villa de Luxe Palmeraie','location'=>'Circuit de la Palmeraie','ref'=>'LC-2024-005','bedrooms'=>'5','bathrooms'=>'4','area'=>'450','land'=>'2000','land_label'=>'Terrain','desc'=>'Villa de prestige nichée dans la légendaire Palmeraie de Marrakech.','features'=>['Piscine à débordement','Pool house','Jardin tropical','Personnel de maison','Domotique','Hammam & spa','Suite parentale 60m²','Sécurité renforcée']],
        ['icon'=>'T','badge'=>'Vente','badge_class'=>'badge-vente','price'=>'750 000 MAD','title'=>'Terrain Constructible Targa','location'=>'Targa, Marrakech','ref'=>'LC-2024-006','bedrooms'=>'—','bathrooms'=>'—','area'=>'500','land'=>'R+2','land_label'=>'Autorisé','desc'=>'Terrain plat et bien situé dans le quartier recherché de Targa.','features'=>['Titre foncier','Autorisation R+2','Réseaux en bordure','Accès goudronné','Quartier résidentiel','Terrain plat','Environnement calme','Proche écoles']],
    ];

    return array_map('lc_translate_property_item', $properties);
}

/* ── CPT: REFERENCES ── */
function lc_register_references_cpt() {
    register_post_type('reference', [
        'labels' => [
            'name'               => __('Références', 'luxurycopro'),
            'singular_name'      => __('Référence', 'luxurycopro'),
            'add_new'            => __('Ajouter une référence', 'luxurycopro'),
            'add_new_item'       => __('Ajouter une nouvelle référence', 'luxurycopro'),
            'edit_item'          => __('Modifier la référence', 'luxurycopro'),
            'view_item'          => __('Voir la référence', 'luxurycopro'),
            'all_items'          => __('Toutes les références', 'luxurycopro'),
            'search_items'       => __('Rechercher une référence', 'luxurycopro'),
            'not_found'          => __('Aucune référence trouvée', 'luxurycopro'),
            'not_found_in_trash' => __('Aucune référence dans la corbeille', 'luxurycopro'),
            'menu_name'          => __('Références', 'luxurycopro'),
        ],
        'public'       => false,
        'show_ui'      => true,
        'has_archive'  => false,
        'menu_icon'    => 'dashicons-groups',
        'supports'     => ['title', 'thumbnail'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'lc_register_references_cpt');

function lc_reference_meta_boxes() {
    add_meta_box('lc_reference_details', __('Détails de la référence', 'luxurycopro'), 'lc_reference_meta_html', 'reference', 'normal', 'high');
}
add_action('add_meta_boxes', 'lc_reference_meta_boxes');

function lc_reference_meta_html($post) {
    wp_nonce_field('lc_reference_meta', 'lc_reference_nonce');
    $fields = [
        'ref_service'  => ['label' => 'Type de service', 'type' => 'select', 'options' => ['Copropriété', 'Gestion de copropriété', 'Location', 'Achat & Vente', 'Suivi immobilier']],
        'ref_location' => ['label' => 'Localisation', 'type' => 'text'],
        'ref_desc'     => ['label' => 'Description courte', 'type' => 'textarea'],
        'ref_order'    => ['label' => 'Ordre d\'affichage (nombre)', 'type' => 'text'],
        'ref_active'   => ['label' => 'Actif', 'type' => 'checkbox'],
    ];
    echo '<table class="form-table">';
    foreach ($fields as $key => $f) {
        $val = get_post_meta($post->ID, '_lc_' . $key, true);
        if ($key === 'ref_active' && $val === '') $val = '1';
        echo '<tr><th><label for="lc_' . esc_attr($key) . '">' . esc_html($f['label']) . '</label></th><td>';
        if ($f['type'] === 'textarea') {
            echo '<textarea id="lc_' . esc_attr($key) . '" name="lc_' . esc_attr($key) . '" rows="3" class="large-text">' . esc_textarea($val) . '</textarea>';
        } elseif ($f['type'] === 'select') {
            echo '<select id="lc_' . esc_attr($key) . '" name="lc_' . esc_attr($key) . '">';
            foreach ($f['options'] as $opt) {
                echo '<option value="' . esc_attr($opt) . '"' . selected($val, $opt, false) . '>' . esc_html($opt) . '</option>';
            }
            echo '</select>';
        } elseif ($f['type'] === 'checkbox') {
            echo '<input type="checkbox" id="lc_' . esc_attr($key) . '" name="lc_' . esc_attr($key) . '" value="1"' . checked($val, '1', false) . '>';
        } else {
            echo '<input type="text" id="lc_' . esc_attr($key) . '" name="lc_' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="regular-text">';
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

function lc_save_reference_meta($post_id) {
    if (!isset($_POST['lc_reference_nonce']) || !wp_verify_nonce($_POST['lc_reference_nonce'], 'lc_reference_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $text_keys = ['ref_service', 'ref_location', 'ref_desc', 'ref_order'];
    foreach ($text_keys as $key) {
        if (isset($_POST['lc_' . $key])) {
            update_post_meta($post_id, '_lc_' . $key, sanitize_textarea_field($_POST['lc_' . $key]));
        }
    }
    update_post_meta($post_id, '_lc_ref_active', isset($_POST['lc_ref_active']) ? '1' : '0');
}
add_action('save_post_reference', 'lc_save_reference_meta');

function lc_get_references() {
    $query = new WP_Query([
        'post_type'      => 'reference',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_lc_ref_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'meta_query'     => [['key' => '_lc_ref_active', 'value' => '1']],
    ]);
    $refs = [];
    while ($query->have_posts()) {
        $query->the_post();
        $id = get_the_ID();
        $refs[] = lc_translate_reference_item([
            'name'      => get_the_title(),
            'service'   => get_post_meta($id, '_lc_ref_service', true),
            'location'  => get_post_meta($id, '_lc_ref_location', true),
            'desc'      => get_post_meta($id, '_lc_ref_desc', true),
            'has_thumb' => has_post_thumbnail(),
            'thumb_url' => get_the_post_thumbnail_url($id, 'medium'),
        ]);
    }
    wp_reset_postdata();
    return $refs;
}

/* ── SEO: JSON-LD STRUCTURED DATA ── */
function lc_jsonld_structured_data() {
    $name    = 'Luxury Copro';
    $phone   = lc_get_option('lc_phone1', '07 00 72 71 65');
    $email   = lc_get_option('lc_email', 'ezzine.surgar@gmail.com');
    $addr1   = lc_get_option('lc_address_1', 'Mg Rdc Imm A, Résidence Amira');
    $addr2   = lc_get_option('lc_address_2', 'Avenue 4ème DMM, Camp El Ghoul');
    $city    = lc_get_option('lc_city', 'Marrakech');

    $logo_url = get_template_directory_uri() . '/assets/img/logo.png';

    $schema = [
        '@context'     => 'https://schema.org',
        '@type'        => ['RealEstateAgent', 'LocalBusiness'],
        'name'         => $name,
        'legalName'    => 'EZZINE SURGAR S.A.R.L.',
        'telephone'    => $phone,
        'email'        => $email,
        'logo'         => $logo_url,
        'image'        => $logo_url,
        'address'      => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $addr1 . ', ' . $addr2,
            'addressLocality' => $city,
            'addressCountry'  => 'MA',
        ],
        'geo'          => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => 31.6305,
            'longitude' => -8.0135,
        ],
        'openingHoursSpecification' => [
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            'opens'     => '09:00',
            'closes'    => '19:00',
        ],
        'priceRange'   => '$$',
        'areaServed'   => $city,
        'url'          => home_url('/'),
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name'  => 'Services immobiliers',
            'itemListElement' => [
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Gestion de Copropriété']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Location']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Achat & Vente']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Travaux & Maintenance']],
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'lc_jsonld_structured_data');

/* ── FLUSH REWRITE ON ACTIVATION ── */
function lc_activate() {
    lc_register_properties_cpt();
    lc_register_references_cpt();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'lc_activate');
