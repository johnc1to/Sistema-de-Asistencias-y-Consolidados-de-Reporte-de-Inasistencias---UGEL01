<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>CONSOLIDADO DE TRABAJO REMOTO</b></h4>
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  
</div>
<div class="modal-body table-responsive">

    <b>OFICINAS QUE HAN PRESENTADO:</b>
    <br>
    <?php
    if($culminado){
        foreach ($culminado as $key){
         echo '<a target="_blank" href="'.(($key->docfirmado)?'.'.$key->docfirmado:'pdfreporteasistencia?idtrabajo='.$key->idtrabajo).'"><b>'.$key->areacorta.':</b> '.$key->equipo.'</a>'.'<br>';
        }
    }
    ?>

<div>
    <b>Area: </b>
    <select class="form-control" id="r_area" onchange="t_reporte_filtro();">
    <option value="">Todos</option>
    <?php
    foreach ($areas as $key){
    ?>
    <option value="<?=$key->idarea?>"><?=$key->area?></option>
    <?php
    }
    ?>
    </select>
    <b>Regimen laboral: </b>
    <select class="form-control" id="r_regimen_laboral" onchange="t_reporte_filtro();">
        <option value="">Todos</option>
        <option>CAP decreto legislativo 276</option>
        <option>Persona bajo la ley 29944</option>
        <option>Practicante decreto legislativo 1401</option>
        <option>CAS decreto legislativo 1057</option>
        <option>Gerentes públicos Decreto legislativo 1024</option>
    </select>
</div>

<form id="subirfirma" class="divsubir" enctype="multipart/form-data" style="margin-top:15px;margin-bottom:15px;width:100%;display:none;" onsubmit="return false;">
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

<form id="subirvisto" class="divsubir" enctype="multipart/form-data" style="margin-top:15px;margin-bottom:15px;width:100%;display:none;" onsubmit="return false;">
    <div class="row">
	    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
	        <input type="text" name="idespecialista" style="display:none;" value="<?=$session['idespecialista']?>">
            <h5 style="font-weight:bold;">Subir visto escaneado</h5>
		    <input type="file" name="txtvisto" class="form-control" onchange="validar_firma(this,'PNG,JPG,JPEG');">
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		    <div class="logo"><?=($session['visto'])?'<img style="border: 4px solid #000;" height="100px" src=".'.$session['visto'].'">':'<b style="color:red;font-size:20px;">El visto escaneado debe estar en un fondo blanco.</b>'?></div>
	    </div>
    </div>
    @csrf
</form>

<div>
    <a target="_blank" href="#" class="btn btn-info" id="descargarpdf" onclick="descargarpdf(this,0);">Descargar en PDF</a>
    <a href="#" class="btn btn-danger" onclick="if($('#subirfirma').css('display')=='none'){ $('.divsubir').css('display',''); }else{ $('.divsubir').css('display','none'); }">Subir firma/visto escaneado</a>
    <a target="_blank" href="#" class="btn btn-success" id="btnfirmar" onclick="descargarpdf(this,1);" style="<?=($session['firma'])?'':'display:none;'?>">Firmar PDF</a>
    <br><b>Para firmar el consolidado la Ugel01, debe haber firmado el consolidado de su equipo.</b>
    <br><br>
</div>



<table table class="display table table-bordered table-striped table-dark" id="t_reporte" style="color:#000;text-align:center;width:100%;background-color:Purple;">
<thead>
  <tr style="font-size:10px;" class="">
      <td style="min-width:40px;color:#fff;" ><b>DNI</b></td>
      <td style="min-width:45px;color:#fff;" ><b>Nombres</b></td>
      <td style="min-width:100px;color:#fff;" ><b>Apellido Paterno</b></td>
      <td style="min-width:100px;color:#fff;" ><b>Apellido Materno</b></td>
      <td style="min-width:100px;color:#fff;" ><b>Regimen Laboral</b></td>
      <td style="min-width:100px;color:#fff;" ><b>Area</b></td>
      <td style="min-width:100px;color:#fff;" ><b>Equipo</b></td>
      <td style="min-width:100px;color:#fff;" ><b>Cargo</b></td>
      <td style="min-width:100px;color:#fff;" ><b>Trabajo remoto</b></td>
      <td style="min-width:20px;color:#fff;" ><b>Total horas laboradas</b></td>
      <td style="min-width:20px;color:#fff;" ><b>Total dias laborados</b></td>
      <td style="min-width:20px;color:#fff;" ><b>Total a descontar</b></td>
      <td style="min-width:20px;color:#fff;" ><b>Trabajo remoto</b></td>
      <td style="min-width:20px;color:#fff;" ><b>Trabajo presencia</b></td>
      <td style="min-width:20px;color:#fff;" ><b>Trabajo mixto</b></td>
      <?php
      for ($i=0; $i < count($fechas); $i++) {
      ?><td style="min-width:20px;color:#fff;" ><b><?=$fechas[$i]->textodia?></b></td><?php
      }
      ?>
      
  </tr>
