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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>BOLETAS PARA CESANTES</b></h5>

    <div class="position-relative form-group">

    <div class="row verboletas">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <b style="color:#000;"> BUSCAR: </b> 
            <input class="form-control" type="text" id="txtbuscar" onkeypress="if(event.keyCode==13){ listarboletas(); }">
            <span class="btn btn-success" style="margin-top:10px;" onclick="listarboletas();">Buscar</span>
            <!--&nbsp;&nbsp;&nbsp;
            <span class="btn btn-info" style="margin-top:10px;" onclick="generarboletas();">Firmar</span>-->
            <!--&nbsp;&nbsp;&nbsp;
            <span class="btn btn-danger" style="margin-top:10px;" onclick="">Firmar en lote</span>-->
            <!--
            <span class="btn btn-danger" style="margin-top:10px;" onclick="popup_anadiraexp();">Adjuntar a expediente</span>
            <span class="btn btn-info" style="margin-top:10px;" onclick="descargarboletasfirmadas();">Descargar solo boletas firmadas</span>-->
        </div>          
    </div>

    <form id="formulario01" enctype="multipart/form-data" style="width:100%;" onsubmit="subirarchivoboleta();return false;">
        <div class="row subirarchivolist">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <b style="color:#000;">AÑO: </b> 
                <select name="anio" id="anio" class="form-control" onchange="listararchivosboleta();">
                <?php
                for ($i=date('Y'); $i>=2020; $i--) { 
                ?><option><?=$i?></option><?php
                }
                ?>
                </select>
            </div>       
        </div>

        <div class="row subirarchivolist">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <b style="color:#000;">MES: </b>            
                <input type="text" name="textmes" id="textmes" style="display:none;">
                <select name="mes" id="mes" class="form-control" onchange="listararchivosboleta();">
                    
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
                    <option value="">MOSTRAR TODO</option>
                </select>        
            </div>    
        </div>

        <div class="row subirarchivolist">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <b style="color:#000;">TIPO: </b> 
                <select name="tipo" id="tipo" class="form-control" onchange="listararchivosboleta();">
				    <option>CESANTE</option>
                    <option>SOBREVIVENCIA</option>
                    <option>CESANTE SUSPENDIDO</option>
                    <option>VIUDEZ</option>
                </select> 
            </div>          
        </div>       

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <br>
                <input type="radio" name="opcion" value="1" onclick="listarboletas();" checked> Boletas
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="opcion" value="2" onclick="listararchivosboleta();"> Subir archivo list
                <br><br>
            </div>
        </div>

    <div id="subir_archivo_list">

        <div  id="div_subirficha" class="row subirarchivolist">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <input name="archivo" type="file" class="form-control">   
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <button class="btn btn-success" id="subirarchivo">Subir Archivo</button>
            </div>
        </div>
        <br>
        
        <div class="row cargar"><img style="margin-left: auto;margin-right: auto;display: block;" src="./assets/images/load10.gif"></div>

        <div class="row subirarchivolist">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <b></b>
                <table class="table" id="t_archivo" style="color:#000;font-size:10px;width:100%;background-color:rgb(0,117,184);">
                    <thead>
                        <tr style="color:#fff;">
                            <td>N°</td>
                            <td>Año</td>
                            <td>Mes</td>
                            <td>Tipo</td>
                            <td>Listar boleta</td>
                            <td>Archivo</td>
                            <td>Eliminar</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="row verboletas">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <b></b>
                <table class="table" id="t_boletas" style="color:#000;font-size:10px;width:100%;background-color:rgb(0,117,184);">
                    <thead style="text-align:center;">
                        <tr style="color:#fff;">
                            <td>N°</td>
                            <td>Año</td>
                            <td>Mes</td>
                            <td>Tipo</td>
                            <!--<td><input type="checkbox" onclick="selectboxtodo(this);" ></td>-->
                            <td>Nombres y Apellidos</td>
                            <!--<td>Firmada</td>-->
                            <td>Boleta</td>
                            <!--<td>Firmar</td>-->
                            <!--<td>Fecha de Nacimiento</td>
                            <td>Documento de Identidad</td>
                            <td>Cargo</td>-->
                            <td>Tipo de Pensionista</td>
                            <td>Tipo de Pension</td>
                            <td>Total liquidez</td>
                            <!--
                            <td>Celular</td>
                            <td>Correo</td>
                            -->
                        </tr>
                    </thead>
                    <tbody style="text-align:center;"></tbody>
                </table>
            </div>
        </div>

    </div>
    @csrf
    </form>


    </div>
