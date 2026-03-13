<?php
/**
 * Xarop Theme — Functions
 *
 * Punto de entrada principal del tema. Lee la configuración centralizada
 * de theme-config.php y activa o desactiva cada módulo en consecuencia.
 *
 * MÓDULOS CARGADOS:
 *   inc/cleanup.php       → Limpieza de WordPress (Gutenberg, emojis, embeds…)
 *   inc/comments.php      → Gestión del sistema de comentarios
 *   inc/headless.php      → Modo Headless (REST API + redirección del front)
 *   inc/cpt-generator.php → Registro automático de Custom Post Types
 *   inc/post-types.php    → CPTs específicos del proyecto
 *   inc/meta-boxes.php    → Meta boxes personalizados
 *   inc/rest-api.php      → Extensiones de la REST API
 *   inc/ajax-grid.php     → Grid AJAX paginado
 *   inc/xarop.php         → Personalización del creador (NO EDITAR)
 *
 * @package Xarop_Theme
 * @since   2.0.0
 */

if (! defined('ABSPATH') ) {
    exit;
}

define('XAROP_VERSION', '2.0.0');
define('XAROP_DIR', get_template_directory());
define('XAROP_URI', get_template_directory_uri());

// ────────────────────────────────────────────────────────────────────────────
// CONFIGURACIÓN CENTRALIZADA
// ────────────────────────────────────────────────────────────────────────────

/**
 * Carga y cachea la configuración del tema desde theme-config.php.
 * Usar siempre esta función para evitar múltiples lecturas del archivo.
 *
 * @return array
 */
function xarop_get_config()
{
    static $config = null;

    if (null === $config ) {
        $file   = XAROP_DIR . '/theme-config.php';
        $config = file_exists($file) ? include $file : [];

        $config = wp_parse_args(
            $config, [
            'colors'               => [],
            'typography'           => [],
            'gutenberg_enabled'    => false,
            'blog_enabled'         => true,
            'comments_enabled'     => false,
            'headless_mode'        => false,
            'animations_enabled'   => true,
            'custom_post_types'    => [],
            'private_site'         => false,
            'maintenance_title'    => 'Site under maintenance',
            'maintenance_message'  => 'We are working on something awesome. Check back soon!',
             ] 
        );

        // Allow modules (e.g. customizer.php) to override values.
        $config = apply_filters('xarop_config', $config);
    }

    return $config;
}


// ────────────────────────────────────────────────────────────────────────────
// SETUP DEL TEMA
// ────────────────────────────────────────────────────────────────────────────

add_action('after_setup_theme', 'xarop_setup');
function xarop_setup()
{
    $config = xarop_get_config();

    load_theme_textdomain('xarop', XAROP_DIR . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support(
        'html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
         ] 
    );
    add_theme_support(
        'custom-logo', [
        'height' => 100, 'width' => 400,
        'flex-height' => true, 'flex-width' => true,
         ] 
    );
    register_nav_menus(
        [
        'main-menu'   => __('Main menu', 'xarop'),
        'footer-menu' => __('Footer menu', 'xarop'),
         ] 
    );

    if (! $config['comments_enabled'] ) {
        remove_post_type_support('post', 'comments');
        remove_post_type_support('page', 'comments');
    }

    // Register the theme color palette in the block editor.
    if ($config['gutenberg_enabled'] && ! empty($config['colors']) ) {
        $c = $config['colors'];

        add_theme_support(
            'editor-color-palette', [
            [ 'name' => __('Primary',           'xarop'), 'slug' => 'primary',       'color' => $c['primary']       ?? '' ],
            [ 'name' => __('Primary dark',       'xarop'), 'slug' => 'primary-dark',  'color' => $c['primary_dark']  ?? '' ],
            [ 'name' => __('Primary light',      'xarop'), 'slug' => 'primary-light', 'color' => $c['primary_light'] ?? '' ],
            [ 'name' => __('Text',               'xarop'), 'slug' => 'text',          'color' => $c['text']          ?? '' ],
            [ 'name' => __('Secondary text',     'xarop'), 'slug' => 'text-light',    'color' => $c['text_light']    ?? '' ],
            [ 'name' => __('Background',         'xarop'), 'slug' => 'bg',            'color' => $c['bg']            ?? '' ],
            [ 'name' => __('Alt background',     'xarop'), 'slug' => 'bg-alt',        'color' => $c['bg_alt']        ?? '' ],
            [ 'name' => __('Border',             'xarop'), 'slug' => 'border',        'color' => $c['border']        ?? '' ],
             ] 
        );

        // Disable the full free-picker so editors only use brand colours.
        add_theme_support('disable-custom-colors');
    }
}

add_action(
    'init', function () {
        register_taxonomy_for_object_type('category', 'page');
    } 
);


// ────────────────────────────────────────────────────────────────────────────
// BLOG / ENTRADAS
// ────────────────────────────────────────────────────────────────────────────

add_action('admin_menu', 'xarop_maybe_disable_blog');
function xarop_maybe_disable_blog()
{
    $config = xarop_get_config();
    if (! $config['blog_enabled'] ) {
        remove_menu_page('edit.php');
    }
}

