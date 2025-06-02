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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>Material Educativo</b>
    </h5>
        <div class="position-relative form-group">
            
        <form id="formulario" onsubmit="grabar_Material();return false;">
            <div class="row">
                <div class="col-sm-4">idMaterial:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="idMaterial" id="idMaterial" readonly></div>

                <div class="col-sm-4">codmod:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="codmod" id="codmod"></div>

                <div class="col-sm-4">grado:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="grado" id="grado"></div>

                <div class="col-sm-4">Area:</div>
                <div class="col-sm-8">
                  <select class="form-control" name="idarea" id="idarea">
                    <option value="">Selecione Area</option>
                    <?php foreach ($area as $key) {
                    ?>
                    <option value="<?=$key->idarea?>"><?=$key->area?></option>
                    <?php
                    }
                    ?>
                </select>
              </div>
                
                <div class="col-sm-4">programado:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="programado" id="programado"></div>

                <div class="col-sm-4">situacion:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="situacion" id="situacion"></div>

                <div class="col-sm-4">cantidad:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="cantidad" id="cantidad" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off"></div>
                
                 <div class="col-sm-4">Pecosa:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="Pecosa" id="Pecosa" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off"></div>

                <div class="col-sm-4">observacion:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="observacion" id="observacion"></div>

                <div class="col-sm-4"></div>
                <div class="col-sm-8"><br><button class="btn btn-success">GRABAR</button> <input class="btn btn-danger" type="reset" value="Cancelar"> </div>
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
                                <td style="width:45px;color:#fff;"><b>codmod</b></td>
                                <td style="width:45px;color:#fff;"><b>grado</b></td>
                                <td style="width:45px;color:#fff;"><b>Area</b></td>
                                <td style="width:35px;color:#fff;"><b>programado</b></td>
                                <td style="width:35px;color:#fff;"><b>situacion</b></td>
                                <td style="width:35px;color:#fff;"><b>cantidad</b></td>
                                <td style="width:35px;color:#fff;"><b>Pecosa</b></td>
                                <td style="width:35px;color:#fff;"><b>observacion</b></td>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">

function grabar_Material(id='formulario'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_Material')}}",  
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
                tabla_Material();
                alert('Guardado');
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
            });
        }

function tabla_Material(){
    ajax_data = {
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
                            data[i]['editar']='<span class="btn btn-success" onclick="editar('+i+')">editar</span>';
                            data[i]['eliminar']='<span class="btn btn-danger" onclick="eliminar_Material('+data[i]['idMaterial']+')">eliminar</span>';
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                      }
              });
              
}
function editar(nro){
  var data = g_data[nro];
  $("#idMaterial").val(data['idMaterial']);
  $("#codmod").val(data['codmod']);
  $("#grado").val(data['grado']);
  $("#idarea ").val(data['idarea']);
  $("#programado").val(data['programado']);
  $("#situacion").val(data['situacion']);
  $("#cantidad").val(data['cantidad']);
  $("#Pecosa").val(data['Pecosa']);
  $("#observacion").val(data['observacion']);

}

function eliminar_Material(idMaterial){
    ajax_data = {
      "idMaterial"   : idMaterial,
      "alt"    : Math.random()
    }
    if(confirm('¿Desea eliminar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_Material')}}",
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
                      tabla_Material();
                      }
              });
      }else{

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
                            { "data": "nro" },
                            { "data": "editar" },
                            { "data": "eliminar" },
                            { "data": "codmod" },
                            { "data": "grado" },
                            { "data": "area" },
                            { "data": "programado" },
                            { "data": "situacion" },
                            { "data": "cantidad" },
                            { "data": "Pecosa" },
                            { "data": "observacion" },
                         

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

