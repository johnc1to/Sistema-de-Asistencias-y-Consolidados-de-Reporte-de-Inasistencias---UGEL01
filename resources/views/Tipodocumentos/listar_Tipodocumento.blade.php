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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>TIPO DOCUMENTO</b>
    </h5>
        <div class="position-relative form-group">
          
            
        <form id="formulario" style="max-width:800px;" onsubmit="guardar_tipodocumento();return false;" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-2">id_tipo:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="id_tipo" id="id_tipo" readonly></div>

                <div class="col-sm-2">nivel:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="nivel" id="nivel"></div>
                
                <div class="col-sm-2">idnivel:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="idnivel" id="idnivel"></div>
                
                <div class="col-sm-2">idmodalidad:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="idmodalidad" id="idmodalidad"></div>

                <div class="col-sm-2">grupo:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="grupo" id="grupo"></div>

                <div class="col-sm-2">tipo_documento:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="tipo_documento" id="tipo_documento"></div>

                <div class="col-sm-2">extenciones:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="extenciones" id="extenciones"></div>

                <div class="col-sm-2">visble:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="visble" id="visble"></div>
                
                <div class="col-sm-2">aprobado1:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="aprobado1" id="aprobado1"></div>

                <div class="col-sm-2">idarea1:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="idarea1" id="idarea1"></div>

                <div class="col-sm-2">orden:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="orden" id="orden"></div>

                <div class="col-sm-2">codlocal_habilitado:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="codlocal_habilitado" id="codlocal_habilitado"></div>

                <div class="col-sm-2">informar_al_director:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="informar_al_director" id="informar_al_director"></div>

                <div class="col-sm-2"></div>
                <div class="col-sm-10"><br><button class="btn btn-success">GRABAR</button> <input class="btn btn-danger" type="reset" value="Cancelar"> </div>
            
                <div class="col-sm-12"><br></div>

                <div class="col-sm-2">Año:</div>
                <div class="col-sm-3">
                <!-- <input type="text" class="form-control" name="tipo" id="tipo"></div>-->
                <select class="form-control" name="anio" id="anio" onchange="tabla_tipodocumento();">
                  <option value="2024">2024</option>  
                  <option value="2023">2023</option>
                  <option value="2022">2022</option>
                  <option value="2021">2021</option>
                  <option value="2020">2020</option>
                  <option value="2019">2019</option>
                  </select>
                </div>
            
            
              </div>
            
            @csrf
        </form>
            
            <div class="divtabla" id="div_citas">
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="width:15px;color:#fff;"><b>N</b></td>
                                <td style="width:55px;color:#fff;"><b>Editar</b></td>
                                <td style="width:35px;color:#fff;"><b>Eliminar</b></td>
                                <td style="width:45px;color:#fff;"><b>copiar</b></td>
                                <td style="width:45px;color:#fff;"><b>nivel</b></td>
                                <td style="width:45px;color:#fff;"><b>idnivel</b></td>
                                <td style="width:55px;color:#fff;"><b>idmodalidad</b></td>
                                <td style="width:35px;color:#fff;"><b>grupo</b></td>
                                <td style="width:35px;color:#fff;"><b>tipo_documento</b></td>
                                <td style="width:35px;color:#fff;"><b>extenciones</b></td>
                                <td style="width:35px;color:#fff;"><b>visble</b></td>
                                <td style="width:35px;color:#fff;"><b>aprobado1</b></td>
                                <td style="width:35px;color:#fff;"><b>idarea1</b></td>
                                <td style="width:35px;color:#fff;"><b>orden</b></td>
                                <td style="width:35px;color:#fff;"><b>codlocal_habilitado</b></td>
                                <td style="width:35px;color:#fff;"><b>informar_al_director</b></td>

                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">

function guardar_tipodocumento(id='formulario'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_tipodocumento')}}",  
              type: 'POST',
              data: formData,
              dataType: "json",
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  
              },
              success: function(data){
                formulario.reset();
                tabla_tipodocumento();
                alert('Guardado');
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
            });
        }

function tabla_tipodocumento(){
    ajax_data = {
      "anio"   : $("#anio").val(),
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_tipodocumento')}}",
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
                            data[i]['editar']='<span class="btn btn-success" onclick="editar('+i+')">editar</span>';
                            data[i]['copiar']='<span class="btn btn-warning" onclick="copiar('+i+')">copiar</span>';
                            data[i]['eliminar']='<span class="btn btn-danger" onclick="eliminar_tipodocumento('+data[i]['id_tipo']+')">eliminar</span>';
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                      }
              });
              
}
function editar(nro){
  var data = g_data[nro];
  $("#id_tipo").val(data['id_tipo']);
  $("#nivel").val(data['nivel']);
  $("#idnivel").val(data['idnivel']);
  $("#idmodalidad").val(data['idmodalidad']);
  $("#grupo").val(data['grupo']);
  $("#tipo_documento").val(data['tipo_documento']);
  $("#extenciones").val(data['extenciones']);
  $("#visble").val(data['visble']);
  $("#aprobado1").val(data['aprobado1']);
  $("#idarea1").val(data['idarea1']);
  $("#orden").val(data['orden']);
  $("#codlocal_habilitado").val(data['codlocal_habilitado']);
  $("#informar_al_director").val(data['informar_al_director']);
}
  function copiar(nro){
  var data = g_data[nro];
  $("#nivel").val(data['nivel']);
  $("#idnivel").val(data['idnivel']);
  $("#idmodalidad").val(data['idmodalidad']);
  $("#grupo").val(data['grupo']);
  $("#tipo_documento").val(data['tipo_documento']);
  $("#extenciones").val(data['extenciones']);
  $("#visble").val(data['visble']);
  $("#aprobado1").val(data['aprobado1']);
  $("#idarea1").val(data['idarea1']);
  $("#orden").val(data['orden']);
  $("#codlocal_habilitado").val(data['codlocal_habilitado']);
  $("#informar_al_director").val(data['informar_al_director']);
}

function eliminar_tipodocumento(id_tipo){
    ajax_data = {
      "id_tipo"   : id_tipo,
      "alt"    : Math.random()
    }
    if(confirm('¿Desea eliminar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_tipodocumento')}}",
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
                      alert('eliminado');
                      tabla_tipodocumento();
                      }
              });
      }else{

      }

}


tabla_tipodocumento();

var table4 = $("#t_programas").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "editar" },
                            { "data": "eliminar" },
                            { "data": "copiar" },
                            { "data": "nivel" },
                            { "data": "idnivel" },
                            { "data": "idmodalidad" },
                            { "data": "grupo" },
                            { "data": "tipo_documento" },
                            { "data": "extenciones" },
                            { "data": "visble" },
                            { "data": "aprobado1" },
                            { "data": "idarea1" },
                            { "data": "orden" },
                            { "data": "codlocal_habilitado" },
                            { "data": "informar_al_director" },

  
                            //{ "data": "eliminar" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

</script>

@endsection
                      
