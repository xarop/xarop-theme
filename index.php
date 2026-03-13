<?php
/**
 * The main template file
 *
 * @package xarop
 * @since   1.0.0
 */

get_header();
?>

<section class="section">
    <div class="container">
        <div class="container-inner">
            <?php
            // get the title
            $blog_page_id = get_option('page_for_posts');
            if ($blog_page_id) {
                $section_title = get_the_title($blog_page_id);
            } else {
                $title = get_bloginfo('name');
            }
            // Obtener todas las categorías para los filtros
            $categories = get_terms(
                array(
                'taxonomy'   => 'category',
                'hide_empty' => true,
                )
            );
            ?>
            <?php if (! empty($categories) && ! is_wp_error($categories) ) : ?>
                <?php include locate_template('template-parts/grid.php'); ?>
            <?php endif; ?>

        </div>
    </div>     
</section>

<?php
get_footer();
