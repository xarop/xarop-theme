<?php
/**
 * Custom Meta Boxes
 *
 * Native Meta Box for Gallery using WordPress Media Library
 *
 * @package xarop
 * @since   1.0.0
 */

// Exit if accessed directly
if (! defined('ABSPATH') ) {
    exit;
}

/**
 * Add custom gallery meta box
 */
function xarop_add_gallery_meta_box()
{
    $post_types = array( 'page', 'post' );

    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'xarop_gallery',
            __('Custom Gallery', 'xarop'),
            'xarop_gallery_meta_box_callback',
            $post_type,
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'xarop_add_gallery_meta_box');

/**
 * Gallery meta box callback
 */
function xarop_gallery_meta_box_callback( $post )
{
    // Add nonce for security
    wp_nonce_field('xarop_gallery_nonce', 'xarop_gallery_nonce_field');

    // Get current gallery IDs
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
                echo '<span class="remove-image" title="' . esc_attr__('Remove', 'xarop') . '">&times;</span>';
                echo '</div>';
            }
        }
    }
    ?>
        </div>

        <p>
            <button type="button" class="button button-primary" id="add-gallery-images">
                <?php esc_html_e('Add Images', 'xarop'); ?>
            </button>
            <button type="button" class="button" id="clear-gallery-images">
                <?php esc_html_e('Clear All', 'xarop'); ?>
            </button>
        </p>

        <p class="description">
    <?php esc_html_e('Select multiple images from the media library to create a gallery.', 'xarop'); ?>
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

        // Add images button
        $('#add-gallery-images').on('click', function(e) {
            e.preventDefault();

            // If the media frame already exists, reopen it
            if (galleryFrame) {
                galleryFrame.open();
                return;
            }

            // Create the media frame
            galleryFrame = wp.media({
                title: '<?php echo esc_js(__('Select Gallery Images', 'xarop')); ?>',
                button: {
                    text: '<?php echo esc_js(__('Add to Gallery', 'xarop')); ?>'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });

            // When images are selected
            galleryFrame.on('select', function() {
                var selection = galleryFrame.state().get('selection');
                
                selection.each(function(attachment) {
                    attachment = attachment.toJSON();
                    
                    // Add to array if not already present
                    if (galleryIds.indexOf(attachment.id.toString()) === -1) {
                        galleryIds.push(attachment.id);
                        
                        // Add preview
                        var imageHtml = '<div class="gallery-image" data-id="' + attachment.id + '">' +
                            '<img src="' + attachment.sizes.thumbnail.url + '" />' +
                            '<span class="remove-image" title="<?php echo esc_js(__('Remove', 'xarop')); ?>">&times;</span>' +
                            '</div>';
                        $('#gallery-preview').append(imageHtml);
                    }
                });

                // Update hidden input
                $('#custom_gallery_ids').val(galleryIds.join(','));
            });

            // Open the modal
            galleryFrame.open();
        });

        // Remove single image
        $(document).on('click', '.remove-image', function() {
            var imageDiv = $(this).parent();
            var imageId = imageDiv.data('id').toString();
            
            // Remove from array
            galleryIds = galleryIds.filter(function(id) {
                return id !== imageId;
            });
            
            // Update hidden input
            $('#custom_gallery_ids').val(galleryIds.join(','));
            
            // Remove from DOM
            imageDiv.remove();
        });

        // Clear all images
        $('#clear-gallery-images').on('click', function(e) {
            e.preventDefault();
            
            if (confirm('<?php echo esc_js(__('Are you sure you want to remove all images?', 'xarop')); ?>')) {
                galleryIds = [];
                $('#custom_gallery_ids').val('');
                $('#gallery-preview').empty();
            }
        });

        // Make gallery sortable
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
 * Save gallery meta box data
 */
function xarop_save_gallery_meta_box( $post_id )
{
    // Check if nonce is set
    if (! isset($_POST['xarop_gallery_nonce_field']) ) {
        return;
    }

    // Verify nonce
    if (! wp_verify_nonce($_POST['xarop_gallery_nonce_field'], 'xarop_gallery_nonce') ) {
        return;
    }

    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    // Check user permissions
    if (! current_user_can('edit_post', $post_id) ) {
        return;
    }

    // Sanitize and save the data
    if (isset($_POST['custom_gallery_ids']) ) {
        $gallery_ids = sanitize_text_field($_POST['custom_gallery_ids']);
        
        // Validate that it's a comma-separated list of numbers
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
 * Enqueue media uploader scripts
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
