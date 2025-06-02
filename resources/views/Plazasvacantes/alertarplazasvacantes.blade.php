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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>MODULO DE MONITOREO Y REPORTE RAPIDO DEL SERVICIO EDUCATIVO NO CUBIERTO</b>
    </h5>
        <div class="position-relative form-group">
            
            <div class="row">
                <div class="col-sm-2">Nivel:</div>
                <div class="col-sm-3">
                <!-- <input type="text" class="form-control" name="tipo" id="tipo"></div>-->
                <select name="dcodmod" id="dcodmod" class="form-control" onchange="tabla_alertarplazasvacantes();">
				<?php
				for ($i=0; $i < count($session['conf_permisos']); $i++) {
				$key = $session['conf_permisos'][$i];
				?><option value="<?=$key['esc_codmod']?>"><?=$key['nivel_pap']?></option><?php
				}
				?>
    			</select>
                </div>
                <div class="col-sm-12" style="padding:12px;"><b>(*) Aquí se muestra el reporte Nexus de su Institución Educativa, si una plaza no está coberturada haga clic en el boton: </b> <span class="btn btn-danger">Alertar</span> <b>al costado del codigo de plaza.</b> </div>
                <div class="col-sm-12" > 
                <span style="background-color:#FFEB9C">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <b onclick="filtrar(this);" ondblclick="vaciar();" style="cursor:pointer;">EN PROCESO DE ATENCIÓN</b>&nbsp;&nbsp;&nbsp;
                <span style="background-color:#C6EFCE">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <b onclick="filtrar(this);" ondblclick="vaciar();" style="cursor:pointer;">ATENDIDO</b>&nbsp;&nbsp;&nbsp;
                <span style="background-color:#FFC7CE">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <b onclick="filtrar(this);" ondblclick="vaciar();" style="cursor:pointer;">NO PROCEDE</b>
                </div>
              </div>
        
            
            <div class="divtabla" id="div_citas" style="display:none;">
                
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                              <!--
                              <tr style="background-color:rgb(0,117,184);text-align:center;font-weight: bold;">
                                  <td colspan="2" rowspan="2" style="min-width:50px;color:#fff;">REGISTRO DE ALERTA</td>
                                  <td colspan="9" style="min-width:50px;color:#fff;">NEXUS DE LA INSTITUCIÓN EDUCATIVA</td>
                              </tr>
                              <tr style="background-color:rgb(0,117,184);">
                                <td style="min-width:50px;color:#fff;"><b>CODIGO DE PLAZA</b></td>
                                <td style="min-width:55px;color:#fff;"><b>DESCRIPCIÓN DEL CARGO</b></td>
                                <td style="min-width:45px;color:#fff;"><b>SITUACION LABORAL</b></td>
                                <td style="min-width:140px;color:#fff;"><b>MOTIVO DE LA VACANCIA</b></td>
                                <td style="min-width:55px;color:#fff;"><b>NOMBRES Y APELLIDOS</b></td>
                                <td style="min-width:10px;color:#fff;"><b>C.R.</b></td>
                                <td style="min-width:10px;color:#fff;"><b>J.L.</b></td>
                                <td style="min-width:100px;color:#fff;"><b>ESTADO</b></td>
                                <td style="min-width:35px;color:#fff;"><b>TIPO DE REGISTRO</b></td>
                            </tr>
                            -->
                             <!--
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="min-width:10px;color:#fff;"><b>N</b></td>
                                <td style="min-width:100px;color:#fff;"><b>ALERTAR VACANTE</b></td>
                                <td style="min-width:50px;color:#fff;"><b>CODIGO DE PLAZA</b></td>
                                <td style="min-width:55px;color:#fff;"><b>DESCRIPCIÓN DEL CARGO</b></td>
                                <td style="min-width:45px;color:#fff;"><b>SITUACION LABORAL</b></td>
                                <td style="min-width:140px;color:#fff;"><b>MOTIVO DE LA VACANCIA</b></td>
                                <td style="min-width:55px;color:#fff;"><b>NOMBRES Y APELLIDOS</b></td>
                                <td style="min-width:10px;color:#fff;"><b>C.R.</b></td>
                                <td style="min-width:10px;color:#fff;"><b>J.L.</b></td>
                                <td style="min-width:100px;color:#fff;"><b>ESTADO</b></td>
                                <td style="min-width:35px;color:#fff;"><b>TIPO DE REGISTRO</b></td>
                            </tr>
                            -->
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">

