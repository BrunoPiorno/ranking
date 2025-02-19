<?php
include 'layout/header.php';
include 'db.php';
?>

<section class="como-funciona">
    <div class="container">
        <h2 class="section-title">Ranking de Jugadores - Explicación del Sistema de Puntos</h2>
        <p class="section-description">
            El sistema de puntos funciona según la diferencia de puntos entre los jugadores y si el ganador tiene más o menos puntos que el perdedor. A continuación, te explicamos cómo se calculan los puntos.
        </p>

        <h3 class="sub-title">Explicación del Sistema de Puntos</h3>
        <table class="points-table">
            <thead>
                <tr>
                    <th>Situación del Partido</th>
                    <th>Puntos Ganados por el Ganador</th>
                    <th>Puntos Perdidos por el Perdedor</th>
                    <th>Ejemplo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Ganador tiene menos puntos que el perdedor</td>
                    <td>10 puntos + Bonificación (10% de la diferencia de puntos)</td>
                    <td>-5 puntos</td>
                    <td class="highlight">
                        Jugador 2 (280 puntos) pierde contra Jugador 1 (217 puntos) 2-0:<br>
                        Jugador 1 recibe 16.3 puntos (10 puntos base + 6.3 puntos de bonificación).<br>
                        Jugador 2 pierde 8.15 puntos (-5 puntos base -3.15 puntos de penalización adicional).
                    </td>
                </tr>
                <tr>
                    <td>Ganador tiene más puntos que el perdedor</td>
                    <td>10 puntos</td>
                    <td>-5 puntos</td>
                    <td>
                        Jugador 2 (280 puntos) gana contra Jugador 1 (217 puntos) 2-0:<br>
                        Jugador 1 recibe 10 puntos.<br>
                        Jugador 2 pierde 5 puntos.
                    </td>
                </tr>
            </tbody>
        </table>

        <h3 class="sub-title">Bonificación</h3>
        <p class="section-description">
            Si el ganador tiene menos puntos que el perdedor antes del partido, recibe una bonificación adicional equivalente al 10% de la diferencia de puntos entre ambos jugadores.
        </p>

        <h3 class="sub-title">Resumen de Cálculos:</h3>
        <ul class="calculation-list">
            <li><strong>Ganador con menos puntos que el perdedor:</strong> 10 puntos base + bonificación (10% de la diferencia).</li>
            <li><strong>Ganador con más puntos que el perdedor:</strong> 10 puntos base.</li>
        </ul>

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