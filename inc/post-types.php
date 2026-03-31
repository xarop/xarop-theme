<?php
// Change post type labels from 'Post' to 'Proyecto'
add_action(
    'init', function () {
        global $wp_post_types;
        if (isset($wp_post_types['post'])) {
            $labels = &$wp_post_types['post']->labels;
            $labels->name                  = __('Proyectos', 'xarop');
            $labels->singular_name         = __('Proyecto', 'xarop');
            $labels->add_new               = __('Añadir nuevo', 'xarop');
            $labels->add_new_item          = __('Añadir nuevo proyecto', 'xarop');
            $labels->edit_item             = __('Editar proyecto', 'xarop');
            $labels->new_item              = __('Nuevo proyecto', 'xarop');
            $labels->view_item             = __('Ver proyecto', 'xarop');
            $labels->search_items          = __('Buscar proyectos', 'xarop');
            $labels->not_found             = __('No se encontraron proyectos', 'xarop');
            $labels->not_found_in_trash    = __('No se encontraron proyectos en la papelera', 'xarop');
            $labels->all_items             = __('Todos los proyectos', 'xarop');
            $labels->menu_name             = __('Proyectos', 'xarop');
            $labels->name_admin_bar        = __('Proyecto', 'xarop');
        }
    }
);
