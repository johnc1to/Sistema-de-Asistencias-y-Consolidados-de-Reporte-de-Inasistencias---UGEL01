<form id="formularioEsp" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="anadirfichaespecialista_iiee();return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b></b></h4>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="idficha" value="<?=$ficha['idFic']?>">
        <input type="hidden" name="idespecialista" value="<?=$session['idespecialista']?>">        
       <div id="testmodal" style="padding: 5px 20px;"> 
       
          <div class="row">
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Institución:</b></div>
              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select name="codlocal" id="codlocal" class="form-control select2 js-example-basic-single" required>
                  <option value="">Elija la institucion</option>
                  <?php
                  //codlocal idmodalidad
                  foreach ($iiee as $key) {
                  $key = (Array)$key;
                  ?>
                  <option value="<?=$key['codlocal']?>,<?=$key['idmodalidad']?>"><?=$key['institucion']?></option>
                  <?php
                  }
                  ?>
                </select>       
              </div>
          </div>
          <br>
          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Fecha de Ficha:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input name="fecProRec" id="fecProRec" type="date" value="{{date('Y-m-d')}}"></div>
          </div>
          

        </div>
        
        
        
          
    </div>
    @csrf
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_ficha">Grabar</button>
      <button type="button" class="btn btn-danger btn-default antoclose" data-dismiss="modal">Cancelar</button>
    </div>
</form>

    <script>
      function anadirfichaespecialista_iiee(id='formularioEsp'){
    var ifimagen = true;
    //$('#btn_enviar').prop('disabled',true);
      if(true){
          //información del formulario
          var formData = new FormData($("#"+id)[0]);
          
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('anadirfichaespecialista_iiee')}}',  
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

$('#codlocal').select2({dropdownParent: "#popup01",'width':'100%'});

</script>