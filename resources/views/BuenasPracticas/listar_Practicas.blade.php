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
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b> Buenas Practicas</b> <a target="_blank" class="btn btn-info" href="https://buenaspracticas.ugel01.gob.pe/">Ver en Pagina Web</a>
    </h5>
        <div class="position-relative form-group">
            
        <form id="formulario" onsubmit="grabar_Practicas();return false;" enctype="multipart/form-data">
            <div class="row"><!--Debe sumar 12-->
                <div class="col-sm-2">idpracticas:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="idpracticas" id="idpracticas" readonly></div>
                <!--Debe sumar 12-->
                <div class="col-sm-2">Titulo:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="Titulo" id="Titulo"></div>
                <!--Debe sumar 12-->
                <div class="col-sm-2">Descripción:</div>
                <div class="col-sm-10"><input type="text" class="form-control" name="descripcion" id="descripcion"></div>

                <!--Debe sumar 12-->
                <div class="col-sm-2">Imagen:</div>
                <div class="col-sm-10"><input type="file" class="form-control" name="imagen" id="imagen"></div>
                <!--Debe sumar 12-->
                <div class="col-sm-2">Categoria:</div>
                <div class="col-sm-10">
                <select class="form-control" name="idCategoria" id="idCategoria"> 
                <?php
                foreach ($categoria as $key) {
                  ?>
                  <option value="<?=$key->idCategoria?>"><?=$key->titulo?></option>
                  <?php
                }
                ?>
                </select>
                </div>
                <!--Debe sumar 12-->
                <div class="col-sm-2">video:</div>
                <div class="col-sm-10"><input type="ruc" class="form-control" name="video" id="video"></div>
                <!--Debe sumar 12-->
                <div class="col-sm-2">PDF:</div>
                <div class="col-sm-10"><input type="file" class="form-control" name="pdf" id="pdf"></div>


                <div class="col-sm-2"></div>
                <div class="col-sm-10"><br><button class="btn btn-success">GRABAR</button> <input class="btn btn-danger" type="reset" value="Cancelar"> </div>
            </div>
            @csrf
        </form>
            
            <div class="divtabla" id="div_citas">
            <table class="display table table-bordered table-striped table-dark" id="t_programas" style="color:#000;font-size:10px;width:100%;">
                          <thead>
                            <tr style="background-color:rgb(0,117,184);">
                                <td style="width:15px;color:#fff;"><b>N</b></td>
                                <td style="width:55px;color:#fff;"><b>Editar</b></td>
                                <td style="width:35px;color:#fff;"><b>Eliminar</b></td>
                                <td style="width:45px;color:#fff;"><b>Titulo</b></td>
                                <td style="width:45px;color:#fff;"><b>Descripción</b></td>
                                <td style="width:35px;color:#fff;"><b>Imagen</b></td>
                                <td style="width:45px;color:#fff;"><b>Categoria</b></td>
                                <td style="width:55px;color:#fff;"><b>video</b></td>
                                <td style="width:35px;color:#fff;"><b>PDF</b></td>
                               
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">


function grabar_Practicas(id='formulario'){
          var formData = new FormData($("#"+id)[0]);
          var message = "";
          $.ajax({
              url: "{{route('guardar_Practicas')}}",  
              type: 'POST',
              data: formData,
              dataType: "json",
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function(){
                  
              },
              success: function(data){
                formulario.reset();
                tabla_Practicas();
                alert('Guardado');
              },
              error: function(){
                alert('Se ha producido un error, recargue la página e inténtelo de nuevo.');
              }
            });
        }

function tabla_Practicas(){
    ajax_data = {
      "alt"    : Math.random()
    }
    $.ajax({
                    type: "GET",
                    url: "{{route('tabla_Practicas')}}",
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
                          for (let i = 0; i < data.length; i++) {
                            data[i]['nro']= i+1;
                            data[i]['tpdf']='<a target="_black" href=".'+data[i]['pdf']+'"><img width="50px" src="assets/images/Logo-archivo-PDF.png"></a>';
                            data[i]['timagen']='<a target="_black" href=".'+data[i]['imagen']+'"><img width="50px" src=".'+data[i]['imagen']+'"></a>';
                            data[i]['tvideo']='<a target="_black" href="'+data[i]['video']+'"><img width="50px" src="assets/images/youtube.png"></a>';
                            data[i]['editar']='<span class="btn btn-success" onclick="editar('+i+')">editar</span>';
                            data[i]['eliminar']='<span class="btn btn-danger" onclick="eliminar_Practicas('+data[i]['idpracticas']+')">eliminar</span>';
                          }
                          table4.clear().draw();
                          table4.rows.add(data).draw();
                          $("#t_programas img").parent().parent().css('text-align','center');
                          g_data=data;
                      }
              });
              
}
function editar(nro){
  var data = g_data[nro];
  $("#idpracticas").val(data['idpracticas']);
  $("#Titulo").val(data['titulo']);
  $("#descripcion").val(data['descripcion']);
  $("#idCategoria").val(data['idCategoria']);
  $("#video").val(data['video']);
}

function eliminar_Practicas(idpracticas){
    ajax_data = {
      "idpracticas"   : idpracticas,
      "alt"    : Math.random()
    }
    if(confirm('¿Desea eliminar?')){
    $.ajax({
                    type: "GET",
                    url: "{{route('eliminar_Practicas')}}",
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
                      alert('eliminado');
                      tabla_Practicas();
                      }
              });
      }else{

      }

}


tabla_Practicas();

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
                            { "data": "editar" },
                            { "data": "eliminar" },
                            { "data": "titulo" },
                            { "data": "descripcion" },
                            { "data": "timagen" },
                            { "data": "descategoria" },
                            { "data": "tvideo" },
                            { "data": "tpdf" },
                            

                            //{ "data": "eliminar" },
                        ],                          
                        rowCallback: function (row, data) {},
                        filter: true,
                        info: true,
                        ordering: true,
                        processing: true,
                        retrieve: true                          
                    });

</script>

@endsection

