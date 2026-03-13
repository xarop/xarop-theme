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

        <!-- CSS-Only Hamburger Menu -->
        <input type="checkbox" id="menu-toggle" />
        <label for="menu-toggle" class="menu-toggle-label" aria-label="<?php esc_attr_e('Abrir menú', 'xarop'); ?>">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <!-- Menu Overlay -->
        <div class="menu-overlay"></div>

        <!-- Main Navigation -->
        <nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Menú principal', 'xarop'); ?>">
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

<main id="main-content" class="site-main">