function filtrar(athis){
    $("#t_programas_filter input").val(athis.innerHTML);
    $("#t_programas_filter input").keyup();
}

function vaciar(){
    $("#t_programas_filter input").val('');
    $("#t_programas_filter input").keyup();
}

function tabla_header(){
    var html = '';
    html +='<tr style="background-color:rgb(0,117,184);text-align:center;font-weight: bold;">';
    html +='<td colspan="2" rowspan="2" style="min-width:70px;color:#fff;">REGISTRO DE ALERTA</td>';
    html +='<td colspan="8" style="min-width:50px;color:#fff;">NEXUS DE LA INSTITUCIÓN EDUCATIVA</td>';
    html +='</tr>';
    html +='<tr style="background-color:rgb(0,117,184);">';
    html +='<td style="min-width:50px;color:#fff;"><b>CODIGO DE PLAZA</b></td>';
    html +='<td style="min-width:55px;color:#fff;"><b>DESCRIPCIÓN DEL CARGO</b></td>';
    html +='<td style="min-width:45px;color:#fff;"><b>SITUACION LABORAL</b></td>';
    html +='<td style="min-width:140px;color:#fff;"><b>MOTIVO DE LA VACANCIA</b></td>';
    html +='<td style="min-width:55px;color:#fff;"><b>NOMBRES Y APELLIDOS</b></td>';
    html +='<td style="min-width:10px;color:#fff;"><b>J.L.</b></td>';
    html +='<td style="min-width:100px;color:#fff;"><b>ESTADO</b></td>';
    html +='<td style="min-width:35px;color:#fff;"><b>TIPO DE REGISTRO</b></td>';
    html +='</tr>';
    $("#t_programas thead").html(html);
}

function guardar_alertarplazasvacantes(id='formulario'){
    //if(!Number.isInteger(parseInt($("#exp").val()))){ $("#exp").val(''); alert('El numero de expediente debe ser un Numero'); }
    if(!$("#fecha_exp").val()){ alert('Registre la fecha del expediente'); return false; }
    if($("#obs").val().length>250){ alert('Escribir máximo 250 caracteres, usted ha escrito '+$("#obs").val().length+' caracteres'); return false; }
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_alertarplazasvacantes')}}",  
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
                tabla_alertarplazasvacantes();
                $("#fc_popuplg").click();
                if(data){alert('Guardado'); }else{ alert('No Guardado'); }
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
            });
        }

function tabla_alertarplazasvacantes(){
    ajax_data = {
      "codmod"   : $("#dcodmod").val(),
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_alertarplazasvacantes')}}",
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
                            data[i]['nro'] = i+1;
                            if(data[i]['idalertplaza']){
                                data[i]['btn_alertarvacante'] = html_situacion_alerta(data[i]['situacion_alerta'],data[i]['motivoalerta'])+((data[i]['situacion_alerta']=='SOLICITADO')?' (<a href="#" onclick="alertarvacante('+i+')">Editar</a>)':' (<a href="#" onclick="alertarvacante('+i+',0)">Ver</a>)');
                            }else{
                                data[i]['btn_alertarvacante'] ='<span class="btn btn-danger" onclick="alertarvacante('+i+')">Alertar</span>';
                            }
                            
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                          tabla_header();
                          $("#t_programas tbody tr td").css('padding','1px 1px 1px 5px');
                          $("#t_programas tbody tr td .btn-danger").parent().css({'text-align':'center','padding':'1px'});
                          $("#div_citas").css('display','');
                      }
              });
}

