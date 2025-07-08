@extends('layout_especialista/cuerpo')
@section('html')

<script src="https://bossanova.uk/jexcel/v3/jexcel.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jexcel/v3/jexcel.css" type="text/css" />

<script src="https://jsuites.net/v3/jsuites.js"></script>
<link rel="stylesheet" href="https://jsuites.net/v3/jsuites.css" type="text/css" />

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

<style>
  .cabezera{font-weight: bold;text-align:center; background-color:rgb(217,217,217);}
  .negritacentrado { font-weight: bold;text-align:center; }
  .gris { background-color:rgb(217,217,217); }
  .marcarx{ width:25px;font-weight: bold;cursor: pointer;text-align:center; }
  .centro { text-align:center;  }
</style>

<style type="text/css">
	#instituciones thead tr th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #ffffff;
        }
    
    /*display:block;
    width:100%;*/
    .jaaf-control{
    height:32px;
    padding:6px 12px;
    font-size:13px;
    line-height:1.42857143;
    color:#555;
    background-color:#fff;
    background-image:none;border:1px solid #e4e4e4;border-radius:0;
    -webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;-o-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s}
    
</style>


<div class="main-card mb-12 card">
    <div class="card-body">
        <h5 class="card-title" style="font-size:20px;"><b>FICHAS - MONITOREO</b></h5>
        
        
        <div class="col-xs-12" style="color:#000;font-size:10px;">
	        <select id="anio" class="form-control" onchange="selecanio();" style="display:inline-block;width:100px;">
                <?php
                for ($i=date('Y'); $i>2020; $i--) { 
                ?><option <?=($anio==$i)?'selected':''?>><?=$i?></option><?php
                }
                ?>
                <option <?=($anio=='TODO')?'selected':''?>>TODO</option>
            </select>
            
            <select id="area" class="form-control" onchange="selecanio();" style="display:inline-block;width:100px;">
                <option></option>
                <?php
                foreach ($listaarea as $key) {
                ?><option <?=($key->areaFic==$area)?'selected':''?>><?=$key->areaFic?></option><?php
                }
                ?>
            </select>
        </div>
        
        
        <div class="position-relative form-group">

            <form id="formulario01" enctype="multipart/form-data" style="width:100%;" onsubmit="return false;">
                
                <div class="col-xs-5" style="color:#000;font-size:10px;padding-left:0px;">
			        <input type="radio" name="selector" value="1" onclick="listar_ficha();"> <b style="color:#000;font-size:16px;">Ficha</b>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			        <?php if($session['id_oficina']==77 or $session['idespecialista']==890 or $session['idespecialista']==675){ ?><input type="radio" name="selector" value="2" onclick="opciones_competencia();"> <b style="color:#000;font-size:16px;">Preguntas</b><?php } ?>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			        <input type="radio" name="selector" value="3" onclick="opciones_respuesta();" checked> <b style="color:#000;font-size:16px;">ver Respuesta</b>
			        
			    </div>
			    
			    <div class="col-xs-12" style="color:#000;font-size:10px;padding-left:0px;margin-top:12px;" id="botones"></div>
			    
			    <div class="col-sm-12" style="color:#000;"><br></div>
			    <!--<div class="col-sm-12" style="color:#000;"><u><b>RECOMENDACIONES:</b></u></div>
			    <div class="col-sm-12" style="color:#000;"><b>(*) Para añadir un nuevo registro debe ir a la ultima fila y presionar enter</b></div>
			    <div class="col-sm-12" style="color:#000;"><b>(*) Para eliminar un registro debe cambiar la columna estado a ELIMINAR</b></div>-->
			    <div class="col-sm-12" style="color:#000;" id="avance"></div>
			    <div class="col-sm-12" style="color:#000;"><br></div>
			    
			    <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="catalogo_nacional"></div>
			    <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="competencias"></div>
			    
			    <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="instituciones">
    			    <div class="">
    			        <table class="display table table-bordered table-striped table-dark" id="t_instituciones" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <th class="header" scope="col" style="min-width:25px;color:#fff;background-color:rgb(0,117,184);"><b>Nro</b></th>
                                <th class="header" scope="col" style="min-width:25px;color:#fff;background-color:rgb(0,117,184);"><b><input type="checkbox" onclick="selectboxficha(this);"></b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>codlocal</b></th>
                                <!--<th style="min-width:55px;color:#fff;"><b>Codmod</b></th>-->
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Red</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Institucion</b></th>
                                <th class="header" scope="col" style="min-width:65px;color:#fff;background-color:rgb(0,117,184);"><b>Modalidad</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Distrito</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>DNI</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Director</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Celular</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Correo</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Fecha</b></th>                                
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>ficha</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Acceder</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Especialista</b></th>
                                <th class="header" scope="col" style="min-width:55px;color:#fff;background-color:rgb(0,117,184);"><b>Visita</b></th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                    </div>
                    <br>
                    <div class="table-responsive divresumen" id="div_sino">
                        <b style="font-size:22px;">RESUMEN (SI/NO)</b>
    			        <table class="display table table-bordered table-striped table-dark" id="t_resumen" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="min-width:15px;color:#fff;"><b>Nro</b></td>
                                <td style="min-width:80px;color:#fff;"><b>Grupo</b></td>
                                <td style="min-width:15px;color:#fff;"><b>Nro</b></td>
                                <td style="min-width:300px;color:#fff;"><b>Ítem</b></td>
                                <td style="min-width:15px;color:#fff;"><b>SI</b></td>
                                <td style="min-width:15px;color:#fff;"><b>NO</b></td>
                                <td style="min-width:15px;color:#fff;"><b>SI</b></td>
                                <td style="min-width:15px;color:#fff;"><b>NO</b></td>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                    </div>
                    <br>
                    <div class="table-responsive divresumen" id="div_ipl">
                        <b style="font-size:22px;">DESCRIPCIÓN DEL NIVEL DE AVANCE</b>
    			        <table class="display table table-bordered table-striped table-dark" id="t_resumenipl" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="min-width:300px;color:#fff;"><b>ASPECTOS MONITOREADOS</b></td>
                                <td style="min-width:15px;color:#fff;"><b>ITEMS</b></td>
                                <td style="min-width:15px;color:#fff;"><b>En Inicio (1)</b></td>
                                <td style="min-width:15px;color:#fff;"><b>En Proceso (2)</b></td>
                                <td style="min-width:15px;color:#fff;"><b>Logrado (3)</b></td>
                                <td style="min-width:15px;color:#fff;"><b>NIVEL DE AVANCE SATISFACTORIO (%)</b></td>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                    </div>
                </div>

			    <div class="col-sm-12 elementos" style="color:#000;font-size:10px;padding-left:0px;" id="cargando"><img style="text-align:center;" src="./assets/images/load10.gif"></div>


            </form>
        </div>
    </div>
