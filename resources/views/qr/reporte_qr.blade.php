@extends('layout_especialista/cuerpo')
@section('html')

<!--graficos Google Charts-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<!--graficos Google Charts-->


<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>FILTROS</b>
    </h5>
        <div class="position-relative form-group">
            <select id="idQr" onchange="grafico_qr()">
            <?php
            foreach ($data as $key) {
            $key = (Array)$key;
            ?>
            <option value="<?php echo $key['idQr']?>" url="<?=$base_url?><?=$key['urlCorQr']?>"><?php echo $key['nomQr'] ?></option>
            <?php
            }
            ?>

            </select>
        </div>
    </div>
</div>

<BR>

<div id="reporte"></div>


<script>
function grafico_qr(){
    ajax_data = {
      "idQr"   : $("#idQr").val(),
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('grafico_qr')}}",
                    data: ajax_data,
                    dataType: "html",
                    beforeSend: function(){
                          //imagen de carga
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                    $("#reporte").html(data);
                    //limpiar contenedor
                    $("#contenedorQR").html("");
                    //indentificar donde esta el codigo qr
                    const contenedorQR = document.getElementById('contenedorQR');
                    //intanciar el codigo qr
                    const QR = new QRCode(contenedorQR);
                    //imprimir codigo qr
                    QR.makeCode($("#idQr option:checked").attr('url'));
                    //centrar codigo qr
                    setTimeout(function(){ $("#contenedorQR img").css('display',''); },500);
                    }
              });

}

grafico_qr();
</script>

@endsection