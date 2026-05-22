<?php
defined('ABSPATH') || exit;

const LC_PROD_POLYLANG_SEED_VERSION = '2026-05-22.1';

function lc_prod_polylang_log($message) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::log($message);
        return;
    }

    echo $message . PHP_EOL;
}

function lc_prod_polylang_error($message) {
    if (defined('WP_CLI') && WP_CLI) {
        WP_CLI::error($message);
    }

    wp_die(esc_html($message));
}

function lc_prod_polylang_env_bool($name, $default) {
    $value = getenv($name);

    if ($value === false || $value === '') {
        return (bool) $default;
    }

    return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
}

function lc_prod_polylang_language_definitions() {
    return [
        'fr' => [
            'name' => 'Français',
            'slug' => 'fr',
            'locale' => 'fr_FR',
            'rtl' => false,
            'flag' => 'fr',
            'term_group' => 0,
        ],
        'en' => [
            'name' => 'English',
            'slug' => 'en',
            'locale' => 'en_US',
            'rtl' => false,
            'flag' => 'us',
            'term_group' => 1,
        ],
    ];
}

function lc_prod_polylang_requested_languages($default_lang) {
    $raw = getenv('LC_POLYLANG_LANGUAGES') ?: 'fr,en';
    $slugs = array_filter(array_map('trim', explode(',', strtolower((string) $raw))));
    array_unshift($slugs, strtolower((string) $default_lang));

    return array_values(array_unique($slugs));
}

function lc_prod_polylang_ensure_languages(array $requested_languages, $default_lang) {
    $definitions = lc_prod_polylang_language_definitions();

    foreach ($requested_languages as $slug) {
        if (!isset($definitions[$slug])) {
            lc_prod_polylang_error(sprintf(
                'Unsupported LC_POLYLANG_LANGUAGES value "%s". Supported languages: %s.',
                $slug,
                implode(', ', array_keys($definitions))
            ));
        }

        if (PLL()->model->get_language($slug)) {
            continue;
        }

        $result = PLL()->model->add_language($definitions[$slug]);

        if (is_wp_error($result)) {
            lc_prod_polylang_error(sprintf('Unable to add Polylang language "%s": %s', $slug, $result->get_error_message()));
        }

        lc_prod_polylang_log(sprintf('Added Polylang language: %s', $slug));
    }

    if (!PLL()->model->get_language($default_lang)) {
        lc_prod_polylang_error(sprintf('Default language "%s" was not created.', $default_lang));
    }

    PLL()->model->update_default_lang($default_lang);
}

function lc_prod_polylang_configure_options($default_lang) {
    $options = get_option('polylang', []);
    if (!is_array($options)) {
        $options = [];
    }

    $options['default_lang'] = $default_lang;
    $options['force_lang'] = 1;
    $options['hide_default'] = lc_prod_polylang_env_bool('LC_POLYLANG_HIDE_DEFAULT', true);
    $options['rewrite'] = true;
    $options['redirect_lang'] = false;
    $options['browser'] = false;
    $options['media_support'] = true;

    update_option('polylang', $options);
}

