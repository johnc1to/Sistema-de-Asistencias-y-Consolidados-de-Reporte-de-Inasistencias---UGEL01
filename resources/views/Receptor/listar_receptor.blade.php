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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="main-card mb-12 card" style="max-width:10000px;">
    <div class="card-body">
      <h5 class="card-title" style="font-size:20px;"><b>BANDEJA DE RECEPTOR</b></h5>
      
            <div class="position-relative form-group">

            <!--ROW--->
            <!--No debe se mayor a 12-->
            <div class="row">
            <div class="col-sm-12"><b>Escribe el DNI del receptor o su nombre y presiona buscar.</b></div>
            <div class="col-sm-2"><input type="text" placeholder="DNI" class="form-control" name="buscar_dni" id="buscar_dni" required=""></div>
            <div class="col-sm-2"><input type="text" placeholder="Nombre, correo o celular" class="form-control" name="buscar_nombre" id="buscar_nombre" required=""></div>
            <div class="col-sm-2"><input type="text" placeholder="Apellido Paterno" class="form-control" name="buscar_apellido_paterno" id="buscar_apellido_paterno" required=""></div>
            <div class="col-sm-2"><input type="text" placeholder="Apellido Materno" class="form-control" name="buscar_apellido_materno" id="buscar_apellido_materno" required=""></div>


            </div>
            <!--ROW--->
            <br>
            <div class="col-sm-4"><span class="btn btn-success" onclick="tabla_receptor()">Buscar Receptor</span></div>

            <div class="divtabla" id="div_citas">
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
              <thead>
                <tr style="background-color:rgb(0,117,184);">
                    <td style="width:15px;color:#fff;"><b>N</b></td>
                    <!--<td style="width:45px;color:#fff;"><b>Eliminar</b></td>-->
                    <td style="width:45px;color:#fff;"><b>Restablecer clave</b></td>
                    <td style="width:45px;color:#fff;"><b>Editar</b></td>
                    <!--<td style="width:45px;color:#fff;"><b>Estado</b></td>-->
                    <td style="width:45px;color:#fff;"><b>N° de documento</b></td>
                    <td style="width:45px;color:#fff;"><b>Nombre</b></td>
                    <td style="width:45px;color:#fff;"><b>Apellido Paterno</b></td>
                    <td style="width:45px;color:#fff;"><b>Apellido Materno</b></td>
                    <td style="width:55px;color:#fff;"><b>Correo</b></td>
                    <td style="width:55px;color:#fff;"><b>Celular</b></td>
                    <td style="width:55px;color:#fff;"><b>Dirección Domiciliaria</b></td>
                    <td style="width:55px;color:#fff;"><b>Departamento</b></td>
                    <td style="width:55px;color:#fff;"><b>Provincia</b></td>
                    <td style="width:55px;color:#fff;"><b>Distrito</b></td>
                    <td style="width:55px;color:#fff;"><b>Tipo Documentos</b></td>

                </tr>
              </thead>
              <tbody></tbody>
            </table>
            </div>
          </div>
    </div>
</div>



<script type="text/javascript">


function tabla_receptor(){
    ajax_data = {
      "dni"    : $("#buscar_dni").val(),
      "nombre"    : $("#buscar_nombre").val(),
      "apellido_paterno"    : $("#buscar_apellido_paterno").val(),
      "apellido_materno"    : $("#buscar_apellido_materno").val(),
      "alt"    : Math.random()
    }
    
    if( ajax_data['dni']=='' && ajax_data['nombre']=='' && ajax_data['apellido_paterno']=='' && ajax_data['apellido_materno']==''  ){ alert('Complete al menos uno de los filtros'); return false; }
    
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_receptor')}}",
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
                            data[i]['editar']='<span class="btn btn-success" onclick="editar('+ data[i]['idreceptor']+')" data-toggle="modal" data-target=".bd-example-modal-lg">editar</span>';
                            //data[i]['eliminar']='<span class="btn btn-danger" onclick="eliminar_receptor('+data[i]['idreceptor']+')">eliminar</span>';
                            data[i]['clave']='<span class="btn btn-warning" onclick="cambiar_clave('+data[i]['idreceptor']+')">clave</span>';
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                      }
              });
              
}
function editar(idreceptor){
  ajax_data = {
      "idreceptor"   : idreceptor,
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('ver_editar_receptor')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){
                          //imagen de carga
                          $("#login").html("<p align='center'><img src='http://intranet.ugel01.gob.pe/prestamos/public/images/cargando.gif'/></p>");
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                      $("#modal01").html(data);
                      }
              });
}

function eliminar_receptor(idreceptor){
    ajax_data = {
      "idreceptor"   : idreceptor,
      "alt"    : Math.random()
    }
    if(confirm('¿Desea eliminar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_receptor')}}",
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
                      tabla_receptor();
                      }
              });
      }else{

      }

}

function cambiar_clave(idreceptor){
    ajax_data = {
      "idreceptor"   : idreceptor,
      "alt"    : Math.random()
    }
    if(confirm('¿Deceas Cambiar la Clave?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('cambiar_clave')}}",
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
                      alert('Clave Cambiado');
                      tabla_receptor();
                      }
              });
      }else{
        
      }

}



//tabla_receptor();

function ver_editar_receptor(idreceptor){
    ajax_data = {
      "idreceptor"   : idreceptor,
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "http://localhost/ventanillavirtual/public/ver_reclamo_y_observaciones",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){
                          //imagen de carga
                          $("#login").html("<p align='center'><img src='http://intranet.ugel01.gob.pe/prestamos/public/images/cargando.gif'/></p>");
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                      $("#modal01").html(data);
                      }
              });
}

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
                            //{ "data": "eliminar" },
                            { "data": "clave"},
                            { "data": "editar" },
                            //{ "data": "estado" },
                            { "data": "documento" },                          
                            { "data": "nombres" }, 
                            { "data": "apellido_paterno" }, 
                            { "data": "apellido_materno" }, 
                            { "data": "correo" }, 
                            { "data": "celular" }, 
                            { "data": "texto_domicilio" }, 
                            { "data": "departamento" }, 
                            { "data": "provincia" }, 
                            { "data": "distrito" }, 
                            { "data": "tipodocumento" }, 

                          
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


<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="modal01" style="padding:20px;">
      ...
    </div>
  </div>
</div>
<div id="div_modal01" data-toggle="modal" data-target=".bd-example-modal-lg"></div>
<!--
<button type="button" class="btn btn-primary" onclick="ver_reclamo_y_observaciones(328552);" data-toggle="modal" data-target=".bd-example-modal-lg">MPT2023-EXT-0154454</button>
-->