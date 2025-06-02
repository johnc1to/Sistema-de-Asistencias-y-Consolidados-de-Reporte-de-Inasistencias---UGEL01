@extends('layout_director/cuerpo')
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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REGISTRO DE MATERIAL EDUCATIVO ENTREGADO</b>
    </h5>
        <div class="position-relative form-group">
            
        <form id="formulario" onsubmit="guardar_Material_Masivo();return false;">
            
            <div class="row">
                <label class="col-sm-1 control-label" for="field-1" style="color:#000;text-align:right;"><b>NIVEL: </b></label>
    			<div class="col-sm-3">
    				<select name="codmod" id="codmod" class="form-control" onchange="tabla_Material()">
    					<?php
    					for ($i=0; $i < count($session['conf_permisos']); $i++) {
    					$key = $session['conf_permisos'][$i];
    					?><option value="<?=$key['esc_codmod']?>"><?=$key['nivel_pap']?></option><?php
    					}
    					?>
    				</select>
    			</div>
    			<div class="col-sm-7"><button class="btn btn-success">GRABAR</button></div>
            </div>
            <!--
            <div class="row">
                <div class="col-sm-1"><b>Area:</b></div>
                <div class="col-sm-4">
                  <select class="form-control" name="idarea" id="idarea" onchange="tabla_Material()">
                    <option value="">Selecione Area</option>
                    <?php foreach ($area as $key) {
                    ?>
                    <option value="<?=$key->idarea?>"><?=$key->area?></option>
                    <?php
                    }
                    ?>
                </select>
              </div>
              <div class="col-sm-7"><button class="btn btn-success">GRABAR</button></div>
            </div>
            -->

            @csrf  
                
            <div class="divtabla" id="div_citas">
                <br>
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);font-size:14px;">
                                <td style="width:45px;color:#fff;max-width:50px;"><b>Grado</b></td>
                                <td style="width:45px;color:#fff;"><b>Area</b></td>
                                <td style="width:35px;color:#fff;max-width:70px;"><b>Programado</b></td>
                                <td style="width:35px;color:#fff;"><b>Situacion</b></td>                
                                <td style="width:35px;color:#fff;"><b>Cantidad</b></td>
                                <td style="width:35px;color:#fff;"><b>N° de Pecosa</b></td>
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
    
function guardar_Material_Masivo(id='formulario'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_Material_Masivo')}}",  
              type: 'POST',
              data: formData,
              dataType: "json",
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  
              },
              success: function(data){
                tabla_Material();
                alert('Guardado');
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
            });
        }

function selected(opt,respuesta){
  return (opt==respuesta)?'selected':'';
}

function tabla_Material(){
    ajax_data = {
        "idarea" : $("#idarea").val(),
        "codmod" : $("#codmod").val(),
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_Material')}}",
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
                          for (let i = 0; i < data.length; i++) {
                            data[i]['nro']= i+1;
                            //data[i]['t_situacion']='<input type="text" value="'+data[i]['situacion']+'" class="form-control" name="situacion" autocomplete="off">';
                            data[i]['t_programado']='<div style="font-size:14px;text-align:center;font-weight: bolder;">'+data[i]['programado']+'</div>';
                            data[i]['t_grado']='<div style="font-size:13px;text-align:center;font-weight: bolder;">'+data[i]['grado']+'</div>'+'<input type="hidden" value="'+data[i]['idMaterial']+'" name="idMaterial[]">';
                            data[i]['t_situacion']='<select class="form-control" onchange="opt_situacion(this);" name="situacion[]" required><option value="">Elegir Situacion</option><option '+selected(data[i]['situacion'],'Completo')+'>Completo</option><option '+selected(data[i]['situacion'],'Sobrante')+'>Sobrante</option><option '+selected(data[i]['situacion'],'Faltante')+'>Faltante</option></select>';
                            data[i]['n_cantidad']='<input type="text" value="'+data[i]['cantidad']+'" class="form-control cantidad" required '+((data[i]['situacion']=='Completo')?'readonly':'')+' name="cantidad[]" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off">';
                            data[i]['n_Pecosa']='<input type="text" value="'+data[i]['Pecosa']+'" class="form-control Pecosa" required name="Pecosa[]" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off">';
                            data[i]['t_observacion']='<input type="text" value="'+data[i]['observacion']+'" class="form-control" name="observacion[]" autocomplete="off">';

                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                      }
              });     
}

var g_this;
function opt_situacion(athis){
  g_this = athis;
  txt_cantidad = $(athis).parent().parent().children('td').children('.cantidad');
  switch (athis.value) {
    case 'Completo':{ $(txt_cantidad).val(0); $(txt_cantidad).prop('readOnly',true); } break;
    default: {  $(txt_cantidad).prop('readOnly',false); } break;
  }
}




tabla_Material();



    var table4 = $("#t_programas").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "t_grado" },
                            { "data": "area" },
                            { "data": "t_programado" },
                            { "data": "t_situacion" },
                            { "data": "n_cantidad" },
                            { "data": "n_Pecosa" },
                            { "data": "t_observacion" },
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
