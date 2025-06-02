<form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="anadirfichadirector();return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b></b></h4>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <?php

      ?>
    </div>
    <div class="modal-body">
        <input type="hidden" name="idficha" value="<?=$ficha['idFic']?>">
       <div id="testmodal" style="padding: 5px 20px;">        
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>DNI:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="" name="dniDoc" onkeyup="docentevalidar_dni(this.value);" class="form-control" maxlength="8" required onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"></div>
          </div>

          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Nombres:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="" name="nomDoc" class="form-control" required></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Apellido Paterno:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="" name="apePatDoc" class="form-control" required></div>
          </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Apellido Materno:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="" name="apeMatDoc" class="form-control" required></div>
          </div>
          
          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Nivel:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select name="idNivDoc" class="form-control" onchange="selectnivel();" required>
                    <option></option>
                    <option value="7">EBA - AVANZADO</option>
                    <option value="6">EBA - INCIAL E INTERMEDIO</option>
                    <option value="5">INICIAL</option>
                    <option value="4">PRIMARIA</option>
                    <option value="3">SECUNDARIA</option>
                    <option value="2">EBE</option>
                    <option value="1">CETPRO</option>
                </select>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Grado:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select name="graDoc" class="form-control" required>
                    <option></option>
                </select>
            </div>
          </div>

            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Seccion:</b></div>
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="" name="secDoc" class="form-control" required></div>
            </div>
          
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Celular:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="" name="telDoc" class="form-control" required></div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Correo:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input tyle="text" value="" name="corDoc" class="form-control" required></div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Selecione los meses en el que realizara el monitoreo:</b></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php
                $nombremes = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
                //date('n')
                $nromes = 3;
                for ($i=$nromes; $i <=12; $i++) {
                $mes = date('Y-'.str_pad($i,1,"0", STR_PAD_LEFT).'-01');
                ?>
                <input type="radio" name="boxmes[]" value="<?=date("Y-m-t", strtotime($mes))?>"> <?=$nombremes[$i]?><br>
                <?php
                }
                ?>
            </div>
          </div>
      </div>       	
    </div>
    @csrf
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_ficha" disabled>Grabar</button>
      <button type="button" class="btn btn-danger antoclose" data-dismiss="modal">Cancelar</button>
    </div>
</form>


<script type="text/javascript">
    function select_cantmonit(){
        var text = '<b>Selecione los meses en el que realizara el monitoreo:</b><br>';
        var cant = $("#cantmonit").val();
        for (let i = 0; i < cant; i++) {
            
            
        }
    }

    function anadirfichadirector(id='formulario01'){
    var ifimagen = true;
    //$('#btn_enviar').prop('disabled',true);
      if(true){
          //información del formulario
          var formData = new FormData($("#"+id)[0]);
          
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('anadirfichadirector')}}',  
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
                $("#btn_ficha").prop('disabled',true);
              },
              //una vez finalizado correctamente
              success: function(data){
                  $("#fc_popup").click();
                  alert('Ficha Guardado');   
                  ver_ficha_ie();               
              },
              error: function(){
                    alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
    }else{
      alert('Llene todos los campos');
    }
}

function docentevalidar_dni(dni){
    ajax_data = {
      "dni" : dni,
      "alt" : Math.random()
    }
    if(dni.length>7){
    $.ajax({
                    type: "GET",
                    url: '{{route('docentevalidar_dni')}}',
                    data: ajax_data,
                    dataType: "json",
                    beforeSend: function(){
                          //imagen de carga
                          $("#btn_ficha").prop('disabled',true);
                    },
                    error: function(){
                          alert("error peticiÃ³n ajax");
                    },
                    success: function(data){
                        if(data){
                        $("input[name=nomDoc")   .val(data['nombres']);
                        $("input[name=apePatDoc").val(data['apellipat']);
                        $("input[name=apeMatDoc").val(data['apellimat']);
                        $("input[name=telDoc")   .val(data['celular']);
                        $("input[name=corDoc")   .val(data['correo']);
                        $("select[name=idNivDoc").val(data['idnivel']);
                        if(data['idnivel']==1){ $("select[name=graDoc]").html('<option value="ETP">ETP</option>');}
                        if(data['idnivel']==2){ $("select[name=graDoc]").html('<option value=""></option><option value="3EBI">3EBE INICIAL</option><option value="4EBI">4EBE INICIAL</option><option value="5EBI">5EBE INICIAL</option><option>1EBE</option><option>2EBE</option><option>3EBE</option><option>4EBE</option><option>5EBE</option><option>6EBE</option>') }
                        if(data['idnivel']==3){ $("select[name=graDoc]").html('<option value=""></option><option>1SEC</option><option>2SEC</option><option>3SEC</option><option>4SEC</option><option>5SEC</option>') }
                        if(data['idnivel']==4){ $("select[name=graDoc]").html('<option value=""></option><option>1PRI</option><option>2PRI</option><option>3PRI</option><option>4PRI</option><option>5PRI</option><option>6PRI</option>') }
                        if(data['idnivel']==5){ $("select[name=graDoc]").html('<option value=""></option><option>2INI</option><option>3INI</option><option>4INI</option><option>5INI</option>');}
                         if(data['idnivel']==7){ $("select[name=graDoc]").html('<option value=""></option><option>1EBA</option><option>2EBA</option><option>3EBA</option><option>4EBA</option>');}
                        if(data['idnivel']==6){ $("select[name=graDoc]").html('<option value=""></option><option>1INI</option><option>2INI</option><option>1INT</option><option>2INT</option><option>3INT</option>');}
                        }
                        
                        $("#btn_ficha").prop('disabled',false);
                    }
              });
    }
}

function selectnivel(){
    var idnivel = $("select[name=idNivDoc").val();
    if(idnivel==1){ $("select[name=graDoc]").html('<option value="ETP">ETP</option>');}
    if(idnivel==2){ $("select[name=graDoc]").html('<option value=""></option><option value="3EBI">3EBE INICIAL</option><option value="4EBI">4EBE INICIAL</option><option value="5EBI">5EBE INICIAL</option><option>1EBE</option><option>2EBE</option><option>3EBE</option><option>4EBE</option><option>5EBE</option><option>6EBE</option>') }
    if(idnivel==3){ $("select[name=graDoc]").html('<option value=""></option><option>1SEC</option><option>2SEC</option><option>3SEC</option><option>4SEC</option><option>5SEC</option>') }
    if(idnivel==4){ $("select[name=graDoc]").html('<option value=""></option><option>1PRI</option><option>2PRI</option><option>3PRI</option><option>4PRI</option><option>5PRI</option><option>6PRI</option>') }
    if(idnivel==5){ $("select[name=graDoc]").html('<option value=""></option><option>2INI</option><option>3INI</option><option>4INI</option><option>5INI</option>');}
     if(idnivel==7){ $("select[name=graDoc]").html('<option value=""></option><option>1EBA</option><option>2EBA</option><option>3EBA</option><option>4EBA</option>');}
     if(idnivel==6){ $("select[name=graDoc]").html('<option value=""></option><option>1INI</option><option>2INI</option><option>1INT</option><option>2INT</option><option>3INT</option>');}
}

</script>