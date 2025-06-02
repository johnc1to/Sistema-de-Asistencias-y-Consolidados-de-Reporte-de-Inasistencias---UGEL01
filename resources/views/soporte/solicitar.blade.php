@extends('layout_director/cuerpo')
@section('html')

<script type="text/javascript" src="assets/scripts/toastr.min.js"></script>
<link rel="stylesheet" type="text/css" href="assets/css/toastr.min.css">

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

<!---------JEXCEL--------------------->
<script src="https://bossanova.uk/jspreadsheet/v3/jexcel.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v3/jexcel.css" type="text/css" />

<script src="https://jsuites.net/v3/jsuites.js"></script>
<link rel="stylesheet" href="https://jsuites.net/v3/jsuites.css" type="text/css" />
<!---------JEXCEL--------------------->



<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;">
        <b>CREACIÓN Y RESTABLECIMIENTO DE ACCESO A APREDOENCASA.PE</b> 
        <br><br>
        
        <!--<a class="btn btn-info" target="_blank" href="storage/manuales/DIRECTOR-APRENDOENCASA.PE.pdf">Ver Manual</a> -->
        <!--<a class="btn btn-success" target="_blank" href="storage/manuales/VideoAprendoenCasa.mp4">Ver Video</a> -->
        <br>
        
        <img src="https://pa1.narvii.com/6470/7c4d5a73e114bce33603653c04e11a8da4e7c9a3_hq.gif" width="40px"> 
        
        <a class="btn " style="color:#000;background-color:#CED3F2;font-weight: bolder;" target="_blank" href="storage/manuales/Manual-de-restauracion-de-contraseña-2023.pdf">Ver Manual de restauración de contraseña 2023</a>
        <a class="btn " style="color:#000;background-color:#D0F2E9;font-weight: bolder;" target="_blank" href="storage/manuales/Formulario-de-restauracion-de-contraseñas-2023.pdf">Ver Formulario de restauración de contraseñas 2023</a>
        <a class="btn " style="color:#000;background-color:#F2E8C9;font-weight: bolder;" target="_blank" href="storage/manuales/Formulario-de-actualizacion-de-datos-de-usuario-2023.pdf">Ver Formulario de actualización de datos de usuario 2023</a>
        <!--<a class="btn btn-info" href="#" onclick="popup_alerta();" style="font-weight: bold;">El restablecimiento de claves lo puede hacer el director INGRESE AQUI</a>-->
        <!--<img src="https://pa1.narvii.com/6470/7c4d5a73e114bce33603653c04e11a8da4e7c9a3_hq.gif" width="40px"> -->
        </h5> 
        <div class="position-relative form-group">

            <b style="color:#000;">Nivel</b>
            <?php
            //dd($session);
            ?>
                <select name="txtnivel" id="txtnivel" class="form-control" onchange="versolicitudes();">
				    <?php
                    for ($i=0; $i < count($session['conf_permisos']); $i++) { 
                        $key = $session['conf_permisos'][$i];
                    ?><option value="<?=$i?>"><?=$key['nivel_pap']?></option><?php
                    }
                    ?>
                </select>
                <br>
                <button class="btn btn-success" onclick="restableceraccesodirector()">RESTABLECER ACCESO DIRECTOR</button>
                <button class="btn btn-danger"  onclick="restableceraccesodocente()">RESTABLECER ACCESO DOCENTE</button>
                <button class="btn btn-warning" onclick="restableceraccesoestudiante()">RESTABLECER ACCESO ESTUDIANTE</button>
                <br><br>
                <button class="btn btn-success" onclick="crearaccesodirector()">CREAR ACCESO DIRECTOR</button>
                <button class="btn btn-warning" onclick="actualizaciondirector()">ACTUALIZAR DIRECTOR</button>
                <button class="btn btn-danger"  onclick="crearaccesodocente()">CREAR ACCESO DOCENTE</button>
                <button class="btn btn-warning" onclick="crearaccesoestudiante()">CREAR ACCESO ESTUDIANTE</button>
        
        <br><br>
        <table border="1" id="t_mantenimiento" class="table table-bordered" style="text-align: center; font-size: 11px;width:100%;">
            <thead>
            <tr style="background: chartreuse;font-weight: bolder;color: black;">
                <td style= "width: 5px;">N°</td>
                <td>TIPO</td>
                <td>NOMBRES Y APELLIDOS</td>
                <td>DNI</td>
                <td>GRADO</td>
                <td>CUENTA</td>      
                <td>ETAPA</td>      
                <!--<td>OBSERVACION</td>-->
                <td>RESPUESTA</td>
                <td>ELIMINAR</td>
                <td>FECHA</td>
            </tr>
        </thead>
        <tbody></tbody>
        </table>


        </div>
    </div>
</div>

