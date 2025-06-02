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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>Requerimiento de visación de certificados de estudio</b></h5>
        <div class="position-relative form-group">

           
            <div class="row" style="color:#000;">
                <a href="#" class="btn btn-success" onclick="$('#div_pendientes .dt-buttons .buttons-excel').click();">DESCARGAR REPORTE EN EXCEL</a>&nbsp;&nbsp;&nbsp;
                <a href="#" target="_blank" id="btn_expedientes" class="btn btn-danger" onclick="descargarexpedientes();">DESCARGAR EXPEDIENTES</a>&nbsp;&nbsp;&nbsp;
            </div>
            <!--
            <div class="row" style="color:#000;">
              <div class="col-sm-12"><br></div>
              <div class="col-sm-1"><b>Buscar:</b></div>
              <div class="col-sm-5">
                <input type="text" class="">
              </div>
              <div class="col-sm-6">
                  <b>Fecha:</b> <input type="text" class="flatfecha" id="fecha">
                  &nbsp;&nbsp;&nbsp;
                  <button class="btn btn-success" onclick="ver_certificadodeestudio();">Consultar</button>
              </div>
            </div>
          -->
            <div class="row" style="color:#000;">
              <div class="col-sm-12"><br></div>              
              <div class="col-sm-12">
                    <input type="radio" name="box" value="1" onclick="ver_certificadodeestudio();" checked> Pendientes
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="2" onclick="ver_certificadodeestudio();"> Por subsanar
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="3" onclick="ver_certificadodeestudio();"> Citados
                    <!--&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="4" onclick="ver_certificadodeestudio();"> Por reprogramar  -->
                    &nbsp;&nbsp;&nbsp;  
                    <input type="radio" name="box" value="5" onclick="ver_certificadodeestudio();"> Recepcionado por el ciudadano
                    &nbsp;&nbsp;&nbsp;  
                    <input type="radio" name="box" value="6" onclick="ver_certificadodeestudio();"> Archivado    
              </div>
              <div class="col-sm-12" style="margin-top:8px;">
                    <b>Buscar:</b>
                    <input type="text" id="buscar" onkeypress="if(window.event.keyCode==13){ver_certificadodeestudio();};">
                    <b>Fecha:</b> <input type="text" class="flatfecha" id="fecha">
                    &nbsp;&nbsp;&nbsp;
                    <button class="btn btn-success" onclick="ver_certificadodeestudio();">Consultar</button>
              </div>
            </div>




            <div class="row">
                <div id="div_load"       class="col-sm-12 tablas" style="text-align: center;display:none;"><img src="assets/images/load10.gif"></div>
                <div id="div_pendientes" class="col-sm-12 tablas table-responsive" style="">
                    <table class="display table table-bordered table-striped" id="t_pendientes" style="color:#000;font-size:10px;width: 100%;">
                      <thead>
                        <tr style="color:#fff;">
                            <td style="background-color: rgb(32,55,100);"><b>N°</b></td>
                            <td style="background-color: rgb(32,55,100);"><input type="checkbox" id="cbbox" onclick="checkear();"></td>
                            <td style="background-color: rgb(32,55,100);"><b>EXPEDIENTE</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>FECHA</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>DOCUMENTO DE IDENTIDAD</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>CIUDADANO</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>ASUNTO</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>CELULAR CIUDADANO</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>CORREO CIUDADANO</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>SUBSANAR</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>CITAR A LA UGEL01</b></td>
                            <td style="background-color: rgb(169,169,169);"><b></b></td>
                            <td style="background-color: rgb(169,169,169);"><b>FUT y TICKET</b></td>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
  function checkear(){
    if($("#cbbox").prop('checked')==true){
      $(".box").prop('checked',true);
    }else{
      $(".box").prop('checked',false);
    }
  }

  function descargarexpedientes(){
    if($(".box:checked").length){
      var elm = $(".box:checked");
      var exp = [];
      for (let i = 0; i < elm.length; i++) {
        exp.push(elm[i].value);
        $("#btn_expedientes").prop("href","https://ventanillavirtual.ugel01.gob.pe/index.php/buzondecomunicaciones/descargar_exp?idreclamo="+exp.join());
      }
    }else{
        $("#btn_expedientes").prop("href","https://ventanillavirtual.ugel01.gob.pe/index.php/buzondecomunicaciones/descargar_exp?idreclamo="+allexp.join());
    }


    
  }
  

