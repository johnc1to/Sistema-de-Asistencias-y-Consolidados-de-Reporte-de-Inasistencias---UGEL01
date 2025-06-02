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

<script src="https://bossanova.uk/jexcel/v3/jexcel.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jexcel/v3/jexcel.css" type="text/css" />
<script src="https://jsuites.net/v3/jsuites.js"></script>
<link rel="stylesheet" href="https://jsuites.net/v3/jsuites.css" type="text/css" />

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REPOSITORIO NORMATIVO DE LA UGEL01</b></h5>
        <div class="position-relative form-group">
            <form onsubmit="return false;">
            <div class="col-xs-12" style="color:#000;font-weight:bolder;"></div>
            
            <div class="col-xs-12" style="color:#000;font-size:10px;padding-left:0px;">
                <input type="radio" name="selector" value="1" onclick="verrepositorio();" checked> <b style="color:#000;font-size:16px;">Repositorio</b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="selector" value="2" onclick="verentidades();"> <b style="color:#000;font-size:16px;">Añadir Ente rector</b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="selector" value="2" onclick="vertemas();"> <b style="color:#000;font-size:16px;">Añadir Tema</b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="selector" value="2" onclick="vertipos();"> <b style="color:#000;font-size:16px;">Añadir Tipo</b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="selector" value="2" onclick="versituacion();"> <b style="color:#000;font-size:16px;">Añadir Situación</b>
                <!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="selector" value="2" onclick="verimportar();"> <b style="color:#000;font-size:16px;">Importar</b>-->
                
            </div>
            
            <div class="col-xs-12" style="color:#000;font-size:10px;padding-left:0px;" id="botones"></div>
            
            <div class="col-sm-12" id="recomendaciones" style="color:#000;"><br></div>            
            <!--
            <div class="col-sm-12" style="color:#000;"><u><b>RECOMENDACIONES:</b></u></div>
            <div class="col-sm-12" style="color:#000;"><b>(*) Para añadir un nuevo registro debe ir a la ultima fila y presionar enter</b></div>
            <div class="col-sm-12" style="color:#000;"><b>(*) Para eliminar un registro debe cambiar la columna estado a ELIMINAR</b></div>
            <div class="col-sm-12" style="color:#000;"><br></div>
            -->
            
            <div class="col-sm-12 elementos table-responsive" style="color:#000;font-size:10px;padding-left:0px;" id="repositorio">
                <table class="display table table-bordered table-striped table-dark" id="t_repositorio" style="color:#000;font-size:9px;width:100%;">
                    <thead>
                      <tr style="background-color:rgb(0,117,184);">
                          <td style="width:15px;color:#fff;"><b>N</b></td>
                          <td style="width:45px;color:#fff;"><b>Ente rector</b></td>
                          <td style="width:45px;color:#fff;"><b>Tema</b></td>
                          <td style="width:300px;color:#fff;"><b>Asunto</b></td>
                          <td style="width:55px;color:#fff;"><b>Numero de documento</b></td>
                          <td style="width:55px;color:#fff;"><b>Fecha</b></td>
                          <td style="width:65px;color:#fff;"><b>Tipo</b></td>
                          <td style="width:65px;color:#fff;"><b>Destinatario</b></td>
                          <td style="width:55px;color:#fff;"><b>Archivo</b></td>
                          <td style="width:55px;color:#fff;"><b>Vigente</b></td>
                          <td style="width:55px;color:#fff;"><b>Editar</b></td>
                          <td style="width:55px;color:#fff;"><b>Eliminar</b></td>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
            </div>
            <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="entidades"></div>
            <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="temas"></div>
            <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="tipos"></div>
            <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="situacion"></div>
            <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="importar"></div>
            <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="cargando"><img style="text-align:center;" src="assets/images/load10.gif"></div>
            @csrf
        </form>
        </div>
    </div>
</div>

