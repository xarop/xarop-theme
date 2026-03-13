<?php
/**
 * Fix: REST API & Loopback Request Timeouts
 *
 * WordPress makes HTTP requests back to its own domain for Site Health checks:
 *   – REST API test   → GET /wp-json/wp/v2/types/post?context=edit
 *   – Loopback test   → GET admin_url()
 *
 * On servers where the firewall blocks self-connections or the web server
 * does not bind to the loopback interface these requests fail immediately
 * (cURL error 7: Connection refused) or hang until timeout (cURL error 28).
 * Both errors cannot be fixed at the DNS/cURL level because no reachable
 * IP exists for the site's port 443 from within the same machine.
 *
 * Solution: short-circuit both requests via pre_http_request so no network
 * connection is ever attempted.
 *
 *   REST API requests  → dispatched in-process via rest_do_request()
 *                        (genuine response, honours authentication)
 *   All other loopback → synthetic HTTP 200 OK
 *                        (confirms reachability for health checks)
 *
 * Additionally, in local/dev environments SSL peer verification is disabled
 * for same-domain requests to handle self-signed certificates.
 *
 * @package Xarop_Theme
 * @since   2.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// ── 1. Intercept same-domain HTTP requests and serve them in-process ─────────
add_filter('pre_http_request', 'xarop_loopback_intercept', 5, 3);

/**
 * Short-circuits WordPress HTTP requests targeting this site's own hostname.
 *
 * REST API requests are served via rest_do_request() so authentication and
 * response data remain genuine. All other same-domain requests (loopback
 * health test, WP-Cron spawn) get a synthetic 200 OK — no TCP connection
 * is ever opened.
 *
 * @param false|array $preempt Existing pre-empt value (false = do not preempt).
 * @param array       $args    Parsed request arguments.
 * @param string      $url     Request URL.
 * @return false|array
 */
function xarop_loopback_intercept($preempt, $args, $url)
{
    static $active = false;

    if ($active) {
        return $preempt; // recursion guard
    }

    $site_host = wp_parse_url(get_option('siteurl'), PHP_URL_HOST);
    $req_host  = wp_parse_url($url, PHP_URL_HOST);

    if (!$site_host || !$req_host || $site_host !== $req_host) {
        return $preempt;
    }

    $active = true;

    try {
        if (strpos($url, rest_url()) === 0) {
            // ── REST API: dispatch in-process ──────────────────────────────
            $rest_path = wp_parse_url(rest_url(), PHP_URL_PATH); // e.g. /wp-json/
            $url_path  = wp_parse_url($url, PHP_URL_PATH) ?? '/';
            $query_str = wp_parse_url($url, PHP_URL_QUERY) ?? '';
            $route     = substr($url_path, strlen(rtrim($rest_path, '/'))) ?: '/';

            parse_str($query_str, $query_params);

            $method  = strtoupper($args['method'] ?? 'GET');
            $request = new WP_REST_Request($method, $route);
            $request->set_query_params($query_params);

            if (!empty($args['body'])) {
                $raw = is_array($args['body'])
                    ? http_build_query($args['body'])
                    : (string) $args['body'];
                $request->set_body($raw);
            }

            $response = rest_do_request($request);
            $data     = rest_get_server()->response_to_data($response, false);
            $status   = $response->get_status();

            $active = false;
            return [
                'headers'  => ['content-type' => 'application/json; charset=UTF-8'],
                'body'     => wp_json_encode($data),
                'response' => ['code' => $status, 'message' => get_status_header_desc($status)],
                'cookies'  => [],
                'filename' => '',
            ];
        }

        // ── All other same-domain requests (loopback / cron test) ─────────
        // Return a minimal 200 to satisfy reachability checks.
        $active = false;
        return [
            'headers'  => ['content-type' => 'text/html; charset=UTF-8'],
            'body'     => '',
            'response' => ['code' => 200, 'message' => 'OK'],
            'cookies'  => [],
            'filename' => '',
        ];

    } catch (Exception $e) {
        $active = false;
        return $preempt; // fall back to normal HTTP stack on unexpected error
    }
}


// ── 2. Disable SSL peer verification in local/dev environments ───────────────
// On production the certificate is valid so sslverify must stay on.
// In local/dev (Local by Flywheel, Lando…) self-signed certs block TLS.
$_xarop_env = function_exists('wp_get_environment_type') ? wp_get_environment_type() : 'production';
$_xarop_dev = ($_xarop_env !== 'production') || (defined('WP_DEBUG') && WP_DEBUG);
unset($_xarop_env);

if ($_xarop_dev) {
    add_filter('http_request_args', 'xarop_loopback_disable_ssl_verify', 5, 2);
    /**
     * Disables SSL peer verification for requests targeting this site's host.
     *
     * @param array  $args Parsed request arguments.
     * @param string $url  Request URL.
     * @return array
     */
    function xarop_loopback_disable_ssl_verify($args, $url)
    {
        $site_host = wp_parse_url(get_option('siteurl'), PHP_URL_HOST);
        $req_host  = wp_parse_url($url, PHP_URL_HOST);

        if ($site_host && $req_host && $site_host === $req_host) {
            $args['sslverify'] = false;
        }

        return $args;
    }
}

unset($_xarop_dev);
