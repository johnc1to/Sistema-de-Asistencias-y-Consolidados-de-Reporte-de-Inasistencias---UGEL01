@extends($layout)
@section('html')

<script src="https://bossanova.uk/jexcel/v3/jexcel.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jexcel/v3/jexcel.css" type="text/css" />
<script src="https://jsuites.net/v3/jsuites.js"></script>
<link rel="stylesheet" href="https://jsuites.net/v3/jsuites.css" type="text/css" />

<div class="main-card mb-12 card">
    <div class="card-body"><h5 class="card-title" style="font-size:20px;"><b>REGISTRO DE EPPS</b></h5>
        <div class="position-relative form-group">
            
            <form id="formulario01" enctype="multipart/form-data" style="width:100%;" onsubmit="return false;">

            <div class="col-sm-12" style="color:#000;"><br></div>
			<div class="col-sm-12" style="color:#000;"><u><b>RECOMENDACIONES:</b></u></div>
			<div class="col-sm-12" style="color:#000;"><b>(*) Llene la información solicitada</b></div>
			<div class="col-sm-12" style="color:#000;"><b>(*) Al finalizar de llenar toda la información presion el botón Grabar</b> </div>
			<div class="col-sm-12" style="color:#000;"><br></div>
			
			<div class="row" style="color:#000;">            
                <div class="col-sm-3">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <button class="btn btn-success" onclick="guardar_epps();" id="btn_guardar">GRABAR</button> 
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
            function guardar_epps(){
            var data = g_jexcel.getData();
            g_datos = [];
            for (var i = 0; i < data.length; i++) {
                        g_datos.push(data[i].join('||').toUpperCase());
            };
                ajax_data = {
                        "data"    : g_datos.join('&&'),
                        "_token"  : $("input[name='_token'").val(),
                        "alt"     : Math.random()
                    }
                    $.ajax({
                                    type: "POST",
                                    url: "{{route('guardar_epps')}}",
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
          "idequipo_codlocal" : '<?=$idequipo_codlocal?>',
          "_token"  :$("input[name=_token]").val(),
          "alt"     : Math.random()
        }
        $.ajax({
                type: "POST",
                url: "{{route('get_epps')}}",
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
                        	key.push(data[i]['id_epps']);
                        	key.push(data[i]['apellidos_y_nombres']);
                        	key.push(data[i]['sexo']);
                        	key.push(data[i]['lentes']);
                        	key.push(data[i]['mascarilla']);
                        	key.push(data[i]['filtro']);
                        	key.push(data[i]['guantes_jebe']);
                        	key.push(data[i]['guantes_banda']);
                        	key.push(data[i]['guantes_latex']);//Nuevo
                        	key.push(data[i]['guantes_jebe_industrial']);
                        	key.push(data[i]['guantes_multiflex']);
                        	key.push(data[i]['casco']);
                        	key.push(data[i]['barbiquero']);
                        	key.push(data[i]['zapato']);
                        	key.push(data[i]['zapato_talla']);
                        	key.push(data[i]['uniforme']);
                        	key.push(data[i]['pantalon_talla']);
                        	key.push(data[i]['camisa_talla']);
                        	key.push(data[i]['chompa']);
                        	key.push(data[i]['chompa_talla']);
                        	key.push(data[i]['bloqueador_solar']);
                        	key.push(data[i]['botin']);
                        	key.push(data[i]['botin_talla']);
                        	key.push(data[i]['bota']);//Nuevo
                            key.push(data[i]['bota_talla']);//Nuevo
                            key.push(data[i]['casco_moto']);//Nuevo
                            key.push(data[i]['rodillera_codera']);//Nuevo
                            key.push(data[i]['guardapolvo']);//Nuevo
                            key.push(data[i]['talla_guardapolvo']);//Nuevo
                            key.push(data[i]['cofia']);//Nuevo
                            key.push(data[i]['mandil_plomado']);//Nuevo
                        	reporte.push(key);
                        }
                        };
                    
                        $(".app-container").addClass('closed-sidebar');
                        jexcel_programa_de_estudio(reporte);
                        vertical_notas();
                        $("#btn_guardar").prop('disabled',false);
                  }
          });
        
    }
    
    var g_jexcel=[];
    function jexcel_programa_de_estudio(data=['','','','','']){
        $("#spreadsheet").html('');
    g_jexcel = [];
    g_jexcel = jexcel(document.getElementById('spreadsheet'), {
        data:data,
        columns: [
            {
                readOnly:true,
                type: 'text',
                title:'id_epps',
                width:0.1
            },
            {
                readOnly:true,
                type: 'text',
                title:'APELLIDOS Y NOMBRES',
                width:220
            },
            {
                type: 'dropdown',
                title:'SEXO',
                width:50,
                source:["H","M"]
            },
            {
                type: 'dropdown',
                title:'LENTES',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'MASCARILLA',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'FILTROS DE CARBON ACTIVADO',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'GUANTE DE JEBE DE USO INDUSTRIAL CALIBRE 25',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'GUANTE DE BADANA',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'GUANTE DE LATEX',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'GUANTE DE JEBE DE USO INDUSTRIAL CALIBRE 10 CAÑA LARGA',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'GUANTE MULTIFLEX',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'CASCO PROTECTOR DE PLASTICO CON OREJERAS',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'BARBIQUEJO',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'ZAPATO DIELECTRICO',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'TALLA ZAPATO DIELETRICO',
                width:50,
                source:["34","36","38","39","40","41","42","43","44"]
            },
            {
                type: 'dropdown',
                title:'UNIFORME DRIL (PANTALON y CAMISA)',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'TALLA DE PANTALON DRIL',
                width:50,
                source:["28","30","32","34","36","38","39","40","42","44"]
            },
            {
                type: 'dropdown',
                title:'TALLA DE CAMISA DRIL',
                width:50,
                source:["S","M","L","XL","XXL"]
            },
            {
                type: 'dropdown',
                title:'CHOMPA',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'TALLA DE CHOMPA',
                width:50,
                source:["S","M","L","XL","XXL"]
            },
            {
                type: 'dropdown',
                title:'BLOQUEADOR SOLAR',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'BOTIN DE CUERO CON PUNTA DE ACERO UNISEX',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'TALLA BOTIN DE CUERO',
                width:80,
                source:["34","36","38","39","40","41","42","43","44"]
            },
            //Nuevo
            {
                type: 'dropdown',
                title:'BOTA DE JEBE CON PUNTA DE ACERO',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'TALLA DE BOTA DE JEBE',
                width:80,
                source:["34","36","38","39","40","41","42","43","44"]
            },
            {
                type: 'dropdown',
                title:'CASCO PARA MOTO',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'RODILLERAS Y CODERAS',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'GUARDAPOLVO',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'TALLA GUARDAPOLVO',
                width:50,
                source:["S","M","L","XL","XXL"]
            },
            {
                type: 'dropdown',
                title:'COFIA',
                width:50,
                source:["SI","NO"]
            },
            {
                type: 'dropdown',
                title:'MANDIL PLOMADO',
                width:50,
                source:["SI","NO"]
            },
            
         ]
    });
    
    }
    
    /*
            {
                type: 'numeric',
                title:'TALLA ZAPATO DIELETRICO',
                mask:'#',
                width:50,
            },
    */
    
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
        $(".app-container").addClass('closed-sidebar');
</script>

@endsection