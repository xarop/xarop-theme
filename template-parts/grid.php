<?php
/**
 * posts Grid Section Template
 *
 * @package xarop
 *
 * Variables expected:
 * $categories (array) - taxonomy terms for filters
 * $section_title (string) - section title (optional)
 * $show_filters (bool) - show category filters (default true)
 * $grid_id (string) - id for the grid container (default 'grid')
 */

$has_posts = (new WP_Query(
    [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'fields' => 'ids',
    ]
))->have_posts();

if (!isset($categories)) {
    $categories = get_terms(
        [
        'taxonomy' => 'category',
        'hide_empty' => true,
        ]
    );
}
if (!isset($section_title)) {
    $section_title = '';
}
if (!isset($show_filters)) {
    $show_filters = true;
}
if (!isset($grid_id)) {
    $grid_id = 'grid';
}
if ($has_posts) :
    ?>
<div class="grid-section">
    <?php if (!empty($section_title)) : ?>
        <h2 class="text-center"><?php echo esc_html($section_title); ?></h2>
    <?php endif; ?>

    <?php if ($show_filters && !empty($categories) && !is_wp_error($categories)) : ?>
        <div class="category-filters">
            <button class="filter-btn active" data-category="all">
                <?php esc_html_e('All', 'xarop'); ?>
            </button>
            <?php foreach ($categories as $category) : ?>
                <button class="filter-btn" data-category="<?php echo esc_attr($category->term_id); ?>">
                    <?php echo esc_html($category->name); ?>
                    <!-- <span class="category-count">(<?php echo esc_html($category->count); ?>)</span> -->
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="grid" id="<?php echo esc_attr($grid_id); ?>">
        <!-- posts will be loaded here via JavaScript or PHP include -->
    </div>
</div>
<?php endif; ?>
