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

<style type="text/css">
	#t_mantenimiento thead tr th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #ffffff;
        }
    
    .header{
        background-color:Salmon;
    }
</style>


<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>RESUMEN REGISTRO DE EQUIPOS INFORMATICOS</b></h5>
        <div class="position-relative form-group">
            
            <form id="formulario01" enctype="multipart/form-data" style="width:100%;" onsubmit="return false;">

            <div class="col-sm-5" style="color:#000;">
                <select id="idtipo" class="form-control" onchange="get_inv_resumen();">
                    <?php
                    foreach ($tipo as $key) {
                    ?>
                    <option value="<?=$key['idTipo']?>"><?=$key['descripcion']?></option>
                    <?php
                    }
                    ?>
                </select>
                
            </div>

            <div class="col-sm-12" style="color:#000;"><br></div>
			<div class="col-sm-12" style="color:#000;"><u><b>RECOMENDACIONES:</b></u></div>
			<div class="col-sm-12" style="color:#000;"><b>(*) Aquí podrás visualizar el avance del llenado del formulario en general y por institución educativa.</b></div>
			<div class="col-sm-12" style="color:#000;"><b>(*) Para descargar el reporte completo presion el botón DESCARGAR REPORTE EN EXCEL.</b> </div>
			<div class="col-sm-12" style="color:#000;"><br></div>
			
			<div class="row" style="color:#000;">            
                <div class="col-sm-10">
                     <!--<button class="btn btn-danger" onclick="get_inv_resumen();" id="btn_guardar">ACTUALIZAR</button> -->
                     <a id="link" href="" target="_blank" class="btn btn-success">DESCARGAR REPORTE EN EXCEL</a>
                </div>
                
                <div class="col-sm-2">

                 <table border="1" style="text-align:center;">
		            <tr>
		                <td style="background-color:Salmon;padding-right:10px;padding-left:10px;"><b>TOTAL</b></td>
		                <td style="padding-right:15px;padding-left:15px;" id="total"></td>
		            </tr>
		        </table>
                <!-- 
                <table border="1" style="text-align:center;">
		            <tr style="background-color:Salmon;">
		                <td><b>TOTAL</b></td>
		            </tr>
		            <tr>
		                <td id="total"></td>
		            </tr>
		        </table>
		        -->
		        </div>
		        
            </div>
            <div class="col-sm-12" style="color:#000;"><br></div>
			<div class="col-sm-12" style="color:#000;font-size:10px;padding-left:0px;width:100%;">
                <table class="display table table-bordered" id="t_mantenimiento" style="color:#000;font-size:13px;">
                  <thead>
                    <tr>
                        <th class="header" scope="col" style="background-color:Salmon;"><b>N°</b></th>
                        <th class="header" scope="col" style="background-color:Salmon;"><b>Codlocal</b></th>
                        <th class="header" scope="col" style="background-color:Salmon;"><b>Institución</b></th>
                        <th class="header" scope="col" style="background-color:Salmon;"><b>Registrarón</b></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
            </div>

            @csrf
            </form>

        </div>
    </div>
</div>

<script>

    function get_inv_resumen(){
        var idtipo = $("#idtipo").val();
        $("#link").prop("href","{{route('excel_equipos_informaticos')}}?idtipo="+idtipo);
                ajax_data = {
                        "_token"  : $("input[name='_token'").val(),
                        "idtipo"  : $("#idtipo").val(),
                        "alt"     : Math.random()
                    }
                    $.ajax({
                                    type: "POST",
                                    url: "{{route('get_inv_resumen')}}",
                                    data: ajax_data,
                                    dataType: "json",
                                    beforeSend: function(){
                                        $("#btn_guardar").prop('disabled',true);
                                        $("#spreadsheet").html('<center><img src="assets/images/load10.gif"></center>');
                                    },
                                    error: function(){
                                        alert("error peticion ajax");
                                    },
                                    success: function(data){
                                        var total = 0;
                                        for (let i = 0; i < data.length; i++) {
                                            data[i]['nro'] = i+1;
                                            total = total + parseInt(data[i]['total']);
                                        }
                                        $("#total").html(total);
                                        table4.clear().draw();
                                        table4.rows.add(data).draw();
                                        $("#btn_guardar").prop('disabled',false);
                                    }
                            });
            }


    var table4 = $("#t_mantenimiento").DataTable( {
                        dom: 'Bfrtip',
                        buttons: [],//excel
                        "iDisplayLength": 10000,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "codlocal" },
                            { "data": "institucion" },
                            { "data": "total" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

    get_inv_resumen();
    $(".app-container").removeClass('fixed-header');
</script>

@endsection