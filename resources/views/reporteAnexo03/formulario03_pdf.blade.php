@php
    use Carbon\Carbon;
    $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;
    $diasSemana = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];
    $feriados = ['2025-05-01'];
    $fechaHoy = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia Detallado</title>
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
            <h1 style="font-weight: bold; margin: 0;">ANEXO 03</h1>
            <h1 style="font-weight: bold; margin: 0;">FORMATO 01: REPORTE DE ASISTENCIA DETALLADO</h1>
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
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th rowspan="2">Nº</th>
            <th rowspan="2">DNI</th>
            <th rowspan="2" style="text-align: left;">Apellidos y Nombres</th>
            <th rowspan="2">Cargo</th>
            <th rowspan="2">Condición</th>
            <th rowspan="2">Jor. Lab.</th>
            @for ($d = 1; $d <= $diasEnMes; $d++)
                <th>{{ $d }}</th>
            @endfor
        </tr>
        <tr>
            @for ($d = 1; $d <= $diasEnMes; $d++)
                @php
                    $fecha = Carbon::create($anio, $mes, $d);
                    $nombreDia = $diasSemana[$fecha->dayOfWeek];
                @endphp
                <th class="nowrap">{{ $nombreDia }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach ($registros as $index => $r)
            @php
                $dni = $r->dni;
                $asistencia = $asistencias[$dni]['asistencia'] ?? [];
                $observacion = $asistencias[$dni]['observacion'] ?? null;

                // Verificamos si toda la asistencia está vacía (solo blancos o vacíos)
                $asistenciaVacia = collect($asistencia)->filter(function($val) {
                    return trim($val) !== '';
                })->isEmpty();
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $dni }}</td>
                <td style="text-align: left;">{{ $r->nombres }}</td>
                <td>{{ $r->cargo }}</td>
                <td>{{ $r->condicion }}</td>
                <td>{{ $r->jornada }}</td>

                @if ($asistenciaVacia)
                    {{-- Mostrar observación centrada si no hay asistencia --}}
                    <td colspan="{{ $diasEnMes }}" style="text-align: center; font-style: italic;">

                        {{ $observacion }}
                    </td>
                @else
                    {{-- Mostrar la asistencia día por día --}}
                    @php
                        $d = 1;
                    @endphp

                    @while ($d <= $diasEnMes)
                        @php
                            $valor = $asistencia[$d - 1] ?? '';

                            // Detectar inicio de bloque "L"
                            if ($valor === 'L') {
                                $inicio = $d;
                                $fin = $d;

                                // Buscar el rango continuo de 'L'
                                for ($j = $d + 1; $j <= $diasEnMes; $j++) {
                                    $valorSiguiente = $asistencia[$j - 1] ?? '';
                                    if ($valorSiguiente === 'L' || $valorSiguiente === null || trim($valorSiguiente) === '') {
                                        $fin = $j;
                                    } else {
                                        break;
                                    }
                                }
                                $colspan = $fin - $inicio + 1;
                        @endphp
                            <td colspan="{{ $colspan }}" style="text-align: center; font-style: italic;">
                        {{ $observacion ?? 'Licencia' }}
                        </td>
                            @php
                                $d = $fin + 1;
                                continue;
                            @endphp
                            @php
                                } else {
                            @endphp
                                <td style="text-align: center;">{{ $valor }}</td>
                            @php
                                $d++;
                                }
                            @endphp
                    @endwhile
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
<!-- Firma, Lugar/Fecha y Leyenda en la misma línea -->
<table width="100%" style="margin-top: 30px; font-size: 13px; border: none; border-collapse: collapse;">
    <tr>
        <!-- Leyenda -->
        <td style="width: 33%; text-align: left; vertical-align: top; border: none;">
            <div style="font-size: 12px; padding: 6px 10px; border-radius: 6px; text-align: left;">
                <p style="margin: 0 0 4px; font-weight: bold;">Leyenda:</p>
                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                    <div><strong>A:</strong> Día laborado</div>
                    <div><strong>I:</strong> Inasistencia injustificada</div>
                    <div><strong>J:</strong> Inasistencia justificada</div>
                    <div><strong>L:</strong> Licencia sin goce</div>
                    <div><strong>P:</strong> Permiso sin goce</div>
                    <div><strong>T:</strong> Tardanza</div>
                    <div><strong>F:</strong> Feriado</div>
                    <div><strong>H:</strong> Huelga o paro</div>
                </div>
            </div>
        </td>
        <!-- Firma -->
        <td style="width: 33%; text-align: center; vertical-align: top; border: none; padding-bottom: 0;">
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

        <!-- Lugar y Fecha -->
        <td style="width: 33%; text-align: right; vertical-align: bottom; border: none;">
            <p style="margin: 0;">Lugar y Fecha: {{ $fechaHoy }}</p>
        </td>
  
    </tr>
</table>

</body>
</html>
