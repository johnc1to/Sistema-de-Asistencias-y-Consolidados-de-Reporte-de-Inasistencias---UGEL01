<form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="guardar_especialista();return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>ESPECIALISTA</b></h4>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      
    </div>
    <div class="modal-body">

       <div id="testmodal" style="padding: 5px 20px;">
            <?php
            //print_r($data);
            ?>
           <div class="row" style="display:none;">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Idespecialista:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['idespecialista']:''?>" name="idespecialista" class="inputesp form-control"></div>
          </div>

          <div class="row" style="display:none;">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>especialista_creo:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=$especialista_creo?>" name="especialista_creo" class="inputesp form-control"></div>
        </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>DNI:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['ddni']:''?>" name="ddni" onkeyup="validar_dni(this.value);" class="inputesp form-control" maxlength="8" required onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"></div>
          </div>
          <!--
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Usuario:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['usuario']:''?>" name="usuario" class="inputesp inputdatos form-control" required></div>
          </div>
            -->
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Nombres:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['esp_nombres']:''?>" name="esp_nombres" class="inputesp inputdatos form-control" required></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Apellido Paterno:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['esp_apellido_paterno']:''?>" name="esp_apellido_paterno" class="inputesp inputdatos form-control" required></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Apellido Materno:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['esp_apellido_materno']:''?>" name="esp_apellido_materno" class="inputesp inputdatos form-control" required></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Cargo:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['cargo']:''?>" name="cargo" class="inputesp inputdatos form-control" required></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Area:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><select onchange="listar_oficinas(this.value);" name="id_area" class="inputesp inputdatos form-control" required></select></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Equipo:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><select name="id_oficina" class="inputesp inputdatos form-control" required></select></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Celular:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['telefono1']:''?>" name="telefono1" class="inputesp inputdatos form-control" maxlength="9" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Correo:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="<?=($data)?$data['correo1']:''?>" name="correo1" class="inputesp inputdatos form-control"></div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Regimen laboral:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select class="inputesp inputdatos form-control" name="regimen_laboral" required>
                    <option value="">Elija el regimen laboral</option>
                    <option>CAP decreto legislativo 276</option>
                    <option>Persona bajo la ley 29944</option>
                    <option>Practicante decreto legislativo 1401</option>
                    <option>CAS decreto legislativo 1057</option>
                    <option>Gerentes públicos Decreto legislativo 1024</option>
                </select>
            </div>
        </div>
        <!--
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Administrador SEPROC:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">adminSeproc
                  <input type="radio" name="adminSeproc" value="0" <?=($data)?(($data['adminSeproc']=='0')?'checked':''):''?> > NO
                  <input type="radio" name="adminSeproc" value="1" <?=($data)?(($data['adminSeproc']=='1')?'checked':''):''?> style="margin-left: 5px;"> SI
              </div>
          </div>
        -->
      </div>       	
    </div>
    @csrf
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_especialista" style="display:none;">Guardar</button>
      <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>
    </div>
</form>

<script type="text/javascript">
    g_list_areas = <?=json_encode($areas)?>;
    function listar_areas(idarea=0){
      var opt = '';
        opt += (g_list_areas.length>1)?'<option value="-1">'+''+'</option>':'';
      for (var i = 0; i < g_list_areas.length; i++) {
        var fila = g_list_areas[i];
        opt += '<option value="'+fila['SedeOficinaId']+'" '+( (fila['SedeOficinaId']==idarea)?' selected':'' )+'>'+fila['Descripcion']+'</option>';
      };
      $("select[name=id_area]").html(opt);
      if(g_list_areas.length==1){ listar_oficinas(g_list_areas[0]['SedeOficinaId']); }
    }
    
    function listar_oficinas(id_area,id_equipo=0){
          var n_area    = -1;
          for (var i = 0; i < g_list_areas.length; i++) {
            var area = g_list_areas[i];
              n_area = (area['SedeOficinaId']==id_area)?i:n_area;
          }
          var oficina = g_list_areas[n_area]['oficina'];
          var opt = '';
            opt += (oficina.length>1)?'<option value="">'+'Elija un Equipo'+'</option>':'';
            if(oficina){
              for (var i = 0; i < oficina.length; i++) {
                var fila = oficina[i];
                opt += '<option value="'+fila['SedeOficinaId']+'" '+( (fila['SedeOficinaId']==id_equipo)?' selected':'' )+'>'+fila['Descripcion']+'</option>';
              };
            }
    
      $("select[name=id_oficina]").html(opt);
    }

    listar_areas();
    <?php
    if($data){
    ?>
    $("select[name=regimen_laboral]").val("<?=$data['regimen_laboral']?>");
    $("#btn_especialista").css('display','');
    <?php
    }
    ?>

function validar_dni(dni){
    ajax_data = {
      "dni" : dni,
      "alt" : Math.random()
    }
    if(dni.length>7){
        $.ajax({
                type: "GET",
                url: '{{route('validar_dni_especialista')}}',
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
                    if(data){
                        alert('El especialista '+data['nombre']+' ya está registrado en el SIIC01');
                        $("#fc_popup").click();
                        $("#btn_especialista").css('display','none');
                        $(".inputdatos").prop('disabled',true);
                    }else{
                        $("#btn_especialista").css('display','');
                        $(".inputdatos").prop('disabled',false);
                    }
                }
        });
    }else{
        $("#btn_especialista").css('display','none');
    }
}

    function guardar_especialista(id='formulario01'){
    var ifimagen = true;
    //$('#btn_enviar').prop('disabled',true);
      if(true){
          //información del formulario
          var formData = new FormData($("#"+id)[0]);
          
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('guardar_especialista')}}',  
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
                $("#btn_especialista").prop('disabled',true);
              },
              //una vez finalizado correctamente
              success: function(data){
                  <?=($funcion)?$funcion:''?>
                  $("#fc_popup").click();
                  alert('Especialista Guardado');                  
              },
              error: function(){
                    alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
    }else{
      alert('Llene todos los campos');
    }
}
</script>