@extends('layout_director/cuerpo')
@section('html')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>FORMULARIO DE REGISTRO DE VACACIONES DEL PERSONAL ADMINISTRATIVO Y SUB DIRECTOR</b></h5>
        
        
            <div class="col-sm-12">
					
                <div class="calendar-sidebar">							
                        
                        <input type="hidden" id="anadido" value="0">
                        
                        <div id="completar1">
                        Personal Administrativo:
                        <select class="form-control " id="personals2">
                            <option></option>
                            <?php 
                                for($a=0; $a < count($codemode); $a++)
                                {
                                for ($i=0; $i <count($personals2[$a]) ; $i++) 
                                { 
                            ?>
                            <option value="<?php echo  $personals2[$a][$i]['codplaza'];?>" sit="<?php echo  $personals2[$a][$i]['situacion'];?>" dn="<?php echo  $personals2[$a][$i]['numdocum'];?>" ids="<?php echo  $personals2[$a][$i]['descargo'];?>" desley="<?php echo  $personals2[$a][$i]['desley'];?>"><?php echo $personals2[$a][$i]["apellipat"]." ".$personals2[$a][$i]["apellimat"]." ".$personals2[$a][$i]["nombres"] ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <a href="#" style="color:red;font-weight: bolder;" onclick="nuevopersonaladm();">Si no lo encuentra, haga click aqui para añadirlo</a>
                        </div>
                        
                        <div id="completar2" style="display:none;">
                        <b>Apellidos y Nombre:</b> <input type="text" class="form-control" id="nombresadm" placeholder="Apellidos y Nombre" /><br>
                        Codigo Plaza: <input type="text" class="form-control" id="codplaza" placeholder="Codigo Plaza" /><br>
                        DNI: <input type="text" class="form-control" id="dni" placeholder="DNI" /><br>
                        </div>
                        Cargo: <input type="text" id="cargoremplazo" class="form-control"  placeholder="Cargo" /><br>
                        Situaci&oacute;n Laboral: 
                        <!--<input type="text" class="form-control" id="situacion" placeholder="situaci&oacute;n" /><br>-->
                        <select class="form-control" id="situacion">
                            <option>CONTRATADO</option>
                            <option>DESIGNADO</option>
                            <option>ENCARGADO</option>
                            <option>NOMBRADO</option>
                            <option>CAS</option>
                        </select>
                        Ley: <select class="form-control" id="desley">
                            <option></option>
                            <option>D. LEG. Nº 1057</option>
                            <option>D.L. 276</option>
                            <option>D.LEG. N° 1153</option>
                            <option>LEY 29944</option>
                            <option>LEY 30328</option>
                            <option>LEY 30493</option>
                            <option>SIN REGIMEN</option>
                        </select><br>
                        Fecha Inicio:
                        <input type="date" class="form-control flatfecha" id="fech-in" placeholder="YYYY-MM-DD" style="background: white;" /><br>
                        Fecha Final:
                         <input type="date" class="form-control flatfecha" id="fech-fin" placeholder="YYYY-MM-DD"  style="background: white;"/><br>
                        Total Dias: <input type="text" class="form-control" id="dias" readonly/><br>							

                        <button class="btn btn-primary" id="add">Grabar</button>
                    
                    <br />
                        
                    <ul class="list-unstyled calendar-list" id="events-list">
                        
                    </ul>
                    
                </div>
                
            </div>
            
            <div class="col-sm-12">
                <h2 class="list-header">Fechas de Vacaciones del Personal Administrativo</h2>
                        <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombres</th>
                                    <th>Cargo</th>
                                    <th>Situaci&oacute;n</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Final</th>
                                    <th>Dias</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 

                                    if(!empty($vacacion))
                                    {
                                    for ($i=0; $i <count($vacacion) ; $i++) { 
                                ?>

                                <tr>
                                    <td><?php echo ($i + 1); ?></td>
                                    <td><?php echo $vacacion[$i]["descripcion"] ?></td>
                                    <td><?php echo $vacacion[$i]["descargo"] ?></td>
                                    <td><?php echo $vacacion[$i]["situacion"] ?></td>
                                    <td><?php echo $vacacion[$i]["t_fecha_inicio"] ?></td>
                                    <td><?php echo $vacacion[$i]["t_fecha_final"] ?></td>
                                    <td><?php echo $vacacion[$i]["total_dias"] ?></td>
                                    <td><button class="btn btn-info deleteadmin" id="<?php echo $vacacion[$i]['id_vacaciones'] ?>">Eliminar</button></td>
                                </tr>

                                <?php
                                    }
                                    }
                                ?>
                            </tbody>
                        </table>
                        </div>
            </div>

        
    </div>
</div>

<script>
    
    function nuevopersonaladm(){
            $("#nombresadm").val("");
            $("#codplaza").val("");
            $("#dni").val("");
            $("#cargoremplazo").val("");
            $("#desley").val("");
            $("#situacion").val("");
            $("#fech-in").val("");
            $("#fech-fin").val("");
            $("#dias").val("");
            
        if($("#completar2").css('display')=='none'){
            $("#completar1").css('display','none');
            $("#completar2").css('display','');
            $("#anadido").val(1);
            
        }else{
            $("#completar1").css('display','');
            $("#completar2").css('display','none');
            $("#anadido").val(2);
            
        }
    }


    $(".flatfecha").flatpickr('.flatfecha', {
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

        restaFechas = function(f1,f2)
						{
							 var aFecha1 = f1.split('-'); 
							 var aFecha2 = f2.split('-'); 
							 var fFecha1 = Date.UTC(aFecha1[0],aFecha1[1]-1,aFecha1[2]); 
							 var fFecha2 = Date.UTC(aFecha2[0],aFecha2[1]-1,aFecha2[2]); 
							 var dif = fFecha2 - fFecha1;
							 var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
							 return dias + 1;
						}

						$("#personals2").change(function(){

							var remplazo = $("#personals2 option:selected").attr("ids");
							$("#situacion").val($("#personals2 option:selected").attr("sit"));
							$("#dni").val($("#personals2 option:selected").attr("dn"));
							$("#cargoremplazo").val(remplazo);
							$("#desley").val($("#personals2 option:selected").attr("desley"));
							$("#codplaza").val($("#personals2").val());
							$("#nombresadm").val($("#personals2 option:selected").html());
							
							

						});

$("#fech-fin").change(function(){
        if($(this).val().length == 10){
            var ini = $("#fech-in").val();
            var fini = $("#fech-fin").val();
            //alert(Date.parse(ini));
            if(ini > fini)
            {
                alert("La fecha inicial no puede ser mayor a la fecha final");
                $(this).val("");
            }
            else
            {
                var dias = restaFechas(ini, fini);
                if(dias < 0)
                {
                    alert("La fecha inicial no puede ser mayor a la fecha final");
                }
                else
                {
                    $("#dias").val(restaFechas(ini, fini));
                }
            }
        }
        });

        $("#add").click(function()
				{	

						var ini = $("#fech-in").val();
						var fini = $("#fech-fin").val();

						if($("#codplaza").val() == "" || $("#dni").val()=="" || ini == "" || fini == "" || $("#dias").val()=="" ||  $("#nombresadm").val()=="" || $("#cargoremplazo").val()=="" || $("#situacion").val()=="" )
						{
							alert("Complete todos los campos");
						}			
						else
						{

						if(Date.parse(ini) > Date.parse(fini))
						{
							alert("La fecha inicial no puede ser mayor a la fecha final");
						}
						else
						{
							
							mydate = new Date(ini);
							mydatef = new Date(fini);						
                            
                            var parametros = {
									"codigoplaza":$("#codplaza").val(),
									"dni": $("#dni").val(),
									"fecha_in": ini,
									"feha_fin": fini,
									"anio": ini.split('-')[0],
									"total_dias": $("#dias").val(),
									"descripcion": "VACACIONES - " + $("#nombresadm").val(),
									"descargo"  : $("#cargoremplazo").val(),
									"desley"  : $("#desley").val(),
									"situacion" : $("#situacion").val(),
								};
								
			      				$.ajax({
			      					url: "{{route('guardarVacacionesAdmin')}}",
			      					type: "get",
			      					data: parametros,
                                    dataType: "json",
				                    success: function(data){
				                     	alert(data[0].message);
				                     	location.reload(true);
				                    }
		      					});
							
                            				
						
						}
					}
				});

				$(".deleteadmin").click(function(){
					var df = confirm("Esta seguro que desea eliminar esta Vacacion");
					if(df)
					{
						var parametros = {
								"codigo":$(this).attr("id")
							};

		      				$.ajax({
		      					url: "{{route('eliminarVacacionadmin')}}",
		      					type: "get",
		      					data: parametros,
                                dataType: "json",
			                    success: function(data){
			                     	alert(data[0].message);
			                     	location.reload(true);
			                    }
	      					});
					}
				});
</script>
@endsection