{{-- @php
    echo "<pre>";
    print_r($dato);
    die();
@endphp --}}
@extends('layout_especialista/cuerpo')
@section('html')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<h2>DESPLAZAMIENTO DE BIENES PATRIMONIALES</h2>
<input type="hidden" id="argumentos" value="" />
<div id="addComponent"></div>
@csrf
<table class="table table-bordered table-condensed" id="table">
    <thead>
        <tr>
            <th>N°</th>
            <th>N° Solicitud</th>
            <th>N° Movimiento</th>
            <th>Movimiento</th>
            <th>Transferente</th>
            <th>Receptor</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @isset($dato)
            @foreach ($dato as $key => $item)
            <tr>
                <td>{{$key +1}}</td>
                <td>{{$item->correlativo}}</td>
                <td>{{$item->movimiento}}</td>
                <td>{{$item->movimiento1->descripcion}}</td>
                <td>{{$item->transferente}}</td>
                <td>{{$item->receptor}}</td>
                <td>
                    @if ($item->flg != 0)
                        {{-- @if ($item->numero_firmas == 0) --}}
                        <a href='https://siguhs.ugel01.gob.pe/ver-pdf-patrimonio/{{$userSiguhs->NomUsu}}/{{$item->correlativo}}' class='btn bg-info btn-sm m-2'  target="_blank"><i class='fas fa-eye'></i></a>
                         {{-- @else
                        <a href='https://mantenimiento.test/movimiento_patrimonio/firmados/{{$item->correlativo}}_{{$item->numero_firmas}}.pdf' class='btn bg-info btn-sm m-2'  target="_blank"><i class='fas fa-eye'></i></a>

                        @endif --}}

                        @if ($item->firmas == null )
                            <a class="btn btn-success" href="#" role="button" id="firmarDoc" data-id='{{$item->correlativo}}' onclick="initInvoker('W');"><i class="fas fa-signature"></i>Firmar</a>
                        @else
                        {{-- $userSiguhs->CodPer --}}
                            @if ($item->id_movimiento == 4 and ($item->id_persona_transferente == $userSiguhs->CodPer) and $item->numero_firmas == 3)
                            <a class="btn btn-warning" href="#" role="button" id="firmarDoc" data-id='{{$item->correlativo}}' onclick="initInvoker('W');"><i class="fas fa-signature"></i>Firmar Retorno</a>

                            @elseif ($item->id_movimiento == 4 and $item->id_persona_transferente == $userSiguhs->CodPer and $item->id_persona_receptor == $userSiguhs->CodPer and $item->numero_firmas == 2)
                            <a class="btn btn-success" href="#" role="button" id="firmarDoc" data-id='{{$item->correlativo}}' onclick="initInvoker('W');"><i class="fas fa-signature"></i>Firmar</a>
                            @endif
                        @endif
                    @endif

                </td>
            </tr>
            @endforeach
        @endisset
    </tbody>

</table>
<script>

    var  postArgumentos="{{route('refirma')}}";

</script>
<script type="text/javascript" src="https://dsp.reniec.gob.pe/refirma_invoker/resources/js/clientclickonce.js"></script>
<script>
const table = $("#table").DataTable({
    "language":{
                "url": "/public/datatable.espaniol.json"
            },
             order: [ 1, 'DESC' ],
});

table.on('order.dt search.dt', function () {
    table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
    // table.cell(cell).invalidate('dom');
    cell.innerHTML = i+1;

    });

    }).draw();



var iddoc=null;
 $("#table").on("click","tr td a#firmarDoc",function(){
    iddoc=$(this).data("id");
 });
 var documentName_ = null;
 //
 window.addEventListener('getArguments', function (e) {

     type = e.detail;
     if(type === 'W'){
         ObtieneArgumentosParaFirmaDesdeLaWeb(); // Llama a getArguments al terminar.
     }else if(type === 'L'){
         ObtieneArgumentosParaFirmaDesdeArchivoLocal(); // Llama a getArguments al terminar.
     }
 });
 function getArguments(){
    console.log("envio de argumentos")
     arg = document.getElementById("argumentos").value;
     dispatchEventClient('sendArguments', arg);
 }

 window.addEventListener('invokerOk', function (e) {
     type = e.detail;
     if(type === 'W'){
         MiFuncionOkWeb();
     }else if(type === 'L'){
         MiFuncionOkLocal();
     }
 });

 window.addEventListener('invokerCancel', function (e) {
     MiFuncionCancel();
 });

 //::LÓGICA DEL PROGRAMADOR::
 function ObtieneArgumentosParaFirmaDesdeLaWeb(){
    console.log("ss");
    //  let hr=document.getElementById("signedDocument");
    //  hr.href="#";
    $("#signedDocument").attr("href","#");

         $.post(postArgumentos, {
             type : "W",
             id:iddoc,
             "_token":$('[name=_token]').val()
         }, function(data, status) {
             //alert("Data: " + data + "\nStatus: " + status);
             console.log(data);
             document.getElementById("argumentos").value = data;
             getArguments();
         });


 }

 function ObtieneArgumentosParaFirmaDesdeArchivoLocal(){
     document.getElementById("signedDocument").href="#";
     $.get(getArgumentos, {}, function(data, status) {
         documentName_ = data;
         //Obtiene argumentos
         $.post(postArgumentos, {
             type : "L",
             documentName : documentName_
         }, function(data, status) {
             //alert("Data: " + data + "\nStatus: " + status);
             document.getElementById("argumentos").value = data;
             getArguments();
         });

     });

 }

 function MiFuncionOkWeb(){
    alert("Documento firmado desde una URL correctamente.");
    $.post("/public/update-firmas", {

        id:iddoc,
        "_token":$('[name=_token]').val()
    }, function(data, status) {
        //alert("Data: " + data + "\nStatus: " + status);
         
        location.reload();

    });






    }

 function MiFuncionOkLocal(){
     alert("Documento firmado desde la PC correctamente.");
     document.getElementById("signedDocument").href="controller/getFile.php?documentName=" + documentName_;
 }

 function MiFuncionCancel(){
     alert("El proceso de firma digital fue cancelado.");
     $("#signedDocument").attr("href","#");
     //document.getElementById("signedDocument").href="#";
 }

</script>
@endsection

