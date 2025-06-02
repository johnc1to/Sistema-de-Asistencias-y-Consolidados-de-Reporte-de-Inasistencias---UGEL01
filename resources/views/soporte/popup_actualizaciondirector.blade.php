<form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="guardar_soporte();return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>ACTUALIZACION DE DIRECTOR</b></h4>      
    </div>
    <div class="modal-body">

    <div id="testmodal" style="padding: 5px 20px;">
    <input name="idTip" value="7" type="hidden">
    <input name="codmodSop" value="" type="hidden">
    <input name="id_contactoSop" value="" type="hidden">

    <input name="cueSop" value="" type="hidden">
    <input name="dniSop" value="" type="hidden">
    <input name="nomSop" value="" type="hidden">
    <input name="apepatSop" value="" type="hidden">
    <input name="apematSop" value="" type="hidden">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><b>DATOS DEL NUEVO DIRECTOR:</b></div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Director(a):</b></div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b id="nomcompleto"></b></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Correo Personal:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="corSop" class="inputdatos form-control" required></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Telefono:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="telSop" class="inputdatos form-control" required></div>
        </div>
        <!--
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Observación:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="obsSop" class="inputdatos form-control" required></div>
        </div>
        -->
        
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><br><b>DATOS DEL ANTIGUO DIRECTOR:</b></div>
        </div>
        <!--inicio jmmj -->
          <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Tipo de Documento:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select name="tipDocAntSop" class="inputdatos form-control" required>
                    <option></option>
                    <option>DNI</option>
                    <option>PASAPORTE</option>
                    <option>CARNET EXTRANJERIA</option>
                   
                </select>
            </div>
        </div>
        <!--fin jmmj -->
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Nro. Documento:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="dniAntSop" class="inputdatos form-control" required></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Nombres:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="nomAntSop" class="inputdatos form-control" required></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Apellido Paterno:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="apepatAntSop" class="inputdatos form-control" required></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Apellido Materno:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8"><input type="text" autocomplete="off" value="" name="apematAntSop" class="inputdatos form-control" required></div>
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"><b>Motivo:</b></div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                <select name="obsSop" class="inputdatos form-control" required>
                    <option></option>
                    <option>CAMBIO DE CODMOD</option>
                    <option>ES DOCENTE DEL MISMO CODMOD</option>
                    <option>ES DOCENTE DE OTRO CODMOD</option>
                    <option>CESE</option>
                    <option>POR SANCION</option>
                </select>
            </div>
        </div>

      </div>       	
    </div>
    @csrf
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_guardaralumno" style="">Guardar</button>
      <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>
    </div>
</form>