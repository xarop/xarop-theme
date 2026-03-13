<?php
/**
 * Theme Cleanup Functions
 *
 * Remove Gutenberg, emojis, embeds, core block CSS, and disable Tags support
 *
 * @package xarop
 * @since   1.0.0
 */

// Salir si se accede directamente
if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Gutenberg: activar o desactivar según theme-config.php
 * gutenberg_enabled = false → desactiva el editor de bloques y sus estilos.
 * gutenberg_enabled = true  → comportamiento WordPress por defecto.
 */
$xarop_cleanup_config = xarop_get_config();

if (! $xarop_cleanup_config['gutenberg_enabled'] ) {

    function xarop_disable_gutenberg()
    {
        add_filter('use_block_editor_for_post',      '__return_false', 10);
        add_filter('use_block_editor_for_post_type', '__return_false', 10);
    }
    add_action('init', 'xarop_disable_gutenberg');

    function xarop_remove_gutenberg_styles()
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles');
        wp_dequeue_style('classic-theme-styles');
    }
    add_action('wp_enqueue_scripts', 'xarop_remove_gutenberg_styles', 100);
}

/**
 * Eliminar scripts y estilos de emojis
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
 * Eliminar prefetch DNS de emojis
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
 * Eliminar plugin de emojis de TinyMCE
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
 * Eliminar la funcionalidad de embeds
 */
function xarop_disable_embeds()
{
    // Eliminar el endpoint REST API
    remove_action('rest_api_init', 'wp_oembed_register_route');

    // Desactivar la detección automática de oEmbed
    add_filter('embed_oembed_discover', '__return_false');

    // No filtrar los resultados de oEmbed
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

    // Eliminar los enlaces de descubrimiento oEmbed
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Eliminar el JavaScript específico de oEmbed del front y del admin
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // Eliminar todas las reglas de reescritura de embeds
    add_filter('rewrite_rules_array', 'xarop_disable_embeds_rewrites');
}
add_action('init', 'xarop_disable_embeds', 9999);

/**
 * Eliminar las reglas de reescritura de embeds
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
 * Eliminar las variables de consulta de embeds
 */
function xarop_disable_embeds_query_vars( $vars )
{
    $vars = array_diff($vars, array( 'embed' ));
    return $vars;
}
add_filter('query_vars', 'xarop_disable_embeds_query_vars');

/**
 * Desactivar el soporte de etiquetas globalmente
 */
function xarop_disable_tags()
{
    // Desregistrar la taxonomía post_tag de las entradas
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'xarop_disable_tags');

/**
 * Eliminar etiquetas de cabecera innecesarias
 */
function xarop_cleanup_head()
{
    // Eliminar el manifiesto de Windows Live Writer
    remove_action('wp_head', 'wlwmanifest_link');
    
    // Eliminar el enlace RSD
    remove_action('wp_head', 'rsd_link');
    
    // Eliminar la versión de WordPress
    remove_action('wp_head', 'wp_generator');
    
    // Eliminar el shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
    
    // Eliminar el enlace REST API
    remove_action('wp_head', 'rest_output_link_wp_head');
    
    // Eliminar los enlaces de feed
    remove_action('wp_head', 'feed_links_extra', 3);
}
add_action('init', 'xarop_cleanup_head');

/**
 * Eliminar jQuery Migrate
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
 * Eliminar las versiones de query string de los recursos estáticos
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


// El sistema de comentarios es gestionado por inc/comments.php
// mediante el valor 'comments_enabled' de theme-config.php.
