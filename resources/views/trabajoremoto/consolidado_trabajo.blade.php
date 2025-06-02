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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>BANDEJA DE CONSOLIDADO DE TRABAJO</b></h5>
        <div class="position-relative form-group">
            <div>
                <a target="blank" class="btn btn-success" style="margin-bottom:10px;" href="http://siic01.ugel01.gob.pe/public/archivos/ESPECIALISTA-CONSOLIDADO-TRABAJO.pdf">Descargar Manual de uso</a><br>
                <b style="color:#000;">AÑO: </b> 
					  <select class="form-control" style="color:#000;" id="select_anio" onchange="ver_mantenimiento();">
					    <option>2025</option>
					    <option>2023</option>
					    <option>2022</option>
					    <option>2021</option>
                        <option>2020</option> 
                      </select>
					  
					  <b style="color:#000;">MES: </b> 
					  <select class="form-control" style="color:#000;" id="select_mes" onchange="ver_mantenimiento();">
					    <option value="01">ENERO</option>
					    <option value="02">FEBRERO</option>
					    <option value="03">MARZO</option>
                        <option value="04">ABRIL</option> 
                        <option value="05">MAYO</option>
                        <option value="06">JUNIO</option>
                        <option value="07">JULIO</option>
                        <option value="08">AGOSTO</option>
                        <option value="09">SEPTIEMBRE</option>
                        <option value="10">OCTUBRE</option>
                        <option value="11">NOVIEMBRE</option>
                        <option value="12">DICIEMBRE</option>
					  </select>

                      <b style="color:#000;">EQUIPO: </b> 
					  <select class="form-control" style="color:#000;" id="select_equipo" onchange="ver_mantenimiento();">
                      <?php
                        if($equipo){
                        foreach ($equipo as $key) {
                        ?><option value="<?=$key->SedeOficinaId?>" <?=($key->SedeOficinaId==$session['id_oficina'])?'selected':''?>><?=$key->Descripcion?></option><?php
                        }
                        }else{
                        ?><option value="<?=$session['id_oficina']?>"><?=$session['equipo']?></option><?php
                        }
                      ?>
                      </select>
					  <br>
					  <div class="table-responsive">
			    <div id="div_regasistencia">
                    <form id="form_regasistencia" enctype="multipart/form-data" style="width:100%;" onsubmit="guardar_consolidadotrabajo_dias(this.id);return false;">
                        <div style="margin-bottom:10px;">
                        <span id="" onclick="anadirespecialista()" class="btn btn-danger">Añadir especialista</span>
                        <button id="btn_grabar" class="btn btn-success">Grabar</button>
                        <!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" id="btn_descargar" style="display:none;" href="#">Descargar PDF</a>-->
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="firmar_descargar();" id="firmar_descargar" style="display:none;" href="#">Visualizar PDF</a>
                        
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="" onclick="reporte_consolidado_trabajo()" class="btn btn-info" style="<?=($session['id_oficina']==70 or $session['id_oficina']==78)?'':'display:none;'?>">Descargar consolidado</span>
                        
                        <div class="col-xs-12" id="div_oficinaspresentaron" style="font-size:11px;"></div>

                        <input name="idtrabajo" type="text" style="display:none;" value="">
                        <input name="idespecialista" type="text" style="display:none;" value="">
                        <input name="fecha"          type="text" style="display:none;" value="">
                        <input name="reg"            type="text" style="display:none;" value="">
                        </div>
                    @csrf
                    </form>
                        
                        <div id="subirpdf"></div>
                        <div class="col-xs-12" id="div_fechagrabado" style="margin-bottom:5px;"></div>
                        
                        <div class="col-xs-12">
                        <table style="font-size:11px;margin-bottom:5px;">
                            <tr style="font-weight: bold;background-color:Purple;color:#fff;"><td colspan="2">LEYENDA</td><td></td></tr>
                            <tr style="font-weight: bold;"><td>R</td><td>=</td><td>DIA TRABAJADO REMOTO</td>                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LG/SC</td><td>=</td><td>LICENCIA CON GOCE SUJETO A COMPENSACION</td></tr>
                            <tr style="font-weight: bold;"><td>P</td><td>=</td><td>DIA TRABAJADO PRESENCIAL</td>                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>O</td><td>=</td><td>ONOMASTICO</td></tr>
                            <tr style="font-weight: bold;"><td>FE</td><td>=</td><td>FERIADO</td>                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>V</td><td>=</td><td>VACACIONES</td></tr>
                            <tr style="font-weight: bold;"><td>DL/PHS</td><td>=</td><td>DIA LABORADO POR HORAS DE SOBRETIEMPO</td>  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LPS</td><td>=</td><td>LICENCIA POR SALUD</td></tr>
                            <tr style="font-weight: bold;"><td>LSG</td><td>=</td><td>LICENCIA SIN GOCE</td>                         <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LPM</td><td>=</td><td>LICENCIA POR MATERNIDAD</td></tr>
                            <tr style="font-weight: bold;"><td>LPP</td><td>=</td><td>LICENCIA POR PATERNIDAD</td>                   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>F</td><td>=</td><td>FALTA</td></tr>
                        </table>
                        </div>

                        <div id="t_boxreg" style="margin-bottom:3px;color:red;"><b>Selecione los especialistas para cambiar los registro en bloque</b></div>
                        <table class="display table table-bordered table-striped table-dark" id="t_regasistencia" style="color:#000;font-size:10px;text-align:center;width:100%;"></table>
                    <div id="reporte"></div>
                </div>
                    
                    
                        
                        <table table class="display table table-bordered table-striped table-dark" id="t_mantenimiento" style="color:#000;font-size:10px;text-align:center;width:100%;background-color:Purple;display:none;">
                          <thead>
                            <tr style="font-size:12px;" class="">
                                <td style="min-width:45px;color:#fff;" ><b>Nro</b></td>
                                <td style="min-width:100px;color:#fff;" ><b>Area</b></td>
                                <td style="min-width:100px;color:#fff;" ><b>Equipo</b></td>
                                <td style="min-width:100px;color:#fff;" ><b>CAP decreto legislativo 276</b></td>
                                <td style="min-width:100px;color:#fff;"><b>Persona bajo la ley 29944</b></td>
                                <td style="min-width:100px;color:#fff;"><b>Practicante decreto legislativo 1401</b></td>
                                <td style="min-width:100px;color:#fff;"><b>CAS decreto legislativo 1057</b></td>
                            </tr>
                          </thead>
                          <tbody style="font-size:10px;"></tbody>
                        </table>
                        
					  </div>

            <!--
                <div class="custom-checkbox custom-control custom-control-inline"><input type="checkbox" id="exampleCustomInline" class="custom-control-input"><label class="custom-control-label" for="exampleCustomInline">An inline custom
                    input</label></div>
                <div class="custom-checkbox custom-control custom-control-inline"><input type="checkbox" id="exampleCustomInline2" class="custom-control-input"><label class="custom-control-label" for="exampleCustomInline2">and another one</label>
                </div>
            -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

