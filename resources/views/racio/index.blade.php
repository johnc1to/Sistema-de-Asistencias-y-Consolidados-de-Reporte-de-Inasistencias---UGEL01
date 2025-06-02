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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>Requerimiento de sección (PAP - I.E 2022)</b></h5>
        <div class="position-relative form-group">

           
            <div class="row" style="color:#000;">
                <a href="#" onclick="anexo_racio_reqseccincrementosecciones(this);" target="_blank" class="btn btn-success">DESCARGAR REPORTE EN EXCEL</a>&nbsp;&nbsp;&nbsp;
                <a href="#" target="_blank" id="btn_expedientes" class="btn btn-danger">DESCARGAR EXPEDIENTES</a>&nbsp;&nbsp;&nbsp;
            </div>

            <div class="row" style="color:#000;">
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12">
                    <input type="radio" name="box" value="1" onclick="verreqseccincrementoie();" checked> Por IE
                    &nbsp;&nbsp;&nbsp;                    
                    <input type="radio" name="box" value="2" onclick="verreqseccincrementosecciones();"> Por Secciones
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <select id="txt_nivel" onchange="if($('input[name=box]:checked').val()==1){verreqseccincrementoie();}else{verreqseccincrementosecciones();}">
                        <option value="">Todos</option>
                        <option value="1">CETPRO</option>
                        <option value="2">EBE</option>
                        <option value="3">EBR Secundaria</option>
                        <option value="4">EBR Primaria</option>
                        <option value="5">EBR Inicial</option>
                        <option value="6">EBA Inicial Intermedio</option>
                        <option value="7">EBA Avanzado</option>
                        </select>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>Niveles Reportados: </b> <b id="msj1">30</b>
                </div>
            </div>

            <div class="row">      
                <div id="divporie" class="col-sm-12 tablas table-responsive" style="display: none;">
                    <table class="display table table-bordered table-striped" id="t_porie" style="color:#000;font-size:10px;width:100%;">
                      <thead>
                        <tr style="color:#fff;background-color: rgb(112,134,164);font-weight: bolder;text-align: center;">
                            <td colspan="10">DATOS DE LA II.EE</td>
                            <td colspan="4">DOCUMENTO - SUSTENTO</td>
                        </tr>
                        <tr style="color:#fff;">
                            <td style="background-color: rgb(32,55,100);"><b>N°</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Código Modular</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Código Local</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Institución Educativa</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Distrito</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Tec</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Jor</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Nivel</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Gestion</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Turno</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>Expediente</b></td>
                            <td style="background-color: rgb(169,169,169);" title="Oficio simple de solicitud de requerimiento de incremento de sección"><b>Oficio</b></td>
                            <td style="background-color: rgb(169,169,169);" title="Copia de la ficha de resumen extraída del aplicativo SIAGIE"><b>Ficha de resumen</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>FUT y ticket</b></td>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                </div>

                <div id="divporsecciones" class="col-sm-12 tablas" style="display: none;">
                    <div class="table-responsive">
                    <table class="display table table-bordered table-striped" id="t_porsecciones" style="color:#000;font-size:10px;width:100%;">
                      <thead>
                        <tr style="color:#fff;background-color: rgb(112,134,164);font-weight: bolder;text-align: center;">
                            <td colspan="11">DATOS DE LA II.EE</td>
                            <td colspan="5">REQUERIMIENTO</td>
                            <td colspan="4">DOCUMENTO - SUSTENTO</td>
                        </tr>
                        <tr style="color:#fff;">
                            <td style="background-color: rgb(32,55,100);"><b>N°</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Código Modular</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Código Local</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Institución Educativa</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Distrito</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Tec</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Jor</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Horas</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Nivel</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Gestion</b></td>
                            <td style="background-color: rgb(32,55,100);"><b>Turno</b></td>

                            <td style="background-color: rgb(169,169,169);"><b>Grado</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>Cantidad de secciones de incremento</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>Horas bolsa</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>Cuenta con aula fisica</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>Cuenta con mobiliario</b></td>

                            <td style="background-color: rgb(169,169,169);"><b>Expediente</b></td>
                            <td style="background-color: rgb(169,169,169);" title="Oficio simple de solicitud de requerimiento de incremento de sección"><b>Oficio</b></td>
                            <td style="background-color: rgb(169,169,169);" title="Copia de la ficha de resumen extraída del aplicativo SIAGIE"><b>Ficha de resumen</b></td>
                            <td style="background-color: rgb(169,169,169);"><b>FUT y ticket</b></td>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function anexo_racio_reqseccincrementosecciones(athis){
        $(athis).prop("href","{{route('anexo_racio_reqseccincrementosecciones')}}?idnivel="+$("#txt_nivel").val());
    }

    function verreqseccincrementoie(){
        ajax_data = {
            "box"    : $("input[name=box]:checked").val(),
            "idnivel": $("#txt_nivel").val(),
            "alt" : Math.random()
        }
        $.ajax({
            type: "GET",
            url: "{{route('verreqseccincrementoie')}}",
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
                $(".tablas").css('display','none');
                $("#divporie").css('display','');
                    var exp = []
                    $("#msj1").html(data.length);
                    for (let i = 0; i < data.length; i++) {
                        data[i]['nro']    = i+1;
                        data[i]['t_exp']  = '<a target="_blank" href="http://siic01.ugel01.gob.pe/index.php/notificacion/fut/'+data[i]['idreclamo']+'">PDF</a>';
                        data[i]['t_adj1'] = '<a target="_blank" href="'+data[i]['adj1']+'">PDF</a>';
                        data[i]['t_adj2'] = '<a target="_blank" href="'+data[i]['adj2']+'">PDF</a>';
                        exp.push(data[i]['idreclamo']);
                    }
                    table4.clear().draw();
                    table4.rows.add(data).draw();
                    $("#btn_expedientes").prop("href","https://ventanillavirtual.ugel01.gob.pe/index.php/buzondecomunicaciones/descargar_exp?idreclamo="+exp.join());
                    $("table tbody tr td").css({'padding':'0px','text-align':'center'});
                }
        });
    }
    
    var table4 = $("#t_porie").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "codmod" },
                            { "data": "codlocal" },
                            { "data": "institucion" },
                            { "data": "distrito" },
                            { "data": "tecnico" },
                            { "data": "jornada" },
                            { "data": "nivel" },
                            { "data": "gestion" },
                            { "data": "turno" },
                            { "data": "cod_reclamo" },
                            { "data": "t_adj1" },
                            { "data": "t_adj2" },
                            { "data": "t_exp" },                            
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
        });
        
    
        function verreqseccincrementosecciones(){
        ajax_data = {
            "box"     : $("input[name=box]:checked").val(),
            "idnivel" : $("#txt_nivel").val(),
            "alt" : Math.random()
        }
        $.ajax({
            type: "GET",
            url: "{{route('verreqseccincrementosecciones')}}",
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
                    $(".tablas").css('display','none');
                    $("#divporsecciones").css('display','');
                    for (let i = 0; i < data.length; i++) {
                        data[i]['nro']    = i+1;
                        data[i]['t_exp']  = '<a target="_blank" href="http://siic01.ugel01.gob.pe/index.php/notificacion/fut/'+data[i]['idreclamo']+'">PDF</a>';
                        data[i]['t_adj1'] = '<a target="_blank" href="'+data[i]['adj1']+'">PDF</a>';
                        data[i]['t_adj2'] = '<a target="_blank" href="'+data[i]['adj2']+'">PDF</a>';
                    }
                    table5.clear().draw();
                    table5.rows.add(data).draw();
                    $("table tbody tr td").css({'padding':'0px','text-align':'center'});
                }
        });
    }
    
    var table5 = $("#t_porsecciones").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "codmod" },
                            { "data": "codlocal" },
                            { "data": "institucion" },
                            { "data": "distrito" },
                            { "data": "tecnico" },
                            { "data": "jornada" },
                            { "data": "horas" },
                            { "data": "nivel" },
                            { "data": "gestion" },
                            { "data": "turno" },

                            { "data": "grado" },
                            { "data": "seccincremento" },
                            { "data": "bolsahoras" },
                            { "data": "aulafisica" },
                            { "data": "mobiliario" },

                            { "data": "cod_reclamo" },
                            { "data": "t_adj1" },
                            { "data": "t_adj2" },
                            { "data": "t_exp" },                            
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
        });