<script>
    var g_entidades = [];
    function verentidades(){
    ajax_data = {
      "alt"   : Math.random()
    }
            $.ajax({
                type: "GET",
                url: '{{route('normasentidades')}}',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      $(".elementos").css('display','none');
                      $("#cargando").css('display','');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    $(".elementos").css('display','none');
                    $("#entidades").css('display','');
                    $("#botones").html('<button class="btn btn-success" onclick="guardarentidades();" id="btn_guardar">GUARDAR</button>');
                    $("#recomendaciones").html(recomendaciones);
                     reporte = [];
                        if(data.length){
                            for (var i = 0; i < data.length; i++) {
                            	var key = [];
                            	key.push(data[i]['idEnt']);
                            	key.push((data[i]['estEnt']==1)?'REGISTRADO':'');
                            	key.push(data[i]['desEnt']);
                                key.push(data[i]['cantidad']);
                            	reporte.push(key);
                            };
                        }else{
                            reporte = [['','','']];
                        }
                        //-------jexcel-------------------
                            g_entidades = [];
                            $("#entidades").html('');
                            g_entidades = jexcel(document.getElementById('entidades'), {
                            data:reporte,
                            columns: [
                                {
                                    type: 'text',
                                    title:'Ent',
                                    width:1
                                },
                                {
                                    type: 'dropdown',
                                    title:'Estado',
                                    width:90,
                                    source:[
                                        "REGISTRADO",
                                        "ELIMINAR",
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'Ente rector',
                                    width:500
                                },
                                {
                                    type: 'text',
                                    readOnly:true,
                                    title:'Cantidad',
                                    width:60
                                },
                             ]
                        });
                        $(".readonly").css('color','#000');
                        //-------jexcel-------------------
                  }
            });
}

function guardarentidades(){
    var datos = [];
    var mdata = g_entidades.getData();
    for (var i = 0; i < mdata.length; i++) {
            datos.push(mdata[i].join('||'));
    };
    ajax_data = {
      "datos"       : datos.join('&&'),
      "_token"      : $("input[name=_token]").val(),
      "alt"         : Math.random()
    }
        $.ajax({
            type: "POST",
            url: '{{route('guardarnormasentidades')}}',
            data: ajax_data,
            dataType: "html",
            beforeSend: function(){
                  $("#btn_guardar").prop('disabled',true);
            },
            error: function(){
                  alert("error peticiÃ³n ajax");
            },
            success: function(data){
                    $("#btn_guardar").prop('disabled',false);
                    verentidades();
                    alert('Datos Guardados');
              }
        });
}
</script>

<script>
var recomendaciones = "<br><b>RECOMENDACIONES:</b><br><b>(*) Para añadir un nuevo registro debe ir a la ultima fila y presionar enter</b><br><b>(*) Para eliminar un registro debe cambiar la columna estado a ELIMINAR</b>";
var g_temas = [];
    function vertemas(){
    ajax_data = {
      "alt"   : Math.random()
    }
            $.ajax({
                type: "GET",
                url: '{{route('normastemas')}}',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      $(".elementos").css('display','none');
                      $("#cargando").css('display','');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    $(".elementos").css('display','none');
                    $("#temas").css('display','');
                    $("#botones").html('<button class="btn btn-success" onclick="guardartemas();" id="btn_guardar">GUARDAR</button>');
                    $("#recomendaciones").html(recomendaciones);
                     reporte = [];
                        if(data.length){
                            for (var i = 0; i < data.length; i++) {
                            	var key = [];
                            	key.push(data[i]['idTem']);
                            	key.push((data[i]['estTem']==1)?'REGISTRADO':'');
                            	key.push(data[i]['desTem']);
                                key.push(data[i]['cantidad']);
                            	reporte.push(key);
                            };
                        }else{
                            reporte = [['','','']];
                        }
                        //-------jexcel-------------------
                            g_temas = [];
                            $("#temas").html('');
                            g_temas = jexcel(document.getElementById('temas'), {
                            data:reporte,
                            columns: [
                                {
                                    type: 'text',
                                    title:'idTem',
                                    width:1
                                },
                                {
                                    type: 'dropdown',
                                    title:'Estado',
                                    width:90,
                                    source:[
                                        "REGISTRADO",
                                        "ELIMINAR",
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'Tema',
                                    width:500
                                },
                                {
                                    type: 'text',
                                    readOnly:true,
                                    title:'Cantidad',
                                    width:60
                                },
                             ]
                        });
                        $(".readonly").css('color','#000');
                        //-------jexcel-------------------
                  }
            });
}

function guardartemas(){
    var datos = [];
    var mdata = g_temas.getData();
    for (var i = 0; i < mdata.length; i++) {
            datos.push(mdata[i].join('||'));
    };
    ajax_data = {
      "datos"       : datos.join('&&'),
      "_token"      : $("input[name=_token]").val(),
      "alt"         : Math.random()
    }
        $.ajax({
            type: "POST",
            url: '{{route('guardarnormastemas')}}',
            data: ajax_data,
            dataType: "html",
            beforeSend: function(){
                  $("#btn_guardar").prop('disabled',true);
            },
            error: function(){
                  alert("error peticiÃ³n ajax");
            },
            success: function(data){
                    $("#btn_guardar").prop('disabled',false);
                    vertemas();
                    alert('Datos Guardados');
              }
        });
}
</script>