var allexp = []
function ver_certificadodeestudio(){
    ajax_data = {
        "box"    : $("input[name=box]:checked").val(),
        "fecha"  : $("#fecha").val(),
        "buscar" : $("#buscar").val(),
      "alt"     : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('ver_certificadodeestudio')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                          $(".tablas")  .css('display','none');
                          $("#div_load").css('display','');
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                        if(data){
                        allexp = []
                        for (let i = 0; i < data.length; i++) {
                            data[i]['nro']          = i+1;
                            data[i]['box']          = '<center><input type="checkbox" name="box" class="box" value="'+data[i]['idreclamo']+'"></center>';
                            //data[i]['t_archivo']    = '<a    class="btn btn-info"   target="_blank" href="'+data[i]['archivo']+'">PDF</a>';
                            var cantidad_subsanar = (data[i]['cantidad_subsanar']>0)?'('+data[i]['cantidad_subsanar']+')':'';
                            data[i]['t_subsanar']   = '<span class="btn btn-danger" onclick="solicitarsubsanarcertificado('+data[i]['idreclamo']+');">SUBSANAR '+cantidad_subsanar+'</span>';
                            var cantidad_citas = (data[i]['cantidad_citas']>0)?'('+data[i]['cantidad_citas']+')':'';
                            data[i]['t_citar']      = '';
                            data[i]['t_citar']     += (data[i]['etapa']=='REPROGRAMAR')?'<span class="btn btn-info" onclick="citarciudadano('+data[i]['idreclamo']+');">REPROGRAMAR'+cantidad_citas+'</span>':'';
                            data[i]['t_citar']     += (data[i]['fechacita'])?'<b style="cursor: pointer;" onclick="citarciudadano('+data[i]['idreclamo']+');">'+data[i]['fechacita']+'</b>':'<span class="btn btn-info" onclick="citarciudadano('+data[i]['idreclamo']+');">CITAR'+cantidad_citas+'</span>';
                            data[i]['t_citar']     += (data[i]['etapa']=='CONFIRMADO')?' <i class="pe-7s-like2" style="font-size:22px;color:green;" title="CONFIRMADO"> </i>':'';
                            
                            data[i]['t_acciones']   = '';
                            if(data[i]['etapa']=='CITADO' || data[i]['etapa']=='CONFIRMADO'){
                               data[i]['t_acciones'] +='<span class="btn btn-info"    onclick="citarciudadano('+data[i]['idreclamo']+');">REPROGRAMAR'+cantidad_citas+'</span>';
                               data[i]['t_acciones'] +='<span class="btn btn-success" onclick="recepcionar_certificado('+data[i]['idreclamo']+');">RECEPCIONAR</span>';
                               data[i]['t_acciones'] +='<span class="btn btn-warning" onclick="archivar_certificado('+data[i]['idreclamo']+');">ARCHIVAR</span>';
                               
                            }

                            data[i]['t_expediente'] = '<a target="_blank" href="http://siic01.ugel01.gob.pe/index.php/notificacion/fut/'+data[i]['idreclamo']+'">PDF</a>';
                            allexp.push(data[i]['idreclamo']);
                        }
                        table4.clear().draw();                  
                        table4.rows.add(data).draw();
                        }
                        $("#div_load").css('display','none');
                        $("#div_pendientes").css('display','');
                      }
              });
}

function recepcionar_certificado(idreclamo){
    ajax_data = {
      "idreclamo" : idreclamo,
      "alt"       : Math.random()
    }
    if(confirm('¿Esta seguro que desea recepcionar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('recepcionar_certificado')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){

                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                      alert('El ciudadano recepciono el certificado');
                      ver_certificadodeestudio();
                    }
              });
    }
}

function archivar_certificado(idreclamo){
    ajax_data = {
      "idreclamo" : idreclamo,
      "alt"       : Math.random()
    }
    if(confirm('¿Esta seguro que desea archivar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('archivar_certificado')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){

                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                      alert('Se archivó');
                      ver_certificadodeestudio();
                    }
              });
    }
}

function solicitarsubsanarcertificado(idreclamo=0){
  $("#popuplg .modal-content").html('<img src="assets/images/load10.gif" style="width:400px;margin-left: auto;margin-right: auto;display: block;">');
  $("#popuplg .modal-content").load('{{route('solicitarsubsanarcertificado')}}?idreclamo='+idreclamo);
  $("#fc_popuplg").click();
}

function citarciudadano(idreclamo=0){
  $("#popuplg .modal-content").load('{{route('citarciudadano')}}?idreclamo='+idreclamo);
  $("#fc_popuplg").click();
}

var table4 = $("#t_pendientes").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "box" },
                            { "data": "cod_reclamo" },
                            { "data": "t_fecha_expediente" },
                            { "data": "documento" },
                            { "data": "ciudadano" },
                            { "data": "resumen_pedido" },
                            { "data": "celular" },
                            { "data": "correo" },
                            //{ "data": "t_archivo" },
                            { "data": "t_subsanar" },
                            { "data": "t_citar" },
                            { "data": "t_acciones" },
                            { "data": "t_expediente" },                            
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
        });

    ver_certificadodeestudio();
</script>

<script>
flatpickr('.flatfecha', {
      mode: "range",
      dateFormat: "Y-m-d",
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