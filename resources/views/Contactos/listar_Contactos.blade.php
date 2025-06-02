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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>CONTACTOS</b>
    </h5>
    <div class="row">
        <div class="col-sm-12" style="color:#000;">
				    <input type="radio" name="box" value="Publica" onclick="tabla_Contactos();" checked=""> IE Publica
				    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				    <input type="radio" name="box" value="Privada" onclick="tabla_Contactos();"> IE Privada
				    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				    <input type="radio" name="box" value="Publica" onclick="tabla_Contactos_elimados();"> Dados de baja
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="https://siic01.ugel01.gob.pe/index.php/login/registro_particulares" class="btn btn-success" target="_blank">Añadir Director</a>
            <!--<a href="https://siic01.ugel01.gob.pe/index.php/rrhh/sincronizar_iiee_a_evaluar_rie" class="btn btn-danger" target="_blank">Sicronizar Directores</a>-->
            <a href="https://siic01.ugel01.gob.pe/index.php/rrhh/sincronizar_iiee_a_evaluar_rie" class="btn btn-danger" target="_blank" style="float:right;margin-right:6px;">Sicronizar Directores</a>
            <a href="{{route('nexus_dir')}}" class="btn btn-danger" target="_blank" style="float:right;float:right;margin-right:6px;">Nexus vs Siic01</a>
            <a href="#" onClick="return popitup('https://apps.ugel01.gob.pe/excel-proyecto/public/vistaplazas');" class="btn btn-danger" target="_blank" style="float:right;float:right;margin-right:6px;">Cargar Nexus</a>
        </div>
        <div class="col-sm-12" style="color:#000;">
        <b>Al precionar el botón restablecer Contraseña, se restabelcerá a: IIee<?=date('Y')?></b>
        </div>
        
    </div>

        <br>
        <div class="position-relative form-group">
        
        <script>
          function mostrarformulario(){
          var texto='';
          texto+='<div class="card-body">';
          texto+='<form id="formulario" style="max-width:800px;" onsubmit="grabar_Contactos();return false;" enctype="multipart/form-data">';
          texto+='<div class="row">';
          texto+='<div class="col-sm-12" style="text-align:center;font-size:20px;"><b>EDITAR CONTACTO</b></div>';
          texto+='<div class="col-sm-12"><br></div>';
          texto+='<div class="col-sm-2">id_contacto :</div>';
          texto+='<div class="col-sm-10"><input type="text" class="form-control" name="id_contacto" id="id_contacto" readonly></div>';
          texto+='<div class="col-sm-2">DNI:</div>';
          texto+='<div class="col-sm-10"><input type="text" class="form-control" name="usuario" id="usuario" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off"></div>';
          texto+='<div class="col-sm-2">Nombres:</div>';
          texto+='<div class="col-sm-10"><input type="text" class="form-control" name="nombres" id="nombres"></div>';
          texto+='<div class="col-sm-2">Apellido Paterno:</div>';
          texto+='<div class="col-sm-10"><input type="text" class="form-control" name="apellipat" id="apellipat"></div>';
          texto+='<div class="col-sm-2">Apellido Materno:</div>';
          texto+='<div class="col-sm-10"><input type="text" class="form-control" name="apellimat" id="apellimat"></div>';
          texto+='<div class="col-sm-2">Celular:</div>';
          texto+='<div class="col-sm-10"><input type="text" class="form-control" name="celular_pers" id="celular_pers" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off"></div>';
          texto+='<div class="col-sm-2">Correo:</div>';
          texto+='<div class="col-sm-10"><input type="text" class="form-control" name="correo_pers" id="correo_pers"></div>';
          texto+='<div class="col-sm-2"></div>';
          texto+='<div class="col-sm-10"><br><button class="btn btn-success">GRABAR</button><input class="btn btn-danger" type="reset" value="Cancelar" onclick="$('+"'"+'#fc_popup'+"'"+').click();"> </div>';
          texto+='</div>';
          texto+='@csrf';
          texto+='</form>';
          texto+='</div>';
          $("#popup01 .modal-content").html(texto);
          $("#fc_popup").click();
          }
          
          function popitup(url)
            {
            	newwindow=window.open(url,'name','height=300,width=400'); 
            	if (window.focus) {newwindow.focus()}
            	return false;
            }

        </script>
        <!--
        <form id="formulario" style="max-width:800px;" onsubmit="grabar_Contactos();return false;" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-2">id_contacto :</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="id_contacto" id="id_contacto" readonly></div>

                <div class="col-sm-2">DNI:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="usuario" id="usuario" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off"></div>

                <div class="col-sm-2">Nombres:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="nombres" id="nombres"></div>

                <div class="col-sm-2">Apellido Paterno:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="apellipat" id="apellipat"></div>

                <div class="col-sm-2">Apellido Materno:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="apellimat" id="apellimat"></div>

                <div class="col-sm-2">Celular:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="celular_pers" id="celular_pers" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off"></div>

                <div class="col-sm-2">Correo:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="correo_pers" id="correo_pers"></div>

                
                <div class="col-sm-2"></div>
                <div class="col-sm-10"><br><button class="btn btn-success">GRABAR</button><input class="btn btn-danger" type="reset" value="Cancelar"> </div>

            </div>

        </form>
