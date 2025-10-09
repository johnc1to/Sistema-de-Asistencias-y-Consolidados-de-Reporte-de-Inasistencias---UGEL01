@php
    use Carbon\Carbon;
    $fechaHoy = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Consolidado Detallado - Preliminar</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 20px; }
        h1 { text-align: center; margin: 0; padding: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 2px; text-align: center; }
        .header-left { text-align: left; margin-top: 10px; margin-bottom: 10px; }
        tbody tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
    </style>
</head>
    <body>
        <div style="margin-right: 0px;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 1em;" border="0">
                <tr>
                    <td style="width: 80px; vertical-align: top; border: none;">
                        @if(!empty($logo))
                            <img src="{{ asset($logo) }}" alt="Logo IE" style="height: 70px;">
                        @else
                            <div style="font-size: 12px; color: #888;">No se encontró logo</div>
                        @endif
                    </td>
                    <td style="text-align: center; border: none;">
                        <h1 style="font-weight: bold; margin: 0;">ANEXO 04 - PUBLICACIÓN PRELIMINAR PARA JUSTIFICACIÓN</h1>
                        <h1 style="font-weight: bold; margin: 0;">FORMATO 02: REPORTE DE CONSOLIDADO DE INASISTENCIAS, TARDANZAS Y PERMISOS SIN GOCE  DE REMUNERACIONES</h1>
                    </td>
                </tr>
            </table>

            <table width="100%" style="font-size: 13px; margin-top: 10px; margin-bottom: 10px; border: none;">
                <tr>
                    <td style="text-align: left; vertical-align: top; border: none;">
                        <p style="margin: 2px 0;"><strong>{{ $registros->first()->ugel ?? 'UGEL 01 SAN JUAN DE MIRAFLORES' }}</strong></p>
                        <p style="margin: 2px 0;"><strong>I.E.:</strong> {{ $institucion }}</p>
                        <p style="margin: 2px 0;"><strong>Nivel / Modalidad Educativa:</strong> {{ $nivelSeleccionado }} {{ $modalidad }}</p>
                    </td>
                    <td style="text-align: left; vertical-align: top; width: 40%; border: none;">
                        <p style="margin: 2px 0;"><strong>PERIODO:</strong> {{ strtoupper(Carbon::create($anio, $mes, 1)->translatedFormat('F Y')) }}</p>
                        <p style="margin: 2px 0;"><strong>Turno:</strong> {{ $d_cod_tur }}</p>
                    </td>
                </tr>
            </table>

            <table style="margin-left: auto; margin-right: 0; width: 100%;">
                <thead>
                    <tr>
                        <th rowspan="2">Nº</th>
                        <th rowspan="2">Apellidos y Nombres</th>
                        <th rowspan="2">Cargo</th>
                        <th rowspan="2">Condición</th>
                        <th rowspan="2">Jor. Lab.</th>
                        <th colspan="2">Inasistencias</th>
                        <th colspan="3">Tardanzas</th>  
                        @php
                            // Revisar si hay al menos un registro con permisos SG
                            $mostrarPermisos = collect($registros)->contains(function($r) use ($datos_inasistencias) {
                                $dni = $r->dni;
                                $datos = $datos_inasistencias[$dni] ?? [];
                                return !empty($datos['permiso_sg_total']['horas']) || !empty($datos['permiso_sg_total']['minutos']) || !empty($datos['permiso_sg_fechas']);
                            });

                            // Revisar si hay al menos un registro con huelga/paro
                            $mostrarHuelga = collect($registros)->contains(function($r) use ($datos_inasistencias) {
                                $dni = $r->dni;
                                $datos = $datos_inasistencias[$dni] ?? [];
                                return !empty($datos['huelga_total']) || !empty($datos['huelga_fechas']);
                            });
                        @endphp

                        @if($mostrarPermisos)
                            <th colspan="3">Permisos SG</th>
                        @endif
                        @if($mostrarHuelga)
                            <th colspan="2">Huelga / Paro</th>
                        @endif
                    </tr>
                    <tr>
                        <th>Total</th>
                        <th>Días</th>
                        <th>Horas</th>
                        <th>Minutos</th>
                        <th>Días/HM</th>
                        @if($mostrarPermisos)
                            <th>Horas</th>
                            <th>Minutos</th>
                            <th>Días/HM</th>
                        @endif
                        @if($mostrarHuelga)
                            <th>Total</th>
                            <th>Días</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $contador = 1; @endphp
                    @foreach ($registros as $r)
                        @php
                            $dni = $r->dni;
                            $cod = $r->cod ?? null;
                            $clave = "{$dni}_{$cod}";
                            $datos = $datos_inasistencias[$clave] ?? [];

                            $inasistencias_txt = !empty($datos['inasistencia_fechas'])
                                ? implode(', ', array_map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'), $datos['inasistencia_fechas']))
                                : '';

                            $tardanzas_txt = !empty($datos['tardanza_fechas'])
                                ? implode(', ', array_map(fn($t) => \Carbon\Carbon::parse($t['fecha'])->format('d/m') . " ({$t['horas']}h{$t['minutos']}m)", $datos['tardanza_fechas']))
                                : '';

                            $permisos_txt = !empty($datos['permiso_sg_fechas'])
                                ? implode(', ', array_map(fn($p) => \Carbon\Carbon::parse($p['fecha'])->format('d/m') . " ({$p['horas']}h{$p['minutos']}m)", $datos['permiso_sg_fechas']))
                                : '';

                            $huelgas_txt = !empty($datos['huelga_fechas'])
                                ? implode(', ', array_map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'), $datos['huelga_fechas']))
                                : '';
                        @endphp

                        <tr>
                            <td>{{ $contador }}</td>
                            <td>{{ $r->nombres }}</td>
                            <td>{{ $r->cargo }}</td>
                            <td>{{ $r->condicion }}</td>
                            <td>{{ $r->jornada }}</td>

                            {{-- Inasistencias --}}
                            <td>{{ $datos['inasistencia_total'] ?? 0 }}</td>
                            <td>{{ $inasistencias_txt }}</td>

                            {{-- Tardanzas --}}
                            <td>{{ $datos['tardanza_total']['horas'] ?? 0 }}</td>
                            <td>{{ $datos['tardanza_total']['minutos'] ?? 0 }}</td>
                            <td>{{ $tardanzas_txt }}</td>

                            {{-- Permisos SG --}}
                            @if($mostrarPermisos)
                                <td>{{ $datos['permiso_sg_total']['horas'] ?? 0 }}</td>
                                <td>{{ $datos['permiso_sg_total']['minutos'] ?? 0 }}</td>
                                <td>{{ $permisos_txt }}</td>
                            @endif

                            {{-- Huelga --}}
                            @if($mostrarHuelga)
                                <td>{{ $datos['huelga_total'] ?? 0 }}</td>
                                <td>{{ $huelgas_txt }}</td>
                            @endif
                        </tr>

                        @php $contador++; @endphp
                    @endforeach
                </tbody>
            </table>

            {{-- Firma y fecha --}}
            <table width="100%" style="margin-top: 30px; font-size: 13px; border: none;">
                <tr>
                    <td style="text-align: left; vertical-align: bottom; border: none;">
                        <div style="font-size: 12px; padding: 6px 10px; border-radius: 6px;">
                            <strong>(*)</strong> Hora y minuto cronológico
                        </div>
                    </td>
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
                    <td style="text-align: right; vertical-align: bottom; border: none;">
                        <p style="margin: 0;">Lugar y Fecha: {{ $fechaHoy }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