function html_situacion_alerta(texto,motivo){
    var res = '';
    switch(texto) {
    case 'SOLICITADO' : res='<b>'+motivo+'</b>'; break;//<b style="color:red;">SOLICITADO</b> res='<b>'+motivo+'</b>'
    case 'EN PROCESO DE ATENCIÓN' : res='<b style="color:GoldenRod;">EN PROCESO DE ATENCIÓN</b>'+'<br> <b>Motivo: '+motivo+'</b>'; break;
    case 'ATENDIDO'   : res='<b style="color:green;">ATENDIDO</b>'+'<br> <b>Motivo:'+motivo+'</b>'; break;
    case 'NO PROCEDE' : res='<b style="color:red;">NO PROCEDE</b>'+'<br> <b>Motivo:'+motivo+'</b>'; break;
    
    default : res=''; break;
    }
    return res;
}

var g_alertamotivo = <?=json_encode($alertamotivo)?>;
function select_motivoalerta(idmotivo,disabled=''){
    var res = '';
        res += '<select class="form-control" id="idmotivo" name="idmotivo" required '+disabled+'>';
            res += '<option value="">ESCOGE EL MOTIVO</option>';
        for (let i = 0; i < g_alertamotivo.length; i++) {
            var key = g_alertamotivo[i];
            res += '<option value="'+key['idmotivo']+'" '+((key['idmotivo']==idmotivo)?'selected':'')+'>'+key['descripcion']+'</option>';
        }
        res += '</select>';
    return res;
}

function alertarvacante(nro,ieditar=1){
  var data = g_data[nro];
  var disabled = (ieditar)?'':'disabled';
  var opt = '';
        opt += '<form id="formulario" style="max-width:800px;" onsubmit="guardar_alertarplazasvacantes();return false;" enctype="multipart/form-data">';
        opt += '<div class="modal-header"><h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>REGISTRAR ALERTA</b></h4></div>';
        opt += '<div class="modal-body">';
        opt += '<div class="row" style="display:none;">';
        opt += '<div class="col-sm-2">idalertplaza:</div>';
        opt += '<div class="col-sm-10"><input type="text" class="form-control" name="idalertplaza" id="idalertplaza" value="'+data['idalertplaza']+'" readonly></div>';
        opt += '</div>';
        opt += '<div class="row">';
        opt += '<div class="col-sm-2">Codmod:</div>';
        opt += '<div class="col-sm-10"><input type="text" class="form-control" name="codmod" id="codmod" value="'+data['codmodce']+'" readonly></div>';
        opt += '<div class="col-sm-2">codplaza:</div>';
        opt += '<div class="col-sm-10"><input type="text" class="form-control" name="codplaza" id="codplaza" value="'+data['codplaza']+'" readonly></div>';
        
        opt += '<div class="col-sm-2">Expediente (*):</div>';
        opt += '<div class="col-sm-10"><input placeholder="N° de Expediente con el que informó a la Ugel01 la plaza vacante (solo numero)" type="text" class="form-control" name="exp" id="exp" value="'+data['exp']+'" onkeypress="return event.charCode >= 48 && event.charCode <= 57" onpaste="return false" required '+disabled+'></div>';
        opt += '<div class="col-sm-2"></div>';
        opt += '<div class="col-sm-10" style="color:red;font-weight: bold;">(*) N° de Expediente con el que informó a la Ugel01 la plaza vacante (solo numero).</div>';
        opt += '<div class="col-sm-2">fecha del expediente:</div>';
        opt += '<div class="col-sm-10"><input type="text" class="form-control" name="fecha_exp" id="fecha_exp" value="'+data['fecha_exp']+'" required '+disabled+'></div>';
        opt += '<div class="col-sm-2">Motivo:</div>';
        opt += '<div class="col-sm-10">'+select_motivoalerta(data['idmotivo'],disabled)+'</div>';
        //opt += '<div class="col-sm-10"><input type="text" class="form-control" name="tipo" id="tipo"></div>';
        opt += '</div>';
        opt += '<div class="row">';
        opt += '<div class="col-sm-2">Observación (**):</div>';
        opt += '<div class="col-sm-10"><textarea class="form-control" name="obs" id="obs" style="height: 100px;" required  '+disabled+'>'+data['obs']+'</textarea></div>';
        opt += '<div class="col-sm-2"></div>';
        opt += '<div class="col-sm-10" style="color:red;font-weight: bold;">(**) Escribir máximo 250 caracteres</div>';
         
        if(data['idalertplaza']){
        opt += '<div class="col-sm-12"><br></div>';
        opt += '<div class="col-sm-2">Situacion:</div>';
        opt += '<div class="col-sm-10"><input type="text" class="form-control" value="'+data['situacion_alerta']+'" readonly></div>';
        if(data['situacion_obs']){
        opt += '<div class="col-sm-2">Respuesta de la Ugel01:</div>';
        opt += '<div class="col-sm-10"><input type="text" class="form-control" value="'+data['situacion_obs']+'" readonly></div>';
        }
        }
        
        if(ieditar){
        opt += '<div class="col-sm-2"></div>';
        opt += '<div class="col-sm-10"><br>';
        opt += '<button class="btn btn-success">GRABAR</button>&nbsp;&nbsp;&nbsp;';
        opt += '<button type="button" class="btn btn-danger antoclose" data-dismiss="modal">Cerrar</button>';
        if(data['idalertplaza']){ opt += '<span style="float:right;" class="btn btn-warning" onclick="eliminar_alertarplazasvacantes('+data['idalertplaza']+');">ELIMINAR ALERTA</span>';}
        opt += '</div>';
        opt += '</div>';
        opt += '@csrf';
        }else{
        opt += '<div class="col-sm-12"><br></div>';
        }
        opt += '</div>';
        opt += '</form>';
        
        //$("#idalertplaza").val(data['idalertplaza']);
        //$("#codmod")      .val(data['codmodce']);
        //$("#codplaza")    .val(data['codplaza']);
        //$("#exp")         .val(data['exp']);
        //$("#fecha_exp")   .val(data['fecha_exp']);
        $("#motivoalerta")        .val(data['motivoalerta']);
        //$("#obs")         .val(data['obs']);
  
  $("#popuplg .modal-content").html(opt);
  
  $("#fecha_exp").flatpickr({
      locale: {
        weekdays: {
          shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
          longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],         
        }, 
        months: {
          shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
          longhand: ['Enero', 'Febreo', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        },
      }
      });
  
  $("#fc_popuplg").click();
  
  
  /*$("#id_tipo").val(data['id_tipo']);
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
  */
  
  
}

