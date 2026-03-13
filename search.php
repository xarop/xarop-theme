<?php
/**
 * Search Results Template
 *
 * @package xarop
 * @since   1.0.0
 */

get_header();
?>

<div class="container">
    <section class="container-inner">
    <header class="page-header">
        <h1 class="page-title">
            <?php
            /* translators: %s: search query */
            printf(esc_html__('Resultados de búsqueda para: %s', 'xarop'), '<span>' . get_search_query() . '</span>');
            ?>
        </h1>
        <div class="searchform-wrapper">
            <?php get_search_form(); ?>
        </div>
    </header>

    <?php if (have_posts()) : ?>
        <div class="grid-section">
            <div class="grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php include locate_template('template-parts/card.php'); ?>
                <?php endwhile; ?>
            </div>
            <?php
            the_posts_pagination(
                array(
                    'prev_text' => __('&larr; Anterior', 'xarop'),
                    'next_text' => __('Siguiente &rarr;', 'xarop'),
                )
            );
            ?>
        </div>
    <?php else : ?>
        <div class="no-results">
            <h2><?php esc_html_e('Sin resultados', 'xarop'); ?></h2>
            <p><?php esc_html_e('Lo sentimos, tu búsqueda no encontró resultados. Prueba con otras palabras.', 'xarop'); ?></p>
            <?php get_search_form(); ?>
        </div>
    <?php endif; ?>
</div>


<?php
get_footer();
