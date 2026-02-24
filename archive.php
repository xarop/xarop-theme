<?php
/**
 * The template for displaying post archives
 *
 * @package xarop
 * @since   1.0.0
 */

get_header();
?>
<section class="container">
    <div class="container-inner">
        <header class="page-header">
            <h1 class="page-title">
                ARCHIVE
                <?php
                $post_type_obj = get_post_type_object(get_post_type());
                echo esc_html($post_type_obj && isset($post_type_obj->labels->name) ? $post_type_obj->labels->name : '');
                ?>
            </h1>
        </header>

        <?php
        // Get all categories for filters
        $categories = get_terms(
            array(
            'taxonomy'   => 'category',
            'hide_empty' => true,
            )
        );
        ?>

        <?php if (! empty($categories) && ! is_wp_error($categories) ) : ?>
            <?php 
            // $section_title = __('posts', 'xarop');
            include locate_template('template-parts/grid.php'); 
            ?>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
