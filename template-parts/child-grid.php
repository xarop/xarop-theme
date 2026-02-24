<?php
/**
 * Template part for displaying a grid of child pages if any exist
 * Usage: include locate_template('template-parts/child-grid.php');
 *
 * @package xarop
 */

$child_pages = get_pages(
    array(
    'child_of'    => get_the_ID(),
    'parent'      => get_the_ID(),
    'sort_column' => 'menu_order',
    'sort_order'  => 'ASC',
    'post_status' => 'publish',
    )
);

if (!empty($child_pages)) : ?>
    <div class="child-grid">
        <div class="grid">
            <?php foreach ($child_pages as $child) : ?>
                <?php
                // Set up $post for card.php
                $post = get_post($child->ID);
                setup_postdata($post);
                include locate_template('template-parts/card.php');
                wp_reset_postdata();
                ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif;
