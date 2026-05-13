<?php
// Datos de conexión y rutas principales.
define('DB_HOST', 'localhost');
define('DB_NAME', 'el_rastro_mvc');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('APP_NAME', 'El Rastro');

define('BASE_URL', '/el_rastro_mvc/public');

function app_url($ruta = '') {
    $base = rtrim(BASE_URL, '/');
    $ruta = trim($ruta, '/');

    if ($ruta == '') {
        return $base . '/index.php';
    }

    return $base . '/index.php?url=' . $ruta;
}

function asset_url($ruta = '') {
    return rtrim(BASE_URL, '/') . '/' . ltrim($ruta, '/');
}

function image_url($ruta) {
    if (!$ruta) {
        return asset_url('assets/img/producto-placeholder.svg');
    }

    if (strpos($ruta, 'http://') === 0 || strpos($ruta, 'https://') === 0 || strpos($ruta, '/') === 0) {
        return $ruta;
    }

    return asset_url($ruta);
}
