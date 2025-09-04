@extends('layout_director/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<!-- Intro.js CSS y JS -->
<link rel="stylesheet" href="https://unpkg.com/intro.js@4.2.2/minified/introjs.min.css">
<script src="https://unpkg.com/intro.js@4.2.2/minified/intro.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
        #modalDocente {
            z-index: 9999; 
        }

    /* Aplica un fondo visual a la columna completa del DNI cuando introJs está activo */
    .introjs-showElement .dni-tour {
        background-color: #e0f2fe !important;
       
        box-shadow: 0 0 0 4px rgba(2,132,199,0.3);
        z-index: 9999;
        position: relative;
    }
    .introjs-tooltiptext {
        text-align: center !important;
        font-size: 15px !important;
        line-height: 1.6 !important;
    }

    .introjs-tooltip {
        max-width: 350px !important;
    }

    .introjs-progress {
        background: #e0e0e0 !important;
    }

    .introjs-progressbar {
        background-color: #6366f1 !important; /* tailwind indigo-500 */
    }

    .introjs-button {
        font-weight: 600;
        padding: 6px 14px !important;
    }

    .introjs-tooltip-title {
        text-align: center;
        font-weight: bold;
    }
    .custom-intro-tooltip {
        text-align: center;
        font-size: 15px;
        line-height: 1.6;
    }
    .custom-intro-tooltip .introjs-skipbutton {
        background: transparent;
        border: none;
        font-size: 22px;
        color: #555;
        position: absolute;
        top: 8px;
        right: 12px;
        padding: 0;
        line-height: 1;
    }

