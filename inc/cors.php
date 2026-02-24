<?php
/**
 * Habilitar CORS para desarrollo local headless
 */
add_action(
    'init', function () {
        // Solo permitir CORS si el entorno es local
        $is_local = false;
        if (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
            if ($host === 'localhost' || $host === '127.0.0.1') {
                $is_local = true;
            }
        }
        if ($is_local && isset($_SERVER['HTTP_ORIGIN'])) {
            $origin = $_SERVER['HTTP_ORIGIN'];
            if (strpos($origin, 'localhost:5173') !== false || strpos($origin, '127.0.0.1:5173') !== false) {
                header("Access-Control-Allow-Origin: $origin");
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce, X-Requested-With");
            }
        }
    }
);
