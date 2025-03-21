$(document).ready(function() {
    // Inicializar Select2 en los selectores de jugadores
    $('.select2-player').select2({
        placeholder: 'Buscar jugador...',
        allowClear: true,
        language: {
            noResults: function() {
                return "No se encontraron jugadores";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });

    // Evitar que se seleccione el mismo jugador en ambos selectores
    $('.select2-player').on('select2:select', function(e) {
        var selectedId = e.params.data.id;
        var otherSelect = $('.select2-player').not(this);
        
        if(otherSelect.val() === selectedId) {
            otherSelect.val('').trigger('change');
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'No puedes seleccionar el mismo jugador dos veces',
                confirmButtonColor: '#1c4857'
            });
        }
    });

    $('#yearSelect').select2({
        width: '200px'
    }).on('change', function() {
        window.location.href = 'tournament.php?year=' + this.value;
    });
});