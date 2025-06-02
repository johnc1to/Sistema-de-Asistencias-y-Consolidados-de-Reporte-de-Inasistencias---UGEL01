@extends($layout)
@section('html')

<script src="https://bossanova.uk/jexcel/v3/jexcel.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jexcel/v3/jexcel.css" type="text/css" />
<script src="https://jsuites.net/v3/jsuites.js"></script>
<link rel="stylesheet" href="https://jsuites.net/v3/jsuites.css" type="text/css" />

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REGISTRO DE EQUIPOS INFORMATICOS</b></h5>
        <div class="position-relative form-group">
            
            <form id="formulario01" enctype="multipart/form-data" style="width:100%;" onsubmit="return false;">


            <div class="col-sm-5" style="color:#000;">
                <select id="idtipo" class="form-control" onchange="ver_tabla();">
                    <?php
                    foreach ($tipo as $key) {
                    ?>
                    <option value="<?=$key['idTipo']?>"><?=$key['descripcion']?></option>
                    <?php
                    }
                    ?>
                </select>
                
            </div>

            <div class="col-sm-12" style="color:#000;"><br></div>
			<div class="col-sm-12" style="color:#000;"><u><b>RECOMENDACIONES:</b></u></div>
			<div class="col-sm-12" style="color:#000;"><b>(*) Llene la información solicitada</b></div>
			<div class="col-sm-12" style="color:#000;"><b>(*) Al finalizar de llenar toda la información presiona el botón Grabar</b> </div>
			<div class="col-sm-12" style="color:#000;"><b>(*) Para eliminar un registro, selecione en la columna ESTADO -> ELIMINAR y presione el botón Grabar</b> </div>
			<div class="col-sm-12" style="color:#000;"><br></div>
			
			<div class="row" style="color:#000;">            
                <div class="col-sm-3">
                     <button class="btn btn-success" onclick="guardar_inv();" id="btn_guardar">GRABAR</button> 
                </div>
            </div>
            <div class="col-sm-12" style="color:#000;"><br></div>
			<div class="col-sm-12" style="color:#000;font-size:10px;padding-left:0px;width:100%;"><div id="spreadsheet"></div></div>
            @csrf
            </form>

        </div>
    </div>
</div>

<script>
    
    var g_datos= [];
            function guardar_inv(){
            var data = g_jexcel.getData();
            g_datos = [];
            for (var i = 0; i < data.length; i++) {
                        g_datos.push(data[i].join('||').toUpperCase());
            };
                ajax_data = {
                        "data"        : g_datos.join('&&'),
                        "codlocal"    : '<?=$codlocal?>',
                        "id_contacto" : '<?=$id_contacto?>',
                        "idtipo"      : $("#idtipo").val(),
                        "_token"      : $("input[name='_token'").val(),
                        "alt"         : Math.random()
                    }
                    $.ajax({
                                    type: "POST",
                                    url: "{{route('guardar_inv')}}",
                                    data: ajax_data,
                                    dataType: "html",
                                    beforeSend: function(){
                                        $("#btn_guardar").prop('disabled',true);
                                        $("#spreadsheet").html('<center><img src="assets/images/load10.gif"></center>');
                                    },
                                    error: function(){
                                        alert("error peticion ajax");
                                    },
                                    success: function(data){
                                        alert('Datos Guardados');
                                        $("#btn_guardar").prop('disabled',false);
                                        ver_tabla();
                                    }
                            });
            }


    function ver_tabla(){
        ajax_data = {
          "codlocal"    : '<?=$codlocal?>',
          "idtipo"      : $("#idtipo").val(),
          "_token"      :$("input[name=_token]").val(),
          "alt"         : Math.random()
        }
        $.ajax({
                type: "POST",
                url: "{{route('get_inv')}}",
                data: ajax_data,
                dataType: "json",
                beforeSend: function(){
                      //imagen de carga
                      $("#btn_guardar").prop('disabled',true);
                      $("#spreadsheet").html('<center><img src="assets/images/load10.gif"></center>');
                },
                error: function(){
                      alert("error peticion ajax");
                },
                success: function(data){
                        var reporte = [];
                        if(data.length){
                        for (var i = 0; i < data.length; i++) {
                        	var key = [];
                        	key.push(data[i]['idInf']);
                        	key.push((data[i]['estado']==1)?'REGISTRADO':'');
                        	key.push(data[i]['codigo_sbn']);
                        	key.push(data[i]['denominacion']);
                        	key.push(data[i]['marca']);
                        	key.push(data[i]['modelo']);
                        	key.push(data[i]['color']);
                        	key.push(data[i]['serie']);
                        	key.push(data[i]['observacion']);
                            reporte.push(key);
                        }
                        };
                    
                        $(".app-container").addClass('closed-sidebar');
                        jexcel_programa_de_estudio(reporte);
                        //vertical_notas();
                        $("#btn_guardar").prop('disabled',false);
                  }
          });
        
    }
    
    var g_jexcel=[];
    function jexcel_programa_de_estudio(data=['','','','','']){
        if(data.length==0){ data=['']; }
        $("#spreadsheet").html('');
    g_jexcel = [];
    g_jexcel = jexcel(document.getElementById('spreadsheet'), {
        data:data,
        columns: [
            {
                readOnly:true,
                type: 'text',
                title:'idInf',
                width:0.1
            },
            
            {
                type: 'dropdown',
                title:'ESTADO',
                width:100,
                source:["REGISTRADO","ELIMINAR"]
            },
            {
                type: 'text',
                title:'CODIGO SBN',
                width:120
            },
            {
                type: 'text',
                title:'DENOMINACIÓN',
                width:150
            },
            {
               type: 'text',
                title:'MARCA',
                width:120
            },
            {
                type: 'text',
                title:'MODELO',
                width:120
            },
            {
                type: 'text',
                title:'COLOR',
                width:120
            },
            {
                type: 'text',
                title:'SERIE',
                width:120
            },
            {
                type: 'text',
                title:'OBSERVACIÓN',
                width:200,
            },
            
         ]
    });
    
    }
    
    function vertical_notas(){
        $(".readonly").css('color','#000');
        var cabeceras = $("#spreadsheet thead tr td");
        var r_texto = [];
        	for (var i = 0; i < cabeceras.length; i++) {
        	    r_texto = cabeceras[i].innerText.split(' ');
        	    if(i>2){
        	         var nuevo_texto = [];
        	         var parte_texto = [];
                     var orden_texto = [];
                     for (let t = 0; t < r_texto.length; t++) {
                        if(t==0 || t%6>0){
                            parte_texto.push(r_texto[t]);
                        }else{
                            orden_texto.push(parte_texto.join(' '));
                            parte_texto = [];
                            parte_texto.push(r_texto[t]);
                        }
                     }
                     orden_texto.push(parte_texto.join(' '));
                     parte_texto = [];	         	
        	         $(cabeceras[i]).html('<b style="writing-mode: vertical-lr;transform: rotate(180deg);">'+orden_texto.reverse().join('<br>')+'</br>');
        	    }else{
        	        $(cabeceras[i]).html('<b>'+cabeceras[i].innerText+'</b>');
        	    }
        	}
        }
        
        
        ver_tabla();
        
</script>

@endsection