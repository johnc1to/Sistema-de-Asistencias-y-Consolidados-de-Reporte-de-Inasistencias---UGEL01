<form id="formularioEsp" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="anadirfichaespecialista();return false;">          
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
                <select name="codmod" id="codmod" class="form-control select2 js-example-basic-single" required>
                  <option value="">Elija la institucion</option>
                  <?php
                  foreach ($iiee as $key) {
                  $key = (Array)$key;
                  ?>
                  <option value="<?=$key['codmod']?>"><?=$key['institucion']?></option>
                  <?php
                  }
                  ?>
                </select>       
              </div>
          </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>Selecione los meses en el que realizara el monitoreo:</b></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php
                $nombremes = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
                $nromes = (date('n')==1)?date('n'):date('n')-2;
                $nromes = 5;
                for ($i=$nromes; $i <=12; $i++) {
                $mes = date('Y-'.str_pad($i,1,"0", STR_PAD_LEFT).'-01');
                ?>
                <input type="checkbox" name="boxmes[]" value="<?=date("Y-m-t", strtotime($mes))?>"> <?=$nombremes[$i]?><br>
                <?php
                }
                ?>
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
      function anadirfichaespecialista(id='formularioEsp'){
    var ifimagen = true;
    //$('#btn_enviar').prop('disabled',true);
      if(true){
          //información del formulario
          var formData = new FormData($("#"+id)[0]);
          
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('anadirfichaespecialista')}}',  
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

$('#codmod').select2({dropdownParent: "#popup01",'width':'100%'});

</script>