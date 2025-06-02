@extends('layout_especialista/cuerpo')

@section('html')
<div class="container d-flex justify-content-center ">
    <div class="text-center col-md-12">
        <h1 class="display-4 mb-3">Actividades Enconmendadas en Teletrabajo</h1>

    </div>
</div>
<div class="container mt-5 col-md-12">



    <table id="tabla_asistencia" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>N°</th>
                <th>Asignado por</th>

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
                url: '{{route("data-actividad-teletrabajo-respuesta")}}', // Aqui solo llamo al formulario. Esta web no inserta nada.
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
                    data:'nombres'
                },

                {
                    data: 'actividad'
                },
                { data: 'fecha_actividad' },
                { data: 'respuesta',render:function(data, type, row){
                    if(row.situacion != 3)
                    {
                        return `<textarea class="form-control" id="respuesta_${row.id}" name="respuesta_${row.id}" rows="3">${data == null ? '' : data}</textarea><br>
                                <select class="form-control" id="situacion_${row.id}" name="situacion_${row.id}">
                                    <option selected disabled></option>
                                    <option value="2" ${row.situacion == 2 ? 'selected' : ''}>En proceso</option>
                                    <option value="3">Concluido</option>
                                    </select><br>
                        <button type="button" class="btn btn-primary" onclick="save_respuesta(this,${row.id})">Grabar</button>`
                    }else{
                        return data;
                    }
                    },
                },
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

    function save_respuesta(buttonElement,id)
    {
        // Usa jQuery para encontrar los elementos dentro del mismo contenedor (la celda 'td')
        // $(buttonElement) convierte el elemento DOM del botón en un objeto jQuery
        let $button = $(buttonElement);
        // .closest('td') encuentra la celda de tabla más cercana que contiene el botón
        let $cell = $button.closest('td');
        // .find(...) busca los elementos por su ID dentro de esa celda
        let $respuestaInput = $cell.find('#respuesta_' + id);
        let $situacionSelect = $cell.find('#situacion_' + id);

        // Ahora puedes acceder a los elementos jQuery o a sus valores
        let respuestaValue = $respuestaInput.val();
        let situacionValue = $situacionSelect.val();

        console.log("ID:", id);
        console.log("Textarea Element:", $respuestaInput); // Objeto jQuery del textarea
        console.log("Select Element:", $situacionSelect);   // Objeto jQuery del select
        console.log("Respuesta Value:", respuestaValue);
        console.log("Situacion Value:", situacionValue);

        // Aquí iría tu lógica para enviar los datos (por ejemplo, usando fetch o $.ajax)
        // Ejemplo con fetch:

        fetch('{{route("guardar-actividades-respuesta")}}', { // Reemplaza con tu ruta real
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Asegúrate que el token CSRF esté disponible
            },
            body: JSON.stringify({
                id: id,
                respuesta: respuestaValue,
                situacion: situacionValue
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            if(data.data==1)
            {
                $('#tabla_asistencia').DataTable().ajax.reload(null, false); // Recargar sin resetear paginación

            }
            // Podrías actualizar la tabla o mostrar un mensaje
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('Error al guardar la respuesta.');
        });
    }

    document.getElementById('save_actividades').addEventListener('click', function() {
            // Realizar la solicitud para registrar la asistencia
            var ruta = "/guardar-actividades";
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