</div>

<br>

<div class="main-card mb-12 card" id="div_htmladicional" style="display:none;">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>HTML ADICIONAL</b></h5>
        <div class="position-relative form-group">
            <form id="formulario02" enctype="multipart/form-data" style="width:100%;" onsubmit="return false;">
            <div class="row">
    			    <div class="col-sm-12" style=""><b style="font-size:14px;color:#000;">Pregunta:</b></div>
                    <div class="col-sm-12">
                    <select  id="idpregunta" class="form-control" onchange="listar_html_adicional(this.value);">
                    <option value="">Selecione la pregunta</option>
                    </select>
                    </div>
                    <div class="col-sm-4">
                    @csrf
                    <button class="btn btn-success" onclick="guardar_html();">GUARDAR</button>
                    </div>
                </div>
                <div class="row">                    
                    <div class="col-sm-12">
                        <b style="color:#000;">HTML</b>
                        <div id="html_adicional"></div>
                        <br>
                        <div id="variables_adicional" style="color:red;"></div>
                    </div>                    
                    <div class="col-sm-12"><br></div>                        
                    <div class="col-sm-12">
                        <b style="color:#000;">TEXTO</b>
                        <textarea id="text_adicional" class="form-control" style="height:300px;" onkeyup="$('#html_adicional').html($('#text_adicional').val());"></textarea>
                    </div>     
                    <div class="col-sm-12"><br></div>
                    <div class="col-sm-12">
                        <b style="color:#000;">NOTA:</b>
                        <p><b>Cuando se requiere colocar check se debe utilizar el siguiente código HTML</b></p>
                        <p>Ejemplo:</p>
                        <p>
                           WhatsApp (<input class="marcarx" type="text" name="" value="" title="WhatsApp" onclick="marcarx(this);" readonly="">)    
                            Zoom (<input class="marcarx" type="text" name="" value="" title="Zoom" onclick="marcarx(this);" readonly="">)       
                            Meet (<input class="marcarx" type="text" name="" value="" title="Meet" onclick="marcarx(this);" readonly="">) 
                            Correo (<input class="marcarx" type="text" name="" value="" title="Correo" onclick="marcarx(this);" readonly="">)   
                            Presencial (<input class="marcarx" type="text" name="" value="" title="Presencial" onclick="marcarx(this);" readonly="">)  
                        </p>
                        <input style="width:100%" value='<input class="marcarx" type="text" name="" value="" title="WhatsApp" onclick="marcarx(this);" readonly="">'></input>
                    </div>
                    <div class="col-sm-12"><br></div>
                    <div class="col-sm-12">
                        <p><b>Cuando se requiere colocar check excluyentes se debe utilizar el siguiente código HTML</b></p>
                        <p>Ejemplo:</p>
                        <p>
                           Presencial (<input class="marcarx formaatencion" type="text" name="" value="" title="Presencial" onclick="marcarx(this,'formaatencion');selhibrido();" readonly="">) 
                           Otros(<input class="marcarx formaatencion" type="text" name="" value="" title="Otros" onclick="marcarx(this,'formaatencion');selhibrido();" readonly="">)
                        </p>
                        <input style="width:100%" value='Presencial (<input class="marcarx formaatencion" type="text" name="" value="" title="Presencial" onclick="marcarx(this,formaatencion);" readonly="">)<br>Otros(<input class="marcarx formaatencion" type="text" name="" value="" title="Otros" onclick="marcarx(this,formaatencion);" readonly="">)'></input>
                    </div>
                    <div class="col-sm-12"><br><br><br><br><br><br><br><br><br><br><br><br><br></div>
                </div>
            </form> 
        </div>
    </div>
