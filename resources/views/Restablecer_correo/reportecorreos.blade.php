@extends('layout_especialista/cuerpo')
@section('html')

<!---------JEXCEL--------------------->
<script src="https://bossanova.uk/jspreadsheet/v3/jexcel.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v3/jexcel.css" type="text/css" />

<script src="https://jsuites.net/v3/jsuites.js"></script>
<link rel="stylesheet" href="https://jsuites.net/v3/jsuites.css" type="text/css" />
<!---------JEXCEL--------------------->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>Control de Requerimiento de restablecimiento o cambio de claves de correos institucionales <br>para los directores de las IIEE</b></h5>
        <div class="position-relative form-group">
           
            
            <div class="row" style="color:#000;">
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12">
                    <input type="radio" name="box" value="SOLICITADO" onclick="versolicitudesesp();" checked> Solicitados
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="ATENDIDO" onclick="versolicitudesesp();"> Atendidos
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="OBSERVADO" onclick="versolicitudesesp();"> Observado
                    &nbsp;&nbsp;&nbsp;
                    Año:&nbsp;
                    <select id="anio" name="anio"><?php for ($i=date('Y'); $i >2020; $i--) { ?><option><?=$i?></option><?php } ?></select>
                    &nbsp;&nbsp;&nbsp;
                    Mes:&nbsp;
                    <select id="mes" name="mes">
                        <option value="">Todos</option>
                        <option value="01">Enero</option>
                        <option value="02">Febrero</option>
                        <option value="03">Marzo</option>
                        <option value="04">Abril</option>
                        <option value="05">Mayo</option>
                        <option value="06">Junio</option>
                        <option value="07">Julio</option>
                        <option value="08">Agosto</option>
                        <option value="09">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                    &nbsp;&nbsp;&nbsp;
                    <!--Día:&nbsp;
                    <Input id="tfecha" name="tfecha" type="text" class="flatfecha"> -->
                    <button class="btn btn-danger btn-consultar"  onclick="versolicitudesesp();">CONSULTAR</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a class="btn btn-warning" id="linkdescarga">DESCARGAR REPORTE EN EXCEL</a>
                </div>
                <div class="col-sm-12"></div>
                <div class="col-sm-2"><b>Institucion:</b> <input id="institucion" onkeypress="if(event.keyCode==13) versolicitudesesp();" class="form-control"> </div>
                <div class="col-sm-2"><b>Fecha de solicitud:</b> <Input id="fecha_exp" name="fecha_exp" type="text" class="form-control flatfecha"> </div>
                <!--<div class="col-sm-2"><b>Fecha de atención:</b> <Input id="fatencion" name="fatencion" type="text"  class="form-control flatfecha"> </div>-->
                <div class="col-sm-8"></div>
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12">
                    <button class="btn btn-danger btn-consultar"  onclick="versolicitudesesp();">CONSULTAR</button>
                    <button id="btn_guardar" class="btn btn-success" onclick="guardar_reportecorreos();">GUARDAR Y ENVIAR CORREO</button>
                    <img height="30px" id="gif01" style="display:none;" src="img/reloj.webp">
                </div>
                <div class="col-sm-12"><b>(*) Al colocar en situación: <span style="color:green;">ATENDIDO</span> la respuesta automatica será: Contraseña restablecida a: IIee<?=date('Y')?></b></div>
                 
                <div class="col-sm-12"><b>Ver los primeros:</b> <select id="limite" onchange="versolicitudesesp();"><option value="0">Todo</option><option>20</option><option>100</option><option>200</option><option>500</option><option>1000</option></select></div>
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12">
                    <div id="spreadsheet"></div>
                </div>
                @csrf
            </div>

        </div>
    </div>
</div>