</div>
</div>

<form id="formInputhidden">
<input type="hidden" name="tipoFirma" id="tipoFirma" value="I" />
<input type="hidden" name="rutaEntrada" id="rutaEntrada" value="/home/siic01/public_html/cargados/" /> <!---->
<input type="hidden" name="rutaSalida" id="rutaSalida" value="/home/siic01/public_html/firmados/" /> <!---->
<input type="hidden" name="nombreArchivo" id="nombreArchivo" value="" /> <!---->
<input type="hidden" name="sistema" id="sistema" value="SIIC01" />
<input type="hidden" name="servidorFirma" id="servidorFirma" value="http://95.111.238.137:85" />
<input type="hidden" name="firmaHolografica" id="firmaHolografica" value="S" />
<input type="hidden" name="obtenerImagenHolograficaDesdeServidor" id="obtenerImagenHolograficaDesdeServidor" value="S" />
<input type="hidden" name="rutaImagenHolograficaServidor" id="rutaImagenHolograficaServidor" value="/home/siic01/public_html/assets/ImagenesFirma/check.jpg" />
<input type="hidden" name="motivo" id="motivo" value="" /> <!---->
<input type="hidden" name="pagina" id="pagina" value="P" />
<input type="hidden" name="posicionX" id="posicionX" value="10" />
<input type="hidden" name="posicionY" id="posicionY" value="5" />
<input type="hidden" name="adicionarQr" id="adicionarQr" value="S" />
<input type="hidden" name="respuesta" id="respuesta" value="" />
<input type="hidden" name="rolUsuario" id="rolUsuario" value="<?php if($session['idespecialista'] == 542){echo '1';}else{echo '0';} ?>" />
<input type="hidden" id="idArchivo" name="idArchivo"/> <!---->
</form>

<script type="text/javascript">
function firmarboleta(nro){
    var ruta   = g_boletas[nro]['arcBcl'].split('/');
    var nomarc = ruta.pop();
    $("#formInputhidden #rutaEntrada").val(ruta.join('/'));
    $("#formInputhidden #rutaSalida").val(ruta.join('/').replace('cargados','firmados'));
    $("#formInputhidden #nombreArchivo").val(nomarc);
    $("#formInputhidden #motivo").val('Doy V° B°');
    $("#formInputhidden #idArchivo").val(g_boletas[nro]['idBcl']);
    //$("#formInputhidden input").prop('type','text');
}

var gthis;
function selectboxtodo(athis){
    gthis=athis;
    if($(gthis).prop('checked')){
        $("input[name=box]").prop('checked',true);
    }else{
        $("input[name=box]").prop('checked',false);
    }
}

function subirarchivoboleta(id='formulario01'){
    var ifimagen = true;
    $("#textmes").val($("#mes option:selected").html());
    //$('#btn_enviar').prop('disabled',true);
    var inputarchivo = $("input[name=archivo]")[0].files;
      if(inputarchivo.length){
        if(inputarchivo[0].name.indexOf('.lis')>-1){
          //información del formulario
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('subirarchivoboleta')}}',  
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
                $("#subirarchivo").html('<img src="./assets/images/2.gif" width="25px"> Cargando...');
                $("#subirarchivo").prop('disabled',true);
              },
              //una vez finalizado correctamente
              success: function(data){
                  alert('Archivo cargado');
                  $("#subirarchivo").html('Subir Archivo');
                  $("#subirarchivo").prop('disabled',false);
                listararchivosboleta();
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
                $("#subirarchivo").html('Subir Archivo');
                $("#subirarchivo").prop('disabled',false);
              }
          });
        }else{
            alert('Selecione un archivo .lis');
        }
    }else{
      alert('Selecione un archivo .lis');
    }
}

