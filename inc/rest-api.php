<?php
/**
 * Personalizaciones de la REST API
 *
 * Expone IDs de galería personalizada y categorías compartidas en la REST API
 *
 * @package xarop
 * @since   1.0.0
 */

// Salir si se accede directamente
if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Añadir campo de galería personalizada a la REST API para Páginas
 */
function xarop_register_gallery_field_page()
{
    register_rest_field(
        'page',
        'custom_gallery',
        array(
        'get_callback'    => 'xarop_get_gallery_data',
        'update_callback' => null,
        'schema'          => array(
                'description' => __('IDs y URLs de imágenes de la galería personalizada', 'xarop'),
                'type'        => 'object',
        ),
        )
    );
}
add_action('rest_api_init', 'xarop_register_gallery_field_page');

/**
 * Añadir campo de galería personalizada a la REST API para Entradas
 */
function xarop_register_gallery_field_post()
{
    register_rest_field(
        'post',
        'custom_gallery',
        array(
        'get_callback'    => 'xarop_get_gallery_data',
        'update_callback' => null,
        'schema'          => array(
                'description' => __('IDs y URLs de imágenes de la galería personalizada', 'xarop'),
                'type'        => 'object',
        ),
        )
    );
}
add_action('rest_api_init', 'xarop_register_gallery_field_post');

/**
 * Callback para obtener datos de galería
 */
function xarop_get_gallery_data( $object )
{
    $post_id = $object['id'];
    $gallery_ids = get_post_meta($post_id, '_custom_gallery_ids', true);

    if (empty($gallery_ids) ) {
        return array(
        'ids'    => array(),
        'images' => array(),
        );
    }

    $ids = explode(',', $gallery_ids);
    $ids = array_filter($ids, 'is_numeric');
    $images = array();

    foreach ( $ids as $id ) {
        $attachment = wp_get_attachment_image_src($id, 'full');
        $thumbnail = wp_get_attachment_image_src($id, 'thumbnail');
        $medium = wp_get_attachment_image_src($id, 'medium');
        $large = wp_get_attachment_image_src($id, 'large');

        if ($attachment ) {
            $images[] = array(
            'id'        => intval($id),
            'full'      => array(
            'url'    => $attachment[0],
            'width'  => $attachment[1],
            'height' => $attachment[2],
            ),
            'large'     => $large ? array(
            'url'    => $large[0],
            'width'  => $large[1],
            'height' => $large[2],
            ) : null,
            'medium'    => $medium ? array(
            'url'    => $medium[0],
            'width'  => $medium[1],
            'height' => $medium[2],
            ) : null,
            'thumbnail' => $thumbnail ? array(
            'url'    => $thumbnail[0],
            'width'  => $thumbnail[1],
            'height' => $thumbnail[2],
            ) : null,
            'alt'       => get_post_meta($id, '_wp_attachment_image_alt', true),
            'caption'   => wp_get_attachment_caption($id),
            'title'     => get_the_title($id),
            );
        }
    }

    return array(
    'ids'    => array_map('intval', $ids),
    'images' => $images,
    );
}

/**
 * Add shared categories to REST API for Pages
 */
function xarop_register_categories_field_page()
{
    register_rest_field(
        'page',
        'shared_categories',
        array(
        'get_callback'    => 'xarop_get_shared_categories',
        'update_callback' => null,
        'schema'          => array(
                'description' => __('Shared categories taxonomy terms', 'xarop'),
                'type'        => 'array',
        ),
        )
    );
}
add_action('rest_api_init', 'xarop_register_categories_field_page');

/**
 * Add shared categories to REST API for posts
 */
function xarop_register_categories_field_post()
{
    register_rest_field(
        'post',
        'shared_categories',
        array(
        'get_callback'    => 'xarop_get_shared_categories',
        'update_callback' => null,
        'schema'          => array(
                'description' => __('Shared categories taxonomy terms', 'xarop'),
                'type'        => 'array',
        ),
        )
    );
}
add_action('rest_api_init', 'xarop_register_categories_field_post');

/**
 * Get shared categories callback
 */
