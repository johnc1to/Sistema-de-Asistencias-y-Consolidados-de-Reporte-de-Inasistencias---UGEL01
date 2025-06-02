<html>
<head>
<style>
          /*top right bottom left*/
    @page { margin: 110px 55px 35px 100px; }
    #header { position: fixed; left: 0px; top: -80px; right: 0px; height: 100px;  text-align: center; }
    #footer { position: fixed; left: 0px; bottom: 0px; right: 0px; height: 40px;  }
    #footer .page:after { content: counter(page, upper-arial);}
  </style>
<body>
  <div id="header">
    <img src="assets/images/baner_ugel01.jpg">
    <center>"Año del fortalecimiento de la sobenanía Nacional"</center>
  </div>
  <div id="footer">
    <hr>
    <center style="color:rgb(31,72,124);font-weight: bold;">Jr. Los Ángeles S/N Urb. Jesús Poderoso Pamplona Baja- Lima – San Juan de Miraflores </center>
    <center style="color:rgb(31,72,124);font-weight: bold;">https://www.ugel01.gob.pe/</center>
    <p class="page">Page </p>
  </div>
  <div id="content" style="text-align: justify; text-justify: inter-word;">
    <p style="text-align: right;">San Juan de Miraflores, <?=$cita['dia']?> de <?=$cita['mes']?> del <?=$cita['anio']?></p>
    <br>
    <center style="font-weight: bold;">INFORME N°<?=$cita['nro_informe']?>-2022</center>
    <br><br>
    <table>
        <tr>
            <td><b>PARA:</b></td>
            <td><b>JHON PETER ABANTO MANOSALVA</b></td>
        </tr>
        <tr>
            <td></td>
            <td>Jefe de área de supervisión y gestión del servicio educativo UGEL 01</td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td><b>CC:</b></td>
            <td><b>EDDY RONALD PAREDES LEON</b></td>
        </tr>
        <tr>
            <td></td>
            <td>Director del Sistema Administrativo II del Área de Recursos Humanos</td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td><b>DE:</b></td>
            <td><b>RAMIRO MANUEL APARCANA PEÑA</b></td>
        </tr>
        <tr>
            <td></td>
            <td>Medico ocupacional de la UGEL 01</td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td><b>ASUNTO:</b></td>
            <td>Informe de caso de <?=$cita['nombres']?> <?=$cita['apellipat']?> <?=$cita['apellimat']?> </td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td><b>REFERENCIA:</b></td>
            <td>Reincorporación de personal <?=$cita['mes']?> <?=$cita['anio']?></td>
        </tr>
    </table>
    

    <hr>
    <p>Tengo el agrado de dirigirme a usted para brindar mis recomendaciones sobre el caso de
    <?=$cita['nombres']?> <?=$cita['apellipat']?> <?=$cita['apellimat']?>, de <?=$cita['edad']?> años, identificado con DNI N° <?=$cita['numdocum']?>. El
    trabajador pertenece a la IE <?=$cita['nombie']?>.
    </p>
    <p><b>Puesto:</b> <?=$cita['descargo']?></p>
    <p><?=$cita['observacion']?></p>
    <?php if($cita['nrodosis']){ ?>
    <p>El trabajador presenta su carné de vacunación con <?=$cita['nrodosis']?> dosis contra COVID-19.</p>
    <?php }else{ ?>
    <p>El trabajador NO presenta su carné de vacunación contra COVID-19.</p>
    <?php } ?>
    <p>Se concluye que el trabajador, de acuerdo con el marco normativo de COVID-19 - RVM 834-2021 MINSA, se
encuentra médicamente <b><?=$cita['dignostico']?></b> para labores presenciales.</p>

<p>Sin otro particular, quedo de usted.</p>

<center><img style="width:180px;" src="assets/images/44782195.jpg"></center>
<center>Médico Ocupacional</center>

<?php
//print_r($cita);
?>
    <!--<p style="page-break-before: always;">the second page</p>-->
  </div>
</body>
</html>
