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
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<style>
    #t_resumen tr td{
        padding-right:5px;
        padding-left:5px;
    }
    
    .si{
        color:green;
        font-size:16px;
        font-weight: bolder;
    }
    
    .no{
        color:red;
        font-size:16px;
        font-weight: bolder;
    }
    
</style>

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>GENERAR QR</b>
    </h5>
        <div class="position-relative form-group">
            
        <form id="formulario" onsubmit="grabarqr();return false;">
            <div class="row">
                <div class="col-sm-2">idQr:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="idQr" id="idQr" readonly></div>

                <div class="col-sm-2">Nombre:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="nombre" id="nombre"></div>
                
                <div class="col-sm-2">Url:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="url" id="url"></div>
                
                <div class="col-sm-2  corta"  id="nomCorta"></div>
                <div class="col-sm-10 corta" id="urlCorta">
                    
                    
                    </div>

                <div class="col-sm-2"></div>
                <div class="col-sm-10"><br><button class="btn btn-success">GRABAR</button> <input class="btn btn-danger" type="reset" value="Cancelar" onclick="$('.corta').html('');"> </div>
            </div>
            @csrf
        </form>
            
            <div class="divtabla" id="div_citas">
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="width:15px;color:#fff;"><b>N</b></td>
                                <td style="width:45px;color:#fff;"><b>Nombre</b></td>
                                <td style="width:45px;color:#fff;"><b>url</b></td>
                                <td style="width:55px;color:#fff;"><b>QR</b></td>
                                <td style="width:55px;color:#fff;"><b>Visitas</b></td>
                                <td style="width:55px;color:#fff;"><b>Editar</b></td>
                                <td style="width:35px;color:#fff;"><b>Eliminar</b></td>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">

function grabarqr(id='formulario'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_qr')}}",  
              type: 'POST',
              data: formData,
              dataType: "json",
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  
              },
              success: function(data){
                $("#pnombre").html(data['nomQr']);
                $("#purl").html('<?=$base_url?>'+data['urlCorQr']);
                $("#contenedorQR").html("");
                const contenedorQR = document.getElementById('contenedorQR');
                const formulario = document.getElementById('formulario');
                const QR = new QRCode(contenedorQR);
                QR.makeCode('<?=$base_url?>'+data['urlCorQr']);
                $("#fc_qr").click();
                setTimeout(function(){ $("#contenedorQR img").css('display',''); },500);
                formulario.reset();
                $(".corta").html('');
                tabla_qr();
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
}

var g_data=[];

function tabla_qr(){
    ajax_data = {
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_qr')}}",
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
                            data[i]['qr']       ='<span class="btn btn-info" onclick="ver_qr('+i+')">ver qr</span>';
                            data[i]['urlCorQr'] ='<?=$base_url?>'+data[i]['urlCorQr'];
                            data[i]['editar']   ='<span class="btn btn-success" onclick="editar('+i+')">editar</span>';
                            data[i]['eliminar'] ='<span class="btn btn-danger" onclick="eliminar_qr('+data[i]['idQr']+')">eliminar</span>';
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          g_data=data;
                      }
              });

}
/*
var qrcode = new QRCode("test", {
    text: "http://jindo.dev.naver.com/collie",
    width: 128,
    height: 128,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});
*/
function ver_qr(nro){
  var data = g_data[nro];
  $("#pnombre").html(data['nomQr']);
  $("#purl").html(data['urlCorQr']);
  $("#contenedorQR").html("");
  const contenedorQR = document.getElementById('contenedorQR');
  const QR = new QRCode(contenedorQR,{width: 256,height: 256});
  QR.makeCode(data['urlCorQr']);
  $("#fc_qr").click();
  setTimeout(function(){ $("#contenedorQR img").css('display',''); },500);
}
function editar(nro){
  var data = g_data[nro];
  $("#idQr").val(data['idQr']);
  $("#nombre").val(data['nomQr']);
  $("#url").val(data['urlQr']);
  $("#nomCorta").html('Url Corta:');
  $("#urlCorta").html(data['urlCorQr']+' (<a href="#" onclick="url_editar('+"'"+data['codQr']+"'"+');">Editar url corta</a>)');
}

function url_editar(codQr){
    $("#urlCorta").html('<?=$base_url?><input type="text" style="width:300px;" name="urlCorQr" value="'+codQr+'">');
}

function eliminar_qr(idQr){
    ajax_data = {
      "idQr"   : idQr,
      "alt"    : Math.random()
    }

    if(confirm('¿Desea eliminar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_qr')}}",
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
                      tabla_qr();
                      }
              });
      }else{

      }

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
                            { "data": "nomQr" },
                            { "data": "urlQr" },
                            { "data": "qr" },
                            { "data": "cantidad" },
                            { "data": "editar" },
                            { "data": "eliminar" },
                            //{ "data": "eliminar" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

                    tabla_qr();
</script>
@endsection

<div id="ModalQr" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          
          <div class="modal-header" style="text-align:center;">            
            <h4 class="modal-title" id="myModalLabel">QR </h4>
          </div>
          <div class="modal-body">

             <div id="testmodal" style="padding: 5px 20px;">
              <form class="form-horizontal calender" role="form">

                <div class="form-group">
                <!--AQUI VAR A COLOCAR EL CODIGO QR-->    
                <div id="pnombre"      style="text-align: center;font-size:30px;color:red;font-weight: bold;"></div>
                <div id="contenedorQR" style="text-align: center;"></div>
                <div id="purl"style="text-align: center;font-size:14px;color:black;font-weight: bold;"></div>
                <br>               
                </div>

              </form>
            </div>       	
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>         
          </div>

        </div>
      </div>
    </div>

<div id="fc_qr"  data-toggle="modal" data-target="#ModalQr"></div>
