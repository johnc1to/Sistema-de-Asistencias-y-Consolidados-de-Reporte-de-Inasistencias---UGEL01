<html>
<head>
<style>
/*top right bottom left*/
/*margin: 90px es el tamaño del header*/
  @page { margin: 97px 35px 35px 35px; }
  #header { position: fixed; left: 0px; top: -104px; right: 0px; height: 200px;  text-align: center; }
  #footer { position: fixed; left: 0px; bottom: -10px; right: 0px; height: 20px;background-color: #fff;text-align: center;  }
  #footer .page:after { content: counter(page, upper-arial);}
  .l-top{border-top:0.3px solid #000;}
  .l-right{border-right:0.3px solid #000;}
  .l-left{border-left:0.3px solid #000;}
  .l-bottom{border-bottom:0.3px solid #000;}
  .l-border{border:0.8px solid #000;}
  .saltopagina {page-break-after:always;}
  table tr td { padding-right: 6px;padding-left: 6px;font-size:12px;}
  .titulo { font-size:12px; }
  .cabezera{font-weight: bold;text-align:center; background-color:rgb(217,217,217);}
  .cuerpo{text-align:center;}
  .nivellogro{font-weight: bold;text-align:center;}
  .negritacentrado { font-weight: bold;text-align:center; }
  .gris { background-color:rgb(217,217,217); }
  .centro { text-align:center;  }
  .izquierda { text-align:left;padding-left:6px;  }
  .resumentotal{ font-weight: bold;}
</style>
<body>
  <div id="header">
    <h1><img style="width: 75%;" src="<?=$ficha['banFic']?>"></h1>
  </div>
  <div id="footer">
    <p class="page">Pag. </p>
  </div>
  <div id="content">
    <div style="text-align: center;font-size:17px;font-weight: bolder;"><?=$ficha['nomFic']?></div>
    <div style="text-align: center;font-size:12px;"><?=($ficha['desFic']=='-')?'':$ficha['desFic']?></div>

    <?php
    /*function nivel($idnivel){
        $nivel = '';
        switch ($idnivel) {
            case '1': $nivel='CETPRO';break;
            case '2': $nivel='EBE';break;
            case '3': $nivel='Secundaria';break;
            case '4': $nivel='Primaria';break;
            case '5': $nivel='Inicial';break;
            case '6': $nivel='Eba Inicial Intermedio';break;
            case '7': $nivel='Eba Avanzado';break;
            default:$nivel='';break;
        }
        return $nivel;
    }*/

    $romano = array('I','II','III','IV','V','VI','VII','VIII','XI','X');
    ?>

    <?php
    $nro=0;
    if($ficha['DatGenFic']){
    ?>
    <b class="titulo"><?=$romano[$nro++]?>.	DATOS GENERALES DE LA INSTITUCIÓN:</b>
    <?php
    if($ficha['htmlDatGenFic']){
        if($registro){
        $ficha['htmlDatGenFic'] = str_replace('|disRec|',$registro['disRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('|redRec|',$registro['redRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('|codlocRec|',$registro['codlocRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('|insRec|',$registro['insRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('|textModalidadRec|',$registro['textModalidadRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('|textNivelesRec|',$registro['textNivelesRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace('|turno|',$registro['turnoRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|fechaficha|",$registro['fechaProgramada'],$ficha['htmlDatGenFic']);
        
        $ficha['htmlDatGenFic'] = str_replace("|nroTerCar|",$registro['nroTerCar'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|nroCuaCar|",$registro['nroCuaCar'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|nroQuiCar|",$registro['nroQuiCar'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|nroManCar|",$registro['nroManCar'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|nroTarCar|",$registro['nroTarCar'],$ficha['htmlDatGenFic']);
        
        $ficha['htmlDatGenFic'] = str_replace("|nomRec|",$registro['nomRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|apePatRec|",$registro['apePatRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|apeMatRec|",$registro['apeMatRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|dniRec|",$registro['dniRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|telRec|",$registro['telRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|corRec|",$registro['corRec'],$ficha['htmlDatGenFic']);
        $ficha['htmlDatGenFic'] = str_replace("|carRec|",$registro['carRec'],$ficha['htmlDatGenFic']);

        }
        echo $ficha['htmlDatGenFic'];
    }else{
    ?>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr class="cabezera">
            <td colspan="4">Número y/o nombre de la I.E.</td>
            <td colspan="2">Código Local</td>
            <td colspan="2">Código Modular</td>
        </tr>
        <tr>
            <td colspan="4"><?=($registro)?$registro['insRec']:'-'?></td>
            <td colspan="2"><?=($registro)?$registro['codlocRec']:''?></td>
            <td colspan="2"><?=($registro)?$registro['codmodRec']:''?></td>
        </tr>
        <tr>
            <td class="cabezera">Región</td>
            <td>Lima Metropolitana</td>
            <td class="cabezera">Distrito</td>
            <td><?=($registro)?$registro['disRec']:''?></td>
            <td class="cabezera">UGEL</td>
            <td>UGEL 01</td>
            <td class="cabezera">REI</td>
            <td><?=($registro)?$registro['redRec']:''?></td>
        </tr>
    </table>
    <br>
    <b class="titulo"><?=$romano[$nro++]?>.	DATOS DEL DIRECTIVO:</b>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr class="cabezera">
            <td colspan="2">Nombres Completos</td>
            <td colspan="2">Apellidos Completos</td>
            <td colspan="2">Documento de Identidad</td>
        </tr>
        <tr>
            <td colspan="2"><?=($registro)?$registro['nomRec']:'-'?></td>
            <td colspan="2"><?=($registro)?$registro['apePatRec'].' '.$registro['apeMatRec']:''?></td>
            <td colspan="2"><?=($registro)?$registro['dniRec']:''?></td>
        </tr>
        <tr>
            <td class="cabezera">Teléfono</td>
            <td><?=($registro)?$registro['telRec']:''?></td>
            <td class="cabezera">Correo electrónico</td>
            <td><?=($registro)?$registro['corRec']:''?></td>
            <td class="cabezera">Cargo</td>
            <td><?=($registro)?$registro['carRec']:''?></td>
        </tr>
    </table>
    <?php
    }
    ?>
    
    <br>
    <!--
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr>
            <td class="cabezera">DISTRITO</td>
            <td>disRec</td>
            <td class="cabezera">Modalidad</td>
            <td></td>
        </tr>
        <tr>
            <td class="cabezera">RED EDUCATIVA</td>
            <td>redRec</td>
            <td rowspan="2" class="cabezera">Niveles educativos</td>
            <td rowspan="2"></td>
        </tr>
        <tr>
            <td class="cabezera">Código de local N.°</td>
            <td>codlocRec</td>
        </tr>
        <tr>
            <td colspan="2" class="cabezera">Nombre de la institución educativa</td>
            <td colspan="2">insRec</td>
        </tr>
    </table>
    -->
    
    <?php
    }
    ?>

    <?php
    if($ficha['tipFic']=='AL DIRECTIVO'){
    ?>    
    <b class="titulo"><?=$romano[$nro++]?>.	DATOS DEL ESPECIALISTA:</b>
    <br>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr class="cabezera">
            <td colspan="2">Nombres completos del especialista</td>
            <td colspan="2">Apellidos completos del especialista</td>
            <td colspan="2">Documento de Identidad</td>
        </tr>
        <tr>
            <td colspan="2"><?=($registro)?$registro['esp_nombres']:'-'?></td>
            <td colspan="2"><?=($registro)?$registro['esp_apellido_paterno']:'-'?> <?=($registro)?$registro['esp_apellido_materno']:''?></td>
            <td colspan="2"><?=($registro)?$registro['ddni']:'-'?></td>
        </tr>
        <tr>
            <td>Número de visita a la IE</td>
            <td><?=($registro)?$registro['nroVisRec']:''?></td>
            <td>Fecha de aplicación</td>
            <td><?=($registro)?date_format(date_create($registro['fechaaplicacion']),"d/m/Y"):''?></td>
            <td>Hora de inicio/fin</td>
            <td><?=($registro)?(($registro['horainicioaplicacion'])?$registro['horainicioaplicacion'].' a ':''):''?><?=($registro)?$registro['horaaplicacion']:''?></td>
        </tr>
        <tr>
            <td colspan="2">Medio por el cual se desarrolla la asistencia técnica</td>
            <td  colspan="2"><?=($registro)?$registro['AsiTecRec']:''?> 
                WhatsApp (<?=($registro)?((($registro['AsiTecRec']=='WhatsApp')?'X':'')):''?>) 
                Zoom (<?=($registro)?((($registro['AsiTecRec']=='Zoom')?'X':'')):''?>) 
                Meet (<?=($registro)?((($registro['AsiTecRec']=='Meet')?'X':'')):''?>) 
                Ms Teams (<?=($registro)?((($registro['AsiTecRec']=='Ms Teams')?'X':'')):''?>) 
                Presencial (<?=($registro)?((($registro['AsiTecRec']=='Presencial')?'X':'')):''?>)</td>
            <td>Teléfono</td>
            <td><?=($registro)?$registro['telefono1']:''?></td>
        </tr>
    </table>
    <br>
    <?php
    }
    ?>

    <?php
    if($ficha['DocMonFic']){
    ?>
    <b class="titulo"><?=$romano[$nro++]?>.	DATOS DEL DOCENTE MONITOREADO:</b>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr class="cabezera">
            <td><b>Nombres y apellidos</b></td>
            <td colspan="3"><b>DNI</b></td>
            <td><b>Grado y Sección</b></td>
            <td><b>Nivel</b></td>
            <td><b>N° de estudiantes matriculados</b></td>
            <td colspan="2"><b>N° de estudiantes atendidos</b></td>
        </tr>
        <tr>
            <td rowspan="2"><?=($registro)?$registro['nomDoc'].' '.$registro['apePatDoc'].' '.$registro['apeMatDoc']:''?></td>
            <td rowspan="2" colspan="3" class="centro"><?=($registro)?$registro['dniDoc']:''?></td>
            <td rowspan="2"><?=($registro)?$registro['graDoc']:''?> <?=($registro)?$registro['secDoc']:''?></td>
            <td>
            <?php
            if($registro){
                $idnivel = $registro['idNivDoc'];
                $nivel = '';
                switch ($idnivel) {
                    case '1': $nivel='CETPRO';break;
                    case '2': $nivel='EBE';break;
                    case '3': $nivel='Secundaria';break;
                    case '4': $nivel='Primaria';break;
                    case '5': $nivel='Inicial';break;
                    case '6': $nivel='Eba Inicial Intermedio';break;
                    case '7': $nivel='Eba Avanzado';break;
                    default:$nivel='';break;
                }
                echo $nivel;
            }
            ?>
            </td>
            <td rowspan="2" class="centro"><?=($registro)?$registro['nroEstRec']:''?></td>
            <td class="negritacentrado">PRESENCIAL</td>
            <td class="negritacentrado">ASINCRÓNICO</td>
        </tr>
        <tr>
            <td class="negritacentrado"><?=($registro)?$registro['areDoc']:''?></td>
            <td class="centro"><?=($registro)?$registro['nroEstPreRec']:''?></td>
            <td class="centro"><?=($registro)?$registro['nroEstAsiRec']:''?></td>
        </tr>
        <tr>
            <td>Tipo de Servicio</td>
            <td class="negritacentrado">&nbsp;&nbsp;P&nbsp;<?=($registro)?(($registro['tipSerRec']=='P')?'(X)':''):''?>&nbsp;</td>
            <td class="negritacentrado">&nbsp;&nbsp;H&nbsp;<?=($registro)?(($registro['tipSerRec']=='H')?'(X)':''):''?>&nbsp;</td>
            <td class="negritacentrado">&nbsp;&nbsp;D&nbsp;<?=($registro)?(($registro['tipSerRec']=='D')?'(X)':''):''?>&nbsp;</td>
            <td>Teléfono</td>
            <td><?=($registro)?$registro['telDoc']:''?></td>
            <td>Correo electrónico</td>
            <td colspan="2"><?=($registro)?$registro['corDoc']:''?></td>
        </tr>
        <?php
		if($ficha['tipFic']=='DIRECTIVO AL DOCENTE'){
		?>
		<tr>
            <td>Número de visita a la IE</td>
            <td colspan="3"><?=($registro)?$registro['nroVisRec']:''?></td>
            <td colspan="2">Fecha de aplicación</td>
            <td><?=($registro)?date_format(date_create($registro['fechaaplicacion']),"d/m/Y"):''?></td>
            <td>Hora de inicio/fin</td>
            <td><?=($registro)?(($registro['horainicioaplicacion'])?$registro['horainicioaplicacion'].' a ':''):''?><?=($registro)?$registro['horaaplicacion']:''?></td>
        </tr>
		<?php
		}
		?>
    </table>
    <br>
    <?php
    }
    ?>

    <?php  
    if($grupo){
        for ($i=0; $i < count($grupo); $i++) {
        $key = $grupo[$i];

        $cabeza = false;
        for ($k=0; $k < count($key['detalle']); $k++) {
            $fila = $key['detalle'][$k];
            if($fila['tipPre']=='SI/NO'){
                $cabeza=1;
            }
            elseif($fila['tipPre']=='INICIO/PROCESO/LOGRADO'){
                $cabeza=2;
            }
            elseif($fila['tipPre']=='INICIO/LOGRADO'){
	            $cabeza=4;
	        }
            elseif($fila['tipPre']=='NOAPLICA/INICIO/PROCESO/LOGRADO'){
	            $cabeza=5;
	        }
	        elseif($fila['tipPre']=='BUENO/REGULAR/MALO'){
	            $cabeza=6;
	        }
	        elseif($fila['tipPre']=='TEXTO CORTO' or $fila['tipPre']=='NUMERO CORTO'){
	            $cabeza=7;
	        }
	        elseif($fila['tipPre']=='0/1/2/3/4'){
	            $cabeza=8;
	        }
	        elseif($fila['tipPre']=='1/2/3'){
	            $cabeza=9;
	        }
	        elseif($fila['tipPre']=='SI/NO/NOAPLICA'){
                $cabeza=10;
            }
            elseif($fila['tipPre']=='SI/NO SIMPLE'){
                $cabeza=11;
            }
            elseif($fila['tipPre']=='UNO O NINGUNO/POCOS/LA MAYORIA/TODOS'){
                $cabeza=12;
            }
        }
    ?>
    <?=($key['SalLinPre'])?'<div style="page-break-after:always;"></div>':''?>
    <div style="font-weight: bolder;font-size:13px;"><?=($key['gruPre']=='-')?'':$key['gruPre']?></div>
    <!-------Contenido------->
    <table class="table" style="color:#000;width:100%;font-size: 12px;" <?php if($cabeza){ ?> border="1" bordercolor="666633" cellpadding="2" cellspacing="0" <?php } ?> >
        <!--<tbody>-->
        <?php
        if($cabeza==1){
        ?>
            <tr class="cabezera">
                <td>&nbsp;N°&nbsp;</td>
                <td>Ítem</td>
                <td>SI</td>
                <td>NO</td>
                <td><?=($ficha['decFic']==1)?'OBSERVACIONES':'EVIDENCIA'?></td>
            </tr>
        <?php
        }
        ?>
        <?php
        if($cabeza==2){
        ?>
            <tr class="cabezera">
                <td>&nbsp;N°&nbsp;</td>
                <td>Ítem</td>
                <!--<td>NO APLICA (0)</td>-->
                <td>EN INICIO (1)</td>
                <td>EN PROCESO (2)</td>
                <td>LOGRADO (3)</td>
                <td>Recursos a Observar</td>
				<td>Observaciones yo precisiones</td>
            </tr>
        <?php
        }
        ?>
        <?php
        if($cabeza==4){
        ?>
            <tr class="cabezera">
                <td>&nbsp;N°&nbsp;</td>
                <td>Ítem</td>
                <td>SI</td>
                <td>NO</td>
                <td>Recursos a Observar</td>
				<td>Observaciones yo precisiones</td>
            </tr>
        <?php
        }
        ?>
        <?php
        if($cabeza==5){
        ?>
            <tr class="cabezera">
                <td>&nbsp;N°&nbsp;</td>
                <td style="width:160px;">Ítem1</td>
                <td>NO APLICA (0)</td>
                <td>EN INICIO (1)</td>
                <td>EN PROCESO (2)</td>
                <td>LOGRADO (3)</td>
                <td>Recursos a Observar</td>
				<td>Observaciones yo precisiones</td>
            </tr>
        <?php
        }
        ?>
        <?php
        if($cabeza==6){
        ?>
            <tr class="cabezera">
                <td>&nbsp;N°&nbsp;</td>
                <td>Ítem</td>
                <!--<td>NO APLICA (0)</td>-->
                <td>MALO</td>
                <td>REGULAR</td>
                <td>BUENO</td>
                <td>Recursos a Observar</td>
				<td>Observaciones yo precisiones</td>
            </tr>
        <?php
        }
        ?>
        <?php
        if($cabeza==7){
        ?>
            <tr class="cabezera">
                <td style="width:15px;">&nbsp;N°&nbsp;</td>
                <td>Ítem</td>
                <td colspan="4">Respuesta</td>
            </tr>
        <?php
        }
        ?>
        
        <?php
	    if($cabeza==8){
	    ?>
	    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
	        <td rowspan="2">N°</td>
	        <td rowspan="2">ítems</td>
	        <td colspan="5">Valoración</td>
	    </tr>
	    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
	        <td>0</td>
	        <td>1</td>
			<td>2</td>
			<td>3</td>
			<td>4</td>
	    </tr>
	    <?php
        }
	    ?>
	    
	    <?php
	    if($cabeza==9){
	    ?>
	    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
	        <td rowspan="2">N°</td>
	        <td rowspan="2">Indicador</td>
	        <td colspan="3">Valoración</td>
	        <td rowspan="2">Observaciones yo precisiones</td>
	    </tr>
	    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
	        <td>1</td>
	        <td>2</td>
			<td>3</td>
	    </tr>
	    <?php
        }
	    ?>
        
        <!--INICIO JMMJ 11-10-2023-->
          <?php
        if($cabeza==10){
        ?>
            <tr class="cabezera">
                <td>&nbsp;N°&nbsp;</td>
                <td>Ítem</td>
                <td>SI</td>
                <td>NO</td>
                <td>NO APLICA</td>
                <td><?=($ficha['decFic']==1)?'OBSERVACIONES':'EVIDENCIA'?></td>
            </tr>
        <?php
        }
        ?>
        <!--FIN JMMJ 11-10-2023-->
        <!--30-05-2025-->
	    <?php
	    if($cabeza==11){
	    ?>
	    <tr class="cabezera">
	        <td rowspan="2">N°</td>
	        <td rowspan="2">Ítem</td>
	        <td colspan="2">Alternativas</td>
	    </tr>
	    <tr class="cabezera">
	        <td>SI</td>
	        <td>NO</td>
	    </tr>
	    <?php
	    }
	    ?>
	    <!--30-05-2025-->
	    <!--01-07-2025-->
	    <?php
	    if($cabeza==12){
	    ?>
	    <tr class="cabezera">
	        <td rowspan="2">N°</td>
	        <td rowspan="2">Ítem</td>
	        <td colspan="4">Alternativas</td>
	    </tr>
	    <tr class="cabezera">
	        <td>UNO O NINGUNO</td>
	        <td>POCOS</td>
	        <td>LA MAYORIA</td>
	        <td>TODOS</td>
	    </tr>
	    <?php
	    }
	    ?>
	    <!--01-07-2025-->
        
        <?php
        if($key['detalle']){
        for ($k=0; $k < count($key['detalle']); $k++) {
            $fila = $key['detalle'][$k];
        ?>
        <?php if($fila['tipPre']=='SI/NO/NOAPLICA' or $fila['tipPre']=='SI/NO' or $fila['tipPre']=='INICIO/PROCESO/LOGRADO' or $fila['tipPre']=='NOAPLICA/INICIO/PROCESO/LOGRADO' or $fila['tipPre']=='BUENO/REGULAR/MALO' or $fila['tipPre']=='INICIO/LOGRADO' or $fila['tipPre']=='0/1/2/3/4' or $fila['tipPre']=='1/2/3'){ ?>
        <?php
        if($fila['altPre']){ 
        ?>
        <tr>
            <td colspan="5" style="font-weight:bolder;background-color:rgb(218,238,243);"><?=$fila['altPre']?></td>
        </tr>
        <?php
        }
        ?>
        <tr class="">
            <td style="text-align:center;font-weight:bolder;"><?=($fila['nroPre']=='-')?'':$fila['nroPre'].'.'?></td>
            <td><?=$fila['textPre']?></td>
            <?php if($fila['tipPre']=='SI/NO'){ ?>
            <td style="text-align:center;"><?=($fila['resRdd']=='SI')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='NO')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['obsRdd'])?$fila['obsRdd']:$fila['obsPre']?></td>
            <?php }elseif($fila['tipPre']=='INICIO/LOGRADO' or $fila['tipPre']=='ARCHIVO'){ ?>
            <td style="text-align:center;"><?=($fila['resRdd']=='3')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='1')?'X':''?></td>
            <td style="text-align:center;"><?=$fila['obsPre']?></td>
            <td style="text-align:center;"><?=$fila['obsRdd']?></td>
            <?php }elseif($fila['tipPre']=='NOAPLICA/INICIO/PROCESO/LOGRADO' or $fila['tipPre']=='ARCHIVO'){ ?>
            <td style="text-align:center;"><?=($fila['resRdd']=='0')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='1')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='2')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='3')?'X':''?></td>
            <td style="text-align:center;"><?=$fila['obsPre']?></td>
            <td style="text-align:center;"><?=$fila['obsRdd']?></td>
            <?php }elseif($fila['tipPre']=='0/1/2/3/4' or $fila['tipPre']=='ARCHIVO'){ ?>
            <td style="text-align:center;"><?=($fila['resRdd']=='0')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='1')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='2')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='3')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='4')?'X':''?></td>
            <?php }elseif($fila['tipPre']=='1/2/3' or $fila['tipPre']=='ARCHIVO'){ ?>
            <td style="text-align:center;"><?=($fila['resRdd']=='1')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='2')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='3')?'X':''?></td>
            <td style="text-align:center;"><?=$fila['obsRdd']?></td>
             <?php }elseif($fila['tipPre']=='SI/NO/NOAPLICA'){ ?>
            <td style="text-align:center;"><?=($fila['resRdd']=='SI')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='NO')?'X':''?></td>
             <td style="text-align:center;"><?=($fila['resRdd']=='NOAPLICA')?'X':''?></td>
            <td style="text-align:center;"><?=$fila['obsPre']?></td>
           
            <?php }else{ ?>
            <!--<td style="text-align:center;"><?=($fila['resRdd']=='0')?'X':''?></td>-->
            <td style="text-align:center;"><?=($fila['resRdd']=='1')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='2')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='3')?'X':''?></td>
            <td style="text-align:center;"><?=$fila['obsPre']?></td>
            <td style="text-align:center;"><?=$fila['obsRdd']?></td>
            <?php } ?>
        </tr>
        <?php if($fila['htmlPre']){ ?>
        <tr class="anexo<?=$fila['idPre']?>" style="<?=($fila['resRdd']=='SI'?'':'')?>" >
            <td></td>
            <td colspan="4">
                <?php
                $varHtmlPre = explode(',',$fila['varHtmlPre']);
                $varInpPre  = explode('***',$fila['varInpPre']);
                $fila['htmlPre'] = str_replace('border="1"','border=1 cellspacing=0 cellpadding=2 bordercolor="666633"',$fila['htmlPre']);
                if($fila['adicional']){
                foreach ($fila['adicional'] as $key1) {
                    //Verificar o comentado
                    //$fila['htmlPre'] = str_replace('name="'.$key1['varVaa'].'"','name="'.$key1['varVaa'].'" value="'.$key1['valRaa'].'"',$fila['htmlPre']);
                    if(count($varHtmlPre)==count($varInpPre)){
                        $fila['htmlPre'] = str_replace($varInpPre[array_search($key1['varVaa'],$varHtmlPre)],'<span style="text-align:center;">'.$key1['valRaa'].'</span>',$fila['htmlPre']);
                    }else{
                        $fila['htmlPre'] = str_replace('name="'.$key1['varVaa'].'"','name="'.$key1['varVaa'].'" value="'.$key1['valRaa'].'"',$fila['htmlPre']);
                    }
                }
                }else{
                    if($varHtmlPre){
                        if(count($varHtmlPre)==count($varInpPre)){
                            for ($m=0; $m < count($varHtmlPre); $m++) {
                                    $fila['htmlPre'] = str_replace($varInpPre[$m],'<span style="text-align:center;">--</span>',$fila['htmlPre']);
                                }
                        }
                    }
                }
                echo $fila['htmlPre'];
                ?>
            </td>
        </tr>
        <?php } ?>
        
        
        <?php } ?>
        
        <?php if($fila['tipPre']=='SI/NO SIMPLE'){ ?>
	    <tr>
	        <td style="text-align:center;font-weight:bolder;"><?=($fila['nroPre']=='-')?'':$fila['nroPre'].'.'?></td>
            <td><?=$fila['textPre']?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='SI')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='NO')?'X':''?></td>
	    </tr>
	    <?php } ?>
	    
	    <?php if($fila['tipPre']=='UNO O NINGUNO/POCOS/LA MAYORIA/TODOS'){ ?>
	    <tr>
	        <td style="text-align:center;font-weight:bolder;"><?=($fila['nroPre']=='-')?'':$fila['nroPre'].'.'?></td>
            <td><?=$fila['textPre']?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='1')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='2')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='3')?'X':''?></td>
            <td style="text-align:center;"><?=($fila['resRdd']=='4')?'X':''?></td>
	    </tr>
	    <?php } ?>
        
        <?php if($fila['tipPre']=='TEXTO'){   ?>
        <tr>
            <td></td>
            <td colspan="5"><textarea class="form-control" name="p<?=$fila['idPre']?>" placeholder="Escriba su respuesta en este campo de texto" style="border-bottom: 1px solid darkgreen;resize: vertical;" required><?=$fila['resRdd']?></textarea></td>
        </tr>
        <?php } ?>
        
        <?php if($fila['tipPre']=='ENCABEZADO'){   ?>
        
        </table>
        
        <table class="table" style="color:#000;width:100%;font-size: 12px;">
            <tr><td><?=$fila['textPre']?></td></tr>
        
        <?php } ?>
        
        <?php if($fila['tipPre']=='TEXTO CORTO' or $fila['tipPre']=='NUMERO CORTO'){   ?>
        <?php /* if($registro['textModalidadRec']==$fila['altPre'] or $fila['altPre']==''){ */ ?>
	    <tr>
	        <td style="text-align:center;font-weight:bolder;"><?=($fila['nroPre']=='-')?'':$fila['nroPre'].'.'?></td>
	        <td><b><?=$fila['textPre']?></b></td>
	        <td colspan="4" style="text-align:center;"><?=$fila['resRdd']?></td>
	    </tr>
	    <?php /* } */ ?>
	    <?php } ?>
        
        <?php if($fila['tipPre']=='TABLA'){  ?>
        <?php
        if($registro){
        $fila['htmlPre'] = str_replace('|codlocal|',$registro['codlocRec'],$fila['htmlPre']);
        $fila['htmlPre'] = str_replace('|nombreie|',$registro['insRec'],$fila['htmlPre']);
        $fila['htmlPre'] = str_replace('|fechaficha|',$registro['fechaficha'],$fila['htmlPre']);
        $fila['htmlPre'] = str_replace('|grado|',$registro['graDoc'],$fila['htmlPre']);
		$fila['htmlPre'] = str_replace('|seccion|',$registro['secDoc'],$fila['htmlPre']);
        $fila['htmlPre'] = str_replace('|ciclo|',$registro['cicDoc'],$fila['htmlPre']);
        }
        $fila['htmlPre'] = str_replace('border="1"','border=1 cellspacing=0 cellpadding=2 bordercolor="666633"',$fila['htmlPre']);
        $varHtmlPre = explode(',',$fila['varHtmlPre']);
        $varInpPre  = explode('***',$fila['varInpPre']);
        if($fila['adicional']){
        foreach ($fila['adicional'] as $key1) {
            //Verificar o comentado
            //$fila['htmlPre'] = str_replace('name="'.$key1['varVaa'].'"','name="'.$key1['varVaa'].'" value="'.$key1['valRaa'].'"',$fila['htmlPre']);
            //$fila['htmlPre'] = str_replace('name="'.$key1['varVaa'].'"','name="'.$key1['varVaa'].'" value="'.$key1['valRaa']  .count($varHtmlPre).' '.count($varInpPre).' '.((count($varHtmlPre)==count($varInpPre))?$varInpPre[array_search($key1['varVaa'],$varHtmlPre)]:'')   .'"',$fila['htmlPre']);
            if(count($varHtmlPre)==count($varInpPre)){
                $fila['htmlPre'] = str_replace($varInpPre[array_search($key1['varVaa'],$varHtmlPre)],'<span style="text-align:center;">'.(($key1['valRaa'])?$key1['valRaa']:'&nbsp;&nbsp;&nbsp;').'</span>',$fila['htmlPre']);
            }else{
                $fila['htmlPre'] = str_replace('name="'.$key1['varVaa'].'"','name="'.$key1['varVaa'].'" value="'.$key1['valRaa'].'"',$fila['htmlPre']);
            }
        }
        }else{
            if($varHtmlPre){
                if(count($varHtmlPre)==count($varInpPre)){
                    for ($m=0; $m < count($varHtmlPre); $m++) {
                            $fila['htmlPre'] = str_replace($varInpPre[$m],'<span style="text-align:center;">--</span>',$fila['htmlPre']);
                        }
                }
            }
        }
        ?>
        <tr>
            <td style="border:0;"></td>
            <td style="border:0;" colspan="5"><input type="text" name="p<?=$fila['idPre']?>" value="SI" style="display:none;"><?=$fila['htmlPre']?></td>
        </tr>
        <?php } ?>
        
        <?php if($fila['tipPre']=='OPCION MULTIPLE'){ ?>
        <tr>
            <td></td>
            <td colspan="5">
                <b><?=$fila['textPre']?></b>
                <?php
                $r_alternativas = explode(',',$fila['altPre']);
                if($fila['altPre']){
                    for ($j=0; $j < count($r_alternativas); $j++){
                    $alt = $r_alternativas[$j];
                    echo (strlen($fila['altPre'])>100)?'<br>':'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    echo '<span class="l-border">'.(($fila['resRdd']==$alt)?'&nbsp;X&nbsp;':'&nbsp;&nbsp;&nbsp;&nbsp;').'</span>&nbsp;&nbsp;'.$alt;//strpos($fila['resRdd'],$alt)>-1
                    }
                }
                ?>
            </td>
        </tr>
        <?php } ?>
        
        <?php if($fila['tipPre']=='OPCION UNICA'){ ?>
        
        <?php if($cabeza==7){ ?>
        <tr>
	        <td style="text-align:center;font-weight:bolder;"><?=($fila['nroPre']=='-')?'':$fila['nroPre'].'.'?></td>
	        <td><b><?=$fila['textPre']?></b></td>
	        <td colspan="4"><?=$fila['resRdd']?></td>
	    </tr>
         <?php }else{ ?>
        <tr>
            <td></td>
            <td colspan="5">
                <b><?=$fila['textPre']?></b>
                <?php
                $r_alternativas = explode(',',$fila['altPre']);
                if($fila['altPre']){
                    for ($j=0; $j < count($r_alternativas); $j++){
                    $alt = $r_alternativas[$j];
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="l-border">'.(($fila['resRdd']==$alt)?'&nbsp;X&nbsp;':'&nbsp;&nbsp;&nbsp;&nbsp;').'</span>&nbsp;&nbsp;'.$alt;//(strpos($fila['resRdd'],$alt)>-1
                    }
                }
                ?>
            </td>
        </tr>
         <?php } ?>
        
        <?php } ?>
        
        <?php
        }
        }
        ?>
        <!--</tbody>-->
    </table>
    <br>
    <!-------Contenido------->
    <?php
    }
    }
    ?>
    <?php
        if($resumenipl){
    ?>
    <div style="page-break-after:always;"></div>
    <div style="text-align: center;font-size:17px;font-weight: bolder;">DESCRIPCIÓN DEL NIVEL DE AVANCE</div>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr class="cabezera">
            <td>ASPECTOS MONITOREADOS</td>
            <td>ITEMS</td>
            <td>En Inicio (1)</td>
            <td>En Proceso (2)</td>
            <td>Logrado (2)</td>
            <td>NIVEL DE AVANCE SATISFACTORIO (%)</td>
        </tr>
        <?php
       
        foreach ($resumenipl as $key) {
            $key = (Array)$key;
        ?>
        <tr style="text-align: center;">
            <td style="text-align: justify;"><?=$key['gruPre']?></td>
            <td><?=$key['cantidad']?></td>
            <td><?=$key['inicio']?></td>
            <td><?=$key['proceso']?></td>
            <td><?=$key['logrado']?></td>
            <td><b><?=round(($key['logrado']/ (($key['cantidadipl']==0)?1:$key['cantidadipl']) )*100,2)?>%</b></td>
        </tr>
        <?php
        }
        
        ?>
        <?php
        if($totalipl){
        ?>
        <tr style="text-align: center;font-weight: bolder;">
            <td>TOTAL</td>
            <td><?=$totalipl['cantidad']?></td>
            <td><?=$totalipl['inicio']?></td>
            <td><?=$totalipl['proceso']?></td>
            <td><?=$totalipl['logrado']?></td>
            <td><?=round(($totalipl['logrado']/ (($totalipl['cantidadipl']==0)?1:$totalipl['cantidadipl']) )*100,2)?>%</td>
        </tr>
        <?php
        }
    ?>
    </table>
    <?php
        }
        ?>
    
    
    <?php
    if($resumen_cge){
    ?>
    <div style="page-break-after:always;"></div>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr class="cabezera">
            <td colspan="7">RESULTADOS DEL SEGUIMIENTO A LOS COMPROMISOS DE GESTIÓN ESCOLAR 2025</td>
        </tr>
        <tr class="cabezera">
            <td>Compromiso</td>
            <td>CGE 1: Desarrollo integral de las y los estudiantes</td>
            <td>CGE 2: Acceso de las y los estudiantes al SEP hasta la culminación de su trayectoria educativa</td>
            <td>CGE 3: Gestión de las condiciones operativas orientada al sostenimiento del SE ofrecido por la IE</td>
            <td>CGE 4: Gestión de la práctica pedagógica orientada al logro de aprendizajes previstos en el perfil de egreso de CNEB</td>
            <td>CGE 5: Gestión del bienestar escolar que promueva el desarrollo integral de los estudiantes</td>
            <td>Nivel de avance de los COMPROMISOS DE GESTIÓN ESCOLAR 2025</td>
        </tr>
        <tr class="cuerpo">
            <td class="izquierda">Puntaje total</td>
            <td>{{$resumen_cge['tcge1']}}</td>
            <td>{{$resumen_cge['tcge2']}}</td>
            <td>{{$resumen_cge['tcge3']}}</td>
            <td>{{$resumen_cge['tcge4']}}</td>
            <td>{{$resumen_cge['tcge5']}}</td>
            <td class="resumentotal">{{$resumen_cge['tcgeTotal']}}</td>
        </tr>
        <tr class="cuerpo">
            <td class="izquierda">Puntaje obtenido</td>
            <td>{{$resumen_cge['cge1']}}</td>
            <td>{{$resumen_cge['cge2']}}</td>
            <td>{{$resumen_cge['cge3']}}</td>
            <td>{{$resumen_cge['cge4']}}</td>
            <td>{{$resumen_cge['cge5']}}</td>
            <td class="resumentotal">{{$resumen_cge['cgeTotal']}}</td>
        </tr>
        <tr class="cuerpo">
            <td class="izquierda">Porcentaje obtenido</td>
            <td>{{round($resumen_cge['porcge1']*100,2)}}%</td>
            <td>{{round($resumen_cge['porcge2']*100,2)}}%</td>
            <td>{{round($resumen_cge['porcge3']*100,2)}}%</td>
            <td>{{round($resumen_cge['porcge4']*100,2)}}%</td>
            <td>{{round($resumen_cge['porcge5']*100,2)}}%</td>
            <td class="resumentotal">{{round($resumen_cge['porcgeTotal']*100,2)}}%</td>
        </tr>
        <tr class="nivellogro">
            <td class="izquierda">Nivel de logro %</td>
            <td style="color:{{$resumen_cge['stylecge1']}};">{{$resumen_cge['textocge1']}}</td>
            <td style="color:{{$resumen_cge['stylecge2']}};">{{$resumen_cge['textocge2']}}</td>
            <td style="color:{{$resumen_cge['stylecge3']}};">{{$resumen_cge['textocge3']}}</td>
            <td style="color:{{$resumen_cge['stylecge4']}};">{{$resumen_cge['textocge4']}}</td>
            <td style="color:{{$resumen_cge['stylecge5']}};">{{$resumen_cge['textocge5']}}</td>
            <td class="resumentotal" style="color:{{$resumen_cge['stylecgeTotal']}};">{{strtoupper($resumen_cge['textocgeTotal'])}}</td>
        </tr>
    </table>
    
    <br>
    <br>
    

    <b>Leyenda</b>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr class="nivellogro">
            <td class="izquierda">Rangos</td>
            <td>- 30%</td>
            <td>30% a 60%</td>
            <td>61% a 90%</td>
            <td>+ 90%</td>
        </tr>
        <tr class="nivellogro">
            <td class="izquierda">Niveles de logro</td>
            <td style="color:Black;">No Presenta</td>
            <td style="color:red;">Inicio</td>
            <td style="color:GoldenRod;">Proceso</td>
            <td style="color:Green;">Logrado</td>
        </tr>
    </table>
    
    <?php
    }
    ?>
    
    <?php
    /*
    if($resumenbrm){
    ?>
    <div style="page-break-after:always;"></div>
    <div style="text-align: center;font-size:17px;font-weight: bolder;">DESCRIPCIÓN DEL NIVEL DE AVANCE</div>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
        <tr class="cabezera">
            <td>ASPECTOS MONITOREADOS</td>
            <td>ITEMS</td>
            <td>Malo</td>
            <td>Regular</td>
            <td>Bueno</td>
            <td>NIVEL DE AVANCE SATISFACTORIO (%)</td>
        </tr>
        <?php
       
        foreach ($resumenbrm as $key) {
            $key = (Array)$key;
        ?>
        <tr style="text-align: center;">
            <td style="text-align: justify;"><?=$key['gruPre']?></td>
            <td><?=$key['cantidad']?></td>
            <td><?=$key['inicio']?></td>
            <td><?=$key['proceso']?></td>
            <td><?=$key['logrado']?></td>
            <td><b><?=round(($key['logrado']/ (($key['cantidadipl']==0)?1:$key['cantidadipl']) )*100,2)?>%</b></td>
        </tr>
        <?php
        }
        
        ?>
        <?php
        if($totalipl){
        ?>
        <tr style="text-align: center;font-weight: bolder;">
            <td>TOTAL</td>
            <td><?=$totalipl['cantidad']?></td>
            <td><?=$totalipl['inicio']?></td>
            <td><?=$totalipl['proceso']?></td>
            <td><?=$totalipl['logrado']?></td>
            <td><?=round(($totalipl['logrado']/ (($totalipl['cantidadipl']==0)?1:$totalipl['cantidadipl']) )*100,2)?>%</td>
        </tr>
        <?php
        }
    ?>
    </table>
    <?php
        }
        */
    ?>
    
    @if ($resumen_matriz && count($resumen_matriz) > 0)
        <div style="page-break-after:always;"></div>
        <br>
        <p style="font-weight: bold;">Resultados:</p>
        <table border="1" cellspacing="0" cellpadding="2" width="100%">
            <tr class="cabezera">
                <td colspan="5">RESULTADOS</td>
            </tr>
            <tr class="cabezera">
                <td rowspan="2" class="center">CARACTERÍSTICAS</td>
                <td colspan="4" align="center">NIVEL DE LOGRO</td>
            </tr>
            <tr class="cabezera">
                <td align="center">Uno o ninguno</td>
                <td align="center">Pocos</td>
                <td align="center">La mayoría</td>
                <td align="center">Todos</td>
            </tr>

            @foreach ($resumen_matriz as $item)
                @php
                    $uno = $pocos = $mayoria = $todos = '';
                    $valor = round($item->porcentaje ?? 0, 2) . '%';

                    switch (trim($item->texto_logro)) {
                        case 'Uno o ninguno':
                            $uno = $valor;
                            break;
                        case 'Pocos':
                            $pocos = $valor;
                            break;
                        case 'La mayoría':
                            $mayoria = $valor;
                            break;
                        case 'Todos':
                            $todos = $valor;
                            break;
                    }
                @endphp
                <tr class="cuerpo">
                    <td class="izquierda">{{ $item->grupo }}</td>
                    <td align="center">{{ $uno }}</td>
                    <td align="center">{{ $pocos }}</td>
                    <td align="center">{{ $mayoria }}</td>
                    <td align="center">{{ $todos }}</td>
                </tr>
            @endforeach
        </table>
        <br>
    @endif
    
    @if (!empty($rangos_ficha))
        <br>
        <p style="font-weight: bold;">Leyenda de porcentajes por grupo:</p>
        <table border="1" cellspacing="0" cellpadding="4" width="100%">
            <thead>
                <tr class="cabezera">
                    <td class="izquierda"><b>CARACTERÍSTICA</b></td>
                    <td align="center"><b>Uno o ninguno</b></td>
                    <td align="center"><b>Pocos</b></td>
                    <td align="center"><b>La mayoría</b></td>
                    <td align="center"><b>Todos</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($rangos_ficha)->sortKeys() as $grupo => $valores)
                    <tr class="cuerpo">
                        <td class="izquierda">{{ $grupo }}</td>
                        <td align="center">0% – {{ $valores[0] }}%</td>
                        <td align="center">{{ $valores[0] + 1 }}% – {{ $valores[1] }}%</td>
                        <td align="center">{{ $valores[1] + 1 }}% – {{ $valores[2] }}%</td>
                        <td align="center">{{ $valores[2] }}% – 100%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif


    <?php
    if($ficha['tipFic']=='AL DIRECTIVO' or $ficha['tipFic']=='DIRECTIVO AL DOCENTE'){
    ?>
    <br>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
    <tr>
        <td width="30%"><b>PRINCIPALES LOGROS</b></td>
        <td width="70%"><?=($registro)?$registro['conRec']:'-'?></td>
    </tr>
    <tr>
        <td><b>ACCIONES DE MEJORA</b></td>
        <td><?=($registro)?$registro['recRec']:'-'?></td>
    </tr>
    <tr>
        <td><b>COMPROMISOS DEL DIRECTOR</b></td>
        <td><?=($registro)?$registro['comDirRec']:'-'?></td>
    </tr>
    <tr>
        <td><b>COMPROMISOS DEL DOCENTE</b></td>
        <td><?=($registro)?$registro['comDocRec']:'-'?></td>
    </tr>
    </table>
    <?php } ?>
    
    <?php
    if($ficha['tipFic']=='A LA IIEE'){
    ?>
    <br>
    <br>
    <b>RECOMENDACIONES DEL ESPECIALISTA:</b>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
    <tr><td><?=($registro)?$registro['texto1Obs']:'-'?></td></tr>
    </table>
    <br>
    <br>
    <b>COMPROMISOS DEL DIRECTOR:</b>
    <table border="1" cellspacing="0" cellpadding="2" width="100%">
    <tr><td><?=($registro)?$registro['texto2Obs']:'-'?></td></tr>
    </table>
    <?php } ?>
  </div>
</body>
</html>
