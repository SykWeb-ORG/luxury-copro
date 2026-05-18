<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package LuxuryCopro
 */

get_header();
?>

<style>
    .error-404-section {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 4rem 1.5rem;
        background: var(--bg);
    }

    .error-404-content {
        max-width: 600px;
    }

    .error-404-code {
        font-family: 'Playfair Display', serif;
        font-size: clamp(8rem, 20vw, 14rem);
        font-weight: 700;
        color: var(--gold, #2968A0);
        line-height: 1;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .error-404-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.5rem, 4vw, 2.5rem);
        color: var(--text, #ffffff);
        margin: 1rem 0 0.75rem;
        font-weight: 600;
    }

    .error-404-message {
        font-family: 'Inter', sans-serif;
        font-size: 1.05rem;
        color: var(--muted, #a0a0a0);
        margin: 0 0 2.5rem;
        line-height: 1.7;
    }

    .error-404-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .error-404-actions .btn-gold {
        display: inline-block;
        padding: 0.85rem 2rem;
        background: var(--gold, #2968A0);
        color: #ffffff;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        border-radius: 4px;
        transition: opacity 0.3s ease, transform 0.3s ease;
        border: 1px solid var(--gold, #2968A0);
    }

    .error-404-actions .btn-gold:hover {
        opacity: 0.85;
        transform: translateY(-2px);
    }

    .error-404-actions .btn-ghost {
        display: inline-block;
        padding: 0.85rem 2rem;
        background: transparent;
        color: var(--text, #ffffff);
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        border-radius: 4px;
        border: 1px solid var(--muted, #a0a0a0);
        transition: border-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
    }

    .error-404-actions .btn-ghost:hover {
        border-color: var(--gold, #2968A0);
        color: var(--gold, #2968A0);
        transform: translateY(-2px);
    }
</style>

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