function xarop_get_shared_categories( $object )
{
    $post_id = $object['id'];
    $terms = get_the_terms($post_id, 'category');

    if (empty($terms) || is_wp_error($terms) ) {
        return array();
    }

    $categories = array();

    foreach ( $terms as $term ) {
        $categories[] = array(
        'id'          => $term->term_id,
        'name'        => $term->name,
        'slug'        => $term->slug,
        'description' => $term->description,
        'count'       => $term->count,
        'link'        => get_term_link($term),
        );
    }

    return $categories;
}

/**
 * Register custom REST route for filtered posts
 */
function xarop_register_custom_routes()
{
    register_rest_route(
        'xarop/v1',
        '/filtered-posts',
        array(
        'methods'             => 'GET',
        'callback'            => 'xarop_get_filtered_posts',
        'permission_callback' => '__return_true',
        'args'                => array(
                'category' => array(
                    'required'          => false,
                    'validate_callback' => function ( $param ) {
                        return is_numeric($param) || $param === 'all';
                    },
                ),
                'per_page' => array(
                    'required'          => false,
                    'default'           => 12,
                    'validate_callback' => function ( $param ) {
                        return is_numeric($param) && $param > 0;
                    },
                ),
        ),
        )
    );

    register_rest_route(
        'xarop/v1',
        '/menus',
        array(
            'methods'             => 'GET',
            'callback'            => 'xarop_get_menus',
            'permission_callback' => '__return_true',
        )
    );
}
add_action('rest_api_init', 'xarop_register_custom_routes');

/**
 * Get menus callback
 */
function xarop_get_menus()
{
    $locations = get_nav_menu_locations();
    $menus = array();

    foreach ($locations as $location => $menu_id) {
        $menu_items = wp_get_nav_menu_items($menu_id);
        if ($menu_items) {
            $items = array();
            foreach ($menu_items as $item) {
                if ($item->menu_item_parent == 0) {
                    $items[] = array(
                        'id'    => $item->ID,
                        'title' => $item->title,
                        'url'   => str_replace(home_url(), '', $item->url), // Relative URLs
                        'children' => xarop_get_menu_children($menu_items, $item->ID),
                    );
                }
            }
            $menus[$location] = $items;
        }
    }

    return rest_ensure_response($menus);
}

/**
 * Helper to get menu children
 */
function xarop_get_menu_children($all_items, $parent_id)
{
    $children = array();
    foreach ($all_items as $item) {
        if ($item->menu_item_parent == $parent_id) {
            $children[] = array(
                'id'    => $item->ID,
                'title' => $item->title,
                'url'   => str_replace(home_url(), '', $item->url),
                'children' => xarop_get_menu_children($all_items, $item->ID),
            );
        }
    }
    return $children;
}

/**
 * Callback para obtener entradas filtradas
 */
function xarop_get_filtered_posts( $request )
{
    $category = $request->get_param('category');
    $per_page = $request->get_param('per_page');

    $args = array(
    'post_type'      => 'post',
    'posts_per_page' => $per_page,
    'post_status'    => 'publish',
    );

    // Añadir filtro de categoría si se especifica
    if ($category && $category !== 'all' ) {
        $args['tax_query'] = array(
        array(
        'taxonomy' => 'category',
        'field'    => 'term_id',
        'terms'    => intval($category),
        ),
        );
    }

    $query = new WP_Query($args);
    $posts = array();

    if ($query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();

            // Obtener categorías
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

            // Obtener imagen destacada
            $thumbnail_id = get_post_thumbnail_id($post_id);
            $thumbnail = null;

            if ($thumbnail_id ) {
                $image = wp_get_attachment_image_src($thumbnail_id, 'medium');
                if ($image ) {
                    $thumbnail = array(
                     'url'    => $image[0],
                     'width'  => $image[1],
                     'height' => $image[2],
                    );
                }
            }

            $posts[] = array(
            'id'         => $post_id,
            'title'      => get_the_title(),
            'excerpt'    => get_the_excerpt(),
            'link'       => get_permalink(),
            'thumbnail'  => $thumbnail,
            'categories' => $categories,
            );
        }
        wp_reset_postdata();
    }

    return rest_ensure_response(
        array(
        'posts' => $posts,
        'total'    => $query->found_posts,
        ) 
    );
}
