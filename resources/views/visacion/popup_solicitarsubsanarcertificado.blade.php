<link rel="stylesheet" type="text/css" href="assets/css/yellow-text-default.css" /> <!-- include the needed css for the texteditor -->
<script type="text/javascript" src="assets/scripts/yellow-text.min.js"></script> <!-- include the texteditor script -->

<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel" style="text-align: center;font-weight: bolder;">SUBSANAR</h4>
</div>

<div class="modal-body">
<form id="formulario01" enctype="multipart/form-data" action="#" onsubmit="return false;">
    <div id="testmodal" style="padding: 5px 20px;">
    
        <input type="hidden" name="idreclamo"  value="<?=$exp['idreclamo']?>">
        <input type="hidden" name="idespecialista"  value="<?=$session['idespecialista']?>">
        <input type="hidden" name="asunto"  value="<?=$asunto?>">
        
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Certificado de estudio:</b> <a href="<?=$exp['archivo']?>" target="_blank" class="btn btn-danger">DESCARGAR</a> <b style="float:right;font-size:19px;"><?=$exp['cod_reclamo']?></b> </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><br></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><object data="<?=$exp['archivo']?>" type="application/pdf" width="100%" height="500px"></object></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Observaciones anteriores:</b></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <?php
              $base_url = 'http://siic01.ugel01.gob.pe/';
                foreach ($obs as $key) {
                  $key = (Array)$key;
                  echo '<b class="alert alert-warning" style="padding:2px;margin-bottom:3px;cursor: pointer;">';
                  echo ($key['observacion_exp']=='CITADO' && $key['confirmarcita']==1)?'<i class="pe-7s-like2" style="font-size:22px;color:green;" title="CONFIRMADO"></i> ':'';
                  echo ($key['observacion_exp']=='CITADO' && $key['confirmarcita']==2)?'<i class="pe-7s-close-circle" style="font-size:22px;color:red;" title="REPROGRAMAR"></i> ':'';
                  echo '<span onclick="vercuerpo('.$key['id_casilla_detalle'].');">'.$key['observacion_exp'].': '.$key['nombre'].'</span>';
                  echo ' <a target="_blank" class="fa-file-pdf-o" href="'.$base_url.'index.php/notificacion/acuse/'.$key['id_casilla_detalle'].'/2">Recibido</a>'.':'.$key['acuse_recibido'];
                  echo ($key['acuse_leido'])?' <a target="_blank" class="fa-file-pdf-o" href="'.$base_url.'index.php/notificacion/acuse/'.$key['id_casilla_detalle'].'/3">Leido</a>'.': '.$key['acuse_leido']:'';
                  echo '</b>';
                  
                  echo '<br>';
                  echo '<div id="cuerpo'.$key['id_casilla_detalle'].'" style="display:none;">'.$key['cuerpo'].'</div>';
                  echo '<br>';
                  //echo '('.$key['acuse_recibido'].')';
                  //echo '<b>'.$key['nombre'].'</b>'.'<br>'.$key['observacion_exp'].' - '.'<a target="_blank" class="fa-file-pdf-o" href="'.$base_url.'index.php/notificacion/acuse/'.$key['id_casilla_detalle'].'/2">Recibido</a>'.':</b> '.$key['acuse_recibido'];
                  //echo ($key['acuse_leido'])?' <a target="_blank" class="fa-file-pdf-o" href="'.$base_url.'index.php/notificacion/acuse/'.$key['id_casilla_detalle'].'/3">Leido</a>'.':</b> '.$key['acuse_leido']:'';
                  
                }
              ?>
            </div>
        </div>

        
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Observacion:</b></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="checkbox" class="boxobs" value="1" onclick="anadir_obscorreo();"><span style="color: red;">De acuerdo a la RVM-273-2020-MINEDU, el uso del formato en papel amarillo se descontinuó.</span>
              <br><input type="checkbox" class="boxobs" value="2" onclick="anadir_obscorreo();"><span style="color: red;">Debe ser firmado por el director de las Institución Educativa.</span>
              <br><input type="checkbox" class="boxobs" value="3" onclick="anadir_obscorreo();"><span style="color: red;">Debe adjuntar el certificado escaneado, no una foto.</span>
            </div>
            <?php
            $modelo = str_replace('|NOMBRE|',$exp['ciudadano'],$modelo);
            $modelo = str_replace('|EXP|',$exp['cod_reclamo'],$modelo);
            ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="solo_textarea"><textarea name="txt_descripcion" id="txt_descripcion"><?=str_replace('|OBS|','<li></li>',$modelo)?></textarea></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Archivo adjunto:</b></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><input type="file" name="txt_archivo" class="form-control"></div>
        </div>
        <input type="submit" id="btnEnviar" style="display: none;">
        @csrf
    </div>
</form>
</div>
<div class="modal-footer">
    <button class="btn btn-success" id="btn_subsanar" onclick="guardarsubsanarcertificado();">SOLICITAR SUBSANAR</button>
   <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>         
</div>

<?php
echo '<pre>';
//print_r($exp);
//print_r($obs);
echo '</pre>';
?>

<script>

function vercuerpo(nro){
  if($("#cuerpo"+nro).css('display')=='none'){
    $("#cuerpo"+nro).css('display','');
  }else{
    $("#cuerpo"+nro).css('display','none');
  }
  
}


var g_modelo = <?=json_encode($modelo)?>;
    function anadir_obscorreo(){
      var elemento = $(".boxobs:checked");
      text = '';
      if(elemento.length){
      for (let i = 0; i < elemento.length; i++) {       
        switch (parseInt(elemento[i].value)) {
          case 1:text+='<li>De acuerdo a la RVM-273-2020-MINEDU, el uso del formato en papel amarillo se descontinuó, los documentos deberán emitirse por SIAGIE, y posteriormente realizar la impresión en papel bond con firma y sello del director. En el caso de fotos de las fichas, o firmas no serán válidas. El nuevo formato del Certificado de Estudio no se visa, en tanto el procedimiento no cuenta con marco normativo la viabilice.</li>';break;
          case 2:text+='<li>Debe ser firmado por el director de las Institución Educativa.</li>';break;
          case 3:text+='<li>Debe adjuntar el certificado escaneado, no una foto.</li>';break;
        }        
      }
      }else{
        text = '<li></li>';
      }
      $("#solo_textarea").html('<textarea name="txt_descripcion" id="txt_descripcion">'+g_modelo.replace('|OBS|',text)+'</textarea>');
      $('#txt_descripcion').YellowText({defaultFont: 'Georgia'});
      //return text;
    }

    $('#txt_descripcion').YellowText({defaultFont: 'Georgia'});
      
      function guardarsubsanarcertificado(){
          $("#btnEnviar").click();
          //información del formulario
          var formData = new FormData($("#formulario01")[0]);
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('guardarsubsanarcertificado')}}',  
              type: 'POST',
              //datos del formulario
              data: formData,
              dataType: "html",
              //necesario para subir archivos via ajax
              cache: false,
              contentType: false,
              processData: false,
              //mientras enviamos el archivo
              beforeSend: function(){
                $("#btn_subsanar").prop('disabled',true);
                $("#btn_subsanar").html('<img src="assets/images/2.gif" width="30px"> Enviando...');
              },
              //una vez finalizado correctamente
              success: function(data){
                $("#btn_subsanar").prop('disabled',false);
                $("#btn_subsanar").html('SOLICITAR SUBSANAR');
                ver_certificadodeestudio();
                $("#fc_popuplg").click();
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
        }
      



</script>
