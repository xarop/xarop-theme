<?php
/**
 * The template for displaying post archives
 *
 * @package xarop
 * @since   1.0.0
 */

get_header();
?>

<div class="container">
    <header class="page-header">
        <h1 class="page-title text-center"><?php echo get_the_archive_title(); ?></h1>
        <?php
        $archive_description = get_the_archive_description();
        if ($archive_description) :
        ?>
            <p class="archive-description text-center"><?php echo wp_kses_post($archive_description); ?></p>
        <?php endif; ?>
    </header>

    <?php if (have_posts()) : ?>
        <div class="grid">
            <?php
            while (have_posts()) :
                the_post();
                include locate_template('template-parts/card.php');
            endwhile;
            ?>
        </div>

        <div class="pagination">
            <?php
            global $wp_query;
            $big = 999999999;
            echo paginate_links(
                array(
                    'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format'    => '?paged=%#%',
                    'current'   => max(1, get_query_var('paged')),
                    'total'     => $wp_query->max_num_pages,
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                )
            );
            ?>
        </div>

    <?php else : ?>
        <p class="text-center"><?php esc_html_e('No posts found.', 'xarop'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
