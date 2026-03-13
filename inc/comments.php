<?php
/**
 * Gestión del Sistema de Comentarios
 *
 * Si 'comments_enabled' es false en theme-config.php, este módulo:
 *   - Cierra los comentarios en todos los post types existentes.
 *   - Elimina el soporte de comentarios y trackbacks a nivel de core.
 *   - Retira el menú "Comentarios" del administrador.
 *   - Oculta el contador de comentarios en la barra de administración.
 *   - Redirige las URLs directas a /wp-admin/edit-comments.php.
 *
 * @package Xarop_Theme
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$xarop_config = xarop_get_config();

if ( ! $xarop_config['comments_enabled'] ) {

    /**
     * Cierra los comentarios en todos los posts al guardar.
     * Garantiza que ningún post pueda tener comentarios abiertos.
     */
    add_filter( 'comments_open', '__return_false', 20, 2 );
    add_filter( 'pings_open',    '__return_false', 20, 2 );

    /**
     * Devuelve array vacío para ocultar comentarios existentes en el front.
     */
    add_filter( 'comments_array', '__return_empty_array', 10, 2 );

    /**
     * Elimina soporte de comentarios y trackbacks en todos los post types.
     * Se ejecuta en admin_init para capturar todos los CPTs registrados.
     */
    add_action( 'admin_init', 'xarop_strip_comment_support' );
    function xarop_strip_comment_support() {
        foreach ( get_post_types() as $post_type ) {
            if ( post_type_supports( $post_type, 'comments' ) ) {
                remove_post_type_support( $post_type, 'comments' );
            }
            if ( post_type_supports( $post_type, 'trackbacks' ) ) {
                remove_post_type_support( $post_type, 'trackbacks' );
            }
        }
    }

    /**
     * Elimina el menú "Comentarios" del sidebar del administrador.
     */
    add_action( 'admin_menu', 'xarop_remove_comments_menu' );
    function xarop_remove_comments_menu() {
        remove_menu_page( 'edit-comments.php' );
    }

    /**
     * Elimina el elemento "Comentarios" de la barra de administración superior.
     */
    add_action( 'admin_bar_menu', 'xarop_remove_comments_adminbar', 999 );
    function xarop_remove_comments_adminbar( $wp_admin_bar ) {
        $wp_admin_bar->remove_node( 'comments' );
    }

    /**
     * Redirige accesos directos a la pantalla de comentarios del admin.
     * Evita que alguien llegue a edit-comments.php por URL directa.
     */
    add_action( 'admin_init', 'xarop_redirect_comments_admin' );
    function xarop_redirect_comments_admin() {
        global $pagenow;
        if ( 'edit-comments.php' === $pagenow ) {
            wp_safe_redirect( admin_url() );
            exit;
        }
    }

    /**
     * Elimina el widget de comentarios recientes del dashboard.
     */
    add_action( 'admin_init', 'xarop_remove_comments_dashboard_widget' );
    function xarop_remove_comments_dashboard_widget() {
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
    }

    /**
     * Elimina el enlace RSS de comentarios del <head>.
     */
    add_action( 'init', 'xarop_remove_comments_feed' );
    function xarop_remove_comments_feed() {
        remove_action( 'wp_head', 'feed_links', 2 );
        add_action( 'wp_head', 'xarop_readd_post_feed' );
    }

    /**
     * Re-añade solo el feed de entradas (no el de comentarios).
     */
    function xarop_readd_post_feed() {
        // Añadimos manualmente solo el feed de posts, sin el de comentarios.
        $feed_url = get_feed_link();
        if ( $feed_url ) {
            echo '<link rel="alternate" type="' . esc_attr( feed_content_type() ) . '" title="'
                . esc_attr( get_bloginfo( 'name' ) ) . ' &raquo; '
                . esc_attr__( 'Feed', 'xarop' )
                . '" href="' . esc_url( $feed_url ) . "\" />\n";
        }
    }
}


/**
 * Callback para el template de comentarios individuales.
 * Se usa solo cuando comments_enabled = true.
 *
 * @param WP_Comment $comment Objeto del comentario.
 * @param array      $args    Argumentos de wp_list_comments().
 * @param int        $depth   Profundidad de anidamiento.
 */
function xarop_comment_template( $comment, $args, $depth ) {
    ?>
    <li <?php comment_class( 'comment-item' ); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment-body">

            <div class="comment-author">
                <?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
                <div class="comment-meta">
                    <b class="fn"><?php comment_author_link(); ?></b>
                    <time datetime="<?php comment_time( 'c' ); ?>">
                        <a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
                            <?php
                            printf(
                                /* translators: 1: date, 2: time */
                                esc_html__( '%1$s a las %2$s', 'xarop' ),
                                esc_html( get_comment_date( '', $comment ) ),
                                esc_html( get_comment_time( '', false, false, $comment ) )
                            );
                            ?>
                        </a>
                    </time>
                </div>
            </div>

            <?php if ( '0' === $comment->comment_approved ) : ?>
                <p class="comment-awaiting-moderation">
                    <?php esc_html_e( 'Tu comentario está pendiente de moderación.', 'xarop' ); ?>
                </p>
            <?php endif; ?>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <div class="comment-reply">
                <?php
                comment_reply_link( array_merge( $args, [
                    'add_below' => 'comment',
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before'    => '',
                    'after'     => '',
                ] ) );
                ?>
            </div>

        </article>
    </li>
    <?php
}
