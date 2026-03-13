<?php
/**
 * WordPress Customizer Integration
 *
 * Registers a "Xarop Theme" panel in Appearance → Customize with sections for:
 *  - Colors
 *  - Typography
 *  - Features (module toggles)
 *  - Maintenance Mode
 *
 * Setting keys follow the pattern:  xarop_{section}_{key}
 * e.g. xarop_colors_primary, xarop_features_blog_enabled, etc.
 *
 * Values saved in the Customizer take precedence over theme-config.php.
 * Use xarop_get_config() as normal — it already merges both sources.
 *
 * @package xarop-theme
 */

if (! defined('ABSPATH') ) {
    exit;
}

// ────────────────────────────────────────────────────────────────────────────
// REGISTER PANEL, SECTIONS AND CONTROLS
// ────────────────────────────────────────────────────────────────────────────

add_action('customize_register', 'xarop_customize_register');

/**
 * Registers all Customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function xarop_customize_register( WP_Customize_Manager $wp_customize ): void
{
    $config = xarop_get_config();

    // ── Main panel ────────────────────────────────────────────────────────
    $wp_customize->add_panel(
        'xarop_theme',
        [
            'title'       => __('Xarop Theme', 'xarop-theme'),
            'description' => __('Global theme settings. Changes made here override theme-config.php.', 'xarop-theme'),
            'priority'    => 30,
        ]
    );

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // SECTION: COLORS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    $wp_customize->add_section(
        'xarop_colors',
        [
            'title'    => __('Colores', 'xarop-theme'),
            'panel'    => 'xarop_theme',
            'priority' => 10,
        ]
    );

    $color_controls = [
        'primary'       => __('Color principal', 'xarop-theme'),
        'primary_dark'  => __('Principal oscuro (hover)', 'xarop-theme'),
        'primary_light' => __('Principal claro (destacados)', 'xarop-theme'),
        'text'          => __('Texto principal', 'xarop-theme'),
        'text_light'    => __('Texto secundario', 'xarop-theme'),
        'bg'            => __('Fondo base', 'xarop-theme'),
        'bg_alt'        => __('Fondo alternativo', 'xarop-theme'),
        'border'        => __('Bordes y separadores', 'xarop-theme'),
    ];

    foreach ( $color_controls as $key => $label ) {
        $setting_id = "xarop_colors_{$key}";
        $default    = $config['colors'][ $key ] ?? '';

        $wp_customize->add_setting(
            $setting_id,
            [
                'default'           => $default,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
            ]
        );

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize,
                $setting_id,
                [
                    'label'   => $label,
                    'section' => 'xarop_colors',
                ]
            )
        );
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // SECTION: TYPOGRAPHY
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    $wp_customize->add_section(
        'xarop_typography',
        [
            'title'    => __('Tipografía', 'xarop-theme'),
            'panel'    => 'xarop_theme',
            'priority' => 20,
        ]
    );

    $typo_controls = [
        'font_primary' => __('Fuente de texto', 'xarop-theme'),
        'font_heading' => __('Fuente de títulos', 'xarop-theme'),
        'font_mono'    => __('Fuente monoespaciada', 'xarop-theme'),
        'size_base'    => __('Tamaño base (px, rem…)', 'xarop-theme'),
        'line_height'  => __('Interlineado', 'xarop-theme'),
    ];

    foreach ( $typo_controls as $key => $label ) {
        $setting_id = "xarop_typography_{$key}";
        $default    = $config['typography'][ $key ] ?? '';

        $wp_customize->add_setting(
            $setting_id,
            [
                'default'           => $default,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
            ]
        );

        $wp_customize->add_control(
            $setting_id,
            [
                'label'   => $label,
                'section' => 'xarop_typography',
                'type'    => 'text',
            ]
        );
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // SECTION: FEATURES
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    $wp_customize->add_section(
        'xarop_features',
        [
            'title'    => __('Funcionalidades', 'xarop-theme'),
            'panel'    => 'xarop_theme',
            'priority' => 30,
        ]
    );

    $feature_controls = [
        'gutenberg_enabled'  => [
            'label'       => __('Editor de bloques (Gutenberg)', 'xarop-theme'),
            'description' => __('Desactívalo para eliminar el editor de bloques.', 'xarop-theme'),
        ],
        'blog_enabled'       => [
            'label'       => __('Blog / Entradas', 'xarop-theme'),
            'description' => __('Desactívalo en sitios sin blog ni noticias.', 'xarop-theme'),
        ],
        'comments_enabled'   => [
            'label'       => __('Sistema de comentarios', 'xarop-theme'),
            'description' => __('Actívalo para mostrar el formulario de comentarios.', 'xarop-theme'),
        ],
        'headless_mode'      => [
            'label'       => __('Modo headless', 'xarop-theme'),
            'description' => __('Redirige el frontend a la URL de la API REST.', 'xarop-theme'),
        ],
        'animations_enabled' => [
            'label'       => __('Animaciones', 'xarop-theme'),
            'description' => __('Carga animations.css y el observer de scroll.', 'xarop-theme'),
        ],
    ];

    foreach ( $feature_controls as $key => $args ) {
        $setting_id = "xarop_features_{$key}";
        $default    = isset($config[ $key ]) ? (bool) $config[ $key ] : false;

        $wp_customize->add_setting(
            $setting_id,
            [
                'default'           => $default,
                'transport'         => 'refresh',
                'sanitize_callback' => 'xarop_sanitize_checkbox',
            ]
        );

        $wp_customize->add_control(
            $setting_id,
            [
                'label'       => $args['label'],
                'description' => $args['description'],
                'section'     => 'xarop_features',
                'type'        => 'checkbox',
            ]
        );
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // SECTION: MAINTENANCE MODE
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    $wp_customize->add_section(
        'xarop_maintenance',
        [
            'title'       => __('Modo mantenimiento', 'xarop-theme'),
            'description' => __('Oculta el sitio a los visitantes no autenticados y muestra un mensaje personalizado.', 'xarop-theme'),
            'panel'       => 'xarop_theme',
            'priority'    => 40,
        ]
    );

    // Toggle: private_site.
    $wp_customize->add_setting(
        'xarop_private_site',
        [
            'default'           => (bool) ( $config['private_site'] ?? false ),
            'transport'         => 'refresh',
            'sanitize_callback' => 'xarop_sanitize_checkbox',
        ]
    );

    $wp_customize->add_control(
        'xarop_private_site',
        [
            'label'       => __('Activar modo mantenimiento', 'xarop-theme'),
            'description' => __('Solo los usuarios con sesión activa podrán ver el sitio.', 'xarop-theme'),
            'section'     => 'xarop_maintenance',
            'type'        => 'checkbox',
        ]
    );

    // Maintenance page title.
    $wp_customize->add_setting(
        'xarop_maintenance_title',
        [
            'default'           => $config['maintenance_title'] ?? 'Site under maintenance',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ]
    );

    $wp_customize->add_control(
        'xarop_maintenance_title',
        [
            'label'           => __('Título de la página', 'xarop-theme'),
            'section'         => 'xarop_maintenance',
            'type'            => 'text',
            'active_callback' => 'xarop_is_private_site_active',
        ]
    );

    // Maintenance message body.
    $wp_customize->add_setting(
        'xarop_maintenance_message',
        [
            'default'           => $config['maintenance_message'] ?? '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'wp_kses_post',
        ]
    );

    $wp_customize->add_control(
        'xarop_maintenance_message',
        [
            'label'           => __('Mensaje para los visitantes', 'xarop-theme'),
            'section'         => 'xarop_maintenance',
            'type'            => 'textarea',
            'active_callback' => 'xarop_is_private_site_active',
        ]
    );
}

// ────────────────────────────────────────────────────────────────────────────
// CONFIG OVERRIDE: MERGE CUSTOMIZER VALUES INTO xarop_get_config()
// ────────────────────────────────────────────────────────────────────────────

add_filter('xarop_config', 'xarop_customizer_override_config');

/**
 * Merges Customizer theme_mod values into the config array so that
 * xarop_get_config() always returns the active (possibly overridden) values.
 *
 * @param  array $config Config array from theme-config.php + defaults.
 * @return array         Config array with Customizer overrides applied.
 */