add_action('pre_get_posts', 'xarop_maybe_exclude_posts_from_queries');
function xarop_maybe_exclude_posts_from_queries( $query )
{
    $config = xarop_get_config();
    if (! $config['blog_enabled'] && ! is_admin() && $query->is_main_query() ) {
        if ($query->is_home() || $query->is_feed() ) {
            $query->set('post__in', [ 0 ]);
        }
    }
}


// ────────────────────────────────────────────────────────────────────────────
// VARIABLES CSS DINÁMICAS
// Inyecta colores y tipografías de theme-config.php como :root vars en el <head>.
// ────────────────────────────────────────────────────────────────────────────

add_action('wp_head', 'xarop_inject_css_variables', 1);
function xarop_inject_css_variables()
{
    $config = xarop_get_config();
    $colors = $config['colors'];
    $typo   = $config['typography'];

    $map = [
        '--color-primary'      => $colors['primary']      ?? '',
        '--color-primary-dark' => $colors['primary_dark'] ?? '',
        '--color-primary-light'=> $colors['primary_light'] ?? '',
        '--color-text'         => $colors['text']          ?? '',
        '--color-text-light'   => $colors['text_light']    ?? '',
        '--color-bg'           => $colors['bg']            ?? '',
        '--color-bg-alt'       => $colors['bg_alt']        ?? '',
        '--color-border'       => $colors['border']        ?? '',
        '--font-primary'       => $typo['font_primary']    ?? '',
        '--font-heading'       => $typo['font_heading']    ?? '',
        '--font-mono'          => $typo['font_mono']       ?? '',
        '--font-size-base'     => $typo['size_base']       ?? '',
        '--line-height-base'   => $typo['line_height']     ?? '',
    ];

    $vars = array_filter($map);
    if (empty($vars) ) { return;
    }

    echo "<style id=\"xarop-config-vars\">\n:root {\n";
    foreach ( $vars as $prop => $value ) {
        printf("\t%s: %s;\n", esc_attr($prop), esc_attr($value));
    }
    echo "}\n</style>\n";
}


// ────────────────────────────────────────────────────────────────────────────
// ENQUEUE SCRIPTS Y ESTILOS
// ────────────────────────────────────────────────────────────────────────────

add_action('wp_enqueue_scripts', 'xarop_scripts');
function xarop_scripts()
{
    $config = xarop_get_config();

    wp_enqueue_style('xarop-style', get_stylesheet_uri(), [], XAROP_VERSION);
    wp_enqueue_style('xarop-custom-style', XAROP_URI . '/assets/css/style.css', [ 'xarop-style' ], XAROP_VERSION);

    // Estilos de comentarios — solo si están activos y la página los necesita.
    if ($config['comments_enabled'] && ( is_single() || is_page() ) && comments_open() ) {
        wp_enqueue_style('xarop-comments', XAROP_URI . '/assets/css/comments.css', [ 'xarop-custom-style' ], XAROP_VERSION);
    }

    // Capa de animaciones — solo si está activa.
    if ($config['animations_enabled'] ) {
        wp_enqueue_style('xarop-animations', XAROP_URI . '/assets/css/animations.css', [ 'xarop-custom-style' ], XAROP_VERSION);
        wp_enqueue_script('xarop-animations', XAROP_URI . '/assets/js/animations.js', [], XAROP_VERSION, true);
        wp_localize_script('xarop-animations', 'xaropAnimations', [ 'autoAnimate' => true ]);
    }

    wp_enqueue_script('xarop-main', XAROP_URI . '/assets/js/main.js', [], XAROP_VERSION, true);
    wp_enqueue_script('xarop-lightbox', XAROP_URI . '/assets/js/lightbox.js', [], XAROP_VERSION, true);
    wp_localize_script(
        'xarop-main', 'xaropData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'restUrl' => rest_url(),
        'nonce'   => wp_create_nonce('xarop_nonce'),
        'version' => XAROP_VERSION,
         ] 
    );

    if ($config['comments_enabled'] && is_singular() && comments_open() && get_option('thread_comments') ) {
        wp_enqueue_script('comment-reply');
    }
}


// ────────────────────────────────────────────────────────────────────────────
// MÓDULOS
// ────────────────────────────────────────────────────────────────────────────

require_once XAROP_DIR . '/inc/customizer.php'; // must be first — registers xarop_config filter before any module calls xarop_get_config()
require_once XAROP_DIR . '/inc/loopback-fix.php'; // fix REST API & loopback timeouts in local dev
require_once XAROP_DIR . '/inc/cleanup.php';
require_once XAROP_DIR . '/inc/meta-boxes.php';
require_once XAROP_DIR . '/inc/rest-api.php';
require_once XAROP_DIR . '/inc/ajax-grid.php';
require_once XAROP_DIR . '/inc/comments.php';
require_once XAROP_DIR . '/inc/headless.php';
require_once XAROP_DIR . '/inc/private.php';
require_once XAROP_DIR . '/inc/xarop.php'; // always last
