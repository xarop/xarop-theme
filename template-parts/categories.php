<?php
/**
 * Template part for displaying shared categories for a post
 * Usage: include locate_template('template-parts/shared-categories.php');
 *
 * Expects $post_id to be set in the parent scope (or uses get_the_ID())
 *
 * @package xarop
 */

if (!isset($post_id)) {
    $post_id = get_the_ID();
}
$terms = get_the_terms($post_id, 'category');
if (!empty($terms) && !is_wp_error($terms)) :
    ?>
    <div class="entry-categories">
        <h3><?php esc_html_e('Categories', 'xarop'); ?></h3>
        <div class="post-categories">
            <?php foreach ($terms as $term) : ?>
                <a href="<?php echo esc_url(get_term_link($term)); ?>" class="category-tag">
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif;