function eliminar_alertarplazasvacantes(idalertplaza){
    ajax_data = {
      "idalertplaza" :idalertplaza,
      "alt"    : Math.random()
    }
    if(confirm('¿Desea eliminar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_alertarplazasvacantes')}}",
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
                      tabla_alertarplazasvacantes();
                      $("#fc_popuplg").click();
                      }
              });
      }else{

      }

}


tabla_alertarplazasvacantes();
var g_exced=[];
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
                            { "data": "btn_alertarvacante" },
                            { "data": "codplaza" },
                            { "data": "descargo" },
                            { "data": "situacion" },
                            { "data": "obser" },
                            { "data": "docente" },
                            { "data": "jornlab" },
                            { "data": "descmovim" },
                            { "data": "tiporegistro" },
                        ],                          
                        rowCallback: function (row, data) {
                            g_exced.push(data);
                            //EN PROCESO DE ATENCIÓN FFC7CE NO PROCEDE
                            //ATENDIDO
                            if(data['situacion_alerta'] == "EN PROCESO DE ATENCIÓN"){
                                $($(row).find("td")).css("background-color","#FFEB9C");
                             }
                             if(data['situacion_alerta'] == "ATENDIDO"){
                                $($(row).find("td")).css("background-color","#C6EFCE");
                             }
                             if(data['situacion_alerta'] == "NO PROCEDE"){
                                $($(row).find("td")).css("background-color","#FFC7CE");
                             }
                        },
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

</script>

@endsection
                      
