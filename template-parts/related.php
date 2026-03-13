<?php
// reset query to avoid conflicts with parent template
wp_reset_postdata();
$post_id = get_the_ID();

$terms = get_the_terms($post_id, 'category');
if (!empty($terms) && !is_wp_error($terms)) :
    $term_ids = wp_list_pluck($terms, 'term_id');
    $related_posts = new WP_Query(
        array(
        'post_type' => 'post',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ),
        ),
        'post__not_in' => array(get_the_ID()),
        )
    );
    if ($related_posts->have_posts()) :
        ?>
            <section class="related-posts">
                <h3><?php esc_html_e('Related posts', 'xarop'); ?></h3>
                <div class="grid">
                <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                    <?php include locate_template('template-parts/card.php'); ?>
                <?php endwhile; ?>
                </div>
            </section>
                <?php
    endif;
    wp_reset_postdata();
endif;
?>
