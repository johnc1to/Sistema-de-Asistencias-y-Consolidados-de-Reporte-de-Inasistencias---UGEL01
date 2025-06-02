<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
        <style>
        h1{
        text-align: center;
        text-transform: uppercase;
        }
        .contenido{
        font-size: 20px;
        }
        #primero{
        background-color: #ccc;
        }
        #segundo{
        color:#44a359;
        }
        #tercero{
        text-decoration:line-through;
        }
        table tr td{
            font-size:9px;
        }
        #t_reporte tbody tr td{
            border-left: 0.3px solid #000;
            border-bottom: 0.3px solid #000;
        }
        #t_reporte .ultimacolumna{
            border-right: 0.3px solid #000;
        }
        #footer {
            position: fixed;
            bottom: -40px;
            left: 0px;
            right: 0px;
            height: 130px;
        }
        #footer .page:after { 
            content: counter(page, upper-arial); 
            
        }
    </style>
    </head>
    <body>
        <!--
        <h2 style="font-weight:bolder;text-align:center;">REPORTE DE ASISTENCIA <?=$trabajo['mes']?> <?=$trabajo['anio']?> </h2>
        <h3 style="text-align:center;"><?=$trabajo['area']?>: <?=$trabajo['equipo']?></h3>
        <hr>
        -->
        <div class="contenido">
        <?php
        $fechas   = $mantenimiento['fechas'];
        $registro = $mantenimiento['registro'];
        ?>
            <table table border="0" class="display table table-bordered table-striped table-dark" id="t_reporte" style="color:#000;text-align:center;width:100%;">
                <thead>
                  <tr style="font-size:11px;background-color:#fff;">
                      <td style="padding-bottom:5px;" colspan="<?=8+count($fechas)?>">
                          <img src="./assets/images/baner_ugel01.jpg"></td>
                  </tr>
                  <tr style="font-size:11px;background-color:rgb(140,140,140);">
                      <td style="min-width:45px;color:#fff;"  rowspan="2"><b>N°</b></td>
                      <td style="min-width:45px;color:#fff;"  rowspan="2"><b>Especialista</b></td>
                      <td style="min-width:100px;color:#fff;" rowspan="2"><b>Regimen Laboral</b></td>
                      <td style="color:#fff;font-size:12px;"colspan="<?=count($fechas)?>"><b>CONSOLIDADO DE ASISTENCIA MES DE <?=$trabajo['mes']?> <?=$trabajo['anio']?> <?=($trabajo['area']==$trabajo['equipo'])?$trabajo['area']:$trabajo['areacorta'].': '.$trabajo['equipo']?> </b></td>
                      <td style="min-width:20px;color:#fff;"  rowspan="2"><b>Total dias laborados</b></td>
                      <td style="min-width:20px;color:#fff;"  rowspan="2"><b>Trabajo remoto</b></td>
                      <td style="min-width:20px;color:#fff;"  rowspan="2"><b>Trabajo presencia</b></td>
                  </tr>
                  <tr style="font-size:11px;background-color:rgb(140,140,140);" class="">
                      <?php
                      for ($i=0; $i < count($fechas); $i++) {
                      ?><td style="min-width:20px;color:#fff;" ><b><?=$fechas[$i]->dia?></b></td><?php
                      }
                      ?>
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
                    <td><?=$key->regimen_laboral?></td>
                    <?php
                    for ($i=0; $i < count($fechas); $i++) {
                    $nrofila = 'fila'.$i;
                    ?>                    
                    <td style="<?=($fechas[$i]->diasemana==5 or $fechas[$i]->diasemana==6)?'background-color:rgb(255,255,0);':(($fechas[$i]->feriado==1)?'background-color:rgb(255,199,206);':'')?>"><?=$key->$nrofila?></td>
                    <?php
                    }
                    ?>
                    <td><?=$key->total?></td>
                    <td><?=$key->remoto?></td>
                    <td class="ultimacolumna"><?=$key->presencia?></td>
                </tr>

                <?php
                }
                }
                ?>
                </tbody>
            </table>
            <br>
            <div style="text-align:center;"><?=($session)?'<img height="100px" src=".'.$session['firma'].'">':''?></div>
        </div>
        <div id="footer">
           <table border="0" style="font-size:10px;margin-bottom:5px;">
                <tr><td style="font-weight: bold;background-color:rgb(140,140,140);color:#fff;" colspan="3">LEYENDA</td><td></td><td></td><td></td><td></td><td rowspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td rowspan="6"><?=($session)?'<img height="70px" src=".'.$session['visto'].'">':''?></td></tr>
                <tr style="font-weight: bold;"><td>R</td><td>=</td><td>DIA TRABAJADO REMOTO</td>                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LG/SC</td><td>=</td><td>LICENCIA CON GOCE SUJETO A COMPENSACION</td></tr>
                <tr style="font-weight: bold;"><td>P</td><td>=</td><td>DIA TRABAJADO PRESENCIAL</td>                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>O</td><td>=</td><td>ONOMASTICO</td></tr>
                <tr style="font-weight: bold;"><td>FE</td><td>=</td><td>FERIADO</td>                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>V</td><td>=</td><td>VACACIONES</td></tr>
                <tr style="font-weight: bold;"><td>DL/PHS</td><td>=</td><td>DIA LABORADO POR HORAS DE SOBRETIEMPO</td>  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LPS</td><td>=</td><td>LICENCIA POR SALUD</td></tr>
                <tr style="font-weight: bold;"><td>LSG</td><td>=</td><td>LICENCIA SIN GOCE</td>                         <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td>LPM</td><td>=</td><td>LICENCIA POR MATERNIDAD</td></tr>
            </table>
            <center><span>pag. </span><span class="page"></span></center>
        </div>
    </body>
</html>