function anadirespecialista(idespecialista=0){
    $("#popup01 .modal-content").load('{{route('popup_especialista')}}?idespecialista='+idespecialista+'&especialista_creo='+<?=$session['idespecialista']?>+'&id_area='+<?=$session['id_area']?>+'&id_oficina='+$("#select_equipo").val()+'&funcion=ver_mantenimiento();');
    $("#fc_popup").click();
}

function eliminarespecialista(idespecialista){
    ajax_data = {
      "idespecialista"       : idespecialista,
      "especialista_elimino" : <?=$session['idespecialista']?>,
      "alt" : Math.random()
    }
    if(confirm('¿Está seguro que desea eliminar al especialista?')){
        $.ajax({
                type: "GET",
                url: '{{route('eliminarespecialista')}}',
                data: ajax_data,
                dataType: "html",
                beforeSend: function(){
                      //imagen de carga
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    alert('Especialista eliminado');
                    ver_mantenimiento();
                }
        });
    }
}

function reporte_consolidado_trabajo(){
    ajax_data = {
        "anio"  : $("#select_anio").val(),
        "idmes" : $("#select_mes").val(),
        "alt"   : Math.random()
    }
        $.ajax({
                type: "GET",
                url: '{{route('reporte_consolidado_trabajo')}}',
                data: ajax_data,
                dataType: "html",
                beforeSend: function(){
                      $("#popuplg .modal-content").html('<div style="text-align:center;"><img style="width:300px;" src="assets/images/load10.gif"></div>');
                      $("#fc_popuplg").click();
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    $("#popuplg .modal-content").html(data);
                }
        });
}

function value_class_a_texto(clase=''){
    var element = $("."+clase);
    datos = [];
    for (let i = 0; i < element.length; i++) {
        datos.push(element[i].value);
    }
    return datos.join();
}

