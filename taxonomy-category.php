<?php
/**
 * Taxonomy template for shared categories
 *
 * @package xarop
 * @since   1.0.0
 */

get_header();
?>

<div class="container">
    <header class="page-header">
        TAXONOMY CATEGORY
        <?php
        $term = get_queried_object();
        ?>
        <h1 class="page-title">
            <?php
            /* translators: %s: Category name */
            printf(esc_html__('Category: %s', 'xarop'), '<span>' . esc_html($term->name) . '</span>');
            ?>
        </h1>
        
        <?php if (! empty($term->description) ) : ?>
            <div class="taxonomy-description">
            <?php echo wp_kses_post(wpautop($term->description)); ?>
            </div>
        <?php endif; ?>
    </header>

    <?php if (have_posts() ) : ?>
        <div class="grid">
        <?php while ( have_posts() ) : the_post(); ?>
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

                        <div class="post-excerpt">
            <?php the_excerpt(); ?>
                        </div>

            <?php
            // Mostrar otras categorías del elemento
            $terms = get_the_terms(get_the_ID(), 'category');
            if (! empty($terms) && ! is_wp_error($terms) ) :
                ?>
                            <div class="post-categories">
                <?php foreach ( $terms as $cat_term ) : ?>
                                    <a href="<?php echo esc_url(get_term_link($cat_term)); ?>" class="category-tag">
                    <?php echo esc_html($cat_term->name); ?>
                                    </a>
                <?php endforeach; ?>
                            </div>
            <?php endif; ?>
                    </div>
                </article>
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

    <?php else : ?>
        <div class="no-results">
            <p><?php esc_html_e('No items found in this category.', 'xarop'); ?></p>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
