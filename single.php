<?php
/**
 * The template for displaying single posts
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
                <small class="entry-meta text-center" style="display:block; margin-bottom:1em; color:#888;">
                    <?php
                    // Data de publicació
                    echo get_the_date();
                    // Temps de lectura aproximat
                    $content = get_post_field('post_content', get_the_ID());
                    $word_count = str_word_count(strip_tags($content));
                    $reading_time = ceil($word_count / 200); // 200 paraules/minut
                    echo ' · ' . sprintf(_n('%d minut de lectura', '%d minuts de lectura', $reading_time, 'xarop'), $reading_time);
                    echo do_shortcode('[gtranslate]');
                    ?>

                </small>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="post-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <?php
            // Mostrar la galería personalizada si existe
            include locate_template('template-parts/gallery.php');
            // Mostrar las categorías
            include locate_template('template-parts/categories.php');
            // Mostrar botons de compartir
            include_once locate_template('inc/share.php');
            xarop_theme_share_buttons();
            $xarop_config = xarop_get_config();
            // Mostrar comentarios si están activados y el post tiene comentarios abiertos
            if ($xarop_config['comments_enabled'] && ( comments_open() || get_comments_number() ) ) {
                comments_template();
            }
            // Mostrar entradas relacionadas
            include locate_template('template-parts/related.php');
            ?>

       
            <footer class="entry-footer">
                <a href="<?php echo esc_url(get_post_type_archive_link('post')); ?>" class="back-link">
                    &larr; <?php esc_html_e('Back to projects', 'xarop'); ?>
                </a>
            </footer>

        </article>


    <?php endwhile; ?>
</div>

<?php
get_footer();
