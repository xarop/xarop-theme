<?php
/**
 * Template part for displaying categories and tags for a post.
 * Usage: include locate_template('template-parts/categories.php');
 *
 * Expects $post_id to be set in the parent scope (or uses get_the_ID())
 *
 * @package xarop
 */

if (!isset($post_id)) {
    $post_id = get_the_ID();
}

$categories = get_the_terms($post_id, 'category');
$tags        = get_the_terms($post_id, 'post_tag');

if (( ! empty($categories) && ! is_wp_error($categories) ) || ( ! empty($tags) && ! is_wp_error($tags) ) ) : ?>
    <div class="entry-categories">
        <?php if (! empty($categories) && ! is_wp_error($categories) ) : ?>
            <!-- <h3><?php esc_html_e('Categories', 'xarop'); ?></h3> -->
            <div class="post-categories">
                <?php foreach ($categories as $term) : ?>
                    <a href="<?php echo esc_url(get_term_link($term)); ?>" class="category-tag">
                        <?php echo esc_html($term->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (! empty($tags) && ! is_wp_error($tags) ) : ?>
            <div class="post-tags">
                <?php
                $tag_links = array();
                foreach ($tags as $tag) {
                    $tag_links[] = '<a href="' . esc_url(get_term_link($tag)) . '" class="post-tag">' . esc_html($tag->name) . '</a>';
                }
                echo implode('<span class="post-tag-sep">, </span>', $tag_links);
                ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif;
