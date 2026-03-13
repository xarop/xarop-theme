<?php
/**
 * Meta Boxes Personalizados
 *
 * Meta Box nativo de Galería usando la Biblioteca de Medios de WordPress
 *
 * @package xarop
 * @since   1.0.0
 */

// Salir si se accede directamente
if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Registrar el meta box de galería personalizada
 */
function xarop_add_gallery_meta_box()
{
    $post_types = array( 'page', 'post' );

    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'xarop_gallery',
            __('Galería personalizada', 'xarop'),
            'xarop_gallery_meta_box_callback',
            $post_type,
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'xarop_add_gallery_meta_box');

/**
 * Callback del meta box de galería
 */
function xarop_gallery_meta_box_callback( $post )
{
    // Añadir nonce de seguridad
    wp_nonce_field('xarop_gallery_nonce', 'xarop_gallery_nonce_field');

    // Obtener los IDs actuales de la galería
    $gallery_ids = get_post_meta($post->ID, '_custom_gallery_ids', true);
    $gallery_ids = ! empty($gallery_ids) ? $gallery_ids : '';

    ?>
    <div class="gallery-wrapper">
        <input type="hidden" id="custom_gallery_ids" name="custom_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>" />
        
        <div id="gallery-preview" class="gallery-preview">
    <?php
    if (! empty($gallery_ids) ) {
        $ids = explode(',', $gallery_ids);
        foreach ( $ids as $id ) {
            $image_url = wp_get_attachment_image_url($id, 'thumbnail');
            if ($image_url ) {
                echo '<div class="gallery-image" data-id="' . esc_attr($id) . '">';
                echo '<img src="' . esc_url($image_url) . '" />';
                echo '<span class="remove-image" title="' . esc_attr__('Eliminar', 'xarop') . '">&times;</span>';
                echo '</div>';
            }
        }
    }
    ?>
        </div>

        <p>
            <button type="button" class="button button-primary" id="add-gallery-images">
                <?php esc_html_e('Añadir imágenes', 'xarop'); ?>
            </button>
            <button type="button" class="button" id="clear-gallery-images">
                <?php esc_html_e('Borrar todo', 'xarop'); ?>
            </button>
        </p>

        <p class="description">
    <?php esc_html_e('Selecciona varias imágenes de la biblioteca de medios para crear una galería.', 'xarop'); ?>
        </p>
    </div>

    <style>
        .gallery-wrapper {
            padding: 10px 0;
        }
        .gallery-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
            min-height: 50px;
            border: 2px dashed #ddd;
            padding: 10px;
            border-radius: 4px;
        }
        .gallery-image {
            position: relative;
            cursor: move;
        }
        .gallery-image img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            display: block;
        }
        .gallery-image .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3232;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }
        .gallery-image .remove-image:hover {
            background: #a00;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var galleryFrame;
        var galleryIds = $('#custom_gallery_ids').val().split(',').filter(Boolean);

        // Botón añadir imágenes
        $('#add-gallery-images').on('click', function(e) {
            e.preventDefault();

            // Si el marco de medios ya existe, reabrirlo
            if (galleryFrame) {
                galleryFrame.open();
                return;
            }

            // Crear el marco de medios
            galleryFrame = wp.media({
                title: '<?php echo esc_js(__('Seleccionar imágenes de la galería', 'xarop')); ?>',
                button: {
                    text: '<?php echo esc_js(__('Añadir a la galería', 'xarop')); ?>'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });

            // Cuando se seleccionan imágenes
            galleryFrame.on('select', function() {
                var selection = galleryFrame.state().get('selection');
                
                selection.each(function(attachment) {
                    attachment = attachment.toJSON();
                    
                    // Añadir al array si no está ya
                    if (galleryIds.indexOf(attachment.id.toString()) === -1) {
                        galleryIds.push(attachment.id);
                        
                        // Añadir vista previa
                        var imageHtml = '<div class="gallery-image" data-id="' + attachment.id + '">' +
                            '<img src="' + attachment.sizes.thumbnail.url + '" />' +
                            '<span class="remove-image" title="<?php echo esc_js(__('Eliminar', 'xarop')); ?>">&times;</span>' +
                            '</div>';
                        $('#gallery-preview').append(imageHtml);
                    }
                });

                // Actualizar el campo oculto
                $('#custom_gallery_ids').val(galleryIds.join(','));
            });

            // Abrir el modal
            galleryFrame.open();
        });

        // Eliminar imagen individual
        $(document).on('click', '.remove-image', function() {
            var imageDiv = $(this).parent();
            var imageId = imageDiv.data('id').toString();
            
            // Eliminar del array
            galleryIds = galleryIds.filter(function(id) {
                return id !== imageId;
            });
            
            // Actualizar el campo oculto
            $('#custom_gallery_ids').val(galleryIds.join(','));
            
            // Eliminar del DOM
            imageDiv.remove();
        });

        // Limpiar todas las imágenes
        $('#clear-gallery-images').on('click', function(e) {
            e.preventDefault();
            
            if (confirm('<?php echo esc_js(__('¿Estás seguro de que quieres eliminar todas las imágenes?', 'xarop')); ?>')) {
                galleryIds = [];
                $('#custom_gallery_ids').val('');
                $('#gallery-preview').empty();
            }
        });

        // Hacer la galería ordenable
        if (typeof $.fn.sortable !== 'undefined') {
            $('#gallery-preview').sortable({
                update: function() {
                    galleryIds = [];
                    $('#gallery-preview .gallery-image').each(function() {
                        galleryIds.push($(this).data('id').toString());
                    });
                    $('#custom_gallery_ids').val(galleryIds.join(','));
                }
            });
        }
    });
    </script>
    <?php
}

/**
 * Guardar los datos del meta box de galería
 */
function xarop_save_gallery_meta_box( $post_id )
{
    // Comprobar si el nonce está definido
    if (! isset($_POST['xarop_gallery_nonce_field']) ) {
        return;
    }

    // Verificar el nonce
    if (! wp_verify_nonce($_POST['xarop_gallery_nonce_field'], 'xarop_gallery_nonce') ) {
        return;
    }

    // Comprobar si es un autoguardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    // Comprobar los permisos del usuario
    if (! current_user_can('edit_post', $post_id) ) {
        return;
    }

    // Sanear y guardar los datos
    if (isset($_POST['custom_gallery_ids']) ) {
        $gallery_ids = sanitize_text_field($_POST['custom_gallery_ids']);
        
        // Validar que sea una lista de números separados por comas
        $ids_array = explode(',', $gallery_ids);
        $ids_array = array_filter($ids_array, 'is_numeric');
        $gallery_ids = implode(',', $ids_array);
        
        update_post_meta($post_id, '_custom_gallery_ids', $gallery_ids);
    } else {
        delete_post_meta($post_id, '_custom_gallery_ids');
    }
}
add_action('save_post', 'xarop_save_gallery_meta_box');

/**
 * Encolar los scripts del cargador de medios
 */
function xarop_enqueue_media_uploader()
{
    global $post_type;
    
    if (in_array($post_type, array( 'page', 'post' )) ) {
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
    }
}
add_action('admin_enqueue_scripts', 'xarop_enqueue_media_uploader');
