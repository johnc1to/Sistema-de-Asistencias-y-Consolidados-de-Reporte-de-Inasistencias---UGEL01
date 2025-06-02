        
        <b>Buscar:</b>
        <input class="form-control" value="" onkeyup="$('#reporte input').val(this.value);" onkeypress="if(event.keyCode==13){ $('#reporte input').keyup(); }">
        <br>
        <table table class="display table table-bordered table-striped table-dark" id="t_reporte" style="color:#000;font-size:10px;text-align:center;width:100%;">
          <thead>
            <tr style="font-size:11px;color:#fff;background-color:Purple;" class="">
                <td style="width:15px;color:#fff;" ><b>Nro</b></td>
                <?php
                if($todo){
                ?> <td style="width:130px;color:#fff;" ><b>Equipo</b></td> <?php
                }
                ?>
                <td style="width:130px;color:#fff;" ><b>Especialista</b></td>
                <td style="width:130px;color:#fff;" ><b>Modalidad de contrato</b></td>
                <?php
                if($fechas){
                for ($i = 0; $i < count($fechas); $i++) {
                $key = $fechas[$i];
                ?>
                <td><b><?=$key->t_diasemana?></b><br> <?=$key->dia?></td>
                <?php
                }
                }
                ?>
            </tr>
          </thead>
          <tbody style="font-size:10px;">
              <?php
              if($lista){
              for ($i = 0; $i < count($lista); $i++) {
              $esp = $lista[$i];    
              ?>
              <tr>
                  <td><?=$i+1?></td>
                  <?php
                if($todo){
                ?> <td><?=$esp->equipo?></td> <?php
                }
                ?>
                  <td><?=$esp->esp_nombres?> <?=$esp->esp_apellido_paterno?> <?=$esp->esp_apellido_materno?></td>
                  <td><?=$esp->regimen_laboral?></td>
                  <?php
                    if($fechas){
                        for ($k = 0; $k < count($fechas); $k++) {
                        $key = $fechas[$k];
                        $style= ($key->diasemana==5 or $key->diasemana==6)?'background-color:rgb(255,255,0);':(($key->feriado==1)?'background-color:rgb(255,199,206);':'');
                        $texto = '';
                        foreach ($esp->asistencia as $asistencia ) {
                            if($asistencia->fecha == $key->fecha){
                                if($asistencia->entrada == $asistencia->salida){
                                    if($asistencia->status==0){
                                        $texto  = $asistencia->entrada.' <br> '.'<b style="color:red;">Sin salida</b>';
                                    }else{
                                        $texto = '<b style="color:red;">Sin entrada</b>'.' <br> '.$asistencia->salida;
                                    }
                                }else{
                                        $texto = $asistencia->entrada.' <br> '.$asistencia->salida;
                                        $texto .= ($verhoras)?' <br>'.$asistencia->horas.' Hrs'.' '.round(($asistencia->min-$asistencia->horas)*60).' min':'';
                                }
                            }
                        }
                        ?>
                        <td style="<?=$style?>"><?=$texto?></td>
                        <?php
                        }
                    }
                  ?>
              </tr>
              <?php
              }
              }
              ?>
          </tbody>
        </table>
        
        <?php
        echo '<pre>';
        //print_r($lista);
        //print_r($fechas);
        echo '</pre>';
        ?>
        
        <script type="text/javascript">
            var table4 = $("#t_reporte").DataTable( {
                    dom: 'Bfrtip',
                    buttons: ['excel'],
                    "iDisplayLength": 55,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                    },
                    "columns": [
                        { "data": "nro" },
                        <?php if($todo){ ?> { "data": "equipo" },<?php }?>
                        { "data": "especialista" },
                        { "data": "regimen_laboral" },
                        <?php
                        if($fechas){
                            for ($k = 0; $k < count($fechas); $k++) {
                            $key = $fechas[$k];
                            ?>
                            { "data": "dia<?=$key->dia?>" },
                            <?php
                            }
                        }
                      ?>
                    ],                          
                    rowCallback: function (row, data) {},
                    filter: true,
                    info: true,
                    ordering: true,
                    processing: true,
                    retrieve: true                          
                });
        </script>
                