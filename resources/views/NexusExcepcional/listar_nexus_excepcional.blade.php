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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>NEXUS EXCEPCIONAL</b>
    </h5>
        <div class="position-relative form-group">
                <!--
                <div class="col-sm-4">Nivel:</div>
                <div class="col-sm-8">
                <select class="form-control" name="filtroidnivel" id="filtroidnivel">
                  <option>INICIAL</option>
                  <option>PRIMARIA</option>
                  <option>SECUNDARIA</option>
                  </select>
                </div>

                <div class="col-sm-4">Situacion:</div>
                <div class="col-sm-8">
                <select class="form-control" name="filtrosituacion" id="filtrosituacion">
                  <option>CONTRATADO</option>
                  <option>DESIGNADO</option>
                  <option>DESTACADO</option>
                  <option>ENCARGADO</option>
                  <option>NOMBRADO</option>
                  <option>VACANTE</option>
                  </select>
                </div>

                <div class="col-sm-4">Fecha de inicio:</div>
                <div class="col-sm-8"><input type="date" class="form-control" name="filtrofecinicio" id="filtrofecinicio"></div>
                
                <div class="col-sm-4">Fecha de Termino:</div>
                <div class="col-sm-8"><input type="date" class="form-control" name="filtrofectermino" id="filtrofectermino"></div>
                -->

                <div class="col-sm-4"></div>
                <div class="col-sm-8"><br><button class="btn btn-success" id="añadir" onclick="añadir()">AÑADIR</button></div>
            </div>
            @csrf
        </form>
            
                

            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="width:15px;color:#fff;"><b>N</b></td>
                                <td style="width:55px;color:#fff;"><b>Editar</b></td>
                                <td style="width:35px;color:#fff;"><b>Eliminar</b></td>
                                <td style="width:45px;color:#fff;"><b>Nivel</b></td>
                                <td style="width:45px;color:#fff;"><b>Codigo de Plaza</b></td>
                                <td style="width:45px;color:#fff;"><b>Descargo</b></td>
                                <td style="width:45px;color:#fff;"><b>Situacion</b></td>
                                <td style="width:45px;color:#fff;"><b>Numero de Documento</b></td>
                                <td style="width:45px;color:#fff;"><b>Apellido Paterno</b></td>
                                <td style="width:45px;color:#fff;"><b>Apellido Materno</b></td>
                                <td style="width:45px;color:#fff;"><b>Nombres</b></td>
                                <td style="width:45px;color:#fff;"><b>jornlab</b></td>
                                <td style="width:45px;color:#fff;"><b>Fecha de inicio</b></td>
                                <td style="width:45px;color:#fff;"><b>Fecha de Termino</b></td>
                        
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">

function añadir() {
    formulario.reset();
    $('#descargo').select2();
    $("#btn_doctipo").click();
}
function guardar_nexus_excepcional(id='formulario'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_nexus_excepcional')}}",  
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
                tabla_nexus_excepcional();                
                alert('Guardado');
                $("#btn_doctipo").click();
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
            });
        }

function tabla_nexus_excepcional(){
    ajax_data = {
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_nexus_excepcional')}}",
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
                            data[i]['eliminar']='<span class="btn btn-danger" onclick="eliminar_nexus_excepcional('+data[i]['nexus_id']+')">eliminar</span>';
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                      }
              });
              
}
function editar(nro){
  var data = g_data[nro];
  $("#nexus_id").val(data['nexus_id']);
  $("#codmodce").val(data['codmodce']);
  $("#codplaza").val(data['codplaza']);
  $("#descargo").val(data['descargo']);
  $("#situacion").val(data['situacion']);
  $("#numdocum").val(data['numdocum']);
  $("#codmods").val(data['codmods']);
  $("#apellipat").val(data['apellipat']);
  $("#apellimat").val(data['apellimat']);
  $("#nombres").val(data['nombres']);
  $("#jornlab").val(data['jornlab']);
  $("#fecinicio").val(data['fecinicio'] ? data['fecinicio'].substring(0, 10) : '');
  $("#fectermino").val(data['fectermino'] ? data['fectermino'].substring(0, 10) : '');
  $('#descargo').select2();
  $("#btn_doctipo").click();
}
function cancelar(){
    $("#btn_doctipo").click();
}

function eliminar_nexus_excepcional(nexus_id){
    ajax_data = {
      "nexus_id"   : nexus_id,
      "alt"    : Math.random()
    }
    if(confirm('¿Desea eliminar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_nexus_excepcional')}}",
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
                      tabla_nexus_excepcional();
                      }
              });
      }else{

      }

}
function buscar_nexus_excepcional(){
    ajax_data = {
      "dni"   : $("#numdocum").val(),
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('buscar_nexus_excepcional')}}",
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
                    if(data){
                      $("#nombres").val(data['nombres']);
                      $("#apellipat").val(data['apellipat']);
                      $("#apellimat").val(data['apellimat']);
                      }else{
                        alert('Trabajador no econtrado, completa los campos');
                      }
                    }
              });
}

