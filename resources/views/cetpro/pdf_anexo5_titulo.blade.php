<style>
    #pag01 {
        font-size:19px;
    }
    #pag02 {
        font-size:19px;
    }

    #pag02 {
    height:100px;
    width:800px;
     text-align:center;
    background-color:blue;
}
    #divHijo {
    margin:0px auto;
    margin-top:100px;
    font-size:14px;
    width:400px;
    border: 2px solid black;
    text-align: center;
}

@page {
		margin-top: 2cm;
		margin-right: 3cm;
		margin-left:  3cm;
	}
</style>

<?php
$healthy = array("á", "é", "í","ó","ú","ñ");
$yummy   = array("Á", "É", "Í","Ó","Ú","Ñ");
?>

<div id="pag01">
    {{--  <div style="font-size:12px;text-align:right;">Anexo Nº 5 B – R.V.M. 188-2020-MINEDU</div>  --}}
    <table border="0" width="100%">
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="width:100px;"> <img style="height:80px;" src=".<?=$iiee['logo']?>"> </td>
            <td style="text-align:center;font-weight: bold;">
                <img style="height:80px;width:80px;" src="assets/images/escudo.jpg">
            </td>
            <td style="width:100px;"> <div style="width:4cm;height:4.6cm; display: inline-block;border: 1px solid #000;text-align: center;">Foto<br>(4 x 4.5 cm)</div> </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:center;font-weight: bold;">
                <div>REPÚBLICA DEL PERÚ</div>
                <div style="margin-top:10px;">
                MINISTERIO DE EDUCACIÓN
                <br>CENTRO DE EDUCACIÓN TÉCNICO PRODUCTIVA
                @php
                    $elemento = explode(" ",$iiee["gestion_dependencia"]);
                @endphp
                    @if (in_array("Publica",$elemento))
                     PÚBLICO
                    @endif
                    @if (in_array("Privada",$elemento))
                    PRIVADA
                   @endif
                <br>
                <div style="font-size: 23px"> “<?=$iiee['institucion']?>”</div>

                </div>
            </td>
        </tr>
    </table>
    <div style="text-align: center;">
    <p>El Director del Centro de Educación Técnico Productiva Público <b><?=$iiee['institucion']?></b>
    por cuanto <b><?=strtoupper(str_replace($healthy, $yummy, $titulo['estudiante']))?></b>
    ha cumplido satisfactoriamente con las normas y disposiciones reglamentarias vigentes, le otorga el <b>Título de <?=$titulo['nivForPee']?></b>
    en {{-- el programa de estudio --}} <b><?=strtoupper(str_replace($healthy, $yummy, $titulo['proEstPee']))?></b>.</p>
    </div>
    <table  width="100%">
        <tr>
            <td width="10%"></td>
            <td width="90%" style="font-style: oblique">POR TANTO:<br>Se expide el presente TÍTULO para que se lo reconozca como tal.</td>
        </tr>
    </table>
    <br>
    <table  width="100%">
        <tr>
            <td width="40%"></td>
            <td width="60%">Dado en <?=$iiee['distrito']?> a los <?=$titulo['dia']?> días del mes de <?=$titulo['mes']?> de <?=$titulo['anio']?></td>
        </tr>
    </table>
    <div style="text-align: center;">
    <table style="width:300px;margin:0px auto;text-align: center;">
        <tr>
            <td><br><br><br></td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #000;font-size:11px;"><b style="font-size:14px;">DIRECTOR(A)</b><br>(Sello, firma, post firma)</td>
        </tr>
    </table>
    </div>

    </div>

    <div style="page-break-after:always;"></div>

    <div id="pag2">
        <div id="divHijo">
            <br>
            <p><img style="height:100px;" src=".<?=$iiee['logo']?>"></p>
            <p><b>Código de Registro Institucional</b>
            <br>N° <?=$titulo['codRegIeTit']?>
            </p>
            <p><b>Código de Registro de la UGEL</b>
            <br>N° <?=$titulo['codRegUgelTit']?>
            </p>
            <br><br>
            <div style="width: 400px; display: flex;justify-content: space-around">
                <div style="margin-left: 50px; border-top: 1px solid #000; width: 300px"><b>DIRECTOR(A)</b><br>
                    <span style="font-size:11px">(Sello, firma, post firma)</span></div>
            </div>

            <p><br></p>
        </div>
        <footer style="position: absolute; bottom: 0px;">
            <div style="font-size:9px;text-align:right;">Anexo Nº 5 B – R.V.M. 188-2020-MINEDU</div>
        </footer>


    </div>
