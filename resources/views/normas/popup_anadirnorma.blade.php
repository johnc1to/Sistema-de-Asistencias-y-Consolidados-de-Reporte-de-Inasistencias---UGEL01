<style>
  .row{
    margin-bottom:10px;
  }
</style>
<form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="guardarnorma();return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>NORMA</b></h4>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      
    </div>
    <div class="modal-body">

       <div id="testmodal" style="padding: 5px 20px;">

           <div class="row" style="display:none;">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>idFnn:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['idFnn']:''?>" name="idFnn" class="inputesp form-control"></div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Ente rector:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select class="form-control" name="idEnt" required>
                    <option value="">Elija el ente rector</option>
                    <?php
                    foreach ($entidades as $key) {
                    ?><option value="<?=$key['idEnt']?>"><?=$key['desEnt']?></option><?php
                    }
                    ?>
                </select>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Tema:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select class="form-control" name="idTem" required>
                    <option value="">Elija el tema</option>
                    <?php
                    foreach ($temas as $key) {
                    ?><option value="<?=$key['idTem']?>"><?=$key['desTem']?></option><?php
                    }
                    ?>
                </select>
            </div>
          </div>

          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Asunto:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['AsuFnn']:''?>" name="AsuFnn" class="form-control" required></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Numero de documento:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <input tyle="text" value="<?=($data)?$data['nroFnn']:''?>" name="nroFnn" class="form-control" autocomplete="off" onkeyup="normanbuscarnroFnn();" required>
                <div id="msjnroFnn"></div>
              </div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Fecha:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" class="flatfecha form-control" value="<?=($data)?$data['fecFnn']:''?>" name="fecFnn" class="form-control" required></div>
          </div>
          
          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Tipo:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select class="form-control" name="idTip" required>
                    <option value="">Elija el tipo</option>
                    <?php
                    foreach ($tipos as $key) {
                    ?><option value="<?=$key['idTip']?>"><?=$key['desTip']?></option><?php
                    }
                    ?>            
                </select>
            </div>
           </div>

           
           <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Palabras clave:</b></div>
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['palClaFnn']:''?>" name="palClaFnn" class="form-control" required></div>
            </div>

          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b></b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"> 
              <input type="radio" name="rtipo" value="1" onclick="tipoarchivo();" <?=($data)?(($data['arcLinFnn']==1)?'checked':''):'checked'?> > Archivo  &nbsp;&nbsp;
              <input type="radio" name="rtipo" value="2" onclick="tipoarchivo();" <?=($data)?(($data['arcLinFnn']==2)?'checked':''):''?> > Enlace  
            </div>
          </div>

          <div class="row divtipo divarchivo" style="<?=($data)?(($data['arcLinFnn']==1)?'':'display:none;'):''?>">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Archivo:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
              <?php
              if($data){
              ?> <a target="_blank" class="btn btn btn-info normadescargar" href="<?=$data['arcFnn']?>">Descargar archivo</a> <span class="btn btn-danger normadescargar" onclick="cambiararchivo();">Cambiar</span> 
              <input type="file" name="arcFnn" class="form-control normacambiar" onchange="validararchivo(this,'PDF');" style="display:none;">
              <?php
              }else{
              ?> <input type="file" name="arcFnn" class="form-control" onchange="validararchivo(this,'PDF');" <?=($data)?(($data['arcLinFnn']==1)?'required':''):'required'?> > <?php
              }
              ?>              
              </div>
          </div>

          <div class="row divtipo divlink" style="<?=($data)?(($data['arcLinFnn']==2)?'':'display:none;'):'display:none;'?>" >
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Enlace:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['arcFnn']:''?>" name="arcFnnLink" class="form-control" <?=($data)?(($data['arcLinFnn']==2)?'required':''):''?> ></div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Situación:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
              <select class="form-control" name="idSit" required>
                <?php
                foreach ($situacion as $key) {
                ?><option value="<?=$key['idSit']?>"><?=$key['desSit']?></option><?php
                }
                ?>
            </select>
            </div>
        </div>
      </div>       	
    </div>
    @csrf
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_grabar">Guardar</button>
      <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>
    </div>
</form>

<script>

function tipoarchivo(){
  $(".divtipo").css('display','none');
  $(".divtipo input").prop('required',false);
  if( $("input[name=rtipo]:checked").val()==1 ){
    $(".divarchivo").css('display','');
    $(".divarchivo input").prop('required',true);
    $(".divarchivo input").val('');
  }else{
    $(".divlink").css('display','');
    $(".divlink input").prop('required',true);
    $(".divlink input").val('');
  }
}

function cambiararchivo(){
  $(".normadescargar").css('display','none');
  $(".normacambiar").css('display','');
}

var gthis;
  function validararchivo(athis,extencion=''){
      gthis=athis;
    if($(gthis)[0].files[0]){
      var fileextencion = $(gthis)[0].files[0]['name'].split('.').pop().toUpperCase();
      if(fileextencion.indexOf(extencion.split(','))>-1){

      }else{
        $(athis).val('');
        alert('Archivo no valido, solo archivos '+extencion);
      }
    }
  }

  function normanbuscarnroFnn(){
    ajax_data = {
      "nroFnn" : $("#popup01 input[name=nroFnn]").val(),
      "alt"    : Math.random()
    }
    if($("#popup01 input[name=nroFnn]").val().length>3){
    $.ajax({
                    type: "GET",
                    url: "{{route('normanbuscarnroFnn')}}",
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                          //imagen de carga
                          $("#login").html("<p align='center'><img src='http://intranet.ugel01.gob.pe/prestamos/public/images/cargando.gif'/></p>");
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                      var texto = '';
                        if(data.length){
                          texto = '<b style="color:red;">El documento ya esta registrado</b>';
                          for (let i = 0; i < data.length; i++) {
                              texto += '<br>'+data[i]['nroFnn'];
                          }
                          $("#btn_grabar").prop('disabled',true);
                          
                        }else{
                          $("#btn_grabar").prop('disabled',false);
                          
                        }
                        $("#msjnroFnn")  .html(texto);
                      }
              });
  }
}
  
    function guardarnorma(){
        var ifimagen = true;
    //$('#btn_enviar').prop('disabled',true);
      if(true){
          //información del formulario
          var formData = new FormData($("#formulario01")[0]);
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('guardarnorma')}}',  
              type: 'POST',
              //datos del formulario
              data: formData,
              dataType: "json",
              //necesario para subir archivos via ajax
              cache: false,
              contentType: false,
              processData: false,
              //mientras enviamos el archivo
              beforeSend: function(){
                  
              },
              //una vez finalizado correctamente
              success: function(data){
                  alert('Datos guardados');
                  normasrepositorio();
                  $("#fc_popup").click();
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
    }else{
      alert('Llene todos los campos');
    }

    }

    flatpickr('.flatfecha', {
      minDate: '1920-01-01',  
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

<script>
  <?php
  if($data){
  ?>
    $("select[name=idTem]") .val("<?=$data['idTem']?>");
    $("select[name=idTip]") .val("<?=$data['idTip']?>");
    $("select[name=idSit]") .val("<?=$data['idSit']?>");
    $("select[name=idEnt]") .val("<?=$data['idEnt']?>");
  <?php
  }
  ?>
</script>