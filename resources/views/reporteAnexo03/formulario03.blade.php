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
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">ANEXO 03 - {{ mb_strtoupper(\Carbon\Carbon::now()->translatedFormat('F '), 'UTF-8') }}</h1>
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
                        $feriados = ['2025-07-28','2025-07-29','2025-08-06','2025-08-30'];
                        $diasSemana = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];
                        $fechaHoy = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
                    @endphp

                    <thead class="bg-gray-200 text-gray-700 uppercase text-xs sticky top-0 z-10">
                        <tr>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">Nº</th>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">
                                DNI
                            </th>

                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">Apellidos y Nombres</th>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">Cargo</th>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">Condición</th>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">Jor. Lab.</th>
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
                    </thead>

                    <tbody class="bg-white" >
                        @forelse ($registros as $index => $r)
                                @php
                                    $clave = $r->dni . '_' . $r->cod;
                                    $asistenciaPersona = $asistencias[$clave] ?? null;
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
                                        @endif
                                    >
                                        <td class="border px-2 py-1">{{ $index + 1 }}</td>
                                        <td class="border px-2 py-1  text-blue-500 dni-tour dni-tour-clickable"
                                            onclick="openModal('{{ $r->dni }}', '{{ $r->nombres }}', '{{ $r->cod }}')"
                                            data-step="2">
                                            {{ $r->dni }}
                                        </td>


                                        <td class="border px-2 py-1 text-left">{{ $r->nombres }}</td>
                                        <td class="border px-2 py-1">{{ $r->cargo }}</td>
                                        <td class="border px-2 py-1">{{ $r->condicion }}</td>
                                        <td class="border px-2 py-1">{{ $r->jornada }}</td>
                                    @php
                                        $clave = $r->dni . '_' . $r->cod;
                                        $asistenciaPersona = $asistencias[$clave] ?? null;
                                        $observacion = $asistenciaPersona['observacion'] ?? null;
                                    @endphp

                                    @php
                                        $tipoObservacion = $asistenciaPersona['tipo_observacion'] ?? null;
                                        $observacion = $asistenciaPersona['observacion'] ?? null;

                                        $d = 1;
                                        $asistencia = $asistenciaPersona['asistencia'] ?? [];

                                        $todoVacio = empty($asistencia) || collect($asistencia)->every(fn($v) => is_null($v) || $v === '');
                                        
                                        if ($todoVacio && empty($observacion) && empty($tipoObservacion)) {
                                            for ($d = 1; $d <= $diasEnMes; $d++) {
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

                                                echo '<td class="border px-1 py-1 text-sm asistencia-celda text-center ' . $claseFondo . '"
                                                        data-id="' . $r->dni . '" data-dia="' . $d . '">
                                                        <span class="asistencia-valor ' . $claseTexto . '">' . $valor . '</span>
                                                    </td>';
                                            }
                                            continue;
                                        }
                                        // Si hay observación pero no hay asistencia registrada, mostrar la observación en un solo <td>
                                        if ($todoVacio && (!empty($observacion) || !empty($tipoObservacion))) {
                                            echo '<td colspan="' . $diasEnMes . '" style="text-align: center;
                                                color: #dc2626;
                                                font-weight: 600;
                                                font-style: italic;
                                                background-color: #fef2f2;
                                                padding: 4px;
                                                font-size: 0.875rem;">' . ($observacion ?? 'Sin asistencia registrada') . '</td>';
                                            continue;
                                        }
                                    @endphp

                                    @while ($d <= $diasEnMes)
                                        @php
                                            $valor = $asistencia[$d - 1] ?? null;
                                        @endphp

                                        {{-- Detectar inicio de bloque de licencia --}}
                                        @if ($valor === 'L')
                                            @php
                                                $inicio = $d;
                                                $fin = $d;

                                                for ($j = $d + 1; $j <= $diasEnMes; $j++) {
                                                    $valorJ = $asistencia[$j - 1] ?? null;

                                                    if ($valorJ === 'L' || $valorJ === null) {
                                                        $fin = $j;
                                                    } else {
                                                        break;
                                                    }
                                                }

                                                $colspan = $fin - $inicio + 1;
                                            @endphp

                                            <td colspan="{{ $colspan }}"
                                                class="text-center text-red-600 font-semibold italic bg-red-50 border px-1 py-1 text-sm">
                                                {{ $observacion ?? 'Licencia' }}
                                            </td>

                                            @php
                                                $d = $fin + 1; // saltamos al siguiente después del último combinado
                                            @endphp
                                            @continue
                                        @endif

                                        {{-- Celda normal --}}
                                        @php
                                            $fecha = Carbon::create($anio, $mes, $d);
                                            $fechaActual = $fecha->format('Y-m-d');
                                            $nombreDia = $diasSemana[$fecha->dayOfWeek];
                                            $valor = $asistencia[$d - 1] ?? null;

                                            if (in_array($fechaActual, $feriados)) {
                                                $valor = 'F'; // sobreescribe valor si es feriado
                                            }

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
                                        @php $d++; @endphp
                                    @endwhile
                                </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 6 + $diasEnMes }}" class="text-center py-2">No hay registros disponibles.</td>
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
                <img id="firmaPreview" src="{{ asset('storage/firmasdirector/' . $firmaGuardada) }}" alt="Firma"
                    class="mt-2" style="height: 80px;">
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

            <!-- Tipo de Observación -->
            <div class="mt-4" data-step='7'>
                <label for="tipo_observacion" class="block text-sm font-semibold mb-1">Tipo de Observación:</label>
                <select id="tipo_observacion" name="tipo_observacion" class="w-full border rounded p-2 text-sm">
                    <option value="" selected>-- Seleccione --</option>
                    <option value="Cese">Cese</option>
                    <option value="Renuncia">Renuncia</option>
                    <option value="Licencia">Licencia</option>
                    <option value="Abandono de Cargo">Abandono de Cargo</option>
                    <option value="Vacaciones">Vacaciones</option>
                </select>
            </div>

            <!-- Rango de Fechas para Licencia -->
            <div id="rangoFechasLicencia" class="mt-4 hidden">
                <label class="block text-sm font-semibold mb-1">Rango de Fechas de Licencia:</label>
                <div class="flex gap-2">
                    <input type="date" id="fechaInicioLicencia" class="w-full border rounded p-2 text-sm">
                    <input type="date" id="fechaFinLicencia" class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <!-- Observación -->
            <div class="mt-4" data-step='9'>
                <label for="observacion" class="block text-sm font-semibold mb-1">Observación:</label>
                <textarea id="observacion" rows="2" class="w-full border rounded p-2 text-sm" placeholder="Ej. Cese Voluntario, Renuncia, etc."></textarea>
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
                <img src="https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExZHMxN3dpajBybDE5cnV5bjJxZXV1NXl2cWRkeWk0Zmgyb3VneW11eiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/E6jscXfv3AkWQ/giphy.gif"
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