function xarop_customizer_override_config( array $config ): array
{
    // Colors.
    $color_keys = [ 'primary', 'primary_dark', 'primary_light', 'text', 'text_light', 'bg', 'bg_alt', 'border' ];
    foreach ( $color_keys as $key ) {
        $mod = get_theme_mod("xarop_colors_{$key}", null);
        if (null !== $mod && '' !== $mod ) {
            $config['colors'][ $key ] = sanitize_hex_color($mod);
        }
    }

    // Typography.
    $typo_keys = [ 'font_primary', 'font_heading', 'font_mono', 'size_base', 'line_height' ];
    foreach ( $typo_keys as $key ) {
        $mod = get_theme_mod("xarop_typography_{$key}", null);
        if (null !== $mod && '' !== $mod ) {
            $config['typography'][ $key ] = sanitize_text_field($mod);
        }
    }

    // Feature toggles.
    $feature_keys = [ 'gutenberg_enabled', 'blog_enabled', 'comments_enabled', 'headless_mode', 'animations_enabled' ];
    foreach ( $feature_keys as $key ) {
        $mod = get_theme_mod("xarop_features_{$key}", null);
        if (null !== $mod ) {
            $config[ $key ] = (bool) $mod;
        }
    }

    // Maintenance / private site.
    $private_mod = get_theme_mod('xarop_private_site', null);
    if (null !== $private_mod ) {
        $config['private_site'] = (bool) $private_mod;
    }

    $title_mod = get_theme_mod('xarop_maintenance_title', null);
    if (null !== $title_mod && '' !== $title_mod ) {
        $config['maintenance_title'] = sanitize_text_field($title_mod);
    }

    $msg_mod = get_theme_mod('xarop_maintenance_message', null);
    if (null !== $msg_mod && '' !== $msg_mod ) {
        $config['maintenance_message'] = wp_kses_post($msg_mod);
    }

    return $config;
}

// ────────────────────────────────────────────────────────────────────────────
// HELPERS
// ────────────────────────────────────────────────────────────────────────────

/**
 * Sanitizes a checkbox value (returns 1 or 0 as boolean).
 *
 * @param  mixed $value Raw input value.
 * @return bool         True if checked, false otherwise.
 */
function xarop_sanitize_checkbox( $value ): bool
{
    return (bool) $value;
}

/**
 * Active callback: shows controls only when private_site is enabled.
 *
 * @return bool
 */
function xarop_is_private_site_active(): bool
{
    return (bool) get_theme_mod('xarop_private_site', false);
}
