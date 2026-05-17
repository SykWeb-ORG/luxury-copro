<?php
defined('ABSPATH') || exit;

define('LC_VERSION', '1.4.0');

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

/* ── ENQUEUE ASSETS ── */
function lc_enqueue_assets() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap', [], null);
    wp_enqueue_style('lc-main', get_template_directory_uri() . '/assets/css/main.css', ['google-fonts'], LC_VERSION);

    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', [], null, true);
    wp_enqueue_script('gsap-st', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', ['gsap'], null, true);
    wp_enqueue_script('lc-main', get_template_directory_uri() . '/assets/js/main.js', ['gsap', 'gsap-st'], LC_VERSION, true);

    $whatsapp = lc_get_option('lc_whatsapp', '212700727165');
    $email    = lc_get_option('lc_email', 'ezzine.surgar@gmail.com');
    wp_localize_script('lc-main', 'lcData', [
        'whatsapp' => esc_attr($whatsapp),
        'email'    => sanitize_email($email),
    ]);
}
add_action('wp_enqueue_scripts', 'lc_enqueue_assets');

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
    return get_theme_mod($key, $default);
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
            $properties[] = [
                'id'         => $id,
                'icon'       => mb_substr(get_the_title(), 0, 1),
                'badge'      => get_post_meta($id, '_lc_type', true) ?: 'Vente',
                'badge_class'=> lc_badge_class(get_post_meta($id, '_lc_type', true)),
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
            ];
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
    return [
        ['icon'=>'V','badge'=>'Exclusif','badge_class'=>'badge-exclu','price'=>'3 200 000 MAD','title'=>'Villa Contemporaine avec Piscine','location'=>'Route de l\'Ourika, Marrakech','ref'=>'LC-2024-001','bedrooms'=>'4','bathrooms'=>'3','area'=>'320','land'=>'800','land_label'=>'Terrain','desc'=>'Magnifique villa contemporaine située dans un quartier résidentiel prisé de Marrakech.','features'=>['Piscine privée','Jardin paysager','Garage double','Climatisation centrale','Cuisine équipée','Vue sur l\'Atlas','Sécurité 24h/24','Proche commodités']],
        ['icon'=>'A','badge'=>'Vente','badge_class'=>'badge-vente','price'=>'1 450 000 MAD','title'=>'Appartement Standing Guéliz','location'=>'Av. Mohammed V, Guéliz','ref'=>'LC-2024-002','bedrooms'=>'3','bathrooms'=>'2','area'=>'140','land'=>'3è','land_label'=>'Étage','desc'=>'Superbe appartement de standing au cœur de Guéliz.','features'=>['Balcon terrasse','Ascenseur','Parking sous-sol','Résidence sécurisée','Double vitrage','Parquet massif','Cuisine américaine','Proche tramway']],
        ['icon'=>'R','badge'=>'Vente','badge_class'=>'badge-vente','price'=>'4 800 000 MAD','title'=>'Riad Rénové — Médina','location'=>'Derb Jdid, Médina','ref'=>'LC-2024-003','bedrooms'=>'5','bathrooms'=>'5','area'=>'280','land'=>'Patio','land_label'=>'+ Terrasse','desc'=>'Riad d\'exception entièrement rénové dans les règles de l\'art.','features'=>['Patio avec fontaine','Terrasse panoramique','Zellige traditionnel','Hammam privé','Cuisine marocaine','Vue Koutoubia','Bois de cèdre sculpté','Rentabilité locative']],
        ['icon'=>'D','badge'=>'Location','badge_class'=>'badge-location','price'=>'8 500 MAD /mois','title'=>'Duplex Meublé Hivernage','location'=>'Av. Echouhada, Hivernage','ref'=>'LC-2024-004','bedrooms'=>'2','bathrooms'=>'2','area'=>'110','land'=>'Meublé','land_label'=>'Équipé','desc'=>'Duplex entièrement meublé et équipé dans le quartier prestigieux de l\'Hivernage.','features'=>['Entièrement meublé','Piscine résidence','Climatisation','Internet fibre','Machine à laver','Quartier calme','Proche centre','Disponible immédiatement']],
        ['icon'=>'V','badge'=>'Exclusif','badge_class'=>'badge-exclu','price'=>'6 500 000 MAD','title'=>'Villa de Luxe Palmeraie','location'=>'Circuit de la Palmeraie','ref'=>'LC-2024-005','bedrooms'=>'5','bathrooms'=>'4','area'=>'450','land'=>'2000','land_label'=>'Terrain','desc'=>'Villa de prestige nichée dans la légendaire Palmeraie de Marrakech.','features'=>['Piscine à débordement','Pool house','Jardin tropical','Personnel de maison','Domotique','Hammam & spa','Suite parentale 60m²','Sécurité renforcée']],
        ['icon'=>'T','badge'=>'Vente','badge_class'=>'badge-vente','price'=>'750 000 MAD','title'=>'Terrain Constructible Targa','location'=>'Targa, Marrakech','ref'=>'LC-2024-006','bedrooms'=>'—','bathrooms'=>'—','area'=>'500','land'=>'R+2','land_label'=>'Autorisé','desc'=>'Terrain plat et bien situé dans le quartier recherché de Targa.','features'=>['Titre foncier','Autorisation R+2','Réseaux en bordure','Accès goudronné','Quartier résidentiel','Terrain plat','Environnement calme','Proche écoles']],
    ];
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
        'ref_service'  => ['label' => 'Type de service', 'type' => 'select', 'options' => ['Gestion de copropriété', 'Location', 'Achat & Vente', 'Suivi immobilier']],
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
        $refs[] = [
            'name'      => get_the_title(),
            'service'   => get_post_meta($id, '_lc_ref_service', true),
            'location'  => get_post_meta($id, '_lc_ref_location', true),
            'desc'      => get_post_meta($id, '_lc_ref_desc', true),
            'has_thumb' => has_post_thumbnail(),
            'thumb_url' => get_the_post_thumbnail_url($id, 'medium'),
        ];
    }
    wp_reset_postdata();
    return $refs;
}

/* ── FLUSH REWRITE ON ACTIVATION ── */
function lc_activate() {
    lc_register_properties_cpt();
    lc_register_references_cpt();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'lc_activate');
