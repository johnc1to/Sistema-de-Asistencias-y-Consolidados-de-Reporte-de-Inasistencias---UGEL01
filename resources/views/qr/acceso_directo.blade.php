@extends('layout_especialista/cuerpo')
@section('html')
<style>
    img{
        max-width: 100%;
    }
</style>

<script defer src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>ACCESOS DIRECTOS</b>
    </h5>
        <div class="position-relative form-group">
            
        </div>
    </div>
</div>

<br>
<div class="row">
<?php
$nro=0;
        foreach ($data as $key) {
        ?>
<div class="col-md-3 col-lg-3">
<div class="card-shadow-primary mb-3 widget-chart widget-chart2 text-left card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;min-height:50px;"><b><?=$key->nomQr?></b>
    </h5>
        <div class="position-relative form-group">
            <div id="contenedorQR<?=$nro?>">Cargando...</div>
            <br>
            <a href="<?=$key->urlQr?>" class="btn btn-success form-control">Ingresar...</a>
            <b style="font-size:30px;"></b>
        </div>
    </div>
</div>
</div>

<?php
$nro++;
    }
    ?>
</div>

<script>
function generarQr(){
    var data = <?=json_encode($data)?>;
for (let i = 0; i < data.length; i++) {
    //limpiar contenedor
    $("#contenedorQR"+i).html("");
    //indentificar donde esta el codigo qr
    const contenedorQR = document.getElementById('contenedorQR'+i);
    //intanciar el codigo qr
    const QR = new QRCode(contenedorQR);
    //imprimir codigo qr
    QR.makeCode(data[i]['urlQr']);
}
    
}

setTimeout(function(){ generarQr(); },1000);


</script>


@endsection