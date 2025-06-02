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

<style>
    #t_resumen tr td{
        padding-right:5px;
        padding-left:5px;
    }
    

    
</style>

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REPORTE DE CITAS MEDICAS</b>
    
            <table border="1" id="t_resumen" style="float:right;text-align:center;">
                <tr style="font-weight: bold;background-color:green;color:#fff;">
                    <td >TOTAL</td>
                    <td >APTO</td>
                    <td >NO APTO</td>
                    <td >NO SE PRESENTO</td>
                </tr>
                <tr>
                    <td id="total"></td>
                    <td id="apto"></td>
                    <td id="noapto"></td>
                    <td id="nosepresento"></td>
                </tr>
            </table>
            
    </h5>
        <div class="position-relative form-group">
            <input type="radio" name="boxcita" value="1" onclick="listarreportecitamedica();" checked> Citas de hoy 
            <input type="radio" name="boxcita" value="2" onclick="listarreportecitamedica();"> Proximas citas 
            <input type="radio" name="boxcita" value="3" onclick="listarreportecitamedica();"> Atendidas
            <input type="radio" name="boxcita" value="3" onclick="listarresumencitamedica();"> Citas por dia
            
            <br><br>
            
            <div class="divtabla" id="div_citas">
            <table class="display table table-bordered table-striped table-dark" id="t_citas" style="width:100%;color:#000;font-size:10px;">
              <thead>
                <tr style="background-color:rgb(0,117,184);">
                    <td style="min-width:55px;color:#fff;"><b>No</b></td>
                    <td style="min-width:55px;color:#fff;"><b>FECHA CITA</b></td>
                    <td style="min-width:55px;color:#fff;"><b>DIAGNOSTICO</b></td>
                    <?php if($editar){ ?>
                    <td style="min-width:55px;color:#fff;"><b></b></td>
                    <?php } ?>
                    <td style="min-width:55px;color:#fff;"><b>NOMBRE COMPLETO</b></td>
                    <td style="min-width:45px;color:#fff;"><b>DNI</b></td>
                    <td style="min-width:45px;color:#fff;"><b>EDAD</b></td>
                    <td style="min-width:50px;color:#fff;"><b>INSTITUCION</b></td>
                    <td style="min-width:50px;color:#fff;"><b>DISTRITO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CORREO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CELULAR</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CARGO</b></td>
                </tr>
              </thead>
              <tbody>
                  <tr><td colspan="12"><center><img src="assets/images/load10.gif"></center></td></tr>
              </tbody>
            </table>
            </div>
            
            <div class="divtabla" id="div_citasxdia">
                <table class="display table table-bordered table-striped table-dark" id="t_citasxdia" style="width:100%;color:#000;font-size:10px;">
              <thead>
                <tr style="background-color:rgb(0,117,184);">
                    <td style="min-width:55px;color:#fff;"><b>FECHA</b></td>
                    <td style="min-width:55px;color:#fff;"><b>CANTIDAD DE CITAS</b></td>
                    <td style="min-width:55px;color:#fff;"><b>APTO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>NO APTO</b></td>
                    <td style="min-width:55px;color:#fff;"><b>NO SE PRESENTO</b></td>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
            </div>
            
        </div>
    </div>
</div>

<script type="text/javascript">

var g_citas = [];
function listarreportecitamedica(){
    $(".divtabla").css('display','none');
    $("#div_citas").css('display','');
    ajax_data = {
      "boxcita" : $("input[name=boxcita]:checked").val(),
      "alt"     : Math.random()
    }
    $.ajax({
                type: "GET",
                url: "{{route('listarreportecitamedica')}}",
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      //imagen de carga
                },
                error: function(){
                      alert("error peticion ajax");
                },
                success: function(citas){
                    var apto         = 0;
                    var noapto       = 0;
                    var nosepresento = 0;
                    for (var i = 0; i < citas.length; i++) {
                        citas[i]['nro'] = i+1;
                        citas[i]['nombrecompleto'] = citas[i]['apellipat']+' '+citas[i]['apellimat']+' '+citas[i]['nombres'];
                        citas[i]['t_dignostico']   = '<span style="'+colordignostico(citas[i]['dignostico'])+'">'+citas[i]['dignostico']+'</span>';
                        if(citas[i]['informemedico']){
                            citas[i]['t_editar']  = '<table style="padding:0;">';
                            citas[i]['t_editar'] += '<tr>';
                            citas[i]['t_editar'] += '<td style="border:0;padding:0;">'+'<span class="btn btn-danger" onclick="vercitamedica('+i+');">Editar</span>'+'</td>';
                            citas[i]['t_editar'] += '<td style="border:0;padding:0;">'+'<a target="_blank" class="btn btn-warning" href="'+citas[i]['informemedico']+'">PDF</a>'+'</td>';
                            citas[i]['t_editar'] += '</tr>';
                            citas[i]['t_editar'] += '</table>';
                        }else{
                            citas[i]['t_editar'] = '<span class="btn btn-danger" onclick="vercitamedica('+i+');">Editar</span>'
                        }
                            citas[i]['t_inicio']       = '<b style="font-size:14px;">'+citas[i]['inicio']+'</b>';
                            if(citas[i]['dignostico']=='APTO'){ apto++; }
                            if(citas[i]['dignostico']=='NO APTO'){ noapto++; }
                            if(citas[i]['dignostico']=='NO SE PRESENTO'){ nosepresento++; }
                    }
                    g_citas = citas;
                    table5.clear().draw();
                    table5.rows.add(citas).draw();
                    $("#t_citas tr td").css('text-align','center');
                    $("#total")       .html(citas.length);
                    $("#apto")        .html(apto);
                    $("#noapto")      .html(noapto);
                    $("#nosepresento").html(nosepresento);
                 }
          });
}

