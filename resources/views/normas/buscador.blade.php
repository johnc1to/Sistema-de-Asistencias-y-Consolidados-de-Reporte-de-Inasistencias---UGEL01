<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>REPOSITORIO NORMATIVO UGEL01</title>
        <link rel="icon" type="image/x-icon" href="assets/images/logonormasmin.png" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="{{asset('assets/scripts/jquery-3.5.1.min.js')}}"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleacpis.com/css?family=Saira+Extra+Condensed:500,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,800,800i" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="assets/css/normas.css" rel="stylesheet" />
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" id="sideNav">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">
                <span class="d-block d-lg-none">REPOSITORIO NORMATIVO UGEL01</span>
                <span class="d-none d-lg-block"><img class="img-fluid img-profile rounded-circle mx-auto mb-2" src="assets/images/logonormas.png" alt="..." /></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#about">Repositorio Normativo<br>UGEL01</a></li>
                    <li class="nav-item" style="color:#fff;padding:0px 18px 0px 18px;text-align:justify">Este repositorio le ayudará a buscar las resoluciones directorales y normas emitidas por la Ugel01, DRELM, Ministerio de educación, Ministerios de economía y finanzas, ministerio de salud y contraloría.</li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#education"  style="visibility:hidden;">.</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#skills"     style="visibility:hidden;">.</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#interests"  style="visibility:hidden;">.</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#awards"     style="visibility:hidden;">.</a></li>
                </ul>
            </div>
        </nav>
        <!-- Page Content-->
        <div class="container-fluid p-0">
            <!-- About-->
            <section class="resume-section" id="about">
                <div class="resume-section-content">
                    <h1 class="mb-0">
                        REPOSITORIO NORMATIVO
                        <span class="text-primary">UGEL01</span>
                    </h1>
                    <div class="subheading mb-5">
                        Resoluciones directorales y consultas normativas
                        <a href=""></a>
                    </div>
                    <p class="lead mb-5">
                        <div class="col-sm-12">
                            <a target="_blank" href="./storage/manuales/CIUDADANO-REPOSITORIONORMATIVO.pdf" class="btn btn-info" style="color:#fff;">Manual: Como buscar</a>
                        </div>
                        <div class="col-sm-12">
                            <b>Busqueda:</b> <input type="radio" name="busquedatipo" value="1" onclick="fbusquedatipo();" checked> Simple
                            &nbsp;&nbsp;
                            <input type="radio" name="busquedatipo" value="2" onclick="fbusquedatipo();" > Avanzada
                            
                            <div id="divbusqueda"><input type="text" id="txtbuscar" class="form-control" value="" onkeypress="if(event.keyCode==13){normasrepositorio();}" autofocus autocomplete="off"></div>
                        </div>

                        <div class="col-sm-12" style="text-align: center;">
                            <br><button class="btn btn-success" onclick="normasrepositorio();" id="btn_guardar">BUSCAR</button>
                            <button class="btn btn-danger" onclick="limpiar();">LIMPIAR</button>
                        </div>

                    </p>
                    <div id="resultados"></div>
                    <div class="row social-icons">
                        
                        <table style="width: 100%;text-align: center;margin-top:10px;">
                        <tr>
                        <?php
                        foreach ($tipos as $key) {
                        ?>
                        <td style="width:100px;"><div class="social-icon" style="margin:0px;background-color:rgb(189,93,56);">
                            <img src="assets/fonts/icoarchivo.svg" alt="<?=$key['desTip']?>"> 
                            </div><br><?=$key['desTip']?></td>
                        <?php
                        }
                        ?>
                        </tr>
                        </table>
                    </div>
                </div>
            </section>
            <hr class="m-0" />
            
        </div>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <!--<script src="assets/scripts/normas.js"></script>-->
    </body>
</html>

