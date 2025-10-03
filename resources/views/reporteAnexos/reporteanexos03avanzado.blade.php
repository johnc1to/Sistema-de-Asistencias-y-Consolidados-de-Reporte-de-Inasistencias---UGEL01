@extends('layout_especialista/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak]{
        display:none !important;
    }
</style>
<!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <nav class="flex text-sm mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li>
            <a href="listar_Contactos" class="inline-flex items-center text-gray-600 hover:text-blue-600">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3.293l6 6V17h-4v-4H8v4H4v-7.707l6-6z"/></svg>
                Inicio
            </a>
            </li>
            <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 7l5 5-5 5V7z"/></svg>
                <span class="text-gray-500">Reportes</span>
            </div>
            </li>
            <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 7l5 5-5 5V7z"/></svg>
                <span class="text-gray-500"><strong>Reporte Anexo 03</strong></span>
            </div>
            </li>
        </ol>
    </nav>
    <div class="flex flex-wrap gap-4 mb-6">
        @if($directoresSinAnexo03->count())
            <div x-data="{ open: false }" class="flex-1 min-w-[250px]">
                <button 
                    @click="open = true"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hover:bg-red-200 transition w-full text-left"
                >
                    <strong class="font-bold">¡Atención! 📢</strong>
                    <span class="block sm:inline">
                        {{ $directoresSinAnexo03->count() }} directores aún no reportan su Anexo 03. Haz clic para ver la lista.
                    </span>
                </button>

                <!-- Modal -->
                <div 
                    x-show="open" 
                    x-transition 
                    x-cloak
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div 
                        @click.away="open = false" 
                        class="bg-white rounded-lg shadow-lg w-full max-w-5xl max-h-[90vh] overflow-hidden"
                    >
                        <div class="p-6 border-b sticky top-0 bg-white z-10">
                            <div class="flex justify-between items-center">
                                <h2 class="text-2xl font-bold text-red-600">Directores que no han reportado Anexo 03</h2>
                                <button 
                                    @click="open = false"
                                    class="text-gray-500 hover:text-gray-700 text-2xl font-bold"
                                >
                                    &times;
                                </button>
                            </div>
                        </div>

                        <!-- Tabla con scroll -->
                        <div class="overflow-y-auto max-h-[65vh]">
                            <table class="min-w-full text-sm divide-y divide-gray-200">
                                <thead class="bg-gray-100 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium">DNI</th>
                                        <th class="px-4 py-2 text-left font-medium">Nombres</th>
                                        <th class="px-4 py-2 text-left font-medium">Celular</th>
                                        <th class="px-4 py-2 text-left font-medium">Institución</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($directoresSinAnexo03 as $dir)
                                        <tr>
                                            <td class="px-4 py-2">{{ $dir->dni_director }}</td>
                                            <td class="px-4 py-2">{{ $dir->director }}</td>
                                            <td class="px-4 py-2">{{ $dir->celular_pers }}</td>
                                            <td class="px-4 py-2">{{ $dir->institucion }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-right p-4 border-t">
                            <button 
                                @click="open = false"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($directoresEnProceso->count())
                <div x-data="{ open: false }" class="flex-1 min-w-[250px]">
                    <button 
                        @click="open = true"
                        class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded hover:bg-yellow-200 transition w-full text-left"
                    >
                        <strong class="font-bold">¡Atención! 📢</strong>
                        <span class="block sm:inline">
                            {{ $directoresEnProceso->count() }} directores que estan en proceso su Anexo 03. Haz clic para ver la lista.
                        </span>
                    </button>
                    <!-- Modal -->
                    <div 
                        x-show="open" 
                        x-transition 
                        x-cloak
                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                    >
                        <div 
                            @click.away="open = false" 
                            class="bg-white rounded-lg shadow-lg w-full max-w-5xl max-h-[90vh] overflow-hidden"
                        >
                            <div class="p-6 border-b sticky top-0 bg-white z-10">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-2xl font-bold text-yellow-600">Directores que estan en proceso Anexo 03</h2>
                                    <button 
                                        @click="open = false"
                                        class="text-gray-500 hover:text-gray-700 text-2xl font-bold"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <!-- Tabla con scroll -->
                            <div class="overflow-y-auto max-h-[65vh]">
                                <table class="min-w-full text-sm divide-y divide-gray-200">
                                    <thead class="bg-gray-100 sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium">DNI</th>
                                            <th class="px-4 py-2 text-left font-medium">Nombres</th>
                                            <th class="px-4 py-2 text-left font-medium">Celular</th>
                                            <th class="px-4 py-2 text-left font-medium">Institución</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($directoresEnProceso as $dir)
                                            <tr>
                                                <td class="px-4 py-2">{{ $dir->dni_director }}</td>
                                                <td class="px-4 py-2">{{ $dir->director }}</td>
                                                <td class="px-4 py-2">{{ $dir->celular_pers }}</td>
                                                <td class="px-4 py-2">{{ $dir->institucion }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-right p-4 border-t">
                                <button 
                                    @click="open = false"
                                    class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700"
                                >
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
        @if($directoresCompletos->count())
                <div x-data="{ open: false }" class="flex-1 min-w-[250px]">
                    <button 
                        @click="open = true"
                        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hover:bg-green-200 transition w-full text-left"
                    >
                        <strong class="font-bold">¡Atención! 📢</strong>
                        <span class="block sm:inline">
                            {{ $directoresCompletos->count() }} directores que completaron con éxito su Anexo 03. Haz clic para ver la lista.
                        </span>
                    </button>
                    <!-- Modal -->
                    <div 
                        x-show="open" 
                        x-transition 
                        x-cloak
                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                    >
                        <div 
                            @click.away="open = false" 
                            class="bg-white rounded-lg shadow-lg w-full max-w-5xl max-h-[90vh] overflow-hidden"
                        >
                            <div class="p-6 border-b sticky top-0 bg-white z-10">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-2xl font-bold text-green-600">Directores que completaron con éxito Anexo 03</h2>
                                    <button 
                                        @click="open = false"
                                        class="text-gray-500 hover:text-gray-700 text-2xl font-bold"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <!-- Tabla con scroll -->
                            <div class="overflow-y-auto max-h-[65vh]">
                                <table class="min-w-full text-sm divide-y divide-gray-200">
                                    <thead class="bg-gray-100 sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium">DNI</th>
                                            <th class="px-4 py-2 text-left font-medium">Nombres</th>
                                            <th class="px-4 py-2 text-left font-medium">Celular</th>
                                            <th class="px-4 py-2 text-left font-medium">Institución</th>
                                            <th class="px-4 py-2 text-left font-medium">Expediente</th>
                                            <th class="px-4 py-2 text-left font-medium">PDF</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($directoresCompletos as $dir)
                                            <tr>
                                                <td class="px-4 py-2">{{ $dir->dni_director }}</td>
                                                <td class="px-4 py-2">{{ $dir->director }}</td>
                                                <td class="px-4 py-2">{{ $dir->celular_pers }}</td>
                                                <td class="px-4 py-2">{{ $dir->institucion }}</td>
                                                <td class="px-4 py-2">{{ $dir->expediente }}</td>
                                                <td class="px-4 py-2">
                                                    @if($dir->ruta_pdf)
                                                        <a href="{{ asset('storage/'.$dir->ruta_pdf) }}" target="_blank" class="text-blue-600 underline">
                                                            📄 Ver PDF
                                                        </a>
                                                    @else
                                                        ❌ Sin PDF
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-right p-4 border-t">
                                <button 
                                    @click="open = false"
                                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                                >
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    </div>

    <div class="w-full max-w-6xl mx-auto bg-white rounded-xl shadow-md p-4 mt-6">
        <h2 class="text-lg font-semibold mb-3">Filtros de búsqueda</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <!-- Institución -->
            <select id="filtroInstitucion" class="border rounded-lg px-2 py-1">
                <option value="">Institución</option>
                @foreach($reportes->pluck('institucion')->unique() as $inst)
                    <option value="{{ strtolower($inst) }}">{{ $inst }}</option>
                @endforeach
            </select>

            <!-- Distrito -->
            <select id="filtroDistrito" class="border rounded-lg px-2 py-1">
                <option value="">Distrito</option>
                @foreach($reportes->pluck('distrito')->unique() as $dist)
                    <option value="{{ strtolower($dist) }}">{{ $dist }}</option>
                @endforeach
            </select>

            <!-- Nivel -->
            <select id="filtroNivel" class="border rounded-lg px-2 py-1">
                <option value="">Nivel</option>
                @foreach($reportes->pluck('nivel')->unique() as $niv)
                    <option value="{{ strtolower($niv) }}">{{ $niv }}</option>
                @endforeach
            </select>

            <!-- Observacion -->
            <select id="filtroObservacion" class="border rounded-lg px-2 py-1">
                <option value="">Observacion</option>
                @foreach($observacionesCriticas->pluck('tipo_observacion')->unique() as $obs)
                    <option value="{{ strtolower($obs) }}">{{ $obs }}</option>
                @endforeach
            </select>

            <!-- Búsqueda libre -->
            <input type="text" id="filtroTexto" placeholder="🔎 DNI, Nombre, etc." 
                class="border rounded-lg px-2 py-1" />
        </div>
    </div>
   

    <div class="mt-4">
        <div class="flex border-b">
            <button id="btnReporte" class="px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600">
                📄 Reporte General
            </button>
            <button id="btnObservaciones" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                ⚠️ Procedimientos Críticas
            </button>
            <button id="btnBloque" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                📊 Estadistica
            </button>
            <button id="btnReporte2" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                📄 Reporte General por Mes
            </button>
            <button id="btnPowerBI" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                📊 Power BI
            </button>
        </div>
        
        <div id="tablaReporte" class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 block">

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded">
                <p class="font-bold text-blue-700">
                    Cumplimiento general ({{ $anioMes }}) vs ({{ $anioMesAnterior }}):
                </p>
                <p>
                    <span class="text-gray-700">Actual ({{ $anioMes }}):</span>
                    Total docentes <span class="font-semibold">{{ $cumplimiento->sum('total_docentes') }}</span> |
                    Con procedimiento <span class="text-red-600 font-semibold">{{ $cumplimiento->sum('docentes_con_observacion') }}</span>
                </p>
                <p>
                    <span class="text-gray-700">Anterior ({{ $anioMesAnterior }}):</span>
                    Total docentes <span class="font-semibold">{{ $cumplimientoAnterior->sum('total_docentes') }}</span> |
                    Con procedimiento <span class="text-red-600 font-semibold">{{ $cumplimientoAnterior->sum('docentes_con_observacion') }}</span>
                </p>    
            </div>

            <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte de Cumplimiento del Anexo 03 de Asistencia Detallado    </h1>
            
            @if($reportes->isEmpty())
                <div class="text-center text-red-600 font-semibold mt-4">
                    No se encontraron registros. Los directores no han reportado o no han seguido el procedimiento.
                </div>
        
            @else
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full border rounded-xl text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs text-center">
                            <tr>
                                <th class="px-2 py-1 border whitespace-nowrap">N°</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Número Oficio</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Expediente</th>
                                <th class="px-2 py-1 border whitespace-nowrap">DNI</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Nombre Completo</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Celular</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Codlocal</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Codmod</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Institución</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Nivel</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Correo Institucional</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Distrito</th>
                                <th class="px-2 py-1 border whitespace-nowrap">Fecha Creación</th>
                                <th class="px-2 py-1 border whitespace-nowrap">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="text-center" id="tablaReporteBody">
                            @foreach($reportes as $index => $reporte)
                                <tr class="hover:bg-gray-50"
                                    data-institucion="{{ strtolower($reporte->institucion) }}"
                                    data-distrito="{{ strtolower($reporte->distrito) }}"
                                    data-nivel="{{ strtolower($reporte->nivel) }}"
                                    data-texto="{{ strtolower($reporte->dni.' '.$reporte->nombres.' '.$reporte->apellipat.' '.$reporte->apellimat.' '.$reporte->institucion) }}"
                                    >
                                    <td class="px-2 py-1 border">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->oficio }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->expediente }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->dni }}</td>
                                    <td class="px-2 py-1 border whitespace-nowrap">{{ $reporte->nombres }} {{ $reporte->apellipat }} {{ $reporte->apellimat }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->celular_pers }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->codlocal }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->codmod }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->institucion }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->nivel }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->correo_inst }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->distrito }}</td>
                                    <td class="px-2 py-1 border">{{ \Carbon\Carbon::parse($reporte->fecha_creacion)->format('d/m/Y H:i') }}</td>
                                    <td class="px-2 py-1 border">
                                        @if($reporte->ruta_pdf)
                                            <a href="{{ asset('storage/'.$reporte->ruta_pdf) }}" target="_blank" class="text-blue-600 underline">
                                                📄 Ver PDF
                                            </a>
                                        @else
                                            <span class="text-gray-400">No disponible</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @php
            use Carbon\Carbon;
            $mesActualLabel = Carbon::parse($anioMes . '-01')->translatedFormat('F Y'); 
            $mesAnteriorLabel = Carbon::parse($anioMesAnterior . '-01')->translatedFormat('F Y');
        @endphp

        @if($observacionesCriticas->count())
            <div id="tablaObservaciones" class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 hidden">
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4 rounded">
                    <p class="font-bold text-yellow-700 mb-2">
                        📊 Cuadro comparativo de procedimiento del mes actual al anterior:
                    </p>

                    <table class="text-sm w-auto">
                        <thead>
                            <tr class="text-left">
                                <th class="pr-4">Tipo</th>
                                <th class="pr-4 font-bold">{{ ucfirst($mesActualLabel) }}</th>
                                <th class="font-bold">{{ ucfirst($mesAnteriorLabel) }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_unique(array_merge(
                                $observacionesPorTipo->keys()->toArray(),
                                $observacionesPorTipoAnterior->keys()->toArray()
                            )) as $tipo)
                                <tr>
                                    <td class="font-semibold pr-4">{{ $tipo }}</td>
                                    <td class="text-red-600 font-bold pr-4">{{ $observacionesPorTipo[$tipo] ?? 0 }}</td>
                                    <td class="text-gray-600 font-bold">{{ $observacionesPorTipoAnterior[$tipo] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                    <h2 class="text-xl font-bold text-red-600 mb-4">⚠️ Alertas de Docentes con Procedimientos Críticas</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border rounded-xl text-sm">
                            <thead class="bg-red-100 text-red-800 uppercase text-xs text-center">
                                <tr>
                                    <th class="px-2 py-1 border">N°</th>
                                    <th class="px-2 py-1 border">Oficio</th>
                                    <th class="px-2 py-1 border">Expediente</th>
                                    <th class="px-2 py-1 border">DNI</th>
                                    <th class="px-2 py-1 border">Nombre</th>
                                    <th class="px-2 py-1 border">Cargo</th>
                                    <th class="px-2 py-1 border">Condición</th>
                                    <th class="px-2 py-1 border">Tipo Procedimiento</th>
                                    <th class="px-2 py-1 border">Observación</th>
                                    <th class="px-2 py-1 border">Observación Detalle</th>
                                    <th class="px-2 py-1 border">Nivel</th>
                                    <th class="px-2 py-1 border">RED</th>
                                    <th class="px-2 py-1 border">IIEE</th>
                                    <th class="px-2 py-1 border">Distrito</th>
                                    <th class="px-2 py-1 border">Fecha Reporte</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($observacionesCriticas as $item)
                                    <tr class="hover:bg-red-50">
                                        <td class="px-2 py-1 border">{{ $loop->iteration }}</td>
                                        <td class="px-2 py-1 border"><strong>{{ $item->oficio }}</strong></td>
                                        <td class="px-2 py-1 border"><strong>{{ $item->expediente }}</strong></td>
                                        <td class="px-2 py-1 border">{{ $item->dni }}</td>
                                        <td class="px-2 py-1 border">{{ $item->nombres }}</td>
                                        <td class="px-2 py-1 border">{{ $item->cargo }}</td>
                                        <td class="px-2 py-1 border">{{ $item->condicion }}</td>
                                        <td class="px-2 py-1 border font-semibold text-red-600">{{ $item->tipo_observacion }}</td>
                                        <td class="px-2 py-1 border">{{ $item->observacion }}</td>
                                        <td class="px-2 py-1 border">{{ $item->observacion_detalle }}</td>
                                        <td class="px-2 py-1 border">{{ $item->nivel }}</td>
                                        <td class="px-2 py-1 border">{{ $item->red_iiee }}</td>
                                        <td class="px-2 py-1 border text-blue-600 underline cursor-pointer nombre-colegio"
                                            data-lat="{{ $item->latitud }}"
                                            data-lng="{{ $item->longitud }}"
                                            data-institucion="{{ $item->institucion }}"
                                            data-distrito="{{ $item->distrito_iiee }}"
                                            data-dni="{{ $item->dni }}"
                                            data-nombres="{{ $item->nombres }}"
                                            data-cargo="{{ $item->cargo }}"
                                            data-condicion="{{ $item->condicion }}"
                                            data-tipo-observacion="{{ $item->tipo_observacion }}"
                                            data-observacion="{{ $item->observacion }}"
                                            data-observacion-detalle="{{ $item->observacion_detalle }}"
                                            data-nivel="{{ $item->nivel }}"
                                            data-red="{{ $item->red_iiee }}"
                                            data-oficio="{{ $item->oficio }}"
                                            data-expediente="{{ $item->expediente }}"
                                            data-fecha="{{ \Carbon\Carbon::parse($item->fecha_creacion)->format('d/m/Y') }}"
                                            >
                                            {{ $item->institucion }}</td>
                                        <td class="px-2 py-1 border">{{ $item->distrito_iiee }}</td>
                                        <td class="px-2 py-1 border">{{ \Carbon\Carbon::parse($item->fecha_creacion)->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($observacionesCriticas->count())
                            <div class="flex justify-end mt-3">
                                <form method="GET" action="{{ route('reporte.observaciones.pdf') }}" target="_blank">
                                    {{-- Pasamos todos los filtros activos --}}
                                    <input type="hidden" name="institucion" id="inputInstitucion">
                                    <input type="hidden" name="distrito" id="inputDistrito">
                                    <input type="hidden" name="nivel" id="inputNivel">
                                    <input type="hidden" name="observacion" id="inputObservacion">
                                    <input type="hidden" name="texto" id="inputTexto">

                                    <button type="submit" 
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                        📄 Exportar PDF
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        <div id="tablaBloque" class="hidden p-6">
            <h1 class="text-xl font-bold text-center mb-4">
                📊 Resumen Estadístico de Bloques de Semanas de Gestión y Lectivas
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                <!-- Tabla -->
                <div class="bg-white shadow rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Detalle de colegios</h2>
                    <div class="overflow-y-auto max-h-[500px]">
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="bg-green-100 p-4 rounded-lg text-center">
                                <h3 class="text-xl font-bold">{{ $cumplimiento->where('cumplimiento', 100)->count() }}</h3>
                                <p class="text-sm">Colegios al 100%</p>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-lg text-center">
                                <h3 class="text-xl font-bold">{{ $cumplimiento->where('cumplimiento', '>=', 80)->where('cumplimiento','<',100)->count() }}</h3>
                                <p class="text-sm">Colegios 80–99%</p>
                            </div>
                            <div class="bg-red-100 p-4 rounded-lg text-center">
                                <h3 class="text-xl font-bold">{{ $cumplimiento->where('cumplimiento','<',80)->count() }}</h3>
                                <p class="text-sm">Colegios < 80% </p>
                            </div>
                        </div>
                        <!-- tabla cumplimiento -->
                        <div class="mb-4">
                            <button onclick="filtrar(100)" class="px-3 py-1 bg-green-500 text-white rounded">Solo 100%</button>
                            <button onclick="filtrar(99)" class="px-3 py-1 bg-red-500 text-white rounded">Menos de 100%</button>
                            <button onclick="filtrar(0)" class="px-3 py-1 bg-gray-500 text-white rounded">Todos</button>
                        </div>    
                        <table id="tablaCumplimiento" class="table-auto w-full text-sm">
                            <thead class="sticky top-0 bg-gray-100">
                                <tr>
                                    <th>Distrito</th>
                                    <th>Institución</th>
                                    <th>% Cumplimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cumplimiento as $c)
                                    <tr data-cumplimiento="{{ $c['cumplimiento'] }}">
                                        <td>{{ $c['distrito'] }}</td>
                                        <td>
                                            <span 
                                                class="text-black-600 "
                                                >
                                                {{ $c['nombre_ie'] }}
                                            </span>
                                        </td>
                                        <td class="@if($c['cumplimiento']==100) bg-green-200
                                                @elseif($c['cumplimiento']>=80) bg-yellow-200
                                                @else bg-red-200 @endif">
                                            {{ $c['cumplimiento'] }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="modalObservaciones" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-6xl p-6">
                    <h2 id="tituloModal" class="text-lg font-semibold mb-4"></h2>

                    <h2 class="text-xl font-bold mb-4 text-center">
                        MES DE {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('F')) }}
                    </h2>

                    <div class="overflow-auto max-h-[80vh] border rounded">
                        
                        <table class="min-w-[1500px] w-full text-sm border-collapse">
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

                                if (!function_exists('obtenerTipoSemana')) {
                                    function obtenerTipoSemana($fecha, $bloques, $feriados) {
                                        if ($fecha->isWeekend() || in_array($fecha->format('Y-m-d'), $feriados)) {
                                            return null;
                                        }

                                        foreach ($bloques as $bloque) {
                                            if ($fecha->between(\Carbon\Carbon::parse($bloque['inicio']), \Carbon\Carbon::parse($bloque['fin']))) {
                                                return $bloque['tipo'];
                                            }
                                        }

                                        return null;
                                    }
                                }
                            @endphp

                            @php
                                $mes = $mes ?? \Carbon\Carbon::now()->month;
                                $anio = $anio ?? \Carbon\Carbon::now()->year;
                                $diasEnMes = \Carbon\Carbon::create($anio, $mes, 1)->daysInMonth;
                                $feriados = ['2025-07-28','2025-07-29','2025-08-06','2025-08-30','2025-10-08' ,'2025-11-01','2025-12-08','2025-12-09','2025-12-25','2025-12-26'];
                                for ($d = 1; $d <= $diasEnMes; $d++) {
                                    $fecha = \Carbon\Carbon::create($anio, $mes, $d);
                                    $patronDias[$d] = obtenerTipoSemana($fecha, $bloques, $feriados); 
                                }
                            @endphp

                            <thead>
                                <tr>
                                    <th class="border px-2 py-1 bg-gray-200" rowspan="3">Nº</th>
                                    <th class="border px-2 py-1 bg-gray-200 w-24 text-center" rowspan="3">DNI</th>
                                    <th class="border px-2 py-1 bg-gray-200" rowspan="3">Apellidos y Nombres</th>
                                    <th class="border px-2 py-1 bg-gray-200 text-center" rowspan="3">Cargo</th>
                                    <th class="border px-2 py-1 bg-gray-200 text-center" rowspan="3">Condición</th>
                                    <th class="border px-2 py-1 bg-gray-200 text-center" rowspan="3">Jor. Lab.</th>

                                    @php
                                        $mes = $mes ?? \Carbon\Carbon::now()->month;
                                        $anio = $anio ?? \Carbon\Carbon::now()->year;
                                        $diasEnMes = \Carbon\Carbon::create($anio, $mes, 1)->daysInMonth;
                                        $diasSemana = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];
                                    @endphp
                                    
                                    {{-- Fila de números de día --}}
                                    @for ($d = 1; $d <= $diasEnMes; $d++)
                                        <th class="border px-1 py-1 bg-gray-200 text-center">{{ $d }}</th>
                                    @endfor

                                    <th class="border px-2 py-1 bg-gray-200 text-center" rowspan="3">Cumplimiento</th>
                                </tr>
                                <tr>
                                    {{-- Fila de nombres de día --}}
                                    @for ($d = 1; $d <= $diasEnMes; $d++)
                                        @php
                                            $fecha = \Carbon\Carbon::create($anio, $mes, $d);
                                            $nombreDia = $diasSemana[$fecha->dayOfWeek];
                                        @endphp
                                        <th class="border px-1 py-1 text-[10px] {{ in_array($nombreDia, ['S', 'D']) ? 'bg-gray-300' : 'bg-gray-200' }}">
                                            {{ $nombreDia }}
                                        </th>
                                    @endfor
                                </tr>
                                <tr>
                                    {{-- Fila de bloques G/L --}}
                                    @for ($d = 1; $d <= $diasEnMes; $d++)
                                        @php
                                            $fecha = \Carbon\Carbon::create($anio, $mes, $d);
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

                            <tbody id="tbodyAsistencia"></tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-right">
                        <button onclick="document.getElementById('modalObservaciones').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="tablaReporte2" class="w-full max-w-auto mx-auto bg-white rounded-xl shadow-md p-6 mt-10 block">
            <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte de Cumplimiento de Anexos</h1>    
            @if($query->isEmpty())
                <div class="text-center text-red-600 font-semibold mt-4">
                    No se encontraron registros. Los directores no han reportado o no han seguido el procedimiento.
                </div>
            @else
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full border rounded-xl text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs text-center">
                            <tr>
                                <th class="px-2 py-1 border">N°</th>
                                <th class="px-2 py-1 border">Codlocal</th>
                                <th class="px-2 py-1 border">Codmod</th>
                                <th class="px-2 py-1 border">Institución</th>
                                <th class="px-2 py-1 border">Nivel</th>
                                <th class="px-2 py-1 border">Modalidad</th>
                                <th class="px-2 py-1 border">Distrito</th>
                                <th class="px-2 py-1 border">Gestión</th>
                                <th class="px-2 py-1 border">DNI Director</th>
                                <th class="px-2 py-1 border">Director</th>
                                <th class="px-2 py-1 border">Celular</th>
                                <th class="px-2 py-1 border">Correo Inst.</th>
                                <th class="px-2 py-1 border">Correo Pers.</th>
                                <th class="px-2 py-1 border">Anexo 03</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($query as $index => $reporte)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-2 py-1 border">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->codlocal }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->codmod }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->institucion }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->nivel }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->modalidad }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->distrito }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->descgestie }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->dni_director }}</td>
                                    <td class="px-2 py-1 border whitespace-nowrap">{{ $reporte->director }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->celular_pers }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->correo_inst }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->correo_pers }}</td>

                                    {{-- Situación Anexo 03 --}}
                                    <td class="px-2 py-1 border font-semibold
                                        @if($reporte->Anexo03 == 'EN PROCESO') text-yellow-600
                                        @elseif($reporte->Anexo03 == 'ENVIADO') text-green-600
                                        @else text-red-600 @endif">
                                        {{ $reporte->Anexo03 }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div id="tablaPowerBI" class="hidden w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10">
            <h1 class="text-2xl font-bold text-center mb-4 uppercase">📊 Dashboard Power BI</h1>
            <div class="overflow-hidden rounded-lg shadow-md">
                <iframe 
                    width="100%" 
                    height="800" 
                    src="https://app.powerbi.com/view?r=eyJrIjoiN2IxN2JhNDItZDBhMi00MzJkLTg2MzMtMDIwMzY2MTcyZjRjIiwidCI6ImQ3OTg3NDY2LWM3YjQtNDEyYS1hNzk0LThjNjA2N2Q1YzU1YSIsImMiOjR9" 
                    frameborder="0" 
                    allowFullScreen="true">
                </iframe>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-4 w-full max-w-3xl relative">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
            <h2 id="modalTitle" class="text-lg font-bold mb-4"></h2>
            <div id="mapContainer" style="height: 500px;"></div>
        </div>
    </div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Carga de Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Inicialización -->
<script>

    const patronDias = @json($patronDias);
    const feriados = @json($feriados);
    const anio = {{ $anio }};
    const mes = {{ $mes }};

    async function verObservaciones(baseUrl, codlocal, nombre_ie) {
        try {

            document.getElementById('tituloModal').innerHTML = "<strong class='text-xl'>" + "Institución - " + nombre_ie + " - Anexo 03</strong>";

            const resp = await fetch(`${baseUrl}?codlocal=${codlocal}`);
            const data = await resp.json();
            
            if (data.error) {
                alert("⚠️ " + data.error);
                return;
            }

            let rows = '';
            Object.keys(data.asistencias).forEach((clave, index) => {
                const asist = data.asistencias[clave];
                const r = data.registros.find(p => (p.dni + '_' + p.cod) === clave);
                if (!r) return;

                rows += `<tr>
                            <td class="border px-2 py-1">${index + 1}</td>
                            <td class="border px-2 py-1">${r.dni}</td>
                            <td class="border px-2 py-1">${r.nombres}</td>
                            <td class="border px-2 py-1">${r.cargo}</td>
                            <td class="border px-2 py-1">${r.condicion}</td>
                            <td class="border px-2 py-1">${r.jornada}</td>`;

                if (asist.asistencia && asist.asistencia.length > 0) {
                    const totalDias = asist.asistencia.length;

                    // Buscar indices de observación (I,P,L,V)
                    let obsIndexIni = -1;
                    let obsIndexFin = -1;
                    asist.asistencia.forEach((val, i) => {
                        if (['I','P','L','V'].includes(val)) {
                            if (obsIndexIni === -1) obsIndexIni = i;
                            obsIndexFin = i;
                        }
                    });

                    // Caso 1: observación cubre todo el mes (todo null pero tiene observación)
                    const todosNull = asist.asistencia.every(v => v === null);
                    if (todosNull && (asist.observacion || asist.tipo_observacion)) {
                        rows += `<td class="border px-1 py-1 bg-red-50 text-center text-red-600 italic font-bold" colspan="${totalDias}">
                                    ${asist.observacion_detalle ?? asist.observacion ?? asist.tipo_observacion}
                                </td>`;
                    } 
                    // Caso 2: observación en un rango específico
                    else if (obsIndexIni !== -1) {
                        // antes de la observación
                        for (let i = 0; i < obsIndexIni; i++) {
                            let val = asist.asistencia[i];
                            let clase = '';
                            if (val === 'A') clase = 'text-black-600 font-bold';
                            if (val === 'F') clase = 'bg-yellow-100';
                            rows += `<td class="border px-1 py-1 text-center ${clase}">${val ?? ''}</td>`;
                        }

                        // rango observado
                        const rango = obsIndexFin - obsIndexIni + 1;
                        rows += `<td class="border px-1 py-1 bg-red-50 text-center text-red-600 italic font-bold" colspan="${rango}">
                                    ${asist.observacion_detalle ?? asist.observacion ?? asist.tipo_observacion}
                                </td>`;

                        // después de la observación
                        for (let i = obsIndexFin + 1; i < totalDias; i++) {
                            let val = asist.asistencia[i];
                            let clase = '';
                            if (val === 'A') clase = 'text-black-600 font-bold';
                            if (val === 'F') clase = 'bg-yellow-100';
                            rows += `<td class="border px-1 py-1 text-center ${clase}">${val ?? ''}</td>`;
                        }
                    } 
                    // Caso 3: sin observaciones → pintar normal
                    else {
                        asist.asistencia.forEach(val => {
                            let clase = '';
                            if (val === 'A') clase = 'text-green-600 font-bold';
                            if (val === 'F') clase = 'bg-yellow-100';
                            rows += `<td class="border px-1 py-1 text-center ${clase}">${val ?? ''}</td>`;
                        });
                    }
                }

                // --- Calcular cumplimiento ---
                let diasGestion = 0, diasLectivos = 0;
                let cumplidosGestion = 0, cumplidosLectivos = 0;

                asist.asistencia.forEach((val, i) => {
                    const dia = i + 1;
                    const tipo = patronDias[dia] ?? null;

                    // marcar feriados como F
                    const fecha = `${anio}-${String(mes).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
                    if (feriados.includes(fecha)) val = 'F';

                    if (tipo === 'g' || tipo === 'l') {
                        if (tipo === 'g') diasGestion++;
                        if (tipo === 'l') diasLectivos++;

                        if (val === 'A') {
                            if (tipo === 'g') cumplidosGestion++;
                            if (tipo === 'l') cumplidosLectivos++;
                        }
                    }
                });

                const totalExigidos = diasGestion + diasLectivos;
                const totalCumplidos = cumplidosGestion + cumplidosLectivos;
                const cumplimiento = totalExigidos > 0 ? Math.round((totalCumplidos/totalExigidos)*100) : null;

                // --- Pintar columna ---
                if (cumplimiento === null) {
                    rows += `<td class="border px-1 py-1 text-center">-</td>`;
                } else {
                    rows += `<td class="border px-1 py-1 text-center font-semibold whitespace-nowrap w-[200px]">
                                <span class="text-blue-600 block">
                                    G: ${cumplidosGestion}/${diasGestion} (${diasGestion > 0 ? Math.round((cumplidosGestion/diasGestion)*100) : 0}%)
                                </span>
                                <span class="text-green-600 block">
                                    L: ${cumplidosLectivos}/${diasLectivos} (${diasLectivos > 0 ? Math.round((cumplidosLectivos/diasLectivos)*100) : 0}%)
                                </span>
                                <span class="${cumplimiento === 100 ? 'text-green-600' : 'text-purple-600'} block">
                                    Total: ${totalCumplidos}/${totalExigidos} (${cumplimiento}%)
                                </span>
                            </td>`;
                }

                rows += `</tr>`;
            });

            document.getElementById("tbodyAsistencia").innerHTML = rows;
            document.getElementById("modalObservaciones").classList.remove("hidden");

        } catch (err) {
            console.error("Error en fetch:", err);
            alert("Error cargando observaciones");
        }
    }


    function filtrar(tipo) {
        const filas = document.querySelectorAll("#tablaCumplimiento tbody tr");
        filas.forEach(fila => {
            const val = parseFloat(fila.dataset.cumplimiento);
            if (tipo === 100) {
                fila.style.display = (val === 100) ? "" : "none";
            } else if (tipo === 99) {
                fila.style.display = (val < 100) ? "" : "none";
            } else {
                fila.style.display = ""; // mostrar todos
            }
        });
    }

    $(document).ready(function() {
        $('.select2').select2({
            allowClear: true,
            width: 'resolve',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                // Personalizado: búsqueda parcial por término dentro del texto completo
                if (typeof data.text === 'undefined') {
                    return null;
                }

                const term = params.term.toLowerCase();
                const text = data.text.toLowerCase();

                if (text.includes(term)) {
                    return data;
                }

                return null;
            }
        });
    });

    let mapInstance;

    document.querySelectorAll('.nombre-colegio').forEach(cell => {
        cell.addEventListener('click', function () {
            const lat = parseFloat(this.dataset.lat);
            const lng = parseFloat(this.dataset.lng);

            if (isNaN(lat) || isNaN(lng)) {
                alert("No hay coordenadas para este colegio");
                return;
            }

            document.getElementById('modalTitle').textContent = this.dataset.institucion + " - " + this.dataset.distrito;
            document.getElementById('mapModal').classList.remove('hidden');

            if (mapInstance) {
                mapInstance.remove();
            }

            mapInstance = L.map('mapContainer').setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(mapInstance);

            const popupContent = `
                <div style="font-size:14px;">
                    <strong><b>${this.dataset.institucion}</b></strong><br>
                    <b>DNI:</b> ${this.dataset.dni}<br>
                    <b>Nombre:</b> ${this.dataset.nombres}<br>
                    <b>Cargo:</b> ${this.dataset.cargo}<br>
                    <b>Condición:</b> ${this.dataset.condicion}<br>
                    <b>Tipo Observación:</b> <span style="color:red;font-weight:bold;">${this.dataset.tipoObservacion}</span><br>
                    <b>Observación:</b> ${this.dataset.observacion}<br>
                    <b>Observación Detalle:</b> ${this.dataset.observacionDetalle}<br>
                    <b>Nivel:</b> ${this.dataset.nivel}<br>
                    <b>RED:</b> ${this.dataset.red}<br>
                    <b>Fecha Reporte:</b> ${this.dataset.fecha}
                </div>
            `;

            L.marker([lat, lng])
                .addTo(mapInstance)
                .bindPopup(popupContent)
                .openPopup();
        });
    });

    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('mapModal').classList.add('hidden');
    });

    document.addEventListener("DOMContentLoaded", function () {
        const btnReporte = document.getElementById("btnReporte");
        const btnReporte2 = document.getElementById("btnReporte2");
        const btnObservaciones = document.getElementById("btnObservaciones");
        const btnBloque = document.getElementById("btnBloque");
        const btnPowerBI = document.getElementById("btnPowerBI");

        const tablaReporte = document.getElementById("tablaReporte");
        const tablaReporte2 = document.getElementById("tablaReporte2");
        const tablaObservaciones = document.getElementById("tablaObservaciones");
        const tablaBloque = document.getElementById("tablaBloque");
        const tablaPowerBI  = document.getElementById("tablaPowerBI");

        const botones = [btnReporte, btnReporte2, btnObservaciones, btnBloque, btnPowerBI];
        const tablas = [tablaReporte, tablaReporte2, tablaObservaciones, tablaBloque, tablaPowerBI];

        function activarTab(botonActivo, tablaActiva) {
            // esconder solo las tablas que existen
            tablas.forEach(t => { if (t) t.classList.add("hidden"); });

            // resetear estilos de botones que existen
            botones.forEach(b => {
                if (!b) return;
                b.classList.remove("text-blue-600", "border-blue-600", "border-b-2");
                b.classList.add("text-gray-500");
            });

            // activar la tabla y el botón si existen
            if (tablaActiva) tablaActiva.classList.remove("hidden");
            if (botonActivo) {
                botonActivo.classList.add("text-blue-600", "border-blue-600", "border-b-2");
                botonActivo.classList.remove("text-gray-500");
            }
        }

        // Añadir listeners solo si existen los botones
        if (btnReporte) btnReporte.addEventListener("click", () => activarTab(btnReporte, tablaReporte));
        if (btnReporte2) btnReporte2.addEventListener("click", () => activarTab(btnReporte2, tablaReporte2));
        if (btnObservaciones) btnObservaciones.addEventListener("click", () => activarTab(btnObservaciones, tablaObservaciones));
        if (btnBloque) btnBloque.addEventListener("click", () => activarTab(btnBloque, tablaBloque));
        if (btnPowerBI) btnPowerBI.addEventListener("click", () => activarTab(btnPowerBI, tablaPowerBI));

        // Opcional: establecer pestaña inicial segura
        if (btnReporte && tablaReporte) activarTab(btnReporte, tablaReporte);
    });

    document.addEventListener("DOMContentLoaded", function () {
        const filtroTexto = document.getElementById("filtroTexto");
        const filtroInstitucion = document.getElementById("filtroInstitucion");
        const filtroDistrito = document.getElementById("filtroDistrito");
        const filtroNivel = document.getElementById("filtroNivel");
        const filtroObservacion = document.getElementById("filtroObservacion");

        function aplicarFiltros() {
            // Tomamos valores
            const texto = filtroTexto.value.toLowerCase();
            const inst = filtroInstitucion.value;
            const dist = filtroDistrito.value;
            const niv = filtroNivel.value;
            const obs = filtroObservacion.value;

            // Recorremos ambas tablas
            document.querySelectorAll("#tablaReporte tbody tr, #tablaObservaciones tbody tr").forEach(row => {
                let mostrar = true;
                const contenidoFila = row.textContent.toLowerCase();

                // Filtro texto
                if (texto && !contenidoFila.includes(texto)) mostrar = false;

                // Filtro institución
                if (inst && !row.innerText.toLowerCase().includes(inst)) mostrar = false;

                // Filtro distrito
                if (dist && !row.innerText.toLowerCase().includes(dist)) mostrar = false;

                // Filtro nivel
                if (niv && !row.innerText.toLowerCase().includes(niv)) mostrar = false;

                // Filtro observacion
                if (obs && !row.innerText.toLowerCase().includes(obs)) mostrar = false;

                row.style.display = mostrar ? "" : "none";
            });
        }

        // Eventos
        [filtroTexto, filtroInstitucion, filtroDistrito, filtroNivel, filtroObservacion].forEach(el => {
            el.addEventListener("input", aplicarFiltros);
            el.addEventListener("change", aplicarFiltros);
        });
    });

    // --- sincronizar hidden inputs antes de enviar ---
    const pdfForm = document.querySelector('form[action="{{ route('reporte.observaciones.pdf') }}"]');
    if (pdfForm) {
        pdfForm.addEventListener('submit', function () {
            document.getElementById('inputInstitucion').value = document.getElementById('filtroInstitucion').value;
            document.getElementById('inputDistrito').value = document.getElementById('filtroDistrito').value;
            document.getElementById('inputNivel').value = document.getElementById('filtroNivel').value;
            document.getElementById('inputObservacion').value = document.getElementById('filtroObservacion').value;
            document.getElementById('inputTexto').value = document.getElementById('filtroTexto').value;
        });
    }
</script>



@endsection
