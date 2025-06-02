<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .anadirdest{
            background-color: green;
            color: #fff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
      <div class="modal-header">
      <b style="font-size:22px;">Seleccionar destinatarios y documentos</b>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
      </button>
    </div>
    <div class="modal-body">
    @csrf
    <div style="font-size:20px;font-weight:bolder;">Verificar si el documento ya fue comunicado / notificado antes de enviarlo.</div>
    <br>
    
    <div class="row">
    <div class="col-sm-2" style="text-align:right;padding-top:8px;"><b>Tipo de correo:</b></div>
    <div class="col-sm-4">
        <select class="form-control" id="idmantenimiento">
            <option value="">ELIJA EL TIPO DE NOTIFICACIÓN</option>
            <option value="1152">RESOLUCIÓN DE CONTRATO</option>
            <option value="932">COMUNICAR</option>
            <option value="46">NOTIFICACION DE ATENCION DE EXPEDIENTE</option>
        </select>
    </div>
    <div class="col-sm-6"></div>
    </div>
    
    
    <br>
    <div class="table-responsive">
        <table id="t_docnotificar" class="table table-bordered table-striped table-hover">
            <thead>
                <tr style="background-color:Chocolate;">
                    <th style="color:#fff;">#</th>
                    <th style="color:#fff;">Tipo</th>
                    <th style="color:#fff;">Numero Expediente</th>
                    <th style="color:#fff;">Resumen Pedido</th>
                    <th style="color:#fff;">Destinatario</th>
                    <th style="color:#fff;">Comunicado Notificado</th>
                    <th style="color:#fff;">Documentos a notificar</th>
                    <th style="color:#fff;">Recibido</th>
                    <th style="color:#fff;">Leido</th>
                    <th style="color:#fff;">Destinatario</th>
                    <th style="color:#fff;">Celular</th>
                    <th style="color:#fff;">Correo</th>
                    
                </tr>
            </thead>
            <tbody id=""><tr style="border-top: 2px solid black;">
            <!--
                <tr>
                    <td rowspan="1" style="vertical-align: middle;">1</td>
                    <td style="vertical-align: middle;" rowspan="1"></td>
                    <td style="vertical-align: middle;" rowspan="1">MPT2020-EXT-0078346</td>
                    <td style="vertical-align: middle;" rowspan="1">SUBSIDIO POR LUTO Y SEPELIO POR FALLECIMIENTO DEL SERVIDOR</td>
                    <td><input type="checkbox" onchange="validarCheck();" id="CH1613096185" class="checkFirmas CHE1" texto="0" name="archivosFirma[]" value="1613096185_RD-2020-10077 (1).pdf"> Resolución Directoral</td>
                    <td id="ES1613096185">Sin Firma</td>
                    <td id="FI1613096185"></td>
                    <td></td>
                    <td></td>
                    <td><a id="RU1613096185" href="https://siic01.ugel01.gob.pe//cargados/1613096185_RD-2020-10077 (1).pdf?a=0.7350317274938485" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a></td>
                </tr>
            -->
            <?php
            //'<span class="anadirdest">'+athis.value+' <input class="iexp'+nro+'_receptor" name="idreceptor'+nro+'[]" value="'+g_listadestinatario[i]['idreceptor']+'" style="display:none;">  <b title="Eliminar" onclick="eliminarb(this)" style="color:red;cursor: pointer;">X</b>&nbsp;&nbsp;</span>'+' '
            function html_destinatario($nro=0,$tipodocumento,$nomcompleto=false,$idreceptor=false){
                $opt  = '';
                $opt .= '<b>Buscar:</b>';
                $opt .= '<input list="destinatario'.$nro.'" onkeyup="if (window.event.keyCode==13){buscarciudadano(this);}" onchange="anadir_destinatario(this,'.$nro.');" autocomplete="off" name="destinatario'.$nro.'" type="text" placeholder="Escriba el nombre y presione enter">';
                $opt .= '<datalist id="destinatario'.$nro.'">';
                $opt .= '</datalist>';
                $opt .= '<div class="texto_destinatario">';
                if($tipodocumento!='OFICINA'){
                $opt .= '<span class="anadirdest">'.$nomcompleto;
                $opt .= '<input class="iexp'.$nro.'_receptor"    name="idreceptor'.$nro.'[]" value="'.$idreceptor.'" style="display:none;">';
                $opt .= '<input class="iexp'.$nro.'_institucion" name="institucion'.$nro.'[]" value="" style="display:none;">';
                $opt .= '<input class="iexp'.$nro.'_codlocal"    name="codlocal'.$nro.'[]" value="" style="display:none;">';
                $opt .= '<input class="iexp'.$nro.'_distrito"    name="distrito'.$nro.'[]" value="" style="display:none;">';
                $opt .= '<b title="Eliminar" onclick="eliminarb(this)" style="color:red;cursor: pointer;">X</b>&nbsp;&nbsp;</span> ';    
                }
                $opt .= '</div>';
                return $opt;
            }

            function acuserecibido($casilla){
                return ($casilla['acuse_recibido'])?' <a target="_blank" class="fa-file-pdf-o" href="https://siic01.ugel01.gob.pe/index.php/notificacion/acuse/'.$casilla['id_casilla_detalle'].'/2"></a>':'';
            }

            function acuseleido($casilla){
                return ($casilla['acuse_leido'])?' <a target="_blank" class="fa-file-pdf-o" href="https://siic01.ugel01.gob.pe/index.php/notificacion/acuse/'.$casilla['id_casilla_detalle'].'/3"></a>':'';
            }
            ?>
                <?php
                if($data){
                    for ($i=0; $i < count($data); $i++) {
                    $exp = $data[$i];
                    $disabled = ($exp['tipodocumento']!='OFICINA')?'':'disabled';
                ?>
                    <tr>
                        <td style="vertical-align: middle;" rowspan="<?=($exp['nrow'])?$exp['nrow']:1?>" style="vertical-align: middle;"><?=$i+1?></td>
                        <td style="vertical-align: middle;" rowspan="<?=($exp['nrow'])?$exp['nrow']:1?>"><?=$exp['tipo_exp']?></td>
                        <td style="vertical-align: middle;" rowspan="<?=($exp['nrow'])?$exp['nrow']:1?>"><?=$exp['cod_reclamo']?><input type="text" class="expanotificar" value="<?=$exp['idreclamo']?>" name="iexp<?=$i?>" style="display:none;"> <span class="cargando"></span> </td>
                        <td style="vertical-align: middle;" rowspan="<?=($exp['nrow'])?$exp['nrow']:1?>"><?=$exp['resumen_pedido']?></td>
                        <td style="vertical-align: middle;" rowspan="<?=($exp['nrow'])?$exp['nrow']:1?>"><?=($exp['doc'])?html_destinatario($i,$exp['tipodocumento'],$exp['nombres'].' '.$exp['apellido_paterno'].' '.$exp['apellido_materno'],$exp['idreceptor']):'Sin archivo adjunto'?></td>
                        <?php
                        
                        $doc = $exp['doc'];
                        $casilla_detalle = false;
                        if($doc){
                        //Mostar la primera fila de los documentos adjuntos
                        $casilla_detalle = $doc[0]['casilla_detalle'];
                        ?>
                            <!--<td rowspan="1" style="vertical-align: middle;">doc <?=1?></td>-->
                            <td rowspan="<?=($casilla_detalle)?count($casilla_detalle):1?>"> <div style="text-align: center;font-weight:bolder;font-size:20px;"><?=($casilla_detalle)?'SI':'NO'?></div></td>
                            <td rowspan="<?=($casilla_detalle)?count($casilla_detalle):1?>" style="vertical-align: middle;">
                                <input class="iexp<?=$i?>_doc" type="checkbox" value="<?=$doc[0]['iddocumento']?>" <?=$disabled?>> <?=$doc[0]['nombre']?>
                                <a id="RU1613096185" href="<?=$doc[0]['archivo']?>" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a>
                            </td>
                            
                            <td><?=($casilla_detalle)?$casilla_detalle[0]['t_acuse_recibido'].acuserecibido($casilla_detalle[0]):'--'?></td>
                            <td><?=($casilla_detalle)?$casilla_detalle[0]['t_acuse_leido'].acuseleido($casilla_detalle[0]):'--'?></td>

                            <td><?=($casilla_detalle)?(($casilla_detalle[0]['ieinstitucion'])?$casilla_detalle[0]['iecodlocal'].' '.$casilla_detalle[0]['ieinstitucion'].': ':'').$casilla_detalle[0]['nombres'].' '.$casilla_detalle[0]['apellido_paterno'].' '.$casilla_detalle[0]['apellido_materno']:'--'?></td>
                            <td><?=($casilla_detalle)?$casilla_detalle[0]['celular']:'--'?></td>
                            <td><?=($casilla_detalle)?$casilla_detalle[0]['correo']:'--'?></td>
                        <?php
                        //Mostar la primera fila de los documentos adjuntos
                        }else{
                        ?>
                        <td rowspan="1" style="vertical-align: middle;">Sin archivo adjunto</td>
                        <td rowspan="1" style="vertical-align: middle;">--</td>
                        <td rowspan="1" style="vertical-align: middle;">--</td>
                        <td rowspan="1" style="vertical-align: middle;">--</td>
                        <td rowspan="1" style="vertical-align: middle;">--</td>
                        <td rowspan="1" style="vertical-align: middle;">--</td>
                        <td rowspan="1" style="vertical-align: middle;">--</td>
                        <?php
                        }
                        ?>
                    </tr>
                    <?php
                    //Casilla detalle
                    if($casilla_detalle){
                            for ($z=1; $z < count($casilla_detalle); $z++) {
                        ?>
                            <tr>
                                <td> <?=($casilla_detalle)?$casilla_detalle[$z]['t_acuse_recibido'].acuserecibido($casilla_detalle[$z]):'--'?></td>
                                <td> <?=($casilla_detalle)?$casilla_detalle[$z]['t_acuse_leido'].acuseleido($casilla_detalle[$z]):'--'?></td>

                                <td><?=($casilla_detalle)?(($casilla_detalle[$z]['ieinstitucion'])?$casilla_detalle[$z]['iecodlocal'].' '.$casilla_detalle[$z]['ieinstitucion'].': ':'').$casilla_detalle[$z]['nombres'].' '.$casilla_detalle[$z]['apellido_paterno'].' '.$casilla_detalle[$z]['apellido_materno']:'--'?></td>
                                <td><?=($casilla_detalle)?$casilla_detalle[$z]['celular']:'--'?></td>
                                <td><?=($casilla_detalle)?$casilla_detalle[$z]['correo'] :'--'?></td>

                            </tr>
                        <?php
                            }
                        }
                    //Casilla detalle
                    ?>

                    <?php
                        
                        if(count($doc)>1){
                        //Mostar la siguinetes fila de los documentos adjuntos
                        for ($k=1; $k < count($doc); $k++) {
                            $casilla_detalle = $doc[$k]['casilla_detalle'];
                        ?>
                        <tr>
                            <td rowspan="<?=($casilla_detalle)?count($casilla_detalle):1?>"> <div style="text-align: center;font-weight:bolder;font-size:20px;"><?=($casilla_detalle)?'SI':'NO'?></div> </td>
                            <td rowspan="<?=($casilla_detalle)?count($casilla_detalle):1?>" style="vertical-align: middle;">
                                <input class="iexp<?=$i?>_doc" type="checkbox" value="<?=$doc[$k]['iddocumento']?>" <?=$disabled?>> <?=$doc[$k]['nombre']?>
                                <a id="RU1613096185" href="<?=$doc[$k]['archivo']?>" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a>
                            </td>
                            
                            <td rowspan=""><?=($casilla_detalle)?$casilla_detalle[0]['t_acuse_recibido'].acuserecibido($casilla_detalle[0]):'--'?></td>
                            <td rowspan=""><?=($casilla_detalle)?$casilla_detalle[0]['t_acuse_leido'].acuseleido($casilla_detalle[0]):'--'?></td>

                            <td><?=($casilla_detalle)?(($casilla_detalle[0]['ieinstitucion'])?$casilla_detalle[0]['iecodlocal'].' '.$casilla_detalle[0]['ieinstitucion'].': ':'').$casilla_detalle[0]['nombres'].' '.$casilla_detalle[0]['apellido_paterno'].' '.$casilla_detalle[0]['apellido_materno']:'--'?></td>
                            <td><?=($casilla_detalle)?$casilla_detalle[0]['celular']:'--'?></td>
                            <td><?=($casilla_detalle)?$casilla_detalle[0]['correo'] :'--'?></td>

                        </tr>
                        <?php
                        //Casilla detalle
                        if($casilla_detalle){
                            for ($z=1; $z < count($casilla_detalle); $z++) {
                        ?>
                            <tr>
                                <td> <?=($casilla_detalle)?$casilla_detalle[$z]['t_acuse_recibido'].acuserecibido($casilla_detalle[$z]):'--'?></td>
                                <td> <?=($casilla_detalle)?$casilla_detalle[$z]['t_acuse_leido'].acuseleido($casilla_detalle[$z]):'--'?></td>

                                <td><?=($casilla_detalle)?(($casilla_detalle[$z]['ieinstitucion'])?$casilla_detalle[$z]['iecodlocal'].' '.$casilla_detalle[$z]['ieinstitucion'].': ':'').$casilla_detalle[$z]['nombres'].' '.$casilla_detalle[$z]['apellido_paterno'].' '.$casilla_detalle[$z]['apellido_materno']:'--'?></td>
                                <td><?=($casilla_detalle)?$casilla_detalle[$z]['celular']:'--'?></td>
                                <td><?=($casilla_detalle)?$casilla_detalle[$z]['correo'] :'--'?></td>

                            </tr>
                        <?php
                            }
                        }
                        //Casilla detalle
                        }
                        //Mostar la siguinetes fila de los documentos adjuntos
                        }
                    ?>
                <?php
                    }
                }
                @csrf
                ?>

                
            </tbody>
        </table>
    </div> 
    </div>
    <br clear="all">
    <div class="modal-footer">
        
      <button class="btn btn-success" id="btn_notificararreglo" onclick="creararregloaguardar();">Enviar documentos seleccionados</button>
      <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
    </div>

</body>
</html>

<script type="text/javascript">
var g_this;
function buscarciudadano(athis){
    g_this = athis;
    
    ajax_data = {
      "valor"  : athis.value.replace('ie ','IE '),
      "alt"    : Math.random()
    }
    if(athis.value.length>3){

        $(athis).val(athis.value.replace('IE ',''));
        $(athis).val(athis.value.replace('ie ',''));
        $.ajax({
                        type: "get",
                        url: "{{route('buscarciudadanoprueba')}}",
                        data: ajax_data,
                        dataType: "json",
                        beforeSend: function(){
                            //$(g_this).prop('disabled',true);
                        },
                        error: function(){
                            alert("error peticiÃ³n ajax");
                        },
                        success: function(data){
                            //$(g_this).prop('disabled',false);
                            var opt = '';
                            g_listadestinatario = data;
                            if(data.length>0){
                                for (let i = 0; i < data.length; i++) {
                                    opt += '<option value="'+data[i]['nombrecompleto']+'"></option>';                                    
                                }
                            }else{
                                alert('No se encontro ciudadanos, escriba otro apellido.');
                            }
                            
                            
                            $(g_this).parent().children('datalist').html(opt);
                            
                        }
                });
    }
}

var g_listadestinatario = [];
function anadir_destinatario(athis,nro){
    g_this = athis;
    if(g_listadestinatario){
        for (let i = 0; i < g_listadestinatario.length; i++) {
                if(athis.value == g_listadestinatario[i]['nombrecompleto']){
                    var opt = '<span class="anadirdest">'+athis.value;
                        opt += ' <input class="iexp'+nro+'_receptor"    name="idreceptor'+nro+'[]" value="'+g_listadestinatario[i]['idreceptor']+'" style="display:none;">';
                        opt += ' <input class="iexp'+nro+'_institucion" name="institucion'+nro+'[]" value="'+((g_listadestinatario[i]['institucion'])?g_listadestinatario[i]['institucion']:'')+'" style="display:none;">';
                        opt += ' <input class="iexp'+nro+'_codlocal"    name="codlocal'+nro+'[]" value="'+((g_listadestinatario[i]['codlocal'])?g_listadestinatario[i]['codlocal']:'')+'" style="display:none;">';
                        opt += ' <input class="iexp'+nro+'_distrito"    name="distrito'+nro+'[]" value="'+((g_listadestinatario[i]['distrito'])?g_listadestinatario[i]['distrito']:'')+'" style="display:none;">';
                        opt += '<b title="Eliminar" onclick="eliminarb(this)" style="color:red;cursor: pointer;">X</b>&nbsp;&nbsp;</span> ';
                    $(g_this).parent().children('.texto_destinatario').append(opt);
                    $(athis).val('');
                    $('.iexp'+nro+'_doc').prop('disabled',false);
                }
            
        }
            

    }
    //lista_destinatario
}

var lista_idreclamo = [];
var arregloaguardar = [];
function creararregloaguardar(){
var idmantenimiento = $("#idmantenimiento").val();
if(idmantenimiento){
    if(confirm('Verifique si este expediente ya ha sido comunicado/notificado')){
        arregloaguardar = [];
        lista_idreclamo = [];
        
        var arreglo = $(".expanotificar");
        for (let i = 0; i < arreglo.length; i++) {
            lista_idreclamo.push(arreglo[i].value);
            var opt  = arreglo[i].name;
                
                for (let j = 0; j < $("."+opt+"_receptor").length; j++){
                    var receptor = [];
                    var institucion = [];
                    var codlocal = [];
                    var distrito = [];
                    receptor.push($("."+opt+"_receptor")[j].value);
                    institucion.push($("."+opt+"_institucion")[j].value);
                    codlocal.push($("."+opt+"_codlocal")[j].value);
                    distrito.push($("."+opt+"_distrito")[j].value);
                    
                    var doc = [];
                    for (let j = 0; j < $("."+opt+"_doc:checked").length; j++) doc.push($("."+opt+"_doc:checked")[j].value);
    
                    if(arreglo[i].value>0 && receptor.length>0 && doc.length>0){
                    var fila = {'idreclamo':arreglo[i].value,'opt':opt,'receptor':receptor,'institucion':institucion,'codlocal':codlocal,'distrito':distrito,'doc':doc,'idmantenimiento':idmantenimiento,'idespecialista':<?=$idespecialista?>}
                    arregloaguardar.push(fila);
                    }
                }
            
        }
    
        if(arregloaguardar.length>0){
            notificararregloaguardar();
        }
    }
}else{
    alert('Elija el tipo de notificación');
}
}

function notificararregloaguardar(nro=0){
    $("#btn_notificararreglo").prop('disabled',true);
    if(arregloaguardar.length>nro){
        $.ajax({
                    type: "get",
                    url: "{{route('notificarciudadanoprueba')}}",
                    data: arregloaguardar[nro],
                    dataType: "html",
                    beforeSend: function(){
                        //imagen de carga
                        $("input[name="+arregloaguardar[nro]['opt']+"]").parent().children('.cargando').html('<br><img width="30px" src="<?=asset('assets/images/3.gif')?>"><b style="color:red;">Cargando...</b>');
                    },
                    error: function(){
                        alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                        //alert(nro);
                        $("input[name="+arregloaguardar[nro]['opt']+"]").parent().children('.cargando').html('<b style="color:green;">Notificado</b>');
                        notificararregloaguardar(nro+1);
                        
                    }
                });
    }else{
        alert('Expedientes notificados');
        $("#btn_notificararreglo").prop('disabled',false);
        //Recargar el listado de expedientes, destinatarios y notificaciones realizadas
        $("#notificacionMasivo .modal-content").load('https://aplicacion.ugel01.gob.pe/public/buscarMultipleNotificacionesprueba?idespecialista=<?=$idespecialista?>&idreclamo='+lista_idreclamo.join());
        jaaf_padding('t_docnotificar');
    }
}

function eliminarb(athis){
    $(athis).parent().remove();
}
function jaaf_padding(id=''){
    $("#"+id+" tr td").css({'padding-top':'4px','padding-bottom':'1px','padding-righ':'1px','padding-left':'3px','font-size':'11px'});
    //$(".paginate_button").attr('onclick',"jaaf_padding()");
}

jaaf_padding('t_docnotificar');
window.setTimeout(function(){
    //alert('Verificar si el documento ya fue comunicado / notificado antes de enviarlo');
}, 500);
</script>