function guardar_consolidadotrabajo_dias(id){
        $('#btn_grabar').prop('disabled',true);
          $("input[name=idespecialista]").val(value_class_a_texto('idespecialista'));
          $("input[name=fecha]")         .val(value_class_a_texto('fecha'));
          $("input[name=reg]")           .val(value_class_a_texto('reg'));
          //información del formulario
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('guardar_consolidadotrabajo_dias')}}',  
              type: 'POST',
              //datos del formulario
              data: formData,
              dataType: "json",
              //necesario para subir archivos via ajax
              cache: false,
              contentType: false,
              processData: false,
              //mientras enviamos el archivo
              beforeSend: function(){
                  
              },
              //una vez finalizado correctamente
              success: function(data){
                $('#btn_grabar').prop('disabled',false);
                ver_mantenimiento();
                alert('Datos guardados');
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
                $('#btn_grabar').prop('disabled',false);
                ver_mantenimiento();
              }
          });

}

function firmar_descargar(){
    $("#popuplg .modal-content").load('{{route('firmar_descargar')}}?idtrabajo='+$("input[name=idtrabajo]").val());
    $("#fc_popuplg").click();
}


var g_athis = [];
function validar_archivo(athis,extencion){
g_athis = athis;
var file = $(athis)[0].files[0];
//obtenemos el nombre del archivo
var fileName = file.name;
//obtenemos la extensión del archivo
fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
//obtenemos el tamaño del archivo
var fileSize = file.size;
//obtenemos el tipo de archivo image/png ejemplo
var fileType = file.type;

var l_Extension_valida = ((extencion)?extencion:'').split(',');
var id = $(athis).parent()[0].id;

if( l_Extension_valida.indexOf(fileExtension.toUpperCase())>-1 ){
    
    $("#"+id).css('display','none');
    $("#"+id).parent().children('.alert_archivo').html("<img height='60px' src='<?=asset('assets/images/3.gif')?>'>"+"<br>"+"<span class='bg-success'  style='font-size:12px;padding:3px;'>Subiendo archivo: "+fileName+"</span>");
    f_enviar(id);
}else{
    $("#"+id).parent().children('.alert_archivo').html("<span class='bg-danger' style='font-size:12px;padding:1px;'>"+"Archivo no valido, debe adjuntar solo: "+l_Extension_valida.toString()+"</span>");
    alert('Archivo '+fileExtension+' no valido, debe adjuntar solo: '+l_Extension_valida.toString());
    
    $(athis).val('');
}
}

function f_enviar(id){
var ifimagen = true;
//$('#btn_enviar').prop('disabled',true);
    if(true){
        //información del formulario
        var formData = new FormData($("#"+id)[0]);
        var message = "";
        //hacemos la petición ajax  
        //dataType: "json",
        $.ajax({
            url: '{{route('guardar_consolidado_trabajo')}}',  
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,
            dataType: "json",
            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function(){
                    
            },
            //una vez finalizado correctamente
            success: function(data){
                
                alert(data['msj']);
                
                if(data['tipo']==1){
                        //tipo_icono
                        $("#"+id).parent().children(".alert_archivo").html('<a target="_blank" style="font-size:30px;" class="'+tipo_icono(data['url'])+'" href=".'+data['url']+'"></a><br><span class="bg-success" style="font-size:12px;padding:3px;">'+data['msj']+'</span>');
                        //ver_mantenimiento();
                        setTimeout('ver_mantenimiento()',2000);
                }else{
                    $("#"+id).parent().children(".alert_archivo").html("<span class='bg-danger'  style='font-size:12px;padding:3px;'>"+data['msj']+"</span>");
                    $("#"+id).parent().children('.archivo_subido').css('display','');
                    $("#"+id).parent().children('.btn-danger').css('display','none');
                }
            },
            //si ha ocurrido un error
            error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
                $("#"+id).parent().html("<span class='error'  style='font-size:12px;padding:3px;'>Se ha producido un error, recargue la página e inténtelo de nuevo.</span>");
                //message = $("<span class='error'>Ha ocurrido un error.</span>");
                //showMessage(message);
            }
        });
    }else{
        alert('Llene todos los campos');
    }
}

