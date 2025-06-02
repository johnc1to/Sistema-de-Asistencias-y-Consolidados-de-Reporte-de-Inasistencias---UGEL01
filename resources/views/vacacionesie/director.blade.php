@extends('layout_director/cuerpo')
@section('html')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>FORMULARIO DE REGISTRO DE VACACIONES DE LOS DIRECTORES</b></h5>
        <div class="position-relative form-group">
                            <input type="hidden" id="codplazadirector" value="<?php echo $vari['codplaza']; ?>">
							<input type="hidden" id="dnidirector" value="<?php echo $vari['dni'];?>">
							<input type="hidden" id="codigolocal" value="<?php echo $vari['conf_permisos'][0]['codlocal']; ?>">							
							<input type="text" class="form-control" id="titulo" placeholder="AGREGAR EVENTO..." value="VACACIONES - <?php echo $vari['apellipat']." ".$vari['apellimat']." ".$vari['nombres']; ?>" /><br>
							Cargo:
							<input type="text" class="form-control"  placeholder="" value="<?php echo $vari['cargo']; ?>" /><br>
							Fecha Inicio :
							<input type="date" class="form-control flatfecha" id="fech-in" placeholder="YYYY-MM-DD" style="background: white;" /><br>
							Fecha Final:
							<input type="date" class="form-control flatfecha" id="fech-fin" placeholder="YYYY-MM-DD" style="background: white;" /><br>
							Total Dias: <input type="text" class="form-control" id="dias"/><br>
							Persona Que estara a cargo de la Direcci&oacute;n: 
							<select class="form-control " id="personals2">
								<option></option>
								<?php 
									for($a=0; $a < count($codemode); $a++)
									{
									for ($i=0; $i <count($personals2[$a]) ; $i++) 
									{ 
								?>
								<option value="<?php echo  $personals2[$a][$i]['codplaza'];?>" dni="<?php echo  $personals2[$a][$i]['numdocum'];?>"  ids="<?php echo  $personals2[$a][$i]['descargo'];?>"><?php echo $personals2[$a][$i]["apellipat"]." ".$personals2[$a][$i]["apellimat"]." ".$personals2[$a][$i]["nombres"] ?></option>
								<?php
									}
								}
								?>
							</select>
							Cargo de la persona de reemplazo: <input type="text" id="cargoremplazo" class="form-control" id="cargo" placeholder="Cargo de Reemplazo" /><br>
							<button class="btn btn-primary" id="add">Grabar</button>
						<br />
						<ul class="list-unstyled calendar-list" id="events-list">
							<li class="list-header">Fechas de Vacaciones del Director</li>
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>#</th>
										<th>Fecha Inicio</th>
										<th>Fecha Final</th>
										<th>Dias</th>
										<th>Reemplazo</th>
										<th></th>
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
										<td><?php echo $vacacion[$i]["t_fecha_inicio"] ?></td>
										<td><?php echo $vacacion[$i]["t_fecha_final"] ?></td>
										<td><?php echo $vacacion[$i]["total_dias"] ?></td>
										<td><?php echo $vacacion[$i]["nombres"] ?></td>
										<td><button class="btn btn-danger deletedirector" id="<?php echo $vacacion[$i]['id_vacaciones'] ?>"><i class="">X</i></button></td>
									</tr>

									<?php
										}
										}
									?>
								</tbody>
							</table>
						</ul>
            
        </div>
    </div>
</div>

<script>
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


    restaFechas = function(f1,f2){
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
    $("#cargoremplazo").val(remplazo);
    });

    $("#fech-fin").change(function(){
					if($(this).val().length == 10)
					{
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


				$(".deletedirector").click(function(){
					var df = confirm("Esta seguro que desea eliminar esta Vacacion");
					if(df)
					{
						var parametros = {
								"codigo":$(this).attr("id")
							};

		      				$.ajax({
		      					url: "{{route('eliminarVacaciondirector')}}",
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

                $("#add").click(function(){

                var ini = $("#fech-in").val();
                var fini = $("#fech-fin").val();


                if($("#personals2").val() === "" || ini === "" || fini === "")
                {
                    alert("Llene todos los campos");
                }
                else
                {

                isranges = false;               

                    <?php 
                        $diasreg=0;
                        for ($i=0; $i <count($vacacion) ; $i++){ 
                        $diasreg = $diasreg + $vacacion[$i]["total_dias"];
                        }
                    ?>					
                diasreg = <?=$diasreg?>;


                diasreg += restaFechas(ini, fini);

                if(!(isranges))	
                {

                //diasreg = 0;
                //diasreg <= 30
                if(true)
                {

                    if(Date.parse(ini) > Date.parse(fini))
                    {
                        alert("La fecha inicial no puede ser mayor a la fecha final");
                    }
                    else
                    {
                        
                        mydate = new Date(ini + ' 23:59:59');
                        mydatef = new Date(fini + ' 23:59:59');

                        //alert(mydatef);
                        if("<?php echo $vari['cargo']?>" == "PROFESOR (FUNCIONES DE DIRECTOR)")
                        {
                            if(true){
                                //(mydate.getMonth() + 1) <= 2 && (mydatef.getMonth() +1 ) <= 2
                                var parametros = {
                                "codigoplaza":$("#codplazadirector").val(),
                                "dni": $("#dnidirector").val(),
                                "fecha_in": ini,
                                "feha_fin": fini,
                                "anio": ini.split('-')[0],
                                "total_dias": $("#dias").val(),
                                "remplazo": $("#personals2").val(),
                                "dniReemplazo": $("#personals2 option:selected").attr("dni"),
                                "local":$("#codigolocal").val(),
                                "descripcion":$("#titulo").val()
                                };

                                $.ajax({
                                    url: "{{route('guardarVacacionesdir')}}",
                                    type: "GET",
                                    data: parametros,
                                    dataType: "json",
                                    success: function(data){
                                        alert(data[0].message);
                                        location.reload(true);

                                    }
                                });
                            }else{
                                alert("Las vacaciones del director solo puede ser en el Mes de Enero y Febrero")
                            }
                        }
                        else
                        {
                            if(true)
                            {
                                var parametros = {
                                "codigoplaza":$("#codplazadirector").val(),
                                "dni": $("#dnidirector").val(),
                                "fecha_in": ini,
                                "feha_fin": fini,
                                "anio": ini.split('-')[0],
                                "total_dias": $("#dias").val(),
                                "remplazo": $("#personals2").val(),
                                "dniReemplazo": $("#personals2 option:selected").attr("dni"),
                                "local":$("#codigolocal").val(),
                                "descripcion":$("#titulo").val()						
                                };

                                $.ajax({
                                    url: "{{route('guardarVacacionesdir')}}",
                                    type: "GET",
                                    data: parametros,
                                    dataType: "json",
                                    success: function(data){
                                        alert(data[0].message);
                                        location.reload(true);

                                    }
                                });
                            }
                            else
                            {
                                alert("Las vacaciones del director solo puede ser entre los meses de Abril y Noviembre");
                            }
                        }				

                                            
                    
                    }
                }
                else
                {
                    alert("Ya paso los 30 dias");
                }
                }
                }
                });
</script>

@endsection


<!--
<div class="row">
    <b></b>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
</div>
-->