var gdata=[];
function listararchivosboleta(){
    ajax_data = {
      "anoBca"   : $("#anio").val(),
      "mesBca"   : $("#mes option:selected").html(),
      "idmesBca" : $("#mes").val(),
      "tipBca"   : $("#tipo").val(),
      "alt"      : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('listararchivosboleta')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                          $(".cargar").css('display','');
                          $(".subirarchivolist").css('display','none');
                          $(".verboletas").css('display','none');
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                        table4.clear().draw();
                        if(data.length){
                            for (let i = 0; i < data.length; i++) {
                                data[i]['nro'] = i+1;
                                data[i]['t_rutBca'] = '<a target="_blank" href=".'+data[i]['rutBca']+'" class="btn btn-info">Archivo</a>';
                                data[i]['t_listar'] = '<span class="btn btn-success" onclick="listarboletas('+data[i]['idBca']+');">Listar boletas</span>';
                                data[i]['eliminar'] = '<span class="btn btn-danger" onclick="eliminar('+data[i]['idBca']+');">X</span>';                            
                            }                            
                            table4.rows.add(data).draw();
                            $(".subirarchivolist").css('display','');                            
                            $(".cargar").css('display','none');
                            $("#div_subirficha").css('display','none');
                        }else{
                            $(".subirarchivolist").css('display','');                            
                            $(".cargar").css('display','none');
                        }
                      }
              });
}

var g_idBca = 0;
var g_boletas = [];
function listarboletas(idBca=''){
    g_idBca = idBca;
    if(idBca) { $("#txtbuscar").val(''); }
    ajax_data = {
      "txtbuscar" : $("#txtbuscar").val(),
      "idBca"     : idBca,
      "alt"       : Math.random()
    }

    if($("#txtbuscar").val().length>3 || idBca){
    $.ajax({
                    type: "GET",
                    url: "{{route('listarboletas')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                        $(".cargar").css('display','');
                        $(".subirarchivolist").css('display','none');
                        $(".verboletas").css('display','none');
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                        var boletas = data['boletas'];
                        for (let i = 0; i < boletas.length; i++) {
                            var cdescagas = (boletas[i]['cantidad'])?' Descargas: '+boletas[i]['cantidad']:'';
                            boletas[i]['nro'] = i+1; //archivo   FirBcl
                            boletas[i]['t_box']        = '<input type="checkbox" name="box" value="'+boletas[i]['idBcl']+'">';
                            boletas[i]['t_nombres']    = '<b>'+boletas[i]['apeBcl']+' '+boletas[i]['nomBcl']+'</b>';
                            boletas[i]['t_boleta']     = (boletas[i]['arcBcl'])?'<a class="btn btn-success" style="padding:5px;" target="_blank" href="'+boletas[i]['arcBcl']+'">Ver boleta</a>'+cdescagas:'<a  class="btn btn-info" style="padding:5px;" target="_blank" href="pdf_boleta?idBcl='+boletas[i]['idBcl']+'">Previsualizar</a>'+cdescagas;
                            boletas[i]['t_firmar']     = (boletas[i]['arcBcl'] && boletas[i]['FirBcl']==0)?'<span class="btn btn-danger" onclick="firmarboleta('+i+');">Firmar</span>':'';
                            boletas[i]['t_firmada']    = ((boletas[i]['arcBcl'])?'<b style="color:green;">SI</b>':'<b style="color:red;">NO</b>');//boletas[i]['FirBcl']==1
                        }
                        g_boletas = boletas;
                        table5.clear().draw();
                        table5.rows.add(boletas).draw();
                        if(idBca) { $($('input[name=opcion]')[0]).prop('checked',true); }
                        $(".verboletas").css('display','');
                        $(".cargar").css('display','none');
                      }
              });
    }else{
        table5.clear().draw();
        $(".cargar").css('display','none');
        $(".subirarchivolist").css('display','none');
        $(".verboletas").css('display','');
    }
}