function lc_prod_polylang_assign_existing_posts($default_lang) {
    if (!lc_prod_polylang_env_bool('LC_POLYLANG_ASSIGN_EXISTING_CONTENT', true)) {
        lc_prod_polylang_log('Skipped assigning existing content to the default language.');
        return;
    }

    if (!function_exists('pll_get_post_language') || !function_exists('pll_set_post_language')) {
        return;
    }

    $post_types = array_filter(
        ['post', 'page', 'property', 'reference'],
        'post_type_exists'
    );

    if (empty($post_types)) {
        return;
    }

    $post_ids = get_posts([
        'post_type' => array_values($post_types),
        'post_status' => ['publish', 'future', 'draft', 'pending', 'private'],
        'numberposts' => -1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);

    $assigned = 0;

    foreach ($post_ids as $post_id) {
        if (pll_get_post_language($post_id)) {
            continue;
        }

        pll_set_post_language($post_id, $default_lang);
        $assigned++;
    }

    lc_prod_polylang_log(sprintf('Assigned %d existing posts/pages/CPT entries to "%s".', $assigned, $default_lang));
}

function lc_prod_polylang_assign_existing_terms($default_lang) {
    if (!lc_prod_polylang_env_bool('LC_POLYLANG_ASSIGN_EXISTING_CONTENT', true)) {
        return;
    }

    if (!function_exists('pll_get_term_language') || !function_exists('pll_set_term_language')) {
        return;
    }

    $taxonomies = array_filter(['category', 'post_tag'], 'taxonomy_exists');
    if (empty($taxonomies)) {
        return;
    }

    $terms = get_terms([
        'taxonomy' => array_values($taxonomies),
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms)) {
        return;
    }

    $assigned = 0;

    foreach ($terms as $term) {
        if (pll_get_term_language($term->term_id)) {
            continue;
        }

        pll_set_term_language($term->term_id, $default_lang);
        $assigned++;
    }

    lc_prod_polylang_log(sprintf('Assigned %d existing categories/tags to "%s".', $assigned, $default_lang));
}

function lc_prod_polylang_english_translations() {
    return [
        'Mg Rdc Imm A, Résidence Amira' => 'Ground floor shop, Building A, Residence Amira',
        'Mg Rdc Imm A, Residence Amira' => 'Ground floor shop, Building A, Residence Amira',
        'Avenue 4ème DMM, Camp El Ghoul' => '4th DMM Avenue, Camp El Ghoul',
        'Avenue 4eme DMM, Camp El Ghoul' => '4th DMM Avenue, Camp El Ghoul',
        'Marrakech' => 'Marrakech',
        'Copropriété & Immobilier — Marrakech' => 'Property Management & Real Estate - Marrakech',
        'Copropriete & Immobilier - Marrakech' => 'Property Management & Real Estate - Marrakech',
        'Votre Patrimoine,<br><em>Notre</em><br><span class="stroke">Expertise</span>' => 'Your Property,<br><em>Our</em><br><span class="stroke">Expertise</span>',
        'Gestion de copropriété, location, achat et vente de biens immobiliers. Un accompagnement professionnel et transparent à Marrakech.' => 'Condominium management, rental, buying and selling real estate. Professional and transparent support in Marrakech.',
        'Gestion de copropriete, location, achat et vente de biens immobiliers. Un accompagnement professionnel et transparent a Marrakech.' => 'Condominium management, rental, buying and selling real estate. Professional and transparent support in Marrakech.',
        'Voir Nos Biens' => 'View Our Properties',
        'Nos Services' => 'Our Services',
        'Qui Sommes-Nous' => 'About Us',
        'Notre <span style="color:var(--primary)">Société</span>' => 'Our <span style="color:var(--primary)">Company</span>',
        'Notre <span style="color:var(--gold)">Societe</span>' => 'Our <span style="color:var(--gold)">Company</span>',
        'Notre société est une entreprise à responsabilité limitée, expérimentée dans la gestion de copropriété ainsi que dans la gestion et la valorisation des biens immobiliers. Forte d\'une approche professionnelle et rigoureuse, elle accompagne les copropriétaires dans l\'administration, la location, l\'achat et la vente de leurs biens immobiliers.' => 'Our company is a limited liability company experienced in condominium management as well as managing and enhancing real estate assets. With a professional and rigorous approach, it supports co-owners in the administration, rental, purchase and sale of their real estate.',
        'Notre societe accompagne les coproprietaires, investisseurs et familles dans la gestion, la location, l\'achat et la vente de biens immobiliers a Marrakech.' => 'Our company supports co-owners, investors and families with property management, rental, buying and selling real estate in Marrakech.',
        'Grâce à une organisation fondée sur la transparence, la proximité et la qualité de service, nous veillons à assurer une gestion efficace des résidences et à répondre aux attentes de notre clientèle dans le respect des dispositions réglementaires en vigueur.' => 'Through an organization based on transparency, proximity and service quality, we ensure effective management of residences and respond to our clients expectations while respecting current regulations.',
        'Nous privilegions une gestion claire, reactive et durable, avec un suivi administratif, technique et commercial adapte a chaque residence.' => 'We prioritize clear, responsive and sustainable management, with administrative, technical and commercial follow-up tailored to each residence.',
        'Références' => 'References',
        'References' => 'References',
        'Ils nous font <span style="color:var(--primary)">confiance</span>' => 'They <span style="color:var(--primary)">trust us</span>',
        'Ils nous font <span style="color:var(--gold)">confiance</span>' => 'They <span style="color:var(--gold)">trust us</span>',
        'Nous accompagnons différentes résidences et clients dans la gestion, la valorisation et le suivi de leurs biens immobiliers.' => 'We support residences and clients with the management, enhancement and follow-up of their real estate assets.',
        'Un apercu de residences et clients accompagnes par Luxury Copro sur Marrakech et sa region.' => 'A selection of residences and clients supported by Luxury Copro in Marrakech and the surrounding region.',
        'Notre Portefeuille' => 'Our Portfolio',
        'Nos Biens<br><span style="color:var(--primary)">Disponibles</span>' => 'Our Properties<br><span style="color:var(--primary)">Available</span>',
        'Nos Biens<br><span style="color:var(--gold)">Disponibles</span>' => 'Our Properties<br><span style="color:var(--gold)">Available</span>',
        'Exemples de Biens<br><span style="color:var(--primary)">Disponibles</span>' => 'Sample Properties<br><span style="color:var(--primary)">Available</span>',
        'Exemples de Biens<br><span style="color:var(--gold)">Disponibles</span>' => 'Sample Properties<br><span style="color:var(--gold)">Available</span>',
        'Découvrez notre sélection de biens immobiliers à Marrakech. Contactez-nous pour plus d\'informations.' => 'Discover our selection of real estate properties in Marrakech. Contact us for more information.',
        'Decouvrez une selection de biens types pour visualiser le rendu front et lancer le projet avec une base concrete.' => 'Discover a selection of sample properties to preview the front-end rendering and start the project with concrete content.',
        'Les biens présentés ci-dessous sont des exemples illustratifs. Pour consulter nos offres réelles et actualisées, veuillez nous contacter directement.' => 'The properties shown below are illustrative examples. To view real and up-to-date listings, please contact us directly.',
        'Les biens presentes ci-dessous sont des exemples illustratifs. Pour consulter les offres reelles et actualisees, veuillez nous contacter directement.' => 'The properties shown below are illustrative examples. To view real and up-to-date listings, please contact us directly.',
        'Gestion de Copropriété' => 'Condominium Management',
        'Gestion de Copropriete' => 'Condominium Management',
        'Gestion administrative, technique et financière des copropriétés. Suivi des charges, budgets et assemblées générales.' => 'Administrative, technical and financial management of condominiums. Monitoring of fees, budgets and general meetings.',
        'Gestion administrative, technique et financiere des coproprietes. Suivi des charges, budgets et assemblees generales.' => 'Administrative, technical and financial management of condominiums. Monitoring of fees, budgets and general meetings.',
        'Location' => 'Rental',
        'Mise en location, sélection des locataires, suivi des contrats et gestion quotidienne de vos biens locatifs.' => 'Rental listing, tenant selection, contract follow-up and day-to-day management of your rental properties.',
        'Mise en location, selection des locataires, suivi des contrats et gestion quotidienne de vos biens locatifs.' => 'Rental listing, tenant selection, contract follow-up and day-to-day management of your rental properties.',
        'Achat & Vente' => 'Buying & Selling',
        'Accompagnement personnalisé pour l\'achat et la vente. Valorisation, promotion et assistance jusqu\'à la finalisation.' => 'Personalized support for buying and selling. Valuation, promotion and assistance through completion.',
        'Accompagnement personnalise pour l\'achat et la vente. Valorisation, promotion et assistance jusqu\'a la finalisation.' => 'Personalized support for buying and selling. Valuation, promotion and assistance through completion.',
        'Travaux & Maintenance' => 'Works & Maintenance',
        'Entretien des équipements communs, suivi des prestataires et travaux techniques divers pour vos résidences.' => 'Maintenance of shared equipment, provider coordination and various technical works for your residences.',
        'Entretien des equipements communs, suivi des prestataires et travaux techniques divers pour vos residences.' => 'Maintenance of shared equipment, provider coordination and various technical works for your residences.',

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
        'Gestion de Copropriété — Travaux Techniques Divers — Intérim — Immobilier. Votre partenaire de confiance à' => 'Condominium Management - Technical Works - Interim Services - Real Estate. Your trusted partner in',
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
        'Exclusif' => 'Exclusive',
        'Chambres' => 'Bedrooms',
        'Salles de Bain' => 'Bathrooms',
        'Surface' => 'Area',
        'Un Accompagnement' => 'Complete',
        'Complet' => 'Support',
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
        'Développer une relation de confiance durable avec nos clients en proposant des services modernes, fiables et adaptés aux exigences du secteur immobilier et de la gestion de copropriété.' => 'Build lasting trust with our clients by providing modern, reliable services tailored to real estate and condominium management requirements.',
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
        'Gestion de copropriété' => 'Condominium management',
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
        'Description' => 'Description',
        'Planifier une Visite' => 'Schedule a Visit',
        'Luxury Copro' => 'Luxury Copro',

        'Villa Contemporaine avec Piscine' => 'Contemporary Villa with Pool',
        'Appartement Standing Guéliz' => 'Premium Apartment in Gueliz',
        'Appartement Standing Gueliz' => 'Premium Apartment in Gueliz',
        'Riad Rénové — Médina' => 'Renovated Riad in the Medina',
        'Riad Renove Medina' => 'Renovated Riad in the Medina',
        'Duplex Meublé Hivernage' => 'Furnished Duplex in Hivernage',
        'Duplex Meuble Hivernage' => 'Furnished Duplex in Hivernage',
        'Villa de Luxe Palmeraie' => 'Luxury Villa in Palmeraie',
        'Terrain Constructible Targa' => 'Buildable Plot in Targa',
        'Route de l\'Ourika, Marrakech' => 'Ourika Road, Marrakech',
        'Av. Mohammed V, Guéliz' => 'Mohammed V Avenue, Gueliz',
        'Av. Mohammed V, Gueliz' => 'Mohammed V Avenue, Gueliz',
        'Derb Jdid, Médina' => 'Derb Jdid, Medina',
        'Derb Jdid, Medina' => 'Derb Jdid, Medina',
        'Av. Echouhada, Hivernage' => 'Echouhada Avenue, Hivernage',
        'Circuit de la Palmeraie' => 'Palmeraie Circuit',
        'Targa, Marrakech' => 'Targa, Marrakech',
        'Terrain' => 'Plot',
        'Étage' => 'Floor',
        'Etage' => 'Floor',
        'Équipé' => 'Equipped',
        'Equipe' => 'Equipped',
        'Autorisé' => 'Approved',
        'Autorise' => 'Approved',
        'Magnifique villa contemporaine située dans un quartier résidentiel prisé de Marrakech.' => 'Beautiful contemporary villa located in a sought-after residential district of Marrakech.',
        'Magnifique villa contemporaine situee dans un quartier residentiel prise de Marrakech.' => 'Beautiful contemporary villa located in a sought-after residential district of Marrakech.',
        'Superbe appartement de standing au cœur de Guéliz.' => 'Superb premium apartment in the heart of Gueliz.',
        'Superbe appartement de standing au coeur de Gueliz.' => 'Superb premium apartment in the heart of Gueliz.',
        'Riad d\'exception entièrement rénové dans les règles de l\'art.' => 'Exceptional riad fully renovated with authentic craftsmanship.',
        'Riad d\'exception entierement renove dans les regles de l\'art.' => 'Exceptional riad fully renovated with authentic craftsmanship.',
        'Duplex entièrement meublé et équipé dans le quartier prestigieux de l\'Hivernage.' => 'Fully furnished and equipped duplex in the prestigious Hivernage district.',
        'Duplex entierement meuble et equipe dans le quartier prestigieux de l\'Hivernage.' => 'Fully furnished and equipped duplex in the prestigious Hivernage district.',
        'Villa de prestige nichée dans la légendaire Palmeraie de Marrakech.' => 'Prestige villa nestled in Marrakech\'s legendary Palmeraie.',
        'Villa de prestige nichee dans la legendaire Palmeraie de Marrakech.' => 'Prestige villa nestled in Marrakech\'s legendary Palmeraie.',
        'Terrain plat et bien situé dans le quartier recherché de Targa.' => 'Flat, well-located plot in the sought-after district of Targa.',
        'Terrain plat et bien situe dans le quartier recherche de Targa.' => 'Flat, well-located plot in the sought-after district of Targa.',
        'Piscine privée' => 'Private pool',
        'Piscine privee' => 'Private pool',
        'Jardin paysager' => 'Landscaped garden',
        'Garage double' => 'Double garage',
        'Climatisation centrale' => 'Central air conditioning',
        'Cuisine équipée' => 'Equipped kitchen',
        'Cuisine equipee' => 'Equipped kitchen',
        'Vue sur l\'Atlas' => 'Atlas Mountains view',
        'Sécurité 24h/24' => '24/7 security',
        'Securite 24h/24' => '24/7 security',
        'Proche commodités' => 'Close to amenities',
        'Proche commodites' => 'Close to amenities',
        'Balcon terrasse' => 'Balcony terrace',
        'Ascenseur' => 'Elevator',
        'Parking sous-sol' => 'Underground parking',
        'Résidence sécurisée' => 'Secure residence',
        'Residence securisee' => 'Secure residence',
        'Double vitrage' => 'Double glazing',
        'Parquet massif' => 'Solid wood flooring',
        'Cuisine américaine' => 'Open-plan kitchen',
        'Cuisine americaine' => 'Open-plan kitchen',
        'Proche tramway' => 'Close to transport',
        'Patio avec fontaine' => 'Patio with fountain',
        'Terrasse panoramique' => 'Panoramic terrace',
        'Zellige traditionnel' => 'Traditional zellige',
        'Hammam privé' => 'Private hammam',
        'Hammam prive' => 'Private hammam',
        'Cuisine marocaine' => 'Moroccan kitchen',
        'Vue Koutoubia' => 'Koutoubia view',
        'Bois de cèdre sculpté' => 'Carved cedar wood',
        'Bois de cedre sculpte' => 'Carved cedar wood',
        'Rentabilité locative' => 'Rental yield',
        'Rentabilite locative' => 'Rental yield',
        'Entièrement meublé' => 'Fully furnished',
        'Entierement meuble' => 'Fully furnished',
        'Piscine résidence' => 'Residence pool',
        'Piscine residence' => 'Residence pool',
        'Climatisation' => 'Air conditioning',
        'Internet fibre' => 'Fiber internet',
        'Machine à laver' => 'Washing machine',
        'Machine a laver' => 'Washing machine',
        'Quartier calme' => 'Quiet neighborhood',
        'Proche centre' => 'Close to the center',
        'Disponible immédiatement' => 'Available immediately',
        'Disponible immediatement' => 'Available immediately',
        'Piscine à débordement' => 'Infinity pool',
        'Piscine a debordement' => 'Infinity pool',
        'Pool house' => 'Pool house',
        'Jardin tropical' => 'Tropical garden',
        'Personnel de maison' => 'House staff',
        'Domotique' => 'Home automation',
        'Hammam & spa' => 'Hammam & spa',
        'Suite parentale 60m²' => '60 sqm master suite',
        'Suite parentale 60m2' => '60 sqm master suite',
        'Sécurité renforcée' => 'Enhanced security',
        'Securite renforcee' => 'Enhanced security',
        'Titre foncier' => 'Land title',
        'Autorisation R+2' => 'R+2 building authorization',
        'Réseaux en bordure' => 'Utilities at plot edge',
        'Reseaux en bordure' => 'Utilities at plot edge',
        'Accès goudronné' => 'Paved access',
        'Acces goudronne' => 'Paved access',
        'Quartier résidentiel' => 'Residential neighborhood',
        'Quartier residentiel' => 'Residential neighborhood',
        'Terrain plat' => 'Flat plot',
        'Environnement calme' => 'Quiet environment',
        'Proche écoles' => 'Close to schools',
        'Proche ecoles' => 'Close to schools',
    ];
}

function lc_prod_polylang_register_strings(array $translations) {
    if (!function_exists('pll_register_string')) {
        return;
    }

    foreach ($translations as $source => $translation) {
        if ($source === '' || $translation === '') {
            continue;
        }

        $name = trim(wp_strip_all_tags((string) $source));
        $name = $name !== '' ? substr($name, 0, 90) : md5((string) $source);
        pll_register_string('Luxury Copro - ' . $name, (string) $source, 'Luxury Copro', true);
    }

    if (function_exists('lc_register_polylang_strings')) {
        lc_register_polylang_strings();
    }
}

function lc_prod_polylang_save_english_translations(array $translations) {
    $english = get_term_by('slug', 'en', 'language');

    if (!$english instanceof WP_Term) {
        lc_prod_polylang_log('English language is not enabled; skipped English string translations.');
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

    foreach ($translations as $source => $translation) {
        if ($source === '' || $translation === '') {
            continue;
        }

        $strings[(string) $source] = (string) $translation;
    }

    $pairs = [];
    foreach ($strings as $source => $translation) {
        $pairs[] = [$source, $translation];
    }

    update_term_meta($english->term_id, '_pll_strings_translations', wp_slash($pairs));
    lc_prod_polylang_log(sprintf('Seeded %d English string translations.', count($translations)));
}

if (!function_exists('PLL')) {
    lc_prod_polylang_error('Polylang is not active. Run scripts/seed-polylang-production.sh so WP-CLI can install and activate it first.');
}

$default_lang = strtolower(getenv('LC_POLYLANG_DEFAULT_LANG') ?: 'fr');
$requested_languages = lc_prod_polylang_requested_languages($default_lang);

lc_prod_polylang_log('Applying production Polylang seed...');
lc_prod_polylang_ensure_languages($requested_languages, $default_lang);
lc_prod_polylang_configure_options($default_lang);
lc_prod_polylang_assign_existing_posts($default_lang);
lc_prod_polylang_assign_existing_terms($default_lang);

$english_translations = lc_prod_polylang_english_translations();
lc_prod_polylang_register_strings($english_translations);
lc_prod_polylang_save_english_translations($english_translations);

$languages = $requested_languages;
sort($languages);
update_option('lc_polylang_language_signature', implode('|', $languages), false);
update_option('luxury_copro_polylang_production_seed_version', LC_PROD_POLYLANG_SEED_VERSION, false);
delete_option('rewrite_rules');
flush_rewrite_rules();

lc_prod_polylang_log(sprintf('Production Polylang seed %s applied.', LC_PROD_POLYLANG_SEED_VERSION));