</div>

<script type="text/javascript">

function marcarx(athis,clase=false){
			if(clase) $("."+clase).val('');
			if($(athis).val()=='X'){
				$(athis).val('');
			}else{
				$(athis).val('X')
			}
	}
		
function selecanio(){
    var anio = $("#anio").val();
    var area = $("#area").val();
    
    $("#btn_guardar").prop('disabled',true);
    location.href = "{{route('crearficha')}}?anio="+anio+((area)?'&area='+area:'');
}

function selectboxficha(athis){
    if($(athis).prop('checked')){
        $('input[name=boxficha]').prop('checked',true);
    }else{
        $('input[name=boxficha]').prop('checked',false);
    }
}

function listar_html_adicional(idpregunta){
    ajax_data = {
      "idpregunta" : idpregunta,
      "alt"        : Math.random()
    }
    $.ajax({
            type: "GET",
            url: "{{route('listar_html_adicional')}}",
            data: ajax_data,
            dataType: "json",
            beforeSend: function(){
                  //imagen de carga
                  $("#login").html("<p align='center'><img src='./public/images/cargando.gif'/></p>");
            },
            error: function(){
                  alert("error peticiÃ³n ajax");
            },
            success: function(data){
                if(data){
                    $("#variables_adicional").html((data['varHtmlPre'])?'<b>variables:</b> '+data['varHtmlPre']+'<br>'+'<b>Titulos:</b> '+data['varTitPre']:'');
                    $("#text_adicional").val(data['htmlPre']);
                    $("#html_adicional").html(data['htmlPre']);
                }else{
                    $("#variables_adicional").html('');
                    $("#text_adicional").val('');
                    $("#html_adicional").html('');
                }
            }
      });

}

function PadLeft(value, length) {
    return (value.toString().length < length) ? PadLeft("0" + value, length) : 
    value;
}

function javasuma(idres,elementos){
    var ele = elementos.split(',');
    var total = 0;
    if(ele){
        for (var i = 0; i < ele.length; i++) {
            var inp = $("#div_htmladicional input[name="+ele[i]+"]").val();
            total = total + parseFloat((inp)?inp:'0');
        }
    }
    $("#div_htmladicional input[name="+idres+"]").val(total);
}

function anadirjavasuma(){
for (var nro = 1; nro <= 30; nro++) {
    var opt = '';
    if($("#div_htmladicional .stotal"+nro).length){
        $("#div_htmladicional .stotal"+nro).prop('readonly',false);
            var sum = [];
            opt = $("#div_htmladicional .stotal"+nro)[0].name;
            if($("#div_htmladicional .suma"+nro).length){
            for (var i = 0; i < $("#div_htmladicional .suma"+nro).length; i++) {
                sum.push($("#div_htmladicional .suma"+nro)[i].name);
            }
            var codigo = "javasuma('"+opt+"','"+sum.join()+"')";
            for (var i = 0; i < $("#div_htmladicional .suma"+nro).length; i++) {
                $($("#div_htmladicional .suma"+nro)[i]).attr('onkeyup',codigo);
            }
            $("#div_htmladicional .stotal"+nro).prop('readonly',true);
            }
            
        }
}
}

var var_input = [];

function guardar_html(){
    if($("#idpregunta option:selected").attr('opt')=="HTML"){
        guardar_html_cabecera();
    }else{
        guardar_html_adicional();
    }
}

function guardar_html_cabecera(){
    alert('guardar_html_cabecera');
}


function guardar_html_adicional(){
    var cabeza = $("#idpregunta").val();
    var elemento  = $("#html_adicional input,#html_adicional textarea,#html_adicional select");
    var var_html   = [];
    var var_titulo = [];
    var_input = [];
    for (var i = 0; i < elemento.length; i++) {
        var nombre = 'p'+PadLeft(cabeza,4)+'var'+PadLeft(i,4);
        elemento[i].name = nombre;
        if($(elemento[i]).hasClass('marcax')){ $(elemento[i]).attr('onclick','marcarx(this);'); $(elemento[i]).prop('readonly',true); }
    }

    anadirjavasuma();

    for (var i = 0; i < elemento.length; i++) {
        var_html  .push(elemento[i].name);
        var_titulo.push(elemento[i].title);
        var_input .push(elemento[i].outerHTML);
    }
    
    


    ajax_data = {
      "idficha"        : $("#idficha").val(),
      "idpregunta"     : $("#idpregunta").val(),
      "text_adicional" : $("#html_adicional").html(),
      "var_html"       : var_html.join(),
      "var_titulo"     : var_titulo.join(),
      "var_input"      : var_input.join('***'),
      "_token"         : $("#div_htmladicional input[name=_token]").val(),
      "alt"            : Math.random()
    }
    $.ajax({
            type: "POST",
            url: "{{route('guardar_html_adicional')}}",
            data: ajax_data,
            dataType: "json",
            beforeSend: function(){
                  //imagen de carga
                  $("#login").html("<p align='center'><img src='./public/images/cargando.gif'/></p>");
            },
            error: function(){
                  alert("error peticiÃ³n ajax");
            },
            success: function(data){
                alert('HTML adicional guardado');
            }
      });

}

