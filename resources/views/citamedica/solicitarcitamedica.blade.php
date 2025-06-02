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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>SOLICITAR CITA MEDICA</b></h5>
        <div class="position-relative form-group">
            <a target="_blank" href="assets/manual/MANUAL-SOLICITUD-DE-CITA-MEDICA.pdf" class="btn btn-danger">Manual de solicitud de cita medica</a>
            <br><br>
            <form id="formulario01" enctype="multipart/form-data" onsubmit="guardarcitamedica();return false;">
            <b style="color:#000;">NIVEL: </b> 
                <select name="codmod" id="codmod" class="form-control" onchange="listarcitamedica();">
    						<?php
    						for ($i=0; $i < count($session['conf_permisos']); $i++) {
    						$key = $session['conf_permisos'][$i];
    						?><option value="<?=$key['esc_codmod']?>"><?=$key['nivel_pap']?></option><?php
    						}
    						?>
    					</select>
    		<div class="col-sm-12"><br></div>
            <div class="row">
            <div class="col-sm-4">
                <!--<button class="btn btn-success" onclick="sinfiltros()">SOLICITAR CITA MEDICA</button>-->
            </div>
            <!--<div class="col-sm-8" style="font-size:18px;"><b>Fecha y hora aproximada de cita: </b> <b style="color:red;" id="fechahora"></b> </div>-->
            </div>
            
            <div class="col-sm-12"><br></div>
            <b style="color:#000;">PERSONAL DE LA IE: </b> 
            <table class="display table table-bordered table-striped table-dark" id="t_nexus" style="width:100%;color:#000;font-size:10px;">
              <thead>
                <tr style="background-color:rgb(0,117,184);">
                    <td style="min-width:10px;color:#fff;"><b>No</b></td>
                    <td style="min-width:55px;color:#fff;"><b>NOMBRE COMPLETO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CORREO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CELULAR</b></td>
                    <td style="min-width:55px;color:#fff;"><b>SIGUIENTE CITA</b></td>
                    <td style="min-width:50px;color:#fff;"><b>CODIGO DE PLAZA</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CARGO</b></td>
                    <td style="min-width:45px;color:#fff;"><b>SITUACION LABORAL</b></td>
                    <td style="min-width:45px;color:#fff;"><b>DNI</b></td>
                    <td style="min-width:35px;color:#fff;"><b>TIPO DE REGISTRO</b></td>
                </tr>
              </thead>
              <tbody>
                  <tr><td colspan="12"><center><img src="assets/images/load10.gif"></center></td></tr>
              </tbody>
            </table>
            <input type="hidden" name="id_contacto" value="<?=$session['id_contacto']?>">
            @csrf
            </form>
            
        </div>
    </div>
</div>

<br><br>
<div id="divcitas"></div>
<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>CITAS MEDICAS SOLICITADAS</b></h5>
        <div class="position-relative form-group">
            <b style="color:#000;">CITAS: </b> 
            <table class="display table table-bordered table-striped table-dark" id="t_citas" style="width:100%;color:#000;font-size:10px;">
              <thead>
                <tr style="background-color:rgb(0,117,184);">
                    <td style="min-width:55px;color:#fff;"><b>No</b></td>
                    <td style="min-width:55px;color:#fff;"><b>FECHA CITA</b></td>
                    <td style="min-width:55px;color:#fff;"><b>DIAGNOSTICO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>NOMBRE COMPLETO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CORREO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CELULAR</b></td>
                    <td style="min-width:50px;color:#fff;"><b>CODIGO DE PLAZA</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CARGO</b></td>
                    <td style="min-width:45px;color:#fff;"><b>SITUACION LABORAL</b></td>
                    <td style="min-width:45px;color:#fff;"><b>DNI</b></td>
                    <td style="min-width:35px;color:#fff;"><b>TIPO DE REGISTRO</b></td>
                </tr>
              </thead>
              <tbody>
                  <tr><td colspan="12"><center><img src="assets/images/load10.gif"></center></td></tr>
              </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">

function sinfiltros(){
    $("#t_nexus_filter input").val('');
    $("#t_nexus_filter input").keyup();
}

function guardarcitamedica(id='formulario01'){
    var ifimagen = true;
    //$('#btn_enviar').prop('disabled',true);
      if(true){
          //información del formulario
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: "{{route('guardarcitamedica')}}",  
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
                  $('html, body').animate({ scrollTop: $("#divcitas").offset().top }, 3000);
                  listarcitamedica();
                  alert('Citas registradas');
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
    }else{
      alert('Llene todos los campos');
    }
}


