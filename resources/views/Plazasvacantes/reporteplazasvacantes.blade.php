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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REPORTE DE PLAZAS VACANTES IIEE (reportadas por los directores)</b></h5>
        <div class="position-relative form-group">
           
            
            <div class="row" style="color:#000;">
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12">
                    <input type="radio" name="box" value="SOLICITADO" onclick="versolicitudesesp();" checked> Solicitados
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="EN PROCESO DE ATENCIÓN" onclick="versolicitudesesp();"> En proceso de atención
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="ATENDIDO" onclick="versolicitudesesp();"> Atendidos
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
                </div>
                <div class="col-sm-12"></div>
                <div class="col-sm-2"><b>Institucion:</b> <input id="institucion" onkeypress="if(event.keyCode==13) versolicitudesesp();" class="form-control"> </div>
                <div class="col-sm-2"><b>Fecha de expediente:</b> <Input id="fecha_exp" name="fecha_exp" type="text" class="form-control flatfecha"> </div>
                <!--<div class="col-sm-2"><b>Fecha de atención:</b> <Input id="fatencion" name="fatencion" type="text"  class="form-control flatfecha"> </div>-->
                <div class="col-sm-8"></div>
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12">
                    <button class="btn btn-danger btn-consultar"  onclick="versolicitudesesp();">CONSULTAR</button>
                    <button id="btn_guardar" class="btn btn-success" onclick="guardar_reporteplazasvacantes();">GUARDAR</button>
                </div>
                 
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
    function guardar_reporteplazasvacantes(){
        g_datos_modulo = [];
        var mdata = g_reporte.getData();
        
        for (var i = 0; i < mdata.length; i++) {
            if(g_row_modificado.indexOf(i)>-1){
            var fila = [];
                fila[0] = mdata[i][0];
                fila[1] = mdata[i][3];
                fila[2] = mdata[i][4];
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
            url: "{{route('guardar_reporteplazasvacantes')}}",
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
                    versolicitudesesp();
                    alert('Datos Guardados');
              }
        });
    
    }

    var g_reporte = [];
    var g_row_modificado=[];
    function versolicitudesesp(idTip=0){
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
                    url: "{{route('tabla_reporteplazasvacantes')}}",
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
                                key.push(data[i]['idalertplaza']);
                                key.push(data[i]['motivoalerta']);
                                key.push(data[i]['obs']);
                                key.push(data[i]['situacion_alerta']);
                                key.push(data[i]['situacion_obs']);
                                key.push(data[i]['exp']);
                                key.push(data[i]['fecha_exp']);
                                key.push(data[i]['codmodce']);
                                key.push(data[i]['codplaza']);
                                key.push(data[i]['nombie']);
                                key.push(data[i]['descniveduc']);
                                key.push(data[i]['descargo']);
                                key.push(data[i]['situacion']);
                                key.push(data[i]['tiporegistro']);
                                key.push(data[i]['docente']);
                                key.push(data[i]['obser']);
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
                                    title:'idalertplaza',
                                    width:1
                                },
                                {
                                    type: 'text',
                                    title:'Alerta',
                                    width:200,
                                    readOnly:true,
                                },
                                {
                                    type: 'text',
                                    title:'Observación',
                                    readOnly:true,
                                    width:200
                                },
                                {
                                    type: 'dropdown',
                                    title:'Respuesta',
                                    width:120,
                                    source:[
                                        "SOLICITADO",
                                        "EN PROCESO DE ATENCIÓN",
                                        "ATENDIDO",
                                        "NO PROCEDE"
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'Detalle de la respuesta',
                                    width:250
                                },
                                {
                                    type: 'text',
                                    title:'Expediente',
                                    width:80,
                                    readOnly:true,
                                },
                                {
                                    type: 'calendar',
                                    title:'Fecha_exp',
                                    width:90,
                                    readOnly:true,
                                },
                                
                                {
                                    type: 'text',
                                    title:'Codmodce',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Codplaza',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'IIEE',
                                    readOnly:true,
                                    width:220
                                },
                                {
                                    type: 'text',
                                    title:'Nivel',
                                    readOnly:true,
                                    width:100
                                },
                                
                                
                                {
                                    type: 'text',
                                    title:'Cargo',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Situacion',
                                    readOnly:true,
                                    width:100
                                },
                                
                                {
                                    type: 'text',
                                    title:'Tipo Registro',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'docente (Segun Nexus)',
                                    readOnly:true,
                                    width:200
                                },
                                {
                                    type: 'text',
                                    title:'Motivo de vacante',
                                    readOnly:true,
                                    width:200
                                },
                            ]

                        });

                        $(".readonly").css({'color':'#000','background-color':'rgb(255,255,204)'});
                        $(".btn-consultar").prop('disabled',false);
                        $("body").css('cursor','');

                      }
              });
}

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
                      
