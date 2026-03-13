<?php
/**
 * Private Site / Maintenance Mode
 *
 * When 'private_site' is enabled in theme-config.php, non-logged-in visitors
 * receive a styled 503 maintenance page instead of the normal site.
 * REST API, cron, WP CLI, and the login page are always allowed through.
 *
 * @package xarop-theme
 */

if (! defined('ABSPATH') ) {
    exit;
}

// ────────────────────────────────────────────────────────────────────────────
// PRIVATE SITE GATE
// ────────────────────────────────────────────────────────────────────────────

add_action('template_redirect', 'xarop_private_site_gate', 1);

/**
 * Intercept front-end requests when private_site mode is active.
 * Sends a 503 maintenance page to all non-authenticated visitors.
 */
function xarop_private_site_gate(): void
{
    $config = xarop_get_config();

    if (empty($config['private_site']) ) {
        return;
    }

    // Always allow: REST API, WP Cron, WP CLI, logged-in users, login page.
    if (( defined('REST_REQUEST')  && REST_REQUEST  ) 
        || ( defined('DOING_CRON')    && DOING_CRON    ) 
        || ( defined('WP_CLI')        && WP_CLI        ) 
        || is_user_logged_in()                              
        || $GLOBALS['pagenow'] === 'wp-login.php'
    ) {
        return;
    }

    $title   = esc_html($config['maintenance_title']   ?? 'Site under maintenance');
    $message = wp_kses_post($config['maintenance_message'] ?? 'We are working on something awesome. Check back soon!');
    $color   = esc_attr($config['colors']['primary'] ?? '#0055ff');

    // Use the WP custom logo (Site Identity) when available.
    $custom_logo_id  = get_theme_mod('custom_logo');
    $logo_html       = '';
    if ($custom_logo_id ) {
        $logo_img  = wp_get_attachment_image(
            $custom_logo_id,
            'full',
            false,
            [ 'class' => 'maintenance-logo', 'alt' => esc_attr(get_bloginfo('name')) ]
        );
        $logo_html = $logo_img ?: '';
    }

    // 503 + Retry-After so search engines know this is temporary.
    http_response_code(503);
    header('Retry-After: 3600');
    header('Content-Type: text/html; charset=utf-8');

    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
    echo xarop_maintenance_html($title, $message, $logo_html, $color);
    // phpcs:enable
    exit;
}

/**
 * Renders the maintenance page HTML.
 *
 * @param  string $title     Page heading.
 * @param  string $message   Body message (may contain basic HTML).
 * @param  string $logo_html Ready-to-output <img> tag for the site logo, or empty string.
 * @param  string $color     Primary brand colour used for the accent bar.
 * @return string          Full HTML document.
 */
function xarop_maintenance_html( string $title, string $message, string $logo_html, string $color ): string
{
    $login_url  = esc_url(wp_login_url(home_url('/')));
    $site_name  = esc_html(get_bloginfo('name'));
    $tagline    = esc_html(get_bloginfo('description'));

    ob_start();
    ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $title; ?> — <?php echo $site_name; ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root { --brand: <?php echo $color; ?>; }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Helvetica, Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .maintenance-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 30px rgba(0,0,0,.08);
            max-width: 520px;
            width: 100%;
            padding: 3rem 2.5rem 2.5rem;
            text-align: center;
            border-top: 5px solid var(--brand);
        }

        .maintenance-logo {
            max-height: 80px;
            width: auto;
            margin-bottom: 1.5rem;
            display: block;
            margin-inline: auto;
        }

        .maintenance-site-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111;
            margin-bottom: .25rem;
        }

        .maintenance-tagline {
            font-size: .85rem;
            color: #888;
            margin-bottom: 2rem;
        }

        .maintenance-divider {
            border: none;
            border-top: 1px solid #e5e5e5;
            margin: 1.5rem 0;
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #111;
        }

        p {
            font-size: 1rem;
            line-height: 1.7;
            color: #555;
            margin-bottom: 1.5rem;
        }

        .maintenance-login {
            display: inline-block;
            margin-top: 1rem;
            font-size: .8rem;
            color: var(--brand);
            text-decoration: none;
            opacity: .7;
            transition: opacity .2s;
        }

        .maintenance-login:hover { opacity: 1; }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <?php if ($logo_html ) : ?>
            <?php echo $logo_html; ?>
        <?php endif; ?>
        <p class="maintenance-site-name"><?php echo $site_name; ?></p>
        <?php if ($tagline ) : ?>
            <p class="maintenance-tagline"><?php echo $tagline; ?></p>
        <?php endif; ?>
        <hr class="maintenance-divider">
        <h1><?php echo $title; ?></h1>
        <p><?php echo $message; ?></p>
        <a href="<?php echo $login_url; ?>" class="maintenance-login">
            <?php esc_html_e('Log in', 'xarop'); ?>
        </a>
    </div>
</body>
</html>
    <?php
    return ob_get_clean();
}
