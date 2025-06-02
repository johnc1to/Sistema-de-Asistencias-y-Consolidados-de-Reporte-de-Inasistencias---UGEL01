<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" type="text/css" href="assets/css/yellow-text-default.css" /> <!-- include the needed css for the texteditor -->
<script type="text/javascript" src="assets/scripts/yellow-text.min.js"></script> <!-- include the texteditor script -->

<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel" style="text-align: center;font-weight: bolder;">CITAR</h4>
</div>

<div class="modal-body">
<form id="formulario01" enctype="multipart/form-data" action="#" onsubmit="return false;">
    <div id="testmodal" style="padding: 5px 20px;">
    
        <input type="hidden" name="idreclamo"  value="<?=$exp['idreclamo']?>">
        <input type="hidden" name="idespecialista"  value="<?=$session['idespecialista']?>">
        <input type="hidden" name="asunto"  value="">

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><br></div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Expediente:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><?=$exp['cod_reclamo']?></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Fecha:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" name="txtfecha" class="form-control flatfecha" onchange="anadirvariablescorreo();"></div>
        </div>
    
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Hora:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4"><select name="txthora" class="form-control"    onchange="anadirvariablescorreo();"><option>08</option><option>09</option><option>10</option><option>11</option><option>12</option><option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option><option>19</option><option>20</option></select></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4"><select name="txtminutos" class="form-control" onchange="anadirvariablescorreo();"><option>00</option><option>15</option><option>30</option><option>45</option></select></div>
        </div>
    

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><br></div>
            <?php
            $modelo = str_replace('|NOMBRE|',$exp['ciudadano'],$modelo);
            $modelo = str_replace('|EXP|',$exp['cod_reclamo'],$modelo);
            ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="solo_textarea"><textarea name="txt_descripcion" id="txt_descripcion"><?=str_replace('|OBS|','<li></li>',$modelo)?></textarea></div>
        </div>

        <input type="submit" id="btnEnviar" style="display: none;">
        @csrf
    </div>
</form>
</div>
<div class="modal-footer">
    <button class="btn btn-success" id="btn_citar" onclick="guardarcitarciudadano();">CITAR</button>
   <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>         
</div>

<script>
var g_modelo = <?=json_encode($modelo)?>;
    function anadirvariablescorreo(){
      var rfecha  = $("input[name=txtfecha]").val().split('-');
      var fecha   = (rfecha.length==3)?rfecha[2]+'/'+rfecha[1]+'/'+rfecha[0]:'';
      var minutos = $("select[name=txthora]").val()+':'+$("select[name=txtminutos]").val()+':00';
      $("input[name=asunto]").val('<?=$asunto?>'.replace('|FECHA|',fecha+' '+minutos));
      $("#solo_textarea").html('<textarea name="txt_descripcion" id="txt_descripcion">'+g_modelo.replace('|FECHA|',fecha).replace('|HORA|',minutos)+'</textarea>');
      $('#txt_descripcion').YellowText({defaultFont: 'Georgia'});
      //return text;
    }

    $('#txt_descripcion').YellowText({defaultFont: 'Georgia'});
      
      function guardarcitarciudadano(){
        if($("input[name=txtfecha]").val() && $("select[name=txthora]").val() && $("select[name=txtminutos]").val()){
          $("#btnEnviar").click();
          //información del formulario
          var formData = new FormData($("#formulario01")[0]);
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('guardarcitarciudadano')}}',  
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
                $("#btn_citar").prop('disabled',true);
                $("#btn_citar").html('<img src="assets/images/2.gif" width="30px"> Enviando...');
              },
              //una vez finalizado correctamente
              success: function(data){
                $("#btn_citar").prop('disabled',false);
                $("#btn_citar").html('CITAR');
                ver_certificadodeestudio();
                $("#fc_popuplg").click();
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
        }else{
            alert('seleccione la fecha y hora de la cita');
        }
        }
      

flatpickr('.flatfecha', {
      locale: {
        firstDayOfWeek: 1,
        weekdays: {
          shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
          longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],         
        }, 
        months: {
          shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
          longhand: ['Enero', 'Febrero', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        },
      },
    });


</script>