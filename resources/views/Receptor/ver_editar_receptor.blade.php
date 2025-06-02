<form id="formulario" onsubmit="guardar_receptor();return false;">

<div class="form-group">
<!--MODELO-->
@csrf

<tittle><center><b>EDITAR RECEPTOR</b></center></tittle>
<br>
<br>

<div class="row">
<div class="col-sm-3"><b>idreceptor:</b></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="idreceptor" id="idreceptor" value="<?=($receptor)?$receptor->idreceptor:''?>"readonly>
    </div>
   
    <div class="col-sm-3"><b>Tipo Documento:</b></div>
    <div class="col-sm-9">
 <select class="form-control" name="tipodocumento" id="tipodocumento" value="<?=($receptor)?$receptor->tipodocumento:''?>"> 
                <?php
                foreach ($tipodocumento as $key) {
                  ?>
                  <option><?=$key->tipodocumento?></option>
                  <?php
                }
                ?>
                </select>
    </div>
    <div class="col-sm-3"><b>N° de documentos:</b></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="documento" id="documento" value="<?=($receptor)?$receptor->documento:''?>" readonly>
    </div>
    <div class="col-sm-3"><b>Nombre:</b></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="nombres" id="nombres" value="<?=($receptor)?$receptor->nombres:''?>">
    </div>
    <div class="col-sm-3"><b>Apellido Paterno:</b></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="apellido_paterno" id="apellido_paterno" value="<?=($receptor)?$receptor->apellido_paterno:''?>"> 
    </div>
    <div class="col-sm-3"><b>Apellido Materno:</b></div>
    <div class="col-sm-9">
    <input type="text" class="form-control" name="apellido_materno" id="apellido_materno" value="<?=($receptor)?$receptor->apellido_materno:''?>"> 
    </div>

    <div class="col-sm-3"><b>Correo:</b></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="correo" id="correo" value="<?=($receptor)?$receptor->correo:''?>">
    </div>

    <div class="col-sm-3"><b>Correo Alternativo:</b></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="correopersonal" id="correopersonal" value="<?=($receptor)?$receptor->correopersonal:''?>">
    </div>
    

    <div class="col-sm-3"><b>Celular:</b></div>
    <div class="col-sm-9">
    <input type="text" class="form-control" name="celular" id="celular" value="<?=($receptor)?$receptor->celular:''?>">

    </div>
  </div>

  <text style="float:right;">(*) Campo obligatorio</text>
  <br>
  <text style="float:right;">(*) El usuario y clave para que el ciudadano acceda a la notificación electrónica será
        el número del documento registrado
  </text>

</div>

<!--MODELO-->
<input style="float:right;" class="btn btn-danger" type="reset" value="Cancelar" data-toggle="modal" data-target=".bd-example-modal-lg">
<input style="float:right;margin-right:5px;" type="submit" class="btn btn-success" value="GUARDAR RECEPTORES">  



</div>

</form>


<script>
    function guardar_receptor(id='formulario'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_receptor')}}",  
              type: 'POST',
              data: formData,
              dataType: "json",
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  
              },
              success: function(data){
                formulario.reset();
                tabla_receptor();
                alert('Guardado');
                $("#div_modal01").click();
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
            });
        }


        function fboxtipo(){
            if( $("input[name=boxtipo]:checked").val() == 'personalizado' ){
                $(".permisos").css('display','');
                $("#ver_esp").css('display','none');
                $("#edit_esp").css('display','');
            }else{
                $(".permisos").css('display','none');
            }
        }
        
        function verlaravel(){
            if($("input[name=laravel]:checked").val()=='0'){
                $(".no_laravel").css("display","");
                $(".si_laravel").css("display","none");
            }else{
                $(".no_laravel").css("display","none");
                $(".si_laravel").css("display","");
            }
        }

        $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
    
});


</script>

<script>
 function btn_editar(){
    $("#div_texto1").css('display','none');
    $("#div_texto2").css('display','');
 }
</script>