var j_id;
function remplazar_doc(athis,id){
  j_id = id;
  $("#"+id).parent().children('.alert_archivo').html('');
if( $("#"+id).css('display')=='none' ){
        $("#"+id).css('display','');
        $(athis).html('Cancelar');
        $("#"+id).parent().children('.archivo_subido').css('display','none');
}else{
        $("#"+id).css('display','none');
        $(athis).html('Remplazar');
        $("#"+id).parent().children('.archivo_subido').css('display','');
}

}
</script>

<script type="text/javascript">
var table4;
//{ "data": "abreviatura" },
//{ "data": "t_gerentepublico" },
//$(window).load(function(){
    table4 = $("#t_mantenimiento").DataTable( {
                    dom: 'Bfrtip',
                    buttons: ['excel'],
                    "iDisplayLength": 55,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                    },
                    data:[],
                    "columns": [
                        { "data": "nro" },
                        { "data": "t_area" },
                        { "data": "t_equipo" },
                        { "data": "t_cap" },
                        { "data": "t_ley29944" },
                        
                        { "data": "t_practicante" },
                        { "data": "t_cas" },
                    ],                          
                    rowCallback: function (row, data) {},
                    filter: true,
                    info: true,
                    ordering: true,
                    processing: true,
                    retrieve: true                          
                });
ver_mantenimiento();
//});

function tipo_icono(archivo){
var extencion = archivo.split('.')[archivo.split('.').length-1];
//var icono = 'pe-7s-file';
var icono = 'pe-7s-cloud-download';
/*switch(extencion.toLowerCase()) {
      case 'xls' : icono = 'fa-file-excel-o'; break;
      case 'xlsx': icono = 'fa-file-excel-o'; break;
      case 'csv' : icono = 'fa-file-excel-o'; break;
      case 'doc' : icono = 'fa-file-word-o' ; break;
      case 'docx': icono = 'fa-file-word-o' ; break;
      case 'rar' : icono = 'fa-file-zip-o'  ; break;
      case 'zip' : icono = 'fa-file-zip-o'  ; break;
      case 'html': icono = 'fa-file-code-o' ; break;
      case 'png' : icono = 'fa-file-image-o'; break;
      case 'jpg' : icono = 'fa-file-image-o'; break;
      case 'gif' : icono = 'fa-file-image-o'; break;
      default    : icono = 'fa-file-pdf-o'  ; break;
}*/
return icono;

}


function form_archivo(fila,elemento,i){
retorno = '';                                            //+fila[elemento].split('/')[fila[elemento].split('/').length-1]
if(<?=$session['id_oficina']?>==fila['idequipo']){
var  boton01 = (fila['visitas_'+elemento]>0 && fila['cas_permiso_eti'] == 0)?'<span style="font-size:15px;color:green;font-weight: bold;">'+fila['visitas_'+elemento]+' visitas</span>':'<span class="btn btn-danger" style="padding:4px;" onclick="remplazar_doc(this,'+"'"+'formfut'+elemento+i+"'"+');">Remplazar</span>';
retorno += (fila[elemento] && !isNaN(i) )?'<a target="_blank" style="font-size:30px;" class=" archivo_subido '+tipo_icono(fila[elemento])+'" href=".'+fila[elemento]+'"></a>':'';
retorno += (fila[elemento] && isNaN(i)  )?'&nbsp;&nbsp;&nbsp;<a target="_blank" style="" class=" archivo_subido" href=".'+fila[elemento]+'">Descargar PDF firmado</a>&nbsp;&nbsp;&nbsp;':'';
retorno += (isNaN(i))?'':'<br>';
retorno += (fila[elemento])?boton01:'';
retorno += '<form style="'+( (fila[elemento])?'display:none;':'' )+'" id="formfut'+elemento+i+'" enctype="multipart/form-data">';
retorno += (isNaN(i))?'':'<label for="Mfilefut'+elemento+i+'" class="pe-7s-cloud-upload" style="cursor: pointer;color:#000;width:100%;font-size:30px;border: 3px dotted #000;text-align:center;"></label>';
retorno += '<input type="file" name="archivopdf" onchange="validar_archivo(this,'+"'PDF,XLS,XLSX'"+');" class="form-control" id="Mfilefut'+elemento+i+'">';
retorno += '<input type="text" style="display:none;" name="campo" value="'+elemento+'">';
retorno += '<input type="text" style="display:none;" name="especialista" value="<?=$session['nombre']?>-<?=$session['esp_apellido_paterno']?>-<?=$session['esp_apellido_materno']?> ">';

retorno += '<input type="text" style="display:none;" name="txt_anio" class="txt_anio" value="'+$("#select_anio").val()+'">';
retorno += '<input type="text" style="display:none;" name="txt_mes" class="txt_mes" value="'+$("#select_mes option:selected").html()+'">';
retorno += '<input type="text" style="display:none;" name="idmantenimiento" value="'+fila['idtrabajo']+'">';
retorno += '@csrf';
retorno += '</form>';
retorno += '<div class="alert_archivo"></div>';
}else{
retorno += (fila[elemento] && !isNaN(i))?'<a target="_blank" style="font-size:30px;" class=" archivo_subido '+tipo_icono(fila[elemento])+'" href=".'+fila[elemento]+'"></a> <br><b style="font-size:9px;">PRESENTADO</b>':'';
retorno += (fila[elemento] && isNaN(i) )?'&nbsp;&nbsp;&nbsp;<a target="_blank" style="" class=" archivo_subido" href=".'+fila[elemento]+'">Descargar PDF firmado</a>&nbsp;&nbsp;&nbsp;':'';
}
return retorno;
}

