<?php
defined('ABSPATH') || exit;

$seed_version = '2026-05-20.2';
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
    update_option('blogdescription', 'Syndic de copropriété et immobilier a Marrakech.');
    update_option('admin_email', $seed_admin_email);
    update_option('timezone_string', 'Africa/Casablanca');
    update_option('date_format', 'j F Y');
    update_option('time_format', 'G\hi');
    update_option('WPLANG', 'fr_FR');
    update_option('siteurl', $seed_site_url);
    update_option('home', $seed_site_url);
}

function lc_seed_theme_mods() {
    return [
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
        'lc_hero_desc' => 'Syndic de copropriété, location, achat et vente de biens immobiliers. Un accompagnement professionnel et transparent a Marrakech.',
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
        'lc_srv1_title' => 'Syndic de copropriété',
        'lc_srv1_desc' => 'Gestion administrative, technique et financiere des coproprietes. Suivi des charges, budgets et assemblees generales.',
        'lc_srv2_title' => 'Location',
        'lc_srv2_desc' => 'Mise en location, selection des locataires, suivi des contrats et gestion quotidienne de vos biens locatifs.',
        'lc_srv3_title' => 'Achat & Vente',
        'lc_srv3_desc' => 'Accompagnement personnalise pour l\'achat et la vente. Valorisation, promotion et assistance jusqu\'a la finalisation.',
        'lc_srv4_title' => 'Travaux & Maintenance',
        'lc_srv4_desc' => 'Entretien des equipements communs, suivi des prestataires et travaux techniques divers pour vos residences.',
    ];
}

function lc_seed_apply_theme_mods() {
    foreach (lc_seed_theme_mods() as $key => $value) {
        set_theme_mod($key, $value);
    }
}

function lc_seed_polylang_available() {
    return function_exists('PLL') && function_exists('pll_set_post_language') && function_exists('pll_save_post_translations');
}

function lc_seed_polylang_languages() {
    if (!function_exists('PLL')) {
        lc_seed_log('Polylang is not active; skipping multilingual seed.');
        return false;
    }

    $languages = [
        [
            'name' => 'Francais',
            'slug' => 'fr',
            'locale' => 'fr_FR',
            'rtl' => false,
            'flag' => 'fr',
            'term_group' => 0,
        ],
        [
            'name' => 'English',
            'slug' => 'en',
            'locale' => 'en_US',
            'rtl' => false,
            'flag' => 'us',
            'term_group' => 1,
        ],
    ];

    foreach ($languages as $language) {
        if (PLL()->model->get_language($language['slug'])) {
            continue;
        }

        $result = PLL()->model->add_language($language);

        if (is_wp_error($result)) {
            lc_seed_log(sprintf('Unable to add Polylang language "%s": %s', $language['slug'], $result->get_error_message()));
            return false;
        }
    }

    $options = get_option('polylang', []);
    if (!is_array($options)) {
        $options = [];
    }

    $options['default_lang'] = 'fr';
    $options['force_lang'] = 1;
    $options['hide_default'] = true;
    $options['rewrite'] = true;
    $options['redirect_lang'] = false;
    $options['browser'] = false;
    $options['media_support'] = true;

    update_option('polylang', $options);

    if (PLL()->model->get_language('fr')) {
        PLL()->model->update_default_lang('fr');
    }

    delete_option('rewrite_rules');

    return true;
}

