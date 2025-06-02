@extends('layout_especialista/cuerpo')
@section('html')
<!-- ***********************LIBRERIAS PARA LA TABLA**************************** -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<!-- ***********************LIBRERIAS PARA LA TABLA**************************** -->

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REPORTE DE ASISTENCIA</b></h5>
        <div class="position-relative form-group">
            <div>
                <b style="color:#000;">AÑO: </b> 
					  <select class="form-control" style="color:#000;" id="select_anio" onchange="ver_asistenciaoficina();">
					    <option>2024</option>
					    <option>2023</option>
                      </select>
					  
					  <b style="color:#000;">MES: </b> 
					  <select class="form-control" style="color:#000;" id="select_mes" onchange="ver_asistenciaoficina();">
					    <option value="01">ENERO</option>
					    <option value="02">FEBRERO</option>
					    <option value="03">MARZO</option>
                        <option value="04">ABRIL</option> 
                        <option value="05">MAYO</option>
                        <option value="06">JUNIO</option>
                        <option value="07">JULIO</option>
                        <option value="08">AGOSTO</option>
                        <option value="09">SEPTIEMBRE</option>
                        <option value="10">OCTUBRE</option>
                        <option value="11">NOVIEMBRE</option>
                        <option value="12">DICIEMBRE</option>
					  </select>

                      <b style="color:#000;">OFICINA: </b> 
					  <select class="form-control" style="color:#000;" id="select_equipo" onchange="ver_asistenciaoficina();">
                      <?php
                        if($equipo){
                        foreach ($equipo as $key) {
                        ?><option value="<?=$key->SedeOficinaId?>" <?=($key->SedeOficinaId==$session['id_oficina'])?'selected':''?>><?=$key->Descripcion?></option><?php
                        }
                        }else{
                        ?><option value="<?=$session['id_oficina']?>"><?=$session['equipo']?></option><?php
                        }
                      ?>
                      </select>
					  <br>
					  <button id="btn_consultar" class="btn btn-success" onclick="ver_asistenciaoficina();">Consultar</button>
					  
					  <br><br>
                        <div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    	   <span style="background-color:rgb(255,255,0);"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Sabado y Domingo
                    	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    	   <span style="background-color:rgb(255,199,206);">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Feriado
                    	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    	   <?php if($session['id_oficina']==77){ ?>
                    	   <b>Ver:</b>&nbsp;&nbsp;
                    	   <input  name="verhoras" id="verhoras" type="checkbox" value="1" onclick="ver_asistenciaoficina();"> Horas trabajadas
                    	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    	   <b>Hora de ingreso:</b> 08:00 am &nbsp;&nbsp;&nbsp;&nbsp; <b>Refrigerio:</b> 45 minutos 
                    	   <?php } ?>
                    	</div>
                	   <br>
                	   
					  <div id="reporte" class="table-responsive">
					      
					  </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

function ver_asistenciaoficina(){
    ajax_data = {        
   "anio"           : $("#select_anio").val(),
   "mes"            : $("#select_mes option:selected").html(),
   "idmes"          : $("#select_mes").val(),
   "area"           : "<?=$session['area']?>",
   "areacorta"      : "<?=$session['areacorta']?>",
   "idarea"         : (<?=$todo?>)?$("#select_equipo").val():<?=$session['id_area']?>,
   "equipo"         : $("#select_equipo option:selected").html(),
   "idequipo"       : (<?=$todo?>)?'0':$("#select_equipo").val(),
   "idespecialista" : "<?=$session['idespecialista']?>",
   "verhoras"       : ($("#verhoras").prop('checked'))?1:0,
   "todo"           : '<?=$todo?>',
   "alt"    : Math.random()
}

$.ajax({
                type: "GET",
                url: '{{route('ver_asistenciaoficina')}}',
                data: ajax_data,
                dataType: "html",
                beforeSend: function(){
                      //imagen de carga
                      $("#reporte").html('<img src="https://siic01.ugel01.gob.pe/public/load/load10.gif">');
                },
                error: function(){
                      alert("error peticiÃ³n ajax");
                },
                success: function(data){
                    $("#reporte").html(data);
                    //$("#reporte tr td").css('padding','3px 5px');
                }
});

}


$("#select_mes").val("<?=str_pad(date('m'), 2, "0", STR_PAD_LEFT)?>");

ver_asistenciaoficina();

</script>

@endsection







