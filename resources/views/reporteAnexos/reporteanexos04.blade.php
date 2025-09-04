@extends('layout_especialista/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak]{
        display:none !important;
    }
</style>

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
                <span class="text-gray-500"><strong>Reporte Anexo 04</strong></span>
            </div>
            </li>
        </ol>
    </nav>

    <!-- Alertas -->
    <div class="flex flex-wrap gap-4 mb-6">
        @if($directoresSinAnexo04->count())
            <div x-data="{ open: false }" class="flex-1 min-w-[250px]">
                <button 
                    @click="open = true"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hover:bg-red-200 transition w-full text-left"
                >
                    <strong class="font-bold">¡Atención! 📢</strong>
                    <span class="block sm:inline">
                        {{ $directoresSinAnexo04->count() }} directores aún no reportan su Anexo 04. Haz clic para ver la lista.
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
                                <h2 class="text-2xl font-bold text-red-600">Directores que no han reportado Anexo 04</h2>
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
                                    @foreach($directoresSinAnexo04 as $dir)
                                        <tr>
                                            <td class="px-4 py-2">{{ $dir->dni }}</td>
                                            <td class="px-4 py-2">{{ $dir->apellipat }} {{ $dir->apellimat }}, {{ $dir->nombres }}</td>
                                            <td class="px-4 py-2">{{ $dir->celular_pers }}</td>
                                            <td class="px-4 py-2">{{ $dir->nombre_inst }}</td>
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
                        {{ $directoresEnProceso->count() }} directores que estan en proceso su Anexo 04. Haz clic para ver la lista.
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
                                <h2 class="text-2xl font-bold text-yellow-600">Directores que estan en proceso Anexo 04</h2>
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
                                            <td class="px-4 py-2">{{ $dir->dni }}</td>
                                            <td class="px-4 py-2">{{ $dir->apellipat }} {{ $dir->apellimat }}, {{ $dir->nombres }}</td>
                                            <td class="px-4 py-2">{{ $dir->celular_pers }}</td>
                                            <td class="px-4 py-2">{{ $dir->nombre_inst }}</td>
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
                        {{ $directoresCompletos->count() }} directores que completaron con éxito su Anexo 04. Haz clic para ver la lista.
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
                                <h2 class="text-2xl font-bold text-green-600">Directores que completaron con éxito Anexo 04</h2>
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
                                    @foreach($directoresCompletos as $dir)
                                        <tr>
                                            <td class="px-4 py-2">{{ $dir->dni }}</td>
                                            <td class="px-4 py-2">{{ $dir->apellipat }} {{ $dir->apellimat }}, {{ $dir->nombres }}</td>
                                            <td class="px-4 py-2">{{ $dir->celular_pers }}</td>
                                            <td class="px-4 py-2">{{ $dir->nombre_inst }}</td>
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

    <!-- Filtros -->
    <div class="w-full max-w-6xl mx-auto bg-white rounded-xl shadow-md p-4 mt-6">
        <h2 class="text-lg font-semibold mb-3">Filtros de búsqueda</h2>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
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

            <!-- Red -->
            <select id="filtroRed" class="border rounded-lg px-2 py-1">
                <option value="">Red</option>
                @foreach($personasConInasistencia->pluck('red')->unique() as $red)
                    @if($red)
                        <option value="{{ strtolower($red) }}">{{ $red }}</option>
                    @endif
                @endforeach
            </select>

            <!-- Nivel -->
            <select id="filtroNivel" class="border rounded-lg px-2 py-1">
                <option value="">Nivel</option>
                @foreach($reportes->pluck('nivel')->unique() as $niv)
                    <option value="{{ strtolower($niv) }}">{{ $niv }}</option>
                @endforeach
            </select>

            <!-- Filtro por Mes -->
            <select id="filtroMes" class="border rounded-lg px-2 py-1">
                <option value="">Mes</option>
                @foreach($reportes->pluck('fecha_creacion')->map(fn($f) => \Carbon\Carbon::parse($f)->format('m'))->unique() as $mesNum)
                    @php
                        $nombreMes = \Carbon\Carbon::create()->month($mesNum)->translatedFormat('F');
                        $nombreMes = mb_convert_case($nombreMes, MB_CASE_TITLE, "UTF-8");
                    @endphp
                    <option value="{{ $mesNum }}">{{ $nombreMes }}</option>
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
                ⚠️ Resumen descuento
            </button>
            <button id="btnEstadistica" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                📊 Estadistica
            </button>
            <button id="btnBloque" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                📊  Estadistica de Bloques
            </button>
        </div>

        <div id="tablaReporte" class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 block">
            <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte de Cumplimiento del Anexo 04</h1>
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
                                <th class="px-2 py-1 border whitespace-nowrap">Número de Oficio</th>
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
                        <tbody class="text-center">
                            @foreach($reportes as $index => $reporte)
                                <tr class="hover:bg-gray-50">
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
        
        <div id="tablaObservaciones" class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 hidden">
            <h3 class="text-xl font-bold text-red-600 mb-4">⚠️ Resumen de inasistencias, tardanzas, permisos, Huelga o paro,</h3> 
            @if($personasConInasistencia->isEmpty())
                <div class="text-center text-red-600 font-semibold mt-4">
                    No se encontraron descuentos. Los directores no han reportado o no han seguido el procedimiento.
                </div>
            @else
                <div class="overflow-x-auto border rounded">
                    <table class="min-w-full text-sm text-center border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">N°</th>
                                <th class="border px-2 py-1">Oficio</th>
                                <th class="border px-2 py-1">Expediente</th>
                                <th class="border px-2 py-1">DNI</th>
                                <th class="border px-2 py-1">Docente</th>
                                <th class="border px-2 py-1">Cargo</th>
                                <th class="border px-2 py-1">Distrito</th>
                                <th class="border px-2 py-1">Red</th>
                                <th class="border px-2 py-1">Institución</th>
                                <th class="border px-2 py-1">Nivel</th>
                                <th class="border px-2 py-1">Inasistencias</th>
                                <th class="border px-2 py-1">Tardanzas</th>
                                <th class="border px-2 py-1">Permisos</th>
                                <th class="border px-2 py-1">Huelgas</th>
                            </tr>
                        </thead>
                        @foreach($personasConInasistencia as $r)
                            <tr>
                                <td class="px-2 py-1 border">{{ $loop->iteration }}</td>
                                <td class="px-2 py-1 border"><strong>{{ $r->oficio ?? '' }}</strong></td>
                                <td class="px-2 py-1 border"><strong>{{ $r->expediente }}</strong></td>
                                <td class="px-2 py-1 border">{{ $r->dni }}</td>
                                <td class="px-2 py-1 border cursor-pointer text-blue-600 hover:underline" 
                                    onclick="abrirModalInasistencia('{{ $r->dni }}')">
                                    {{ $r->nombres }}
                                </td>
                                <td class="px-2 py-1 border">{{ $r->cargo }}</td>
                                <td class="px-2 py-1 border">{{ $r->distrito ?? '' }}</td>
                                <td class="px-2 py-1 border">{{ $r->red ?? '' }}</td>
                                <td class="px-2 py-1 border">{{ $r->institucion ?? '' }}</td>
                                <td class="px-2 py-1 border">{{ $r->nivel }}</td>
                                <td class="px-2 py-1 border {{ ($r->inasistencia['inasistencia_total'] ?? 0) > 0 ? 'text-red-600 font-bold' : '' }}">
                                    {{ $r->inasistencia['inasistencia_total'] ?? 0 }}
                                </td>
                                <td class="px-2 py-1 border {{ (($r->inasistencia['tardanza_total']['horas'] ?? 0) > 0 || ($r->inasistencia['tardanza_total']['minutos'] ?? 0) > 0) ? 'text-red-600 font-bold' : '' }}">
                                    {{ $r->inasistencia['tardanza_total']['horas'] ?? 0 }}h {{ $r->inasistencia['tardanza_total']['minutos'] ?? 0 }}m
                                </td>
                                <td class="px-2 py-1 border {{ (($r->inasistencia['permiso_sg_total']['horas'] ?? 0) > 0 || ($r->inasistencia['permiso_sg_total']['minutos'] ?? 0) > 0) ? 'text-red-600 font-bold' : '' }}">
                                    {{ $r->inasistencia['permiso_sg_total']['horas'] ?? 0 }}h {{ $r->inasistencia['permiso_sg_total']['minutos'] ?? 0 }}m
                                </td>
                                <td class="px-2 py-1 border {{ ($r->inasistencia['huelga_total'] ?? 0) > 0 ? 'text-red-600 font-bold' : '' }}">
                                    {{ $r->inasistencia['huelga_total'] ?? 0 }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>

        <div id="tablaEstadistica" class="hidden p-6">
            <h1 class="text-xl font-bold text-center mb-4">📊 Resumen Estadístico de Descuento</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Gráfico de barras -->
                <div class="bg-white rounded-xl shadow-md p-4">
                    <h2 class="text-md font-semibold text-center mb-2">Totales por tipo</h2>
                    <canvas id="chartBarras"></canvas>
                </div>
                <!-- Gráfico circular -->
                <div class="bg-white rounded-xl shadow-md p-4">
                    <h2 class="text-md font-semibold text-center mb-2">Distribución porcentual</h2>
                    <canvas id="chartPie"></canvas>
                </div>
            </div>
        </div>

        <div id="tablaBloque" class="hidden p-6">
            <h1 class="text-xl font-bold text-center mb-4">
                📊 Resumen Estadístico de Bloques de Semanas de Gestión y Lectivas
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Gráfico barras -->
                <div class="bg-white shadow rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Cumplimiento por bloque</h2>
                    <div class="relative w-full h-96">
                        <canvas id="chartBloques"></canvas>
                    </div>
                </div>

                <!-- Gráfico barras -->
                <!-- <div class="bg-white shadow rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Cumplimiento por bloque</h2>
                    <div class="relative w-full h-96">
                        <canvas id="graficoCumplimiento"></canvas>
                    </div>
                </div> -->

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
                                                class="cursor-pointer text-blue-600 hover:underline"
                                                onclick="verObservaciones(
                                                    '{{ url('reporte/observaciones-ie') }}',
                                                    '{{ $c['codlocal'] }}',
                                                    '{{ $c['nombre_ie'] }}'
                                                )">
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
        

            <div id="modalInasistencia" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
                <div class="bg-white rounded-xl shadow-lg w-11/12 max-w-3xl p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold" id="nombreDocente">Docente</h3>
                        <button onclick="cerrarModal()" class="text-red-500 font-bold">&times;</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-center border-collapse">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-2 py-1">Fecha</th>
                                    <th class="border px-2 py-1">Bloque</th>
                                    <th class="border px-2 py-1">Semana Bloque</th>
                                    <th class="border px-2 py-1">Tipo Descuento</th>
                                    <th class="border px-2 py-1">Horas/Minutos</th>
                                </tr>
                            </thead>
                            <tbody id="detalleInasistencia"></tbody>
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

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- CDN de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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


    let cumplimiento = @json($cumplimiento);

    const labels = cumplimiento.map(c => c.nombre_ie);
    const top10 = cumplimiento.sort((a,b) => b.cumplimiento - a.cumplimiento).slice(0,10);
    const bottom10 = cumplimiento.sort((a,b) => a.cumplimiento - b.cumplimiento).slice(0,10);
    const data = cumplimiento.map(c => ({x: c.total_docentes, y: c.cumplimiento}));

    // new Chart(document.getElementById('graficoCumplimiento'), {
    //     type: 'scatter',
    //     data: {
    //         datasets: [{
    //             label: 'Colegios',
    //             data: data,
    //             pointBackgroundColor: data.map(d => 
    //                 d.y === 100 ? 'green' : (d.y >= 80 ? 'orange' : 'red')
    //             )
    //         }]
    //     },
    //     options: {
    //         scales: {
    //             x: { title: { display: true, text: 'Total Docentes' }},
    //             y: { title: { display: true, text: 'Cumplimiento (%)' }, max: 100 }
    //         }
    //     }
    // });

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

    const personasConInasistencia = @json($personasConInasistencia);
    const totalDocentes    = @json($totalDocentes);
    const totalDeficientes = @json($totalDeficientes);
    const totalCumplen     = @json($totalCumplen);
    
    document.addEventListener("DOMContentLoaded", function () {
        const btnReporte = document.getElementById("btnReporte");
        const btnObservaciones = document.getElementById("btnObservaciones");
        const btnEstadistica = document.getElementById("btnEstadistica");
        const tablaReporte = document.getElementById("tablaReporte");
        const tablaObservaciones = document.getElementById("tablaObservaciones");
        const tablaEstadistica = document.getElementById("tablaEstadistica");
        const btnBloque = document.getElementById("btnBloque");
        const tablaBloque = document.getElementById("tablaBloque");

        function activarTab(botonActivo, tablaActiva) {
            [tablaReporte, tablaObservaciones, tablaEstadistica, tablaBloque].forEach(t => t.classList.add("hidden"));
            [btnReporte, btnObservaciones, btnEstadistica, btnBloque].forEach(b => {
                b.classList.remove("text-blue-600", "border-blue-600", "border-b-2");
                b.classList.add("text-gray-500");
            });
            tablaActiva.classList.remove("hidden");
            botonActivo.classList.add("text-blue-600", "border-blue-600", "border-b-2");
            botonActivo.classList.remove("text-gray-500");
        }
        btnReporte.addEventListener("click", () => activarTab(btnReporte, tablaReporte));
        btnObservaciones.addEventListener("click", () => activarTab(btnObservaciones, tablaObservaciones));
        btnEstadistica.addEventListener("click", () => activarTab(btnEstadistica, tablaEstadistica));
        btnBloque.addEventListener("click", () => activarTab(btnBloque, tablaBloque));
    });


    const bloques = [
        {nombre:'1° semana gest.', tipo:'GESTIÓN', inicio:'2025-03-03', fin:'2025-03-14'},
        {nombre:'1° semana lect.', tipo:'LECTIVA', inicio:'2025-03-17', fin:'2025-05-16'},
        {nombre:'2° semana gest.', tipo:'GESTIÓN', inicio:'2025-05-19', fin:'2025-05-23'},
        {nombre:'2° semana lect.', tipo:'LECTIVA', inicio:'2025-05-26', fin:'2025-07-25'},
        {nombre:'3° semana gest.', tipo:'GESTIÓN', inicio:'2025-07-28', fin:'2025-08-08'},
        {nombre:'3° semana lect.', tipo:'LECTIVA', inicio:'2025-08-11', fin:'2025-10-10'},
        {nombre:'4° semana gest.', tipo:'GESTIÓN', inicio:'2025-10-13', fin:'2025-10-17'},
        {nombre:'4° semana lect.', tipo:'LECTIVA', inicio:'2025-10-20', fin:'2025-12-19'},
        {nombre:'5° semana gest.', tipo:'GESTIÓN', inicio:'2025-12-22', fin:'2025-12-31'},
    ];


    function abrirModalInasistencia(dni) {
        const docente = personasConInasistencia.find(d => d.dni === dni);
        if (!docente) return;

        document.getElementById('nombreDocente').textContent = docente.nombres;

        const tbody = document.getElementById('detalleInasistencia');
        tbody.innerHTML = '';

        const detalle = JSON.parse(docente.detalle || '{}');

        const tipos = ['inasistencia','tardanza','permiso_sg','huelga'];
        const nombresTipos = {
            inasistencia: 'Inasistencia',
            tardanza: 'Tardanza',
            permiso_sg: 'Permiso sin goce',
            huelga: 'Huelga/Paro'
        };

        const thead = tbody.closest('table').querySelector('thead');
        let ths = `
            <th class="border px-2 py-1">Fecha</th>
            <th class="border px-2 py-1">Bloque</th>
            <th class="border px-2 py-1">Semana Bloque</th>
            <th class="border px-2 py-1">Tipo Descuento</th>
        `;
        
        if (detalle.tardanza?.length || detalle.permiso_sg?.length) {
            ths += `<th class="border px-2 py-1">Horas/Minutos</th>`;
        }
        thead.innerHTML = `<tr>${ths}</tr>`;

        tipos.forEach(tipo => {
            (detalle[tipo] || []).forEach(item => {
                let fecha = '';
                let horasMinutos = '';

                if (typeof item === 'object' && item.fecha) {
                    fecha = item.fecha;
                    if (tipo === 'tardanza' || tipo === 'permiso_sg') {
                        horasMinutos = `${item.horas || 0}h ${item.minutos || 0}m`;
                    }
                } else {
                    fecha = item;
                }

                const bloque = bloques.find(b => fecha >= b.inicio && fecha <= b.fin);
                if (bloque) {
                    const colorTipo = bloque.tipo === 'GESTIÓN' ? 'bg-blue-100 text-blue-800 font-bold' :
                                    bloque.tipo === 'LECTIVA' ? 'bg-green-100 text-green-800 font-bold' : '';
                    let partes = bloque.nombre.split(" ");
                    let nombreTransformado = `${partes[0]} bloque ${partes[1]}`;
                    tbody.innerHTML += `<tr>
                        <td class="border px-2 py-1">${fecha}</td>
                        <td class="border px-2 py-1">${nombreTransformado}</td>
                        <td class="border px-2 py-1 ${colorTipo}">${bloque.tipo}</td>
                        <td class="border px-2 py-1">${nombresTipos[tipo]}</td>
                        ${tipo === 'tardanza' || tipo === 'permiso_sg' ? `<td class="border px-2 py-1">${horasMinutos}</td>` : ''}
                    </tr>`;
                }
            });
        });

        const modal = document.getElementById('modalInasistencia');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function cerrarModal() {
        const modal = document.getElementById('modalInasistencia');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

// =================== FILTROS UNIFICADOS ===================
    let chartBarras, chartPie, chartBloques, chartBloquesPie;

    function aplicarFiltros() {
        let inst = document.getElementById('filtroInstitucion').value;
        let dist = document.getElementById('filtroDistrito').value;
        let red  = document.getElementById('filtroRed').value;
        let niv  = document.getElementById('filtroNivel').value;
        let mes  = document.getElementById('filtroMes').value;
        let texto = document.getElementById('filtroTexto').value.toLowerCase();

        // --- Filtrado base ---
        const filtrados = personasConInasistencia.filter(p => {
            let cadena = `${p.dni} ${p.nombres} ${p.cargo} ${p.distrito ?? ''} ${p.red ?? ''} ${p.institucion ?? ''} ${p.nivel} ${p.fecha_creacion ?? ''}`.toLowerCase();
            return (!inst || cadena.includes(inst)) &&
                (!dist || cadena.includes(dist)) &&
                (!red  || cadena.includes(red)) &&
                (!niv  || cadena.includes(niv)) &&
                (!mes  || cadena.includes(mes)) &&
                (!texto || cadena.includes(texto));
        });

        // --- Tabla Reporte ---
        // document.querySelectorAll('#tablaReporte tbody tr').forEach(tr => {
        //     tr.style.display = filtrados.some(f => tr.innerText.toLowerCase().includes(f.dni.toLowerCase())) ? '' : 'none';
        // });

        // --- Tabla Observaciones ---
        document.querySelectorAll('#tablaObservaciones table tr').forEach((tr, i) => {
            if (i === 0) return;
            tr.style.display = filtrados.some(f => tr.innerText.toLowerCase().includes(f.dni.toLowerCase())) ? '' : 'none';
        });

        // --- Actualizar Gráficos Globales ---
        actualizarGraficos(filtrados);

        // --- Actualizar Gráficos por Bloques ---
        renderizarGraficasBloques(filtrados);
    }


// =================== GRÁFICOS ===================
    function actualizarGraficos(data) {
        let totalInasist = 0, totalHuelga = 0;
        let totalTardanzaMin = 0, totalPermisoMin = 0;

        data.forEach(p => {
            totalInasist += p.inasistencia?.inasistencia_total ?? 0;
            totalHuelga += p.inasistencia?.huelga_total ?? 0;

            let hT = p.inasistencia?.tardanza_total?.horas ?? 0;
            let mT = p.inasistencia?.tardanza_total?.minutos ?? 0;
            totalTardanzaMin += (hT * 60 + mT);

            let hP = p.inasistencia?.permiso_sg_total?.horas ?? 0;
            let mP = p.inasistencia?.permiso_sg_total?.minutos ?? 0;
            totalPermisoMin += (hP * 60 + mP);
        });

        const labelsBarras = ["Inasistencias (días)", "Huelgas (días)"];
        const labelsPie = ["Tardanzas (min)", "Permisos SG (min)"];
        const colorsBarras = ["#3b82f6", "#16a34a"];
        const colorsPie = ["#e11d48", "#f97316"];

        if (!chartBarras) {
        chartBarras = new Chart(document.getElementById("chartBarras"), {
            type: "bar",
            data: {
                labels: ["Inasistencias (días)", "Huelgas (días)"], 
                datasets: [
                    {
                        label: "Totales",
                        data: [totalInasist, totalHuelga], 
                        backgroundColor: [colorsBarras[0], colorsBarras[1]],
                        barPercentage: 0.5,   
                        categoryPercentage: 0.6 
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false } 
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { align: "center" }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        chartPie = new Chart(document.getElementById("chartPie"), {
            type: "doughnut",
            data: {
                labels: labelsPie,
                datasets: [{
                    data: [totalTardanzaMin, totalPermisoMin],
                    backgroundColor: colorsPie
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: "bottom" } }
            }
        });

        } else {
            chartBarras.data.datasets[0].data = [totalInasist, totalHuelga];
            chartPie.data.datasets[0].data = [totalTardanzaMin, totalPermisoMin];
            chartBarras.update();
            chartPie.update();
        }

    }


    function calcularDeficienciaPorBloque(data) {
        // Inicializar resumen por bloque
        const resumen = bloques.map(b => {
            const diasHabiles = diasHabilesPorBloque(b.inicio, b.fin);
            return {
                bloque: b.nombre,
                tipo: b.tipo,
                totalDias: diasHabiles,
                docentesConFaltas: new Set(),
                totalDocentes: totalDocentes || 0, 
                cumplieron: 0,
                noCumplieron: 0,
                porcentajeCumplimiento: 0
            };
        });
        // Si no hay datos de inasistencias, todos cumplen
        if (!data || data.length === 0) {
            resumen.forEach(r => {
                r.cumplieron = r.totalDocentes;
                r.noCumplieron = 0;
                r.porcentajeCumplimiento = 100; 
            });
            return resumen;
        }

        // Procesar cada docente con inasistencias
        data.forEach(docente => {
            const detalle = JSON.parse(docente.detalle || '{}');
            ['inasistencia', 'tardanza', 'permiso_sg', 'huelga'].forEach(tipoFalta => {
                (detalle[tipoFalta] || []).forEach(item => {
                    let fecha = (typeof item === 'object' && item.fecha) ? item.fecha : item;
                    const bloqueIndex = bloques.findIndex(b => fecha >= b.inicio && fecha <= b.fin);

                    if (bloqueIndex !== -1) {
                        const docenteId = docente.dni || docente.id || `docente_${Math.random()}`;
                        resumen[bloqueIndex].docentesConFaltas.add(docenteId);
                        
                    }
                });
            });
        });

        // Cálculo por bloque
        resumen.forEach(r => {
            const noCumplieron = r.docentesConFaltas.size;
            const cumplieron = r.totalDocentes - noCumplieron;

            r.noCumplieron = noCumplieron;
            r.cumplieron = cumplieron >= 0 ? cumplieron : 0; 
            r.porcentajeCumplimiento = r.totalDocentes > 0
                ? Math.round((cumplieron / r.totalDocentes) * 100)
                : 100;
        });

        return resumen;
    }

    // Función auxiliar: cuenta días hábiles (L-V) entre dos fechas
    function diasHabilesPorBloque(inicio, fin) {
        const start = new Date(inicio);
        const end = new Date(fin);
        let count = 0;

        for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            const day = d.getDay(); 
            if (day !== 0 && day !== 6) count++;
        }
        return count;
    }


    function renderizarGraficasBloques(data,totalDocentes) {

        // Verificar elementos DOM
        const elemBloques = document.getElementById("chartBloques");
        // 🔹 Recalcular totales según el filtro aplicado
        const totalDocentesFiltrado = new Set(data.map(d => d.dni)).size; 
        const totalCumplenFiltrado = data.filter(d => d.cumple).length;
        const totalDeficientesFiltrado = totalDocentesFiltrado - totalCumplenFiltrado;

        // Usar los nuevos totales filtrados en la función resumen
        const resumen = calcularDeficienciaPorBloque(
            data, 
            totalDocentesFiltrado, 
            totalCumplenFiltrado, 
            totalDeficientesFiltrado
        );

        const labels = resumen.map(r => r.bloque);
        const noCumplieron = resumen.map(r => r.noCumplieron);
        const cumplieron = resumen.map(r => totalDocentesFiltrado);

        // Colores
        const coloresGestion = 'rgba(59, 130, 246, 0.8)'; // Azul para gestión
        const coloresFaltas = 'rgba(239, 68, 68, 0.8)';   // Rojo para faltas
        
        // 🔹 Plugin para pintar el fondo de las etiquetas según tipo de bloque
        const fondoEtiquetasPlugin = {
            id: 'fondoEtiquetas',
            beforeDraw(chart) {
                const xScale = chart.scales['x']; 
                if (!xScale) return; 

                const {ctx, chartArea: {top, bottom}} = chart;
                const step = xScale.width / (chart.data.labels.length || 1); 

                resumen.forEach((bloque, i) => {
                    const xCenter = xScale.getPixelForTick(i);
                    const xStart  = xCenter - step / 2;
                    const xEnd    = xCenter + step / 2;

                    ctx.save();
                    ctx.fillStyle = bloque.tipo === "Gestión"
                        ? "rgba(59,130,246,0.1)" 
                        : "rgba(16,185,129,0.1)"; 
                    ctx.fillRect(xStart, top, xEnd - xStart, bottom - top);
                    ctx.restore();
                });
            }
        };


        // Gráfica
        chartBloques = new Chart(elemBloques, {
            type: "bar",
            data: {
                labels,
                datasets: [
                    // {
                    //     label: "Docentes que Cumplieron",
                    //     data: totalDocentes,
                    //     backgroundColor: coloresGestion, 
                    //     borderWidth: 1
                    // },
                    {
                        label: "Docentes con Deficiencias",
                        data: noCumplieron,
                        backgroundColor: coloresFaltas,
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Cumplimiento por Bloques - Total Docentes: ${totalDocentesFiltrado}`,
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const bloque = resumen[context.dataIndex];
                                const porcentaje = bloque.totalDocentes > 0 ? 
                                    Math.round((context.parsed.y / bloque.totalDocentes) * 100) : 0;
                                return `${context.dataset.label}: ${context.parsed.y} (${porcentaje}%)`;
                            },
                            afterLabel: function(context) {
                                const bloque = resumen[context.dataIndex];
                                return `Tipo: ${bloque.tipo} | % Cumplimiento: ${bloque.porcentajeCumplimiento}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        title: { display: true, text: 'Bloques del Año Escolar' }
                    },
                    y: {
                        beginAtZero: true,
                        max: totalDocentesFiltrado,
                        title: { display: true, text: 'Número de Docentes' },
                        ticks: { stepSize: Math.ceil(totalDocentesFiltrado / 10) }
                    }
                }
            },
            plugins: [fondoEtiquetasPlugin]
        });  
    }

    // =================== EVENTOS ===================
    ['filtroInstitucion','filtroDistrito','filtroRed','filtroNivel','filtroMes','filtroTexto'].forEach(id => {
        document.getElementById(id).addEventListener('input', aplicarFiltros);
    });


    document.addEventListener("DOMContentLoaded", function () {
        aplicarFiltros();
    });

</script>



@endsection
