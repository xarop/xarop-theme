<?php
/**
 * Template part for displaying a custom gallery for a post
 * Usage: include locate_template('template-parts/gallery.php');
 *
 * Expects $gallery_ids to be set in the parent scope (or pass as variable)
 *
 * @package xarop
 */

if (!isset($gallery_ids)) {
    $gallery_ids = get_post_meta(get_the_ID(), '_custom_gallery_ids', true);
}

if (!empty($gallery_ids)) :
    $ids = explode(',', $gallery_ids);
    $ids = array_filter($ids, 'is_numeric');
    if (!empty($ids)) :
        ?>
        <div class="gallery">
            <?php foreach ($ids as $index => $image_id) :
                $image_url = wp_get_attachment_image_url($image_id, 'large');
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                if ($image_url) : ?>
                    <a href="<?php echo esc_url(wp_get_attachment_image_url($image_id, 'full')); ?>" data-index="<?php echo $index; ?>" class="gallery-item" target="_blank">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
                    </a>
                <?php endif;
            endforeach; ?>
        </div>
    <?php endif;
endif;