function opciones_competencia(){
    
    $("#catalogo_nacional").html('');
    $("#competencias")     .html('');
    $("#avance").html('');
    $("#botones")          .html('');
    $(".elementos").css('display','none');
    $("#div_htmladicional").css('display','none');
    var opt = '';
    if(g_catalogo){
        opt += '<div class="col-sm-3" style=""><b style="font-size:14px;">Ficha:</b></div>';
        opt += '<div class="col-sm-5">';
        opt += '<select  id="idficha" class="form-control" onchange="listar_pregunta();">';
        opt += '<option value="">Selecione la ficha</option>';
        for (var i = 0; i < g_catalogo.length; i++) {
                opt += '<option value="'+g_catalogo[i]['idFic']+'">'+g_catalogo[i]['nomFic']+' '+'('+g_catalogo[i]['areaFic']+')'+'</option>';
        }
        opt += '</select>';
        opt += '</div>';
        opt += '<div class="col-sm-4" id="btn_pregunta" style="margin-top:12px;display:none;">';
        opt += '@csrf<button class="btn btn-success" onclick="guardar_pregunta();" id="btn_guardar">GUARDAR</button>&nbsp;&nbsp;&nbsp;';
        opt += '<button class="btn btn-info" onclick="mostrar_modelo_ficha();">Ver modelo ficha</button>&nbsp;&nbsp;&nbsp;';
        opt += '<a class="btn btn-danger" id="link_fichapdf" href="#" target="_blank">Ver modelo ficha PDF</a>';
        opt += '</div>';
    }
    $("#botones").html(opt);
}

function opciones_respuesta(){
    $("#div_htmladicional").css('display','none');
    $("#instituciones").css('display','none');
    $("#cargando").css('display','none');
    $("#catalogo_nacional").html('');
    $("#competencias")     .html('');
    $("#avance").html('');
    $("#botones")          .html('');
    var opt = '';
    if(g_catalogo){
        opt += '<div class="col-sm-3" style=""><b style="font-size:14px;">Ficha:</b></div>';
        opt += '<div class="row">';
        opt += '<div class="col-sm-5">';
        opt += '<select  id="idficha" class="form-control" onchange="ver_ficha_ie();">';
        opt += '<option value="">Selecione la ficha</option>';
        for (var i = 0; i < g_catalogo.length; i++) {
                opt += '<option value="'+g_catalogo[i]['idFic']+'">'+g_catalogo[i]['nomFic']+' '+'('+g_catalogo[i]['areaFic']+')'+'</option>';
        }
        opt += '</select>';
        opt += '</div>';
        opt += '<div class="col-sm-4">';
        opt += '<span class="btn btn-success" onclick="ver_ficha_ie();">Consultar</span>';
        opt += '<span id="btnpbi"></span>';
        opt += '</div>';
        opt += '</div>';
        
        opt += '<div class="col-sm-12" id="btn_pregunta" style="margin-top:12px;display:none;">';
        opt += '<a id="btn_anadirficha" class="btn btn-warning" href="#" onclick="">PROGRAMAR ASISTENCIA TECNICA</a>&nbsp;&nbsp;&nbsp;';
        opt += '<a id="btn_exportar_respuestas_ficha" class="btn btn-info"   target="_blank" href="#">Exportar respuestas completadas de la ficha</a>&nbsp;&nbsp;&nbsp;';
        opt += '<a id="btn_exportar_sustento_ficha"   class="btn btn-danger" target="_blank" href="#" onclick="link_exportar_sustento_ficha();">Exportar sustentos de las fichas</a>&nbsp;&nbsp;&nbsp;';
        <?php if($session['id_oficina']==77 or $session['idespecialista']==138 or  $session['idespecialista']==890  ){ ?> 
        opt += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        opt += '<a id="btn_generar_masa_pdf_ficha"   class="btn btn-danger" href="#" onclick="link_generar_masa_pdf_ficha();">Generar fichas</a>&nbsp;&nbsp;&nbsp;';
        <?php } ?>
        opt += '</div>';
    }
    $("#botones").html(opt);
}

function anadirficha(idFic){
  $("#popup01 .modal-content").load('{{route('popup_anadirfichaesp')}}?idFic='+idFic);
  $("#fc_popup").click();
}

function anadirfichaiiee(idFic){
  $("#popup01 .modal-content").load('{{route('popup_anadirfichaesp_iiee')}}?idFic='+idFic);
  $("#fc_popup").click();
}