<script>
    var g_tipos = [];
    function vertipos(){
    ajax_data = {
      "alt"   : Math.random()
    }
            $.ajax({
                type: "GET",
                url: '{{route('normastipos')}}',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      $(".elementos").css('display','none');
                      $("#cargando").css('display','');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    $(".elementos").css('display','none');
                    $("#tipos").css('display','');
                    $("#botones").html('<button class="btn btn-success" onclick="guardartipos();" id="btn_guardar">GUARDAR</button>');
                    $("#recomendaciones").html(recomendaciones);
                     reporte = [];
                        if(data.length){
                            for (var i = 0; i < data.length; i++) {
                            	var key = [];
                            	key.push(data[i]['idTip']);
                            	key.push((data[i]['estTip']==1)?'REGISTRADO':'');
                            	key.push(data[i]['desTip']);
                                key.push(data[i]['cantidad']);
                            	reporte.push(key);
                            };
                        }else{
                            reporte = [['','','']];
                        }
                        //-------jexcel-------------------
                            g_tipos = [];
                            $("#tipos").html('');
                            g_tipos = jexcel(document.getElementById('tipos'), {
                            data:reporte,
                            columns: [
                                {
                                    type: 'text',
                                    title:'idTip',
                                    width:1
                                },
                                {
                                    type: 'dropdown',
                                    title:'Estado',
                                    width:90,
                                    source:[
                                        "REGISTRADO",
                                        "ELIMINAR",
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'Tipos',
                                    width:500
                                },
                                {
                                    type: 'text',
                                    readOnly:true,
                                    title:'Cantidad',
                                    width:60
                                },
                             ]
                        });
                        $(".readonly").css('color','#000');
                        //-------jexcel-------------------
                  }
            });
}

function guardartipos(){
    var datos = [];
    var mdata = g_tipos.getData();
    for (var i = 0; i < mdata.length; i++) {
            datos.push(mdata[i].join('||'));
    };
    ajax_data = {
      "datos"       : datos.join('&&'),
      "_token"      : $("input[name=_token]").val(),
      "alt"         : Math.random()
    }
        $.ajax({
            type: "POST",
            url: '{{route('guardarnormastipos')}}',
            data: ajax_data,
            dataType: "html",
            beforeSend: function(){
                  $("#btn_guardar").prop('disabled',true);
            },
            error: function(){
                  alert("error peticiÃ³n ajax");
            },
            success: function(data){
                    $("#btn_guardar").prop('disabled',false);
                    vertipos();
                    alert('Datos Guardados');
              }
        });
}
</script>

<script>
    var g_situacion = [];
    function versituacion(){
    ajax_data = {
      "alt"   : Math.random()
    }
            $.ajax({
                type: "GET",
                url: '{{route('normassituacion')}}',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      $(".elementos").css('display','none');
                      $("#cargando").css('display','');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    $(".elementos").css('display','none');
                    $("#situacion").css('display','');
                    $("#botones").html('<button class="btn btn-success" onclick="guardarsituacion();" id="btn_guardar">GUARDAR</button>');
                    $("#recomendaciones").html(recomendaciones);
                     reporte = [];
                        if(data.length){
                            for (var i = 0; i < data.length; i++) {
                            	var key = [];
                            	key.push(data[i]['idSit']);
                            	key.push((data[i]['estSit']==1)?'REGISTRADO':'');
                            	key.push(data[i]['desSit']);
                                key.push(data[i]['cantidad']);
                            	reporte.push(key);
                            };
                        }else{
                            reporte = [['','','']];
                        }
                        //-------jexcel-------------------
                            g_situacion = [];
                            $("#situacion").html('');
                            g_situacion = jexcel(document.getElementById('situacion'), {
                            data:reporte,
                            columns: [
                                {
                                    type: 'text',
                                    title:'idSit',
                                    width:1
                                },
                                {
                                    type: 'dropdown',
                                    title:'Estado',
                                    width:90,
                                    source:[
                                        "REGISTRADO",
                                        "ELIMINAR",
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'Situacion',
                                    width:500
                                },
                                {
                                    type: 'text',
                                    readOnly:true,
                                    title:'Cantidad',
                                    width:60
                                },
                             ]
                        });
                        $(".readonly").css('color','#000');
                        //-------jexcel-------------------
                  }
            });
}

