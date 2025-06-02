<form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="if(confirm(msj)){guardar_soporte();}return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>CREAR ACCESO DOCENTE</b></h4>      
    </div>
    <div class="modal-body">

    <div id="testmodal" style="padding: 5px 20px;">

    <input name="idTip" value="3" type="hidden">
    <input name="codmodSop" value="" type="hidden">
    <input name="id_contactoSop" value="" type="hidden">
    <input name="cueSop" value="" type="hidden">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> <input type="radio" name="docbox" checked> <b>Registro unico</b> &nbsp;&nbsp;&nbsp; <input type="radio" name="docbox" onclick="masivocrearaccesodocente();"> <b>Registro masivo</b> <br><br> </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Docente:</b></div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">            
            <input list="listdocente" autocomplete="off" name="docente" type="text" class="form-control" onclick="this.value=''" placeholder="Elige al docente"  onchange="completardocente(this.value);">
            <datalist id="listdocente">
                <?php
                $dnexus = array();
                for ($i=0; $i < count($nexus); $i++) {
                $key = $nexus[$i];
                $id = $key['nombres'].' '.$key['apellipat'].' '.$key['apellimat'].' - '.$key['numdocum'];
                $dnexus[$id] = $key;
                ?>
                <option><?=$id?></option>
                <?php
                }
                ?>
            </datalist>
            <?php
            /*
            ?>
            <select name="docente" class="form-control" onchange="completardocente(this.value);">
                <option value="">Ninguno</option>
                <?php
                for ($i=0; $i < count($nexus); $i++) { 
                $key = $nexus[$i];
                ?>
                <option value="<?=$i?>"><?=$key['nombres']?> <?=$key['apellipat']?> <?=$key['apellimat']?> - <?=$key['numdocum']?></option>
                <?php
                }
                ?>
            </select>
            <?php
            */
            ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Tipo de Documento:</b></div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
            <select name="tipDocSop" class="inputdatos form-control" required>
                <option>DNI</option>
                <option>CARNET DE EXTRANJERIA</option>
                <option>PASAPORTE</option>
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Numero de Documento:</b></div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="dniSop" class="inputdatos form-control" required></div>
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
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Correo Personal:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="corSop" class="form-control" required></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Telefono:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="telSop" class="form-control" required></div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:justify;color:red;"><br><b id="msj"></b></div>
        </div>
        
        <!--
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Observación:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="obsSop" class="inputdatos form-control" required></div>
        </div>
        -->
      </div>       	
    </div>
    @csrf
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_guardaralumno" style="">Guardar</button>
      <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>
    </div>
</form>

<script>
    var nexus = <?=json_encode($dnexus)?>;
    function completardocente(nro){
    if(nro){
        $('input[name=cueSop]')   .val('d'+nexus[nro]['numdocum']+'o');
        $('input[name=dniSop]')   .val(nexus[nro]['numdocum']);
        $('input[name=nomSop]')   .val(nexus[nro]['nombres']);
        $('input[name=apepatSop]').val(nexus[nro]['apellipat']);
        $('input[name=apematSop]').val(nexus[nro]['apellimat']);
        $('input[name=corSop]')   .val('');
        $('input[name=telSop]')   .val('');
        $(".inputdatos").prop('readonly',true);
    }else{
        $(".inputdatos")          .prop('readonly',false);
        $(".inputdatos")          .val('');
        $('input[name=corSop]')   .val('');
        $('input[name=telSop]')   .val('');
    }
    }
    
    $("#msj").html('Nota: '+msj);
</script>