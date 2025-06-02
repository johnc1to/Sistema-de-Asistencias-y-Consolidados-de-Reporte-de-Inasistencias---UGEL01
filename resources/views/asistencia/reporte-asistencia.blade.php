@extends('layout_especialista/cuerpo')

@section('html')
<div class="container d-flex justify-content-center ">
    <div class="text-center col-md-12">
        <h1 class="display-4">Bienvenido al reporte de asistencia de teletrabajo</h1>
    </div>
</div>
<div class="container mt-5 col-md-12">
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
                url: '{{route("reporte-asistencia-teletrabajo")}}', // Aqui solo llamo al formulario. Esta web no inserta nada.
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
            order: [[3, 'desc']], // Ordenar por la primera columna (ID) de forma descendente
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
</script>
@endsection