<script>
    function limpiar(){
        $("input[name=busquedatipo]:checked").click();
        $("#resultados").html('');
    }

    function fbusquedatipo(){
        if( $("input[name=busquedatipo]:checked").val()==1 ){
            $("#divbusqueda").html('<input type="text" id="txtbuscar" class="form-control" value="" autocomplete="off">');
        }else{
            var opt = '';
            opt += '<div class="row">';
            
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
            
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><b>Numero de documento:</b><input tyle="text" value="" id="nroFnn" class="form-control"></div>';
            
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
            opt += '<b>Año:</b><select class="form-control" id="fecFnn">';
            opt += '<option value="">Elija el año</option>';
                    <?php
                    for ($i=2008; $i<=date('Y'); $i++) {
                    ?>
                        opt += '<option value="<?=$i?>"><?=$i?></option>';
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
           
            opt += '</div>';

            opt += '<div class="row">';
            
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
            
            opt += '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><b>Palabras clave:</b><input tyle="text" value="" id="palClaFnn" class="form-control"></div>';
            opt += '</div>';

            $("#divbusqueda").html(opt);
        }
    }
</script>

<script>
    function normasrepositorio(){
    ajax_data = {
      "buscar"    : $("#txtbuscar").val(),
      "idTem"     : $("#idTem").val(),
      "AsuFnn"    : $("#AsuFnn").val(),
      "nroFnn"    : ($("#nroFnn").val())?Number($("#nroFnn").val()):'',
      "idTip"     : $("#idTip").val(),
      "tipo"      : $("#idTip option:selected").html(),
      "palClaFnn" : $("#palClaFnn").val(),
      "idSit"     : $("#idSit").val(),
      "fecFnn"    : $("#fecFnn").val(),
      "alt"       : Math.random()
    }
    var bbuscar = false;
    if( ajax_data['buscar'] ){ bbuscar = true; if(ajax_data['buscar'].split('-').length==2){ ajax_data['nroFnn'] = ajax_data['buscar'].split('-')[0]; ajax_data['fecFnn'] = ajax_data['buscar'].split('-')[1];  } }
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
                      $("#resultados").html('<div style="text-align:center;" ><img src="assets/images/load10.gif"></div>');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    var resultados = '';
                    if(data){
                    if(data.length){
                    resultados += '<table border="2" style="width: 100%;font-size:12px;text-align:center;">';
                    resultados += '    <thead>';
                    resultados += '        <tr style="background-color:rgb(189,93,56);">';
                    resultados += '            <td style="width:15px;color:#fff;"><b>N</b></td>';
                    resultados += '            <td style="width:45px;color:#fff;"><b>Tema</b></td>';
                    resultados += '            <td style="width:150px;color:#fff;"><b>Asunto</b></td>';
                    resultados += '            <td style="width:55px;color:#fff;"><b>Numero de documento</b></td>';
                    resultados += '            <td style="width:55px;color:#fff;"><b>Fecha</b></td>';
                    resultados += '            <td style="width:65px;color:#fff;"><b>Tipo</b></td>';
                    resultados += '            <td style="width:65px;color:#fff;"><b>Destinatario</b></td>';
                    resultados += '            <td style="width:55px;color:#fff;"><b>Archivo</b></td>';
                    resultados += '            <td style="width:55px;color:#fff;"><b>Vigente</b></td>';
                    resultados += '        </tr>';
                    resultados += '        </thead>';
                    resultados += '<tbody>';
                    
                    for (var i = 0; i < data.length; i++) {
                        var key = data[i];
                        resultados += '<tr>';
                        resultados += '<td>'+(i+1).toString()+'</td>';
                        resultados += '<td>'+isnull(key['desTem'])+'</td>';
                        resultados += '<td>'+isnull(key['AsuFnn'])+'</td>';
                        resultados += '<td>'+isnull(key['nroFnn'])+'</td>';
                        resultados += '<td>'+isnull(key['fecFnn'])+'</td>';
                        resultados += '<td>'+isnull(key['desTip'])+'</td>';
                        resultados += '<td>'+isnull(key['desFnn'])+'</td>';
                        resultados += '<td>'+((key['arcFnn'])?'<a style="font-size:12px;" target="_blank" class="btn btn btn-info" href="normalink?idFnn='+key['idFnn']+'&link='+plink(key['arcFnn'])+'">Descargar</a>':'')+'</td>';
                        resultados += '<td>'+isnull(key['desSit'])+'</td>';
                        resultados += '</tr>';
                    }
                    resultados += '</tbody>';
                    resultados += '</table>';
                    resultados += '<br>';
                    }
                    }
                    $("#resultados").html(resultados);

                  }
            });
    }else{
        alert('Debe escribir en el buscador');
    }
}
    <?php
    $ip_rd = ($ip=='200.123.16.107')?'10.2.1.62':'';
    ?>
    function plink(url){
        //Contingencia, si la extranet deja de funcionar o firewall
        //return url.replace('http://extranet.ugel01.gob.pe','http://<?=$ip_rd?>:8060');
        return url;
    }

    function consultaarchivo(){
    ajax_data = {
      "texto"   : $("#txtbuscar").val(),
      "alt"     : Math.random()
    }
    
            $.ajax({
                type: "GET",
                url: 'http://<?=$ip_rd?>/rd/consultaarchivo.php',
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      $(".elementos").css('display','none');
                      $("#cargando").css('display','');
                      $("#resultados").html('<div style="text-align:center;" ><img src="assets/images/load10.gif"></div>');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    var resultados = '';
                    if(data.length){
                    resultados += '<table border="2" style="width: 100%;font-size:12px;text-align:center;">';
                    resultados += '    <thead>';
                    resultados += '        <tr style="background-color:rgb(189,93,56);">';
                    resultados += '            <td style="width:15px;color:#fff;"><b>N</b></td>';
                    resultados += '            <td style="width:45px;color:#fff;"><b>Tema</b></td>';
                    resultados += '            <td style="width:150px;color:#fff;"><b>Asunto</b></td>';
                    resultados += '            <td style="width:55px;color:#fff;"><b>Numero de documento</b></td>';
                    resultados += '            <td style="width:55px;color:#fff;"><b>Fecha</b></td>';
                    resultados += '            <td style="width:65px;color:#fff;"><b>Tipo</b></td>';
                    resultados += '            <td style="width:65px;color:#fff;"><b>Destinatario</b></td>';
                    resultados += '            <td style="width:55px;color:#fff;"><b>Archivo</b></td>';
                    resultados += '            <td style="width:55px;color:#fff;"><b>Vigente</b></td>';
                    resultados += '        </tr>';
                    resultados += '        </thead>';
                    resultados += '<tbody>';
                    
                    for (var i = 0; i < data.length; i++) {
                        var key = data[i];
                        resultados += '<tr>';
                        resultados += '<td>'+(i+1).toString()+'</td>';
                        resultados += '<td>'+isnull(key['desTem'])+'</td>';
                        resultados += '<td>'+isnull(key['AsuFnn'])+'</td>';
                        resultados += '<td>'+isnull(key['nroFnn'])+'</td>';
                        resultados += '<td>'+isnull(key['fecFnn'])+'</td>';
                        resultados += '<td>'+isnull(key['desTip'])+'</td>';
                        resultados += '<td>'+isnull(key['desFnn'])+'</td>';
                        resultados += '<td>'+((key['arcFnn'])?'<a style="font-size:12px;" target="_blank" class="btn btn btn-info" href="normalink?idFnn='+key['idFnn']+'&link='+plink(key['arcFnn'])+'">Descargar</a>':'')+'</td>';
                        resultados += '<td>'+isnull(key['desSit'])+'</td>';
                        resultados += '</tr>';
                    }
                    resultados += '</tbody>';
                    resultados += '</table>';
                    resultados += '<br>';
                    }

                    $("#resultados").html(resultados);

                  }
            });
    
}

function isnull(texto){
    return (texto)?texto:'';
}
</script>


