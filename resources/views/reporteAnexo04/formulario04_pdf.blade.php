@php
    use Carbon\Carbon;
    $fechaHoy = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inasistencia Detallado</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 20px; }
        h1 { text-align: center; margin: 0; padding: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 2px; text-align: center; }
        .header-left { text-align: left; margin-top: 10px; margin-bottom: 10px; }
        .page-break { page-break-after: always; }
        .nowrap { white-space: nowrap; }
        tbody tr {
            page-break-inside: avoid; /* Evita que una fila se divida en dos páginas */
        }
        /* Opcional para evitar que la tabla se corte mal */
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }

    </style>
</head>
<body>
<div style="margin-right: 50px;"> <!-- Aumentamos margen para texto vertical -->


<table style="width: 100%; border-collapse: collapse; margin-bottom: 1em;" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <!-- Logo a la izquierda -->
        <td style="width: 80px; vertical-align: top; border: none;">
            @if(!empty($logo))
                    <img src="{{ asset($logo) }}" alt="Logo IE" style="height: 70px;">
                @else
                    <div style="font-size: 12px; color: #888;">No se encontró logo</div>
            @endif
        </td>

        <!-- Textos centrados al lado derecho -->
        <td style="text-align: center; border: none;">
            <h1 style="font-weight: bold; margin: 0;">ANEXO 04</h1>
            <h1 style="font-weight: bold; margin: 0;">FORMATO 02: REPORTE DE CONSOLIDADO DE INASISTENCIAS, TARDANZAS Y PERMISOS SIN GOCE  DE REMUNERACIONES</h1>
        </td>
    </tr>
</table>