function lc_seed_english_translations() {
    $theme_mods = lc_seed_theme_mods();

    $translations = [
        $theme_mods['lc_address_1'] => 'Ground floor shop, Building A, Residence Amira',
        $theme_mods['lc_address_2'] => '4th DMM Avenue, Camp El Ghoul',
        $theme_mods['lc_city'] => 'Marrakech',
        $theme_mods['lc_hero_tag'] => 'Property Management & Real Estate - Marrakech',
        $theme_mods['lc_hero_title'] => 'Your Property,<br><em>Our</em><br><span class="stroke">Expertise</span>',
        $theme_mods['lc_hero_desc'] => 'Condominium management, rental, buying and selling real estate. Professional and transparent support in Marrakech.',
        $theme_mods['lc_hero_btn1'] => 'View Our Properties',
        $theme_mods['lc_hero_btn2'] => 'Our Services',
        $theme_mods['lc_about_label'] => 'About Us',
        $theme_mods['lc_about_title'] => 'Our <span style="color:var(--gold)">Company</span>',
        $theme_mods['lc_about_p1'] => 'Our company supports co-owners, investors and families with property management, rental, buying and selling real estate in Marrakech.',
        $theme_mods['lc_about_p2'] => 'We prioritize clear, responsive and sustainable management, with administrative, technical and commercial follow-up tailored to each residence.',
        $theme_mods['lc_refs_label'] => 'References',
        $theme_mods['lc_refs_title'] => 'They <span style="color:var(--gold)">trust us</span>',
        $theme_mods['lc_refs_intro'] => 'A selection of residences and clients supported by Luxury Copro in Marrakech and the surrounding region.',
        $theme_mods['lc_biens_label'] => 'Our Portfolio',
        $theme_mods['lc_biens_title'] => 'Our Properties<br><span style="color:var(--gold)">Available</span>',
        $theme_mods['lc_biens_title_fallback'] => 'Sample Properties<br><span style="color:var(--gold)">Available</span>',
        $theme_mods['lc_biens_desc'] => 'Discover a selection of sample properties to preview the front-end rendering and start the project with concrete content.',
        $theme_mods['lc_biens_desc_fallback'] => 'The properties shown below are illustrative examples. To view real and up-to-date listings, please contact us directly.',
        $theme_mods['lc_srv1_title'] => 'Condominium Management',
        $theme_mods['lc_srv1_desc'] => 'Administrative, technical and financial management of condominiums. Monitoring of fees, budgets and general meetings.',
        $theme_mods['lc_srv2_title'] => 'Rental',
        $theme_mods['lc_srv2_desc'] => 'Rental listing, tenant selection, contract follow-up and day-to-day management of your rental properties.',
        $theme_mods['lc_srv3_title'] => 'Buying & Selling',
        $theme_mods['lc_srv3_desc'] => 'Personalized support for buying and selling. Valuation, promotion and assistance through completion.',
        $theme_mods['lc_srv4_title'] => 'Works & Maintenance',
        $theme_mods['lc_srv4_desc'] => 'Maintenance of shared equipment, provider coordination and various technical works for your residences.',
    ];

    return array_replace($translations, [
        'Accueil' => 'Home',
        'À Propos' => 'About',
        'Nos Biens' => 'Our Properties',
        'Services' => 'Services',
        'Engagements' => 'Commitments',
        'Nous Contacter' => 'Contact Us',
        'Changer le thème' => 'Change theme',
        'Basculer entre mode clair et sombre' => 'Switch between light and dark mode',
        'Ouvrir le menu' => 'Open menu',
        'Fermer le menu' => 'Close menu',
        'Retour en haut' => 'Back to top',
        'Choisir la langue' => 'Choose language',
        'Page introuvable' => 'Page not found',
        'La page que vous recherchez n\'existe pas ou a été déplacée.' => 'The page you are looking for does not exist or has been moved.',
        'Retour à l\'accueil' => 'Back to home',
        'En ligne' => 'Online',
        'Bonjour ! Bienvenue chez LUXURY COPRO. Comment puis-je vous aider dans votre projet immobilier ou de copropriété ?' => 'Hello! Welcome to Luxury Copro. How can I help with your real estate or condominium project?',
        'Maintenant' => 'Now',
        'Tapez un message...' => 'Type a message...',
        'Envoyer' => 'Send',
        'Ouvrir WhatsApp' => 'Open WhatsApp',
        'Syndic de copropriété — Travaux Techniques Divers — Intérim — Immobilier. Votre partenaire de confiance à' => 'Condominium Management - Technical Works - Interim Services - Real Estate. Your trusted partner in',
        'Navigation' => 'Navigation',
        'Contact' => 'Contact',
        'Tous droits réservés.' => 'All rights reserved.',
        'Politique de Confidentialité' => 'Privacy Policy',
        'Mentions Légales' => 'Legal Notice',
        'Menu principal' => 'Main menu',
        'Menu mobile' => 'Mobile menu',
        'Biens Immobiliers' => 'Real Estate Properties',
        'Bien' => 'Property',
        'Ajouter un bien' => 'Add property',
        'Ajouter un nouveau bien' => 'Add new property',
        'Modifier le bien' => 'Edit property',
        'Voir le bien' => 'View property',
        'Tous les biens' => 'All properties',
        'Rechercher un bien' => 'Search property',
        'Aucun bien trouvé' => 'No property found',
        'Aucun bien dans la corbeille' => 'No property in trash',
        'Biens' => 'Properties',
        'Détails du bien' => 'Property details',
        'Coordonnées' => 'Contact details',
        'Informations légales' => 'Legal information',
        'Section Hero' => 'Hero section',
        'Notre Société' => 'Our Company',
        'Nos Références' => 'Our References',
        'Références' => 'References',
        'Référence' => 'Reference',
        'Ajouter une référence' => 'Add reference',
        'Ajouter une nouvelle référence' => 'Add new reference',
        'Modifier la référence' => 'Edit reference',
        'Voir la référence' => 'View reference',
        'Toutes les références' => 'All references',
        'Rechercher une référence' => 'Search reference',
        'Aucune référence trouvée' => 'No reference found',
        'Aucune référence dans la corbeille' => 'No reference in trash',
        'Détails de la référence' => 'Reference details',
        'Biens Gérés' => 'Managed Properties',
        'Clients Satisfaits' => 'Satisfied Clients',
        'Ans d\'Expérience' => 'Years of Experience',
        'Tout' => 'All',
        'Vente' => 'Sale',
        'Location' => 'Rental',
        'Exclusif' => 'Exclusive',
        'Chambres' => 'Bedrooms',
        'Nos Services' => 'Our Services',
        'Un Accompagnement' => 'Complete',
        'Complet' => 'Support',
        'Syndic de copropriété' => 'Condominium Management',
        'Nous assurons une gestion administrative, technique et financière des copropriétaires afin de garantir le bon fonctionnement des parties communes et la préservation du patrimoine immobilier.' => 'We provide administrative, technical and financial management for co-owners to keep common areas running smoothly and preserve the value of the property.',
        'Le suivi administratif et réglementaire' => 'Administrative and regulatory follow-up',
        'L\'entretien et la maintenance des équipements communs' => 'Maintenance of shared equipment',
        'Le suivi des prestataires et des contrats' => 'Provider and contract follow-up',
        'La gestion des charges et des budgets' => 'Fee and budget management',
        'L\'accompagnement des copropriétaires' => 'Support for co-owners',
        'L\'organisation et le suivi des assemblées générales' => 'Organization and follow-up of general meetings',
        'Gestion des Biens Immobiliers' => 'Property Management',
        'Nous proposons des solutions adaptées à la gestion et la valorisation des biens immobiliers destinés à l\'habitation ou à l\'investissement.' => 'We provide tailored solutions for managing and enhancing residential or investment properties.',
        'Valorisation et promotion des biens' => 'Property valuation and promotion',
        'Assistance dans les démarches administratives et commerciales' => 'Administrative and commercial assistance',
        'Conseil et suivi, jusqu\'à la finalisation des opérations' => 'Advice and follow-up through completion',
        'Accompagnement personnalisé pour l\'achat et la vente de biens immobiliers' => 'Personalized support for buying and selling real estate',
        'Un service complet pour la mise en location et la gestion quotidienne de vos biens locatifs.' => 'A complete service for rental listing and day-to-day management of your rental properties.',
        'Mise en location des biens' => 'Rental listing',
        'Recherche et sélection des locataires' => 'Tenant search and selection',
        'Suivi administratif des contrats' => 'Administrative contract follow-up',
        'Gestion quotidienne des locataires, vente et achat' => 'Day-to-day tenant management, sale and purchase',
        'Achat & Vente' => 'Buying & Selling',
        'Un accompagnement de A à Z pour concrétiser votre projet immobilier en toute sérénité.' => 'End-to-end support to complete your real estate project with peace of mind.',
        'Copropriété' => 'Condominium',
        'Gestion Immobilière' => 'Property Management',
        'Travaux' => 'Works',
        'Nos Engagements' => 'Our Commitments',
        'Votre Partenaire' => 'Your Trusted',
        'de' => 'Real Estate',
        'Confiance' => 'Partner',
        'Transparence' => 'Transparency',
        'Professionnalisme et clarté dans toutes nos démarches et nos rapports.' => 'Professionalism and clarity in all our processes and reports.',
        'Qualité de Service' => 'Service Quality',
        'Des prestations rigoureuses et adaptées aux plus hautes exigences.' => 'Rigorous services tailored to the highest standards.',
        'Réactivité & Proximité' => 'Responsiveness & Proximity',
        'Une écoute attentive et des réponses rapides à chacune de vos demandes.' => 'Attentive listening and fast responses to every request.',
        'Respect des Délais' => 'Deadline Commitment',
        'Respect des engagements pris et des échéances convenues avec nos clients.' => 'Respect for commitments and deadlines agreed with our clients.',
        'Patrimoine Préservé' => 'Preserved Property Value',
        'Préservation et valorisation durable de votre patrimoine immobilier.' => 'Sustainable preservation and enhancement of your real estate assets.',
        'Notre Ambition' => 'Our Ambition',
        'Développer une relation de confiance durable avec nos clients en proposant des services modernes, fiables et adaptés aux exigences du secteur immobilier et du syndic de copropriété.' => 'Build lasting trust with our clients by providing modern, reliable services tailored to real estate and condominium management requirements.',
        'Société enregistrée' => 'Registered company',
        'Basés à' => 'Based in',
        'Domaines d\'activité' => 'Business areas',
        'Conformité' => 'Compliance',
        'Respect du cadre réglementaire' => 'Regulatory compliance',
        'Vous Avez un Projet' => 'Have a Real Estate Project',
        'Immobilier ?' => 'in Mind?',
        'Copropriété, location, achat ou vente — contactez-nous pour un accompagnement sur mesure.' => 'Condominium, rental, purchase or sale - contact us for tailored support.',
        'Appeler' => 'Call',
        'Parlons de Votre' => 'Let\'s Talk About Your',
        'Projet' => 'Project',
        'Notre Bureau' => 'Our Office',
        'Adresse' => 'Address',
        'Téléphone' => 'Phone',
        'Horaires' => 'Hours',
        'Lundi - Samedi : 9h00 - 19h00' => 'Monday - Saturday: 9:00 AM - 7:00 PM',
        'Contacter via WhatsApp' => 'Contact via WhatsApp',
        'Appelez-nous' => 'Call us',
        'Localisation Luxury Copro sur Google Maps' => 'Luxury Copro location on Google Maps',
        'Envoyez-nous un message' => 'Send us a message',
        'Décrivez votre projet et nous vous répondrons dans les plus brefs délais.' => 'Describe your project and we will reply as soon as possible.',
        'Formulaire de contact' => 'Contact form',
        'Nom Complet' => 'Full Name',
        'Votre nom' => 'Your name',
        'Veuillez entrer votre nom' => 'Please enter your name',
        'Veuillez entrer un numéro valide' => 'Please enter a valid number',
        'Type de Projet' => 'Project Type',
        'Syndic de copropriété' => 'Condominium management',
        'Je veux acheter' => 'I want to buy',
        'Je veux vendre' => 'I want to sell',
        'Je veux louer' => 'I want to rent',
        'Travaux & maintenance' => 'Works & maintenance',
        'Budget' => 'Budget',
        'Moins de 500 000 MAD' => 'Less than 500,000 MAD',
        'Plus de 3 000 000 MAD' => 'More than 3,000,000 MAD',
        'Message' => 'Message',
        'Décrivez votre projet immobilier...' => 'Describe your real estate project...',
        'Envoyer via WhatsApp' => 'Send via WhatsApp',
        'Envoyer par E-mail' => 'Send by Email',
        'Votre message sera envoyé directement sur notre WhatsApp ou par e-mail.' => 'Your message will be sent directly to our WhatsApp or by email.',
        'Fermer' => 'Close',
        'Description' => 'Description',
        'Planifier une Visite' => 'Schedule a Visit',
        'Salles de Bain' => 'Bathrooms',
        'Surface' => 'Area',
        'Luxury Copro' => 'Luxury Copro',
    ]);
}