var g_equipo     = <?=json_encode($equipo)?>;
var g_presentaron = [];
var mantenimiento = [];
function ver_mantenimiento(registrar=false){
    
    $("#div_regasistencia").css('display','none');
    $("#t_mantenimiento_wrapper").css('display','none');
    $("#btn_grabar").prop('disabled',false);
                      
    //var registrar = (registrar)?registrar:((parseInt($("#select_mes").val())>4 && parseInt($("#select_anio").val())>2020)?1:0);
    var registrar = (registrar)?registrar:((parseInt($("#select_anio").val()+$("#select_mes").val())>202104)?1:0);
   /* var registrar = 1;
    if(parseInt($("#select_anio").val())>2020){
        if(parseInt($("#select_mes").val())>4){
            registrar = 0;
        }
    }*/
    
    //registrar = 1;
    ajax_data = {        
   "anio"           : $("#select_anio").val(),
   "mes"            : $("#select_mes option:selected").html(),
   "idmes"          : $("#select_mes").val(),
   "registrar"      : registrar,
   "area"           : "<?=$session['area']?>",
   "areacorta"      : "<?=$session['areacorta']?>",
   "idarea"         : "<?=$session['id_area']?>",
   "equipo"         : $("#select_equipo option:selected").html(),
   "idequipo"       :  $("#select_equipo").val(),
   "idespecialista" : "<?=$session['idespecialista']?>",
   
   "alt"    : Math.random()
}
$.ajax({
                type: "GET",
                url: '{{route('ver_consolidado_trabajo')}}',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      //imagen de carga
                      
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                        //mantenimiento = [];
                g_presentaron = [];
                mantenimiento = data['mantenimiento'];
                var registro  = data['registro'];
                var fechas    = data['fechas'];
                if(mantenimiento){
                    if(registrar==1){
                        $("#div_fechagrabado").html((mantenimiento[0]['culminado']==1)?'<b>Guardado: '+mantenimiento[0]['t_modificado']+'</b>':'');
                        
                        var presentaron = '';
                        if(data['oficinaspresentaron'].length){
                            presentaron += '<table style="margin-top:10px;">';
                            presentaron += '<tr><td style="font-weight:bold;background-color:Purple;color:#fff;">OFICINAS QUE HAN PRESENTADO:</td> <td></td>  <td style="font-weight:bold;background-color:red;color:#fff;">OFICINAS QUE NO HAN PRESENTADO:</td> </tr>';
                            presentaron += '<tr>';
                            
                            presentaron += '<td style="font-weight:bold;vertical-align: text-top;">';
                            for (let k = 0; k < data['oficinaspresentaron'].length; k++) {
                                presentaron += (k+1)+') '+data['oficinaspresentaron'][k]['equipo']+' ('+data['oficinaspresentaron'][k]['t_modificado']+')'+'<br>';
                                g_presentaron.push(data['oficinaspresentaron'][k]['idequipo']);
                            }
                            presentaron += '</td>';
                            
                            presentaron += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                            
                            presentaron += '<td style="font-weight:bold;vertical-align: text-top;">';
                            var nro = 1;
                            for (let k = 0; k < g_equipo.length; k++) {
                                presentaron += ( g_presentaron.indexOf(g_equipo[k]['SedeOficinaId'])==-1 )?(nro++)+') '+g_equipo[k]['Descripcion']+'<br>':'';
                            }
                            presentaron += '</td>';
                            
                            presentaron += '</tr>';
                            presentaron += '</table>';
                        }
                        $("#div_oficinaspresentaron").html(presentaron);

                        $("input[name=idtrabajo]").val(data['idtrabajo']);
                        $("#firmar_descargar").css('display',((mantenimiento[0]['culminado']==1 && !mantenimiento[0]['docfirmado'])?'':'none'));
                        //$("#btn_descargar").css('display',((mantenimiento[0]['culminado']==1 && !mantenimiento[0]['docfirmado'])?'':'none'));
                        $("#firmar_descargar").css('display',((mantenimiento[0]['culminado']==1)?'':'none'));
                        //$("#btn_descargar").prop('href','pdfreporteasistencia?idtrabajo='+data['idtrabajo']);
                        //$("#subirpdf").html( (mantenimiento[0]['culminado']==1)?('<b>Documento firmado:</b>'+form_archivo(mantenimiento[0],'docfirmado','docfirmado')):'' );
                        var sin_regimen_laboral = [];
                        var opt  = '<thead>';
                            opt += '<tr style="background-color:Purple;">';
                            opt += '<td rowspan="2" style="color:#fff;">N</td>';
                            opt += '<td colspan="3" style="color:#fff;">Opciones</td>';
                            opt += '<td rowspan="2" style="color:#fff;">Especialista</td>';
                            opt += '<td rowspan="2" style="color:#fff;">Modalidad de contrato</td>';
                            for (let k = 0; k < fechas.length; k++) {
                                opt += '<td style="color:#fff;">'+fechas[k]['t_diasemana']+'</td>';
                            }
                            opt += '</td>';
                            opt += '<tr style="background-color:Purple;">';
                            opt += '<td style="color:#fff;"></td>';
                            opt += '<td style="color:#fff;">Editar</td>';
                            opt += '<td style="color:#fff;">Eliminar</td>';
                            for (let k = 0; k < fechas.length; k++) {
                                opt += '<td style="color:#fff;">'+fechas[k]['dia']+'</td>';
                            }
                            opt += '</td>';
                            opt += '</thead>';
                            for (let i = 0; i < registro.length; i++) {
                                if(!registro[i]['regimen_laboral']){ sin_regimen_laboral.push(registro[i]['esp_nombres']+' '+registro[i]['esp_apellido_paterno']+' '+registro[i]['esp_apellido_materno']); }
                                opt += '<tr style="'+((registro[i]['regimen_laboral'])?'background-color:#fff;':'background-color:rgb(255,199,206);')+'">';
                                opt += '<td>'+(i+1)+'</td>';
                                opt += '<td><input type="checkbox" class="boxreg" onclick="mostrar_boxreg()"></td>';
                                opt += '<td><span style="padding: 3px 8px 3px 8px;" title="Editar"   class="btn btn-info"   onclick="anadirespecialista('+registro[i]['idespecialista']+')">O</span></td>';
                                opt += '<td>'+((registro[i]['especialista_creo']==<?=$session['idespecialista']?>)?'<span style="padding: 3px 8px 3px 8px;" title="Eliminar" class="btn btn-danger" onclick="eliminarespecialista('+registro[i]['idespecialista']+')">X</span>':'')+'</td>';
                                opt += '<td>'+registro[i]['esp_nombres']+' '+registro[i]['esp_apellido_paterno']+' '+registro[i]['esp_apellido_materno']+'</td>';
                                opt += '<td>'+((registro[i]['regimen_laboral'])?registro[i]['regimen_laboral']:'')+'</td>';
                                for (let k = 0; k < fechas.length; k++) {
                                opt += '<td style="'+(([5,6].indexOf(fechas[k]['diasemana'])>-1)?'background-color:rgb(255,255,0);':((fechas[k]['feriado']==1)?'background-color:rgb(255,199,206);':''))+'">'+select_reg(registro[i]['fila'+k],registro[i]['idespecialista'],fechas[k]['fecha'],fechas[k]['diasemana'],fechas[k]['feriado'])+'</td>';
                                }
                                opt += '</td>';
                            }
                        $("#div_regasistencia").css('display','');
                        if(sin_regimen_laboral.length){ $("#btn_grabar").prop('disabled',true); alert( ((sin_regimen_laboral.length==1)?'El especialista '+sin_regimen_laboral.join(', ')+', de color rojo, no tiene':'Los especialistas '+sin_regimen_laboral.join(', ')+', de color rojo, no tienen')+' régimen laboral registrado. Presione el botón editar y añada el régimen laboral antes de grabar.'); }
                        $("#t_regasistencia").html(opt);
                        $("#t_regasistencia tr td").css({'padding-top':'3px','padding-bottom':'3px','padding-right':'5px','padding-left':'5px'});
                        mostrar_boxreg();
                    }else{
                    for (var i = 0; i < mantenimiento.length; i++) {
                        mantenimiento[i]['nro']              = i+1;
                        mantenimiento[i]['t_area']           = '<b>'+mantenimiento[i]['area']+'</b>';
                        mantenimiento[i]['t_equipo']         = '<b>'+mantenimiento[i]['equipo']+'</b>';
                        mantenimiento[i]['t_cap']            = form_archivo(mantenimiento[i],'cap',i);
                        mantenimiento[i]['t_ley29944']       = form_archivo(mantenimiento[i],'ley29944',i);
                        mantenimiento[i]['t_gerentepublico'] = form_archivo(mantenimiento[i],'gerentepublico',i);
                        mantenimiento[i]['t_practicante']    = form_archivo(mantenimiento[i],'practicante',i);
                        mantenimiento[i]['t_cas']            = form_archivo(mantenimiento[i],'cas',i);
                    }
                    table4.clear().draw();
                    table4.rows.add(mantenimiento).draw();
                    $('.dt-buttons button span').html('Descargar Excel');
                    $('.dt-buttons button').css('color','#000');
                    $("#t_mantenimiento_wrapper").css('display','');
                    $("#t_mantenimiento tr td").css({'padding-top':'3px','padding-bottom':'3px','padding-right':'5px','padding-left':'5px'});
                    }
                }else{
                    table4.clear().draw();
                }
                  
                }
          });
}