const feriados = ['2025-07-28','2025-07-29','2025-08-06','2025-08-30']; 
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

        for (let dia = 1; dia <= diasEnMes; dia++) {
            const fecha = new Date(anio, mes - 1, dia);
            const diaSemana = fecha.getDay();
            const fechaStr = fecha.toISOString().slice(0, 10);
            const esFeriado = feriados.includes(fechaStr);
            const isFinSemana = (diaSemana === 0 || diaSemana === 6);

            filaNumeros.innerHTML += `<th class="border px-1">${dia}</th>`;
            filaLetras.innerHTML += `<th class="border px-1">${diasSemana[diaSemana]}</th>`;

            // Buscar valor actual en la tabla principal
            const fila = document.querySelector(`tr[data-dni="${dni}"][data-cod="${cod}"]`);
            const celda = fila ? fila.querySelector(`.asistencia-celda[data-id="${dni}"][data-dia="${dia}"] .asistencia-valor`) : null;

            let valor = celda ? celda.textContent.trim().toUpperCase() : '';

            // Si es feriado, forzar valor "F"
            if (esFeriado) valor = 'F';

            if (isFinSemana) {
                filaInputs.innerHTML += `
                    <td class="border px-1 bg-gray-100">
                        <input 
                            type="text" 
                            class="w-10 text-center bg-transparent focus:outline-none" 
                            readonly 
                        />
                    </td>`;
            } else {
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

        // Limpiar checkboxes de patrón
        document.querySelectorAll('.dia-patron').forEach(cb => cb.checked = false);

        // Recuperar observación y tipo de observación si existen en el <tr>
        const fila = document.querySelector(`tr[data-dni="${dni}"]`);
        if (fila) {
            const obs = fila.getAttribute('data-observacion') || '';
            const tipoObs = fila.getAttribute('data-tipo-observacion') || '';

            document.getElementById('observacion').value = obs;
            document.getElementById('tipo_observacion').value = tipoObs;
        }
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
                fila.querySelectorAll('td:not([data-dia]):not(:nth-child(-n+6))').forEach(td => td.remove());

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

        if (tipoObservacion === 'Licencia') {
            const fInicio = document.getElementById('fechaInicioLicencia').value;
            const fFin = document.getElementById('fechaFinLicencia').value;

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
                alert("Debe indicar el rango de fechas para la licencia.");
                return;
            }

        } else if (observacion !== '') {
            let celda = fila.children[6];
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
    document.getElementById('guardarTodo').addEventListener('click', function () {
        // const numeroOficio = document.getElementById('oficio_guardado').value.trim();
        // const numeroExpediente = document.getElementById('campoNumeroExpediente').value.trim();

        // if (!numeroOficio || !numeroExpediente) {
        //     alert("Debe ingresar el número de oficio y expediente antes de guardar.");
        //     return;
        // }

        const filas = document.querySelectorAll('tr[data-dni]');
        
        const docentes = [];

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

            const celdaObservacion = fila.querySelector('td[colspan]');
            if (celdaObservacion) {
                observacion = celdaObservacion.textContent.trim();
            }

            // Paso 1: Días con licencia
            let diasLicencia = [];
            if (tipo_observacion === "Licencia" && observacion) {
                const match = observacion.match(/del\s+(\d{1,2})\s+al\s+(\d{1,2})/i);
                if (match) {
                    const inicio = parseInt(match[1], 10);
                    const fin = parseInt(match[2], 10);
                    for (let d = inicio; d <= fin; d++) {
                        diasLicencia.push(d);
                    }
                }
            }

            // Paso 2: Obtener número de días del mes actual
            const hoy = new Date();
            const anio = hoy.getFullYear();
            const mes = hoy.getMonth(); // Ej. 5 para junio

            const diasEnMes = new Date(anio, mes + 1, 0).getDate(); // Ej. 30

            // Leer asistencia directamente desde el DOM
            for (let dia = 1; dia <= diasEnMes; dia++) {
                const celda = fila.querySelector(`td[data-dia='${dia}']`);
                const valorSpan = celda ? celda.querySelector('.asistencia-valor') : null;
                let valor = valorSpan ? valorSpan.textContent.trim() : '';

                if ((!valor || valor === '') && diasLicencia.includes(dia)) {
                    valor = 'L'; // Forzar valor 'L' si está dentro del rango de licencia
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
                tipo_observacion
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

        fetch('{{ route("guardar.reporte.masivo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const text = await response.text();

            if (!response.ok) {
                console.error("Error HTTP:", response.status, text);
                alert("Error del servidor (" + response.status + "). Ver consola para detalles.");
                //location.reload();
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
            //location.reload();
        })
        .catch(error => {
            console.error("Error de red o excepción del fetch:", error);
            alert("Guardado exitoso 111");
            return false;

        });
    });

    // guardar el tipo de observacion
    document.getElementById('tipo_observacion').addEventListener('change', function () {
        const esLicencia = this.value === 'Licencia';
        const contenedor = document.getElementById('rangoFechasLicencia');

        if (esLicencia) {
            contenedor.classList.remove('hidden');
        } else {
            contenedor.classList.add('hidden');
            document.getElementById('fechaInicioLicencia').value = '';
            document.getElementById('fechaFinLicencia').value = '';
        }
    });


const tipoObservacion = document.getElementById('tipo_observacion');
const fechaInicio = document.getElementById('fechaInicioLicencia');
const fechaFin = document.getElementById('fechaFinLicencia');
const observacionTextarea = document.getElementById('observacion');
const contenedorFechas = document.getElementById('rangoFechasLicencia');


    tipoObservacion.addEventListener('change', function () {
        const isLicencia = this.value === 'Licencia';
        contenedorFechas.classList.toggle('hidden', !isLicencia);

        if (!isLicencia) {
            fechaInicio.value = '';
            fechaFin.value = '';
            limpiarFechasEnObservacion();
        }
        });

        [fechaInicio, fechaFin].forEach(input => {
            input.addEventListener('change', function () {
                if (fechaInicio.value && fechaFin.value) {
                    insertarFechasEnObservacion();
                }
            });
        });


        function insertarFechasEnObservacion() {
        const inicio = formatFecha(fechaInicio.value);
        const fin = formatFecha(fechaFin.value);

        limpiarFechasEnObservacion();

        if (inicio && fin) {
            observacionTextarea.value = observacionTextarea.value.trim() + ` L del ${inicio} al ${fin}`;
        }
    }

    function limpiarFechasEnObservacion() {
        observacionTextarea.value = observacionTextarea.value.replace(/\[Licencia del .*? al .*?\]/g, '').trim();
    }

    function formatFecha(fechaStr) {
        const [a, m, d] = fechaStr.split('-');
        return `${d}`;
    }

</script>


@endsection