function guardarsituacion(){
    var datos = [];
    var mdata = g_situacion.getData();
    for (var i = 0; i < mdata.length; i++) {
            datos.push(mdata[i].join('||'));
    };
    ajax_data = {
      "datos"       : datos.join('&&'),
      "_token"      : $("input[name=_token]").val(),
      "alt"         : Math.random()
    }
        $.ajax({
            type: "POST",
            url: '{{route('guardarnormassituacion')}}',
            data: ajax_data,
            dataType: "html",
            beforeSend: function(){
                  $("#btn_guardar").prop('disabled',true);
            },
            error: function(){
                  alert("error peticiÃ³n ajax");
            },
            success: function(data){
                    $("#btn_guardar").prop('disabled',false);
                    versituacion();
                    alert('Datos Guardados');
              }
        });
}
</script>

<script>

var g_temas = [];

    function verrepositorio(){
        $(".elementos").css('display','none');
        $("#repositorio").css('display','');
        var opt = '';
        opt += '';
        opt += '<div class="col-sm-12">';
        opt += '<br><b style="font-size:16px;">Buscar:</b>';
        opt += ' <input type="radio" name="busquedatipo" value="1" onclick="fbusquedatipo();" checked> Simple';
        opt += '&nbsp;&nbsp;';
        opt += ' <input type="radio" name="busquedatipo" value="2" onclick="fbusquedatipo();" > Avanzada';
        opt += '<div id="divbusqueda"><input type="text" id="txtbuscar" class="form-control" value=""></div><br>';
        opt += '</div>';
        opt += '<div class="col-sm-12">';
        opt += '<button class="btn btn-success" onclick="normasrepositorio();" id="btn_guardar">BUSCAR</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        opt += '<button class="btn btn-danger" onclick="anadirnorma();" id="btn_anadir">AÑADIR</button>';
        opt += '</div>';
        $("#botones").html(opt);
        $("#recomendaciones").html("<br>");
    }

    function fbusquedatipo(){
        if( $("input[name=busquedatipo]:checked").val()==1 ){
            $("#divbusqueda").html('<input type="text" id="txtbuscar" class="form-control" value="">');
        }else{
            var opt = '';
            opt += '<div class="row">';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Entidad rector:</b><select class="form-control" id="idEnt">';
            opt += '<option value="">Elija la entidad rector</option>';
                    <?php
                    foreach ($entidades as $key) {
                    ?>
                    opt += '<option value="<?=$key['idEnt']?>"><?=$key['desEnt']?></option>';
                    <?php
                    }
                    ?>
            opt += '</select>';
            opt += '</div>';

            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Situación:</b><select class="form-control" id="idSit">';
            opt += '<option value="">Elija la situación</option>';
                    <?php
                    foreach ($situacion as $key) {
                    ?>
                    opt += '<option value="<?=$key['idSit']?>"><?=$key['desSit']?></option>';
                    <?php
                    }
                    ?>
            opt += '</select>';
            opt += '</div>';
            opt += '</div>';

            opt += '<div class="row">';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Tema:</b><select class="form-control" id="idTem">';
            opt += '<option value="">Elija el tema</option>';
                    <?php
                    foreach ($temas as $key) {
                    ?>
                    opt += '<option value="<?=$key['idTem']?>"><?=$key['desTem']?></option>';
                    <?php
                    }
                    ?>
            opt += '</select>';
            opt += '</div>';

            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><b>Asunto:</b><input tyle="text" value="" id="AsuFnn" class="form-control"></div>';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><b>Numero de documento:</b><input tyle="text" value="" id="nroFnn" class="form-control"></div>';
            opt += '</div>';

            opt += '<div class="row">';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Año:</b><select class="form-control" id="fecFnn">';
            opt += '<option value="">Elija el año</option>';
                    <?php
                    foreach ($anios as $key) {
                    ?>
                    opt += '<option value="<?=$key['anio']?>"><?=$key['anio']?></option>';
                    <?php
                    }
                    ?>
            opt += '</select>';
            opt += '</div>';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Tipo:</b><select class="form-control" id="idTip">';
            opt += '<option value="">Elija el tipo</option>';
                    <?php
                    foreach ($tipos as $key) {
                    ?>
                    opt += '<option value="<?=$key['idTip']?>"><?=$key['desTip']?></option>';
                    <?php
                    }
                    ?>  
            opt += '</select>';
            opt += '</div>';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><b>Palabras clave:</b><input tyle="text" value="" id="palClaFnn" class="form-control"></div>';
            opt += '</div>';

            $("#divbusqueda").html(opt);
        }
    }

    function anadirnorma(){
        $("#popup01 .modal-content").load("{{route('popup_anadirnorma')}}");
        $("#fc_popup").click();
    }

    function normasrepositorio(){
    ajax_data = {
      "buscar"    : $("#txtbuscar").val(),
      "idEnt"     : $("#idEnt").val(),
      "idTem"     : $("#idTem").val(),
      "AsuFnn"    : $("#AsuFnn").val(),
      "nroFnn"    : $("#nroFnn").val(),
      "idTip"     : $("#idTip").val(),
      "palClaFnn" : $("#palClaFnn").val(),
      "idSit"     : $("#idSit").val(),
      "fecFnn"    : $("#fecFnn").val(),
      "alt"       : Math.random()
    }
    
    var bbuscar = false;
    if( ajax_data['buscar'] ){ bbuscar = true; }
    if( ajax_data['idEnt'] || ajax_data['idTem'] || ajax_data['AsuFnn'] || ajax_data['nroFnn'] || ajax_data['idTip'] || ajax_data['palClaFnn'] || ajax_data['idSit'] || ajax_data['fecFnn'] ){ bbuscar = true; }
    
    if(bbuscar){
            $.ajax({
                type: "GET",
                url: '{{route('normasrepositorio')}}',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      $(".elementos").css('display','none');
                      $("#cargando").css('display','');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    $("#cargando").css('display','none');
                    $("#repositorio").css('display','');
                    if(data){
                    for (var i = 0; i < data.length; i++) {
                        data[i]['nro'] = i+1;
                        data[i]['t_arcFnn']   = (data[i]['arcFnn'])?'<a target="_blank" style="padding:2px;" class="btn btn btn-info" href="'+((data[i]['arcLinFnn']==1)?'.'+data[i]['arcFnn']:data[i]['arcFnn'])+'">Descargar</a>'+((data[i]['cantidad'])?'<br><b>Descargas: '+data[i]['cantidad']+'</b>':''):'';
                        data[i]['t_editar']   = '<span onclick="editarnorma('+data[i]['idFnn']+');"   style="padding:3px 6px 3px 6px;" class="btn btn-success">O</span>';
                        data[i]['t_eliminar'] = '<span onclick="eliminarnorma('+data[i]['idFnn']+');" style="padding:3px 6px 3px 6px;" class="btn btn-danger">X</span>';
                        //data[i]['t_cantidad'] = '<span style="-moz-border-radius: 25px;-webkit-border-radius: 25px;padding: 10px;color:#fff;" class="bg-info">'+data[i]['cantidad']+'</span>';
                        
                    }
                    }
                    table4.clear().draw();
                    table4.rows.add(data).draw();
                    $("#t_repositorio tr td").css({'padding':'0px','text-align':'center'});

                  }
            });
    }else{
        alert('Debe escribir en el buscador');
    }
}