tabla_nexus_excepcional();

var table4 = $("#t_programas").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "editar" },
                            { "data": "eliminar" },
                            { "data": "descniveduc" },
                            { "data": "codplaza" },
                            { "data": "descargo" },
                            { "data": "situacion" },
                            { "data": "numdocum" },
                            { "data": "apellipat" },
                            { "data": "apellimat" },
                            { "data": "nombres" },
                            { "data": "jornlab" },
                            { "data": "fecinicio" },
                            { "data": "fectermino" },

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


<button id="btn_doctipo" type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">Large modal</button>
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header" style="justify-content:center;">
            <h4 class="modal-title" id="myLargeModalLabel">NEXUS EXCEPCIONAL</h4>
        </div>
        <div class="modal-body">
           <form id="formulario" onsubmit="guardar_nexus_excepcional();return false;">
            <div class="row">
                @csrf
                <div class="col-sm-4">nexus_id:</div>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="nexus_id" id="nexus_id" readonly>
                    <input type="text" class="form-control" name="id_contacto" id="id_contacto" value="<?=$id_contacto?>" readonly>
                    </div>

                  <div class="col-sm-4">Nivel:</div>
                <div class="col-sm-8">
                <!-- <input type="text" class="form-control" name="tipo" id="tipo"></div>-->
                <select class="form-control" name="codmodce" id="codmodce" required>
                <?php
                foreach ($lniveles as $key) {
                ?><option value="<?=$key['esc_codmod']?>"><?=$key['d_niv_mod']?></option><?php
                }
                ?>
                </select>
                </div>

                <div class="col-sm-4">Codigo de Plaza:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="codplaza" id="codplaza" onKeypress="if (event.keyCode < 47 || event.keyCode > 57) event.returnValue = false;" required></div>

                <div class="col-sm-4">Descargo:</div>
                <div class="col-sm-8">
                    <!--<input type="text" class="form-control" name="descargo" id="descargo">--->
                    <select class="form-control" name="descargo" id="descargo" style="width:100%;height:38px;" required>
                    <?php
                    foreach ($lcargos as $key) {
                    ?><option><?=$key->descargo?></option><?php
                    }
                    ?>
                    </select>
                    
                    </div>

                  <div class="col-sm-4">Situacion:</div>
                <div class="col-sm-8">
                <!-- <input type="text" class="form-control" name="tipo" id="tipo"></div>-->
                <select class="form-control" name="situacion" id="situacion" required>
                  <option>CONTRATADO</option>
                  <option>DESIGNADO</option>
                  <option>DESTACADO</option>
                  <option>ENCARGADO</option>
                  <option>NOMBRADO</option>
                  <option>VACANTE</option>
                  </select>
                </div>

                <div class="col-sm-4">Numero de Documento: <a href="#" onclick="buscar_nexus_excepcional();">(Buscar)</a></div>
                <div class="col-sm-8"><input type="text" class="form-control" name="numdocum" id="numdocum" onKeypress="if (event.keyCode < 47 || event.keyCode > 57) event.returnValue = false;" onkeyup="if(this.value.length>7){ buscar_nexus_excepcional(); }" required></div>
        
                <div class="col-sm-4">Apellido Paterno:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="apellipat" id="apellipat" required></div>
                
                <div class="col-sm-4">Apellido Materno:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="apellimat" id="apellimat" required></div>
                
                <div class="col-sm-4">Nombres:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="nombres" id="nombres" required></div>

                <div class="col-sm-4">Jornada laboral:</div>
                <div class="col-sm-8"><input type="text" class="form-control" name="jornlab" id="jornlab" onKeypress="if (event.keyCode < 47 || event.keyCode > 57) event.returnValue = false;" required></div>

                <div class="col-sm-4">Fecha de Inicio:</div>
                <div class="col-sm-8"><input type="date" class="form-control" name="fecinicio" id="fecinicio" required></div>

                <div class="col-sm-4">Fecha de Termino:</div>
                <div class="col-sm-8"><input type="date" class="form-control" name="fectermino" id="fectermino" required></div>

                <div class="col-sm-4"></div>
                <div class="col-sm-8"><br><button class="btn btn-success" id="grabar">GRABAR</button> 
                <input class="btn btn-danger" type="reset" value="Cancelar" onclick="cancelar()"> 
              </div>
            </div>
        </form>

          </div>
    </div>
  </div>
</div>