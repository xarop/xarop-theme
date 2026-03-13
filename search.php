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
            printf(esc_html__('Search results for: %s', 'xarop'), '<span>' . get_search_query() . '</span>');
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
                    'prev_text' => __('&larr; Previous', 'xarop'),
                    'next_text' => __('Next &rarr;', 'xarop'),
                )
            );
            ?>
        </div>
    <?php else : ?>
        <div class="no-results">
            <h2><?php esc_html_e('No results', 'xarop'); ?></h2>
            <p><?php esc_html_e('Sorry, no results matched your search. Please try different keywords.', 'xarop'); ?></p>
            <?php get_search_form(); ?>
        </div>
    <?php endif; ?>
</div>


<?php
get_footer();
