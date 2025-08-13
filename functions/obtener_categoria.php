<?php
function obtener_categoria($puntos) {
    if ($puntos >= 800) {
        return 'Primera';
    } elseif ($puntos >= 500) {
        return 'Segunda';
    } elseif ($puntos >= 300) {
        return 'Tercera';
    } elseif ($puntos >= 150) {
        return 'Cuarta';
    } elseif ($puntos >= 100) {
        return 'Quinta';
    } else {
        return 'Menores';
    }
}