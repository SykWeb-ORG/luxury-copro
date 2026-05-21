<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package LuxuryCopro
 */

get_header();
?>

<section class="error-404-section">
    <div class="error-404-content">
        <p class="error-404-code"><?php echo esc_html('404'); ?></p>
        <h1 class="error-404-title"><?php esc_html_e('Page introuvable', 'luxurycopro'); ?></h1>
        <p class="error-404-message"><?php esc_html_e('La page que vous recherchez n\'existe pas ou a été déplacée.', 'luxurycopro'); ?></p>
        <div class="error-404-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-gold">
                <?php esc_html_e('Retour à l\'accueil', 'luxurycopro'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn-ghost">
                <?php esc_html_e('Nous Contacter', 'luxurycopro'); ?>
            </a>
        </div>
    </div>
</section>

<?php
get_footer();