-->
        <div class="col-sm-12" style="color:#000;" id="div_load"><center><img src="../public/img/load10.gif"></center></div>
            <div  class="divtabla" id="div_instituciones">
            <br>
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="width:15px;color:#fff;"><b>Nro</b></td>
                                <td style="width:35px;color:#fff;"><b>Codlocal</b></td>
                                <td style="width:45px;color:#fff;"><b>Codmod</b></td>
                                <td style="width:45px;color:#fff;"><b>Red</b></td>
                                <td style="width:45px;color:#fff;"><b>Institucion</b></td>
                                <td style="width:45px;color:#fff;"><b>Modalidad</b></td>
                                <td style="width:45px;color:#fff;"><b>Nivel</b></td>
                                <td style="width:45px;color:#fff;"><b>Distrito</b></td>
                                <td style="width:35px;color:#fff;"><b>Gestion</b></td>
                                <td style="width:35px;color:#fff;"><b>Gestion Dependencia</b></td>
                                <td style="width:35px;color:#fff;"><b>DNI</b></td>
                                <td style="width:35px;color:#fff;"><b>Director</b></td>
                                <td style="width:35px;color:#fff;"><b>Celular</b></td>
                                <td style="width:35px;color:#fff;"><b>Correo Institucional</b></td>
                                <td style="width:35px;color:#fff;"><b>Correo Personal</b></td>
                                <td style="width:35px;color:#fff;"><b>Restablecer contraseña</b></td>
                                <td style="width:35px;color:#fff;"><b>Dar de baja</b></td>
                                <td style="width:55px;color:#fff;"><b>Editar</b></td>
                                <td style="width:55px;color:#fff;"><b>Nombres</b></td>
                                <td style="width:55px;color:#fff;"><b>Apellido Paterno</b></td>
                                <td style="width:55px;color:#fff;"><b>Apellido Materno</b></td>
                                <td style="width:55px;color:#fff;"><b>Creado</b></td>
                                
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">

function grabar_Contactos(id='formulario'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_Contactos')}}",  
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
                tabla_Contactos();
                alert('Guardado');
                $("#fc_popup").click();
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
            });
        }

function tabla_Contactos(){
    ajax_data = {
      "gestion" : $('input[name=box]:checked').val(),
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_Contactos')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                      $("#div_load").css('display','');
                      $("#div_instituciones").css('display','none'); 
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                          for (let i = 0; i < data.length; i++) {
                            data[i]['nro']= i+1;
                            if(<?=$ingresar_como_director?>){ data[i]['institucion'] = '<a target="_blank" href="https://siic01.ugel01.gob.pe/index.php/login/login_user_id_contacto/'+data[i]['id_contacto']+'/DocumentosRemitidos__director"><b>'+data[i]['institucion']+'</b></a>'; }
                            if(data[i]['id_contacto'] && <?=$restablecer_pass?>){
                            data[i]['editar']='<span title="Editar datos del director" class="btn btn-success pe-7s-pen" onclick="editar('+i+')"></span>';
                            data[i]['eliminar']='<span title="Dar de baja el usuario del director de IE" class="btn btn-danger" onclick="eliminar_Contactos('+data[i]['id_contacto']+')">X</span>';
                            data[i]['contraseña']='<span title="Restablecer contraseña a IIee<?=date('Y')?>" class="btn btn-warning pe-7s-unlock" onclick="contrasena_Contactos('+data[i]['id_contacto']+')"></span>';
                            }else{
                              data[i]['editar']="";
                              data[i]['eliminar']="";
                              data[i]['contraseña']="";
                            }
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                          $("#div_load").css('display','none');
                          $("#div_instituciones").css('display',''); 
                          $("#t_programas tr td").css({'padding-top':'2px','padding-bottom':'2px','padding-right':'2px','padding-left':'2px',});
                          $("#t_programas_filter").css({'position':'absolute','margin-top':'-60px','margin-left':'0px'});
                          $("#t_programas_filter input").css('width','200px');
                      }
              });
              
}

