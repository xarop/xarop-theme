<?php
/**
 * Xarop Theme — Centralized Configuration
 *
 * Edit this file to change the visual identity, or enable/disable
 * complete theme features in seconds.
 *
 * ──────────────────────────────────────────────────────────────────────────
 * QUICK GUIDE — Change the visual identity in 1 minute:
 *   1. Modify the 'colors' and 'typography' values in this file.
 *   2. Save. The :root CSS variables are regenerated automatically.
 *   3. No compiling, no cache to clear. Done.
 * ──────────────────────────────────────────────────────────────────────────
 *
 * @package Xarop_Theme
 * @since   2.0.0
 */

if (! defined('ABSPATH') ) {
    exit;
}

return [

    // ────────────────────────────────────────────────────────────────────────
    // VISUAL IDENTITY
    // These values are injected as CSS variables in the <head> of the theme.
    // You can use any valid CSS value (hex, rgb, hsl, rem, px…).
    // ────────────────────────────────────────────────────────────────────────
    'colors' => [
        'primary'       => '#ee2455',   // Main brand color
        'primary_dark'  => '#b01a3e',   // Dark variant (hover, active)
        'primary_light' => '#ff5c7a',   // Light variant (highlights)
        'text'          => '#1e293b',   // Main text
        'text_light'    => '#64748b',   // Secondary text / subtitles
        'bg'            => '#ffffff',   // Base background
        'bg_alt'        => '#f8fafc',   // Alternative background (sections)
        'border'        => '#e2e8f0',   // Borders and dividers
    ],

    'typography' => [
        // Fonts: accepts Google Fonts, system-fonts or any CSS stack.
        // If using Google Fonts, add the @import in assets/css/style.css.
        'font_primary' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
        'font_heading' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
        'font_mono'    => "'SF Mono', 'Cascadia Code', 'Courier New', monospace",
        'size_base'    => '16px',
        'line_height'  => '1.6',
    ],

    // ────────────────────────────────────────────────────────────────────────
    // FUNCTIONAL MODULES
    // true  → module active
    // false → module completely disabled (no residual HTML output)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Block Editor (Gutenberg)
     * false → disables the visual editor and removes its stylesheets from the front.
     */
    'gutenberg_enabled' => true,

    /**
     * Blog / Posts
     * false → hides the "Posts" menu from the admin and disables the post CPT.
     *         Ideal for corporate sites or portfolios without a blog.
     */
    'blog_enabled' => true,

    /**
     * Comments System
     * false → removes comment support from all post types,
     *          removes the "Comments" menu from admin and the admin bar.
     */
    'comments_enabled' => true,

    /**
     * Headless Mode
     * true  → redirects all front-end traffic to /wp-admin,
     *          cleans the <head> and enables additional REST endpoints.
     *          Use this mode when the front is handled by React/Next.js.
     * false → standard WordPress behaviour.
     */
    'headless_mode' => false,

    /**
     * Animations Layer
     * true  → loads animations.css and animations.js (IntersectionObserver).
     *          Elements with class .animate-on-scroll animate when entering
     *          the viewport.
     * false → zero animation overhead. Ideal for ultra-fast sites.
     */
    'animations_enabled' => true,

    /**
     * Private Site / Maintenance Mode
     * true  → only logged-in users can access the front-end.
     *          All other visitors see a maintenance page and cannot browse the site.
     * false → site is publicly accessible (normal behaviour).
     *
     * Customise the message shown to visitors in 'maintenance_title' and
     * 'maintenance_message' below.
     */
    'private_site'          => false,
    'maintenance_title'     => 'Site under maintenance',
    'maintenance_message'   => 'We are working on something awesome. Check back soon!',

    // ────────────────────────────────────────────────────────────────────────
    // CUSTOM POST TYPE GENERATOR (CPT)
    // Add as many CPTs as you need. Each entry accepts:
    //   slug     (string) → unique identifier, lowercase, no spaces
    //   singular (string) → singular name (e.g. "Project")
    //   plural   (string) → plural name   (e.g. "Projects")
    //   icon     (string) → dashicon or image URL (e.g. "dashicons-portfolio")
    //   public   (bool)   → whether it appears on the front-end (default true)
    //   rest     (bool)   → whether it is exposed in the REST API (default true)
    //
    // Dashicons reference: https://developer.wordpress.org/resource/dashicons/
    // ────────────────────────────────────────────────────────────────────────
    'custom_post_types' => [
        // Active example — uncomment or add your own:
        // [
        //     'slug'     => 'project',
        //     'singular' => __( 'Project', 'xarop' ),
        //     'plural'   => __( 'Projects', 'xarop' ),
        //     'icon'     => 'dashicons-portfolio',
        //     'public'   => true,
        //     'rest'     => true,
        // ],
        // [
        //     'slug'     => 'service',
        //     'singular' => __( 'Service', 'xarop' ),
        //     'plural'   => __( 'Services', 'xarop' ),
        //     'icon'     => 'dashicons-admin-tools',
        //     'public'   => true,
        //     'rest'     => true,
        // ],
    ],

];
