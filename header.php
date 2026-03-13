<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-inner">
        <div class="site-branding logo-tagline-wrap">
            <?php
            $description = get_bloginfo('description', 'display');
            if (has_custom_logo()) {
                echo '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link" rel="home">';
                the_custom_logo();
                if ($description || is_customize_preview()) {
                    echo '<span class="site-tagline">' . esc_html($description) . '</span>';
                }
                echo '</a>';
            } else {
                if (is_front_page() && is_home()) : ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <?php bloginfo('name'); ?>
                            <?php if ($description || is_customize_preview()) : ?>
                                <span class="site-tagline"><?php echo esc_html($description); ?></span>
                            <?php endif; ?>
                        </a>
                    </h1>
                <?php else : ?>
                    <p class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <?php bloginfo('name'); ?>
                            <?php if ($description || is_customize_preview()) : ?>
                                <span class="site-tagline"><?php echo esc_html($description); ?></span>
                            <?php endif; ?>
                        </a>
                    </p>
                <?php endif;
            }
            ?>
        </div>

        <!-- Search Toggle (before hamburger so it sits to its left on mobile) -->
        <label for="search-toggle" class="search-toggle-label" aria-label="<?php esc_attr_e('Open search', 'xarop'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                <circle cx="11" cy="11" r="7"/>
                <line x1="16.5" y1="16.5" x2="22" y2="22"/>
            </svg>
        </label>

        <!-- CSS-Only Hamburger Menu -->
        <input type="checkbox" id="menu-toggle" />
        <label for="menu-toggle" class="menu-toggle-label" aria-label="<?php esc_attr_e('Open menu', 'xarop'); ?>">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <!-- Menu Overlay -->
        <div class="menu-overlay"></div>

        <!-- Main Navigation -->
        <nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Main menu', 'xarop'); ?>">
            <?php
            function xarop_mobile_logo()
            {
                $logo_id = get_theme_mod('custom_logo');
                $logo_src = $logo_id ? wp_get_attachment_image_src($logo_id, 'medium')[0] : '';
                if ($logo_src) {
                    echo '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link" rel="home">';
                    echo '<img src="' . esc_url($logo_src) . '" alt="' . esc_attr(get_bloginfo('name')) . '" class="custom-logo" style="max-width:90px;max-height:40px;" />';
                     $description = get_bloginfo('description', 'display');       
                    echo '</a>';
                    
                } else {
                    echo '<a href="' . esc_url(home_url('/')) . '" class="site-title-mini" rel="home">' . get_bloginfo('name') . '</a>';
                }
                // echo '<small class="text-center">' . esc_html($description) . '</small>';
            }
            echo '<div class="mobile-logo">';
            xarop_mobile_logo();
           
            if ($description || is_customize_preview()) {
                    
            }
            echo '</div>';

            wp_nav_menu(
                array(
                    'theme_location' => 'main-menu',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                )
            );
            ?>
        </nav>
    </div>
</header>

<!-- Search checkbox + modal live OUTSIDE <header> so backdrop-filter on the
     header does not trap position:fixed children inside its bounds.
     The <label> (magnifier) inside the header still controls this checkbox. -->
<input type="checkbox" id="search-toggle" />
<div class="search-modal">
    <label for="search-toggle" class="search-modal-backdrop" aria-hidden="true"></label>
    <div class="search-modal-content">
        <div class="search-modal-header">
            <p class="search-modal-hint"><?php esc_html_e('What are you looking for?', 'xarop'); ?></p>
            <label for="search-toggle" class="search-modal-close" aria-label="<?php esc_attr_e('Close search', 'xarop'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true" focusable="false">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </label>
        </div>
        <?php get_search_form(); ?>
    </div>
</div>

<main id="main-content" class="site-main">