function editarnorma(idFnn){
    $("#popup01 .modal-content").load("{{route('popup_anadirnorma')}}?idFnn="+idFnn);
    $("#fc_popup").click();
}

function eliminarnorma(idFnn){
    ajax_data = {
      "idFnn" : idFnn,
      "alt"   : Math.random()
    }
            $.ajax({
                type: "GET",
                url: '{{route('eliminarnorma')}}',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    normasrepositorio();
                    alert('Norma eliminada');
                  }
            });      
}

        var table4 = $("#t_repositorio").DataTable( {
                            dom: 'Bfrtip',
                            buttons: ['excel'],
                            "iDisplayLength": 35,
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                            },
                            data:[],
                            "columns": [
                                { "data": "nro" },
                                { "data": "desEnt" },
                                { "data": "desTem" },
                                { "data": "AsuFnn" },
                                { "data": "nroFnn" },
                                { "data": "fecFnn" },
                                { "data": "desTip" },
                                { "data": "desFnn" },
                                { "data": "t_arcFnn" },
                                { "data": "desSit" },
                                { "data": "t_editar" },
                                { "data": "t_eliminar" },
                            ],                          
                            rowCallback: function (row, data) {},
                            filter: true,
                            info: true,
                            ordering: true,
                            processing: true,
                            retrieve: true                          
                        });
        
