<html>
<head>
  <style>
                /*top right bottom left*/
    @page { margin: 20px 35px 60px 35px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; background-color: orange; text-align: center; }
    #footer { position: fixed; bottom: -40px; left: 0px; right: 0px; height: 130px; }
    body{  margin-bottom:135px; }
    /*#footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 150px; background-color: lightblue; }*/
    #footer .page:after { content: counter(page, upper-arial); }
    table tr td{
        font-size:8px;
    }
    #t_reporte tbody tr td{
        border-left: 0.3px solid #000;
        border-bottom: 0.3px solid #000;
    }
    #t_reporte .ultimacolumna{
        border-right: 0.3px solid #000;
    }
  </style>
<body>
  <div id="header"></div>
  <div id="footer">
    <table border="0" style="font-size:10px;margin-bottom:5px;">
        <tr><td style="font-weight: bold;background-color:rgb(140,140,140);color:#fff;" colspan="3">LEYENDA</td><td></td><td></td><td></td><td></td><td rowspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td rowspan="6"><?=($esp1)?(($esp1['visto'])?'<img height="70px" src=".'.$esp1['visto'].'">':''):''?></td>
            <td rowspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td rowspan="6"><?=($esp2)?(($esp2['visto'])?'<img height="70px" src=".'.$esp2['visto'].'">':''):''?></td>
        </tr>
        <tr style="font-weight: bold;"><td>R</td><td>=</td><td>DIA TRABAJADO REMOTO</td>                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LG/SC</td><td>=</td><td>LICENCIA CON GOCE SUJETO A COMPENSACION</td></tr>
        <tr style="font-weight: bold;"><td>P</td><td>=</td><td>DIA TRABAJADO PRESENCIAL</td>                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>O</td><td>=</td><td>ONOMASTICO</td></tr>
        <tr style="font-weight: bold;"><td>FE</td><td>=</td><td>FERIADO</td>                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>V</td><td>=</td><td>VACACIONES</td></tr>
        <tr style="font-weight: bold;"><td>DL/PHS</td><td>=</td><td>DIA LABORADO POR HORAS DE SOBRETIEMPO</td>  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LPS</td><td>=</td><td>LICENCIA POR SALUD</td></tr>
        <tr style="font-weight: bold;"><td>LSG</td><td>=</td><td>LICENCIA SIN GOCE</td>                         <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LPM</td><td>=</td><td>LICENCIA POR MATERNIDAD</td></tr>
        <tr><td><br><br></td></tr>
    </table>
    <center><span>pag. </span><span class="page"></span></center>
  </div>
  <div id="content">
    <?php
        $fechas   = $mantenimiento['fechas'];
        $registro = $mantenimiento['registro'];
        ?>
            <table class="" border="0" id="t_reporte" style="color:#000;text-align:center;width:100%;">
                <thead>
                  <tr style="font-size:11px;background-color:#fff;">
                      <td style="padding-bottom:5px;" colspan="<?=8+count($fechas)?>"><img src="./assets/images/baner_ugel01.jpg"></td>
                  </tr>
                  <tr style="font-size:11px;background-color:#fff;">
                      <td style="padding-bottom:5px;text-align:right;font-size:11px;" colspan="<?=8+count($fechas)?>"><b>Fecha de reporte: <?=date('Y/m/d H:i:s')?> </b></td>
                  </tr>
                  <tr style="font-size:11px;background-color:rgb(140,140,140);">
                      <td style="min-width:45px;color:#fff;"  rowspan="2"><b>N°</b></td>
                      <td style="min-width:45px;color:#fff;"  rowspan="2"><b>Apellidos y nombres</b></td>
                      <td style="min-width:45px;color:#fff;"  rowspan="2"><b>DNI</b></td>
                      <td style="min-width:100px;color:#fff;" rowspan="2"><b>Condición</b></td>
                      <td style="min-width:100px;color:#fff;" rowspan="2"><b>Cargo</b></td>
                      <td style="color:#fff;font-size:12px;" colspan="<?=count($fechas)?>"><b>CONSOLIDADO DE ASISTENCIA MES DE <?=$titulo?></b></td>
                      <td style="min-width:20px;color:#fff;"  rowspan="2"><b>Total dias laborados</b></td>
                      <td style="min-width:20px;color:#fff;"  rowspan="2"><b>Total a descontar</b></td>
                      <td style="min-width:20px;color:#fff;"  colspan="2"><b>Modalidad de trabajo</b></td>
                  </tr>
                  <tr style="font-size:11px;color:#000;background-color:rgb(140,140,140);">
                      <?php
                      for ($i=0; $i < count($fechas); $i++) {
                      ?><td style="min-width:20px;color:#fff;" ><b><?=$fechas[$i]->dia?></b></td><?php
                      }
                      ?>
                      <td style="min-width:20px;color:#fff;"  rowspan="1"><b>Remoto</b></td>
                      <td style="min-width:20px;color:#fff;"  rowspan="1"><b>Presencia</b></td>
                  </tr>
                </thead>
                <tbody style="font-size:12px;background-color: #fff;">
                <?php
                if($registro){
                for ($k=0; $k < count($registro); $k++) {
                $key = $registro[$k];
                ?>
                <tr>
                    <td><?=$k+1?></td>
                    <td><?=$key->esp_nombres?> <?=$key->esp_apellido_paterno?> <?=$key->esp_apellido_materno?></td>
                    <td><?=$key->ddni?></td>
                    <td><?=$key->regimen_laboral?></td>
                    <td><?=$key->cargo?></td>
                    <?php
                    for ($i=0; $i < count($fechas); $i++) {
                    $nrofila = 'fila'.$i;
                    ?>                    
                    <td style="<?=($fechas[$i]->diasemana==5 or $fechas[$i]->diasemana==6)?'background-color:rgb(255,255,0);':(($fechas[$i]->feriado==1)?'background-color:rgb(255,199,206);':'')?>"><?=$key->$nrofila?></td>
                    <?php
                    }
                    ?>
                    <td><?=$key->total?></td>
                    <td><?=$key->descuento?></td>
                    <td><?=$key->remoto?></td>
                    <td class="ultimacolumna"><?=$key->presencia?></td>
                </tr>
                <?php
                }
                }
                ?>
                </tbody>
            </table>
            <br><br>
            <div style="text-align:center;">
                <?=($esp1)?(($esp1['firma'])?'<img height="100px" src=".'.$esp1['firma'].'">':''):''?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?=($esp2)?(($esp2['firma'])?'<img height="100px" src=".'.$esp2['firma'].'">':''):''?>
                </div>
  </div>
</body>
</html>