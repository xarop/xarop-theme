<?php
/**
 * The template for displaying single pages
 *
 * @package xarop
 * @since   1.0.0
 */

get_header();
?>

<div class="container">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('single-content'); ?>>
            <header class="entry-header">
                <?php the_title('<h1 class="entry-title text-center">', '</h1>'); ?>
            </header>

            <!-- <?php if (has_post_thumbnail() ) : ?>
                <div class="post-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
           <?php endif; ?> -->

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <?php
            // Mostrar la cuadrícula de páginas hijas si las hay
            include locate_template('template-parts/child-grid.php');
            // Mostrar la galería personalizada si existe
            include locate_template('template-parts/gallery.php');
            // Mostrar las categorías
            // include locate_template('template-parts/categories.php');
            // Mostrar entradas relacionadas
            include locate_template('template-parts/related.php');
            ?>
            <footer class="entry-footer">
                <?php
                $parent_id = wp_get_post_parent_id(get_the_ID());
                if ($parent_id) :
                    ?>
                    <a href="<?php echo esc_url(get_permalink($parent_id)); ?>" class="back-link">
                        &larr; <?php esc_html_e('Back to home', 'xarop'); ?>
                    </a>
                <?php endif; ?>
            </footer>
        </article>

    <?php endwhile; ?>
</div>

<?php
get_footer();
