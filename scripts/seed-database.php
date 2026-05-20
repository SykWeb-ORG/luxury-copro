<?php
defined('ABSPATH') || exit;

$seed_version = '2026-05-20.1';
$seed_site_url = rtrim(getenv('SEED_SITE_URL') ?: 'http://localhost:8080', '/');
$seed_admin_email = getenv('SEED_ADMIN_EMAIL') ?: 'dev@luxury-copro.local';

function lc_seed_log($message) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log($message);
        return;
    }

    echo $message . PHP_EOL;
}

function lc_seed_upsert_post(array $post_data, array $meta = []) {
    $post_type = $post_data['post_type'] ?? 'post';
    $slug = $post_data['post_name'] ?? '';
    $existing = null;

    if ($slug !== '') {
        $existing = get_page_by_path($slug, OBJECT, $post_type);
    }

    if ($existing instanceof WP_Post) {
        $post_data['ID'] = $existing->ID;
    }

    $post_data['post_status'] = $post_data['post_status'] ?? 'publish';
    $post_data['post_author'] = $post_data['post_author'] ?? get_current_user_id();

    $post_id = wp_insert_post(wp_slash($post_data), true);

    if (is_wp_error($post_id)) {
        lc_seed_log(sprintf('Unable to seed "%s": %s', $post_data['post_title'] ?? $slug, $post_id->get_error_message()));
        return 0;
    }

    foreach ($meta as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    return (int) $post_id;
}

function lc_seed_delete_post_by_path($path, $post_type) {
    $post = get_page_by_path($path, OBJECT, $post_type);

    if ($post instanceof WP_Post) {
        wp_delete_post($post->ID, true);
    }
}

function lc_seed_delete_default_content() {
    lc_seed_delete_post_by_path('hello-world', 'post');
    lc_seed_delete_post_by_path('sample-page', 'page');
    lc_seed_delete_post_by_path('privacy-policy', 'page');

    $auto_drafts = get_posts([
        'post_type' => 'post',
        'post_status' => 'auto-draft',
        'numberposts' => -1,
        'fields' => 'ids',
    ]);

    foreach ($auto_drafts as $post_id) {
        wp_delete_post($post_id, true);
    }
}

function lc_seed_apply_options($seed_site_url, $seed_admin_email) {
    update_option('blogname', 'Luxury Copro');
    update_option('blogdescription', 'Gestion de copropriete et immobilier a Marrakech.');
    update_option('admin_email', $seed_admin_email);
    update_option('timezone_string', 'Africa/Casablanca');
    update_option('date_format', 'j F Y');
    update_option('time_format', 'G\hi');
    update_option('WPLANG', 'fr_FR');
    update_option('siteurl', $seed_site_url);
    update_option('home', $seed_site_url);
}

function lc_seed_apply_theme_mods() {
    $theme_mods = [
        'lc_about_visible' => true,
        'lc_refs_visible' => true,
        'lc_biens_visible' => true,
        'lc_phone1' => '07 00 72 71 65',
        'lc_phone2' => '06 53 64 83 82',
        'lc_whatsapp' => '212700727165',
        'lc_email' => 'contact@luxury-copro.local',
        'lc_address_1' => 'Mg Rdc Imm A, Residence Amira',
        'lc_address_2' => 'Avenue 4eme DMM, Camp El Ghoul',
        'lc_city' => 'Marrakech',
        'lc_maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3396.5!2d-8.0135!3d31.6305!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xdafee8d985a5bef%3A0x1c3e4a3b8e9f1d2a!2sResidence%20Amira%2C%20Avenue%204eme%20DMM%2C%20Camp%20El%20Ghoul%2C%20Marrakech!5e0!3m2!1sfr!2sma!4v1700000000000',
        'lc_rc' => '138059',
        'lc_tp' => '64261180',
        'lc_if' => '53830046',
        'lc_cnss' => '4910963',
        'lc_ice' => '003295207000042',
        'lc_hero_tag' => 'Copropriete & Immobilier - Marrakech',
        'lc_hero_title' => 'Votre Patrimoine,<br><em>Notre</em><br><span class="stroke">Expertise</span>',
        'lc_hero_desc' => 'Gestion de copropriete, location, achat et vente de biens immobiliers. Un accompagnement professionnel et transparent a Marrakech.',
        'lc_hero_btn1' => 'Voir Nos Biens',
        'lc_hero_btn2' => 'Nos Services',
        'lc_about_label' => 'Qui Sommes-Nous',
        'lc_about_title' => 'Notre <span style="color:var(--gold)">Societe</span>',
        'lc_about_p1' => 'Notre societe accompagne les coproprietaires, investisseurs et familles dans la gestion, la location, l\'achat et la vente de biens immobiliers a Marrakech.',
        'lc_about_p2' => 'Nous privilegions une gestion claire, reactive et durable, avec un suivi administratif, technique et commercial adapte a chaque residence.',
        'lc_refs_label' => 'References',
        'lc_refs_title' => 'Ils nous font <span style="color:var(--gold)">confiance</span>',
        'lc_refs_intro' => 'Un apercu de residences et clients accompagnes par Luxury Copro sur Marrakech et sa region.',
        'lc_biens_label' => 'Notre Portefeuille',
        'lc_biens_title' => 'Nos Biens<br><span style="color:var(--gold)">Disponibles</span>',
        'lc_biens_title_fallback' => 'Exemples de Biens<br><span style="color:var(--gold)">Disponibles</span>',
        'lc_biens_desc' => 'Decouvrez une selection de biens types pour visualiser le rendu front et lancer le projet avec une base concrete.',
        'lc_biens_desc_fallback' => 'Les biens presentes ci-dessous sont des exemples illustratifs. Pour consulter les offres reelles et actualisees, veuillez nous contacter directement.',
        'lc_srv1_title' => 'Gestion de Copropriete',
        'lc_srv1_desc' => 'Gestion administrative, technique et financiere des coproprietes. Suivi des charges, budgets et assemblees generales.',
        'lc_srv2_title' => 'Location',
        'lc_srv2_desc' => 'Mise en location, selection des locataires, suivi des contrats et gestion quotidienne de vos biens locatifs.',
        'lc_srv3_title' => 'Achat & Vente',
        'lc_srv3_desc' => 'Accompagnement personnalise pour l\'achat et la vente. Valorisation, promotion et assistance jusqu\'a la finalisation.',
        'lc_srv4_title' => 'Travaux & Maintenance',
        'lc_srv4_desc' => 'Entretien des equipements communs, suivi des prestataires et travaux techniques divers pour vos residences.',
    ];

    foreach ($theme_mods as $key => $value) {
        set_theme_mod($key, $value);
    }
}

function lc_seed_pages() {
    $privacy_content = <<<HTML
<!-- wp:paragraph -->
<p>Cette version locale contient des donnees de demonstration pour faciliter le developpement du theme et des contenus de Luxury Copro.</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Donnees collectees</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Les formulaires et interactions presentes sur cette instance de developpement ne doivent pas etre utilises pour des donnees reelles. Utilisez uniquement des donnees de test.</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Usage interne</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Cette page existe pour eviter les liens morts dans le footer et fournir un point de depart coherent aux developpeurs qui cloneraient le projet.</p>
<!-- /wp:paragraph -->
HTML;

    $legal_content = <<<HTML
<!-- wp:paragraph -->
<p><strong>Luxury Copro</strong><br>Residence Amira, Camp El Ghoul<br>Marrakech, Maroc</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Activite</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Gestion de copropriete, travaux techniques divers et accompagnement immobilier.</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Contact</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Telephone : 07 00 72 71 65<br>Email : contact@luxury-copro.local</p>
<!-- /wp:paragraph -->
HTML;

    $privacy_id = lc_seed_upsert_post([
        'post_type' => 'page',
        'post_title' => 'Politique de confidentialite',
        'post_name' => 'politique-de-confidentialite',
        'post_content' => $privacy_content,
    ]);

    $legal_id = lc_seed_upsert_post([
        'post_type' => 'page',
        'post_title' => 'Mentions legales',
        'post_name' => 'mentions-legales',
        'post_content' => $legal_content,
    ]);

    if ($privacy_id > 0) {
        update_option('wp_page_for_privacy_policy', $privacy_id);
    }

    return [$privacy_id, $legal_id];
}

function lc_seed_properties() {
    $properties = [
        [
            'title' => 'Villa Contemporaine avec Piscine',
            'slug' => 'villa-contemporaine-avec-piscine',
            'type' => 'Exclusif',
            'price' => '3 200 000 MAD',
            'location' => 'Route de l\'Ourika, Marrakech',
            'ref' => 'LC-2024-001',
            'bedrooms' => '4',
            'bathrooms' => '3',
            'area' => '320',
            'land' => '800',
            'land_label' => 'Terrain',
            'desc' => 'Magnifique villa contemporaine situee dans un quartier residentiel prise de Marrakech.',
            'features' => [
                'Piscine privee',
                'Jardin paysager',
                'Garage double',
                'Climatisation centrale',
                'Cuisine equipee',
                'Vue sur l\'Atlas',
                'Securite 24h/24',
                'Proche commodites',
            ],
        ],
        [
            'title' => 'Appartement Standing Gueliz',
            'slug' => 'appartement-standing-gueliz',
            'type' => 'Vente',
            'price' => '1 450 000 MAD',
            'location' => 'Av. Mohammed V, Gueliz',
            'ref' => 'LC-2024-002',
            'bedrooms' => '3',
            'bathrooms' => '2',
            'area' => '140',
            'land' => '3e',
            'land_label' => 'Etage',
            'desc' => 'Superbe appartement de standing au coeur de Gueliz.',
            'features' => [
                'Balcon terrasse',
                'Ascenseur',
                'Parking sous-sol',
                'Residence securisee',
                'Double vitrage',
                'Parquet massif',
                'Cuisine americaine',
                'Proche tramway',
            ],
        ],
        [
            'title' => 'Riad Renove Medina',
            'slug' => 'riad-renove-medina',
            'type' => 'Vente',
            'price' => '4 800 000 MAD',
            'location' => 'Derb Jdid, Medina',
            'ref' => 'LC-2024-003',
            'bedrooms' => '5',
            'bathrooms' => '5',
            'area' => '280',
            'land' => 'Patio',
            'land_label' => '+ Terrasse',
            'desc' => 'Riad d\'exception entierement renove dans les regles de l\'art.',
            'features' => [
                'Patio avec fontaine',
                'Terrasse panoramique',
                'Zellige traditionnel',
                'Hammam prive',
                'Cuisine marocaine',
                'Vue Koutoubia',
                'Bois de cedre sculpte',
                'Rentabilite locative',
            ],
        ],
        [
            'title' => 'Duplex Meuble Hivernage',
            'slug' => 'duplex-meuble-hivernage',
            'type' => 'Location',
            'price' => '8 500 MAD /mois',
            'location' => 'Av. Echouhada, Hivernage',
            'ref' => 'LC-2024-004',
            'bedrooms' => '2',
            'bathrooms' => '2',
            'area' => '110',
            'land' => 'Meuble',
            'land_label' => 'Equipe',
            'desc' => 'Duplex entierement meuble et equipe dans le quartier prestigieux de l\'Hivernage.',
            'features' => [
                'Entierement meuble',
                'Piscine residence',
                'Climatisation',
                'Internet fibre',
                'Machine a laver',
                'Quartier calme',
                'Proche centre',
                'Disponible immediatement',
            ],
        ],
        [
            'title' => 'Villa de Luxe Palmeraie',
            'slug' => 'villa-de-luxe-palmeraie',
            'type' => 'Exclusif',
            'price' => '6 500 000 MAD',
            'location' => 'Circuit de la Palmeraie',
            'ref' => 'LC-2024-005',
            'bedrooms' => '5',
            'bathrooms' => '4',
            'area' => '450',
            'land' => '2000',
            'land_label' => 'Terrain',
            'desc' => 'Villa de prestige nichee dans la legendaire Palmeraie de Marrakech.',
            'features' => [
                'Piscine a debordement',
                'Pool house',
                'Jardin tropical',
                'Personnel de maison',
                'Domotique',
                'Hammam & spa',
                'Suite parentale 60m2',
                'Securite renforcee',
            ],
        ],
        [
            'title' => 'Terrain Constructible Targa',
            'slug' => 'terrain-constructible-targa',
            'type' => 'Vente',
            'price' => '750 000 MAD',
            'location' => 'Targa, Marrakech',
            'ref' => 'LC-2024-006',
            'bedrooms' => '-',
            'bathrooms' => '-',
            'area' => '500',
            'land' => 'R+2',
            'land_label' => 'Autorise',
            'desc' => 'Terrain plat et bien situe dans le quartier recherche de Targa.',
            'features' => [
                'Titre foncier',
                'Autorisation R+2',
                'Reseaux en bordure',
                'Acces goudronne',
                'Quartier residentiel',
                'Terrain plat',
                'Environnement calme',
                'Proche ecoles',
            ],
        ],
    ];

    foreach ($properties as $property) {
        lc_seed_upsert_post(
            [
                'post_type' => 'property',
                'post_title' => $property['title'],
                'post_name' => $property['slug'],
                'post_content' => $property['desc'],
            ],
            [
                '_lc_price' => $property['price'],
                '_lc_location' => $property['location'],
                '_lc_ref' => $property['ref'],
                '_lc_type' => $property['type'],
                '_lc_bedrooms' => $property['bedrooms'],
                '_lc_bathrooms' => $property['bathrooms'],
                '_lc_area' => $property['area'],
                '_lc_land' => $property['land'],
                '_lc_land_label' => $property['land_label'],
                '_lc_features' => implode("\n", $property['features']),
            ]
        );
    }
}

function lc_seed_references() {
    $references = [
        [
            'title' => 'Residence Amira',
            'slug' => 'residence-amira',
            'service' => 'Gestion de copropriete',
            'location' => 'Camp El Ghoul, Marrakech',
            'desc' => 'Gestion complete de la copropriete : charges, assemblees generales, entretien des parties communes et suivi des prestataires.',
            'order' => '1',
        ],
        [
            'title' => 'Residence Al Baraka',
            'slug' => 'residence-al-baraka',
            'service' => 'Gestion de copropriete',
            'location' => 'Gueliz, Marrakech',
            'desc' => 'Administration et suivi technique d\'une residence de 48 appartements avec piscine et espaces verts.',
            'order' => '2',
        ],
        [
            'title' => 'M. Benjelloun',
            'slug' => 'm-benjelloun',
            'service' => 'Achat & Vente',
            'location' => 'Palmeraie, Marrakech',
            'desc' => 'Accompagnement a l\'acquisition d\'une villa de prestige, de la recherche a la finalisation notariale.',
            'order' => '3',
        ],
        [
            'title' => 'Residence Les Jardins de l\'Atlas',
            'slug' => 'residence-les-jardins-de-l-atlas',
            'service' => 'Suivi immobilier',
            'location' => 'Route de l\'Ourika, Marrakech',
            'desc' => 'Suivi et valorisation d\'un complexe residentiel de standing avec reporting mensuel aux proprietaires.',
            'order' => '4',
        ],
        [
            'title' => 'Groupe Invest Riad',
            'slug' => 'groupe-invest-riad',
            'service' => 'Location',
            'location' => 'Medina, Marrakech',
            'desc' => 'Gestion locative de 3 riads touristiques : selection des locataires, contrats et suivi quotidien.',
            'order' => '5',
        ],
        [
            'title' => 'Residence Nour',
            'slug' => 'residence-nour',
            'service' => 'Gestion de copropriete',
            'location' => 'Hivernage, Marrakech',
            'desc' => 'Prise en charge de la copropriete d\'une residence haut standing de 32 unites avec conciergerie.',
            'order' => '6',
        ],
    ];

    foreach ($references as $reference) {
        lc_seed_upsert_post(
            [
                'post_type' => 'reference',
                'post_title' => $reference['title'],
                'post_name' => $reference['slug'],
            ],
            [
                '_lc_ref_service' => $reference['service'],
                '_lc_ref_location' => $reference['location'],
                '_lc_ref_desc' => $reference['desc'],
                '_lc_ref_order' => $reference['order'],
                '_lc_ref_active' => '1',
            ]
        );
    }
}

lc_seed_log('Applying local development seed...');

lc_seed_delete_default_content();
lc_seed_apply_options($seed_site_url, $seed_admin_email);
lc_seed_apply_theme_mods();
lc_seed_pages();
lc_seed_properties();
lc_seed_references();

update_option('luxury_copro_seed_version', $seed_version);
flush_rewrite_rules();

lc_seed_log(sprintf('Luxury Copro seed %s applied.', $seed_version));
