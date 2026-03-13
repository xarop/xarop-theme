<?php
/**
 * posts Grid Section Template
 *
 * @package xarop
 *
 * Variables expected:
 * $categories (array) - taxonomy terms for filters
 * $section_title (string) - section title (optional)
 * $show_filters (bool) - show category filters (default true)
 * $grid_id (string) - id for the grid container (default 'grid')
 */

$has_posts = (new WP_Query(
    [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'fields' => 'ids',
    ]
))->have_posts();

if (!isset($categories)) {
    $categories = get_terms(
        [
        'taxonomy' => 'category',
        'hide_empty' => true,
        ]
    );
    // Filtrar categorías sin posts publicados
    $categories = array_filter(
        $categories, function ($cat) {
            return $cat->count > 0;
        }
    );
}
if (!isset($section_title)) {
    $section_title = '';
}
if (!isset($show_filters)) {
    $show_filters = true;
}
if (!isset($grid_id)) {
    $grid_id = 'grid';
}
if ($has_posts) :
    ?>
<div class="grid-section">
    <?php if (!empty($section_title)) : ?>
        <h2 class="text-center"><?php echo esc_html($section_title); ?></h2>
    <?php endif; ?>

    <?php if ($show_filters && !empty($categories) && !is_wp_error($categories)) : ?>
        <div class="category-filters" role="group" aria-label="<?php esc_attr_e('Filtrar por categoría', 'xarop'); ?>">
            <button type="button" data-category="all" class="filter-btn<?php echo (!isset($_GET['category']) || $_GET['category'] === 'all') ? ' active' : ''; ?>">
                <?php esc_html_e('Todos', 'xarop'); ?>
            </button>
            <?php foreach ($categories as $category) : ?>
                <button type="button" data-category="<?php echo esc_attr($category->slug); ?>" class="filter-btn<?php echo (isset($_GET['category']) && $_GET['category'] === $category->slug) ? ' active' : ''; ?>">
                    <?php echo esc_html($category->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="grid" id="<?php echo esc_attr($grid_id); ?>">
        <?php
        // Soporte para paginación en home, archivo y páginas personalizadas
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }
        $posts_per_page = get_option('posts_per_page');
        $cat_filter = isset($_GET['category']) && $_GET['category'] !== 'all' ? sanitize_key($_GET['category']) : '';
        $query_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
        ];
        if ($cat_filter) {
            $query_args['category_name'] = $cat_filter;
        }
        // Si estamos en un archivo de categoría, preseleccionar el filtro
        if (is_category()) {
            $cat_obj = get_queried_object();
            if ($cat_obj && isset($cat_obj->slug)) {
                $query_args['category_name'] = $cat_obj->slug;
            }
        }
        $posts_query = new WP_Query($query_args);
        if ($posts_query->have_posts()) :
            while ($posts_query->have_posts()) : $posts_query->the_post();
                // Aquí puedes personalizar la salida de cada post
                echo '<div class="grid-item">';
                get_template_part('template-parts/content', get_post_format());
                echo '</div>';
            endwhile;
            // Paginación
            $big = 999999999; // need an unlikely integer
            echo '<div class="pagination">';
            echo paginate_links(
                [
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, $paged),
                'total' => $posts_query->max_num_pages,
                'prev_text' => __('&laquo;', 'xarop'),
                'next_text' => __('&raquo;', 'xarop'),
                ]
            );
            echo '</div>';
            wp_reset_postdata();
        else:
            echo '<p>' . esc_html__('Sin entradas.', 'xarop') . '</p>';
        endif;
        ?>
    </div>
</div>
<?php endif; ?>