<table width="100%" style="font-size: 13px; margin-top: 10px; margin-bottom: 10px; border-collapse: collapse; border: none;">
    <tr>
        {{-- Columna izquierda --}}
        <td style="text-align: left; vertical-align: top; border: none;">
            <p style="margin: 2px 0;"><strong>{{ $registros->first()->ugel ?? 'N/A' }}</strong></p>
            <p style="margin: 2px 0;"><strong>I.E.:</strong> {{ $institucion }}</p>
            <p style="margin: 2px 0;"><strong>Nivel / Modalidad Educativa:</strong> {{ $nivelSeleccionado }} {{ $modalidad }}</p>
        </td>

        {{-- Columna derecha --}}
        <td style="text-align: left; vertical-align: top; width: 40%; border: none;">
            <p style="margin: 2px 0;"><strong>PERIODO:</strong> {{ strtoupper(Carbon::create($anio, $mes, 1)->translatedFormat('F Y')) }}</p>
            <p style="margin: 2px 0;"><strong>Turno:</strong> {{ $d_cod_tur }}</p>
            <p><strong>Total registros:</strong> {{ count($registros) }}</p>
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th rowspan="2">Nº</th>
            <th rowspan="2">DNI</th>
            <th rowspan="2" style="text-align: center;">Apellidos y Nombres</th>
            <th rowspan="2">Cargo</th>
            <th rowspan="2">Condición</th>
            <th rowspan="2">Jor. Lab.</th>
            <th class="border px-2 py-0 bg-gray-200 text-center" rowspan="2">
                        <div class="flex flex-col h-full">
                            <div class="border-b border-white py-1">Inasistencias</div>
                            <div class="py-1 text-xs font-semibold">Días</div>
                        </div>
                    </th>

                            <th class="border px-2 py-1 bg-gray-200 text-center" colspan="2">
                                Tardanzas<br>
                            </th>

                            <th class="border px-2 py-1 bg-gray-200 text-center" colspan="2">
                                Permisos SG<br>
                            </th>

                            <th class="border px-2 py-0 bg-gray-200 text-center" rowspan="2">
                        <div class="flex flex-col h-full">
                            <div class="border-b border-white py-1">Huelga / Paro</div>
                            <div class="py-1 text-xs font-semibold">Días</div>
                        </div>
                    </th>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">Observaciones</th>
                        </tr>
                        <tr>
                            <th class="border px-2 py-0 bg-gray-200 text-xs font-normal">
                                <div class="flex flex-col items-center justify-center h-full leading-tight">
                                    <div class="font-semibold">Horas</div>
                                    <div>(*)</div>
                                </div>
                            </th>
                            <th class="border px-2 py-0 bg-gray-200 text-xs font-normal">
                                <div class="flex flex-col items-center justify-center h-full leading-tight">
                                    <div class="font-semibold">Minutos</div>
                                    <div>(*)</div>
                                </div>
                            </th>
                            <th class="border px-2 py-0 bg-gray-200 text-xs font-normal">
                                <div class="flex flex-col items-center justify-center h-full leading-tight">
                                    <div class="font-semibold">Horas</div>
                                    <div>(*)</div>
                                </div>
                            </th>
                            <th class="border px-2 py-0 bg-gray-200 text-xs font-normal">
                                <div class="flex flex-col items-center justify-center h-full leading-tight">
                                    <div class="font-semibold">Minutos</div>
                                    <div>(*)</div>
                                </div>
                            </th>
                        </tr>
        </thead>
        <tbody>
        @foreach ($registros as $index => $r)
            @php
                $dni = $r->dni;
                $datos = $datos_inasistencias[$dni] ?? [];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $dni }}</td>
                <td style="text-align: left;">{{ $r->nombres }}</td>
                <td>{{ $r->cargo }}</td>
                <td>{{ $r->condicion }}</td>
                <td>{{ $r->jornada }}</td>

                {{-- Valores desde datos_inasistencias --}}
                <td class="border px-2 py-1 text-center">{{ $datos['inasistencia_total'] ?? '' }}</td>
                <td class="border px-2 py-1 text-center">{{ $datos['tardanza_total']['horas'] ?? '' }}</td>
                <td class="border px-2 py-1 text-center">{{ $datos['tardanza_total']['minutos'] ?? '' }}</td>
                <td class="border px-2 py-1 text-center">{{ $datos['permiso_sg_total']['horas'] ?? '' }}</td>
                <td class="border px-2 py-1 text-center">{{ $datos['permiso_sg_total']['minutos'] ?? '' }}</td>
                <td class="border px-2 py-1 text-center">{{ $datos['huelga_total'] ?? '' }}</td>
                <td class="border px-2 py-1">{{ e($r->observaciones ?? '') }}</td>
            </tr>
        @endforeach
    </tbody>

</table>

<!-- Firma, Lugar/Fecha y Hora/Minuto en la misma línea -->
<table width="100%" style="margin-top: 30px; font-size: 13px; border: none; border-collapse: collapse;">
    <tr>
        

        <!-- Celda de Hora y Minuto Cronológico -->
        <td style="text-align: left; vertical-align: bottom; border: none;">
            <div style="font-size: 12px;  padding: 6px 10px; border-radius: 6px;">
                <strong>(*)</strong> Hora y minuto cronológico
            </div>
        </td>
        <!-- Celda de Firma -->
        <td style="text-align: center; vertical-align: top; border: none; padding-bottom: 0;">
            @if(!empty($firmaGuardada) && file_exists(storage_path('app/public/firmasdirector/' . $firmaGuardada)))
                <img src="{{ storage_path('app/public/firmasdirector/' . $firmaGuardada) }}" 
                     alt="Firma Guardada" 
                     style="max-height: 63px; max-width: 200px; display: block; margin: 0 auto;">
            @elseif(!empty($firmaBase64))
                <img src="{{ $firmaBase64 }}" 
                     alt="Firma Temporal" 
                     style="max-height: 63px; max-width: 200px; display: block; margin: 0 auto;">
            @endif
        </td>
        
        <!-- Celda de Lugar y Fecha -->
        <td style="text-align: right; vertical-align: bottom; border: none;">
            <p style="margin: 0;">Lugar y Fecha: {{ $fechaHoy }}</p>
        </td>
    </tr>
</table>




</body>
</html>
