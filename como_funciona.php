<?php
include 'layout/header.php';
include 'db.php';
?>

<section class="como-funciona">
    <div class="container">
        <h2 class="section-title">Ranking de Jugadores - Explicación del Sistema de Puntos</h2>
        <p class="section-description">
            El sistema de puntos está diseñado para premiar a quienes vencen a jugadores más fuertes y penalizar más cuando se pierde contra jugadores más débiles. Los puntos se calculan automáticamente según la diferencia de puntuación entre los jugadores.
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
                        Si un jugador con 100 puntos le gana a uno con 200 puntos:<br>
                        El ganador recibe +20 puntos (+10 base + 10 de bonus por ganarle a alguien más fuerte)<br>
                        El perdedor solo pierde -5 puntos base (sin penalización por perder contra alguien más débil)
                    </td>
                </tr>
                <tr>
                    <td>Ganador tiene más puntos que el perdedor</td>
                    <td>10 puntos</td>
                    <td>-5 puntos</td>
                    <td>
                        Si un jugador con 200 puntos le gana a uno con 100 puntos:<br>
                        El ganador recibe +10 puntos base (sin bonus por ser más fuerte)<br>
                        El perdedor pierde -10 puntos (-5 base -5 de penalización por perder contra alguien más fuerte)
                    </td>
                </tr>
            </tbody>
        </table>

        <h3 class="sub-title">Bonificación</h3>
        <p class="section-description">
            Cuando un jugador gana contra alguien que tiene más puntos, recibe un BONUS del 10% de la diferencia. Por ejemplo, si alguien con 150 puntos le gana a alguien con 200, recibirá +10 puntos base más un bonus de (200-150)*0.10 = +5 puntos extra.
        </p>

        <h3 class="sub-title">Resumen de Cálculos:</h3>
        <ul class="calculation-list">
            <li><strong>Si ganas contra alguien con mayor puntaje:</strong> Recibes +10 puntos base + bonus del 10% de la diferencia de puntos</li>
            <li><strong>Si ganas contra alguien con menor puntaje:</strong> Recibes solo +10 puntos base</li>
            <li><strong>Si pierdes contra alguien con mayor puntaje:</strong> Pierdes solo -5 puntos base</li>
            <li><strong>Si pierdes contra alguien con menor puntaje:</strong> Pierdes -5 puntos base + penalización del 5% de la diferencia</li>
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