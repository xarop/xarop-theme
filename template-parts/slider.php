<?php
/**
 * Slider Partial — self-contained, reusable on any page.
 *
 * Usage: require locate_template('template-parts/slider.php');
 *
 * Optional variables (set before including):
 *   $slider_query  (WP_Query) — pre-built query; auto-built from current page's categories if omitted.
 *   $slider_config (array)    — ['type' => 'posts'|'pages'] to override post type (default: 'pages').
 *   $slide_class            (string) — CSS class for each slide (default: 'slide').
 *   $slider_container_class (string) — CSS class for the track (default: 'slider-container').
 */

$slide_class            = isset($slide_class) ? $slide_class : 'slide';
$slider_container_class = isset($slider_container_class) ? $slider_container_class : 'slider-container';

// Auto-build query from the current page's categories if not provided externally.
if ( ! isset($slider_query) || ! ($slider_query instanceof WP_Query) ) {
    $slider_post_type = ( isset($slider_config['type']) && $slider_config['type'] === 'posts' ) ? 'post' : 'page';
    $current_id       = (int) get_queried_object_id();
    $current_cats     = $current_id
        ? wp_get_post_terms($current_id, 'category', array('fields' => 'ids'))
        : array();

    $auto_tax_query = ( ! empty($current_cats) && ! is_wp_error($current_cats) )
        ? array(
            array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $current_cats,
                'operator' => 'IN',
            ),
        )
        : array(
            array(
                'taxonomy' => 'category',
                'operator' => 'EXISTS',
            ),
        );

    $slider_ids = get_posts(
        array(
            'post_type'      => $slider_post_type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'post__not_in'   => array($current_id),
            'tax_query'      => $auto_tax_query,
        )
    );

    if ( empty($slider_ids) ) {
        return;
    }

    $slider_query = new WP_Query(
        array(
            'post_type'      => $slider_post_type,
            'post__in'       => $slider_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => count($slider_ids),
        )
    );
}
?>
<div class="slider-wrapper" style="position:relative;">
<?php
if (function_exists('is_front_page') && is_front_page() && isset($GLOBALS['post'])) {
    $front_id = $GLOBALS['post']->ID;
    if (has_post_thumbnail($front_id)) {
        echo '<a href="#main" class="slider-featured-image">';
        echo get_the_post_thumbnail($front_id, 'large', array('class' => 'featured-image'));
        echo '</a>';
    }
}
?>
<div class="slider">
    <?php if ($slider_query->have_posts()) : ?>
        <div class="<?php echo esc_attr($slider_container_class); ?>" data-current-slide="0">
            <?php
            $slide_index = 0;
            while ($slider_query->have_posts()) :
                $slider_query->the_post();
                ?>
                <div class="<?php echo esc_attr($slide_class); ?>" data-slide="<?php echo esc_attr($slide_index); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('full', array('class' => 'slide-image')); ?>
                    <?php else : ?>
                        <div class="slide-image" style="height: 500px;"></div>
                    <?php endif; ?>
                    <div class="slide-content">
                            <h2 class="slide-title"><?php the_title(); ?></h2>
                            <div class="slide-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="slide-link">
                                <?php esc_html_e('Read more', 'xarop'); ?>
                            </a>
                    </div>
                </div>
                <?php
                $slide_index++;
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php if ($slide_index > 1) : ?>
            <div class="slider-controls">
                <?php for ($i = 0; $i < $slide_index; $i++) : ?>
                    <span class="slider-dot <?php echo ($i === 0) ? 'active' : ''; ?>" data-slide="<?php echo esc_attr($i); ?>"></span>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>
