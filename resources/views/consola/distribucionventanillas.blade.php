
<b>DISTRIBUCIÓN DE ACCESO A VENTANILLAS</b>
<table border="1">
    <tr>
        <th>Ventanilla</th>
        <th>Cantidad de usuarios</th>
    </tr>
    <?php
    $total = 0;
    foreach ($result as $key){
    $key = (Array)$key;
    $total = $total + $key['cantidad'];
    ?>
    <tr style="text-align:center;<?=($menor==$key['ventanilla'])?'color:green;':''?>">
        <td>Ventanilla 0<?=$key['ventanilla']?></td>
        <td><?=$key['cantidad']?></td>
    </tr>
    <?php
    }
    ?>
    <tr>
        <td><b>Total</b></td>
        <td style="text-align:center;"><b><?=$total?></b></td>
    </tr>
</table>