function lc_seed_seeded_content_translations() {
    return [
        'Villa Contemporaine avec Piscine' => 'Contemporary Villa with Pool',
        'Appartement Standing Gueliz' => 'Premium Apartment in Gueliz',
        'Riad Renove Medina' => 'Renovated Riad in the Medina',
        'Duplex Meuble Hivernage' => 'Furnished Duplex in Hivernage',
        'Villa de Luxe Palmeraie' => 'Luxury Villa in Palmeraie',
        'Terrain Constructible Targa' => 'Buildable Plot in Targa',
        'Route de l\'Ourika, Marrakech' => 'Ourika Road, Marrakech',
        'Av. Mohammed V, Gueliz' => 'Mohammed V Avenue, Gueliz',
        'Derb Jdid, Medina' => 'Derb Jdid, Medina',
        'Av. Echouhada, Hivernage' => 'Echouhada Avenue, Hivernage',
        'Circuit de la Palmeraie' => 'Palmeraie Circuit',
        'Targa, Marrakech' => 'Targa, Marrakech',
        'Terrain' => 'Plot',
        'Etage' => 'Floor',
        'Equipe' => 'Equipped',
        'Autorise' => 'Approved',
        'Magnifique villa contemporaine situee dans un quartier residentiel prise de Marrakech.' => 'Beautiful contemporary villa located in a sought-after residential district of Marrakech.',
        'Superbe appartement de standing au coeur de Gueliz.' => 'Superb premium apartment in the heart of Gueliz.',
        'Riad d\'exception entierement renove dans les regles de l\'art.' => 'Exceptional riad fully renovated with authentic craftsmanship.',
        'Duplex entierement meuble et equipe dans le quartier prestigieux de l\'Hivernage.' => 'Fully furnished and equipped duplex in the prestigious Hivernage district.',
        'Villa de prestige nichee dans la legendaire Palmeraie de Marrakech.' => 'Prestige villa nestled in Marrakech\'s legendary Palmeraie.',
        'Terrain plat et bien situe dans le quartier recherche de Targa.' => 'Flat, well-located plot in the sought-after Targa district.',
        'Piscine privee' => 'Private pool',
        'Jardin paysager' => 'Landscaped garden',
        'Garage double' => 'Double garage',
        'Climatisation centrale' => 'Central air conditioning',
        'Cuisine equipee' => 'Equipped kitchen',
        'Vue sur l\'Atlas' => 'Atlas Mountains view',
        'Securite 24h/24' => '24/7 security',
        'Proche commodites' => 'Close to amenities',
        'Balcon terrasse' => 'Balcony terrace',
        'Ascenseur' => 'Elevator',
        'Parking sous-sol' => 'Underground parking',
        'Residence securisee' => 'Secure residence',
        'Double vitrage' => 'Double glazing',
        'Parquet massif' => 'Solid wood flooring',
        'Cuisine americaine' => 'Open-plan kitchen',
        'Proche tramway' => 'Close to transport',
        'Patio avec fontaine' => 'Patio with fountain',
        'Terrasse panoramique' => 'Panoramic terrace',
        'Zellige traditionnel' => 'Traditional zellige',
        'Hammam prive' => 'Private hammam',
        'Cuisine marocaine' => 'Moroccan kitchen',
        'Vue Koutoubia' => 'Koutoubia view',
        'Bois de cedre sculpte' => 'Carved cedar wood',
        'Rentabilite locative' => 'Rental yield',
        'Entierement meuble' => 'Fully furnished',
        'Piscine residence' => 'Residence pool',
        'Climatisation' => 'Air conditioning',
        'Internet fibre' => 'Fiber internet',
        'Machine a laver' => 'Washing machine',
        'Quartier calme' => 'Quiet neighborhood',
        'Proche centre' => 'Close to the center',
        'Disponible immediatement' => 'Available immediately',
        'Piscine a debordement' => 'Infinity pool',
        'Pool house' => 'Pool house',
        'Jardin tropical' => 'Tropical garden',
        'Personnel de maison' => 'House staff',
        'Domotique' => 'Home automation',
        'Hammam & spa' => 'Hammam & spa',
        'Suite parentale 60m2' => '60 sqm master suite',
        'Securite renforcee' => 'Enhanced security',
        'Titre foncier' => 'Land title',
        'Autorisation R+2' => 'R+2 building authorization',
        'Reseaux en bordure' => 'Utilities at plot edge',
        'Acces goudronne' => 'Paved access',
        'Quartier residentiel' => 'Residential neighborhood',
        'Terrain plat' => 'Flat plot',
        'Environnement calme' => 'Quiet environment',
        'Proche ecoles' => 'Close to schools',
        'Residence Amira' => 'Residence Amira',
        'Residence Al Baraka' => 'Residence Al Baraka',
        'M. Benjelloun' => 'Mr. Benjelloun',
        'Residence Les Jardins de l\'Atlas' => 'Residence Les Jardins de l\'Atlas',
        'Groupe Invest Riad' => 'Groupe Invest Riad',
        'Residence Nour' => 'Residence Nour',
        'Camp El Ghoul, Marrakech' => 'Camp El Ghoul, Marrakech',
        'Gueliz, Marrakech' => 'Gueliz, Marrakech',
        'Palmeraie, Marrakech' => 'Palmeraie, Marrakech',
        'Medina, Marrakech' => 'Medina, Marrakech',
        'Hivernage, Marrakech' => 'Hivernage, Marrakech',
        'Suivi immobilier' => 'Real estate follow-up',
        'Gestion complete de la copropriete : charges, assemblees generales, entretien des parties communes et suivi des prestataires.' => 'Complete condominium management: fees, general meetings, common-area maintenance and provider follow-up.',
        'Administration et suivi technique d\'une residence de 48 appartements avec piscine et espaces verts.' => 'Administration and technical follow-up for a 48-apartment residence with pool and green spaces.',
        'Accompagnement a l\'acquisition d\'une villa de prestige, de la recherche a la finalisation notariale.' => 'Support for acquiring a prestige villa, from search to notarial completion.',
        'Route de l\'Ourika, Marrakech' => 'Ourika Road, Marrakech',
        'Suivi et valorisation d\'un complexe residentiel de standing avec reporting mensuel aux proprietaires.' => 'Follow-up and value enhancement for a premium residential complex with monthly owner reporting.',
        'Gestion locative de 3 riads touristiques : selection des locataires, contrats et suivi quotidien.' => 'Rental management for three tourist riads: tenant selection, contracts and daily follow-up.',
        'Prise en charge de la copropriete d\'une residence haut standing de 32 unites avec conciergerie.' => 'Condominium management for a high-end 32-unit residence with concierge service.',
    ];
}

