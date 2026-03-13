<?php
/**
 * 404 Error Page Template
 *
 * @package xarop
 * @since   1.0.0
 */

get_header();
?>

<div class="container">
    <div class="error-404 not-found single-content">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e('404 - Page not found', 'xarop'); ?></h1>
        </header>

        <div class="page-content">
            <p><?php esc_html_e('Nothing was found at this location. Try searching?', 'xarop'); ?></p>

            <?php get_search_form(); ?>

            <h2><?php esc_html_e('Recent posts', 'xarop'); ?></h2>

            <?php
            $recent_posts = new WP_Query(
                array(
                    'post_type'      => 'post',
                    'posts_per_page' => 3,
                    'post_status'    => 'publish',
                )
            );

            if ($recent_posts->have_posts() ) :
                ?>
                <div class="grid">
                <?php
                while ( $recent_posts->have_posts() ) :
                    $recent_posts->the_post();
                    ?>
                        <article class="card">
                    <?php if (has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium', array( 'class' => 'post-image' )); ?>
                                </a>
                    <?php endif; ?>

                            <div class="card-content">
                                <h3 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                            </div>
                        </article>
                <?php endwhile; ?>
                </div>
                <?php
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