var nro_carga = 1;
var g_nexus = [];
function listarcitamedica(){
    ajax_data = {
      "codmodce" : $("#codmod").val(),
      "alt"      : Math.random()
    }
    $.ajax({
                type: "GET",
                url: "{{route('listarcitamedica')}}",
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      //imagen de carga
                      if(nro_carga>1) $("#t_nexus tbody").html('<tr><td colspan="12"><center><img src="assets/images/load10.gif"></center></td></tr>');
                      nro_carga++;
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    var nexus = data['nexus'];
                    $("#fechahora").html(data['siguiente_cita']['fecha_inicio']);
                    for (var i = 0; i < nexus.length; i++) {
                		nexus[i]['box']        = (nexus[i]['inicio'])?'':'<input style="width:16px;height:16px;" type="checkbox" onclick="selectbox(this,'+i+');" name="box[]" value="'+nexus[i]['nexus_id']+'">';
                		nexus[i]['t_correo']   = '<div id="viewcorreo'+i+'">'+nexus[i]['correo'] +'</div>'+'<div id="correo'+i+'"></div>';
                		nexus[i]['t_celular']  = '<div id="viewcelular'+i+'">'+nexus[i]['celular']+'</div>'+'<div id="celular'+i+'"></div>';
                        nexus[i]['nombrecompleto'] = nexus[i]['apellipat']+' '+nexus[i]['apellimat']+' '+nexus[i]['nombres'];
                    }
                	g_nexus = nexus;
                	table4.clear().draw();
                    table4.rows.add(nexus).draw();
                    
                    var citas = data['citas'];
                    for (var i = 0; i < citas.length; i++) {
                        citas[i]['nro']            = i+1;
                        citas[i]['nombrecompleto'] = citas[i]['apellipat']+' '+citas[i]['apellimat']+' '+citas[i]['nombres'];
                        var r_caso = [];
                        if(citas[i]['caso1']){ r_caso.push(citas[i]['caso1']); }
                        if(citas[i]['caso2']){ r_caso.push(citas[i]['caso2']); }
                        if(citas[i]['caso3']){ r_caso.push(citas[i]['caso3']); }
                        citas[i]['t_dignostico']   = '<span style="'+colordignostico(citas[i]['dignostico'])+'">'+citas[i]['dignostico']+'</span>'+' '+((r_caso.length)?"<b>("+r_caso.join()+")</b>":"");
                        citas[i]['t_inicio']       = '<b style="font-size:14px;">'+citas[i]['inicio']+'</b>';
                        
                    }
                    table5.clear().draw();
                    table5.rows.add(citas).draw();
                    $("#t_nexus tr td").css('text-align','center');
                    $("#t_citas tr td").css('text-align','center');
                    
                    jaaf_padding();
                    $(".paginate_button").attr('onclick',"jaaf_padding()");
                    $(".dt-button.buttons-excel.buttons-html5 span").html('Descargar Excel');
                 }
          });
}

function colordignostico(dignostico){
    var style = '';
    switch (dignostico) {
      case 'APTO': style="font-size:14px;color:green;font-weight: bold;";break;
      case 'NO APTO': style="font-size:14px;color:red;font-weight: bold;";break;
      case 'NO SE PRESENTO': style="font-size:14px;font-weight: bold;";break;
      default: style="";break;
    }
    return style;
}

var g_this;
function selectbox(athis,nro){
    if(athis.checked){
        $("#viewcorreo"+nro).css('display','none');
        $("#viewcelular"+nro).css('display','none');
        $("#correo"+nro) .html('<input style="width:100%;" name=correo[]  value="'+$("#viewcorreo"+nro).html()+'"  required>');
        $("#celular"+nro).html('<input style="width:100%;" name=celular[] value="'+$("#viewcelular"+nro).html()+'" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required>');
    }else{
        $("#viewcorreo"+nro).css('display','');
        $("#viewcelular"+nro).css('display','');
        $("#correo"+nro) .html('');
        $("#celular"+nro) .html('');
        $("#data"+nro) .html('');
    }
}

function jaaf_padding(id='t_nexus'){
    $("#"+id+" tr td").css({'padding-top':'4px','padding-bottom':'1px','padding-righ':'1px','padding-left':'1px'});
    $(".paginate_button").attr('onclick',"jaaf_padding()");
}

            var table4 = $("#t_nexus").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 300,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "box" },
                            { "data": "nombrecompleto" },
                            { "data": "t_correo" },
                            { "data": "t_celular" },
                            { "data": "inicio" },
                            { "data": "codplaza" },
                            { "data": "descargo" },
                            { "data": "situacion" },
                            { "data": "numdocum" },
                            { "data": "tiporegistro" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });
                    
            var table5 = $("#t_citas").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 30,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "t_inicio" },
                            { "data": "t_dignostico" },
                            { "data": "nombrecompleto" },
                            { "data": "correo" },
                            { "data": "celular" },
                            { "data": "codplaza" },
                            { "data": "descargo" },
                            { "data": "situacion" },
                            { "data": "numdocum" },
                            { "data": "tiporegistro" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

listarcitamedica();

</script>


@endsection