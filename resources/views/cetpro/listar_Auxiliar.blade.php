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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b> Descargar Reportes</b>
    </h5>
        <div class="position-relative form-group">
            
        <form id="formulario" onsubmit="return false;" enctype="multipart/form-data">
        <div class="col-sm-2">Cetpo:</div>
                <div class="col-sm-5">
                  <select class="form-control" name="codmod" id="codmod" onchange="generarenlaces();">
                  <option value="0707844">JOSÉ FAUSTINO SÁNCHEZ CARRIÓN</option>
                  <option value="0330084">MARGARITA GONZALES DE DANKERS</option>
                  <option value="0330027">PEDRO PAULET</option>
                  <option value="0643411">VIRGEN DEL ROSARIO</option>
                  <option value="0643445">YACHAYHUASI</option>
                  <option value="1240761">7215 NACIONES UNIDAS</option>
                  <option value="0537456">PROMAE</option>
                  <option value="0694646">JAVIER PEREZ DE CUELLAR</option>
                  <option value="0607200">LA INMACULADA CONCEPCION</option>
                  <option value="0647149">LA MEDALLA MILAGROSA</option>
                  <option value="0607226">MARIA AUXILIADORA</option>
                  <option value="0503425">JOSE GALVEZ BARRENECHEA</option>
                  <option value="0469114">SAN FRANCISCO</option>
                  <option value="0647099">SAN GABRIEL</option>
                  <option value="0330001">VILLA JARDIN</option>
                  <option value="0643320">VIRGEN DEL CARMEN</option>


                  </select>
                  </div>

        <div class="col-sm-2">Año:</div>
                <div class="col-sm-5">
                  <select class="form-control" name="anio" id="anio" onchange="generarenlaces();">
                  <option>2024</option>
                  <option selected>2023</option>
                  <option>2022</option>
                  </select>
                  </div>
                <div class="col-sm-2"></div>
                <div class="col-sm-10"><br><a id="btn_matriculados" class="btn btn-success" href="">DESCARGAR MATRICULADOS</a> <a id="btn_titulados" class="btn btn-danger" href="">DESCARGAR TITULADOS</a> </div>
            </div>
            @csrf
        </form>
    </div>
</div>

<br>

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b> Otras descargas</b>
    </h5>
        <div class="position-relative form-group">
            
            <a href="" id="btn_todomatricula">DESCARGAR TODOS LOS MATRICULADOS DEL AÑO</a>
        
    </div>
</div>
        
        
        
        <script>
            function generarenlaces(){
                var codmod=$("#codmod").val();
                var anio=$("#anio").val();
                $("#btn_matriculados").prop('href','https://aplicacion.ugel01.gob.pe/public/excel_registro_matricula_cetpro?codmod='+codmod+'&anio='+anio);
                $("#btn_titulados").prop('href','https://siapcetpro.ugel01.gob.pe/public/exportsTituladosAnexo7?codmod='+codmod+'&anio='+anio);
                $("#btn_todomatricula").prop('href','https://aplicacion.ugel01.gob.pe/public/excel_registro_matricula_cetpro?&anio='+anio);
                $("#btn_todomatricula").html('DESCARGAR TODOS LOS MATRICULADOS DEL AÑO '+anio);
            }
            
            generarenlaces();
            
        </script>
        
        
            
@endsection

