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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>CREACIÓN Y RESTABLECIMIENTO DE ACCESO A APREDOENCASA.PE</b></h5>
        <div class="position-relative form-group">
           
            <div class="row" style="color:#000;">
                <a href="{{route('anexo05director')}}" class="btn btn-success">Anexo 05 (Directores)</a>&nbsp;&nbsp;&nbsp;
                <a href="{{route('anexo06docentes')}}" class="btn btn-danger">Anexo 06 (Docentes)</a>&nbsp;&nbsp;&nbsp;
                <a href="{{route('anexo07estudiantes')}}" class="btn btn-warning">Anexo 07 (Estudiantes)</a>&nbsp;&nbsp;&nbsp;
                <a href="{{route('anexo08reportemensual')}}" class="btn btn-info" onclick="anexo08reportemensual(this);">Anexo 08 (Reporte mensual)</a>&nbsp;&nbsp;&nbsp;
                <a href="{{route('anexo09actualizaciondirectores')}}" class="btn btn-success">Anexo 09 (Actualizacion de Directores)</a>&nbsp;&nbsp;&nbsp;
                <a href="{{route('anexotraslado')}}" class="btn btn-warning">Trasladado UGEL</a>
            </div>
            
            <div class="row" style="color:#000;">
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12">
                    <input type="radio" name="box" value="1" idTip="5,6,7,1,3" onclick="versolicitudesesp();" checked> Solicitados Directivos y Docentes
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="1" idTip="2,4" onclick="versolicitudesesp();"> Solicitados Estudiantes
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="0" idTip="5,6,7,1,3" onclick="versolicitudesesp();"> Atendidos Directivos y Docentes
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="0" idTip="2,4" onclick="versolicitudesesp();"> Atendidos Estudiantes
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="box" value="2" idTip="" onclick="versolicitudesesp();"> Traslado de UGEL
                    &nbsp;&nbsp;&nbsp;
                    Año:&nbsp;
                    <select id="anio" name="anio"><?php for ($i=date('Y'); $i >2020; $i--) { ?><option><?=$i?></option><?php } ?></select>
                    &nbsp;&nbsp;&nbsp;
                    Mes:&nbsp;
                    <select id="mes" name="mes">
                        <option value="01" <?=(date('m')==1)?'selected':''?>>Enero</option>
                        <option value="02" <?=(date('m')==2)?'selected':''?>>Febrero</option>
                        <option value="03" <?=(date('m')==3)?'selected':''?>>Marzo</option>
                        <option value="04" <?=(date('m')==4)?'selected':''?>>Abril</option>
                        <option value="05" <?=(date('m')==5)?'selected':''?>>Mayo</option>
                        <option value="06" <?=(date('m')==6)?'selected':''?>>Junio</option>
                        <option value="07" <?=(date('m')==7)?'selected':''?>>Julio</option>
                        <option value="08" <?=(date('m')==8)?'selected':''?>>Agosto</option>
                        <option value="09" <?=(date('m')==9)?'selected':''?>>Septiembre</option>
                        <option value="10" <?=(date('m')==10)?'selected':''?>>Octubre</option>
                        <option value="11" <?=(date('m')==11)?'selected':''?>>Noviembre</option>
                        <option value="12" <?=(date('m')==12)?'selected':''?>>Diciembre</option>
                    </select>
                    &nbsp;&nbsp;&nbsp;
                    Día:&nbsp;
                    <Input id="tfecha" name="tfecha" type="text" class="flatfecha"> <button class="btn btn-danger btn-consultar"  onclick="versolicitudesesp();">CONSULTAR</button>
                </div>
                <div class="col-sm-12"></div>
                <div class="col-sm-2"><b>Estudiante:</b> <input  id="buscador"    onkeypress="if(event.keyCode==13) versolicitudesesp();" class="form-control"> </div>
                <div class="col-sm-2"><b>Institucion:</b> <input id="institucion" onkeypress="if(event.keyCode==13) versolicitudesesp();" class="form-control"> </div>
                <div class="col-sm-2"><b>Fecha de solicitud:</b> <Input id="fcreacion" name="fcreacion" type="text" class="form-control flatfecha"> </div>
                <div class="col-sm-2"><b>Fecha de atención:</b> <Input id="fatencion" name="fatencion" type="text"  class="form-control flatfecha"> </div>
                <div class="col-sm-8"></div>
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12">
                    <button class="btn btn-danger btn-consultar"  onclick="versolicitudesesp();">CONSULTAR</button>
                    <button id="btn_guardar" class="btn btn-success" onclick="guardarrespuesta();">GUARDAR</button>
                </div>
                <div class="col-sm-12"><b>(*) Al colocar etapa: <span style="color:green;">ATENDIDO</span> en el tipo: <span style="color:green;">RESTABLECER ACCESO</span> la respuesta automatica será: Clave restablecida a: Ugel01<?=date('Y')?></b></div>
                <div class="col-sm-12"><b>(*) Al colocar etapa: <span style="color:green;">ATENDIDO</span> en el tipo: <span style="color:green;">CREAR ACCESO</span> la respuesta automatica será: El Minedu le remitirá un correo con sus credenciales para acceder a aprendoencasa.pe</b></div>
                <div class="col-sm-12"><b>(*) Al colocar etapa: <span style="color:green;">ATENDIDO MINEDU</span> en el tipo: <span style="color:green;">RESTABLECER ACCESO</span> la respuesta automatica será: Se pidió restablecimiento masivo a MINEDU, en breve revise el correo del director aprendoencasa.pe</b></div>
                
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
    var g_reporte = [];
    var g_datos_modulo = [];
    function guardarrespuesta(){
        g_datos_modulo = [];
        var mdata = g_reporte.getData();
        
        for (var i = 0; i < mdata.length; i++) {
            var fila = [];
                fila[0] = mdata[i][0];
                fila[1] = mdata[i][1];
                fila[2] = '';
                fila[3] = mdata[i][3];
                fila[4] = mdata[i][4];
                fila[5] = mdata[i][5];
            g_datos_modulo.push(fila.join('||'));
            //g_datos_modulo.push([mdata[i][0],mdata[i][1],mdata[i][2],mdata[i][3],mdata[i][4]]);
        };
    
        ajax_data = {
          "datos_modulo"   : g_datos_modulo.join('&&'),
          "idespecialista" : <?=$session['idespecialista']?>,
          "box"            : $("input[name=box]:checked").val(),
          "_token"         : $("input[name=_token]").val(),
          "alt"            : Math.random()
        }
        $.ajax({
            type: "POST",
            url: "{{route('guardarrespuesta')}}",
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
              }
        });
    
    }

    function anexo08reportemensual(athis){
        $(athis).prop("href","{{route('anexo08reportemensual')}}?aniomes="+$("#anio").val()+"-"+$("#mes").val()+"&mes="+$("#mes option:selected").html());
    }

    var g_reporte = [];
    function versolicitudesesp(idTip=0){
    ajax_data = {
      "buscador": $("#buscador").val(),
      "institucion": $("#institucion").val(),
      "fcreacion": $("#fcreacion").val(),
      "fatencion": $("#fatencion").val(),
      "idTip"   : $("input[name=box]:checked").attr('idTip'),
      "aniomes" : $("#anio").val()+'-'+$("#mes").val(),
      "tfecha"  : $("#tfecha").val(),
      "box"     : $("input[name=box]:checked").val(),
      "limite"  : $("#limite").val(),
      "alt"     : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('versolicitudesesp')}}",
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
                                var desTip = (data[i]['idTip']==1 || data[i]['idTip']==2 || data[i]['idTip']==5)?'<span style="color:green;font-weight: bolder;">'+data[i]['desTip'].replace('RESTABLECER','RESTABLECER<br>').replace('CREAR','CREAR<br>')+'</span>':data[i]['desTip'].replace('RESTABLECER','RESTABLECER<br>').replace('CREAR','CREAR<br>');
                                key.push(data[i]['idSop']);
                                key.push(data[i]['idTip']);
                                key.push(desTip);
                                key.push(data[i]['etaSop']);
                                key.push(data[i]['resSop'].replace('correo','correo<br>').replace('acceder','acceder<br>'));
                                key.push(data[i]['cueSop']);
                                key.push(data[i]['institucion']);
                                key.push(data[i]['nivel']);
                                key.push(data[i]['graSop']);
                                key.push(data[i]['codmodSop']);
                                key.push(data[i]['nomSop']+' '+data[i]['apepatSop']+' '+data[i]['apematSop']);
                                //key.push(data[i]['obsSop']);
                                key.push(data[i]['dniSop']);
                                key.push(data[i]['codSop']);
                                key.push(data[i]['fecha']);
                                key.push(data[i]['hora']);
                                //if(ajax_data['idTip']=='5,6,7,1,3' || ajax_data['idTip']=='2,4' ){
                                key.push(data[i]['atemfecha']);
                                key.push(data[i]['atemhora']);
                                //}
                                
                                key.push(data[i]['celular_pers']);
                                key.push(data[i]['director']);
                                key.push(data[i]['correo_pers']);
                                reporte.push(key);
                            }
                        }else{
                            reporte = [['','','','','','','','','','','','','']];
                        }

                        $("#spreadsheet").html('');
                        
                        g_reporte = [];
                        g_reporte = jexcel(document.getElementById('spreadsheet'), {
                            data:reporte,
                            columns: [
                                {
                                    type: 'text',
                                    title:'idSop',
                                    width:1
                                },
                                {
                                    type: 'text',
                                    title:'idTip',
                                    width:1
                                },
                                {
                                    type: 'text',
                                    title:'Tipo',
                                    width:150,
                                    readOnly:true,
                                },
                                {
                                    type: 'dropdown',
                                    title:'Etapa',
                                    width:150,
                                    source:[
                                        "SOLICITADO",
                                        "ATENDIDO",
                                        "ATENDIDO MINEDU",
                                        "OBSERVADO",
                                        "TRASLADO DE UGEL",
                                    ]
                                },
                                {
                                    type: 'text',
                                    title:'Respuesta especialista',
                                    width:220
                                },
                                {
                                    type: 'text',
                                    title:'Usuario',
                                    width:100
                                },
                                
                                
                                {
                                    type: 'text',
                                    title:'Institucion',
                                    readOnly:true,
                                    width:200
                                },
                                {
                                    type: 'text',
                                    title:'Nivel',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Grado',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'CodMod',
                                    readOnly:true,
                                    width:100
                                },
                                
                                {
                                    type: 'text',
                                    title:'Nombre y apellido',
                                    readOnly:true,
                                    width:220
                                },
                                {
                                    type: 'text',
                                    title:'Dni',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Cod.Estudiante',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Fecha',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Hora',
                                    readOnly:true,
                                    width:100
                                },
                                
                                {
                                    type: 'text',
                                    title:'Fecha Atendido',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Hora Atendido',
                                    readOnly:true,
                                    width:100
                                },
                                
                                {
                                    type: 'text',
                                    title:'Celular Director',
                                    readOnly:true,
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Director',
                                    readOnly:true,
                                    width:200
                                },
                                {
                                    type: 'text',
                                    title:'Correo Director',
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