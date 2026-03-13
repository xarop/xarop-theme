<?php
/**
 * Modo Headless
 *
 * Cuando 'headless_mode' es true en theme-config.php:
 *   - Redirige todo el tráfico del front-end al panel de administración.
 *   - Limpia el <head> de scripts y estilos innecesarios.
 *   - Desactiva los scripts del front-end del tema.
 *   - Amplía la REST API con cabeceras CORS para el front desacoplado.
 *   - Habilita el archivo inc/cors.php de soporte.
 *
 * ─────────────────────────────────────────────────────────────────────────
 * ARQUITECTURA HEADLESS CON XAROP
 *
 * WordPress actúa como CMS headless (solo back-end):
 *
 *   ┌─────────────────┐         REST API          ┌──────────────────────┐
 *   │  WordPress      │  ──────────────────────▶  │  Front-end externo   │
 *   │  (xarop-theme)  │  /wp-json/wp/v2/          │  React / Next.js /   │
 *   │  Headless Mode  │  /wp-json/xarop/v1/       │  Vite (_headless/)   │
 *   └─────────────────┘                           └──────────────────────┘
 *
 * El front-end desacoplado vive en:
 *   → themes/xarop-theme/_headless/   (Vite + Vanilla JS)
 *   → o en un repo Next.js externo
 *
 * Endpoints clave de la REST API de Xarop:
 *   GET /wp-json/wp/v2/pages          → Páginas (con _embed para imágenes)
 *   GET /wp-json/wp/v2/posts          → Entradas
 *   GET /wp-json/wp/v2/categories     → Categorías
 *   GET /wp-json/xarop/v1/menus       → Menús de navegación
 *   GET /wp-json/xarop/v1/filtered-posts?category=ID&per_page=12
 *   GET /wp-json                       → Info general del sitio
 *
 * Campos extra disponibles en posts y páginas:
 *   custom_gallery     → { ids: [], images: [{id, full, medium, thumbnail}] }
 *   shared_categories  → [{ id, name, slug, description, count, link }]
 * ─────────────────────────────────────────────────────────────────────────
 *
 * @package Xarop_Theme
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$xarop_config = xarop_get_config();

if ( $xarop_config['headless_mode'] ) {

    /**
     * Redirige el tráfico del front-end al administrador.
     * Los visitantes anónimos reciben un 301 al home del admin.
     * Los usuarios logueados son redirigidos al dashboard.
     */
    add_action( 'template_redirect', 'xarop_headless_redirect' );
    function xarop_headless_redirect() {
        // Permitir acceso a la REST API, wp-cron y feeds.
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return;
        }
        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return;
        }

        if ( is_user_logged_in() ) {
            wp_safe_redirect( admin_url() );
        } else {
            wp_safe_redirect( wp_login_url() );
        }
        exit;
    }

    /**
     * Limpia el <head> de recursos del front-end.
     * En modo headless no necesitamos cargar nada en el navegador.
     */
    add_action( 'wp_enqueue_scripts', 'xarop_headless_dequeue_all', 9999 );
    function xarop_headless_dequeue_all() {
        global $wp_scripts, $wp_styles;
        // Elimina todos los scripts y estilos del front.
        $wp_scripts->queue = [];
        $wp_styles->queue  = [];
    }

    /**
     * Limpieza adicional del <head> en modo headless.
     * Elimina meta tags y links no necesarios para una API pura.
     */
    add_action( 'init', 'xarop_headless_cleanup_head' );
    function xarop_headless_cleanup_head() {
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
    }

    /**
     * Añade cabeceras CORS a las respuestas de la REST API.
     * Permite que el front-end externo (cualquier origen) consuma la API.
     *
     * ⚠️  En producción, reemplaza '*' por el dominio exacto del front:
     *      header( 'Access-Control-Allow-Origin: https://tu-frontend.com' );
     */
    add_action( 'rest_api_init', 'xarop_headless_cors_headers' );
    function xarop_headless_cors_headers() {
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

        add_filter( 'rest_pre_serve_request', function ( $value ) {
            // Permite cualquier origen en desarrollo. Cambia a dominio específico en producción.
            header( 'Access-Control-Allow-Origin: *' );
            header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS' );
            header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
            header( 'Access-Control-Allow-Credentials: true' );
            return $value;
        } );
    }

    /**
     * Expone la URL de la REST API en el <head> del admin para referencia.
     * Facilita la configuración del front-end al ver las URLs desde el admin.
     */
    add_action( 'admin_notices', 'xarop_headless_admin_notice' );
    function xarop_headless_admin_notice() {
        $screen = get_current_screen();
        if ( $screen && 'dashboard' === $screen->id ) {
            $rest_url    = esc_url( rest_url() );
            $custom_url  = esc_url( rest_url( 'xarop/v1/' ) );
            $headless_dir = esc_html( get_template_directory() . '/_headless' );
            ?>
            <div class="notice notice-info is-dismissible">
                <h3>🚀 <?php esc_html_e( 'Xarop — Modo Headless Activo', 'xarop' ); ?></h3>
                <p><?php esc_html_e( 'El front-end nativo de WordPress está desactivado. El contenido se sirve exclusivamente a través de la REST API.', 'xarop' ); ?></p>
                <p>
                    <strong><?php esc_html_e( 'API Base:', 'xarop' ); ?></strong>
                    <code><?php echo $rest_url; ?></code>
                </p>
                <p>
                    <strong><?php esc_html_e( 'Endpoints Xarop:', 'xarop' ); ?></strong>
                    <code><?php echo $custom_url; ?>menus</code> ·
                    <code><?php echo $custom_url; ?>filtered-posts</code>
                </p>
                <p>
                    <strong><?php esc_html_e( 'Front-end Vite:', 'xarop' ); ?></strong>
                    <code><?php echo $headless_dir; ?></code> →
                    <em><?php esc_html_e( 'cd _headless && npm install && npm run dev', 'xarop' ); ?></em>
                </p>
            </div>
            <?php
        }
    }
}