function generarboletas(){
    var lista = [];
    for (let i = 0; i < $("input[name=box]:checked").length; i++) {
        lista.push($("input[name=box]:checked")[i].value);        
    }
    ajax_data = {
      "idBcls" : lista.join(),
      "idespecialista" : <?=$session['idespecialista']?>,
      "alt"    : Math.random()
    }
    if(lista.length){
    if(true){
    $.ajax({
                    type: "GET",
                    url: "{{route('generarboletas')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                          //imagen de carga
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                        listarboletas(g_idBca);
                      }
              });
    }
    }else{
        alert('Selecione las boletas a firmar');
    }
}

function eliminar(idBca){
    ajax_data = {
      "idBca" : idBca,
      "alt"   : Math.random()
    }
    if(confirm('¿Esta seguro que desea eliminar este archivo?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminarboletas')}}",
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
                        listararchivosboleta();
                      }
              });
    }
}


    var table4 = $("#t_archivo").DataTable( {
        dom: 'Bfrtip',
        buttons: ['excel'],
        "iDisplayLength": 35,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        },
        data:[],
        "columns": [
            { "data": "nro" },
            { "data": "anoBca" },
            { "data": "mesBca" },
            { "data": "tipBca" },
            { "data": "t_listar" },
            { "data": "t_rutBca" },
            { "data": "eliminar" },
        ],                          
        rowCallback: function (row, data) {},
        filter: true,
        info: true,
        ordering: true,
        processing: true,
        retrieve: true                          
    });

    var table5 = $("#t_boletas").DataTable( {
        dom: 'Bfrtip',
        buttons: ['excel'],
        "iDisplayLength": 35,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        },
        data:[],
        "columns": [
            { "data": "nro" },
            { "data": "anoBca" },
            { "data": "mesBca" },
            { "data": "tipBca" },
            //{ "data": "t_box" },
            { "data": "t_nombres" },
            //{ "data": "t_firmada" },
            { "data": "t_boleta" },
            //{ "data": "t_firmar" },
            //{ "data": "fecBcl" },
            //{ "data": "docBcl" }, 
            //{ "data": "carBcl" },            
            { "data": "tipBcl" },
            { "data": "tipoPenBcl" },
            { "data": "tliquBcl" },
            //{ "data": "celular" },
            //{ "data": "correo" },
        ],                          
        rowCallback: function (row, data) {},
        filter: true,
        info: true,
        ordering: true,
        processing: true,
        retrieve: true                          
    }); 

listarboletas();
</script>

<script>

function descargarboletasfirmadas(){
    var lista = [];
    for (let i = 0; i < $("input[name=box]:checked").length; i++) {
        lista.push($("input[name=box]:checked")[i].value);        
    }
    ajax_data = {
      "idBcls" : lista.join(),
      "alt"    : Math.random()
    }
    if(lista.length){
    if(confirm('¿Esta seguro que desea descargar las boletas selecionadas?')){
        window.open('{{route('descargarboletasfirmadas')}}?alt='+Math.random()+'&idBcls='+lista.join(), '_blank');
    /*$.ajax({
                    type: "GET",
                    url: "{{route('descargarboletasfirmadas')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                          //imagen de carga
                          //$("#login").html("<p align='center'><img src='http://intranet.ugel01.gob.pe/prestamos/public/images/cargando.gif'/></p>");
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                        listarboletas(g_idBca);
                      }
              });*/
    }
    }else{
        alert('Selecione las boletas a descargar');
    }
}
</script>

@endsection