<script>
//var msj = 'Estimado director la mayoría de los correos aprendo en casa ya están creados, ¿está seguro de solicitar la creación de un correo aprendo en casa? Si no está seguro por favor llame o escriba a este número: 921447017';
var msj = 'ESTIMADO DIRECTOR: EL REPORTE COMPLETO DE CORREOS DE APRENDO EN CASA DE ESTUDIANTES Y DOCENTES, YA FUE ENVIADO EN EL MES DE MARZO DEL 2022 POR MINEDU A SU CORREO DE APRENDO EN CASA, ¿DIRECTOR ESTA SEGURO DE SOLICITAR LA CREACION DEL CORREO?';
function vervideo(){
    var opt = '';
    opt += '<div class="modal-header">';
    opt += '<h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>VIDEO</b></h4>';
    opt += '</div>';
    opt += '<div class="modal-body">';
    opt += '<iframe width="100%" height="500px" src="storage/manuales/VideoAprendoenCasa.mp4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    opt += '</div>';
    opt += '</div>';
    $("#popuplg .modal-content").html(opt);
    $("#fc_popuplg").click();
}

var permisos = <?=json_encode($session['conf_permisos'])?>;

    function actualizaciondirector(){
        ajax_data = {
            "codmod" : permisos[$("#txtnivel").val()]['esc_codmod'],
            "alt"    : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_actualizaciondirector')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popup01 .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        $("input[name=cueSop]")   .val("d<?=$session['dni']?>o");
                        $("input[name=dniSop]")   .val("<?=$session['dni']?>");
                        $("input[name=nomSop]")   .val("<?=$session['nombres']?>");
                        $("input[name=apepatSop]").val("<?=$session['apellipat']?>");
                        $("input[name=apematSop]").val("<?=$session['apellimat']?>");
                        $("input[name=corSop]")   .val("<?=$session['correo_pers']?>");
                        $("input[name=telSop]")   .val("<?=$session['celular_pers']?>");
                        $("#nomcompleto").html("<?=$session['nombres']?> <?=$session['apellipat']?> <?=$session['apellimat']?>");
                        $("#fc_popup").click();
                      }
              });        
    }
    
    function crearaccesodirector(){
        ajax_data = {
            "codmod" : permisos[$("#txtnivel").val()]['esc_codmod'],
            "alt"    : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_crearaccesodirector')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popup01 .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        $("input[name=cueSop]")   .val("d<?=$session['dni']?>o");
                        $("input[name=dniSop]")   .val("<?=$session['dni']?>");
                        $("input[name=nomSop]")   .val("<?=$session['nombres']?>");
                        $("input[name=apepatSop]").val("<?=$session['apellipat']?>");
                        $("input[name=apematSop]").val("<?=$session['apellimat']?>");
                        $("input[name=corSop]")   .val("<?=$session['correo_pers']?>");
                        $("input[name=telSop]")   .val("<?=$session['celular_pers']?>");
                        $("#nomcompleto").html("<?=$session['nombres']?> <?=$session['apellipat']?> <?=$session['apellimat']?>");
                        $("#fc_popup").click();
                      }
              });        
    }

    function restableceraccesodirector(){
        ajax_data = {
            "codmod" : permisos[$("#txtnivel").val()]['esc_codmod'],
            "alt"    : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_restableceracesodirector')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popup01 .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        $("input[name=cueSop]")   .val("d<?=$session['dni']?>o");
                        $("input[name=dniSop]")   .val("<?=$session['dni']?>");
                        $("input[name=nomSop]")   .val("<?=$session['nombres']?>");
                        $("input[name=apepatSop]").val("<?=$session['apellipat']?>");
                        $("input[name=apematSop]").val("<?=$session['apellimat']?>");
                        $("input[name=corSop]")   .val("<?=$session['correo_pers']?>");
                        $("input[name=telSop]")   .val("<?=$session['celular_pers']?>");
                        $("#nomcompleto").html("<?=$session['nombres']?> <?=$session['apellipat']?> <?=$session['apellimat']?>");
                        $("#fc_popup").click();
                      }
              });        
    }

    function crearaccesodocente(){
        ajax_data = {
            "codmod" : permisos[$("#txtnivel").val()]['esc_codmod'],
            "alt"    : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_crearaccesodocente')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popup01 .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        $("#fc_popup").click();
                      }
              });
    }
    
    function restableceraccesodocente(){
        ajax_data = {
            "codmod" : permisos[$("#txtnivel").val()]['esc_codmod'],
            "alt"    : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_accesodocente')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popup01 .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        $("#fc_popup").click();
                      }
              });        
    }
    
    function crearaccesoestudiante(){
        ajax_data = {
            "codmod"  : permisos[$("#txtnivel").val()]['esc_codmod'],
            "idnivel" : permisos[$("#txtnivel").val()]['idnivel'],
            "alt"     : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_crearaccesoestudiante')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popuplg .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        if([4,6].indexOf(permisos[$("#txtnivel").val()]['idnivel'])>-1){ $("select[name=graSop]").append('<option>sexto</option>');}
                        $("#fc_popuplg").click();
                        $("#popuplg .modal-content").css('width','930px');
                      }
              });
    }
    
    function restableceraccesoestudiante(){
        ajax_data = {
            "codmod"  : permisos[$("#txtnivel").val()]['esc_codmod'],
            "idnivel" : permisos[$("#txtnivel").val()]['idnivel'],
            "alt"     : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_restableceraccesoestudiante')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popuplg .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        if([4,6].indexOf(permisos[$("#txtnivel").val()]['idnivel'])>-1){ $("select[name=graSop]").append('<option>sexto</option>');}
                        $("#fc_popuplg").click();
                      }
              });
    }

    
    function masivocrearaccesodocente(){
        $("#fc_popup").click();
        ajax_data = {
            "codmod"  : permisos[$("#txtnivel").val()]['esc_codmod'],
            "idnivel" : permisos[$("#txtnivel").val()]['idnivel'],
            "alt"     : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_masivocrearaccesodocente')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popuplg .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        if([4,6].indexOf(permisos[$("#txtnivel").val()]['idnivel'])>-1){ $("select[name=graSop]").append('<option>sexto</option>');}
                        $("#fc_popuplg").click();
                        $("#popuplg .modal-content").css('width','930px');
                      }
              });
    }
    
    function masivorestableceraccesodocente(){
        $("#fc_popup").click();
        ajax_data = {
            "codmod"  : permisos[$("#txtnivel").val()]['esc_codmod'],
            "idnivel" : permisos[$("#txtnivel").val()]['idnivel'],
            "alt"     : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_masivorestableceraccesodocente')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popuplg .modal-content").html(data);
                        $("input[name=codmodSop]").val(ajax_data['codmod']);
                        $("input[name=id_contactoSop]").val(<?=$session['id_contacto']?>);
                        if([4,6].indexOf(permisos[$("#txtnivel").val()]['idnivel'])>-1){ $("select[name=graSop]").append('<option>sexto</option>');}
                        $("#fc_popuplg").click();
                      }
              });
    }
    

