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


<?php require locate_template('template-parts/slider.php'); ?>
<section id="main" class="container">
    <div class="container-inner ">
        <?php
        // Display page content if it exists
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
            // Get all categories for filters
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
        // Display the custom gallery if it exists
            require locate_template('template-parts/gallery.php');
        ?>
    </div>
</section>        

<?php
get_footer();
