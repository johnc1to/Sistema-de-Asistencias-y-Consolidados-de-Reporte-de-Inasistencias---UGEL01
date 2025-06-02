@extends('layout_especialista.cuerposiap')
@section('html')

<link href="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.22.0/dist/extensions/page-jump-to/bootstrap-table-page-jump-to.min.css" rel="stylesheet">



<script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table-locale-all.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.22.0/dist/extensions/mobile/bootstrap-table-mobile.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.22.0/dist/extensions/page-jump-to/bootstrap-table-page-jump-to.min.js"></script>

<div class="container-fluid">
    <div class="col-md-8">
        <form class="d-flex" action="{{ route('getReporte') }}" id="consultaTitulados">
            <div class="col-md-6">
                <label for="periodo">Seleccione un periodo</label>
                <select name="periodo" id="periodo" class="form-control" >
                    <?php
                    for ($i=date('Y'); $i>=2020; $i--) {
                    ?> <option><?=$i?>-I</option> <option><?=$i?>-II</option> <?php
                    }
                    ?>
                </select>

            </div>
            <div class="col-md-6">
                @csrf
                <label for="codmod">Seleccione un opción</label>
                <select class="form-control" id="codmod" name="codmod">
                    <option value="1">Matrículados</option>
                    <option value="2">Con Certificación</option>
                    <option value="3">Con Título</option>
                    <option value="4">Egresados</option>
                </select>
            </div>


        </form>
    </div>
    <a class="btn btn-info btn-md-2" id="excelExport" href="{{ $ruta }}">Descargar Excel</a>
    <div class="table table-responsive-md table-condensed">
        <table id="table"
        data-locale="es-PE"
        data-toggle="table"
        data-show-jump-to="true"
        data-url="{{ route('getReporte') }}"
        data-pagination="true"
        data-side-pagination="server"
        {{--  data-page-list="[5, 10, 20, 50, 100, 200]"  --}}
        data-search="true"
        data-searchable="true"
        data-height="750"
        data-method="post"
        data-show-toggle="true"
        data-icons-prefix="fa"
        data-icons="icons"
        data-mobile-responsive="true"
        data-query-params="queryParams"

        >
        <thead>
            <tr>
                <th colspan="5" class="text-center">Datos del Estudiante</th>
                <th colspan="6" class="text-center">Programa de Estudio</th>

            </tr>
        <tr>
          <th data-field="apePatAlu"  data-formatter="nombreCompleto">Nombre Estudiante</th>
          <th data-field="perPee">Periodo</th>
          <th data-field="tipDocAlu">Tip. Doc.</th>
          <th data-field="docAlu">N° Doc.</th>
          <th data-field="sexAlu">Sexo<br>(F/M)</th>
          <th data-field="fecNacAlu">Fecha Nac.</th>

          <th data-field="codMat">Cód. Matrícula</th>
          <th data-field="fecMat">fec. Matrícula</th>
          <th data-field="proEstPee">Prog. Estudio</th>
          <th data-field="tipSerEduPee">tip. Servicio</th>
          <th data-field="credPee">N° de créditos</th>
          <th data-field="horPee">N° de Horas</th>

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

        function nombreCompleto(e,row)
        {
            if(row.idTit!=null)
            {
                return `<a href="#" href="#" data-bs-toggle="modal" data-bs-target="#seguimientoEgresado"
                        data-bs-idtit="${row.idTit}" data-bs-idalu="${row.idAlu}" data-bs-idpro="${row.idPro}">${row.apePatAlu} ${row.apeMatAlu} ${row.nomAlu}</a>`
            }
          return row.apePatAlu+" "+row.apeMatAlu+" "+row.nomAlu
        }

        function queryParams(params) {
            params.codmod = $("#codmod").val()
            params.periodo = $("#periodo").val()
           // alert('queryParams: ' + JSON.stringify(params))
            return params
          }

          $(function() {
            $('#codmod').change(function () {
              var queryParamsType = $(this).val()
                if($(this).val()==1)
                {
                    $("#excelExport").attr("href","{{ route('exportExcel') }}")
                }
                if($(this).val()==2)
                {
                    $("#excelExport").attr("href","{{ route('exportsCertificados') }}")
                }
                if($(this).val()==3)
                {
                    $("#excelExport").attr("href","{{ route('exportsTitulados') }}")
                }
              $table.bootstrapTable('refreshOptions', {
                queryParamsType: queryParamsType
              })
            })

            $('#periodo').change(function () {
                var queryParamsType = $(this).val()
                  if($(this).val()==1)
                  {
                      $("#excelExport").attr("href","{{ route('exportExcel') }}")
                  }
                  if($(this).val()==2)
                  {
                      $("#excelExport").attr("href","{{ route('exportsCertificados') }}")
                  }
                  if($(this).val()==3)
                  {
                      $("#excelExport").attr("href","{{ route('exportsTitulados') }}")
                  }
                $table.bootstrapTable('refreshOptions', {
                  queryParamsType: queryParamsType
                })
              })
          })
    </script>
@endsection




