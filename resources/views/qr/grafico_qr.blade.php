
<h2>REPORTE <?php echo $data[0]->nomQr?> </h2>

<div class="row">
    

<div class="col-md-6 col-lg-6">
<div class="card-shadow-primary mb-3 widget-chart widget-chart2 text-left card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>Escaneos Totales</b>
    </h5>
        <div class="position-relative form-group">
            Muestra el número de veces que se ha escaneado el Codigo QR,lo
            que indica su popularidad general
            <br>
            <b style="font-size:30px;"><?php echo $resumen[0]->total?></b>
        </div>
    </div>
</div>
</div>

<div class="col-md-6 col-lg-6">
<div class="card-shadow-primary mb-3 widget-chart widget-chart2 text-left card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>Usuarios Únicos</b>
    </h5>
        <div class="position-relative form-group">
            Muestra el número de usuarios que han escaneado el
            codigo, proporcionando informacion sobre su alcance y audiencia
            <br>
            <b style="font-size:30px;"><?php echo $resumen[0]->unicos?></b>

        </div>
    </div>
</div>

</div>
</div>


<div class="row">

<div class="col-md-12 col-lg-12">
<div class="card-shadow-primary mb-3 widget-chart widget-chart2 text-left card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>Usuarios Totales y Unicos</b>
    </h5>
        <div class="position-relative form-group responsive">
        <!--char-->
        <div id="chart_div"></div>




<script>
var grafico1=<?=json_encode($grafico1)?>;
var data1=[];
for (let i = 0; i < grafico1.length; i++) {
    var key = grafico1[i];
    data1.push([new Date(key['año'], key['mes'], key['dia']),  key['total'], key['unicos']]);    
}

</script>

        <script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBackgroundColor);

function drawBackgroundColor() {
      var data = new google.visualization.DataTable();
      data.addColumn('date', 'Day');
      data.addColumn('number', 'Escaneos Totales (<?php echo $resumen[0]->total?>)');
      data.addColumn('number', 'Usuarios Unicos (<?php echo $resumen[0]->unicos?>)');

      data.addRows(data1);

      var options = {
        backgroundColor: '',
        legend: {position: 'bottom', textStyle: {color: 'black', fontSize: 12}}
      };

      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }            
    </script>
        <!--char-->
        </div>
    </div>
</div>
</div>


<div class="col-md-6 col-lg-6">
<div class="card-shadow-primary mb-3 widget-chart widget-chart2 text-left card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>Usuarios Totales y Unicos</b>
    </h5>
        <div class="position-relative form-group">
            <table border= "1" style="text-align: center;">
                <tr>
                    <th>DIA</th>
                    <th>Usuarios Totales</th>
                    <th>Usuarios Unicos</th>
                </tr>
                <?php
                for ($i=0; $i < count($grafico1); $i++) { 
                    $key=$grafico1[$i];
                ?>
                <tr>
                    <td><?=$key->dia?>/<?=$key->mes?>/<?=$key->año?></td>
                    <td><?=$key->total?></td>
                    <td><?=$key->unicos?></td>
                </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
</div>


<div class="col-md-6 col-lg-6">
<div class="card-shadow-primary mb-3 widget-chart widget-chart2 text-left card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>QR</b>
    </h5>
        <div class="position-relative form-group" style="text-align: center;">
            <!--QR-->
            <div id="contenedorQR" style="text-align: center;"></div>            
            <!--URL-->
            <a  target="_black" href="<?=$base_url?><?=$data[0]->urlCorQr?>"><?=$base_url?><?=$data[0]->urlCorQr?></a>
            
        </div>
    </div>
</div>

</div>
</div>