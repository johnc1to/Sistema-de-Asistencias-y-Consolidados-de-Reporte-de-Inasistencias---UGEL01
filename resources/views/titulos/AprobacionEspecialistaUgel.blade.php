@extends('layout_especialista.cuerposiap')
@section('html')

<link href="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css" rel="stylesheet">

<script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table-locale-all.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/export/bootstrap-table-export.min.js"></script>

<div class="container-fluid">
    @isset($colegio)
    <div class="col-md-12">
        <form class="d-flex" action="{{ route('titulados_cetpro') }}" id="consultaTitulados">
            <div class="col-md-6">
                @csrf
                <label for="codmod">CETPRO</label>
                <select class="form-control" id="codmod" name="codmod">
                    <option>Seleccione la IIEE</option>
                    @foreach ($colegio as $key => $item)
                        <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @endisset
    <div class="table-responsive-xl">

        <table id="table"
        data-locale="es-PE"
        data-toggle="table"
        data-url="{{ route('titulados_cetpro') }}"
        data-pagination="true"
        data-side-pagination="server"
        {{--  data-page-list="[5, 10, 20, 50, 100, 200]"  --}}
        data-search="true"
        data-searchable="true"
        data-height="600"
        data-method="post"
        data-query-params="queryParams"
        data-show-toggle="true"
        data-detail-formatter="detailFormatter"
        data-row-style="rowStyle"
        data-icons-prefix="fa"
        data-icons="icons"
        >
        <thead>
            <tr>
                <th colspan="2" class="text-center">Datos del Estudiante</th>
                <th colspan="5" class="text-center">Programa de Estudio</th>
                <th colspan="5" class="text-center">Información del Título</th>

            </tr>
        <tr>
          <th data-field="nombre">Nombre</th>
          <th data-field="docAlu">N° Doc.</th>

          <th data-field="perPee">Periodo</th>
          <th data-field="nivForPee">Nivel formativo</th>
          <th data-field="tipSerEduPee">Tipo de Servicio</th>
          <th data-field="credPee">Créditos</th>
          <th data-field="horPee">Horas</th>

          <th data-field="fecEgrTit">F. Egreso</th>
          <th data-field="rdExpTit">N° R.D.</th>
          <th data-field="codRegIeTit" data-formatter="actionFormatter" data-events="actionEvents">Cód. Reg. IIEE</th>
          <th data-field="codRegUgelTit">Cód. Reg. UGEL</th>
          <th data-field="situacion.comentario">Observación</th>
        </tr>
        </thead>
      </table>
        </div>



</div>
    <script>
        window.icons = {
            refresh: 'fa-refresh',
            toggleOn: 'fa-toggle-on',
            toggleOff: 'fa-toggle-off',
            columns: 'fa-th-list'
          }
        var $table = $('#table')

        window.actionEvents = {
            'click .ver_titulo': function (e) {
                let idTit = e.target.id
                $.ajax({
                    type: "post",
                    url: "{{ route('view_titulo') }}",
                    data:{"idTit":idTit},
                    datType:"html",
                    beforeSend: function()
                    {
                    },
                    error: function(){
                      alert("Hubo un error");
                    },
                    success: function(data){

                        $("#staticBackdrop .modal-body").html(data);
                        $("#exampleModalToggle2 .ver_titulo").attr("id",idTit)
                        $("#exampleModalToggle2 #form-id #idTit").val(idTit)
                    }
                  });
             // console.log(e.target.id)
            }
          }

          function rowStyle(row, index) {

            var classes = [
                'table-danger',
                'table-success',

                ]
            if(row.situacion!=null)
            {
                //console.log(row.situacion.situacion)
                if(row.situacion.situacion==3)
                {
                    return {
                        classes: classes[0]
                      }
                }
                if(row.situacion.situacion==2)
                {
                    return {
                        classes: classes[1]
                      }
                }
                if(row.situacion.situacion==4)
                {
                    return {
                        classes: classes[1]
                      }
                }
            }
            return {
                css: {
                  color: 'black'
                }
              }


          }

        function detailFormatter(index, row) {
            var html = []
            $.each(row, function (key, value) {
              html.push('<p><b>' + key + ':</b> ' + value + '</p>')
            })
            return html.join('')
          }

        /* function initTable() {
            console.log("hola")
          $table.bootstrapTable('destroy').bootstrapTable({
            height: 550,
            locale: $('#codmod').val()})

          }*/
        function actionFormatter(e,row)
        {
        // console.log(row)
            if(row.situacion!=null)
            {
                if(row.situacion.situacion==4)
                {
                    return e
                }
            }
            return `<a class='btn btn-outline-success ver_titulo'  id='${row.idTit}' href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop">${e}</a>`;
        }


          function queryParams(params) {
            params.codmod = $("#codmod").val()

           // alert('queryParams: ' + JSON.stringify(params))
            return params
          }

          $(function() {
            $('#codmod').change(function () {
              var queryParamsType = $(this).val()

              $table.bootstrapTable('refreshOptions', {
                queryParamsType: queryParamsType
              })
            })
          })

    </script>

    <script>
       /* let selectCodmod = document.getElementById("codmod")
        var traerTitulados = (e)=>
        {
            e.preventDefault()
            let action = e.target.form.action
            let data = $("#"+ e.target.form.id).serialize()
            console.log(data)
            $.ajax({
                headers: { 'X-CSRF-TOKEN':$('input[name="_token"]').val() },
                url: action,
                type: "GET",
                dataType: "JSON",
                data:data,
                beforeSend: function (xhr, opts) {
                    //ejecuta durante el envio al servidor
                    $("#staticBackdrop3 .modal-dialog .modal-content").append('<div class="overlay d-flex justify-content-center align-items-center jmmj4"><i class="fas fa-2x fa-sync fa-spin"></i></div>');
                },
                success: function (data) {
                    var aa=[];
                    aa.push(data.rows)
                    //initTable()
                    $('#table').bootstrapTable('load', data);

                }
            })
        }

        selectCodmod.addEventListener("change",traerTitulados,false)
*/
    </script>
@endsection




