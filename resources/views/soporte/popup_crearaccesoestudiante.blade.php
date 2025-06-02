<form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="if(confirm(msj)){guardaraccesoestudiante();}return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>CREAR ACCESO ESTUDIANTE</b></h4>      
    </div>
    <div class="modal-body">

    <div id="testmodal" style="padding: 5px 20px;">

    <input name="idTip" value="4" type="hidden">
    <input name="codmodSop" value="" type="hidden">
    <input name="id_contactoSop" value="" type="hidden">

    <div class="row">
        <div class="col-xs-12">
            <b>(*) Puede copiar y pegar el listado de estudiantes de un archivo Excel</b>
            <br>
            <b>(*) Para insertar un nuevo registro presione ENTER</b>
            <br>
            <b style="color:red;" id="msj"></b>
            <br><br>
        </div>
        <div class="col-xs-12">
            <div id="spreadsheet"></div>
        </div>
    </div>

    </div>
    @csrf
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_guardaralumno" style="">Guardar</button>
      <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>
    </div>
</form>


<script>
var optNivel = [];
    switch ({{$idnivel}}) {
        //case 2: optNivel = ["primero","segundo","tercero","cuarto","quinto","sexto"];  break;
        case 3: optNivel = ["1° SECUNDARIA","2° SECUNDARIA","3° SECUNDARIA","4° SECUNDARIA","5° SECUNDARIA"];  break;
        case 4: optNivel = ["1° PRIMARIA","2° PRIMARIA","3° PRIMARIA","4° PRIMARIA","5° PRIMARIA","6° PRIMARIA"];  break;    
        case 5: optNivel = ["2 AÑOS","3 AÑOS","4 AÑOS","5 AÑOS"];  break;
        //case 6: optNivel = ["primero","segundo","tercero","cuarto","quinto"];  break;
        //case 7: optNivel = ["primero","segundo","tercero","cuarto","quinto"];  break;
        default: optNivel = []; break;
    }

var g_reporte = [];
    var g_datos_modulo = [];
    function guardaraccesoestudiante(){
        var msjalerta = '';
        var alerta = 1;
        g_datos_modulo = [];
        var mdata = g_reporte.getData();
        
        for (var i = 0; i < mdata.length; i++) {
                var fila = mdata[i];
                if(fila[0]==''){ alerta=0; }
                if(fila[1]==''){ alerta=0; }
                if(fila[2]==''){ alerta=0; }
                if(fila[3]==''){ alerta=0; }
                if(fila[4]==''){ alerta=0; }
                if( optNivel.indexOf(fila[6])==-1){ alerta=0; }
                g_datos_modulo.push(mdata[i].join('||'));
        };
        
        if(alerta){
        ajax_data = {
          "codmodSop"      : $("input[name=codmodSop]").val(),
          "id_contactoSop" : $("input[name=id_contactoSop]").val(),
          "idTip"          : $("input[name=idTip]").val(),
          "datos_modulo"   : g_datos_modulo.join('&&'),
          "_token"         : $("input[name=_token]").val(),
          "alt"            : Math.random()
        }
        $.ajax({
            type: "POST",
            url: "{{route('guardaraccesoestudiante')}}",
            data: ajax_data,
            dataType: "html",
            beforeSend: function(){
                  $("#btn_guardar").prop('disabled',true);
            },
            error: function(){
                  alert("error peticiÃ³n ajax");
            },
            success: function(data){
                versolicitudes();
                $("#fc_popuplg").click();
              }
        });
        }else{
            alert('Complete todos los campos antes de guardar');
        }


    }    
    
    $("#msj").html('(*) '+msj);
    $("#spreadsheet").html('');                        
                        g_reporte = [];
                        g_reporte = jexcel(document.getElementById('spreadsheet'), {
                            data:[['','','','','','','']],
                            columns: [
                                {
                                    type: 'dropdown',
                                    title:'Tipo de Documento',
                                    width:130,
                                    source: ['DNI','CARNET DE EXTRANJERIA','PASAPORTE']
                                },
                                {
                                    type: 'text',
                                    title:'Codigo Estudiante',
                                    width:130
                                },
                                {
                                    type: 'text',
                                    title:'DNI',
                                    width:80
                                },
                                {
                                    type: 'text',
                                    title:'Nombres',
                                    width:140,
                                },
                                {
                                    type: 'text',
                                    title:'Apellido Paterno',
                                    width:140,
                                },
                                {
                                    type: 'text',
                                    title:'Apellido Materno',
                                    width:140,
                                },
                                {
                                    type: 'dropdown',
                                    title:'Nivel',
                                    width:80,
                                    source: optNivel
                                }
                            ]

                        });
</script>



<!--
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>DNI:</b></div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="dniSop" class="inputdatos form-control" required></div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Codigo Estudiante:</b></div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="codSop" class="inputdatos form-control" required></div>
    </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Nombres:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="nomSop" class="inputdatos form-control" required></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Apellido Paterno:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="apepatSop" class="inputdatos form-control" required></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Apellido Materno:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="apematSop" class="inputdatos form-control" required></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Grado:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
            <select name="graSop" class="form-control" required>
                <option value="">Elija el grado</option>
                <option>primero</option>
                <option>segundo</option>
                <option>tercero</option>
                <option>cuarto</option>
                <option>quinto</option>
            </select>
        </div>
      </div>
    
      <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Observación:</b></div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="obsSop" class="inputdatos form-control" required></div>
      </div>
    -->