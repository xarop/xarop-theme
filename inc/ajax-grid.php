<?php
/**
 * AJAX Grid Handler
 *
 * Handle server-side logic for category-based dynamic grid
 *
 * @package xarop
 * @since   1.0.0
 */

// Exit if accessed directly
if (! defined('ABSPATH') ) {
    exit;
}

/**
 * AJAX handler for filtering posts by category
 */
function xarop_filter_posts()
{
    // Verify nonce
    check_ajax_referer('xarop_nonce', 'nonce');

    // Get parameters
    $category_id = isset($_POST['category']) ? intval($_POST['category']) : 0;
    $posts_per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 12;

    // Build query arguments
    $args = array(
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    );

    // Add category filter if specified
    if ($category_id > 0 ) {
        $args['tax_query'] = array(
        array(
        'taxonomy' => 'category',
        'field'    => 'term_id',
        'terms'    => $category_id,
        ),
        );
    }

    // Execute query
    $query = new WP_Query($args);

    // Prepare response
    $response = array(
    'success' => false,
    'data'    => array(
    'posts' => array(),
    'total'    => 0,
    ),
    );

    if ($query->have_posts() ) {
        $posts = array();

        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();

            // Get categories
            $terms = get_the_terms($post_id, 'category');
            $categories = array();

            if (! empty($terms) && ! is_wp_error($terms) ) {
                foreach ( $terms as $term ) {
                    $categories[] = array(
                    'id'   => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    );
                }
            }

            // Get featured image
            $thumbnail_id = get_post_thumbnail_id($post_id);
            $thumbnail_url = '';

            if ($thumbnail_id ) {
                $image = wp_get_attachment_image_src($thumbnail_id, 'medium');
                if ($image ) {
                    $thumbnail_url = $image[0];
                }
            }

            // Get gallery IDs
            $gallery_ids = get_post_meta($post_id, '_custom_gallery_ids', true);
            $gallery_count = 0;

            if (! empty($gallery_ids) ) {
                $ids = explode(',', $gallery_ids);
                $gallery_count = count(array_filter($ids, 'is_numeric'));
            }

            $posts[] = array(
            'id'            => $post_id,
            'title'         => get_the_title(),
            'excerpt'       => get_the_excerpt(),
            'content'       => get_the_content(),
            'link'          => get_permalink(),
            'thumbnail'     => $thumbnail_url,
            'categories'    => $categories,
            'gallery_count' => $gallery_count,
            );
        }

        wp_reset_postdata();

        $response['success'] = true;
        $response['data']['posts'] = $posts;
        $response['data']['total'] = $query->found_posts;
    } else {
        $response['success'] = true;
        $response['data']['message'] = __('No posts found.', 'xarop');
    }

    wp_send_json($response);
}
add_action('wp_ajax_filter_posts', 'xarop_filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'xarop_filter_posts');

/**
 * AJAX handler for getting all categories
 */
function xarop_get_categories()
{
    // Verify nonce
    check_ajax_referer('xarop_nonce', 'nonce');

    $terms = get_terms(
        array(
        'taxonomy'   => 'category',
        'hide_empty' => true,
        ) 
    );

    $response = array(
    'success' => false,
    'data'    => array(
    'categories' => array(),
    ),
    );

    if (! empty($terms) && ! is_wp_error($terms) ) {
        $categories = array();

        foreach ( $terms as $term ) {
            $categories[] = array(
            'id'          => $term->term_id,
            'name'        => $term->name,
            'slug'        => $term->slug,
            'description' => $term->description,
            'count'       => $term->count,
            );
        }

        $response['success'] = true;
        $response['data']['categories'] = $categories;
    }

    wp_send_json($response);
}
add_action('wp_ajax_get_categories', 'xarop_get_categories');
add_action('wp_ajax_nopriv_get_categories', 'xarop_get_categories');

/**
 * AJAX handler for returning post cards HTML using the card.php template-part
 * Endpoint: filter_posts_html
 */
function xarop_filter_posts_html()
{
    // Verify nonce (optional, add if needed)
    // check_ajax_referer('xarop_nonce', 'nonce');

    // Minimal AJAX handler for grid posts
    $category_slug = isset($_POST['category']) && $_POST['category'] !== 'all' ? sanitize_key($_POST['category']) : '';
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $posts_per_page = get_option('posts_per_page');

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $page,
    );
    if ($category_slug) {
        $args['category_name'] = $category_slug;
    }

    $query = new WP_Query($args);
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            include locate_template('template-parts/card.php'); // Minimal post card
        }
        // Pagination
        $big = 999999999;
        echo '<div class="pagination">';
        echo paginate_links(
            array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, $page),
            'total' => $query->max_num_pages,
            'prev_text' => __('&laquo;', 'xarop'),
            'next_text' => __('&raquo;', 'xarop'),
            )
        );
        echo '</div>';
    } else {
        echo '<div class="no-results"><p>' . esc_html__('No posts found in this category.', 'xarop') . '</p></div>';
    }
    wp_reset_postdata();
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
    wp_die();
}
add_action('wp_ajax_filter_posts_html', 'xarop_filter_posts_html');
add_action('wp_ajax_nopriv_filter_posts_html', 'xarop_filter_posts_html');
