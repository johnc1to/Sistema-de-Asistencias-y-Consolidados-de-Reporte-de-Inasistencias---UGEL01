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

<style>
    #t_resumen tr td{
        padding-right:5px;
        padding-left:5px;
    }
    
    .si{
        color:green;
        font-size:16px;
        font-weight: bolder;
    }
    
    .no{
        color:red;
        font-size:16px;
        font-weight: bolder;
    }
    
</style>

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REPORTE DE PROGRAMAS</b>
    <!--
    <a href="https://aplicacion.ugel01.gob.pe/public/excel_registro_matricula_cetpro" target="_blank" class="btn btn-danger">Descargar Registro de Matricula Institucional</a>
    -->    
    </h5>
        <div class="position-relative form-group">
            
            <div class="divtabla" id="div_citas">
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="width:15px;color:#fff;"><b>N</b></td>
                                <td style="width:45px;color:#fff;"><b>Cod Catalogo</b></td>
                                <td style="width:55px;color:#fff;"><b>Institucion</b></td>
                                <td style="width:55px;color:#fff;"><b>Distrito</b></td>
                                <td style="width:55px;color:#fff;"><b>Gestion</b></td>
                                <td style="width:55px;color:#fff;"><b>Sector Economico</b></td>
                                <td style="width:55px;color:#fff;"><b>Familia Productiva</b></td>
                                <td style="width:65px;color:#fff;"><b>Actividad Economica</b></td>
                                <td style="width:55px;color:#fff;"><b>Programa de Estudio</b></td>
                                <td style="width:55px;color:#fff;"><b>Nivel Formativo</b></td>
                                <td style="width:35px;color:#fff;"><b>Creditos</b></td>
                                <td style="width:35px;color:#fff;"><b>Horas</b></td>
                                <td style="width:55px;color:#fff;"><b>Creado</b></td>
                                
                                <td style="width:55px;color:#fff;"><b>Estado</b></td>
                                
                                <td style="width:35px;color:#fff;"><b>Programa de estudio</b></td>
                                <td style="width:35px;color:#fff;"><b>Perfil de egreso</b></td>
                                <td style="width:35px;color:#fff;"><b>Itinerario formativo</b></td>
                                
                                <td style="width:35px;color:#fff;"><b>Programa de estudio</b></td>
                                <td style="width:35px;color:#fff;"><b>Perfil de egreso</b></td>
                                <td style="width:35px;color:#fff;"><b>Itinerario formativo</b></td>
                                
                                <!--<td style="width:35px;color:#fff;"><b>Eliminar</b></td>-->
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>

        </div>
    </div>
</div>

<?php
function base_url(){
    return 'https://siapcetpro.ugel01.gob.pe/public/';
}
?>

<script type="text/javascript">

function ver_programa_de_estudio(){
    
    ajax_data = {
      "situacion" : $("input[name=btn_situacion]:checked").val(),
      "alt"       : Math.random()
    }
    $.ajax({
        type: "GET",
        url: "{{route('listarprogramas')}}",
        data: ajax_data,
        dataType: "json",
        beforeSend: function(){
              $("#t_programas tbody").html('<tr><td colspan="12"><center><img src="assets/images/load10.gif"></center></td></tr>');
        },
        error: function(){
              alert("error peticiÃ³n ajax");
        },
        success: function(data){
            
            table4.clear().draw();
            var programa = data['programa'];
            if(programa){
                for (var i = 0; i < programa.length; i++) {
                    programa[i]['nro']      = i+1;
                    
                    programa[i]['vprogaestudio'] = (programa[i]['cantidUdd'])?'<center class="si">SI</center>':'<center class="no">NO</center>';
                	programa[i]['vperfilegreso'] = (programa[i]['cantidCap'])?'<center class="si">SI</center>':'<center class="no">NO</center>';
                	programa[i]['vanexo']        = (programa[i]['cantidIll'])?'<center class="si">SI</center>':'<center class="no">NO</center>';
                	
                	programa[i]['t_estado']      = (programa[i]['cantidUdd'] && programa[i]['cantidCap'] && programa[i]['cantidIll'])?'<center class="si">COMPLETO</center>':'<center class="no">INCOMPLETO</center>';
                	
                	programa[i]['progaestudio'] = (programa[i]['cantidUdd'])?'<a target="_blank" href="<?=base_url()?>pdf_programadeestudio?idprograma='+programa[i]['idPro']+'" class="btn btn-warning">pdf</a>':'';
                	programa[i]['perfilegreso'] = (programa[i]['cantidCap'])?'<a target="_blank" href="<?=base_url()?>pdf_perfildelegresado?idprograma='+programa[i]['idPro']+'" class="btn btn-warning">pdf</a>':'';
                	programa[i]['anexo']        = (programa[i]['cantidIll'])?'<a target="_blank" href="<?=base_url()?>pdf_anexo10A?idprograma='+programa[i]['idPro']+'" class="btn btn-warning">pdf</a>':'';
                }
            table4.rows.add(programa).draw();
            $("#t_programas tr td").css({'padding-top':'3px','padding-bottom':'2px','padding-right':'3px','padding-left':'3px'});
            }
            
            g_programa = programa;
            
          }
    });
}

var table4 = $("#t_programas").DataTable( {
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        "iDisplayLength": 35,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                        data:[],
                        "columns": [
                            { "data": "nro" },
                            { "data": "codCatCnn" },
                            { "data": "institucion" },
                            { "data": "distrito" },
                            { "data": "gestion" },
                            { "data": "secEcoPee" },
                            { "data": "famProPee" },
                            { "data": "actEcoPee" },
                            { "data": "proEstPee" },
                            { "data": "nivForPee" },
                            { "data": "credPee" },
                            { "data": "horPee" },
                            { "data": "t_creado" },
                            
                            { "data": "t_estado" },
                            
                            { "data": "vprogaestudio" },
                            { "data": "vperfilegreso" },
                            { "data": "vanexo" },
                            
                            { "data": "progaestudio" },
                            { "data": "perfilegreso" },
                            { "data": "anexo" },
                            
                            //{ "data": "eliminar" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

ver_programa_de_estudio();

</script>
@endsection

