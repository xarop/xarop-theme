<?php
/**
 * post/Page Card Template
 *
 * Usage: include or get_template_part with $post context set up
 *
 * @package xarop
 */

if (empty($post)) {
    global $post;
}

if (!isset($post) || !is_a($post, 'WP_Post')) {
    return;
}

$post_id = $post->ID;
$post_type = get_post_type($post_id);
$link = get_permalink($post_id);
$title = get_the_title($post_id);
$thumb_size = isset($thumb_size) ? $thumb_size : 'medium';

// Get image
if (has_post_thumbnail($post_id)) {
    $image = get_the_post_thumbnail($post_id, $thumb_size, array('class' => 'post-image'));
} else {
    $image = '<div class="post-image placeholder" style="background:#f0f0f0;height:200px;"></div>';
}

// Get excerpt
$excerpt = get_the_excerpt($post_id);
// Get categories (category)
$terms = get_the_terms($post_id, 'category');
?>
<article class="card card-<?php echo esc_attr($post_type); ?>">
    <a href="<?php echo esc_url($link); ?>">
        <?php echo $image; ?>
        <div class="card-content">
            <h4 class="card-title"><?php echo esc_html($title); ?></h4>
            <?php if (!empty($excerpt)) : ?>
                <div class="card-excerpt"><?php echo esc_html(wp_trim_words($excerpt, 18)); ?></div>
            <?php endif; ?>
            <?php if (!empty($terms) && !is_wp_error($terms)) : ?>
                <div class="card-categories">
                    <?php foreach ($terms as $term) : ?>
                        <span class="category-tag"><?php echo esc_html($term->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </a>
</article>
