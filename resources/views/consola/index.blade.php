
@extends('layout_especialista/cuerpo')
@section('html')

<style>
</style>
<?php
    $script='';
    $gobal = array();
    foreach ($variablesgobales as $key){
    $funcion='';
    $texto='';
    switch ($key['variable']) {
        case 'varias_ventanillas': {
            $texto .='<div class="row">';
            $texto .='<div class="col-sm-2">';
            foreach ($ventanilla as $ven){
                $funcion = 'onclick="ver('."'".$key['variable']."'".');"';
                $texto .='<div><b style="font-size:16px;color:#000;">Ventanilla'.$ven['ventanilla'].':</b> <input type="checkbox" name="v'.$ven['ventanilla'].'" value="1" '.(($ven['estado']==1)?'checked':'').'>'.'</div>';
            }
            $texto .='</div>';
            $texto .='<div class="col-sm-10 reporte">REPORTE</div>';
            $texto .='<div class="col-sm-12"><br></div>';
            $texto .='</div>';
            if($key['valor']==1){
                $script .= 'function actualizar(){ $("#div_varias_ventanillas .reporte").load("distribucionventanillas"); } ';
                $script .= 'actualizar(); ';
                $script .= 'setInterval("actualizar()",60000);';
            }
            
            //$script .= ($key['valor']==1)?'$("#div_varias_ventanillas .reporte").load("distribucionventanillas")':'';
        };break;
    default: {};break;
    }
    $gobal[$key['variable']] = array('funcion'=>$funcion,'texto'=>$texto);
    }
?>

<div class="main-card mb-12 card" id="">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>CONSOLA DE VENTANILLA VIRTUAL</b></h5>
        <div class="position-relative form-group">
            <form id="formulario02" method="POST" enctype="multipart/form-data" action="{{route('guardarconsola')}}" style="padding-left:15px;"> <!-- onsubmit="return false;"-->
                
                    <?php
                    foreach ($variablesgobales as $key){
                    $var = $gobal[$key['variable']];
                    ?>
                    <div class="row">
                    <div class="col-sm-12"><br></div>
                    <div class="col-sm-12"><b style="font-size:16px;color:#000;"><?=$key['descripcion']?>:</b> <input type="checkbox" name="<?=$key['variable']?>" value="1" <?=($key['valor']==1)?'checked':''?> <?=$var['funcion']?> ></div>
                    <div class="col-sm-12" id="div_<?=$key['variable']?>" style="<?=($key['valor']==1)?'':'display:none'?>"><?=$var['texto']?></div>
                    </div>
                    <?php
                    }
                    ?>
                    @csrf
                    <div class="row">
                    <div class="col-sm-12"><br>
                    <button class="btn btn-success">GRABAR</button>
                    </div>
                    </div>
            </form>
        </div>
    </div>
</div>
<br>
<div class="main-card mb-6 card" id="">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>CONSOLA DE REPORTE SINAD - RAP ALERTEX</b></h5>
        <div class="position-relative form-group" style="padding-left:30px;">
            <br>
            <?php
            $ip_alertex = ($ip=='200.123.16.107')?'10.2.1.62':'200.123.19.247';//200.123.16.107 es la IP de la Ugel01
            ?>
            <a href="http://<?=$ip_alertex?>:8060/siic01/index.php/alertex/reporte_x_oficina/61" target="_blank">Enviar reporte de APP al Jefe de APP por correo > </a>
        </div>
    </div>
</div>

<br>
<div class="main-card mb-6 card" id="">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>CONSOLA DE REPORTE RD PARA CONTRATO</b></h5>
        <div class="position-relative form-group" style="padding-left:30px;">
            <br>
            <a href="https://siic01.ugel01.gob.pe/index.php/notificacion/listar_rd_alertar" target="_blank">Enviar reporte de firma de RD > </a>
        </div>
    </div>
</div>

<script>
    function ver(id=''){
        if($("#div_"+id).css('display')=='none'){
            $("#div_"+id).css('display','');
        }else{
            $("#div_"+id).css('display','none');
        }
    }
</script>

<script>
<?=$script?>
</script>

@endsection

