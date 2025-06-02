<form id="formulario01" enctype="multipart/form-data" class="form-horizontal calender" method="post" role="form" onsubmit="guardar_soporte();return false;">          
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel" style="text-align:center;"><b>RESTABLECER ACCESO DIRECTOR</b></h4>      
    </div>
    <div class="modal-body">

    <div id="testmodal" style="padding: 5px 20px;">
    <input name="idTip" value="5" type="hidden">
    <input name="codmodSop" value="" type="hidden">
    <input name="id_contactoSop" value="" type="hidden">

    <input name="cueSop" value="" type="hidden">
    <input name="dniSop" value="" type="hidden">
    <input name="nomSop" value="" type="hidden">
    <input name="apepatSop" value="" type="hidden">
    <input name="apematSop" value="" type="hidden">

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

      </div>       	
    </div>
    @csrf
    <div class="modal-footer">
      <button class="btn btn-success" id="btn_guardaralumno" style="">Guardar</button>
      <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Cerrar</button>
    </div>
</form>