</style>
<meta name="guardar-firma-url" content="{{ route('guardar.firma.director') }}">
    <div class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6" >
        <h1 class="text-3xl font-bold text-center mb-4 uppercase">
            ANEXO 03 - 
            <strong class="text-blue-600 text-3xl">
                {{ mb_strtoupper(\Carbon\Carbon::now()->translatedFormat('F '), 'UTF-8') }}
            </strong>
        </h1>
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">Formato 01: Reporte de Asistencia Detallado</h1>
        <button onclick="iniciarTutorial()" class="mb-4 px-4 py-2 bg-emerald-600 text-white rounded bg-violet-600 hover:bg-violet-700">
            Ver tutorial
        </button>

        <!-- Información de la institución y nivel -->
        <div class="mb-4 flex flex-wrap justify-between items-center gap-4" data-step="1">
        <!-- Columna izquierda: UGEL, IE, Nivel -->
        <div class="flex-1 min-w-[250px]">
            <p class="text-sm font-medium">{{ $registros->first()->ugel ?? 'N/A' }}</p>
            <p class="text-sm font-medium">I.E: {{ $institucion }}</p>
            <p class="text-sm font-medium">
                Nivel / Modalidad Educativa: {{ $nivelSeleccionado ?? 'No disponible' }} {{ $modalidad ?? 'No disponible' }}
            </p>
        </div>

            <!-- Columna derecha: Periodo y Turno -->
            <div class="flex-1 min-w-[200px]">
                <p class="text-sm font-medium mt-[2px]">
                    PERIODO: {{ mb_strtoupper(\Carbon\Carbon::now()->translatedFormat('F Y'), 'UTF-8') }}
                </p>

                <p class="text-sm font-medium">Turno: {{ $d_cod_tur }}</p>
            </div>

            <form method="GET" action="{{ url('/reporte_anexo03') }}" class="flex flex-wrap items-center gap-4">

                <!-- Botón Guardar Asistencia Masiva -->
                <button type="button" id="guardarTodo" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" data-step='19'>
                    Guardar Asistencia Masiva
                </button>
                <div id="loader" class="hidden flex items-center justify-center space-x-2 text-blue-600 mt-4">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>Guardando asistencia...</span>
                </div>

                <!-- Selector de Nivel -->
                <div class="flex items-center ml-auto" data-step="2">
                    <label for="nivel" class="text-sm font-medium mr-2">Nivel:</label>
                    <select id="nivel" name="nivel" onchange="this.form.submit()"
                            class="border border-gray-300 rounded px-2 py-1 text-sm"
                            {{ count($niveles) <= 1 ? 'disabled' : '' }}>
                        @foreach ($niveles as $nivel)
                            <option value="{{ $nivel }}" {{ $nivel == $nivelSeleccionado ? 'selected' : '' }}>{{ $nivel }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
       

    <!-- Contenedor exclusivo para la tabla -->
    <div class="mb-4" data-step="3">
        <div class="overflow-auto border rounded max-h-[500px] w-full">
            <div class="min-w-[900px] w-full" >
                <table  class="min-w-[1200px] w-full text-sm table-auto border-collapse">
                    @php
                        use Carbon\Carbon;
                        $mes = $mes ?? 3;
                        $anio = $anio ?? 2025;
                        $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;
                        $feriados = ['2025-07-28','2025-07-29','2025-08-06','2025-08-30','2025-10-08' ,'2025-11-01','2025-12-08','2025-12-09','2025-12-25','2025-12-26'];
                        $diasSemana = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];
                        $fechaHoy = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
                    @endphp
                    @php
                        $bloques = [
                            ['tipo' => 'g', 'inicio' => '2025-03-03', 'fin' => '2025-03-14'],
                            ['tipo' => 'l', 'inicio' => '2025-03-17', 'fin' => '2025-05-16'],
                            ['tipo' => 'g', 'inicio' => '2025-05-19', 'fin' => '2025-05-23'],
                            ['tipo' => 'l', 'inicio' => '2025-05-26', 'fin' => '2025-07-25'],
                            ['tipo' => 'g', 'inicio' => '2025-07-28', 'fin' => '2025-08-08'],
                            ['tipo' => 'l', 'inicio' => '2025-08-11', 'fin' => '2025-10-10'],
                            ['tipo' => 'g', 'inicio' => '2025-10-13', 'fin' => '2025-10-17'],
                            ['tipo' => 'l', 'inicio' => '2025-10-20', 'fin' => '2025-12-19'],
                            ['tipo' => 'g', 'inicio' => '2025-12-22', 'fin' => '2025-12-31'],
                        ];
                    @endphp
                    @php
                        if (!function_exists('obtenerTipoSemana')) {
                            function obtenerTipoSemana($fecha, $bloques, $feriados) {
                                if ($fecha->isWeekend() || in_array($fecha->format('Y-m-d'), $feriados)) {
                                    return null;
                                }
                                foreach ($bloques as $bloque) {
                                    if ($fecha->between(Carbon::parse($bloque['inicio']), Carbon::parse($bloque['fin']))) {
                                        return $bloque['tipo'];
                                    }
                                }
                                return null;
                            }
                        }
                    @endphp

                    @php
                        $patronDias = [];
                        for ($d = 1; $d <= $diasEnMes; $d++) {
                            $fecha = Carbon::create($anio, $mes, $d);
                            // Guardar en índice $d (o $d-1, pero que uses igual en todo el código)
                            $patronDias[$d] = obtenerTipoSemana($fecha, $bloques, $feriados); 
                        }
                    @endphp
                    <thead class="bg-gray-200 text-gray-700 uppercase text-xs sticky top-0 z-10">
                        <tr>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="3">Nº</th>
                            <th class="border px-2 py-1 bg-gray-200 w-24 text-center" rowspan="3">
                                DNI
                            </th>
                            <th class="border px-2 py-1 bg-gray-200 w-24 text-center" rowspan="3">
                                Cod.Plaza
                            </th>
                            <th class="border px-2 py-1 bg-gray-200 " rowspan="3">Apellidos y Nombres</th>
                            <th class="border px-2 py-1 bg-gray-200 text-center" rowspan="3">Cargo</th>
                            <th class="border px-2 py-1 bg-gray-200 text-center" rowspan="3">Condición</th>
                            <th class="border px-2 py-1 bg-gray-200  text-center" rowspan="3">Jor. Lab.</th>
                            @for ($d = 1; $d <= $diasEnMes; $d++)
                                <th class="border px-1 py-1 bg-gray-200">{{ $d }}</th>
                            @endfor
                        </tr>
                        <tr>
                            @for ($d = 1; $d <= $diasEnMes; $d++)
                                @php
                                    $fecha = Carbon::create($anio, $mes, $d);
                                    $nombreDia = $diasSemana[$fecha->dayOfWeek];
                                @endphp
                                <th class="border px-1 py-1 text-[10px] {{ in_array($nombreDia, ['S', 'D']) ? 'bg-gray-300' : 'bg-gray-200' }}">
                                    {{ $nombreDia }}
                                </th>
                            @endfor
                        </tr>
                        <tr>
                            @for ($d = 1; $d <= $diasEnMes; $d++)
                                @php
                                    $fecha = Carbon::create($anio, $mes, $d);
                                    $tipo = obtenerTipoSemana($fecha, $bloques, $feriados);

                                    $texto = match($tipo) {
                                        'g' => 'G',
                                        'l' => 'L',
                                        default => '',
                                    };

                                    $bgColor = match($tipo) {
                                        'g' => 'bg-blue-100 text-blue-800',
                                        'l' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-500',
                                    };
                                @endphp
                                <th class="border px-1 py-1 text-[10px] {{ $bgColor }}">
                                    {{ $texto }}
                                </th>
                            @endfor
                        </tr>                        
                    </thead>
                    @php
                        if (!function_exists('obtenerTextoPorCodigo')) {
                            function obtenerTextoPorCodigo($codigo) {
                                return match($codigo) {
                                    'L' => 'Licencia',
                                    'I' => 'Incapacidad',
                                    'P' => 'Permiso',
                                    'V' => 'Vacaciones',
                                    default => 'Observación',
                                };
                            }
                        }
                    @endphp
                    @php
                    if (!function_exists('adaptarAsistenciaMes')) {
                        function adaptarAsistenciaMes(array $asistencia, int $anio, int $mes): array {
                                    // Detectar qué días de la semana están marcados como "A"
                                    $diasActivos = [];
                                    foreach ($asistencia as $i => $valor) {
                                        if ($valor === 'A') {
                                            $fecha = Carbon::create(null, 10, $i+1);
                                            $diasActivos[$fecha->dayOfWeek] = true;
                                        }
                                    }
                                    // Generar array para el mes destino
                                    $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;
                                    $nuevo = [];
                                    for ($d = 1; $d <= $diasEnMes; $d++) {
                                        $fecha = Carbon::create($anio, $mes, $d);
                                        $dow = $fecha->dayOfWeek;
                                        $nuevo[] = isset($diasActivos[$dow]) ? 'A' : null;
                                }

                            return $nuevo;
                        }
                    }
                    @endphp
                    <tbody class="bg-white" >
                        @php
                            if (!function_exists('empiezaCon')) {
                                function empiezaCon($texto, $prefijo) {
                                    return $texto && str_starts_with(strtoupper(trim($texto)), strtoupper($prefijo));
                                }
                            }
                        @endphp
                        @forelse ($registros as $index => $r)
                            @php
                                $clave = $r->dni . '_' . $r->cod;
                                $asistenciaPersona = $asistencias[$clave] ?? null;

                                $mov = strtoupper(trim($r->mov));

                                // Detectar movimientos especiales
                                $esEncargatura = ($mov === 'ENCARGATURA');
                                $esLicencia = empiezaCon($mov, 'LICENCIA');
                                $esVacaciones = ($mov === 'VACACIONES');
                                $finicio = $r->finicio ? Carbon::parse($r->finicio)->format('Y-m-d') : null;
                                $ftermino = $r->ftermino ? Carbon::parse($r->ftermino)->format('Y-m-d') : null;
                            @endphp
                            <tr class="hover:bg-gray-100"
                                data-dni="{{ $r->dni }}"
                                data-nombres="{{ $r->nombres }}"
                                data-cargo="{{ $r->cargo }}"
                                data-condicion="{{ $r->condicion }}"
                                data-jornada="{{ $r->jornada }}"
                                data-cod="{{$r->cod}}"
                                @if ($asistenciaPersona)
                                    @if (!empty($asistenciaPersona['observacion']))
                                        data-observacion="{{ $asistenciaPersona['observacion'] }}"
                                    @endif
                                    @if (!empty($asistenciaPersona['tipo_observacion']))
                                        data-tipo-observacion="{{ $asistenciaPersona['tipo_observacion'] }}"
                                    @endif
                                    @if (!empty($asistenciaPersona['observacion_detalle']))
                                        data-observacion-detalle="{{ $asistenciaPersona['observacion_detalle'] }}"
                                    @endif
                                    @if (!empty($asistenciaPersona['fecha_inicio']))
                                        data-fecha-inicio="{{ \Carbon\Carbon::parse($asistenciaPersona['fecha_inicio'])->format('Y-m-d') }}"
                                    @endif
                                    @if (!empty($asistenciaPersona['fecha_fin']))
                                        data-fecha-fin="{{ \Carbon\Carbon::parse($asistenciaPersona['fecha_fin'])->format('Y-m-d') }}"
                                    @endif
                                @endif
                            >
                                <td class="border px-2 py-1">{{ $index + 1 }}</td>
                                <td class="border px-2 py-1  text-blue-500 dni-tour dni-tour-clickable w-24"
                                    onclick="openModal('{{ $r->dni }}', '{{ $r->nombres }}', '{{ $r->cod }}')"
                                >
                                    {{ $r->dni }}
                                </td>
                                <td class="border px-2 py-1 text-left text-left ">{{ $r->cod }}</td>
                                <td class="border px-2 py-1 text-left text-left ">{{ $r->nombres }}</td>
                                <td class="border px-2 py-1 text-center ">{{ $r->cargo }}</td>
                                <td class="border px-2 py-1 text-center ">{{ $r->condicion }}</td>
                                <td class="border px-2 py-1 text-center ">{{ $r->jornada }}</td>
                                
                                @php
                                    $clave = $r->dni . '_' . $r->cod;
                                    $asistenciaPersona = $asistencias[$clave] ?? null;
                                    $observacion = $asistenciaPersona['observacion'] ?? null;

                                    $tipoObservacion = $asistenciaPersona['tipo_observacion'] ?? null;
                                    $observacion = $asistenciaPersona['observacion'] ?? null;

                                    $d = 1;
                                    $asistencia = $asistenciaPersona['asistencia'] ?? [];
      
                                    $todoVacio = empty($asistencia) || collect($asistencia)->every(fn($v) => is_null($v) || $v === '');
                                @endphp
                                @php
                                    // Detectar movimiento especial del titular (nombrado/designado)
                                    $titularMov = collect($registros)
                                        ->first(function ($d) use ($r) {
                                            $mov = strtoupper(trim($d->mov));

                                            return $d->cod === $r->cod
                                                && in_array($d->condicion, ['NOMBRADO', 'DESIGNADO'])
                                                && (
                                                    (
                                                        str_starts_with($mov, 'LICENCIA')
                                                        && $mov !== 'LICENCIA CGR POR FALLECIMIENTO DE FAMILIAR' 
                                                    )
                                                    || $mov === 'VACACIONES'
                                                    || $mov === 'ENCARGATURA'
                                                );
                                        });

                                    $rangoMovEspecial = null;
                                    if ($titularMov && $titularMov->finicio && $titularMov->ftermino) {
                                        $rangoMovEspecial = [
                                            'inicio' => Carbon::parse($titularMov->finicio)->startOfDay(),
                                            'fin'    => Carbon::parse($titularMov->ftermino)->endOfDay(),
                                        ];
                                    }
                                @endphp
                                @php
                                    // Definir una vez el helper y los códigos en negrita
                                    $codigosNegrita = ['I','3T','J','L','P','T','H','F'];

                                    $pintarCelda = function($anio, $mes, $dia, $diasSemana, $feriados, $asistencia) use ($codigosNegrita) {
                                        $fecha       = \Carbon\Carbon::create($anio, $mes, $dia);
                                        $fechaActual = $fecha->format('Y-m-d');
                                        $nombreDia   = $diasSemana[$fecha->dayOfWeek] ?? null;

                                        // Valor base desde arreglo de asistencia (si existe)
                                        $valor = $asistencia[$dia - 1] ?? null;

                                        // Override por feriado
                                        if (in_array($fechaActual, $feriados)) {
                                            $valor = 'F';
                                        } elseif ($valor === null || $valor === '') {
                                            // Sin valor → calcular automático (fin de semana vacío, resto 'A')
                                            $valor = in_array($nombreDia, ['S', 'D']) ? '' : 'A';
                                        }

                                        // Clases
                                        $claseFondo = '';
                                        if ($valor === 'F') {
                                            $claseFondo = 'bg-yellow-100';
                                        } elseif (in_array($nombreDia, ['S', 'D'])) {
                                            $claseFondo = 'bg-gray-100';
                                        }

                                        $claseTexto = in_array(strtoupper($valor), $codigosNegrita) ? 'font-bold' : '';

                                        return [
                                            'valor'      => $valor,
                                            'claseFondo' => $claseFondo,
                                            'claseTexto' => $claseTexto,
                                        ];
                                    };
                                @endphp

                                @if ($esEncargatura && $finicio && $ftermino)
                                    @php
                                        $inicioMes = Carbon::create($anio, $mes, 1)->startOfDay();
                                        $finMes    = Carbon::create($anio, $mes, $diasEnMes)->endOfDay();

                                        $inicioRango = Carbon::parse($finicio)->startOfDay();
                                        $finRango    = Carbon::parse($ftermino)->endOfDay();

                                        // Si NO intersecta con el mes, dejamos que el resto del flujo pinte normal
                                        if ($finRango->lt($inicioMes) || $inicioRango->gt($finMes)) {
                                            $mostrarEncargatura = false;
                                        } else {
                                            $mostrarEncargatura = true;

                                            // Recortar a los límites del mes
                                            if ($inicioRango->lt($inicioMes)) $inicioRango = $inicioMes;
                                            if ($finRango->gt($finMes))       $finRango   = $finMes;

                                            $colspan   = $inicioRango->diffInDays($finRango) + 1;
                                            $diaInicio = $inicioRango->day;
                                            $diaFin    = $finRango->day;
                                        }
                                    @endphp

                                    @if (!empty($mostrarEncargatura))
                                        {{-- Antes del rango: pintar normal --}}
                                        @for ($dia = 1; $dia < $diaInicio; $dia++)
                                            @php
                                                $c = $pintarCelda($anio, $mes, $dia, $diasSemana, $feriados, $asistencia);
                                            @endphp
                                            <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $c['claseFondo'] }}"
                                                data-id="{{ $r->dni }}" data-dia="{{ $dia }}">
                                                <span class="asistencia-valor {{ $c['claseTexto'] }}">{{ $c['valor'] }}</span>
                                            </td>
                                        @endfor

                                        {{-- Rango de encargatura con colspan --}}
                                        <td colspan="{{ $colspan }}"
                                            class="border px-1 py-1 text-xs text-purple-700 font-semibold bg-purple-50 text-center"
                                            data-id="{{ $r->dni }}">
                                            {{ $r->mov }} - {{ \Carbon\Carbon::parse($finicio)->format('d/m/Y') }}
                                            al {{ \Carbon\Carbon::parse($ftermino)->format('d/m/Y') }}
                                        </td>

                                        {{-- Después del rango: pintar normal --}}
                                        @for ($dia = $diaFin + 1; $dia <= $diasEnMes; $dia++)
                                            @php
                                                $c = $pintarCelda($anio, $mes, $dia, $diasSemana, $feriados, $asistencia);
                                            @endphp
                                            <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $c['claseFondo'] }}"
                                                data-id="{{ $r->dni }}" data-dia="{{ $dia }}">
                                                <span class="asistencia-valor {{ $c['claseTexto'] }}">{{ $c['valor'] }}</span>
                                            </td>
                                        @endfor

                                        @continue
                                    @endif
                                @endif
                                @if ($esLicencia && $finicio && $ftermino)
                                    @php
                                        $inicioMes = Carbon::create($anio, $mes, 1)->startOfDay();
                                        $finMes    = Carbon::create($anio, $mes, $diasEnMes)->endOfDay();

                                        $inicioRango = Carbon::parse($finicio)->startOfDay();
                                        $finRango    = Carbon::parse($ftermino)->endOfDay();

                                        // Si NO intersecta con el mes, dejamos que el resto del flujo pinte normal
                                        if ($finRango->lt($inicioMes) || $inicioRango->gt($finMes)) {
                                            $mostrarLicencia = false;
                                        } else {
                                            $mostrarLicencia = true;

                                            // Recortar a los límites del mes
                                            if ($inicioRango->lt($inicioMes)) $inicioRango = $inicioMes;
                                            if ($finRango->gt($finMes))       $finRango   = $finMes;

                                            $colspan   = $inicioRango->diffInDays($finRango) + 1;
                                            $diaInicio = $inicioRango->day;
                                            $diaFin    = $finRango->day;
                                        }
                                    @endphp

                                    @if (!empty($mostrarLicencia))
                                        {{-- Antes del rango: pintar normal --}}
                                        @for ($dia = 1; $dia < $diaInicio; $dia++)
                                            @php
                                                $c = $pintarCelda($anio, $mes, $dia, $diasSemana, $feriados, $asistencia);
                                            @endphp
                                            <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $c['claseFondo'] }}"
                                                data-id="{{ $r->dni }}" data-dia="{{ $dia }}">
                                                <span class="asistencia-valor {{ $c['claseTexto'] }}">{{ $c['valor'] }}</span>
                                            </td>
                                        @endfor

                                        {{-- Rango de licencia con colspan --}}
                                        <td colspan="{{ $colspan }}"
                                            class="border px-1 py-1 text-xs text-purple-700 font-semibold bg-purple-50 text-center"
                                            data-id="{{ $r->dni }}">
                                            {{ $r->mov }} - {{ \Carbon\Carbon::parse($finicio)->format('d/m/Y') }}
                                            al {{ \Carbon\Carbon::parse($ftermino)->format('d/m/Y') }}
                                        </td>

                                        {{-- Después del rango: pintar normal --}}
                                        @for ($dia = $diaFin + 1; $dia <= $diasEnMes; $dia++)
                                            @php
                                                $c = $pintarCelda($anio, $mes, $dia, $diasSemana, $feriados, $asistencia);
                                            @endphp
                                            <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $c['claseFondo'] }}"
                                                data-id="{{ $r->dni }}" data-dia="{{ $dia }}">
                                                <span class="asistencia-valor {{ $c['claseTexto'] }}">{{ $c['valor'] }}</span>
                                            </td>
                                        @endfor

                                        @continue
                                    @endif
                                @endif
                                @if ($esVacaciones && $finicio && $ftermino)
                                    @php
                                        $inicioMes = Carbon::create($anio, $mes, 1)->startOfDay();
                                        $finMes    = Carbon::create($anio, $mes, $diasEnMes)->endOfDay();

                                        $inicioRango = Carbon::parse($finicio)->startOfDay();
                                        $finRango    = Carbon::parse($ftermino)->endOfDay();

                                        // Si NO intersecta con el mes, dejamos que el resto del flujo pinte normal
                                        if ($finRango->lt($inicioMes) || $inicioRango->gt($finMes)) {
                                            $mostrarVacaciones = false;
                                        } else {
                                            $mostrarVacaciones = true;

                                            // Recortar a los límites del mes
                                            if ($inicioRango->lt($inicioMes)) $inicioRango = $inicioMes;
                                            if ($finRango->gt($finMes))       $finRango   = $finMes;

                                            $colspan   = $inicioRango->diffInDays($finRango) + 1;
                                            $diaInicio = $inicioRango->day;
                                            $diaFin    = $finRango->day;
                                        }
                                    @endphp

                                    @if (!empty($mostrarVacaciones))
                                        {{-- Antes del rango: pintar normal --}}
                                        @for ($dia = 1; $dia < $diaInicio; $dia++)
                                            @php
                                                $c = $pintarCelda($anio, $mes, $dia, $diasSemana, $feriados, $asistencia);
                                            @endphp
                                            <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $c['claseFondo'] }}"
                                                data-id="{{ $r->dni }}" data-dia="{{ $dia }}">
                                                <span class="asistencia-valor {{ $c['claseTexto'] }}">{{ $c['valor'] }}</span>
                                            </td>
                                        @endfor

                                        {{-- Rango de vacaciones con colspan --}}
                                        <td colspan="{{ $colspan }}"
                                            class="border px-1 py-1 text-xs text-purple-700 font-semibold bg-purple-50 text-center"
                                            data-id="{{ $r->dni }}">
                                            {{ $r->mov }} - {{ \Carbon\Carbon::parse($finicio)->format('d/m/Y') }}
                                            al {{ \Carbon\Carbon::parse($ftermino)->format('d/m/Y') }}
                                        </td>

                                        {{-- Después del rango: pintar normal --}}
                                        @for ($dia = $diaFin + 1; $dia <= $diasEnMes; $dia++)
                                            @php
                                                $c = $pintarCelda($anio, $mes, $dia, $diasSemana, $feriados, $asistencia);
                                            @endphp
                                            <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $c['claseFondo'] }}"
                                                data-id="{{ $r->dni }}" data-dia="{{ $dia }}">
                                                <span class="asistencia-valor {{ $c['claseTexto'] }}">{{ $c['valor'] }}</span>
                                            </td>
                                        @endfor

                                        @continue
                                    @endif
                                @endif
                                {{-- Caso REEMPLAZO --}}
                                @if (in_array($r->condicion, ['CONTRATADO', 'ENCARGADO', 'DESIGNADO']) && $rangoMovEspecial)
                                    @for ($dia = 1; $dia <= $diasEnMes; $dia++)
                                        @php
                                            $fecha = \Carbon\Carbon::create($anio, $mes, $dia);

                                            // Obtener la celda base
                                            $c = $pintarCelda($anio, $mes, $dia, $diasSemana, $feriados, $asistencia);

                                            // Solo dentro del rango del movimiento especial (L-V) → forzar 'A' (salvo feriados)
                                            if (
                                                $fecha->between($rangoMovEspecial['inicio'], $rangoMovEspecial['fin']) &&
                                                $fecha->dayOfWeek >= 1 && $fecha->dayOfWeek <= 5 &&
                                                !in_array($fecha->format('Y-m-d'), $feriados)
                                            ) {
                                                $c['valor'] = 'A';
                                                // recalcular claseTexto si quieres forzar negrita según códigos
                                                $c['claseTexto'] = in_array(strtoupper($c['valor']), $codigosNegrita) ? 'font-bold' : '';
                                                // opcional: eliminar fondo de feriado/fin de semana para que se vea como día laborado
                                                $c['claseFondo'] = '';
                                            }
                                        @endphp
                                        <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $c['claseFondo'] }}"
                                            data-id="{{ $r->dni }}" data-dia="{{ $dia }}">
                                            <span class="asistencia-valor {{ $c['claseTexto'] }}">{{ $c['valor'] }}</span>
                                        </td>
                                    @endfor
                                    @continue
                                @endif
                                @if ($todoVacio && empty($observacion) && empty($tipoObservacion))
                                    @for ($d = 1; $d <= $diasEnMes; $d++)
                                        @php
                                            $fecha = Carbon::create($anio, $mes, $d);
                                            $fechaActual = $fecha->format('Y-m-d');
                                            $nombreDia = $diasSemana[$fecha->dayOfWeek];

                                            // Determinar si es feriado
                                            $esFeriado = in_array($fechaActual, $feriados);
                                            $valor = $esFeriado ? 'F' : (in_array($nombreDia, ['S', 'D']) ? '' : 'A');

                                            $claseFondo = match (true) {
                                                $valor === 'F' => 'bg-yellow-100',
                                                in_array($nombreDia, ['S', 'D']) => 'bg-gray-100',
                                                default => '',
                                            };

                                            $codigosNegrita = ['I', '3T', 'J', 'L', 'P', 'T', 'H', 'F'];
                                            $claseTexto = in_array(strtoupper($valor), $codigosNegrita) ? 'font-bold' : '';
                                        @endphp

                                        <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $claseFondo }}"
                                            data-id="{{ $r->dni }}" data-dia="{{ $d }}">
                                            <span class="asistencia-valor {{ $claseTexto }}">{{ $valor }}</span>
                                        </td>
                                    @endfor
                                    @continue
                                @endif
                                @if ($todoVacio && (!empty($observacion) || !empty($tipoObservacion)))
                                    <td colspan="{{ $diasEnMes }}" style="text-align: center;
                                        color: #dc2626;
                                        font-weight: 600;
                                        font-style: italic;
                                        background-color: #fef2f2;
                                        padding: 4px;
                                        font-size: 0.875rem;">
                                        {{ $observacion ?? 'Sin asistencia registrada' }}
                                    </td>
                                    {{-- Columna cumplimiento en caso de solo observación --}}
                                    <!-- <td class="border px-2 py-1 text-center text-gray-500 italic">-</td> -->
                                    @continue
                                @endif
                                @while ($d <= $diasEnMes)
                                    @php
                                        $valor = $asistencia[$d - 1] ?? null;
                                    @endphp

                                    {{-- Detectar bloque especial --}}
                                    @if (in_array($valor, ['L', 'I', 'P', 'V']))
                                        @php
                                            $inicio = $d;
                                            $fin = $d;
                                            for ($j = $d + 1; $j <= $diasEnMes; $j++) {
                                                $valorJ = $asistencia[$j - 1] ?? null;
                                                if ($valorJ === $valor) { $fin = $j; } else { break; }
                                            }
                                            $colspan = $fin - $inicio + 1;
                                        @endphp
                                        <td colspan="{{ $colspan }}"
                                            class="text-center text-red-600 font-semibold italic bg-red-50 border px-1 py-1 text-sm">
                                            {{ $observacion ?? obtenerTextoPorCodigo($valor) }}
                                        </td>
                                        @php $d = $fin + 1; @endphp
                                        @continue
                                    @endif

                                    {{-- Celda normal --}}
                                    @php
                                        $fecha = Carbon::create($anio, $mes, $d);
                                        $fechaActual = $fecha->format('Y-m-d');
                                        $nombreDia = $diasSemana[$fecha->dayOfWeek];
                                        $valor = $asistencia[$d - 1] ?? null;

                                        if (in_array($fechaActual, $feriados)) $valor = 'F';

                                        $claseFondo = match (true) {
                                            $valor === 'F' => 'bg-yellow-100',
                                            in_array($nombreDia, ['S', 'D']) => 'bg-gray-100',
                                            default => '',
                                        };
                                        $codigosNegrita = ['I','3T','J','L','P','T','H','F'];
                                        $claseTexto = in_array(strtoupper($valor), $codigosNegrita) ? 'font-bold' : '';
                                    @endphp

                                    <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $claseFondo }}"
                                        data-id="{{ $r->dni }}" data-dia="{{ $d }}">
                                        <span class="asistencia-valor {{ $claseTexto }}">{{ $valor }}</span>
                                    </td>
                                    @php $d++; @endphp
                                @endwhile
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 6 + $diasEnMes + 1 }}" class="text-center py-2">No hay registros disponibles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Leyenda -->
    <div class="mb-4 text-sm bg-blue-50 p-4 rounded-lg">
        <p><strong>Leyenda:</strong></p>
        <div class="mt-1 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2 text-gray-700">
            <span><strong>A:</strong> Día laborado</span>
            <span><strong>I:</strong> Inasistencia injustificada</span>
            <span><strong>J:</strong> Inasistencia justificada</span>
            <span><strong>L:</strong> Licencia sin goce de remuneraciones</span>
            <span><strong>P:</strong> Permiso sin goce de remuneraciones</span>
            <span><strong>T:</strong> Tardanza</span>
            <span><strong>H:</strong> Huelga o paro</span>
        </div>
    </div>
    
    <span style="color: blue; margin-right: 12px;"><strong>G:</strong> Bloque de Semanas de Gestión</span>
    <span style="color: green;"><strong>L:</strong> Bloque de Semanas Lectivas</span>


    <!-- Firma y botón de exportación -->
    <div class="mt-10 text-sm text-right">
        <p>Lugar y Fecha: {{ $fechaHoy }}</p>
    </div>

    <div class="flex items-start gap-4">
        <!-- Botón ingresar oficio + vista previa -->
        <div class="flex flex-col items-center" data-step='10'>
            <button id="btnOficio" onclick="openModal2()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Ingresar número de oficio
            </button>
            <p id="previewOficio" class="mt-2 font-bold text-blue-800"></p>
            <!-- Campo oculto con el número de oficio ya guardado -->
            <input type="hidden" id="oficio_guardado" value="{{ $numeroOficio ?? '' }}">
        </div>

        <!-- Firma: Botón y vista previa -->
        <div class="flex flex-col items-center" data-step='14'>
            <button onclick="openFirmaModal()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
                Ingresar firma
            </button>

            @if (!empty($firmaGuardada))
                <img id="firmaPreview" src="{{ asset('storage/firmasdirector/' . $firmaGuardada) }}" alt="Firma" class="mt-2" style="height: 80px;">
            @else
                <img id="firmaPreview" src="" alt="Firma temporal" class="hidden mt-2" style="height: 80px;">
            @endif
        </div>

        <!-- Botón Exportar PDF -->
        <form id="exportarForm" method="POST" action="{{ route('asistencia.exportar.pdf', ['nivel' => $nivelSeleccionado]) }}" target="_blank">
            @csrf
            <input type="hidden" name="numero_oficio" id="campoNumeroOficio">
            <input type="hidden" name="numero_expediente" id="campoNumeroExpediente">
            <input type="hidden" name="firma_base64" id="campoFirmaBase64">

            <div class="flex flex-col items-center" data-step='20'>
                <button type="submit"
                    onclick="antesDeExportar()"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Exportar en PDF
                </button>
            </div>
        </form>
        <!-- Botón ingresar expediente + vista previa -->
        <div class="flex flex-col items-center" data-step='21'>
            <button id="btnExpediente" onclick="openmodalExpediente()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Ingresar número de expediente
            </button>
            <p id="previewExpediente" class="mt-2 font-bold text-indigo-800"></p>
        </div>
    </div>

    <!-- CSRF para JS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Campo oculto -->
    <input type="hidden" id="oficio_guardado">

    <!-- Modal para subir la firma -->
    <div id="modalFirma" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden" data-step='15'>
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4 text-center">Subir firma</h2>

            <div class="mb-3 p-2 bg-yellow-100 text-yellow-800 text-sm rounded border border-yellow-300">
                Si no marcas la opción de guardar firma, esta se usará solo de forma temporal en el presente documento y deberás volver a subirla cada vez antes de generar el reporte.
            </div>

            <input type="file" id="firmaInput" accept="image/*"
                class="w-full border border-gray-300 rounded px-3 py-2 mb-4" data-step='16'>

            <div class="mb-4" data-step='17'>
                <label class="flex items-start space-x-2">
                    <input type="checkbox" id="guardarFirmaCheck" class="mt-1">
                    <span class="text-sm text-gray-700">
                        Deseo guardar esta firma para futuros usos.<br>
                        <span class="text-xs text-gray-500 italic block mt-1">
                            Al marcar esta opción y subir su firma, usted declara bajo su responsabilidad que la firma proporcionada le pertenece y autoriza su uso dentro de este sistema. La entidad no se hace responsable por el uso indebido, falsificación o suplantación de identidad derivada del mal uso de la imagen de la firma.
                        </span>
                    </span>
                </label>
            </div>

            <div class="flex justify-end space-x-2" data-step='18'>
                <button onclick="closeFirmaModal()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded">Cancelar</button>
                <button onclick="guardarFirma()" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded">Guardar</button>
            </div>
        </div>
    </div>
    <!-- Modal para ingresar el Oficio-->
    <div id="modalOficio" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden" data-step='11'>
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4 text-center">Número de Oficio</h2>

            <label for="numeroOficio" class="block text-sm font-medium text-gray-700 mb-1">
                Ingrese el número:
            </label>
            <input type="number" id="numeroOficio"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    min="0"
                    data-step='12'>

            <div class="flex justify-end space-x-2" data-step='13'>
                <button onclick="closeModal()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded">Cancelar</button>
                <button onclick="guardarOficio()" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded">Guardar</button>
            </div>
        </div>
    </div>
    <!-- Modal para ingresar número de expediente -->
    <div id="modalExpediente" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden" data-step='22'>
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Ingresar número de expediente</h2>
            <input type="text" id="inputExpediente" placeholder="Ej. 123456"
                class="w-full border rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300" data-step='23'>
            <div class="flex justify-end gap-2 mt-4" data-step='24'>
                <button onclick="cerrarmodalExpediente()"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                <button onclick="guardarExpediente()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal con leyenda, datalist, tabla editable , validación y observación -->
    <div data-step="8" id="modalForm" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex justify-center items-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-4xl p-6 overflow-auto max-h-[90vh]">
            <h2 class="text-xl font-bold mb-4" id="modalTitle">Modificar Asistencia</h2>
            <form id="asistenciaForm">
            <input type="hidden" name="dni" id="dni">

            <!-- Leyenda -->
            <div class="mb-4 text-sm bg-blue-50 p-4 rounded-lg" data-step="4">
            <p><strong>Leyenda:</strong></p>
            <div class="mt-1 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2 text-gray-700">
                <span><strong>A:</strong> Día laborado</span>
                <span><strong>I:</strong> Inasistencia injustificada</span>
                <span><strong>J:</strong> Inasistencia justificada</span>
                <span><strong>L:</strong> Licencia sin goce de remuneraciones</span>
                <span><strong>P:</strong> Permiso sin goce de remuneraciones</span>
                <span><strong>T:</strong> Tardanza</span>
                <span><strong>H:</strong> Huelga o paro</span>
            </div>

            </div>
            <!-- Aplicar patrón -->
                <div class="mt-4 bg-gray-50 p-4 rounded-lg border" data-step='5'>
                    <p class="text-sm font-semibold mb-2">Rellenar automáticamente con “A” según días seleccionados:</p>
                    <div class="flex flex-wrap gap-4 text-sm">
                    <label><input type="checkbox" class="dia-patron" value="1"> Lunes</label>
                    <label><input type="checkbox" class="dia-patron" value="2"> Martes</label>
                    <label><input type="checkbox" class="dia-patron" value="3"> Miércoles</label>
                    <label><input type="checkbox" class="dia-patron" value="4"> Jueves</label>
                    <label><input type="checkbox" class="dia-patron" value="5"> Viernes</label>
                    <button id="aplicar-patron" type="button" class="ml-4 bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition">
                        Aplicar patrón
                    </button>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-center mt-6 mb-2 uppercase">
                MES DE {{ mb_strtoupper(\Carbon\Carbon::now()->translatedFormat('F '), 'UTF-8') }}
            </h1>
                           
            <!-- Tabla editable -->
            <div class="overflow-auto mb-4 mt-4" data-step='6'>
            <table class="min-w-full text-sm border border-gray-400 shadow rounded-lg">
                <thead class="border px-2 py-1 bg-gray-300 text-center">
                <tr id="dias-numeros"></tr>
                <tr id="dias-letras"></tr>
                </thead>
                <tbody>
                <tr id="fila-asistencia"></tr>
                </tbody>
            </table>
            </div>

            <!-- Tipo Observacion -->
            <div class="mt-4" data-step='7'>
                <label for="tipo_observacion" class="block text-sm font-semibold mb-1">Tipo de Observación:</label>
                <select id="tipo_observacion" name="tipo_observacion" class="w-full border rounded p-2 text-sm">
                    <option value="" selected>-- Seleccione --</option>
                    <option value="Cese">Cese</option>
                    <option value="InasistenciaJustificada">Inasistencia Justificada (Licencia / Permiso)</option>
                    <option value="Licencia">Licencia sin goce de remuneraciones</option>
                    <option value="PermisoSinGoce">Permiso sin goce de remuneraciones</option>
                    <option value="AbandonoCargo">Abandono de Cargo</option>
                    <option value="Vacaciones">Vacaciones</option>
                    <!-- <option value="Encargatura">Encargatura</option> -->
                </select>
            </div>
            <!-- Subtipo -->
            <div class="mt-4">
                <label for="tipo_especifico" class="block text-sm font-semibold mb-1">Detalle (<strong>RVM-081-2023-MINEDU</strong>):</label>
                <select id="tipo_especifico" name="tipo_especifico" class="w-full border rounded p-2 text-sm" disabled>
                    <option value="">-- Seleccione un tipo observación primero --</option>
                </select>
            </div>
            <!-- Rango de Fechas para Licencia -->
            <div id="rangoFechasLicencia" class="mt-4 hidden">
                <label class="block text-sm font-semibold mb-1">Rango de Fechas de Licencia sin goce de remuneraciones:</label>
                <div class="flex gap-2">
                    <input type="date" id="fechaInicioLicencia" class="w-full border rounded p-2 text-sm">
                    <input type="date" id="fechaFinLicencia" class="w-full border rounded p-2 text-sm">
                </div>
            </div>
            <!-- Rango de Fechas para Inasistencia -->
            <div id="rangoFechasInasistencia" class="mt-4 hidden">
                <label class="block text-sm font-semibold mb-1">Rango de Fechas de Inasistencia Justificada:</label>
                <div class="flex gap-2">
                    <input type="date" id="fechaInicioInasistencia" class="w-full border rounded p-2 text-sm">
                    <input type="date" id="fechaFinInasistencia" class="w-full border rounded p-2 text-sm">
                </div>
            </div>
            <!-- Rango de Fechas para Permisos -->
            <div id="rangoFechasPermisos" class="mt-4 hidden">
                <label class="block text-sm font-semibold mb-1">Rango de Fechas de Permiso sin foce de remuneraciones:</label>
                <div class="flex gap-2">
                    <input type="date" id="fechaInicioPermiso" class="w-full border rounded p-2 text-sm">
                    <input type="date" id="fechaFinPermiso" class="w-full border rounded p-2 text-sm">
                </div>
            </div>
            <!-- Rango de Fechas para Vacaciones -->
            <div id="rangoFechasVacaciones" class="mt-4 hidden">
                <label class="block text-sm font-semibold mb-1">Rango de Fechas de Vacaciones:</label>
                <div class="flex gap-2">
                    <input type="date" id="fechaInicioVacaciones" class="w-full border rounded p-2 text-sm">
                    <input type="date" id="fechaFinVacaciones" class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <!-- Observación -->
            <div class="mt-4" data-step='9'>
                <label for="observacion" class="block text-sm font-semibold mb-1">Observación:</label>
                <textarea id="observacion" rows="2" class="w-full border rounded p-2 text-sm" placeholder="Ej. Detalle EXPEDIENTE,RESOLUCION ,etc"></textarea>
            </div>

            <!-- Botones -->
            <div class="text-right mt-6">
                <button type="button" id="saveAsistencia" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Guardar
                </button>
                <button type="button" id="closeModal" class="ml-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Cancelar
                </button>
            </div>
            </form>
        </div>
    </div>

        <!-- Datalist de valores válidos -->
        <datalist id="asistencia-codigos">
            <option value="A">
            <option value="I">
            <option value="J">
            <option value="L">
            <option value="P">
            <option value="T">
            <option value="H">
            <option value="F">
        </datalist>
    </div>