function tabla_Contactos_elimados(){
    ajax_data = {
      "gestion" : $('input[name=box]:checked').val(),
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_Contactos_elimados')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                      $("#div_load").css('display','');
                      $("#div_instituciones").css('display','none'); 
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                          for (let i = 0; i < data.length; i++) {
                            data[i]['nro']= i+1;
                              data[i]['editar']="";
                              data[i]['eliminar']="";
                              data[i]['contraseña']="";
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                          $("#div_load").css('display','none');
                          $("#div_instituciones").css('display',''); 
                          $("#t_programas tr td").css({'padding-top':'2px','padding-bottom':'2px','padding-right':'2px','padding-left':'2px',});
                          $("#t_programas_filter").css({'position':'absolute','margin-top':'-60px','margin-left':'0px'});
                          $("#t_programas_filter input").css('width','200px');
                      }
              });
              
}

function editar(nro){
  var data = g_data[nro];
  mostrarformulario();
  $("#id_contacto").val(data['id_contacto']);
  $("#E.codlocal").val(data['E.codlocal']);
  $("#E.codmod").val(data['E.codmod']);
  $("#E.red").val(data['E.red']);
  $("#E.institucion").val(data['E.institucion']);
  $("#E.idmodalidad").val(data['E.idmodalidad']);
  $("#E.nivel").val(data['E.nivel']);
  $("#E.distrito").val(data['E.distrito']);
  $("#E.gestion").val(data['E.gestion']);
  $("#E.gestion_dependencia").val(data['E.gestion_dependencia']);
  $("#usuario").val(data['usuario']);
  $("#acceso_director").val(data['acceso_director']);
  $("#celular_pers").val(data['celular_pers']);
  $("#correo_pers").val(data['correo_pers']);
  $("#nombres").val(data['nombres']);
  $("#apellipat").val(data['apellipat']);
  $("#apellimat").val(data['apellimat']);
  listar_oficinas(data['id_area'],data['id_oficina']);

}

function eliminar_Contactos(id_contacto){
    ajax_data = {
      "id_contacto"   : id_contacto,
      "especialista_elimino" : <?=$session['idespecialista']?>,
      "alt"    : Math.random()
    }
    if(confirm('¿Desea dar de baja al usuario del Director?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_Contactos')}}",
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
                      alert('De baja');
                      tabla_Contactos();
                      }
              });
      }else{

      }

}

function contrasena_Contactos(id_contacto){
    ajax_data = {
      "id_contacto"         : id_contacto,
      "contacto_establece"  : <?=$session['idespecialista']?>,
      "alt"    : Math.random()
    }
    if(confirm('¿Desea Restablecer?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('contrasena_Contactos')}}",
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
                      alert('Contraseña Restablecida');
                      tabla_Contactos();
                      }
              });
      }else{

      }

}


tabla_Contactos();

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
                            { "data": "codlocal" },
                            { "data": "codmod" },
                            { "data": "red" },
                            { "data": "institucion" },
                            { "data": "modalidad" },
                            { "data": "nivel" },
                            { "data": "distrito" },
                            { "data": "gestion" },
                            { "data": "gestion_dependencia" },
                            { "data": "usuario" },
                            { "data": "director" },
                            { "data": "celular_pers" },
                            { "data": "correo_inst" },
                            { "data": "correo_pers" }, 
                            { "data": "contraseña" },
                            { "data": "eliminar" },    
                            { "data": "editar" },
                            { "data": "nombres" },
                            { "data": "apellipat" },
                            { "data": "apellimat" },
                            { "data": "t_modificado" },
                            //{ "data": "eliminar" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

var g_list_areas = <?=json_encode($areas)?>;

function listar_areas(idarea=0){
  var opt = '';
    opt += '<option value="-1">'+''+'</option>';
  for (var i = 0; i < g_list_areas.length; i++) {
    var fila = g_list_areas[i];
    opt += '<option value="'+fila['SedeOficinaId']+'" '+( (fila['SedeOficinaId']==idarea)?' selected':'' )+'>'+fila['Descripcion']+'</option>';
  };
  $("select[name=id_area]").html(opt);
  //listar_oficinas(idarea);
}

function listar_oficinas(id_area,id_equipo=0){
	  var n_area    = -1;
	  for (var i = 0; i < g_list_areas.length; i++) {
	    var area = g_list_areas[i];
	      n_area = (area['SedeOficinaId']==id_area)?i:n_area;
	  }
	if(n_area>-1){  
	  var oficina = g_list_areas[n_area]['oficina'];
	  var opt = '';
	    opt += '<option value="'+(-1)+'">'+'Sin Equipo'+'</option>';
	    if(oficina){
		  for (var i = 0; i < oficina.length; i++) {
		    var fila = oficina[i];
		    opt += '<option value="'+fila['SedeOficinaId']+'" '+( (fila['SedeOficinaId']==id_equipo)?' selected':'' )+'>'+fila['Descripcion']+'</option>';
		  };
		}
    }

  $("select[name=id_oficina]").html(opt);
}

listar_areas();

</script>

@endsection
                      