function lc_seed_polylang_string_translations() {
    $english = get_term_by('slug', 'en', 'language');

    if (!$english instanceof WP_Term) {
        lc_seed_log('English language is missing; skipping Polylang string translations.');
        return;
    }

    $existing = get_term_meta($english->term_id, '_pll_strings_translations', true);
    $strings = [];

    if (is_array($existing)) {
        foreach ($existing as $entry) {
            if (!is_array($entry) || count($entry) < 2 || $entry[0] === '') {
                continue;
            }

            $strings[(string) $entry[0]] = (string) $entry[1];
        }
    }

    foreach (array_replace(lc_seed_english_translations(), lc_seed_seeded_content_translations()) as $source => $translation) {
        if ($source === '' || $translation === '') {
            continue;
        }

        $strings[$source] = $translation;
    }

    $pairs = [];
    foreach ($strings as $source => $translation) {
        $pairs[] = [$source, $translation];
    }

    update_term_meta($english->term_id, '_pll_strings_translations', wp_slash($pairs));
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
<p>Syndic de copropriété, travaux techniques divers et accompagnement immobilier.</p>
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

function lc_seed_polylang_page_translations(array $page_ids) {
    if (!lc_seed_polylang_available()) {
        return;
    }

    [$privacy_id, $legal_id] = $page_ids;

    $privacy_content_en = <<<HTML
<!-- wp:paragraph -->
<p>This local version contains demo data to make development of the Luxury Copro theme and content easier.</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Collected data</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>The forms and interactions on this development instance must not be used with real data. Use test data only.</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Internal use</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>This page exists to avoid dead links in the footer and to provide a coherent starting point for developers cloning the project.</p>
<!-- /wp:paragraph -->
HTML;

    $legal_content_en = <<<HTML
<!-- wp:paragraph -->
<p><strong>Luxury Copro</strong><br>Residence Amira, Camp El Ghoul<br>Marrakech, Morocco</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Business activity</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Condominium management, miscellaneous technical works and real estate support.</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Contact</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Phone: 07 00 72 71 65<br>Email: contact@luxury-copro.local</p>
<!-- /wp:paragraph -->
HTML;

    $privacy_en_id = lc_seed_upsert_post([
        'post_type' => 'page',
        'post_title' => 'Privacy Policy',
        'post_name' => 'privacy-policy',
        'post_content' => $privacy_content_en,
    ]);

    $legal_en_id = lc_seed_upsert_post([
        'post_type' => 'page',
        'post_title' => 'Legal Notice',
        'post_name' => 'legal-notice',
        'post_content' => $legal_content_en,
    ]);

    if ($privacy_id > 0 && $privacy_en_id > 0) {
        pll_set_post_language($privacy_id, 'fr');
        pll_set_post_language($privacy_en_id, 'en');
        pll_save_post_translations(['fr' => $privacy_id, 'en' => $privacy_en_id]);
    }

    if ($legal_id > 0 && $legal_en_id > 0) {
        pll_set_post_language($legal_id, 'fr');
        pll_set_post_language($legal_en_id, 'en');
        pll_save_post_translations(['fr' => $legal_id, 'en' => $legal_en_id]);
    }
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
            'service' => 'Syndic de copropriété',
            'location' => 'Camp El Ghoul, Marrakech',
            'desc' => 'Gestion complete de la copropriete : charges, assemblees generales, entretien des parties communes et suivi des prestataires.',
            'order' => '1',
        ],
        [
            'title' => 'Residence Al Baraka',
            'slug' => 'residence-al-baraka',
            'service' => 'Syndic de copropriété',
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
            'service' => 'Syndic de copropriété',
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
lc_seed_polylang_languages();
lc_seed_apply_theme_mods();
$page_ids = lc_seed_pages();
lc_seed_polylang_page_translations($page_ids);
lc_seed_properties();
lc_seed_references();
lc_seed_polylang_string_translations();

update_option('luxury_copro_seed_version', $seed_version);
flush_rewrite_rules();

lc_seed_log(sprintf('Luxury Copro seed %s applied.', $seed_version));