function ver_ficha_ie(){
     ajax_data = {
      "idficha" : $("#idficha").val(),
      "alt"    : Math.random()
    }
    if($("#idficha").val()){
        $.ajax({
                type: "GET",
                url: "{{route('listar_ie_respuesta')}}",
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      $(".elementos").css('display','none');
                      $("#cargando").css('display','');
                      $("#btn_anadirficha").css('display','none');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                        var fichaculminada = 0;
                        var resumen     = data['resumensino'];
                        var resumenipl  = data['resumenipl'];
                        var institucion = data['institucion'];
                        var ficha       = data['ficha'];
                        $("#instituciones").css('display','');
                        $("#cargando").css('display','none');
                        $(".divresumen").css('display','none');
                        $("#btn_pregunta").css('display','');
                        if(ficha['tipFic']=='AL DIRECTIVO'){
                        $("#btn_anadirficha").css('display','');
                        $("#btn_anadirficha").attr('onclick','anadirficha('+$("#idficha").val()+');');
                        $("#btn_anadirficha").html('PROGRAMAR ASISTENCIA TECNICA');
                        
                        }
                        if(ficha['tipFic']=='A LA IIEE'){
                             $("#btn_anadirficha").css('display','');
                             $("#btn_anadirficha").attr('onclick','anadirfichaiiee('+$("#idficha").val()+');');
                             $("#btn_anadirficha").html('CREAR FICHA DE MONITOREO');
                        }
                        
                        $("#btn_exportar_respuestas_ficha").prop('href','{{route('exportar_respuestas_ficha')}}?idficha='+$("#idficha").val());
                        //$("#btn_exportar_sustento_ficha")  .prop('href','exportar_sustento_ficha?idficha='+$("#idficha").val());
                        $("#link_fichapdf").prop('href','mostrar_pdf_ficha?idficha='+$("#idficha").val());
                        table6.clear().draw();
                        table5.clear().draw();
                        table4.clear().draw();                        
                        if(institucion){
                        for (var i = 0; i < institucion.length; i++) {
                    		institucion[i]['nro']    = i+1;
                            institucion[i]['box']    = '<input type="checkbox" name="boxficha" value="'+institucion[i]['idRec']+'">';
                            institucion[i]['modFic'] = (institucion[i]['modFic']=='TODOS')?institucion[i]['nivRec']:institucion[i]['modFic'];
                            //<span style="font-size:15px;padding:2px;" class="btn btn-success" onclick="mostrar_modelo_ficha('+institucion[i]['idRec']+');">Visualizar</span>&nbsp&nbsp&nbsp
                            var fichapdf = (institucion[i]['fichapdf'])?institucion[i]['fichapdf']:'mostrar_pdf_ficha?idficha='+institucion[i]['idFic']+'&idreceptor='+institucion[i]['idRec'];
                    		institucion[i]['verficha'] = (institucion[i]['culRec']==1)?'<a class="btn btn-danger" style="font-size:15px;padding:2px;" target="_blank" href="'+fichapdf+'">Ver Pdf</a><b>COMPLETADO</b>':'<b>EN PROCESO</b>';
                            //institucion[i]['fichapdf']
                            institucion[i]['acceso_ficha'] = (institucion[i]['habilitado']=='1')?((false)?'':'<span style="font-size:15px;padding:2px;" class="btn btn-success" onclick="mostrar_ficha('+institucion[i]['idFic']+','+institucion[i]['idRec']+');">Registrar</span>'+'<span style="font-size:15px;padding:2px;" class="btn btn-danger" onclick="eliminar_ficha('+institucion[i]['idRec']+')">Eliminar</span>'):'CERRADO';
                            if(ficha['idFic']==6){ institucion[i]['acceso_ficha'] = '<span style="font-size:15px;padding:2px;" class="btn btn-danger" onclick="eliminar_ficha('+institucion[i]['idRec']+')">Eliminar</span>'; }
                            if(institucion[i]['culRec']==1){ fichaculminada++;}
                        }
                        $("#avance").html('<span style="color:green;font-weight: bold;float:right;font-size:22px;">Avance: '+(Math.round((fichaculminada/ficha['totRecFic'])*100*100)/100)+'%</span>');                        
                        if(resumenipl.length){
                            for (var i = 0; i < resumenipl.length; i++) {
                                resumenipl[i]['navance'] = Math.round((resumenipl[i]['logrado']/resumenipl[i]['total'])*100*100)/100 + '%';
                            }
                            $("#div_ipl") .css('display','');
                        }
                        if(resumen.length)   { $("#div_sino").css('display',''); }
                        table6.rows.add(resumenipl).draw();
                        table5.rows.add(resumen).draw();
                        table4.rows.add(institucion).draw();

                        }else{
                        $("#avance").html('');
                        }
                        $("#t_resumen tr td").css({'padding-top':'2px','padding-bottom':'2px','padding-right':'2px','padding-left':'2px',});
                        $("#btnpbi").html((ficha['pbiFic'])?'&nbsp;&nbsp;&nbsp;<a class="btn btn btn-warning" target="_blank" href="'+ficha['pbiFic']+'"><img src="assets/images/icopbi.png" width="30px"> Ver reporte</<a>':'');
                  }
          });
    }else{
        alert('Selecione una ficha');
        table4.clear().draw();
    }
}

