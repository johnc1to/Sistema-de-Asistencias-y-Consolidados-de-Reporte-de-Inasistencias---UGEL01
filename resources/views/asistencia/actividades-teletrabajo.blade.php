@extends('layout_especialista/cuerpo')

@section('html')
<div class="container d-flex justify-content-center ">
    <div class="text-center col-md-12">
        <h1 class="display-4 mb-3">Actividades Enconmendadas en Teletrabajo</h1>

    </div>
</div>
<div class="container mt-5 col-md-12">
     <form action="{{route('guardar-actividades')}}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="id_usuario" class="form-label">Usuario en teletrabajo</label>
            <select class="form-control" id="id_usuario" name="id_usuario">
                <option selected disabled>Seleccione un Usuario</option>
                @isset($persona)
                    @foreach ($persona as $item)
                        <option value="{{$item->idUsuario}}">{{$item->nombres.' '.$item->apellidos}}</option>
                    @endforeach
                @endisset
            </select>

        </div>
        <div class="mb-3">
            <label for="actividad" class="form-label">Actividad</label>
            <textarea class="form-control" id="actividad" name="actividad" rows="3"></textarea>
        </div>
        <button type="button" class="btn btn-primary" id="save_actividades">Guardar</button>
    </form>


    <table id="tabla_asistencia" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>N°</th>
                <th>Nombre completo</th>
                <th>Área</th>
                <th>Equipo</th</th>
                <th>Actividad</th>
                <th>Fecha Act.</th>
                <th>Respuesta</th>
                <th>Fecha Resp.</th>
                <th>Estado</th>

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
                url: '{{route("data-actividad-teletrabajo")}}', // Aqui solo llamo al formulario. Esta web no inserta nada.
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
                    "targets": 0,
                    "searchable": false,
                    "orderable": false,
                }
            ],
            columns: [
               // Columna para el número de fila
                { data: 'created_at' },
                {
                    data:'nombres',render: function(data, type, row) {
                            return data+' '+row.apellidos;
                    }
                },

                {
                    data:'area'
                },
                {
                    data:'equipo'
                },
                {
                    data: 'actividad'
                },
                { data: 'fecha_actividad' },
                { data: 'respuesta' },
                { data: 'fecha_respuesta' },
                { data: 'situacion',render: function(data, type, row) {
                    return (data == 1) ? 'Asignado' : ((data == 2) ? 'En proceso': 'Concluido');
                }},
            ],
            order: [[5, 'desc']], // Ordenar por la primera columna (ID) de forma descendente
            pageLength: 10, // Número de filas por página
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

    document.getElementById('save_actividades').addEventListener('click', function() {
            // Realizar la solicitud para registrar la asistencia
            var ruta = "/public/guardar-actividades";
            var id_usuario = document.getElementById("id_usuario");
            var actividad = document.getElementById("actividad")

            fetch(ruta, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Token CSRF para seguridad
                },
                body: JSON.stringify({ id_usuario: id_usuario.value,actividad: actividad.value })
            })
            .then(response => {
                if (response.ok) {
                    return response.json();

                } else {
                    alert('Error al registrar la asistencia.');
                }
            }).then(data => {

                if(data.data!=null)
                {
                    let table =  $('#tabla_asistencia').DataTable();
                    table.ajax.reload(null, false);
                    return false;
                }
                alert("No se puedo guardar la actividad.");


            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al registrar la asistencia.');
            });
        });
</script>
@endsection
