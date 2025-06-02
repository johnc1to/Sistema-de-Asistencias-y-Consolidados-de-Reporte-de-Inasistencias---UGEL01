<style>
.marcax {cursor: pointer;text-align: center; font-weight: bolder;}
.stotal1 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal2 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal3 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal4 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal5 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal6 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal7 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal8 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal9 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal10 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal11 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal12 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal13 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal14 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal15 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal16 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal17 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal18 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal19 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal20 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal21 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal22 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal23 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal24 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal25 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal26 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal27 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal28 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal29 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal30 { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.stotal   { background-color:rgb(241,241,241);cursor: not-allowed;border: 0; }
.msj{ color:red;font-size:10px; }
.msj b{ color:red;font-size:12px; }
.marcarx{ width:30px;font-weight: bold;cursor: pointer;text-align:center; }
.cabezera{font-weight: bold;text-align:center; background-color:rgb(217,217,217);}
.negritacentrado { font-weight: bold;text-align:center; }
.gris { background-color:rgb(217,217,217); }
.centro { text-align:center;  }
</style>

<?php
	$aldirectivo = ($ficha['tipFic']=='AL DIRECTIVO')?true:false;
	$aldocente   = ($ficha['tipFic']=='DIRECTIVO AL DOCENTE')?true:false;
	$romano = array('I','II','III','IV','V','VI','VII','VIII','XI','X');
	$nro=2;
?>
	<div class="modal-header">
		<h4 class="modal-title" id="myModalLabel" style="width:100%;text-align:center;font-weight:bold;font-size:22px;"><?=$ficha['nomFic']?><br><?=$ficha['desFic']?></h4>
	</div>
          <div class="modal-body">

             <div id="testmodal" style="padding: 5px 20px;">
              <!--<form id="antoform" class="form-horizontal calender" role="form" onsubmit="return false;">-->
              <div class="form-horizontal calender" role="form">
                <div class="form-group">
			<!--ACORDION-->
			<div class="row">
				<div class="col-md-12">
					<div id="accordion" class="accordion-wrapper mb-3">
						<div id="btn_enviar_ficha_ugel01" style="text-align:center;<?=($ficha_respondida)?(($ficha_respondida['grupo_respondido'])?'':'display:none;'):''?>">
							<?php
							$buscar  = array(".",",",")",":","<BR>","<br>"," ");
							$cambiar = array("","","","","","","");
							?>
						</div>
						
						<?php
						if($aldirectivo){
						?>
						<div class="card">
							<div id="headingOne" class="card-header">
								<button type="button" data-toggle="collapse" data-target="#collapseOneDIR" aria-expanded="true" aria-controls="collapseOne" class="text-left m-0 p-0 btn btn-link btn-block">
									<h5 class="m-0 p-0">II. DATOS DEL DIRECTIVO
										<span id="icoformDIR"><?=(($registro)?(($registro['fechaaplicacion'])?true:false):false)?'<span style="color:green;" class="pe-7s-check"></span>':'<span style="color:red;" class="pe-7s-diskette"></span>'?></span>
										<span style="padding:2px;float:right;" class="btn btn-danger">Despegar <span class="pe-7s-angle-down-circle"></span></span>
									</h5>
								</button>
							</div>
							<div data-parent="#accordion" id="collapseOneDIR" aria-labelledby="headingOne" class="collapse" style="">
								<div class="card-body table-responsive table-responsive" style="padding:0px;">
									<!--Cuerpo del formulario-->
									<form enctype="multipart/form-data" id="formDIR" method="post" style="width:100%;" onsubmit="guardar_receptores('formDIR');return false;">
										@csrf
										<input type="hidden" name="idDoc" value="<?=($registro)?$registro['idDoc']:''?>">
										<input type="hidden" name="idRec" value="<?=($registro)?$registro['idRec']:''?>">
										<table border="1" cellspacing="0" cellpadding="2" width="100%">
											<tr class="cabezera">
												<td colspan="2">Nombres Completos</td>
												<td colspan="2">Apellidos Completos</td>
												<td colspan="2">Documento de Identidad</td>
											</tr>
											<tr>
												<td colspan="2"><input name="nomRec"    value="<?=($registro)?$registro['nomRec']:''?>"></td>
												<td colspan="2"><input name="apePatRec" value="<?=($registro)?$registro['apePatRec']:''?>"> <input name="apeMatRec" value="<?=($registro)?$registro['apeMatRec']:''?>"></td>
												<td colspan="2"><input name="dniRec"    value="<?=($registro)?$registro['dniRec']:''?>" onkeyup="directorvalidar_dni(this.value);" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
											</tr>
											<tr>
												<td class="cabezera">Teléfono</td>
												<td><input name="telRec" value="<?=($registro)?$registro['telRec']:''?>"></td>
												<td class="cabezera">Correo electrónico</td>
												<td><input name="corRec" value="<?=($registro)?$registro['corRec']:''?>"></td>
												<td class="cabezera">Cargo</td>
												<td><input name="carRec" value="<?=($registro)?$registro['carRec']:''?>"></td>
											</tr>
										</table>

										<?php
										if($editarficha){
										?><button class="btn btn-success guardarficha" style="float:right;" <?=($registro)?'':'disabled'?>>GRABAR</button><?php
										}
										?>
									</form>
								</div>
							</div>
						</div>									
						<?php
						}
						?>

						<?php
						if($aldirectivo){
						?>
						<div class="card">
							<div id="headingOne" class="card-header">
								<button type="button" data-toggle="collapse" data-target="#collapseOneA" aria-expanded="true" aria-controls="collapseOne" class="text-left m-0 p-0 btn btn-link btn-block">
									<h5 class="m-0 p-0"><?=$romano[$nro++]?>. DATOS DEL ESPECIALISTA
										<span id="icoformA"><?=(($registro)?(($registro['fechaaplicacion'])?true:false):false)?'<span style="color:green;" class="pe-7s-check"></span>':'<span style="color:red;" class="pe-7s-diskette"></span>'?></span>
									    <span style="padding:2px;float:right;" class="btn btn-danger">Despegar <span class="pe-7s-angle-down-circle"></span></span>
									</h5>
								</button>
							</div>
							<div data-parent="#accordion" id="collapseOneA" aria-labelledby="headingOne" class="collapse" style="">
								<div class="card-body table-responsive table-responsive" style="padding:0px;">
									<!--Cuerpo del formulario-->
									<form enctype="multipart/form-data" id="formA" method="post" style="width:100%;" onsubmit="guardar_receptores('formA');return false;">
										@csrf
										<input type="hidden" name="idDoc" value="<?=($registro)?$registro['idDoc']:''?>">
										<input type="hidden" name="idRec" value="<?=($registro)?$registro['idRec']:''?>">
										<table border="1" cellspacing="0" cellpadding="2" width="100%">
											<tr class="cabezera">
												<td colspan="2">Nombres completos del especialista</td>
												<td colspan="2">Apellidos completos del especialista</td>
												<td colspan="2">Documento de Identidad</td>
											</tr>
											<tr>
												<td colspan="2"><?=($registro)?$registro['esp_nombres']:'-'?></td>
												<td colspan="2"><?=($registro)?$registro['esp_apellido_paterno']:'-'?> <?=($registro)?$registro['esp_apellido_materno']:''?></td>
												<td colspan="2"><?=($registro)?$registro['ddni']:'-'?></td>
											</tr>
											<tr>
												<td>Número de visita a la IE</td>
												<td><input name="nroVisRec" value="<?=($registro)?$registro['nroVisRec']:''?>" style="width:40px;" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
												<td>Fecha de aplicación</td>
												<td><input type="text" class="form-control flatfecha" name="fechaaplicacion" style="width:110px;" value="<?=($registro)?$registro['fechaaplicacion']:''?>" required/></td>
												<td>Hora de inicio/fin</td>
												<td>
												    <input type="text" class="form-control flathora" name="horainicioaplicacion" style="width:110px;" value="<?=($registro)?$registro['horainicioaplicacion']:''?>" required/>
												    <input type="text" class="form-control flathora" name="horaaplicacion" style="width:110px;" value="<?=($registro)?$registro['horaaplicacion']:''?>" required/>
												</td>
											</tr>
											<tr><!--AsiTecRec-->
												<td colspan="2">Medio por el cual se desarrolla la asistencia técnica</td>
												<td  colspan="2"><?=($registro)?$registro['AsiTecRec']:''?> 
													WhatsApp   (<input type="radio" name="AsiTecRec" value="WhatsApp" <?=($registro)?((($registro['AsiTecRec']=='WhatsApp')?'checked':'')):''?>>) 
													Zoom       (<input type="radio" name="AsiTecRec" value="Zoom" <?=($registro)?((($registro['AsiTecRec']=='Zoom')?'checked':'')):''?>>) 
													Meet       (<input type="radio" name="AsiTecRec" value="Meet" <?=($registro)?((($registro['AsiTecRec']=='Meet')?'checked':'')):''?>>) 
													Ms Teams   (<input type="radio" name="AsiTecRec" value="Ms Teams" <?=($registro)?((($registro['AsiTecRec']=='Ms Teams')?'checked':'')):''?>>) 
													Presencial (<input type="radio" name="AsiTecRec" value="Presencial" <?=($registro)?((($registro['AsiTecRec']=='Presencial')?'checked':'')):''?>>)
												</td>
												<td>Teléfono</td>
												<td><?=($registro)?$registro['telefono1']:''?></td>
											</tr>
										</table>
										<?php
										if($editarficha){
										?><button class="btn btn-success guardarficha" style="float:right;" <?=($registro)?'':'disabled'?>>GRABAR</button><?php
										}
										?>
									</form>
								</div>
							</div>
						</div>									
						<?php
						}
						?>

						<?php
						if($ficha['DocMonFic']){
						?>
						<div class="card">
							<div id="headingOne" class="card-header">
								<button type="button" data-toggle="collapse" data-target="#collapseOneB" aria-expanded="true" aria-controls="collapseOne" class="text-left m-0 p-0 btn btn-link btn-block">
									<h5 class="m-0 p-0"><?=$romano[$nro++]?>. DATOS DEL DOCENTE MONITOREADO
										<span id="icoformB"><?=(($registro)?(($registro['nroEstPreRec'])?true:false):false)?'<span style="color:green;" class="pe-7s-check"></span>':'<span style="color:red;" class="pe-7s-diskette"></span>'?></span>
									    <span style="padding:2px;float:right;" class="btn btn-danger">Despegar <span class="pe-7s-angle-down-circle"></span></span>
									</h5>
								</button>
							</div>
							<div data-parent="#accordion" id="collapseOneB" aria-labelledby="headingOne" class="collapse" style="">
								<div class="card-body table-responsive table-responsive" style="padding:0px;">
									<!--Cuerpo del formulario-->
									<form enctype="multipart/form-data" id="formB" method="post" style="width:100%;" onsubmit="guardar_docente('formB');return false;">
										@csrf
										<input type="hidden" name="idDoc" value="<?=($registro)?$registro['idDoc']:''?>">
										<input type="hidden" name="idRec" value="<?=($registro)?$registro['idRec']:''?>">
										<table border="1" cellspacing="0" cellpadding="2" width="100%">
											<tr class="cabezera">
												<td><b>Nombres y apellidos</b></td>
												<td colspan="3"><b>DNI</b></td>
												<td><b>Grado y Sección</b></td>
												<td><b>Nivel</b></td>
												<td><b>N° de estudiantes matriculados</b></td>
												<td colspan="2"><b>N° de estudiantes atendidos</b></td>
											</tr>
											<tr>
												<td rowspan="2">
													<input width="100%" name="nomDoc"    value="<?=($registro)?$registro['nomDoc']:''?>" required>
													<input width="100%" name="apePatDoc" value="<?=($registro)?$registro['apePatDoc']:''?>" required>
													<input width="100%" name="apeMatDoc" value="<?=($registro)?$registro['apeMatDoc']:''?>" required>
												</td>
												<td rowspan="2" colspan="3"><input style="width:80px;" name="dniDoc" value="<?=($registro)?$registro['dniDoc']:''?>"  onkeyup="docentevalidar_dni(this.value);" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required></td>
												<td rowspan="2">
													<select style="width:80px;" name="graDoc"></select>
													<input style="width:80px;" name="secDoc" value="<?=($registro)?$registro['secDoc']:''?>" required>
												<td><?=($registro)? str_replace(array('1','2','3','4','5','6','7'),array('CETPRO','EBE','Secundaria','Primaria','Inicial','Eba Inicial Intermedio','Eba Avanzado'),$registro['idNivDoc']) :''?></td>
												<td rowspan="2"><input style="width:80px;" name="nroEstRec" value="<?=($registro)?$registro['nroEstRec']:''?>" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required></td>
												<td class="negritacentrado">PRESENCIAL</td>
												<td class="negritacentrado">ASINCRÓNICO</td>
											</tr>
											<tr>
												<td class="negritacentrado">Área 
													<select style="width:80px;" name="areDoc">
														<option>Desarrollo Personal, Ciudadania y Cívica</option>
														<option>Ciencias Sociales</option>
														<option>Educación Fisica</option>
														<option>Arte y Cultura</option>
														<option>Comunicación</option>
														<option>Ingles</option>
														<option>Matemática</option>
														<option>Ciencia y Tecnologia</option>
														<option>Personal Social</option>
														<option>Educación para el Trabajo</option>
														<option>Eucación Religiosa</option>
													</select>
												</td>
												<td><input width="100%" name="nroEstPreRec" value="<?=($registro)?$registro['nroEstPreRec']:''?>" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required></td>
												<td><input width="100%" name="nroEstAsiRec" value="<?=($registro)?$registro['nroEstAsiRec']:''?>" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required></td>
											</tr>
											<tr>
												<td>Tipo de Servicio</td>
												<td class="negritacentrado"><input type="radio" name="tipSerRec" value="P" <?=($registro)?(($registro['tipSerRec']=='P')?'checked':''):''?> required> P</td>
												<td class="negritacentrado"><input type="radio" name="tipSerRec" value="H" <?=($registro)?(($registro['tipSerRec']=='H')?'checked':''):''?>> H</td>
												<td class="negritacentrado"><input type="radio" name="tipSerRec" value="D" <?=($registro)?(($registro['tipSerRec']=='D')?'checked':''):''?>> D</td>
												<td>Teléfono</td>
												<td><input style="width:100px;" name="telDoc" value="<?=($registro)?$registro['telDoc']:''?>" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required></td>
												<td></td>
												<td>Correo electrónico</td>
												<td><input width="100%" name="corDoc" value="<?=($registro)?$registro['corDoc']:''?>"></td>
											</tr>
											<?php
											if($aldocente){
											?>
											<tr>
												<td>Número de visita a la IE</td>
												<td colspan="3"><input name="nroVisRec" value="<?=($registro)?$registro['nroVisRec']:''?>" style="width:40px;" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"></td>
												<td colspan="2">Fecha de aplicación</td>
												<td><input type="text" class="form-control flatfecha" name="fechaaplicacion" style="width:110px;" value="<?=($registro)?$registro['fechaaplicacion']:''?>" required/></td>
												<td>Hora de inicio/fin</td>
												<td>
												    <input type="text" class="form-control flathora" name="horainicioaplicacion" style="width:110px;" value="<?=($registro)?$registro['horainicioaplicacion']:''?>" required/>
												    <input type="text" class="form-control flathora" name="horaaplicacion" style="width:110px;" value="<?=($registro)?$registro['horaaplicacion']:''?>" required/>
												</td>
											</tr>
											<?php
											}
											?>
										</table>
										<?php
										if($editarficha){
										?><button class="btn btn-success guardarficha" style="float:right;" <?=($registro)?'':'disabled'?>>GRABAR</button><?php
										}
										?>
									</form>
								</div>
							</div>
						</div>									
						<?php
						}
						?>

						<?php
					    if($grupo){
						    for ($i=0; $i < count($grupo); $i++) {
						    $key = $grupo[$i];
						?>
						<div class="card">
							<div id="headingOne" class="card-header">
								<button type="button" data-toggle="collapse" data-target="#collapseOne<?=$i?>" aria-expanded="true" aria-controls="collapseOne" class="text-left m-0 p-0 btn btn-link btn-block">
									<h5 class="m-0 p-0"><?=$key['gruPre']?>
										<span id="icoform<?=$i?>"><?=($key['grupo_respondido'])?'<span style="color:green;" class="pe-7s-check"></span>':'<span style="color:red;" class="pe-7s-diskette"></span>'?></span>
									    <span style="padding:2px;float:right;" class="btn btn-danger">Despegar <span class="pe-7s-angle-down-circle"></span></span>
									</h5>
								</button>
							</div>
							<div data-parent="#accordion" id="collapseOne<?=$i?>" aria-labelledby="headingOne" class="collapse <?=($i==0 and !$ficha['DocMonFic'])?'show':''?>" style="">
								<div class="card-body" style="padding:0px;">
									<!--Cuerpo del formulario-->
									<form enctype="multipart/form-data" id="form<?=$i?>" method="post" style="width:100%;" onsubmit="guardar_respuesta('form<?=$i?>');return false;">
        								@csrf
										<input name="idficha"    style="display:none;" value="<?=($registro)?$registro['idFic']:''?>">
        								<input name="idreceptor" style="display:none;" value="<?=($registro)?$registro['idRec']:''?>">
        								<div class="table-responsive">
            								<table class="table" style="color:#000;">
            								    <?php
            								    //$fila->tipo=='SI/NO' and $fila->tipo=='TEXTO' and $fila->tipo=='OPCION MULTIPLE'
            								    $cabeza = false;
            								    for ($k=0; $k < count($key['detalle']); $k++) {
            								        $fila = $key['detalle'][$k];
            								        if($fila['tipPre']=='SI/NO'){
            								            $cabeza=1;
            								        }
													if($fila['tipPre']=='INICIO/PROCESO/LOGRADO'){
            								            $cabeza=2;
            								        }
            								        if($fila['tipPre']=='ARCHIVO'){
            								            $cabeza=3;
            								        }
            								        if($fila['tipPre']=='INICIO/LOGRADO'){
            								            $cabeza=4;
            								        }
            								        if($fila['tipPre']=='NOAPLICA/INICIO/PROCESO/LOGRADO'){
            								            $cabeza=5;
            								        }
                								    if($fila['tipPre']=='BUENO/REGULAR/MALO'){
            								            $cabeza=6;
            								        }
            								        
            								        
            								        if($fila['tipPre']=='0/1/2/3/4'){
            								            $cabeza=8;
            								        }
            								        if($fila['tipPre']=='1/2/3'){
            								            $cabeza=9;
            								        }
            								        /**INICIO JMMJ 11-10-2023*/
            								         if($fila['tipPre']=='SI/NO/NOAPLICA'){
            								            $cabeza=10;
            								            /*FIN JMMJ 11-10-2023*/
            								        }
            								    }
            								    ?>

												<?php
            								    if($cabeza==1){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">Descripción</td>
            								        <td colspan="2">Valoración</td>
            								        <td colspan="2">Evidencias</td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>SI</td>
            								        <td>NO</td>
            								        <td>Cargar Archivo</td>
            								        <td>Anexo</td>
            								    </tr>
            								    <?php
						                        }
            								    ?>

												<?php
            								    if($cabeza==2){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">Descripción</td>
            								        <td colspan="3">Valoración</td>
            								        <td colspan="3"></td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>EN INICIO(1)</td>
            								        <td>EN PROCESO(2)</td>
													<td>LOGRADO(3)</td>
            								        <td>Cargar Archivo</td>
            								        <td>Recursos a Observar</td>
													<td>Observaciones yo precisiones</td>
            								    </tr>
            								    <?php
						                        }
            								    ?>
            								    
            								    <?php
            								    if($cabeza==3){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">Descripción</td>
            								        <td colspan="2">Tiene</td>
            								        <td colspan="2"></td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>SI</td>
            								        <td>NO</td>
            								        <td>Cargar Archivo</td>
            								        <td></td>
            								    </tr>
            								    <?php
						                        }
            								    ?>
            								    
            								    <?php
            								    if($cabeza==4){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">Descripción</td>
            								        <td colspan="4">Valoración</td>
            								        <td colspan="3"></td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>SI</td>
													<td>NO</td>
            								        <td>Cargar Archivo</td>
            								        <td>Recursos a Observar</td>
													<td>Observaciones yo precisiones</td>
            								    </tr>
            								    <?php
						                        }
            								    ?>
            								    
            								    <?php
            								    if($cabeza==5){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">Descripción</td>
            								        <td colspan="4">Valoración</td>
            								        <td colspan="3"></td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>NO APLICA(0)</td>
            								        <td>EN INICIO(1)</td>
            								        <td>EN PROCESO(2)</td>
													<td>LOGRADO(3)</td>
            								        <td>Cargar Archivo</td>
            								        <td>Recursos a Observar</td>
													<td>Observaciones yo precisiones</td>
            								    </tr>
            								    <?php
						                        }
            								    ?>
            								    
            								    <?php
            								    if($cabeza==6){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">Descripción</td>
            								        <td colspan="3">Valoración</td>
            								        <td colspan="3"></td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>MALO</td>
            								        <td>REGULAR</td>
													<td>BUENO</td>
            								        <td>Cargar Archivo</td>
            								        <td>Recursos a Observar</td>
													<td>Observaciones yo precisiones</td>
            								    </tr>
            								    <?php
						                        }
            								    ?>
            								    
            								    <?php
            								    if($cabeza==8){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">ítems</td>
            								        <td colspan="5">Valoración</td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>0</td>
            								        <td>1</td>
													<td>2</td>
													<td>3</td>
													<td>4</td>
            								    </tr>
            								    <?php
						                        }
            								    ?>
            								    
            								    <?php
            								    if($cabeza==9){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">Indicador</td>
            								        <td colspan="3">Valoración</td>
            								        <td rowspan="2">Observaciones yo precisiones</td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>1</td>
            								        <td>2</td>
													<td>3</td>
            								    </tr>
            								    <?php
						                        }
            								    ?>
            								    
            								    <!--INIICO JMMJ 11-10-2023-->
            								    <?php
            								    if($cabeza==10){
            								    ?>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td rowspan="2">N°</td>
            								        <td rowspan="2">Descripción</td>
            								        <td colspan="3">Valoración</td>
            								        <td colspan="2">Evidencias</td>
            								    </tr>
            								    <tr style="font-weight: bold;text-align:center; background-color:rgb(226,239,218);">
            								        <td>SI</td>
            								        <td>NO</td>
            								        <td>NO APLICA</td>
            								        <td>Cargar Archivo</td>
            								        <td>Anexo</td>
            								    </tr>
            								    <?php
						                        }
            								    ?>
            								    <!--FIN JMMJ 11-10-2023-->
            								    <?php
            								    if($key['detalle']){
            								    for ($k=0; $k < count($key['detalle']); $k++) {
            								        $fila = $key['detalle'][$k];
            								    ?>
            								    <?php if($fila['tipPre']=='SI/NO/NOAPLICA' or $fila['tipPre']=='SI/NO' or $fila['tipPre']=='INICIO/PROCESO/LOGRADO' or $fila['tipPre']=='INICIO/LOGRADO' or $fila['tipPre']=='NOAPLICA/INICIO/PROCESO/LOGRADO' or $fila['tipPre']=='BUENO/REGULAR/MALO' or $fila['tipPre']=='ARCHIVO' or $fila['tipPre']=='0/1/2/3/4' or $fila['tipPre']=='1/2/3'){
            								    $required = ($fila['camOblPre']=='1')?'required':'';
            								    ?>
												<?php
												if($fila['altPre']){ 
												?>
												<tr>
													<td colspan="6" style="font-weight:bolder;background-color:rgb(218,238,243);"><?=$fila['altPre']?></td>
												</tr>
												<?php
												}
												?>
            								    <tr>
            								        <td style="font-weight:bolder;text-align: center;"><?=($fila['nroPre']=='-')?'':$fila['nroPre'].'.'?></td>
            								        <td style="font-weight: bold;color:#000;"><?=$fila['textPre']?></td>
													<?php if($fila['tipPre']=='SI/NO' or $fila['tipPre']=='ARCHIVO'){	?>
            								        <td style="text-align:center;"><input type="radio" value="SI" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='SI')?'checked':''?> <?=$required?>></td>
            								        <td style="text-align:center;"><input type="radio" value="NO" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='NO')?'checked':''?> <?=$required?>></td>
            								        <?php }elseif($fila['tipPre']=='INICIO/LOGRADO' or $fila['tipPre']=='ARCHIVO'){ ?>
            								        <td style="text-align:center;"><input type="radio" value="3" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='3')?'checked':''?> <?=$required?>></td>
            								        <td style="text-align:center;"><input type="radio" value="1" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='1')?'checked':''?> <?=$required?>></td>
            								        <?php }elseif($fila['tipPre']=='NOAPLICA/INICIO/PROCESO/LOGRADO' or $fila['tipPre']=='ARCHIVO'){ ?>
            								        <td style="text-align:center;"><input type="radio" value="0" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='0')?'checked':''?> <?=$required?>></td>
													<td style="text-align:center;"><input type="radio" value="1" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='1')?'checked':''?> <?=$required?>></td>
            								        <td style="text-align:center;"><input type="radio" value="2" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='2')?'checked':''?> <?=$required?>></td>
													<td style="text-align:center;"><input type="radio" value="3" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='3')?'checked':''?> <?=$required?>></td>
            								        <?php }elseif($fila['tipPre']=='0/1/2/3/4' or $fila['tipPre']=='ARCHIVO'){ ?>
            								        <td style="text-align:center;"><input type="radio" value="0" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='0')?'checked':''?> <?=$required?>></td>
													<td style="text-align:center;"><input type="radio" value="1" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='1')?'checked':''?> <?=$required?>></td>
            								        <td style="text-align:center;"><input type="radio" value="2" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='2')?'checked':''?> <?=$required?>></td>
													<td style="text-align:center;"><input type="radio" value="3" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='3')?'checked':''?> <?=$required?>></td>
													<td style="text-align:center;"><input type="radio" value="4" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='4')?'checked':''?> <?=$required?>></td>
            								        
            								        <?php }elseif($fila['tipPre']=='SI/NO/NOAPLICA' or $fila['tipPre']=='ARCHIVO'){	?>
            								        <td style="text-align:center;"><input type="radio" value="SI" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='SI')?'checked':''?> <?=$required?>></td>
            								        <td style="text-align:center;"><input type="radio" value="NO" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='NO')?'checked':''?> <?=$required?>></td>
            								        <td style="text-align:center;"><input type="radio" value="NOAPLICA" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='NOAPLICA')?'checked':''?> <?=$required?>></td>
            								        <?php }
            								        
            								        else{?>
													<td style="text-align:center;"><input type="radio" value="1" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='1')?'checked':''?> <?=$required?> ondblclick="$(this).prop('checked',false);"></td>
            								        <td style="text-align:center;"><input type="radio" value="2" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='2')?'checked':''?> <?=$required?> ondblclick="$(this).prop('checked',false);"></td>
													<td style="text-align:center;"><input type="radio" value="3" onclick="validar_anexo(<?=$fila['idPre']?>,this.value);" name="p<?=$fila['idPre']?>" <?=($fila['resRdd']=='3')?'checked':''?> <?=$required?> ondblclick="$(this).prop('checked',false);"></td>
													<?php }		?>
													
													<?php if($fila['tipPre']=='0/1/2/3/4'){ ?>
													
													<?php }elseif($fila['tipPre']=='1/2/3'){ ?>
													<td style="text-align:center;"><textarea class="form-control"  name="obs<?=$fila['idPre']?>"><?=$fila['obsRdd']?></textarea></td>
													
													<?php }else{ ?>
            								        <td style="text-align:center;">
            								            <?php
            								            if($fila['adjArcPre']==1){
            								                if($fila['arcRdd'] and $fila['resRdd']!='NO'){
            								                ?>
            								                    <table ><tr>
            								                    <td><a    class="divcargado<?=$fila['idPre']?> btn btn-success" target="_blank" href=".<?=$fila['arcRdd']?>"><span style="font-size:14px;">O</span></a></td>
            								                    <td>&nbsp;</td>
																
            								                    <td><?php if($editarficha){ ?><span class="divcargado<?=$fila['idPre']?> btn btn-danger"                style="font-size:14px;" onclick="habilitar_subida(<?=$fila['idPre']?>);">X</span><?php } ?></td>
            								                    </tr></table>
            								                    <input type="file" class="form-control" name="anexo<?=$fila['idPre']?>" <?=($fila['resRdd']=='NO' or $fila['resRdd']=='0')?'disabled':''?> onchange="verificararchivo(<?=$fila['idPre']?>);" style="display:none;">
            								                    <div id="msj<?=$fila['idPre']?>"></div>
            								                <?php
            								                }else{
            								                ?>
            								                    <input type="file" class="form-control" name="anexo<?=$fila['idPre']?>" <?=($fila['resRdd']=='NO' or $fila['resRdd']=='0')?'disabled':''?> onchange="verificararchivo(<?=$fila['idPre']?>);">
            								                    <div id="msj<?=$fila['idPre']?>"></div>
            								                <?php
            								                }
            								            }else{
            								            echo 'Declarativo';
            								            }
            								            ?>
            								        </td>            								    
            								        <td style="text-align:center;"><?=$fila['obsPre']?></td>
													<?php if($fila['tipPre']=='INICIO/PROCESO/LOGRADO' or $fila['tipPre']=='INICIO/LOGRADO' or $fila['tipPre']=='NOAPLICA/INICIO/PROCESO/LOGRADO' or $fila['tipPre']=='BUENO/REGULAR/MALO' or $fila['tipPre']=='0/1/2/3/4' ){	?>
													<td style="text-align:center;"><textarea class="form-control"  name="obs<?=$fila['idPre']?>"><?=$fila['obsRdd']?></textarea></td>
													<?php }	?>
													
													<?php } ?>
            								    </tr>
            								    <?php if($fila['htmlPre']){ ?>
            								    <tr class="anexo<?=$fila['idPre']?>" style="<?=($fila['resRdd']=='SI'?'':'display:none;')?>" >
            								        <td></td>
            								        <td colspan="5">
            								            <?php
                    								    if($fila['adicional']){
                    								    foreach ($fila['adicional'] as $key1) {
															//Verificar o comentado
                    								        $fila['htmlPre'] = str_replace('name="'.$key1['varVaa'].'"','name="'.$key1['varVaa'].'" value="'.$key1['valRaa'].'"',$fila['htmlPre']);
                    								    }
                    								    }
                    								    echo $fila['htmlPre'];
                    								    ?>
            								        </td>
            								    </tr>
            								    <?php } ?>
            								    <?php } ?>
            								    
            								    
            								    <?php if($fila['tipPre']=='TEXTO'){   ?>
            								    <tr>
            								        <td></td>
            								        <td colspan="5"><textarea class="form-control" name="p<?=$fila['idPre']?>" placeholder="Escriba su respuesta en este campo de texto" style="border-bottom: 1px solid darkgreen;resize: vertical;" required><?=$fila['resRdd']?></textarea></td>
            								    </tr>
            								    <?php } ?>
            								    
            								    <?php if($fila['tipPre']=='TEXTO CORTO'){   ?>
            								    <tr>
            								        <td></td>
            								        <td colspan="5"><b><?=$fila['textPre']?></b> (<span id="s<?=$fila['idPre']?>">0</span> de 555 caracteres)<br><?=$fila['obsPre']?><br><input class="form-control spanmaxtext" name="p<?=$fila['idPre']?>" style="border-bottom: 1px solid darkgreen;" value="<?=$fila['resRdd']?>" onkeyup="maxtext(<?=$fila['idPre']?>);" autocomplete="off" required></td>
            								    </tr>
            								    <?php } ?>
            								    
            								    <?php if($fila['tipPre']=='NUMERO CORTO'){   ?>
            								    <?php /*if($registro['textModalidadRec']==$fila['altPre'] or $fila['altPre']==''){  */ ?>
            								    <tr>
            								        <td></td>
            								        <td colspan="5"><b><?=$fila['textPre']?></b><br><?=$fila['obsPre']?><br><input class="form-control spanmaxtext" name="p<?=$fila['idPre']?>" style="border-bottom: 1px solid darkgreen;" value="<?=$fila['resRdd']?>" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" autocomplete="off" required></td>
            								    </tr>
            								    <?php /*}*/ ?>
            								    <?php } ?>
            								    
            								    <?php if($fila['tipPre']=='TABLA'){  ?>
            								    <?php
												if($registro){
												$fila['htmlPre'] = str_replace('|codlocal|',$registro['codlocRec'],$fila['htmlPre']);
												$fila['htmlPre'] = str_replace('|nombreie|',$registro['insRec'],$fila['htmlPre']);
												$fila['htmlPre'] = str_replace('|fechaficha|',$registro['fechaficha'],$fila['htmlPre']);
												$fila['htmlPre'] = str_replace('|grado|',$registro['graDoc'],$fila['htmlPre']);
												$fila['htmlPre'] = str_replace('|seccion|',$registro['secDoc'],$fila['htmlPre']);
												$fila['htmlPre'] = str_replace('|ciclo|',$registro['cicDoc'],$fila['htmlPre']);
												}
            								    if($fila['adicional']){
            								    foreach ($fila['adicional'] as $key1) {
													//Verificar o comentado
            								        $fila['htmlPre'] = str_replace('name="'.$key1['varVaa'].'"','name="'.$key1['varVaa'].'" value="'.$key1['valRaa'].'"',$fila['htmlPre']);
            								    }
            								    }
            								    ?>
            								    <tr>
            								        <td></td>
            								        <td colspan="5"><input type="text" name="p<?=$fila['idPre']?>" value="-" style="display:none;"><?=$fila['htmlPre']?></td>
            								    </tr>
            								    <?php } ?>
            								    
            								    <?php if($fila['tipPre']=='OPCION MULTIPLE'){ ?>
            								    <tr>
            								        <td></td>
            								        <td colspan="5">
            								            <b><?=$fila['textPre']?></b>
            								            <?php
            								            $r_alternativas = explode(',',$fila['altPre']);
            								            if($fila['altPre']){
            								                for ($j=0; $j < count($r_alternativas); $j++){
            								                $alt = $r_alternativas[$j];
															echo (strlen($fila['altPre'])>100)?'<br>':'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            								                echo '<input type="checkbox" name="p'.$fila['idPre'].'[]" '.(($j==0)?'':'').' value="'.$alt.'" '.((strpos($fila['resRdd'],$alt)>-1)?'checked':'').'> '.$alt;
            								                }
            								            }
            								            ?>
            								        </td>
            								    </tr>
            								    <?php } ?>
            								    
            								    <?php if($fila['tipPre']=='OPCION UNICA'){ ?>
            								    <tr>
            								        <td></td>
            								        <td colspan="5">
            								            <b><?=$fila['textPre']?></b>
            								            <?php
            								            $r_alternativas = explode(',',$fila['altPre']);
            								            if($fila['altPre']){
            								                for ($j=0; $j < count($r_alternativas); $j++){
            								                $alt = $r_alternativas[$j];
            								                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="p'.$fila['idPre'].'" '.(($j==0)?'required':'').' value="'.$alt.'" '.(($alt==$fila['resRdd'])?'checked':'').'> '.$alt;
            								                }
            								            }
            								            ?>
            								        </td>
            								    </tr>
            								    <?php } ?>
            								    
            								    <?php
            								    }
						                        }
            								    ?>
            								    
            								</table>
        								  </div>
        								<div>	
        								    <?php
											if($editarficha){
                    					    ?><button class="btn btn-success guardarficha" style="float:right;" <?=($registro)?'':'disabled'?>>GRABAR</button><?php
						                    }
        								    ?>
        								</div>
        								
        								<p></p>
        								
        							  </form>
									<!--Cuerpo del formulario-->
								</div>
							</div>
						</div>
						<?php
							}
						}
						?>

						<?php
						if(false){//$aldirectivo or $aldocente
						?>
						<div class="card">
							<div id="headingOne" class="card-header">
								<button type="button" data-toggle="collapse" data-target="#collapseOneCRC" aria-expanded="true" aria-controls="collapseOne" class="text-left m-0 p-0 btn btn-link btn-block">
									<h5 class="m-0 p-0">CONCLUSIONES, RECOMENDACIONES Y COMPROMISOS
										<span id="icoformCRC"><?=(($registro)?(($registro['conRec'])?true:false):false)?'<span style="color:green;" class="pe-7s-check"></span>':'<span style="color:red;" class="pe-7s-diskette"></span>'?></span>
									    <span style="padding:2px;float:right;" class="btn btn-danger">Despegar <span class="pe-7s-angle-down-circle"></span></span>
									</h5>
								</button>
							</div>
							<div data-parent="#accordion" id="collapseOneCRC" aria-labelledby="headingOne" class="collapse">
								<div class="card-body table-responsive table-responsive" style="padding:0px;">
									<!--Cuerpo del formulario-->
									<form enctype="multipart/form-data" id="formCRC" method="post" style="width:100%;" onsubmit="guardar_receptores('formCRC');return false;">
										@csrf
										<input type="hidden" name="idRec" value="<?=($registro)?$registro['idRec']:''?>">
										<b>PRINCIPALES LOGROS:</b>
										<textarea class="form-control" name="conRec"><?=($registro)?$registro['conRec']:''?></textarea>
										<br>
										<b>ACCIONES DE MEJORA:</b>
										<textarea class="form-control" name="recRec"><?=($registro)?$registro['recRec']:''?></textarea>
										<br>
										<!--<b>COMPROMISOS:</b>-->

										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><br></div>
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<b>COMPROMISOS DEL DIRECTIVO</b>
												<textarea class="form-control" name="comDirRec"><?=($registro)?$registro['comDirRec']:''?></textarea>
											</div>
											<!--
											<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
												<b>¿CÓMO SE IMPLEMENTA EL COMPROMISO?</b>
												<textarea class="form-control" name="impDirRec"><?=($registro)?$registro['impDirRec']:''?></textarea>
											</div>
											-->
										</div>

										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><br></div>
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<b>COMPROMISOS DEL DOCENTE</b>
												<textarea class="form-control" name="comDocRec"><?=($registro)?$registro['comDocRec']:''?></textarea>
											</div>
											<!--
											<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
												<b>¿CÓMO SE IMPLEMENTA EL COMPROMISO?</b>
												<textarea class="form-control" name="impDocRec"><?=($registro)?$registro['impDocRec']:''?></textarea>
											</div>
											-->
										</div>
										<?php if($aldirectivo){ ?>
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><br></div>
											<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
												<b>COMPROMISOS DEL ESPECIALISTA DE UGEL</b>
												<textarea class="form-control" name="comEspRec"><?=($registro)?$registro['comEspRec']:''?></textarea>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
												<b>¿CÓMO SE IMPLEMENTA EL COMPROMISO?</b>
												<textarea class="form-control" name="impEspRec"><?=($registro)?$registro['impEspRec']:''?></textarea>
											</div>
										</div>
										<?php } ?>
										<?php
										if($editarficha){
										?><button class="btn btn-success guardarficha" style="float:right;" <?=($registro)?'':'disabled'?>>GRABAR</button><?php
										}
										?>
									</form>
								</div>
							</div>
						</div>
						<?php
						}
						?>
					</div>
				</div>				
			</div>
			<!--ACORDIAON-->
                </div>
              </div>
            </div>       	
          </div>
          <div class="modal-footer">
              <?php if($ficha['genPdfFic']){ ?><span class="btn btn-info" onclick="enviar_ficha_ugel01()">Generar ficha en PDF</span><?php } ?>
            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>         
          </div>
          
          <script type="text/javascript">
          
          function maxtext(id){
              $("#s"+id).html($("input[name=p"+id+"]").val().length);
          }
          
          function valorestextarea(){
            var elemento = $("#Modalficha textarea");
              for (var i = 0; i < elemento.length; i++) {
                  if($(elemento[i]).attr('value')){
                      $(elemento[i]).val($(elemento[i]).attr('value'));
                  }
              }
          }
          <?php if($fila['tipPre']=='TABLA'){ echo 'valorestextarea();'; } ?>
          </script>
          
          <script type="text/javascript">
			var DocMonFic = <?=$ficha['DocMonFic']?>;
		  $("select[name=areDoc]").val("<?=($registro)?$registro['areDoc']:''?>");
          
		  function javasuma(idres,elementos,comparar=false,msj=''){
				var ele = elementos.split(',');
				var total = 0;
				if(ele){
					for (var i = 0; i < ele.length; i++) {
						var inp = $("#Modalficha input[name="+ele[i]+"]").val();
						total = total + parseFloat((inp)?inp:'0');
					}
				}
				$("#Modalficha input[name="+idres+"]").val(total);
				
				//------------------------------
				//Comparar
				if(comparar){
				    var ele = comparar.split(',');
    				var comptotal = 0;
    				if(ele){
    					for (var i = 0; i < ele.length; i++) {
    						var inp = $("#Modalficha input[name="+ele[i]+"]").val();
    						comptotal = comptotal + parseFloat((inp)?inp:'0');
    					}
    				}
    				
    				if(total==comptotal){
    				    $("#Modalficha input[name="+idres+"]").parent().children('.msj').html('');
    				}else{
    				    $("#Modalficha input[name="+idres+"]").parent().children('.msj').html(msj.replace('|comptotal|',comptotal));
    				}
    				
    				
				}
				
				//------------------------------
			}

          function verificararchivo(nro,tipo_archivo='PDF,JPG,PNG'){
              if($("input[name=anexo"+nro+"]").val()){
                  var sizeByte = $("input[name=anexo"+nro+"]")[0].files[0].size;
                  if(sizeByte < 30*(1024*1024)){
                        var texto = $("input[name=anexo"+nro+"]").val();
                        var extencion = texto.split('.')[texto.split('.').length-1].toUpperCase();
                        if( tipo_archivo.split(',').indexOf(extencion)>-1 ){
                            $("#msj"+nro).html('<span style="color:green;font-weight: bolder;">Archivo correcto</span>');
                        }else{
                            $("input[name=anexo"+nro+"]").val('');
                            alert('Solo se permite adjuntar archivos '+tipo_archivo);
                            $("#msj"+nro).html('<span style="color:red;font-weight: bolder;">'+'Solo se permite adjuntar archivos '+tipo_archivo+'</span>');
                        }
                  }else{
                      $("input[name=anexo"+nro+"]").val('');
                      alert('El archivo no debe pesar más de 30MB.');
                      $("#msj"+nro).html('<span style="color:red;font-weight: bolder;">El archivo no debe pesar más de 30MB.</span>');
                  }
              }
          }
          
          <?php
		  	
		    if($registro){
		    if(!$editarficha){
		    ?> $('#Modalficha input').prop('disabled',true); $('#Modalficha .form-control').prop('disabled',true); <?php
		    }
            }
			
		    ?>
          
          
          function habilitar_subida(id){
              if(confirm('¿Esta seguro que desea cambiar el archivo?')){
              $(".divcargado"+id).css('display','none');
              $("input[name=anexo"+id+"]").css('display','');
              }
          }
          
          function validar_anexo(id,value){
                  if(value=='NO' || value=='0'|| value=='1'){
                  $("input[name=anexo"+id+"]").prop('disabled',true);
                  $(".anexo"+id).css('display','none');
                  $(".divcargado"+id).css('display','none');
              }else{
                  $("input[name=anexo"+id+"]").prop('disabled',false);
                  $(".anexo"+id).css('display','');
                  $(".divcargado"+id).css('display','');
              }
          }
          
          function guardar_respuesta(id){
                
                $("#"+id+" .guardarficha").prop('disabled',true);
                $("#"+id+" .guardarficha").html('<img src="./assets/images/2.gif" style="width:30px;"> GRABAR');
                              
                var ifimagen = true;
                      //información del formulario
                      var formData = new FormData($("#"+id)[0]);
                      var message = "";
                      //hacemos la petición ajax  
                      $.ajax({
                          url: '{{route('guardar_respuesta')}}',  
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
                              //toastr.info('Datos Guardados');
							  alert('Datos Guardados');
                              $("#"+id+" .guardarficha").prop('disabled',false);
                              $("#"+id+" .guardarficha").html('GRABAR');
                              $("#ico"+id).html('<span style="color:green;" class="pe-7s-check"></span>');
                              if(data['ficha_respondida']['grupo_respondido']==1 && !DocMonFic){ 
								  $("#fc_ficha").click();
                                  alert('Ha completado la ficha. Felicitaciones!!!');
								  ver_ficha_ie();
                              }
                              
                          },
                          error: function(){
                            alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
							$("#"+id+" .guardarficha").prop('disabled',false);
                            $("#"+id+" .guardarficha").html('GRABAR');
                          }
                      });

          }

		  function guardar_docente(id){
                $("#"+id+" .guardarficha").prop('disabled',true);
                $("#"+id+" .guardarficha").html('<img src="./assets/images/2.gif" style="width:30px;"> GRABAR');                              
                var ifimagen = true;
                      //información del formulario
                      var formData = new FormData($("#"+id)[0]);
                      var message = "";
                      //hacemos la petición ajax  
                      $.ajax({
                          url: '{{route('guardar_docente')}}',  
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
							alert('Datos Guardados');
							$("#"+id+" .guardarficha").prop('disabled',false);
                            $("#"+id+" .guardarficha").html('GRABAR');
							$("#ico"+id).html('<span style="color:green;" class="pe-7s-check"></span>');
                          },
                          error: function(){
                            alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
							$("#"+id+" .guardarficha").prop('disabled',false);
                            $("#"+id+" .guardarficha").html('GRABAR');
                          }
                      });
          }

		  function guardar_receptores(id){
                $("#"+id+" .guardarficha").prop('disabled',true);
                $("#"+id+" .guardarficha").html('<img src="./assets/images/2.gif" style="width:30px;"> GRABAR');                              
                var ifimagen = true;
                      //información del formulario
                      var formData = new FormData($("#"+id)[0]);
                      var message = "";
                      //hacemos la petición ajax  
                      $.ajax({
                          url: '{{route('guardar_receptores')}}',  
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
							alert('Datos Guardados');
							$("#"+id+" .guardarficha").prop('disabled',false);
                            $("#"+id+" .guardarficha").html('GRABAR');
							$("#ico"+id).html('<span style="color:green;" class="pe-7s-check"></span>');
                          },
                          error: function(){
                            alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
							$("#"+id+" .guardarficha").prop('disabled',false);
                            $("#"+id+" .guardarficha").html('GRABAR');
                          }
                      });
          }
          
        function enviar_ficha_ugel01(){
            
            if( $("input[name=idreceptor]").val() ){
                if(confirm('¿Desea generar la ficha en PDF')){
                    ajax_data = {
                      "idreceptor" : $("input[name=idreceptor]").val(),
                      "alt"   : Math.random()
                    }
                        $.ajax({
                            type: "GET",
                            url: "{{route('enviar_ficha_ugel01')}}",
                            data: ajax_data,
                            dataType: "html",
                            beforeSend: function(){
                                  $("#btn_guardar").prop('disabled',true);
                            },
                            error: function(){
                                  alert("error peticiÃ³n ajax");
                            },
                            success: function(data){
                                    ver_ficha_ie();
                                    $("#fc_ficha").click();
                              }
                        });
                }
            }else{
                alert('Elija una ficha');
            }
        }
          
		function marcarx(athis,clase=false){
			if(clase) $("."+clase).val('');
			if($(athis).val()=='X'){
				$(athis).val('');
			}else{
				$(athis).val('X')
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
								if(data['idnivel']==2){ $("select[name=graDoc]").html('<option value=""></option><option>1EBE</option><option>2EBE</option><option>3EBE</option><option>4EBE</option><option>5EBE</option><option>6EBE</option>') }
								if(data['idnivel']==3){ $("select[name=graDoc]").html('<option value=""></option><option>1SEC</option><option>2SEC</option><option>3SEC</option><option>4SEC</option><option>5SEC</option>') }
								if(data['idnivel']==4){ $("select[name=graDoc]").html('<option value=""></option><option>1PRI</option><option>2PRI</option><option>3PRI</option><option>4PRI</option><option>5PRI</option><option>6PRI</option>') }
								if(data['idnivel']==5){ $("select[name=graDoc]").html('<option value=""></option><option>2INI</option><option>3INI</option><option>4INI</option><option>5INI</option>');}
								$("#btn_ficha").prop('disabled',false);                        
								}
							}
					});
			}
		}

		function directorvalidar_dni(dni){
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
								$("input[name=nomRec")   .val(data['nombres']);
								$("input[name=apePatRec").val(data['apellipat']);
								$("input[name=apeMatRec").val(data['apellimat']);
								$("input[name=telRec")   .val(data['celular']);
								$("input[name=corRec")   .val(data['correo']);
								$("input[name=carRec")   .val(data['descargo']);
								$("#btn_ficha").prop('disabled',false);                        
								}
							}
					});
			}
		}

		function selectnivel(){
			var idnivel = <?=($registro)?(($registro['idNivDoc'])?$registro['idNivDoc']:0):0?>;
			if(idnivel==2){ $("select[name=graDoc]").html('<option value=""></option><option>1EBE</option><option>2EBE</option><option>3EBE</option><option>4EBE</option><option>5EBE</option><option>6EBE</option>') }
			if(idnivel==3){ $("select[name=graDoc]").html('<option value=""></option><option>1SEC</option><option>2SEC</option><option>3SEC</option><option>4SEC</option><option>5SEC</option>') }
			if(idnivel==4){ $("select[name=graDoc]").html('<option value=""></option><option>1PRI</option><option>2PRI</option><option>3PRI</option><option>4PRI</option><option>5PRI</option><option>6PRI</option>') }
			if(idnivel==5){ $("select[name=graDoc]").html('<option value=""></option><option>2INI</option><option>3INI</option><option>4INI</option><option>5INI</option>');}
		}

		selectnivel();
		$("select[name=graDoc]").val("<?=($registro)?$registro['graDoc']:''?>");
		
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

		flatpickr('.flathora', {
			enableTime: true,
			noCalendar: true,
			dateFormat: "H:i",
			time_24hr: true
		});

		
          </script>
          
        <script type="text/javascript">
          $(".ejecutar").keyup();
          $(".spanmaxtext").keyup();
        </script>
          
          