function listarresumencitamedica(){
    $(".divtabla").css('display','none');
    $("#div_citasxdia").css('display','');
    ajax_data = {
      "alt"     : Math.random()
    }
    $.ajax({
                type: "GET",
                url: "{{route('listarresumencitamedica')}}",
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      //imagen de carga
                },
                error: function(){
                      alert("error peticion ajax");
                },
                success: function(citas){
                    var total        = 0;
                    var apto         = 0;
                    var noapto       = 0;
                    var nosepresento = 0;
                    for (var i = 0; i < citas.length; i++) {
                        total        = total + parseInt(citas[i]['total']);
                        apto         = apto + parseInt(citas[i]['apto']);
                        noapto       = noapto + parseInt(citas[i]['noapto']);
                        nosepresento = nosepresento + parseInt(citas[i]['nosepresento']);
                    }
                    
                    table6.clear().draw();
                    table6.rows.add(citas).draw();
                    $("#total")       .html(total);
                    $("#apto")        .html(apto);
                    $("#noapto")      .html(noapto);
                    $("#nosepresento").html(nosepresento);
                 }
          });
}

function vercitamedica(nro){
    var key = g_citas[nro];
    $("input[name=idCmm]").val(key['idCmm']);
    $("input[name=nro_informe]")   .val(key['nro_informe']);
    $("select[name=dignostico]")   .val(key['dignostico']);
    $("select[name=nrodosis]")     .val(key['nrodosis']);
    $("textarea[name=observacion]").val(key['observacion']);
    $("input[name=caso1]").prop('checked',(key['caso1'])?true:false);
    $("input[name=caso2]").prop('checked',(key['caso2'])?true:false);
    $("input[name=caso3]").prop('checked',(key['caso3'])?true:false);
    
    if(key['dignostico']=='NO APTO'){
        $("#div_caso").css('display','');
    }else{
        $("#div_caso").css('display','none');
    }
    
    $("#fc_vercitamedica").click();
}

function div_caso(){
    if($("select[name=dignostico]").val()=='NO APTO'){
        $("#div_caso").css('display','');
    }else{
        $("#div_caso").css('display','none');
    }
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
                            <?php if($editar){ ?>
                            { "data": "t_editar" },
                            <?php } ?>
                            { "data": "nombrecompleto" },
                            { "data": "numdocum" },
                            { "data": "edad" },
                            { "data": "nombie" },
                            { "data": "distrito" },
                            { "data": "correo" },
                            { "data": "celular" },
                            { "data": "descargo" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

var table6 = $("#t_citasxdia").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 30,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "fecha" },
                            { "data": "total" },
                            { "data": "apto" },
                            { "data": "noapto" },
                            { "data": "nosepresento" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });
                    
listarreportecitamedica();

</script>

@endsection

    <div id="vercitamedica" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
          <div class="modal-header" ><h4 style="font-weight: bold;">CITA MEDICA</h4></div>
          <form id="formulario01" class="form-horizontal calender" role="form" onsubmit="editarcitamedica();return false;">
          <div class="modal-body">
             <div id="testmodal" style="padding: 5px 20px;">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Numero de Informe:</b></div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <input name="nro_informe" class="form-control" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Diagnostico:</b></div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <input type="hidden" name="idCmm">
                        <select class="form-control" name="dignostico" onchange="div_caso()" required>
                            <option value="">Seleccione una opción</option>
                            <option>APTO</option>
                            <option>NO APTO</option>
                            <option>NO SE PRESENTO</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="div_caso">
                        <br><input type="checkbox" name="caso1" value="Enfermedad cronica"> Enfermedad cronica
                        <br><input type="checkbox" name="caso2" value="Obecidad"> Obecidad
                        <br><input type="checkbox" name="caso3" value="Edad"> Edad
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Numero de dosis:</b></div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <select class="form-control" name="nrodosis" required>
                            <option value="">Seleccione una opción</option>
                            <option>0</option>
                            <option>01</option>
                            <option>02</option>
                            <option selected>03</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Observaciones:</b></div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><textarea class="form-control" name="observacion" style="height: 150px;" required></textarea></div>
                </div>
              
            </div>       	
          </div>
          <div class="modal-footer">
            <button class="btn btn-success">Grabar</button>
            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>         
          </div>
          @csrf
          </form>
        </div>
      </div>
    </div>

<div id="fc_vercitamedica"  data-toggle="modal" data-target="#vercitamedica"></div>

<script type="text/javascript">

function editarcitamedica(id='formulario01'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          //hacemos la peticion ajax  
          $.ajax({
              url: '{{route('editarcitamedica')}}',  
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
                  listarreportecitamedica();
                  if(data){
                    alert('Datos guardados');
                    window.open(data,'_blank');
                  }else{
                    alert('No se puedo guardar los datos');
                  }
                  $("#fc_vercitamedica").click();
              },
              error: function(){
                alert('Se ha producido un error, recargue la pagina de nuevo.');
              }
          });
}

</script>