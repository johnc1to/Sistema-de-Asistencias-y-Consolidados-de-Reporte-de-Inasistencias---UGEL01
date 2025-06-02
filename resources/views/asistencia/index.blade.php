@extends('layout_especialista/cuerpo')

@section('html')

<div class="container d-flex justify-content-center ">

    <div class="text-center col-md-12">

            @isset($teletrabajo)
                @if($teletrabajo->personalteletrabajo == null)
                <h1 class="display-4">¡Usted no cuenta con teletrabajo programado para el dia de hoy!  </h1>

                <p class="lead">Si tienes alguna consulta, por favor contacta a tu supervisor.</p>

                @else
                <h1 class="display-4">¡Bienvenido a la Marcación de Asistencia!  </h1>
                    <p class="lead">Por favor, haz clic en el botón para registrar tu <span id="tipo">asistencia</span> de teletrabajo.</p>

                    <button class="btn btn-primary btn-lg px-5 py-3" id="btn_marcar_asistencia" style="font-size: 1.5rem;background-color:#3B82F6;">Marcar Ingreso</button>

                @endif
            @endisset

            <div id="reloj" class="display-5 my-4" style="font-weight: bold;font-size: 2rem;"></div>
    </div>

</div>
<div class="container mt-5 col-md-12">
    <h1 class="text-center mb-4">Registro de Asistencia</h1>

    <table id="tabla_asistencia" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>

                <th>N°</th>
                <th>Tipo de Marcación</th>
                <th>Día</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Tipo Asistencia</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>

    $(document).ready(function () {
       let table =  $('#tabla_asistencia').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            },
            ajax: {
                url: '{{route("asistencia-teletrabajo")}}', // Aqui solo llamo al formulario. Esta web no inserta nada.
                type: "GET",

            },
            "autoWidth": false,
            responsive: {
                details: {
                    type: 'column',
                    target: 'tr'
                }
            },
            columnDefs: [
                {
                    "render": function(data, type, row, meta) {
                        const today = new Date(row.fecha).toISOString().split('T')[0]; // Fecha actual en formato YYYY-MM-DD
                        return dia(today);
                    },
                    "targets": 2 // Cambia el índice de la columna según tu tabla
                },
                {
                    "targets": 0,
                    "searchable": false,
                    "orderable": false,
                }
            ],
            columns: [
               // Columna para el número de fila
                { data: 'idLogAsistencia' },
                { data: 'entrada_salida',render: function(data, type, row) {
                    return data == 0 ? 'Ingreso' : 'Salida';
                }},
                { data: 'dia' },
                { data: 'fecha' },
                { data: 'hora' },
                { data: 'tipo_asistencia',render: function(data, type, row) {
                    return data == 1 ? 'Teletrabajo' : 'Presencial';
                }},
            ],
            order: [[0, 'desc']], // Ordenar por la primera columna (ID) de forma descendente
            pageLength: 5, // Número de filas por página
            lengthMenu: [5, 10, 25, 50], // Opciones de número de filas por página
            pagingType: 'simple', // Tipo de paginación
            dom: 'Bfrtip', // Elementos a mostrar en la tabla
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6] // Exportar todas las columnas excepto la primera
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Exportar a PDF',
                    className: 'btn btn-danger',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6] // Exportar todas las columnas excepto la primera
                    }
                }
            ]
        });
        table.on('order.dt search.dt', function() {

        table.column(0, {
            search: 'applied',
            order: 'applied'
        }).nodes().each(function(cell, i) {
            table.cell(cell).invalidate('dom');
            cell.innerHTML = i + 1;
        });
        }).draw();
    });

    function dia(today) {
    const daysOfWeek = {
        'Monday': 'Lunes',
        'Tuesday': 'Martes',
        'Wednesday': 'Miércoles',
        'Thursday': 'Jueves',
        'Friday': 'Viernes',
        'Saturday': 'Sábado',
        'Sunday': 'Domingo'
    };

    const date = new Date(`${today}T00:00:00`); // Agrega la hora 00:00:00

    const dayOfWeek = date.toLocaleString('en-US', { weekday: 'long' }); // Obtiene el día en inglés
    //console.log(date)
    return daysOfWeek[dayOfWeek]; // Traduce al español
}
    let hora_salida = 13
      // Función para actualizar el reloj digital
      let horaActual=0;
      let btn= ""
      function actualizarReloj() {
        const ahora = new Date();
        const horas = String(ahora.getHours()).padStart(2, '0');
        const minutos = String(ahora.getMinutes()).padStart(2, '0');
        const segundos = String(ahora.getSeconds()).padStart(2, '0');
        document.getElementById('reloj').textContent = `${horas}:${minutos}:${segundos}`;
        horaActual = horas;
        // Cambiar el color del reloj y el texto del botón según la hora
        if(horas>hora_salida){
                document.getElementById('reloj').style.color = 'red';

            btn =  document.getElementById('btn_marcar_asistencia')
            if(btn){
                document.getElementById('tipo').innerHTML = 'salida';
                btn.innerHTML = 'Marcar Salida';
            }
            }else{
                btn =  document.getElementById('btn_marcar_asistencia')
            }
        }

    // Actualizar el reloj cada segundo
    setInterval(actualizarReloj, 1000);
    actualizarReloj(); // Llamar inmediatamente para mostrar la hora al cargar la página
    if(btn){
        document.getElementById('btn_marcar_asistencia').addEventListener('click', function() {
            // Realizar la solicitud para registrar la asistencia
            var ruta = "/public/registrar-asistencia";
            var boolean = false;
            if(horaActual>hora_salida){
                ruta = "/public/registrar-salida";
                boolean = true;
            }
            fetch(ruta, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Token CSRF para seguridad
                },
                body: JSON.stringify({ tipo: 'teletrabajo' })
            })
            .then(response => {
                if (response.ok) {
                    return response.json();

                } else {
                    alert('Error al registrar la asistencia.');
                }
            }).then(data => {
                if(boolean == false)
                {
                    // Actualizar las horas de ingreso y salida en la interfaz
                    let linkTeams = data.usuario.personalteletrabajo.url_teams.url;
                    // Redirigir al enlace de Teams después de registrar la asistencia
                    let table =  $('#tabla_asistencia').DataTable();
                    table.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación
                    window.open(linkTeams, '_blank');
                }else{
                    let table =  $('#tabla_asistencia').DataTable();
                    table.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación

                    alert(data.message);
                }

            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al registrar la asistencia.');
            });
        });
    }

</script>
@endsection



