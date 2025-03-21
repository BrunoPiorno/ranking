<?php
function obtener_categoria($puntos) {
    if ($puntos >= 900) {
        return 'Primera';
    } elseif ($puntos >= 500) {
        return 'Segunda';
    } elseif ($puntos >= 300) {
        return 'Tercera';
    } elseif ($puntos >= 100) {
        return 'Cuarta';
    } else {
        return 'Menores';
    }
}