function mostrar_boxreg(){
    if($(".boxreg:checked").length>0){        
        $("#t_boxreg").html('<b>Cambie los registro en bloque:</b> '+select_reg().replace('class="reg regbox"','onchange="modificar_boxreg(value)"') + '<br>');
    }else{
        $("#t_boxreg").html('<b>Selecione los especialistas para cambiar los registro en bloque</b>');
    }
}

function modificar_boxreg(value){
    var elemento = $(".boxreg:checked");
    for (let i = 0; i < elemento.length; i++) {
         var tr = $(elemento[i]).parent().parent();
         if( ['R','P','N','M','FE','O'].indexOf(value)>-1){
             $(tr).children('td').children('select.regbox') .val(value);
             $(tr).children('td').children('select.sabado') .val('S');
             $(tr).children('td').children('select.domingo').val('D');
             $(tr).children('td').children('select.feriado').val('FE');
         }else{
              $(tr).children('td').children('select').val(value);
         }
    }
}

function select_reg(value='',idespecialista='',fecha='',diasemana='',feriado=''){
var opt  = '';
    opt += (idespecialista)?'<input class="idespecialista" style="display:none;" value="'+idespecialista+'" type="text">':'';
    opt += (fecha)         ?'<input class="fecha"          style="display:none;" value="'+fecha+'" type="text">':'';
    
    if(diasemana==5){
        //opt += '<input class="reg" type="text" style="display:none;" value="'+((value)?value:'S')+'">';
        opt += '<select class="reg sabado" style="width:40px;">';
        opt += '<option '+((value=='S')     ?'selected':'')+'>S</option>';
        opt += '<option '+((value=='DL/PHS')?'selected':'')+'>DL/PHS</option>';
        opt += '<option '+((value=='LSG')   ?'selected':'')+'>LSG</option>';
        opt += '<option '+((value=='LG/SC') ?'selected':'')+'>LG/SC</option>';
        opt += '<option '+((value=='V')     ?'selected':'')+'>V</option>';
        opt += '<option '+((value=='LPS')   ?'selected':'')+'>LPS</option>';
        opt += '<option '+((value=='LPM')   ?'selected':'')+'>LPM</option>';
        opt += '<option '+((value=='LPP')   ?'selected':'')+'>LPP</option>';
        opt += '<option '+((value=='-')     ?'selected':'')+'>-</option>';
        opt += (value=='M')?'<option selected>M</option>':'';
        opt += '</select>';
        //opt += '<b>'+((value)?value:'S')+'</b>';
    }else if(diasemana==6){
        //opt += '<input class="reg" type="text" value="'+((value)?value:'D')+'">';
        opt += '<select class="reg domingo" style="width:40px;">';
        opt += '<option '+((value=='D')     ?'selected':'')+'>D</option>';
        opt += '<option '+((value=='DL/PHS')?'selected':'')+'>DL/PHS</option>';
        opt += '<option '+((value=='LSG')   ?'selected':'')+'>LSG</option>';
        opt += '<option '+((value=='LG/SC') ?'selected':'')+'>LG/SC</option>';
        opt += '<option '+((value=='V')     ?'selected':'')+'>V</option>';
        opt += '<option '+((value=='LPS')   ?'selected':'')+'>LPS</option>';
        opt += '<option '+((value=='LPM')   ?'selected':'')+'>LPM</option>';
        opt += '<option '+((value=='LPP')   ?'selected':'')+'>LPP</option>';
        opt += '<option '+((value=='-')     ?'selected':'')+'>-</option>';
        opt += (value=='M')?'<option selected>M</option>':'';
        opt += '</select>';
        //opt += '<b>'+((value)?value:'D')+'</b>';
    }else if(feriado==1){
        opt += '<select class="reg feriado" style="width:40px;">';
        opt += '<option '+((value=='FE')     ?'selected':'')+'>FE</option>';
        opt += '<option '+((value=='DL/PHS')?'selected':'')+'>DL/PHS</option>';
        opt += '<option '+((value=='LSG')   ?'selected':'')+'>LSG</option>';
        opt += '<option '+((value=='LG/SC') ?'selected':'')+'>LG/SC</option>';
        opt += '<option '+((value=='V')     ?'selected':'')+'>V</option>';
        opt += '<option '+((value=='LPS')   ?'selected':'')+'>LPS</option>';
        opt += '<option '+((value=='LPM')   ?'selected':'')+'>LPM</option>';
        opt += '<option '+((value=='LPP')   ?'selected':'')+'>LPP</option>';
        opt += '<option '+((value=='R')     ?'selected':'')+'>R</option>';
        opt += '<option '+((value=='P')     ?'selected':'')+'>P</option>';
        opt += '<option '+((value=='-')     ?'selected':'')+'>-</option>';
        opt += (value=='M')?'<option selected>M</option>':'';
        opt += '</select>';
        //opt += '<input class="reg" type="text" style="display:none;" value="'+((value)?value:'FE')+'">';
        //opt += '<b>'+((value)?value:'FE')+'</b>';
    }else{
    opt += '<select class="reg regbox" style="width:40px;">';
    opt += '<option '+((value=='P')     ?'selected':'')+'>P</option>';//
    opt += '<option '+((value=='R')     ?'selected':'')+'>R</option>';//
    //opt += '<option '+((value=='N')     ?'selected':'')+'>N</option>';//
    opt += '<option '+((value=='FE')    ?'selected':'')+'>FE</option>';//
    opt += '<option '+((value=='DL/PHS')?'selected':'')+'>DL/PHS</option>';
    opt += '<option '+((value=='LSG')   ?'selected':'')+'>LSG</option>';
    opt += '<option '+((value=='LG/SC') ?'selected':'')+'>LG/SC</option>';
    opt += '<option '+((value=='O')     ?'selected':'')+'>O</option>';//
    opt += '<option '+((value=='V')     ?'selected':'')+'>V</option>';
    opt += '<option '+((value=='LPS')   ?'selected':'')+'>LPS</option>';
    opt += '<option '+((value=='LPM')   ?'selected':'')+'>LPM</option>';
    opt += '<option '+((value=='LPP')   ?'selected':'')+'>LPP</option>';
    opt += '<option '+((value=='F')     ?'selected':'')+'>F</option>';
    opt += '<option '+((value=='-')     ?'selected':'')+'>-</option>';
    opt += (value=='M')?'<option selected>M</option>':'';
    opt += '</select>';
    }
    
    return opt;
}

</script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script type="text/javascript">
flatpickr('.flatfecha', {
    mode: "multiple",
dateFormat: "Y-m-d",
disable: [
    function(date) {
        // return true to disable
        return (date.getDay() === 0 || date.getDay() === 6);
    }
],
  minDate: '<?=date('Y-m-d')?>',  
  locale: {
    firstDayOfWeek: 1,
    weekdays: {
      shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
      longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],         
    }, 
    months: {
      shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
      longhand: ['Enero', 'Febrero', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    },
  },
});
</script>
@endsection