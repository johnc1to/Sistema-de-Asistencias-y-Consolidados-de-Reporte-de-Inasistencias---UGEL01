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
                <span class="text-gray-500"><strong>Reporte Anexo 03</strong></span>
            </div>
            </li>
        </ol>
    </nav>
    <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte de Cumplimiento del Anexo 03 de Asistencia Detallado</h1>

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

            <!-- Mes -->
            <select id="filtroMes" class="border rounded-lg px-2 py-1">
                <option value="">Mes</option>
                @foreach($reportes->pluck('fecha_creacion')->unique() as $mes)
                    <option value="{{ strtolower($mes) }}">{{ $mes }}</option>
                @endforeach
            </select>

            <!-- Búsqueda libre -->
            <input type="text" id="filtroTexto" placeholder="🔎 DNI, Nombre, etc." 
                class="border rounded-lg px-2 py-1" />
        </div>
    </div>
    <div class="flex flex-wrap gap-4 mb-2 mt-6">
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

    <div class="mt-4">
        <div class="flex border-b">
            <button id="btnReporte" class="px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600">
                📄 Reporte General
            </button>
        </div>

        <div id="tablaReporte" class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 block">            
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


    document.addEventListener("DOMContentLoaded", function () {
        const btnReporte = document.getElementById("btnReporte");

        const tablaReporte = document.getElementById("tablaReporte");

        const botones = [btnReporte];
        const tablas = [tablaReporte];

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

        // Opcional: establecer pestaña inicial segura
        if (btnReporte && tablaReporte) activarTab(btnReporte, tablaReporte);
    });

    document.addEventListener("DOMContentLoaded", function () {
        const filtroTexto = document.getElementById("filtroTexto");
        const filtroInstitucion = document.getElementById("filtroInstitucion");
        const filtroDistrito = document.getElementById("filtroDistrito");
        const filtroNivel = document.getElementById("filtroNivel");
        const filtroObservacion = document.getElementById("filtroObservacion");
        conts filtroMes = document.getElementById("filtroMes")

        function aplicarFiltros() {
            // Tomamos valores
            const texto = filtroTexto.value.toLowerCase();
            const inst = filtroInstitucion.value;
            const dist = filtroDistrito.value;
            const niv = filtroNivel.value;
            const obs = filtroObservacion.value;

            // Recorremos ambas tablas
            document.querySelectorAll("#tablaReporte tbody tr").forEach(row => {
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
