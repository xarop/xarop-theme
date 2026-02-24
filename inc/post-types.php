<?php
// Change 'Post' to 'Project' in admin menu
add_action(
    'admin_menu', function () {
        global $menu, $submenu;
        foreach ($menu as $key => $value) {
            if (isset($value[0]) && $value[0] === 'Posts') {
                $menu[$key][0] = __('Projects', 'xarop');
            }
        }
        if (isset($submenu['edit.php'])) {
            foreach ($submenu['edit.php'] as $key => $value) {
                if ($value[0] === 'Add New') {
                    $submenu['edit.php'][$key][0] = __('Add New Project', 'xarop');
                } elseif ($value[0] === 'Posts') {
                    $submenu['edit.php'][$key][0] = __('Projects', 'xarop');
                }
            }
        }
    }
);

// Change post type labels from 'Post' to 'Project'
add_action(
    'init', function () {
        global $wp_post_types;
        if (isset($wp_post_types['post'])) {
            $labels = &$wp_post_types['post']->labels;
            $labels->name = __('Projects', 'xarop');
            $labels->singular_name = __('Project', 'xarop');
            $labels->add_new = __('Add New Project', 'xarop');
            $labels->add_new_item = __('Add New Project', 'xarop');
            $labels->edit_item = __('Edit Project', 'xarop');
            $labels->new_item = __('New Project', 'xarop');
            $labels->view_item = __('View Project', 'xarop');
            $labels->search_items = __('Search Projects', 'xarop');
            $labels->not_found = __('No projects found', 'xarop');
            $labels->not_found_in_trash = __('No projects found in Trash', 'xarop');
            $labels->all_items = __('All Projects', 'xarop');
            $labels->menu_name = __('Projects', 'xarop');
            $labels->name_admin_bar = __('Project', 'xarop');
        }
    }
);