# Sistema de Ranking

Este es un sistema de ranking para gestionar jugadores, partidos y puntajes. Permite registrar jugadores, agregar partidos, calcular puntos automáticamente y visualizar el ranking actualizado.

## Características
- Registro y gestión de jugadores.
- Agregar partidos con resultados.
- Cálculo automático de puntos.
- Visualización de ranking y perfil de jugadores.
- Seguridad en la base de datos mediante `intval()` y `htmlspecialchars()`.

## Requisitos
- Servidor web con PHP (Apache recomendado).
- Base de datos MySQL.
- PHP 7.4 o superior.
- Composer (opcional para futuras mejoras).

## Instalación
1. Clona este repositorio:
   ```bash
   git clone https://github.com/BrunoPiornoteam/ranking.git
   ```
2. Ingresa al directorio del proyecto:
   ```bash
   cd ranking
   ```
3. Configura la base de datos:
   - Crea una base de datos en MySQL.
   - Importa el archivo `database.sql` (si está disponible).
   - Configura las credenciales en `db.php`.

## Uso
1. Inicia el servidor local (si usas PHP nativo):
   ```bash
   php -S localhost:8888
   ```
2. Abre en tu navegador:
   ```
   http://localhost:8888<?= url('/') ?>
   ```
3. Registra jugadores y partidos desde la interfaz.
4. Consulta el ranking actualizado.

## Archivos principales
- `index.php` → Página principal del sistema.
- `ranking.php` → Muestra el ranking de jugadores.
- `player_profile.php` → Perfil de cada jugador.
- `add_player.php` → Formulario para agregar jugadores.
- `add_match.php` → Formulario para agregar partidos.
- `calculate_points.php` → Lógica para calcular los puntos.

## Contribuciones
Si deseas mejorar este sistema, ¡eres bienvenido! Puedes hacer un fork, crear una nueva rama y enviar un pull request.

## Licencia
Este proyecto está bajo la licencia MIT.