<script>
var g_datos_modulo = [];
    function guardar_reportecorreos(){
        g_datos_modulo = [];
        var mdata = g_reporte.getData();
        
        for (var i = 0; i < mdata.length; i++) {
            if(g_row_modificado.indexOf(i)>-1){
            var fila = [];
                fila[0] = mdata[i][0];
                fila[1] = mdata[i][3];
                fila[2] = mdata[i][2];
                //fila[2] = mdata[i][4];
            //g_datos_modulo.push(fila);
            g_datos_modulo.push(fila.join('||'));
            }
        };
        if(!g_datos_modulo.length){ return false; }
        ajax_data = {
          "datos_modulo"   : g_datos_modulo.join('&&'),
          "idespecialista" : <?=$session['idespecialista']?>,
          "_token"         : $("input[name=_token]").val(),
          "alt"            : Math.random()
        }
        $.ajax({
            type: "POST",
            url: "{{route('guardar_reportecorreos')}}",
            data: ajax_data,
            dataType: "html",
            beforeSend: function(){
                  $("#btn_guardar").prop('disabled',true);
                  $("#gif01").css('display','');
            },
            error: function(){
                  alert("error peticiÃ³n ajax");
                  $("#btn_guardar").prop('disabled',false);
                  $("#gif01").css('display','none')
            },
            success: function(data){
                    $("#btn_guardar").prop('disabled',false);
                    $("#gif01").css('display','none')
                    versolicitudesesp();
                    alert('Datos Guardados');
              }
        });
    
    }

    var g_reporte = [];
    var g_row_modificado=[];
    function versolicitudesesp(idTip=0){
        
        $("#btn_guardar").html(($("input[name=box]:checked").val()=='SOLICITADO')?'GUARDAR Y ENVIAR CORREO >':'GUARDAR');
        
    g_row_modificado=[];
    ajax_data = {
      "institucion": $("#institucion").val(),
      "fecha_exp": $("#fecha_exp").val(),
      "situacion_alerta" : $("input[name=box]:checked").val(),
      "anio"    : $("#anio").val(),
      "aniomes" : ($("#mes").val())?$("#anio").val()+'-'+$("#mes").val():'',
      "tfecha"  : $("#tfecha").val(),
      "limite"  : $("#limite").val(),
      "alt"     : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_reportecorreos')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                          //imagen de carga
                          $(".btn-consultar").prop('disabled',true);
                          $("body").css('cursor','wait');
                          //$("#login").html("<p align='center'><img src='http://intranet.ugel01.gob.pe/prestamos/public/images/cargando.gif'/></p>");
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                          $(".btn-consultar").prop('disabled',false);
                          $("body").css('cursor','');
                    },
                    success: function(data){
                        var reporte = [];
                        if(data.length){
                            for (let i = 0; i < data.length; i++) {
                                var key = [];
                                key.push(data[i]['idRes']);
                                key.push(data[i]['creado_at']);
                                key.push(data[i]['correo_inst']);
                                key.push(data[i]['flg']);
                                //key.push(data[i]['resRes']);
                                key.push(data[i]['correo']);
                                key.push(data[i]['director']);
                                key.push(data[i]['institucion']);
                                key.push(data[i]['distrito']);
                                key.push(data[i]['codlocal']);
                                key.push(data[i]['updated_at'])
                                reporte.push(key);
                            }
                        }else{
                            reporte = [['','','','','','','','','','','','','','']];
                        }

                        $("#spreadsheet").html('');
                        
                        g_reporte = [];
                        g_reporte = jexcel(document.getElementById('spreadsheet'), {
                            data:reporte,
                            onchange:handler,
                            columns: [
                                {
                                    type: 'text',
                                    title:'idRes',
                                    width:1
                                },
                                {
                                    type: 'text',
                                    title:'Fecha de solicitud',
                                    width:140,
                                    readOnly:true,
                                },
                                {
                                    type: 'text',
                                    title:'Correo Institucional',
                                    readOnly:true,
                                    width:320
                                },
                                {
                                    type: 'dropdown',
                                    title:'Situación',
                                    width:120,
                                    source:[
                                        "SOLICITADO",
                                        "ATENDIDO",
                                        "OBSERVADO",
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'correo personal',
                                    width:180,
                                    readOnly:true,
                                },
                                {
                                    type: 'text',
                                    title:'director',
                                    width:200,
                                    readOnly:true,
                                },
                                {
                                    type: 'text',
                                    title:'institucion',
                                    width:200,
                                    readOnly:true,
                                },
                                {
                                    type: 'text',
                                    title:'distrito',
                                    width:160,
                                    readOnly:true,
                                },
                                {
                                    type: 'text',
                                    title:'codigo local',
                                    width:80,
                                    readOnly:true,
                                },
                                {
                                    type: 'text',
                                    title:'Fecha de atención',
                                    width:140,
                                    readOnly:true,
                                },
                            ]

                        });

                        $(".readonly").css({'color':'#000','background-color':'rgb(255,255,204)'});
                        $(".btn-consultar").prop('disabled',false);
                        $("body").css('cursor','');
                        $("#linkdescarga").prop('href',"{{route('excel_reportecorreos')}}?flg="+$("input[name=box]:checked").val());

                      }
              });
}
/*
{
                                    type: 'dropdown',
                                    title:'flg',
                                    width:120,
                                    source:[
                                        "SOLICITADO",
                                        "EN PROCESO DE ATENCIÓN",
                                        "ATENDIDO",
                                        "NO PROCEDE"
                                      ]
                                },
                                {
                                    type: 'calendar',
                                    title:'codlocal',
                                    width:90,
                                    readOnly:true,
                                }
*/
handler = function(obj, cell, col, row, val) {
    if(g_row_modificado.indexOf(parseInt(row))==-1){ g_row_modificado.push(parseInt(row)); }
}

flatpickr('.flatfecha', {
      minDate: '1920-01-01',  
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
    
versolicitudesesp();

</script>

@endsection
                      
