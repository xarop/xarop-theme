<?php
/**
 * Comments Template
 *
 * Plantilla de comentarios minimalista y estándar de WordPress.
 * Solo se carga cuando comments_enabled = true en theme-config.php.
 *
 * @package Xarop_Theme
 * @since   2.0.0
 */

if ( post_password_required() ) {
    return;
}
?>

<section id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>

        <h2 class="comments-title">
            <?php
            $xarop_comment_count = get_comments_number();
            printf(
                /* translators: 1: comment count, 2: post title */
                esc_html( _nx(
                    '%1$s comentario en &ldquo;%2$s&rdquo;',
                    '%1$s comentarios en &ldquo;%2$s&rdquo;',
                    $xarop_comment_count,
                    'comments title',
                    'xarop'
                ) ),
                number_format_i18n( $xarop_comment_count ),
                '<span>' . wp_kses_post( get_the_title() ) . '</span>'
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( [
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => 'xarop_comment_template',
            ] );
            ?>
        </ol>

        <?php the_comments_pagination( [
            'prev_text' => '&larr; ' . __( 'Comentarios anteriores', 'xarop' ),
            'next_text' => __( 'Comentarios siguientes', 'xarop' ) . ' &rarr;',
        ] ); ?>

    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
        <p class="no-comments"><?php esc_html_e( 'Los comentarios están cerrados.', 'xarop' ); ?></p>
    <?php endif; ?>

    <?php
    comment_form( [
        'title_reply'          => __( 'Deja un comentario', 'xarop' ),
        'title_reply_to'       => __( 'Responder a %s', 'xarop' ),
        'cancel_reply_link'    => __( 'Cancelar respuesta', 'xarop' ),
        'label_submit'         => __( 'Publicar comentario', 'xarop' ),
        'comment_notes_before' => '',
        'comment_field'        => '<p class="comment-form-comment"><label for="comment">'
            . __( 'Comentario', 'xarop' )
            . ' <span class="required">*</span></label>'
            . '<textarea id="comment" name="comment" cols="45" rows="6" required></textarea></p>',
    ] );
    ?>

</section>
