<?php
/**
 * Theme Cleanup Functions
 *
 * Remove Gutenberg, emojis, embeds, core block CSS, and disable Tags support
 *
 * @package xarop
 * @since   1.0.0
 */

// Exit if accessed directly
if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Remove Gutenberg editor
 */
function xarop_disable_gutenberg()
{
    // Disable for all post types
    add_filter('use_block_editor_for_post', '__return_false', 10);
    add_filter('use_block_editor_for_post_type', '__return_false', 10);
}
add_action('init', 'xarop_disable_gutenberg');

/**
 * Remove Gutenberg styles
 */
function xarop_remove_gutenberg_styles()
{
    // Remove block library CSS
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    
    // Remove inline global styles
    wp_dequeue_style('global-styles');
    
    // Remove classic themes styles
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'xarop_remove_gutenberg_styles', 100);

/**
 * Remove emoji scripts and styles
 */
function xarop_disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'xarop_disable_emojis');

/**
 * Remove emoji DNS prefetch
 */
function xarop_disable_emoji_dns_prefetch( $urls, $relation_type )
{
    if ('dns-prefetch' === $relation_type ) {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
        $urls = array_diff($urls, array( $emoji_svg_url ));
    }
    return $urls;
}
add_filter('wp_resource_hints', 'xarop_disable_emoji_dns_prefetch', 10, 2);

/**
 * Remove TinyMCE emoji plugin
 */
function xarop_disable_emoji_tinymce( $plugins )
{
    if (is_array($plugins) ) {
        return array_diff($plugins, array( 'wpemoji' ));
    }
    return array();
}
add_filter('tiny_mce_plugins', 'xarop_disable_emoji_tinymce');

/**
 * Remove embeds functionality
 */
function xarop_disable_embeds()
{
    // Remove the REST API endpoint
    remove_action('rest_api_init', 'wp_oembed_register_route');

    // Turn off oEmbed auto discovery
    add_filter('embed_oembed_discover', '__return_false');

    // Don't filter oEmbed results
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

    // Remove oEmbed discovery links
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove oEmbed-specific JavaScript from the front-end and back-end
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // Remove all embeds rewrite rules
    add_filter('rewrite_rules_array', 'xarop_disable_embeds_rewrites');
}
add_action('init', 'xarop_disable_embeds', 9999);

/**
 * Remove embed rewrite rules
 */
function xarop_disable_embeds_rewrites( $rules )
{
    foreach ( $rules as $rule => $rewrite ) {
        if (false !== strpos($rewrite, 'embed=true') ) {
            unset($rules[ $rule ]);
        }
    }
    return $rules;
}

/**
 * Remove embed query vars
 */
function xarop_disable_embeds_query_vars( $vars )
{
    $vars = array_diff($vars, array( 'embed' ));
    return $vars;
}
add_filter('query_vars', 'xarop_disable_embeds_query_vars');

/**
 * Disable Tags support globally
 */
function xarop_disable_tags()
{
    // Unregister post_tag taxonomy from posts
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'xarop_disable_tags');

/**
 * Remove unnecessary header tags
 */
function xarop_cleanup_head()
{
    // Remove Windows Live Writer manifest
    remove_action('wp_head', 'wlwmanifest_link');
    
    // Remove RSD link
    remove_action('wp_head', 'rsd_link');
    
    // Remove WordPress version
    remove_action('wp_head', 'wp_generator');
    
    // Remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
    
    // Remove REST API link
    remove_action('wp_head', 'rest_output_link_wp_head');
    
    // Remove feed links (we added them back selectively in setup)
    remove_action('wp_head', 'feed_links_extra', 3);
}
add_action('init', 'xarop_cleanup_head');

/**
 * Remove jQuery Migrate
 */
function xarop_remove_jquery_migrate( $scripts )
{
    if (! is_admin() && isset($scripts->registered['jquery']) ) {
        $script = $scripts->registered['jquery'];
        if ($script->deps ) {
            $script->deps = array_diff($script->deps, array( 'jquery-migrate' ));
        }
    }
}
add_action('wp_default_scripts', 'xarop_remove_jquery_migrate');

/**
 * Remove version query strings from static resources
 */
function xarop_remove_script_version( $src )
{
    if (strpos($src, 'ver=') ) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'xarop_remove_script_version', 9999);
add_filter('script_loader_src', 'xarop_remove_script_version', 9999);


// Remove Gutenberg block library CSS from the front-end
function xarop_remove_block_library_css()
{
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
}
add_action('wp_enqueue_scripts', 'xarop_remove_block_library_css', 100);    


// Disable comments
function xarop_disable_comments()
{
    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type ) {
        if (post_type_supports($post_type, 'comments') ) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'xarop_disable_comments');