function mostrar_ficha(idficha,idRec,codlocal=''){
    ajax_data = {
      "idficha"  : idficha,
      "idRec"    : idRec,
      "codlocal" : codlocal,
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

function eliminar_ficha(idRec){
 if(confirm('¿Esta seguro que desea eliminar la ficha?')){   
    ajax_data = {
      "idRec"    : idRec,
      "alt"      : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_ficha')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){
                          //imagen de carga
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                            alert('Ficha eliminada');
                            ver_ficha_ie();
                      }
              });
}
}

function link_exportar_sustento_ficha(){
    if($('input[name=boxficha]:checked').length){
    var opt = [];
    for (let i = 0; i < $('input[name=boxficha]:checked').length; i++) {
       opt.push($('input[name=boxficha]:checked')[i].value);        
    }
    $("#btn_exportar_sustento_ficha")  .prop('href','exportar_sustento_ficha?idficha='+$("#idficha").val()+'&idReceptor='+opt.join());
    }else{
    $("#btn_exportar_sustento_ficha")  .prop('href','exportar_sustento_ficha?idficha='+$("#idficha").val());
    }
}

function link_generar_masa_pdf_ficha(){
    if($('input[name=boxficha]:checked').length){
    var opt = [];
    for (let i = 0; i < $('input[name=boxficha]:checked').length; i++) {
       opt.push($('input[name=boxficha]:checked')[i].value);        
    }
    window.open('generar_masa_pdf_ficha?idficha='+$("#idficha").val()+'&idReceptor='+opt.join(), '_blank');
    //$("#btn_generar_masa_pdf_ficha")  .prop('href','generar_masa_pdf_ficha?idficha='+$("#idficha").val()+'&idReceptor='+opt.join());
    }else{
    alert('Selecione fichas');
    //window.open(URL, '_blank');
    //$("#btn_generar_masa_pdf_ficha")  .prop('href','generar_masa_pdf_ficha?idficha='+$("#idficha").val());
    }
}

var g_competencias = [];
function listar_pregunta(){
    g_row_modificado = [];
    ajax_data = {
      "idficha" : $("#idficha").val(),
      "alt"   : Math.random()
    }
    
    if( $("#idficha").val() ){
            $("#btn_pregunta").css('display','');
            $.ajax({
                type: "GET",
                url: "{{route('listar_pregunta')}}",
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
                    $("#div_htmladicional").css('display','');
                    $(".elementos").css('display','none');
                    $("#competencias").css('display','');
                    $("#link_fichapdf").prop('href','mostrar_pdf_ficha?idficha='+$("#idficha").val());
                    var competencia = data['competencia'];
                     reporte_catalogo = [];
                     var opt = '<option value="">Elija la pregunta</option>';//idpregunta
                        if(competencia.length){
                            for (var i = 0; i < competencia.length; i++) {
                            	var key = [];
                            	key.push(competencia[i]['idPre']);
                            	key.push((competencia[i]['estPre']==1)?'REGISTRADO':'');
                            	key.push(competencia[i]['ordPre']);
                                key.push(competencia[i]['SalLinPre']);
                            	key.push(competencia[i]['gruPre']);
                                key.push(competencia[i]['nroPre']);
                            	key.push(competencia[i]['textPre']);
                            	key.push(competencia[i]['tipPre']);
                            	key.push(competencia[i]['altPre']);
                            	key.push(competencia[i]['adjArcPre']);
                            	key.push(competencia[i]['camOblPre']);
                            	key.push(competencia[i]['obsPre']);
                            	key.push(competencia[i]['nroPreConPre']);
                            	//key.push(competencia[i]['htmlPre']);
                            	opt += (['TABLA','SI/NO','HTML'].indexOf(competencia[i]['tipPre'])>-1)?'<option style="'+((competencia[i]['varHtmlPre'])?'font-weight:bolder;':'')+'" value="'+competencia[i]['idPre']+'" opt="'+competencia[i]['tipPre']+'">'+competencia[i]['ordPre']+') '+competencia[i]['gruPre']+': '+competencia[i]['textPre']+'</option>':'';
                            	reporte_catalogo.push(key);
                            };
                            $("#idpregunta").html(opt);
                        }else{
                            reporte_catalogo = [['','REGISTRADO','','','','']];
                        }
                        //-------jexcel-------------------
                            g_competencias = [];
                            $("#competencias").html('');
                            g_competencias = jexcel(document.getElementById('competencias'), {
                            data:reporte_catalogo,
                            onchange:handler,
                            columns: [
                                {
                                    type: 'text',
                                    title:'idpregunta',
                                    width:1
                                },
                                {
                                    type: 'dropdown',
                                    title:'estado',
                                    width:90,
                                    source:[
                                        "REGISTRADO",
                                        "ELIMINAR",
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'orden',
                                    width:100
                                },
                                {
                                    type: 'checkbox',
                                    title:'Salto de linea',
                                    width:80
                                },
                                {
                                    type: 'text',
                                    title:'grupo',
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'N° pregunta',
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Pregunta',
                                    width:200
                                },
                                {
                                    type: 'dropdown',
                                    title:'tipo',
                                    width:100,
                                    source:[
                                        "SI/NO/NOAPLICA",
                                        "SI/NO",
                                        "SI/NO SIMPLE",
                                        "INICIO/PROCESO/LOGRADO",
                                        "NOAPLICA/INICIO/PROCESO/LOGRADO",
                                        "UNO O NINGUNO/POCOS/LA MAYORIA/TODOS",
                                        "INICIO/LOGRADO",
                                        "BUENO/REGULAR/MALO",
                                        "0/1/2/3/4",
                                        "1/2/3",
                                        "OPCION MULTIPLE",
                                        "OPCION UNICA",
                                        "TEXTO",
                                        "TEXTO CORTO",
                                        "NUMERO CORTO",
                                        "TABLA",
                                        "ARCHIVO",
                                        "HTML",
                                        "HTML CORTO",
                                        "ENCABEZADO",
                                      ]
                                },
                                {
                                    type: 'text',
                                    title:'alternativas',
                                    width:100
                                },
                                {
                                    type: 'checkbox',
                                    title:'adjuntar archivo',
                                    width:80
                                },
                                {
                                    type: 'checkbox',
                                    title:'Campo Obigatorio',
                                    width:80
                                },
                                {
                                    type: 'text',
                                    title:'observación',
                                    width:100
                                },
                                {
                                    type: 'text',
                                    title:'Nro de Preguntas condicionadas',
                                    width:100
                                },
                                /*{
                                    type: 'text',
                                    title:'html <br>(solo utilizar variables var1,var2,var3,var4,var5,var6 para input)',
                                    width:200
                                },*/
                             ]
                        });
                        //-------jexcel-------------------
                  }
            });
            setTimeout(ordenarjexcel, 3000);
    }else{
        $("#competencias").html('');
        $("#btn_pregunta").css('display','none');
        alert('Elija una ficha');
    }
    
}

   var g_row_modificado = [];
   handler = function(obj, cell, col, row, val) {
    if(g_row_modificado.indexOf(parseInt(row))==-1){ g_row_modificado.push(parseInt(row)); }
    }

