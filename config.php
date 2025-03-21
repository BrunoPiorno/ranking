<?php
// Detectar automáticamente si estamos en producción o desarrollo
$base_path = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
              strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) 
              ? '/ranking' 
              : '';

define('BASE_URL', $base_path);

// Función helper para URLs
function url($path = '') {
    return BASE_URL . $path;
}