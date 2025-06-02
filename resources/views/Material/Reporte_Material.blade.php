@extends('layout_especialista/cuerpo')
@section('html')

<!-- ***********************LIBRERIAS PARA LA TABLA**************************** -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<!-- ***********************LIBRERIAS PARA LA TABLA**************************** -->

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REPORTE EDUCATIVO</b></h5>
            @csrf  
                
            <div class="divtabla" id="div_citas">
                <br>
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="width:45px;color:#fff;"><b>Red</b></td>
                                <td style="width:45px;color:#fff;"><b>Cod mod</b></td>
                                <td style="width:35px;color:#fff;"><b>Centro educativo</b></td>
                                <td style="width:35px;color:#fff;"><b>Distrito</b></td>
                                <td style="width:35px;color:#fff;"><b>Nivel</b></td>
                                <td style="width:35px;color:#fff;"><b>Modalidad</b></td>                
                                <td style="width:35px;color:#fff;"><b>Gestión</b></td>
                                <td style="width:35px;color:#fff;"><b>Grado</b></td>
                                <td style="width:35px;color:#fff;"><b>Area</b></td>
                                <td style="width:35px;color:#fff;"><b>Programado</b></td>
                                <td style="width:35px;color:#fff;"><b>Situacion</b></td>
                                <td style="width:35px;color:#fff;"><b>Cantidad</b></td>
                                <td style="width:35px;color:#fff;"><b>Pecosa</b></td>
                                <td style="width:35px;color:#fff;"><b>Observacion</b></td>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>
        </form>

        </div>
    </div>
</div>
<script type="text/javascript">



function tabla_Reporte(){
    ajax_data = {
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_Reporte')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                          //imagen de carga
                          $("#login").html("<p align='center'><img src='http://intranet.ugel01.gob.pe/prestamos/public/images/cargando.gif'/></p>");
                    },
                    error: function(){ 
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                      }
              });
              
}


tabla_Reporte();



    var table4 = $("#t_programas").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "red" },
                            { "data": "codmod" },
                            { "data": "institucion" },
                            { "data": "distrito" },
                            { "data": "nivel" },
                            { "data": "modalidad" },
                            { "data": "gestion" },
                            { "data": "grado" },
                            { "data": "area" },
                            { "data": "programado" },
                            { "data": "situacion" },
                            { "data": "cantidad" },
                            { "data": "Pecosa" },
                            { "data": "observacion" },

                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });
</script>

@csrf

@endsection
