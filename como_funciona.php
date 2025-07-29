<?php
include 'layout/header.php';
include 'db.php';
?>

<section class="como-funciona">
    <div class="container">
        <h2 class="section-title">Ranking de Jugadores - Explicación del Sistema de Puntos</h2>
        <p class="section-description">
            Nuestro sistema de puntos se basa en un modelo de tabla probado, diseñado para ser justo, predecible y competitivo. Para garantizar la máxima transparencia, hemos adoptado el mismo sistema que usan distintas organizaciones. La cantidad de puntos que un jugador gana depende de la diferencia de ranking con su oponente en el momento del partido.
        </p>

        <h3 class="sub-title">Tabla de Puntuación para el Ganador</h3>
        <table class="points-table">
            <thead>
                <tr>
                    <th>Diferencia entre Jugadores</th>
                    <th>Puntos si GANA el de MAYOR puntaje</th>
                    <th>Puntos si GANA el de MENOR puntaje</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>750 o más</td><td>1</td><td>28</td></tr>
                <tr><td>De 500 a 749</td><td>2</td><td>26</td></tr>
                <tr><td>De 400 a 499</td><td>3</td><td>24</td></tr>
                <tr><td>De 300 a 399</td><td>4</td><td>22</td></tr>
                <tr><td>De 200 a 299</td><td>5</td><td>20</td></tr>
                <tr><td>De 150 a 199</td><td>6</td><td>18</td></tr>
                <tr><td>De 100 a 149</td><td>7</td><td>16</td></tr>
                <tr><td>De 50 a 99</td><td>8</td><td>14</td></tr>
                <tr><td>De 25 a 49</td><td>9</td><td>12</td></tr>
                <tr><td>De 0 a 24</td><td>10</td><td>10</td></tr>
            </tbody>
        </table>

        <h3 class="sub-title">Puntos para el Perdedor (Sistema de Suma Cero)</h3>
        <p class="section-description">
            Para que el ranking sea lo más competitivo y justo posible, nuestro sistema es de <strong>"suma cero"</strong>. Esto significa que los puntos no se crean ni se destruyen, simplemente se transfieren. La cantidad de puntos que el perdedor pierde es exactamente la misma que el ganador obtiene.
            <br>Por ejemplo, si el ganador de un partido obtiene <strong>+16 puntos</strong>, el perdedor recibirá <strong>-16 puntos</strong>.
        </p>



        <h3 class="sub-title">Puntos por Posición en el Torneo</h3>
        <p class="section-description">
            Además de los puntos obtenidos por los partidos, los jugadores también reciben puntos por su posición final en el torneo. A continuación, se detallan los puntos otorgados según el lugar ocupado al final del torneo.
        </p>

        <table class="position-points-table">
            <thead>
                <tr>
                    <th>Posición</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1° Lugar</td>
                    <td>30 puntos</td>
                </tr>
                <tr>
                    <td>2° Lugar</td>
                    <td>25 puntos</td>
                </tr>
                <tr>
                    <td>3° Lugar</td>
                    <td>21 puntos</td>
                </tr>
                <tr>
                    <td>4° Lugar</td>
                    <td>17 puntos</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php include 'layout/footer.php'; ?>