function ordenarjexcel(){
$(".jexcel tbody tr td").css('white-space','nowrap');    
}

function guardar_pregunta(){
    
    var datos = [];
    var mdata = g_competencias.getData();
    for (var i = 0; i < mdata.length; i++) {
        if(g_row_modificado.indexOf(i)>-1){
        datos.push(mdata[i].join('||'));
        }
    };
    
    if(datos.length==0){ alert('No hay ninguna modificación'); return false; }
    
    if( $("#idficha").val() ){
    ajax_data = {
      "idficha" : $("#idficha").val(),
      "datos"   : datos.join('&&'),
      "_token"  : $("#formulario01 input[name=_token]").val(),
      "alt"     : Math.random()
    }
        $.ajax({
            type: "POST",
            url: "{{route('guardar_pregunta')}}",
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
                    listar_pregunta();
                    alert('Datos Guardados');
                    //toastr.info('Datos Guardados');
              }
        });
    }else{
        alert('Elija una ficha');
    }
}


var lista_catalogo = [];
var g_catalogo_nacional = [];
var g_catalogo    = <?=json_encode($catalogo)?>;
function guardar_ficha(){
    var g_datos_catalogo = [];
    var mdata = g_catalogo_nacional.getData();
    for (var i = 0; i < mdata.length; i++) {
            g_datos_catalogo.push(mdata[i].join('||'));
    };
    ajax_data = {
      "datos"  : g_datos_catalogo.join('&&'),
      "_token" : $("#formulario01 input[name=_token]").val(),
      "alt"    : Math.random()
    }
        $.ajax({
            type: "POST",
            url: "{{route('guardar_ficha')}}",
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
                    listar_ficha();
                    alert('Datos Guardados');
                    //toastr.info('Datos Guardados');
              }
        });
}