$("input[name=selector]:checked").click();
</script>


<script>

function verimportar(){
            var opt = '';
            opt += '<div class="row">';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Tema:</b><select class="form-control" id="idTem">';
            opt += '<option value="">Elija el tema</option>';
                    <?php
                    foreach ($temas as $key) {
                    ?>
                    opt += '<option value="<?=$key['idTem']?>"><?=$key['desTem']?></option>';
                    <?php
                    }
                    ?>
            opt += '</select>';
            opt += '</div>';

            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><b>Asunto:</b><input tyle="text" value="" id="AsuFnn" class="form-control"></div>';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><b>Numero de documento:</b><input tyle="text" value="" id="nroFnn" class="form-control"></div>';
            opt += '</div>';

            opt += '<div class="row">';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Año:</b><select class="form-control" id="fecFnn">';
            opt += '<option value="">Elija el año</option>';
                    <?php
                    foreach ($anios as $key) {
                    ?>
                    opt += '<option value="<?=$key['anio']?>"><?=$key['anio']?></option>';
                    <?php
                    }
                    ?>
            opt += '</select>';
            opt += '</div>';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Tipo:</b><select class="form-control" id="idTip">';
            opt += '<option value="">Elija el tema</option>';
                    <?php
                    foreach ($tipos as $key) {
                    ?>
                    opt += '<option value="<?=$key['idTip']?>"><?=$key['desTip']?></option>';
                    <?php
                    }
                    ?>  
            opt += '</select>';
            opt += '</div>';
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><b>Palabras clave:</b><input tyle="text" value="" id="palClaFnn" class="form-control"></div>';
            opt += '</div>';
            
            opt += '<br><button class="btn btn-success" onclick="excelnormasrepositorio();" id="">BUSCAR</button>';
            opt += '&nbsp;&nbsp;&nbsp;';
            opt += '<button class="btn btn-danger" onclick="importarnormas();" id="btn_guardar">GUARDAR</button>';

            $("#botones").html(opt);
            $(".elementos").css('display','none');
            $("#importar").css('display','');
            
    }

    var g_excelnormas = [];
    function excelnormasrepositorio(){
    ajax_data = {
      "buscar"    : $("#txtbuscar").val(),
      "idTem"     : $("#idTem").val(),
      "AsuFnn"    : $("#AsuFnn").val(),
      "nroFnn"    : $("#nroFnn").val(),
      "idTip"     : $("#idTip").val(),
      "palClaFnn" : $("#palClaFnn").val(),
      "idSit"     : $("#idSit").val(),
      "fecFnn"    : $("#fecFnn").val(),
      "alt"       : Math.random()
    }
            $.ajax({
                type: "GET",
                url: '{{route('normasrepositorio')}}',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      $(".elementos").css('display','none');
                      $("#cargando").css('display','');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    $(".elementos").css('display','none');
                    $("#situacion").css('display','');
                    $("#recomendaciones").html(recomendaciones);
                     reporte = [];
                        if(data.length){
                            for (var i = 0; i < data.length; i++) {
                            	var key = [];
                            	key.push(data[i]['idFnn']);
                            	key.push((data[i]['estFnn']==1)?'REGISTRADO':'');
                            	key.push(data[i]['AsuFnn']);
                                key.push(data[i]['nroFnn']);
                                key.push(data[i]['fecFnn']);
                                key.push(data[i]['palClaFnn']);
                            	reporte.push(key);
                            };
                        }else{
                            reporte = [['','','']];
                        }
                        //-------jexcel-------------------
                            g_excelnormas = [];
                            $("#situacion").html('');
                            g_excelnormas = jexcel(document.getElementById('situacion'), {
                            data:reporte,
                            columns: [
                                {
                                    type: 'text',
                                    title:'idFnn',
                                    width:100
                                },
                                {
                                    type: 'dropdown',
                                    title:'Estado',
                                    width:90,
                                    source:[
                                        "REGISTRADO",
                                        "ELIMINAR",
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'AsuFnn',
                                    width:200
                                },
                                {
                                    type: 'text',
                                    title:'nroFnn',
                                    width:200
                                },
                                {
                                    type: 'text',
                                    title:'fecFnn',
                                    width:200
                                },
                                {
                                    type: 'text',
                                    title:'palClaFnn',
                                    width:200
                                },
                             ]
                        });
                        //-------jexcel-------------------
                  }
            });
    }
</script>

@endsection