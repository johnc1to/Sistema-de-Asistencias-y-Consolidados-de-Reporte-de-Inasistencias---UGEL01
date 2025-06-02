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

<div class="main-card mb-12 card" id="div_programas">
  <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>FICHAS - MONITOREO</b></h5>
      <div class="position-relative form-group">        
        
        <?php
        if($anadirficha){
        ?><div class="col-sm-12" style="color:#000;"><?php
        foreach ($anadirficha as $key) {
        $key = (Array)$key;
        ?><button class="btn btn-danger" onclick="anadirficha(<?=$key['idFic']?>);"><?=$key['nomFic']?></button><?php
        }
        ?></div><br><?php
        }
        ?>
        <div class="row">
        <div class="col-sm-1" style="color:#000;text-align:right;"><b>AÑO:</b></div>
        <div class="col-sm-5" style="color:#000;">
            <select name="anio" id="anio" class="form-control" onchange="ver_ficha_ie();">
                <?php
                for ($i=date('Y'); $i>=2021; $i--) { 
                ?> <option><?=$i?></option> <?php
                }
                ?>
            </select>
        </div>
        </div>
        
        <div class="col-sm-12" style="color:#000;">
          <div class="table-responsive" id="div_instituciones" style="">
            <table class="display table table-bordered table-striped table-dark" id="t_ficha" style="color:#000;font-size:10px;width:100%;">
              <thead>
                <tr style="background-color:rgb(0,117,184);">
                    <td style="width:15px;color:#fff;"><b>N</b></td>
                    <td style="width:45px;color:#fff;"><b>Nombre</b></td>
                    <td style="width:55px;color:#fff;"><b>Descripción</b></td>
                    <td style="width:55px;color:#fff;"><b>Fecha inicio</b></td>
                    <td style="width:55px;color:#fff;"><b>Fecha fin</b></td>
                    <td style="width:55px;color:#fff;"><b>Modalidad</b></td>
                    <td style="width:55px;color:#fff;"><b>Estado</b></td>
                    <td style="width:55px;color:#fff;"><b>Acceder a la ficha</b></td>
                    <td style="width:55px;color:#fff;"><b>Ver PDF</b></td>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
      </div>
      </div>
  </div>
</div>

<script type="text/javascript">
var gthis;
function anadirficha(idFic){
  $("#popup01 .modal-content").load('{{route('popup_anadirfichadirector')}}?idFic='+idFic);
  $("#fc_popup").click();
}

var table4 = $("#t_ficha").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "nomFic" },
                            { "data": "desFic" },
                            { "data": "t_inicio" },
                            { "data": "tt_fin" },
                            { "data": "modFic" },
                            { "data": "t_flg" },
                            { "data": "acceso_ficha" },
                            { "data": "ver_pdf" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });


function ver_ficha_ie(){
    
    ajax_data = {
      "anio" : $("#anio").val(),
      "alt"  : Math.random()
    }
    $.ajax({
        type: "GET",
        url: "{{route('ver_ficha_ie')}}",
        data: ajax_data,
        dataType: "json",
        beforeSend: function(){
              $("#t_ficha tbody").html('<tr><td colspan="12"><center><img src="./assets/images/load10.gif"></center></td></tr>');
        },
        error: function(){
              alert("error peticiÃ³n ajax");
        },
        success: function(data){
            
            table4.clear().draw();
            var lista = data['lista'];
            if(lista){
                for (var i = 0; i < lista.length; i++) {
                    lista[i]['nro']          = i+1;
                    lista[i]['acceso_ficha'] = (lista[i]['habilitado']=='1')?'<span id="'+((lista[i]['culRec']=='1')?'':'reg'+lista[i]['idFic'])+'" style="font-size:15px;padding:2px;" class="btn btn-success" onclick="mostrar_ficha('+lista[i]['idFic']+','+lista[i]['idRec']+');">Registrar</span>':'CERRADO';
                    lista[i]['ver_pdf']      = (lista[i]['culRec']=='1')?'<a style="font-size:15px;padding:2px;" class="btn btn-success" target="_blank" href="mostrar_pdf_ficha?idficha='+lista[i]['idFic']+'&idreceptor='+lista[i]['idRec']+'">VerPDF</a>':'';
                    lista[i]['t_flg']        = (lista[i]['habilitado']=='1')?((lista[i]['culRec']=='1')?'COMPLETADO':'PENDIENTE'):'CERRADO';
                    lista[i]['tt_fin']       = '<b>'+lista[i]['t_fin']+'</b>';
                    
                }
            table4.rows.add(lista).draw();
            $("#t_ficha tr td").css({'padding-top':'3px','padding-bottom':'2px','padding-right':'3px','padding-left':'3px','font-weight':'bolder'});
            
                //Estilo
                for (var i = 0; i < lista.length; i++) {
                    if(lista[i]['priFic']=='1'){ $($("#t_ficha tbody tr")[i]).css({'color':'red','font-size':'12px'}); }
                }
            }
            $("#reg72").click();
          }
          
    });
}

ver_ficha_ie();

function mostrar_ficha(idficha,idRec){
    ajax_data = {
      "idficha"  : idficha,
      "idRec"    : idRec,
      "codlocal" : '<?=$session['codlocal']?>',
      "alt"      : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('mostrar_ficha')}}",
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
                            $("#Modalficha .modal-content").html(data);
                            $("#fc_ficha").click();
                      }
              });
}
</script>

@endsection

<div id="Modalficha" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="width:90%;">
    <div class="modal-content"></div>
  </div>
</div>
<div id="fc_ficha"  data-toggle="modal" data-target="#Modalficha"></div>

