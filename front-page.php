<?php
/**
 * Template Name: Front Page
 * The front page template
 *
 * @package xarop
 * @since   1.0.0
 */

get_header();
?>


<?php
// Configuración del slider principal
$slider_config = array(
    'type' => 'pages', // 'pages' (páginas) o 'posts' (entradas)
    'ids'  => array(1327,1102), // Se rellena con los IDs publicados
);
$slider_posts = get_posts(
    array(
        'post_type'      => ($slider_config['type'] === 'posts') ? 'post' : 'page',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'operator' => 'EXISTS',
            ),
        ),
    )
);
$slider_config['ids'] = $slider_posts;

$post_type = ($slider_config['type'] === 'posts') ? 'post' : 'page';
$slider_query = new WP_Query(
    array(
    'post_type'      => $post_type,
    'post__in'       => $slider_config['ids'],
    'orderby'        => 'post__in',
    'posts_per_page' => count($slider_config['ids']),
    )
);
require locate_template('template-parts/slider.php');
?>
<section id="main" class="container">
    <div class="container-inner ">
        <?php
        // Mostrar el contenido de la página si existe
        if (have_posts() ) :
            while ( have_posts() ) :
                the_post();
                ?>
                <div class="entry-content text-center">
                    <h1><?php the_title() ?></h1>
                     <?php the_content(); ?>
                </div>
                <?php
            endwhile;
        endif;
        ?>
    
   
    </div>  
</section>
<section class="section">
    <div class="container">
            
         <?php 
            // Obtener todas las categorías para los filtros
            $categories = get_terms(
                array(
                'taxonomy'   => 'category',
                'hide_empty' => true,
                )
            );
            if (! empty($categories) && ! is_wp_error($categories) ) : 
                include locate_template('template-parts/grid.php'); 
            endif; 
            ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php
        // Mostrar la galería personalizada si existe
            require locate_template('template-parts/gallery.php');
        ?>
    </div>
</section>        

<?php
get_footer();
