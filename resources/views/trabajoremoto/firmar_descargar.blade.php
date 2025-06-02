         
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>REPORTE DE ASISTENCIA <?=$trabajo['mes']?> <?=$trabajo['anio']?></b></h4>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      
    </div>
    <div class="modal-body">

       <div id="testmodal" style="padding: 5px 20px;">
            
            <!--
            <div class="row">
			    <div class=".col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <h5 style="font-weight:bold;">Subir firma escaneada</h5>
    			    <input type="file" name="txtfirma" class="form-control" onchange="validar_firma(this,'PNG,JPG,JPEG');">
    			</div>
    			<div class=".col-xs-12 col-sm-6 col-md-6 col-lg-6">
    			    <div id="logo"><?=($session['firma'])?'<img style="border: 4px solid #000;" height="100px" src=".'.$session['firma'].'">':'<b style="color:red;font-size:20px;">La firma escaneada debe tener el sello de Jefe o coordinador en un fondo blanco.</b>'?></div>
			    </div>
            </div>
            -->
            
            <form id="subirfirma" enctype="multipart/form-data" style="margin-top:15px;margin-bottom:15px;width:100%;" onsubmit="return false;">
                <div class="row">
            	    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            		    <input type="text" name="idespecialista" style="display:none;" value="<?=$session['idespecialista']?>">
                        <h5 style="font-weight:bold;">Subir firma escaneada</h5>
            		    <input type="file" name="txtfirma" class="form-control" onchange="validar_firma(this,'PNG,JPG,JPEG');">
            		</div>
            		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            		    <div class="logo"><?=($session['firma'])?'<img style="border: 4px solid #000;" height="100px" src=".'.$session['firma'].'">':'<b style="color:red;font-size:20px;">La firma escaneada debe tener el sello de Jefe o coordinador en un fondo blanco.</b>'?></div>
            	    </div>
                </div>
            @csrf
            </form>
            <form id="subirvisto" enctype="multipart/form-data" style="margin-top:15px;margin-bottom:15px;width:100%;" onsubmit="return false;">
                <br>
                <div class="row">
            	    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <h5 style="font-weight:bold;">Subir visto escaneado</h5>
            		    <input type="file" name="txtvisto" class="form-control" onchange="validar_firma(this,'PNG,JPG,JPEG');">
            		</div>
            		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            		    <div class="logo"><?=($session['visto'])?'<img style="border: 4px solid #000;" height="100px" src=".'.$session['visto'].'">':'<b style="color:red;font-size:20px;">El visto escaneado debe estar en un fondo blanco.</b>'?></div>
            	    </div>
                </div>
                @csrf
            </form>
            
            <form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="firmarreporteasistencia();return false;"> 
            
            <input type="text" style="display:none;" name="idespecialista" value="<?=$session['idespecialista']?>">
		    <input type="text" style="display:none;" name="idtrabajo" value="<?=$trabajo['idtrabajo']?>">
		    <input type="text" style="display:none;" name="txt_anio" value="<?=$trabajo['anio']?>">
		    <input type="text" style="display:none;" name="txt_mes" value="<?=$trabajo['mes']?>">
		    <input type="text" style="display:none;" name="txt_areacorta" value="<?=$trabajo['areacorta']?>">
            
            <div class="row"><div class="col-xs-12"><br></div></div>
            
            <div class="row"><div class="col-xs-12"><h5 style="font-weight:bold;">REPORTE:</h5></div></div>
            
            <div class="row">
                <div class="col-xs-12">
                        <a style="font-size:16px;" target="_blank" href="<?=($trabajo['docfirmado'])?'.'.$trabajo['docfirmado']:route('pdfreporteasistencia').'?idtrabajo='.$trabajo['idtrabajo']?>" class="btn btn-danger">Descargar</a>
                    <input style="font-size:16px;" type="submit" class="btn btn-success" id="btn_firmar" value="Firmar" <?=($session['firma'])?'':'disabled'?>>  </div>
            </div>
            @csrf
            </form>
            
            <div class="row"><div class="col-xs-12"><br></div></div>
            
            <div class="row">
              <!--<object width="100%" height="800px" data="{{route('pdfreporteasistencia')}}?idtrabajo=<?=$trabajo['idtrabajo']?>"></object>-->
              <object width="100%" height="800px" data="<?=($trabajo['docfirmado'])?'.'.$trabajo['docfirmado']:route('pdfreporteasistencia').'?idtrabajo='.$trabajo['idtrabajo']?>"></object>
            </div>
          
          <?php
           //print_r($trabajo);
          ?>

      </div>       	
    </div>
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_especialista" style="display:none;">Guardar</button>
      <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>
    </div>


<script type="text/javascript">
var gathis;
    function validar_firma(athis,extencion){
        gathis = athis;
        var file = $(athis)[0].files[0];
        //obtenemos el nombre del archivo
        var fileName = file.name;
        //obtenemos la extensión del archivo
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        //obtenemos el tamaño del archivo
        var fileSize = file.size;
        //obtenemos el tipo de archivo image/png ejemplo
        var fileType = file.type;
        
        var l_Extension_valida = ((extencion)?extencion:'').split(',');
        var id = $(athis).parent()[0].id;
        
        if( l_Extension_valida.indexOf(fileExtension.toUpperCase())>-1 ){
            
            //$('#btn_firmar').prop('disabled',false);
            //$("#"+id).css('display','none');
            //$("#"+id).parent().children('.alert_archivo').html("<img height='60px' src='<?=asset('assets/images/3.gif')?>'>"+"<br>"+"<span class='bg-success'  style='font-size:12px;padding:3px;'>Subiendo archivo: "+fileName+"</span>");
            subirfirma(athis);
        }else{
            alert('Archivo '+fileExtension+' no valido, debe adjuntar solo: '+l_Extension_valida.toString());
            //$("#"+id).parent().children('.alert_archivo').html("<span class='bg-danger' style='font-size:12px;padding:1px;'>"+"Archivo no valido, debe adjuntar solo: "+l_Extension_valida.toString()+"</span>");
            //alert('Archivo '+fileExtension+' no valido, debe adjuntar solo: '+l_Extension_valida.toString());
            
            $(athis).val('');
        }
    }

    function subirfirma(athis){
        var ifimagen = true;
        var id = $(athis).parent().parent().parent().prop('id');
        //$('#btn_enviar').prop('disabled',true);
          if(true){
              //información del formulario
              var formData = new FormData($("#"+id)[0]);
              
              var message = "";
              //hacemos la petición ajax  
              $.ajax({
                  url: '{{route('subirfirma')}}',  
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
                      if(data){
                          $(athis).parent().parent().children('div').children('.logo').html('<img style="border: 4px solid #000;" height="100px" src=".'+data+'">');
                          
                          alert('Firma subida');
                      }
                  },
                  error: function(){
                        alert('Sesión concluida. Recargue la página');
                  }
              });
        }else{
          alert('Llene todos los campos');
        }
    }

    function firmarreporteasistencia(id='formulario01'){
    var ifimagen = true;
    //$('#btn_enviar').prop('disabled',true);
      if(true){
          //información del formulario
          var formData = new FormData($("#"+id)[0]);
          
          var message = "";
          //hacemos la petición ajax  
          $.ajax({
              url: '{{route('firmarreporteasistencia')}}',  
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
                $("#btn_firmar").prop('disabled',true);
              },
              //una vez finalizado correctamente
              success: function(data){
                  $("#popuplg .modal-content").load('{{route('firmar_descargar')}}?idtrabajo=<?=$trabajo['idtrabajo']?>');
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