function popup_alerta(){
        ajax_data = {
            "alt"    : Math.random()
        }
        $.ajax({
                    type: "GET",
                    url: "{{route('popup_alerta')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){},
                    error: function(){alert("error peticiÃ³n ajax");},
                    success: function(data){
                        $("#popup01 .modal-content").html('');
                        $("#popuplg .modal-content").html('');
                        $("#popup01 .modal-content").html(data);
                        $("#fc_popup").click();
                      }
              });        
    }

    function guardar_soporte(){
          //información del formulario
          var formData = new FormData($("#formulario01")[0]);
          var message = "";
          //hacemos la petición ajax  
          
          $.ajax({
              url: '{{route('guardar_soporte')}}',  
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
                $("#fc_popup").click();
                versolicitudes();
                toastr.success('Datos guardados');
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
        
    }




//versolicitudes

function versolicitudes(){
    ajax_data = {
    "codmod" : permisos[$("#txtnivel").val()]['esc_codmod'],
      "alt"  : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('versolicitudes')}}",
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
                            data[i]['nro'] = i+1;
                            data[i]['t_etaSop'] = '<b style="'+((data[i]['etaSop']!='SOLICITADO')?'color:green;':'')+'">'+data[i]['etaSop']+'</b>';
                            data[i]['resSop']   = '<span style="color:green;">'+data[i]['resSop']+'</span>';
                            data[i]['t_nomSop'] = data[i]['nomSop']+' '+data[i]['apepatSop']+' '+data[i]['apematSop'];
                            data[i]['eliminar'] = (data[i]['etaSop']=='SOLICITADO')?'<button onclick="eliminarsolicitud('+data[i]['idSop']+');" style="padding: 2px 8px 2px 8px;" class="btn btn-danger">X</button>':'';
                        }
                        table4.clear().draw();
                        table4.rows.add(data).draw();
                      }
              });
}

function eliminarsolicitud(idSop){
    ajax_data = {
        'idSop': idSop,
      "alt"    : Math.random()
    }
    if(confirm('¿Esta seguro que desea eliminar la solicitud?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminarsolicitud')}}",
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
                        versolicitudes();
                        toastr.info('Eliminado');
                      }
              });
    }
}

</script>

<script type="text/javascript">
        var table4 = $("#t_mantenimiento").DataTable( {
                            dom: 'Bfrtip',
                            buttons: ['excel'],
                            "iDisplayLength": 35,
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                            },
                            data:[],
                            "columns": [
                                { "data": "nro" },
                                { "data": "desTip" },         
                                { "data": "t_nomSop" },
                                { "data": "dniSop" },
                                { "data": "graSop" },
                                { "data": "cueSop" },
                                { "data": "t_etaSop" },
                                //{ "data": "obsSop" },
                                { "data": "resSop" },                                
                                { "data": "eliminar" },
                                { "data": "fecha" },
                            ],                          
                            rowCallback: function (row, data) {},
                            filter: true,
                            info: true,
                            ordering: true,
                            processing: true,
                            retrieve: true                          
                        });

    //popup_alerta();
    
    versolicitudes();
    
    </script>


@endsection
