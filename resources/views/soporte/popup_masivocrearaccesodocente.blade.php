<form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="if(confirm(msj)){guardaraccesodocente();}return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>CREAR ACCESO DOCENTE</b></h4>      
    </div>
    <div class="modal-body">

    <div id="testmodal" style="padding: 5px 20px;">

    <input name="idTip" value="3" type="hidden">
    <input name="codmodSop" value="" type="hidden">
    <input name="id_contactoSop" value="" type="hidden">

    <div class="row">
        <div class="col-xs-12">
            <b>(*) Puede copiar y pegar el listado de docentes de un archivo Excel</b>
            <br>
            <b>(*) Para insertar un nuevo registro presione ENTER</b>
            <br>
            <b id="msj"></b>
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
var g_reporte = [];
    var g_datos_modulo = [];
    function guardaraccesodocente(){
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
            url: "{{route('guardaraccesodocente')}}",
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
                                    title:'DNI',
                                    width:80
                                },
                                {
                                    type: 'text',
                                    title:'Nombres',
                                    width:130,
                                },
                                {
                                    type: 'text',
                                    title:'Apellido Paterno',
                                    width:130,
                                },
                                {
                                    type: 'text',
                                    title:'Apellido Materno',
                                    width:130,
                                },
                                {
                                    type: 'text',
                                    title:'Correo',
                                    width:120,
                                },
                                {
                                    type: 'text',
                                    title:'Celular',
                                    width:120,
                                }
                            ]

                        });
</script>