function listar_ficha(){
    
    $("#catalogo_nacional").html('');
    $("#competencias")     .html('');
    $("#avance").html('');
    $("#div_htmladicional").css('display','none');
    
    ajax_data = {
      "anio"  : $("#anio").val(),
      "area"  : $("#area").val(),
      "alt"   : Math.random()
    }
    $.ajax({
        type: "GET",
        url: "{{route('listar_ficha')}}",
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
            $("#catalogo_nacional").css('display','');
              
            g_catalogo = data['catalogo'];
             reporte_catalogo = [];
             $("#botones").html('@csrf<button class="btn btn-success" onclick="guardar_ficha();" id="btn_guardar">GUARDAR</button>');
                if(g_catalogo.length){
                    for (var i = 0; i < g_catalogo.length; i++) {
                    	var key = [];
                    	key.push(g_catalogo[i]['idFic']);
                    	key.push((g_catalogo[i]['estFic']==1)?'REGISTRADO':((g_catalogo[i]['estFic']==2)?'BORRADOR':''));
                    	key.push(g_catalogo[i]['nomFic']);
                    	key.push(g_catalogo[i]['desFic']);
                        key.push(g_catalogo[i]['totRecFic']);
                        key.push(g_catalogo[i]['areaFic']);
                    	key.push(g_catalogo[i]['iniFic']);
                    	key.push(g_catalogo[i]['finFic']);
                    	key.push(g_catalogo[i]['modFic']);
                    	key.push(g_catalogo[i]['gesFic']);
                    	key.push(g_catalogo[i]['tipFic']);
                    	key.push(g_catalogo[i]['decFic']);
                        key.push(g_catalogo[i]['DatGenFic']);
                        key.push(g_catalogo[i]['DocMonFic']);
                        key.push(g_catalogo[i]['genPdfFic']);
                        key.push(g_catalogo[i]['pbiFic']);
                    	reporte_catalogo.push(key);
                    };
                }else{
                    reporte_catalogo = [['','REGISTRADO','','','','','','','','']];
                }
                //-------jexcel-------------------
                    g_catalogo_nacional = [];
                    $("#catalogo_nacional").html('');
                    g_catalogo_nacional = jexcel(document.getElementById('catalogo_nacional'), {
                    data:reporte_catalogo,
                    columns: [
                        {
                            type: 'text',
                            title:'idficha',
                            color:'red',
                            width:1
                        },
                        {
                            type: 'dropdown',
                            title:'estado',
                            width:90,
                            source:[
                                "REGISTRADO",
                                "BORRADOR",
                                "ELIMINAR",
                              ]
                        },
                        {
                            type: 'text',
                            title:'nombre',
                            width:250
                        },
                        {
                            type: 'text',
                            title:'descripcion',
                            width:250
                        },
                        {
                            type: 'text',
                            title:'Total de receptores esperados',
                            width:200
                        },
                        {
                            type: 'text',
                            title:'Area',
                            width:100
                        },
                        {
                            type: 'calendar',
                            title:'fecha inicio',
                            width:100
                        },
                        {
                            type: 'calendar',
                            title:'fecha fin',
                            width:100
                        },
                        {
                            type: 'dropdown',
                            title:'modalidad',
                            width:100,
                            source:[
                                "TODOS",
                                "CETPRO",
                                "EBA",
                                "EBE",
                                "EBR",
                                "EBR Inicial",
                                "EBR Primaria",
                                "EBR Secundaria",
                                "EBA Inicial Intermedio",
                                "EBA Avanzado",
                              ]
                        },
                        {
                            type: 'dropdown',
                            title:'gestion',
                            width:100,
                            source:[
                                "TODOS",
                                "ESTATAL",
                                "PARTICULAR",
                              ]
                        },
                        {
                            type: 'dropdown',
                            title:'Tipo de ficha',
                            width:100,
                            source:[
                                "FICHA",
                                 "ACOMPAÑAMIENTO",
                                "DIRECTIVO AL DOCENTE",
                                "AL DIRECTIVO",
                                "A LA IIEE",
                              ]
                        },
                        {
                            type: 'checkbox',
                            title:'Todas las preguntas son declarativas',
                            width:90
                        },
                        {
                            type: 'checkbox',
                            title:'Datos generales en PDF',
                            width:90
                        },
                        {
                            type: 'checkbox',
                            title:'Docente monitoreado',
                            width:90
                        },
                        {
                            type: 'checkbox',
                            title:'Generar PDF manual',
                            width:90
                        },
                        {
                            type: 'text',
                            title:'Reporte PBI',
                            width:250
                        },
                     ]
                });
                
                //-------jexcel-------------------
          }
    });
}

$('input[name=selector]:checked').click();

$("#catalogo_nacional").bind("contextmenu",function(e){
    return false;
});

/*$("#competencias").bind("contextmenu",function(e){
    return false;
});*/

function mostrar_modelo_ficha(idreceptor=0){
    ajax_data = {
      "idficha"    : $("#idficha").val(),
      "idreceptor" : idreceptor,
      "alt"        : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('mostrar_modelo_ficha')}}",
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

var table4 = $("#t_instituciones").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "box" }, 
                            { "data": "codlocRec" },
                            //{ "data": "codmod" },
                            { "data": "redRec" },
                            { "data": "insRec" },
                            { "data": "textModalidadRec" },
                            { "data": "disRec" },
                            { "data": "dniRec" },
                            { "data": "dirRec" },
                            { "data": "telRec" },
                            { "data": "corRec" },
                            { "data": "fechaficha" },                            
                            { "data": "verficha" },
                            { "data": "acceso_ficha" },
                            { "data": "especialista" },
                            { "data": "nroVisRec" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });
                    
var table5 = $("#t_resumen").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "ordPre" },
                            { "data": "gruPre" }, 
                            { "data": "nroPre" },
                            { "data": "textPre" },
                            { "data": "res_si" },
                            { "data": "res_no" },
                            { "data": "porc_si" },
                            { "data": "porc_no" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

 var table6 = $("#t_resumenipl").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "gruPre" },
                            { "data": "cantidad" }, 
                            { "data": "inicio" },
                            { "data": "proceso" },
                            { "data": "logrado" },
                            { "data": "navance" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });
</script>

<script>
    $(".app-container").removeClass('fixed-header');
</script>

@endsection

<div id="Modalficha" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="width:90%;">
    <div class="modal-content"></div>
  </div>
</div>
<div id="fc_ficha"  data-toggle="modal" data-target="#Modalficha"></div>