<script>

    function iniciarTutorial() {
        const tour = introJs();

        const dniElements = document.querySelectorAll('.dni-tour-clickable');
        const sextoDni = dniElements[0];

        tour.setOptions({
            scrollToElement: false,
            showProgress: true,
            showBullets: false,
            tooltipClass: 'custom-intro-tooltip',
            nextLabel: 'Siguiente',
            prevLabel: 'Anterior',
            skipLabel: 'x',
            doneLabel: 'Finalizar',
            steps: [
                { element: '[data-step="1"]', intro: "Datos de su Institución" },
                { element: '[data-step="2"]', intro: "Selector por nivel educativo" },
                { element: '[data-step="3"]', intro: "Cuadro de asistencia según nivel" },
                { element: sextoDni, intro: "Haz clic aquí para abrir el formulario de asistencia" },
                { element: '#modalForm', intro: "Formulario emergente de asistencia" },
                { element: '[data-step="4"]', intro: "Leyenda de códigos válidos" },
                { element: '[data-step="5"]', intro: "Selector de patrón de días" },
                { element: '[data-step="6"]', intro: "Valores de asistencia por día" },
                { element: '[data-step="7"]', intro: "Tipo de observación" },
                { element: '[data-step="9"]', intro: "Detalle de observación" },
                { element: '[data-step="10"]', intro: "Botón ingresar número de oficio" },
                { element: '[data-step="11"]', intro: "Formulario de oficio" },
                { element: '[data-step="12"]', intro: "Ingrese su número de oficio" },
                { element: '[data-step="13"]', intro: "Botones guardar / cancelar oficio" },
                { element: '[data-step="14"]', intro: "Botón ingresar firma" },
                { element: '[data-step="15"]', intro: "Formulario de firma" },
                { element: '[data-step="16"]', intro: "Sube la imagen de tu firma" },
                { element: '[data-step="17"]', intro: "Casilla para firma permanente" },
                { element: '[data-step="18"]', intro: "Si no marcas, la firma es temporal" },
                { element: '[data-step="19"]', intro: "Botón final de guardar asistencias" },
                { element: '[data-step="20"]', intro: "Vista previa del PDF para MINEDU" },
                { element: '[data-step="21"]', intro: "Número de expediente" },
                { element: '[data-step="22"]', intro: "Formulario de expediente" },
                { element: '[data-step="23"]', intro: "Ingresa solo tu número de expediente" },
                { element: '[data-step="24"]', intro: "Botones para expediente" }
            ].map((step) => {
                if (typeof step.element === 'string') {
                    step.element = document.querySelector(step.element);
                }
                return step;
            }).filter(step => step.element)
        });

        tour.onafterchange((element) => {
            const paso = tour._currentStep;

            if (element === sextoDni) {
                sextoDni?.click();
            }

            if (element?.id === 'modalForm') {
                const modal = document.getElementById('modalForm');
                if (modal?.classList.contains('hidden')) {
                    sextoDni?.click();
                    setTimeout(() => {
                        tour.goToStepNumber(4).start();
                    }, 150);
                }
            }

            if (paso === 10) {
                cerrarModal('modalForm');
                abrirModal('modalOficio');
            }

            if (paso === 14) {
                cerrarModal('modalOficio');
                abrirModal('modalFirma');
            }

            if (paso === 19) {
                cerrarModal('modalFirma');
            }

            if (paso === 22) {
                abrirModal('modalExpediente');
                setTimeout(() => {
                    tour.refresh();
                }, 10);
            }

            if (paso === 25) {
                cerrarModal('modalExpediente');
            }
        });

        tour.oncomplete(() => {
        Swal.fire({
            title: "🎉 ¡Tutorial finalizado!",
            html: `
                <div style="text-align: center;">
                    <p style="margin-bottom: 12px; font-size: 16px;">
                        Si tienes dudas puedes repetir el recorrido cuando gustes.
                    </p>
                    <img src="https://media.giphy.com/media/v1.Y2lkPWVjZjA1ZTQ3a3JjbnJlMnl5a2I0bzRpcHl3cDdiaWVlaWxycGZjbWtjbnl2YmlrNSZlcD12MV9naWZzX3JlbGF0ZWQmY3Q9Zw/VTKW1mIHKlBTWWkUWI/giphy.gif"
                        style="display: block; margin: 10px auto 0 auto; width: 100%; max-width: 300px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                </div>
            `,
            icon: "success",
            confirmButtonText: "👍 Entendido",
            customClass: {
                popup: 'rounded-xl'
            }
        });

            cerrarModal('modalFirma');
            cerrarModal('modalOficio');
            cerrarModal('modalForm');
            cerrarModal('modalExpediente');
        });



        tour.start();

        function abrirModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('hidden');
        }

        function cerrarModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('hidden');
        }
    }

    const feriados = ['2025-07-28','2025-07-29','2025-08-06','2025-08-30','2025-10-08' ,'2025-11-01','2025-12-08','2025-12-09','2025-12-25','2025-12-26']; 
    const diasSemana = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];
    const codigosValidos = ['A', 'I', 'J', 'L', 'P', 'T', 'H', 'F'];

    const urlGuardarFirma = document.querySelector('meta[name="guardar-firma-url"]').content;

    let firmaBase64 = null;

    function openFirmaModal() {
        document.getElementById('modalFirma').classList.remove('hidden');
    }

    function closeFirmaModal() {
        document.getElementById('modalFirma').classList.add('hidden');
    }

    function guardarCambiosModal() {
        const selects = document.querySelectorAll('.asistencia-input');

        selects.forEach(select => {
            const dia = select.dataset.dia;
            const valor = select.value.trim();
            const celda = document.querySelector(`.asistencia-celda[data-id="${dniActual}"][data-dia="${dia}"]`);

            if (celda) {
                // Limpiar contenido anterior
                celda.innerHTML = '';

                // Crear nuevo <span> con o sin negrita
                const span = document.createElement('span');
                span.classList.add('asistencia-valor');
                span.textContent = valor;

                // Aplicar negrita si NO es "A" y no está vacío
                if (valor && valor !== 'A') {
                    span.classList.add('font-bold');
                }

                celda.appendChild(span);
            }
        });

        // Cerrar el modal
        document.getElementById('modalForm').classList.add('hidden');
    }

    function openmodalExpediente() {
        document.getElementById('modalExpediente').classList.remove('hidden');
        document.getElementById('inputExpediente').value = '';
        document.getElementById('inputExpediente').focus();
    }

    function cerrarmodalExpediente() {
        document.getElementById('modalExpediente').classList.add('hidden');
    }

    function guardarFirma() {
        const fileInput = document.getElementById('firmaInput');
        const guardarCheck = document.getElementById('guardarFirmaCheck').checked;
        const file = fileInput.files[0];

        if (!file) {
            alert("Seleccione una imagen.");
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            firmaBase64 = e.target.result; // guardo la firma temporal en esta variable global

            const preview = document.getElementById('firmaPreview');
            preview.src = firmaBase64;
            preview.classList.remove('hidden');

            closeFirmaModal();

            if (guardarCheck) {
                const formData = new FormData();
                formData.append('firma', file);

                fetch(urlGuardarFirma, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })

                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Firma guardada correctamente.");
                    } else {
                        alert("Error al guardar la firma: " + (data.error ?? ''));
                    }
                })
                .catch(() => alert("Error en la comunicación con el servidor."));
            } else {
                alert("Firma cargada temporalmente.");
            }
        };

        reader.readAsDataURL(file);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const nroOficio = document.getElementById('oficio_guardado').value.trim();
        if (nroOficio) {
            document.getElementById('previewOficio').innerText = 'Oficio N° ' + nroOficio;
            document.getElementById('btnOficio').innerText = 'Editar número de oficio';
        }
    });

    function openModal2() {
        const currentNro = document.getElementById('oficio_guardado').value.trim();
        document.getElementById('numeroOficio').value = currentNro;
        document.getElementById('modalOficio').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalOficio').classList.add('hidden');
    }

    function guardarOficio() {
        const nuevoNro = document.getElementById('numeroOficio').value.trim();
        if (!nuevoNro) return;

        document.getElementById('oficio_guardado').value = nuevoNro;
        document.getElementById('previewOficio').innerText = 'Oficio N° ' + nuevoNro;
        document.getElementById('btnOficio').innerText = 'Editar número de oficio';

        closeModal();
    }

    function guardarExpediente() {
        const numero = document.getElementById('inputExpediente').value.trim();
        if (numero !== '') {
            document.getElementById('campoNumeroExpediente').value = numero;
            document.getElementById('previewExpediente').innerText = 'Expediente MPD2025-EXP-' +numero;
            cerrarmodalExpediente();
        } else {
            alert('Por favor, ingrese un número de expediente válido.');
        }
    }

    function antesDeExportar() {
        document.getElementById('campoNumeroOficio').value = document.getElementById('oficio_guardado').value;
        document.getElementById('campoFirmaBase64').value = firmaBase64 ?? '';
    }


    let dniActual = '';
    let fechaActual = new Date();
    let mes = fechaActual.getMonth() + 1;
    let anio = fechaActual.getFullYear();
    let numeroOficio = null;
    let numeroExpediente = null;
    let codActual = '';

    function openModal(dni, nombres, cod) {
        document.getElementById('observacion').value = '';
        document.getElementById('tipo_observacion').value = '';
        
            // 🔹 Resetear subtipo
        const subSelect = document.getElementById('tipo_especifico');
        subSelect.innerHTML = "<option value=''>-- Seleccione un tipo observación primero --</option>";
        subSelect.disabled = true;

        // 🔹 Ocultar todos los rangos de fechas primero
        document.getElementById("rangoFechasLicencia").classList.add("hidden");
        document.getElementById("rangoFechasInasistencia").classList.add("hidden");
        document.getElementById("rangoFechasPermisos").classList.add("hidden");
        document.getElementById("rangoFechasVacaciones").classList.add("hidden");

            // 🔹 Vaciar inputs de fechas
        document.querySelectorAll("#rangoFechasLicencia input, #rangoFechasInasistencia input, #rangoFechasPermisos input, #rangoFechasVacaciones input")
            .forEach(inp => inp.value = "");

        dniActual = dni;
        codActual = cod;
        const title = document.getElementById('modalTitle');
        const filaNumeros = document.getElementById('dias-numeros');
        const filaLetras = document.getElementById('dias-letras');
        const filaInputs = document.getElementById('fila-asistencia');

        title.textContent = `Modificar Asistencia de ${nombres} `;
        document.getElementById('modalForm').classList.remove('hidden');

        filaNumeros.innerHTML = '';
        filaLetras.innerHTML = '';
        filaInputs.innerHTML = '';

        const diasEnMes = new Date(anio, mes, 0).getDate();
        const id_modalidad = @json(session('siic01.idmodalidad'));

        // Lista de id_modalidad que SÍ pueden usar sábado y domingo
        const idmodalidadConSabDom = ["1", "4"];
        const permiteSabDom = idmodalidadConSabDom.includes(id_modalidad);

        for (let dia = 1; dia <= diasEnMes; dia++) {
            const fecha = new Date(anio, mes - 1, dia);
            const diaSemana = fecha.getDay();
            const fechaStr = fecha.toISOString().slice(0, 10);
            const esFeriado = feriados.includes(fechaStr);

            const isDomingo = diaSemana === 0;
            const isSabado = diaSemana === 6;

            filaNumeros.innerHTML += `<th class="border px-1">${dia}</th>`;
            filaLetras.innerHTML += `<th class="border px-1">${diasSemana[diaSemana]}</th>`;

            const fila = document.querySelector(`tr[data-dni="${dni}"][data-cod="${cod}"]`);
            const celda = fila ? fila.querySelector(`.asistencia-celda[data-id="${dni}"][data-dia="${dia}"] .asistencia-valor`) : null;

            let valor = celda ? celda.textContent.trim().toUpperCase() : '';
            if (esFeriado) valor = 'F';

            // Bloquear sábados y domingos si modalidad no permite
            if (!permiteSabDom && (isSabado || isDomingo)) {
                filaInputs.innerHTML += `
                    <td class="border px-1 bg-gray-100">
                        <input type="text" class="w-10 text-center bg-transparent focus:outline-none" readonly />
                    </td>`;
            } else {
                // Normal: editable
                filaInputs.innerHTML += `
                    <td class="border px-1">
                        <select 
                            class="asistencia-input w-12 text-center bg-white border rounded"
                            data-dia="${dia}" 
                            data-fecha="${fechaStr}"
                            data-dia-semana="${diaSemana}"
                            ${esFeriado ? 'disabled' : ''}>
                            <option value="" ${valor === '' ? 'selected' : ''}></option>
                            <option value="A" ${valor === 'A' ? 'selected' : ''}>A</option>
                            <option value="I" ${valor === 'I' ? 'selected' : ''}>I</option>
                            <option value="J" ${valor === 'J' ? 'selected' : ''}>J</option>
                            <option value="L" ${valor === 'L' ? 'selected' : ''}>L</option>
                            <option value="P" ${valor === 'P' ? 'selected' : ''}>P</option>
                            <option value="T" ${valor === 'T' ? 'selected' : ''}>T</option>
                            <option value="F" ${valor === 'F' ? 'selected' : ''}>F</option>
                            <option value="H" ${valor === 'H' ? 'selected' : ''}>H</option>
                        </select>
                    </td>`;
            }
        }


        document.querySelectorAll('.dia-patron').forEach(cb => cb.checked = false);

        // 🔹 Recuperar observación y fechas
        const fila = document.querySelector(`tr[data-dni="${dni}"][data-cod="${cod}"]`);
        if (fila) {
            const obs = fila.getAttribute('data-observacion') || '';
            const tipoObs = fila.getAttribute('data-tipo-observacion') || '';
            const detalleObs = fila.getAttribute('data-observacion-detalle') || '';
            const fechaInicio = fila.getAttribute('data-fecha-inicio') || '';
            const fechaFin = fila.getAttribute('data-fecha-fin') || '';

            document.getElementById('observacion').value = obs;
            document.getElementById('tipo_observacion').value = tipoObs;

            // 🔹 Rellenar subtipo si existe
            if (opciones[tipoObs]) {
                subSelect.innerHTML = "";

                // 🔹 Siempre el placeholder
                const placeholder = document.createElement('option');
                placeholder.value = "";
                placeholder.textContent = "-- Seleccione un detalle --";
                subSelect.appendChild(placeholder);

                subSelect.disabled = false;
                opciones[tipoObs].forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt;
                    option.textContent = opt;
                    if (opt === detalleObs) option.selected = true; // mantiene lo guardado
                    subSelect.appendChild(option);
                });
            }

            // 🔹 Mostrar y rellenar el rango de fechas correcto
            if (tipoObs === "Licencia") {
                document.getElementById("rangoFechasLicencia").classList.remove("hidden");
                document.getElementById("fechaInicioLicencia").value = fechaInicio;
                document.getElementById("fechaFinLicencia").value = fechaFin;
            }
            if (tipoObs === "InasistenciaJustificada") {
                document.getElementById("rangoFechasInasistencia").classList.remove("hidden");
                document.getElementById("fechaInicioInasistencia").value = fechaInicio;
                document.getElementById("fechaFinInasistencia").value = fechaFin;
            }
            if (tipoObs === "PermisoSinGoce") {
                document.getElementById("rangoFechasPermisos").classList.remove("hidden");
                document.getElementById("fechaInicioPermiso").value = fechaInicio;
                document.getElementById("fechaFinPermiso").value = fechaFin;
            }
            if (tipoObs === "Vacaciones") {
                document.getElementById("rangoFechasVacaciones").classList.remove("hidden");
                document.getElementById("fechaInicioVacaciones").value = fechaInicio;
                document.getElementById("fechaFinVacaciones").value = fechaFin;
            }
        }
        // Finalmente mostrar modal
        document.getElementById('modalForm').classList.remove('hidden');
    }


    document.addEventListener('DOMContentLoaded', () => {
        // Cerrar el modal
        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('modalForm').classList.add('hidden');
        });

    });

    // Aplicar patrón
    document.getElementById('aplicar-patron').addEventListener('click', () => {
        const seleccionados = Array.from(document.querySelectorAll('.dia-patron'))
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value)); 

        const inputs = document.querySelectorAll('.asistencia-input');

        
        document.querySelectorAll(`tr[data-dni="${dniActual}"][data-cod="${codActual}"]`).forEach(fila => {
            const dni = fila.dataset.dni;
            const diasEnMes = new Date(anio, mes, 0).getDate();
            const celdasDia = fila.querySelectorAll('td[data-dia]');

            if (celdasDia.length < diasEnMes) {
                // Eliminar todas las celdas que no tienen data-dia (es decir, las que vienen de licencias parciales u observaciones)
                fila.querySelectorAll('td:not([data-dia]):not(:nth-child(-n+7))').forEach(td => td.remove());

                // Regenerar solo los días faltantes
                const existentes = new Set(Array.from(fila.querySelectorAll('td[data-dia]')).map(td => parseInt(td.dataset.dia)));

                for (let d = 1; d <= diasEnMes; d++) {
                    if (!existentes.has(d)) {
                        const nuevaCelda = document.createElement('td');
                        nuevaCelda.className = 'asistencia-celda text-center align-middle border px-1 py-0.5';
                        nuevaCelda.setAttribute('data-id', dni);
                        nuevaCelda.setAttribute('data-dia', d);

                        const input = document.createElement('input');
                        input.type = 'text';
                        input.maxLength = 2;
                        input.className = 'asistencia-input w-6 text-center bg-white border border-gray-300 rounded';
                        input.setAttribute('data-dia', d);
                        input.setAttribute('data-id', dni);
                        const fecha = `${anio}-${String(mes).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                        input.setAttribute('data-fecha', fecha);
                        const diaSemana = new Date(anio, mes - 1, d).getDay();
                        input.setAttribute('data-dia-semana', diaSemana);

                        nuevaCelda.appendChild(input);

                        // Insertar en la posición correcta
                        let insertBefore = Array.from(fila.children).find(td => {
                            const dia = td.getAttribute('data-dia');
                            return dia && parseInt(dia) > d;
                        });

                        fila.insertBefore(nuevaCelda, insertBefore || null);
                    }
                }

                fila.removeAttribute('data-observacion');
                fila.removeAttribute('data-tipo-observacion');
            }
        });


        // 2. Aplicar el patrón a todos los inputs
        inputs.forEach(input => {
            const diaSemana = parseInt(input.dataset.diaSemana);
            const fecha = input.dataset.fecha;
            const esFeriado = feriados.includes(fecha);
            const isFinSemana = (diaSemana === 0 || diaSemana === 6);

            if (isFinSemana) return;
            if (esFeriado) {
                input.value = 'F';
            } else if (seleccionados.includes(diaSemana)) {
                input.value = 'A';
            } else {
                input.value = '';
            }
        });
    });

    // Guardar los datos para la tabla
    document.getElementById('saveAsistencia').addEventListener('click', () => {
        const observacion = document.getElementById('observacion')?.value.trim() || '';
        const tipoObservacion = document.getElementById('tipo_observacion')?.value || '';
        const fila = document.querySelector(`tr[data-dni="${dniActual}"][data-cod="${codActual}"]`);
        

        if (!fila) return;

        // Eliminar observación previa si existe
        const celdaObservacion = fila.querySelector('td[colspan]');
        if (celdaObservacion) celdaObservacion.remove();

        fila.removeAttribute('data-observacion');
        fila.removeAttribute('data-tipo-observacion');

        const inputs = document.querySelectorAll('.asistencia-input');
        const diasEnMes = new Date(anio, mes, 0).getDate();

        if (tipoObservacion === 'Licencia' || 
            tipoObservacion === 'InasistenciaJustificada' || 
            tipoObservacion === 'PermisoSinGoce' || 
            tipoObservacion === 'Vacaciones') {

            let fInicio, fFin;

            if (tipoObservacion === 'Licencia') {
                fInicio = document.getElementById('fechaInicioLicencia').value;
                fFin = document.getElementById('fechaFinLicencia').value;
            } else if (tipoObservacion === 'InasistenciaJustificada') {
                fInicio = document.getElementById('fechaInicioInasistencia').value;
                fFin = document.getElementById('fechaFinInasistencia').value;
            } else if (tipoObservacion === 'PermisoSinGoce') {
                fInicio = document.getElementById('fechaInicioPermiso').value;
                fFin = document.getElementById('fechaFinPermiso').value;
            } else if (tipoObservacion === 'Vacaciones') {
                fInicio = document.getElementById('fechaInicioVacaciones').value;
                fFin = document.getElementById('fechaFinVacaciones').value;
            }

            if (fInicio && fFin) {
                const [anioI, mesI, diaI] = fInicio.split('-').map(Number);
                const [anioF, mesF, diaF] = fFin.split('-').map(Number);

                const inicio = new Date(anioI, mesI - 1, diaI);
                const fin = new Date(anioF, mesF - 1, diaF);

                fila.setAttribute('data-observacion', observacion);
                fila.setAttribute('data-tipo-observacion', tipoObservacion);

                const primerDia = inicio.getDate();
                const ultimoDia = fin.getDate();
                const colspan = (ultimoDia - primerDia + 1);

                for (let d = primerDia; d <= ultimoDia; d++) {
                    const celda = fila.querySelector(`.asistencia-celda[data-id="${dniActual}"][data-dia="${d}"]`);
                    if (celda) celda.remove();
                }

                const celdaUnificada = document.createElement('td');
                celdaUnificada.colSpan = colspan;
                celdaUnificada.className = "text-center font-semibold text-red-600 italic bg-red-50 text-xs";
                celdaUnificada.textContent = observacion;

                const celdasRestantes = fila.querySelectorAll(`.asistencia-celda[data-id="${dniActual}"]`);
                let insertado = false;
                for (const celda of celdasRestantes) {
                    const dia = parseInt(celda.dataset.dia);
                    if (dia > primerDia) {
                        celda.parentNode.insertBefore(celdaUnificada, celda);
                        insertado = true;
                        break;
                    }
                }
                if (!insertado) {
                    fila.appendChild(celdaUnificada);
                }

            } else {
                alert(`Debe indicar el rango de fechas para ${tipoObservacion}.`);
                return;
            }
        } else if (observacion !== '') {
            let celda = fila.children[7];
            while (celda) {
                const siguiente = celda.nextSibling;
                celda.remove();
                celda = siguiente;
            }

            const td = document.createElement('td');
            td.colSpan = diasEnMes;
            td.className = "text-center text-red-600 font-semibold italic bg-red-50";
            td.textContent = observacion;

            fila.appendChild(td);

            fila.setAttribute('data-tipo-observacion', tipoObservacion);
            fila.setAttribute('data-observacion', observacion);

        } else {
            inputs.forEach(input => {
                const dia = input.dataset.dia;
                const valor = input.value.toUpperCase();
                const celda = fila.querySelector(`.asistencia-celda[data-id="${dniActual}"][data-dia="${dia}"]`);

                if (celda) {
                    celda.innerHTML = '';
                    const span = document.createElement('span');
                    span.classList.add('asistencia-valor');
                    span.textContent = valor;
                    if (valor && valor !== 'A') {
                        span.classList.add('font-bold');
                    }
                    celda.appendChild(span);
                }
            });
        }

        // Limpiar y cerrar modal
        document.getElementById('observacion').value = '';
        document.getElementById('tipo_observacion').value = '';
        document.getElementById('fechaInicioLicencia').value = '';
        document.getElementById('fechaFinLicencia').value = '';
        document.getElementById('modalForm').classList.add('hidden');
    });

    // Guardar datos a la BD
    document.getElementById('guardarTodo').addEventListener('click', async function () {
        const boton = this;
        const loader = document.getElementById('loader');

        // Deshabilitar botón y mostrar loader
        boton.disabled = true;
        boton.classList.add('opacity-50', 'cursor-not-allowed');
        loader.classList.remove('hidden');

        const filas = document.querySelectorAll('tr[data-dni]');
        const docentes = [];

        // Mapeo de letras por tipo de observación
        const letraPorTipo = {
            Licencia: "L",
            InasistenciaJustificada: "I",
            PermisoSinGoce: "P",
            Vacaciones: "V"
        };

        filas.forEach(fila => {
            const dni = fila.getAttribute('data-dni');
            const nombres = fila.getAttribute('data-nombres');
            const cargo = fila.getAttribute('data-cargo');
            const condicion = fila.getAttribute('data-condicion');
            const jornada = fila.getAttribute('data-jornada');
            const cod = fila.getAttribute('data-cod');

            let asistencia = [];
            let observacion = null;
            let tipo_observacion = fila.getAttribute('data-tipo-observacion') || null;

            // Obtenemos el texto del option seleccionado
            const selectDetalle = fila.querySelector('.tipo_especifico');
            let observacion_detalle = fila.getAttribute('data-observacion-detalle') || null;

            if (selectDetalle && selectDetalle.options.length > 0) {
                const opcionSeleccionada = selectDetalle.options[selectDetalle.selectedIndex];
                if (opcionSeleccionada && opcionSeleccionada.textContent.trim() !== '') {
                    observacion_detalle = opcionSeleccionada.textContent.trim();
                }
            }

            const celdaObservacion = fila.querySelector('td[colspan]');
            if (celdaObservacion) {
                observacion = celdaObservacion.textContent.trim();
            }
    
            let fecha_inicio = fila.getAttribute('data-fecha-inicio') || null;
            let fecha_fin = fila.getAttribute('data-fecha-fin') || null;
        
            let diasMarcados = [];
            if (letraPorTipo[tipo_observacion] && fecha_inicio && fecha_fin) {
                const inicio = parseInt(fecha_inicio.split("-")[2], 10);
                const fin = parseInt(fecha_fin.split("-")[2], 10);

                for (let d = inicio; d <= fin; d++) {
                    diasMarcados.push(d);
                }
            }


            const hoy = new Date();
            const anio = hoy.getFullYear();
            const mes = hoy.getMonth();
            const diasEnMes = new Date(anio, mes + 1, 0).getDate();

            for (let dia = 1; dia <= diasEnMes; dia++) {
                const celda = fila.querySelector(`td[data-dia='${dia}']`);
                const valorSpan = celda ? celda.querySelector('.asistencia-valor') : null;
                let valor = valorSpan ? valorSpan.textContent.trim() : '';

                if ((!valor || valor === '') && diasMarcados.includes(dia)) {
                    valor = letraPorTipo[tipo_observacion] || null;
                }

                asistencia.push(valor || null);
            }

            docentes.push({
                dni,
                nombres,
                cargo,
                condicion,
                jornada,
                cod,
                asistencia,
                observacion,
                tipo_observacion,
                observacion_detalle,
                fecha_inicio,   
                fecha_fin     

            });
        });

        const payload = {
            id_contacto: {{ session('siic01.id_contacto') }},
            codlocal: "{{ session('siic01.conf_permisos')[0]['codlocal'] ?? '000000' }}",
            nivel: document.getElementById('nivel').value,
            numero_oficio: document.getElementById('oficio_guardado').value.trim(),
            numero_expediente: document.getElementById('campoNumeroExpediente').value.trim(),
            docentes: docentes
        };

        try {
            const response = await fetch('{{ route("guardar.reporte.masivo") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const text = await response.text();

            if (!response.ok) {
                console.error("Error HTTP:", response.status, text);
                alert("Error del servidor (" + response.status + "). Ver consola para detalles.");
                return;
            }

            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alert("Guardado exitoso.");
                } else {
                    console.warn("Guardado con advertencias:", data);
                    alert("Guardado con observaciones. Ver consola.");
                }
            } catch (e) {
                console.error("No se pudo parsear JSON:", text);
                alert("Guardado completado, pero la respuesta del servidor no fue JSON válido.");
            }

        } catch (error) {
            console.error("Error de red o excepción del fetch:", error);
            alert("Guardado exitoso 111");
        } finally {
            // Siempre ocultar loader y habilitar botón
            boton.disabled = false;
            boton.classList.remove('opacity-50', 'cursor-not-allowed');
            loader.classList.add('hidden');
        }
    });

    // guardar el tipo de observacion
    document.getElementById('tipo_observacion').addEventListener('change', function () {
        const esLicencia = this.value === 'Licencia';
        const esInasistencia = this.value === 'InasistenciaJustificada';
        const esPermiso = this.value === 'PermisoSinGoce';
        const esVacaciones = this.value === 'Vacaciones';
        const contenedor = document.getElementById('rangoFechasLicencia');
        const contenedor2 = document.getElementById('rangoFechasInasistencia');
        const contenedor3 = document.getElementById('rangoFechasPermisos');
        const contenedor4 = document.getElementById('rangoFechasVacaciones');

        if (esLicencia) {
            contenedor.classList.remove('hidden');
        } else {
            contenedor.classList.add('hidden');
            document.getElementById('fechaInicioLicencia').value = '';
            document.getElementById('fechaFinLicencia').value = '';
        }
        if (esInasistencia) {
            contenedor2.classList.remove('hidden');
        } else {
            contenedor2.classList.add('hidden');
            document.getElementById('fechaInicioInasistencia').value = '';
            document.getElementById('fechaFinInasistencia').value = '';
        }
        if (esPermiso) {
            contenedor3.classList.remove('hidden');
        } else {
            contenedor3.classList.add('hidden');
            document.getElementById('fechaInicioPermiso').value = '';
            document.getElementById('fechaFinPermiso').value = '';
        }
        if (esVacaciones) {
            contenedor4.classList.remove('hidden');
        } else {
            contenedor4.classList.add('hidden');
            document.getElementById('fechaInicioVacaciones').value = '';
            document.getElementById('fechaFinVacaciones').value = '';
        }
    });

    
    const fechaInicio = document.getElementById('fechaInicioLicencia');
    const fechaFin = document.getElementById('fechaFinLicencia');
    
    const contenedorFechas = document.getElementById('rangoFechasLicencia');

    const fechaInicioI = document.getElementById('fechaInicioInasistencia');
    const fechaFinI = document.getElementById('fechaFinInasistencia');
    const contenedorFechasI = document.getElementById('rangoFechasInasistencia');

    const fechaInicioP = document.getElementById('fechaInicioPermiso');
    const fechaFinP = document.getElementById('fechaFinPermiso');
    const contenedorFechasP = document.getElementById('rangoFechasPermisos');

    const fechaInicioV = document.getElementById('fechaInicioVacaciones');
    const fechaFinV = document.getElementById('fechaFinVacaciones');
    const contenedorFechasV = document.getElementById('rangoFechasVacaciones');

    const tipoObservacion = document.getElementById('tipo_observacion');
    const observacionTextarea = document.getElementById('observacion');

    // Mapa de tipos con sus inputs y contenedores
    const fechaConfig = {
        Licencia: {
            inicio: document.getElementById('fechaInicioLicencia'),
            fin: document.getElementById('fechaFinLicencia'),
            contenedor: document.getElementById('rangoFechasLicencia'),
            etiqueta: 'L'
        },
        InasistenciaJustificada: {
            inicio: document.getElementById('fechaInicioInasistencia'),
            fin: document.getElementById('fechaFinInasistencia'),
            contenedor: document.getElementById('rangoFechasInasistencia'),
            etiqueta: 'I'
        },
        PermisoSinGoce: {
            inicio: document.getElementById('fechaInicioPermiso'),
            fin: document.getElementById('fechaFinPermiso'),
            contenedor: document.getElementById('rangoFechasPermisos'),
            etiqueta: 'P'
        },
        Vacaciones: {
            inicio: document.getElementById('fechaInicioVacaciones'),
            fin: document.getElementById('fechaFinVacaciones'),
            contenedor: document.getElementById('rangoFechasVacaciones'),
            etiqueta: 'V'
        }
    };

    // Evento al cambiar tipo de observación
    tipoObservacion.addEventListener('change', function () {
        const tipo = this.value;

        // Mostrar solo el contenedor del tipo actual y ocultar los demás
        for (let key in fechaConfig) {
            fechaConfig[key].contenedor.classList.toggle('hidden', key !== tipo);
            if (key !== tipo) {
                fechaConfig[key].inicio.value = '';
                fechaConfig[key].fin.value = '';
                limpiarFechasEnObservacion(key);
            }
        }
    });

    // Escuchar cambios en fechas para todos los tipos
    for (let key in fechaConfig) {
        [fechaConfig[key].inicio, fechaConfig[key].fin].forEach(input => {
            input.addEventListener('change', function () {
                if (fechaConfig[key].inicio.value && fechaConfig[key].fin.value) {
                    insertarFechasEnObservacion(key);
                }
            });
        });
    }

    function insertarFechasEnObservacion(tipo) {
        const inicio = formatFecha(fechaConfig[tipo].inicio.value);
        const fin = formatFecha(fechaConfig[tipo].fin.value);

        limpiarFechasEnObservacion(tipo);

        if (inicio && fin) {
            observacionTextarea.value = observacionTextarea.value.trim() + 
                ` ${fechaConfig[tipo].etiqueta} del ${inicio} al ${fin}`;
        }
    }

    function limpiarFechasEnObservacion(tipo) {
        const etiqueta = fechaConfig[tipo].etiqueta;
        const regex = new RegExp(`\\${etiqueta} del .*? al .*?`, 'g');
        observacionTextarea.value = observacionTextarea.value.replace(regex, '').trim();
    }

    function formatFecha(fechaStr) {
        if (!fechaStr) return '';
        const [a, m, d] = fechaStr.split('-');
        return `${d}`;
    }

    const opciones = {
        InasistenciaJustificada: [
            "Licencia por incapacidad temporal",
            "Licencia por familiar directo con enfermedad grave o terminal o accidente grave",
            "Licencia por maternidad",
            "Licencia por paternidad",
            "Licencia por adopción",
            "Licencia por fallecimiento de padres, cónyuge o hijos",
            "Licencia por siniestros",
            "Licencia por estudios de posgrado, especialización o perfeccionamiento",
            "Licencia por capacitación organizada por el Minedu o gobiernos regionales",
            "Licencia por asumir representación oficial del Estado Peruano",
            "Licencia por citación expresa, judicial, militar o policial",
            "Licencia por representación sindical",
            "Licencia por desempeño de cargos de consejero regional o regidor municipal",
            "Licencia por asistencia médica y terapia de rehabilitación de personas con discapacidad",
            "Licencia para realizar exámenes oncológicos preventivos anuales",
            "Permiso por enfermedad",
            "Permiso por maternidad",
            "Permiso por lactancia",
            "Permiso por capacitación oficializada",
            "Permiso por citación expresa judicial, militar o policial",
            "Permiso por onomástico",
            "Permiso por el día del maestro",
            "Permiso para ejercer docencia superior o universitaria",
            "Permiso por representación sindical"
        ],
        Licencia: [
            "Licencia por motivos particulares",
            "Licencia por capacitación no oficializada",
            "Licencia por desempeño de funciones públicas por elección o asumir cargos políticos o de confianza",
            "Licencia por enfermedad grave de padres, cónyuge, conviviente reconocido judicialmente o hijos",
            "Conclusión anticipada de la licencia"
        ],
        PermisoSinGoce: [
            "Permiso por motivos particulares",
            "Permiso por capacitación no oficializada",
            "Permiso por enfermedad grave de padres, cónyuge, conviviente o hijos"
        ],
        Vacaciones: [
            "Disposiciones generales",
            "Programación de vacaciones",
            "Reprogramación de vacaciones",
            "Fraccionamiento del descanso vacacional",
            "Adelanto del descanso vacacional",
            "Vacaciones truncas"
        ],
        Cese: [
            "Cese por límite de edad",
            "Cese por invalidez",
            "Cese por fallecimiento"
        ],
        AbandonoCargo: [
            "Abandono injustificado de funciones"
        ],
        Encargatura: [
            "Cambio de colegio"
        ],
    };

    document.getElementById('tipo_observacion').addEventListener('change', function() {
        const tipo = this.value;
        const subSelect = document.getElementById('tipo_especifico');

        subSelect.innerHTML = "";
        if (opciones[tipo]) {
            subSelect.disabled = false;

            // 🔹 Opción placeholder inicial
            const placeholder = document.createElement('option');
            placeholder.value = "";
            placeholder.textContent = "-- Seleccione un detalle --";
            placeholder.selected = true;
            placeholder.disabled = true; // <- evita que lo guarden como válido
            subSelect.appendChild(placeholder);

            // 🔹 Agregar las opciones del tipo
            opciones[tipo].forEach(opt => {
                const option = document.createElement('option');
                option.value = opt;
                option.textContent = opt;
                subSelect.appendChild(option);
            });
        } else {
            subSelect.disabled = true;
            subSelect.innerHTML = '<option value="">-- Seleccione un tipo observación primero --</option>';
        }
    });

    document.getElementById('tipo_especifico').addEventListener('change', function () {
        const dni = dniActual; 
        const cod = codActual; 
        const fila = document.querySelector(`tr[data-dni="${dni}"][data-cod="${cod}"]`);
        if (fila) {
            fila.setAttribute('data-observacion-detalle', this.options[this.selectedIndex].textContent.trim());
        }
    });
    
    document.querySelectorAll("#fechaInicioLicencia, #fechaFinLicencia, #fechaInicioInasistencia, #fechaFinInasistencia, #fechaInicioPermiso, #fechaFinPermiso, #fechaInicioVacaciones, #fechaFinVacaciones")
        .forEach(input => {
            input.addEventListener("change", function() {
                const fila = document.querySelector(`tr[data-dni="${dniActual}"][data-cod="${codActual}"]`);
                if (fila) {
                    if (this.id.includes("Inicio")) {
                        fila.setAttribute("data-fecha-inicio", this.value);
                    } else if (this.id.includes("Fin")) {
                        fila.setAttribute("data-fecha-fin", this.value);
                    }
                }
            });
    });

</script>


@endsection