</thead>
<tbody style="font-size:10px;"></tbody>
</table>

</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>
</div>

<script type="text/javascript">

function descargarpdf(athis,firma=0){
    $(athis).prop('href',"{{route('pdfreporteasistenciamensual')}}?anio=<?=$anio?>&idmes=<?=$idmes?>&firmar="+firma+"&idarea="+$('#r_area').val()+"&regimen_laboral="+$('#r_regimen_laboral').val());
}

var gthis;
function validar_firma(athis,extencion){
        gthis = athis;
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
            
            $('#btn_firmar').prop('disabled',false);
            subirfirma(athis);
            //$("#"+id).css('display','none');
            //$("#"+id).parent().children('.alert_archivo').html("<img height='60px' src='<?=asset('assets/images/3.gif')?>'>"+"<br>"+"<span class='bg-success'  style='font-size:12px;padding:3px;'>Subiendo archivo: "+fileName+"</span>");
            //f_enviar(id);
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
                $("#btn_firmar").prop('disabled',true);
              },
              //una vez finalizado correctamente
              success: function(data){
                  if(data){
                      $(athis).parent().parent().children('div').children('.logo').html('<img style="border: 4px solid #000;" height="100px" src=".'+data+'">');
                      //$(".logo").html('<img style="border: 4px solid #000;" height="100px" src=".'+data+'">');
                      $("#btnfirmar").css('display','');
                      alert('Firma subida');
                  }
              },
              error: function(){
                    alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
          });
    }else{
      alert('Llene todos los campos');
    }
}

function t_reporte_filtro(){
    var data = [];
    for (var i = 0; i < datatable6.length; i++) {
        if( $("#r_area").val()=="" || $("#r_area").val()==datatable6[i]['id_area'] ){
            if( $("#r_regimen_laboral").val()=="" || $("#r_regimen_laboral").val()==datatable6[i]['regimen_laboral'] ){
                //datatable6[i]['t_trabajoremoto'] = 't_trabajoremoto';
                if(datatable6[i]['t_trabajoremoto']=='R'  ) datatable6[i]['t_trabajoremoto'] = 'REMOTO';
                if(datatable6[i]['t_trabajoremoto']=='P'  ) datatable6[i]['t_trabajoremoto'] = 'PRESENCIAL';
                if(datatable6[i]['t_trabajoremoto']=='M'  ) datatable6[i]['t_trabajoremoto'] = 'MIXTO';
                if(datatable6[i]['t_trabajoremoto']=='R,P') datatable6[i]['t_trabajoremoto'] = 'MIXTO';
                if(datatable6[i]['t_trabajoremoto']=='P,R') datatable6[i]['t_trabajoremoto'] = 'MIXTO';
                if(datatable6[i]['t_trabajoremoto']=='P,M') datatable6[i]['t_trabajoremoto'] = 'MIXTO';
                if(datatable6[i]['t_trabajoremoto']=='M,P') datatable6[i]['t_trabajoremoto'] = 'MIXTO';
                if(datatable6[i]['t_trabajoremoto']=='R,M') datatable6[i]['t_trabajoremoto'] = 'MIXTO';
                if(datatable6[i]['t_trabajoremoto']=='M,R') datatable6[i]['t_trabajoremoto'] = 'MIXTO';
                data.push(datatable6[i]);
            }
        }
    }
    table6.clear().draw();
    table6.rows.add(data).draw();
}

var datatable6 = <?=json_encode($registro)?>;
table6 = $("#t_reporte").DataTable( {
                    dom: 'Bfrtip',
                    buttons: ['excel'],
                    "iDisplayLength": 8,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                    },
                    data:[],
                    "columns": [
                        { "data": "ddni" },
                        { "data": "esp_nombres" },
                        { "data": "esp_apellido_paterno" },
                        { "data": "esp_apellido_materno" },
                        { "data": "regimen_laboral" },
                        { "data": "area" },
                        { "data": "equipo" },
                        { "data": "cargo" },
                        { "data": "t_trabajoremoto" },
                        { "data": "totalhoras" },
                        { "data": "total" },
                        { "data": "descuento" },
                        { "data": "remoto" },
                        { "data": "presencia" },
                        { "data": "mixto" },
                        <?php
                        for ($i=0; $i < count($fechas); $i++) {
                        ?>{ "data": "fila<?=$i?>" },<?php
                        }
                        ?>
                        
                    ],                          
                    rowCallback: function (row, data) {},
                    filter: true,
                    info: true,
                    ordering: true,
                    processing: true,
                    retrieve: true                          
                });

t_reporte_filtro();
</script>
