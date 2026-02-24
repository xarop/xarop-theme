<?php
/**
 * Xarop Theme Functions
 *
 * @package Xarop_Theme
 * @since   1.0.0
 */

// Exit if accessed directly
if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Habilitar CORS para desarrollo local headless
 */
// require_once XAROP_DIR . '/inc/cors.php';


/**
 * Define theme constants
 */
define('XAROP_VERSION', '1.0.0');
define('XAROP_DIR', get_template_directory());
define('XAROP_URI', get_template_directory_uri());

/**
 * Theme setup
 */
function xarop_setup()
{
    // Make theme available for translation
    load_theme_textdomain('xarop', XAROP_DIR . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Enable HTML5 markup
    add_theme_support(
        'html5',
        array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        )
    );

    // Add custom logo support
    add_theme_support(
        'custom-logo',
        array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        )
    );

    // Register navigation menus
    register_nav_menus(
        array(
        'main-menu'   => __('Main Menu', 'xarop'),
        'footer-menu' => __('Footer Menu', 'xarop'),

        )
    );

    
}
add_action('after_setup_theme', 'xarop_setup');


// Register category taxonomy for both 'project' and 'page' post types
add_action(
    'init', function () {
        register_taxonomy_for_object_type('category', 'page');
    }
);
    
/**
 * Enqueue scripts and styles
 */
function xarop_scripts()
{

    // Enqueue xarop-theme custom CSS
    wp_enqueue_style(
        'xarop-custom-style',
        XAROP_URI . '/assets/css/style.css',
        array('xarop-style'),
        XAROP_VERSION
    );

      // Enqueue main stylesheet
    wp_enqueue_style(
        'xarop-style',
        get_stylesheet_uri(),
        array(),
        XAROP_VERSION
    );


    // Enqueue main JavaScript
    wp_enqueue_script(
        'xarop-main',
        XAROP_URI . '/assets/js/main.js',
        array(),
        XAROP_VERSION,
        true
    );

    // Enqueue lightbox JavaScript
    wp_enqueue_script(
        'xarop-lightbox',
        XAROP_URI . '/assets/js/lightbox.js',
        array(),
        XAROP_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script(
        'xarop-main',
        'xaropData',
        array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'restUrl' => rest_url(),
        'nonce'   => wp_create_nonce('xarop_nonce'),
        )
    );
}
    add_action('wp_enqueue_scripts', 'xarop_scripts');

    /**
     * Require modular files
     */
    
    require_once XAROP_DIR . '/inc/cleanup.php';
    require_once XAROP_DIR . '/inc/post-types.php';
    require_once XAROP_DIR . '/inc/meta-boxes.php';
    require_once XAROP_DIR . '/inc/rest-api.php';
    require_once XAROP_DIR . '/inc/ajax-grid.php';
    require_once XAROP_DIR . '/inc/xarop.php';
