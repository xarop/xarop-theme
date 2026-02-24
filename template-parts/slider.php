<?php
/**
 * Reusable Slider Partial
 *
 * Usage: include or get_template_part('template-parts/slider')
 * Variables expected:
 * $slider_query (WP_Query) - query with slides
 * $slide_class (string) - optional, default 'slide'
 * $slider_container_class (string) - optional, default 'slider-container'
 */
if (!isset($slider_query) || !($slider_query instanceof WP_Query)) {
    return;
}
$slide_class = isset($slide_class) ? $slide_class : 'slide';
$slider_container_class = isset($slider_container_class) ? $slider_container_class : 'slider-container';
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
                            <?php esc_html_e('Learn More', 'xarop'); ?>
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