</script>

<script>
    verreqseccincrementoie();
</script>

@endsection

<?php
/*
?>
<div id="divexample" class="col-sm-12">
                <table id="example" class="display" style="width:100%;font-size:9px;">
                    <thead>
                        <tr>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>N°</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Código Modular</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Código Local</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Institución Educativa</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Distrito</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Tec</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Jor</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Nivel</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Gestion</b></th>
                            <th style="background-color: rgb(32,55,100);color:#fff;"><b>Turno</b></th>
                            <th style="background-color: rgb(169,169,169);color:#fff;"><b>Expediente</b></th>
                            <th style="background-color: rgb(169,169,169);color:#fff;" title="Oficio simple de solicitud de requerimiento de incremento de sección"><b>Oficio</b></th>
                            <th style="background-color: rgb(169,169,169);color:#fff;" title="Copia de la ficha de resumen extraída del aplicativo SIAGIE"><b>Ficha de resumen</b></th>
                            <th style="background-color: rgb(169,169,169);color:#fff;"><b>FUT y ticket</b></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>N°</th>
                            <!--<th></th>-->
                            <th>ESTADO</th>
                            <th>PLAZA</th>
                            <th>CARGO</th>
                            <th>DISTRITO</th>
                            <th>INSTITUCIÓN</th>
                            <th>AREA</th>
                            <th>DISTRIBUCIÓN</th>
                            <th>FECHA FIN</th>
                            <th>TIPO</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

<script>
    $('#example thead tr').clone(true).appendTo( '#example thead' );
    $('#example thead tr th').css('padding-left','6px');
    $('#example thead tr:eq(1) th').each( function (i) {
        if(i>0){
            var title = $(this).text();
            var style = $(this).attr('style');
            $(this).html( '<input type="text" style="'+style+'" placeholder="'+title+'" />' );
            
            $( 'input', this ).on( 'keyup change', function () {
                if ( table6.column(i).search() !== this.value ) {
                    table6
                        .column(i)
                        .search( this.value )
                        .draw();
                }
            } );
        }
        
    } );

    var table6 = $('#example').DataTable( {
        orderCellsTop: true,
        fixedHeader: true,
       
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        },
        "columns": [
            { "data": "nro" },
            //{ "data": "cbox" },
            { "data": "t_estado" },
            { "data": "t_codigo_nexus" },
            { "data": "t_cargo" },
            { "data": "t_distrito" },
            { "data": "t_ie" },
            { "data": "t_area" },
            { "data": "t_distribucion" },
            { "data": "t_fin_contrato" },
            { "data": "t_tipo_vacante" },
        ],
    } );
</script>

<?php
*/
?>