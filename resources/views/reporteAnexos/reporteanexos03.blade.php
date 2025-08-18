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
    @if($directoresSinAnexo03->count())
        <div x-data="{ open: false }" class="mb-6">
            <button 
                @click="open = true"
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hover:bg-red-200 transition w-full text-left"
            >
                <strong class="font-bold">¡Atención!</strong>
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
    <form method="GET" class="flex flex-wrap gap-4 items-end mb-6">
        
        <!-- Distrito -->
        <div class="flex flex-col">
            <label for="distrito" class="text-sm font-medium text-gray-700">Distrito:</label>
            <select name="distrito" id="distrito" class="select2 w-64" data-placeholder="Todos los distritos">
                <option value="">Todos</option>
                @foreach($distritos as $dist)
                    <option value="{{ $dist }}" {{ request('distrito') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                @endforeach
            </select>
        </div>

        <!-- Institución Educativa (nombre + código) -->
        <div class="flex flex-col">
            <label for="institucion" class="text-sm font-medium text-gray-700">Institución Educativa:</label>
            <select name="institucion" id="institucion" class="select2 w-96" data-placeholder="Todas las IIEE">
                <option value="">Todas</option>
                @foreach($instituciones as $ie)
                    <option value="{{ $ie->codmod }} - {{ $ie->institucion }}" {{ request('institucion') == "$ie->codmod - $ie->institucion" ? 'selected' : '' }}>
                        {{ $ie->codmod }} - {{ $ie->institucion }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Nivel -->
        <div class="flex flex-col">
            <label for="nivel" class="text-sm font-medium text-gray-700">Nivel:</label>
            <select name="nivel" id="nivel" class="select2 w-64" data-placeholder="Todos los niveles">
                <option value="">Todos</option>
                @foreach($niveles as $niv)
                    <option value="{{ $niv }}" {{ request('nivel') == $niv ? 'selected' : '' }}>{{ $niv }}</option>
                @endforeach
            </select>
        </div>

        <!-- Botones -->
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Filtrar</button>
            <a href="{{ route('reporte.anexos03') }}" class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600">Limpiar Filtros</a>
        </div>
    </form>

    <div class="mt-4">
        <div class="flex border-b">
            <button id="btnReporte" class="px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600">
                📄 Reporte General
            </button>
            <button id="btnObservaciones" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                ⚠️ Observaciones Críticas
            </button>
        </div>
        <div id="tablaReporte" class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 block">
            <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte Anexo 03</h1>
            
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
            @if($observacionesCriticas->count())
                <div id="tablaObservaciones" class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 hidden">
                    <h2 class="text-xl font-bold text-red-600 mb-4">⚠️ Alertas de Docentes con Observaciones Críticas</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border rounded-xl text-sm">
                            <thead class="bg-red-100 text-red-800 uppercase text-xs text-center">
                                <tr>
                                    <th class="px-2 py-1 border">DNI</th>
                                    <th class="px-2 py-1 border">Nombre</th>
                                    <th class="px-2 py-1 border">Cargo</th>
                                    <th class="px-2 py-1 border">Condición</th>
                                    <th class="px-2 py-1 border">Tipo Observación</th>
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
                                            data-fecha="{{ \Carbon\Carbon::parse($item->fecha_creacion)->format('d/m/Y') }}"
                                        >
                                        {{ $item->institucion }}</td>
                                        <td class="px-2 py-1 border">{{ $item->distrito_iiee }}</td>
                                        <td class="px-2 py-1 border">{{ \Carbon\Carbon::parse($item->fecha_creacion)->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
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
    const btnObservaciones = document.getElementById("btnObservaciones");
    const tablaReporte = document.getElementById("tablaReporte");
    const tablaObservaciones = document.getElementById("tablaObservaciones");

    btnReporte.addEventListener("click", () => {
        tablaReporte.classList.remove("hidden");
        tablaObservaciones.classList.add("hidden");

        btnReporte.classList.add("text-blue-600", "border-blue-600", "border-b-2");
        btnReporte.classList.remove("text-gray-500");

        btnObservaciones.classList.remove("text-blue-600", "border-blue-600", "border-b-2");
        btnObservaciones.classList.add("text-gray-500");
    });

    btnObservaciones.addEventListener("click", () => {
        tablaReporte.classList.add("hidden");
        tablaObservaciones.classList.remove("hidden");

        btnObservaciones.classList.add("text-blue-600", "border-blue-600", "border-b-2");
        btnObservaciones.classList.remove("text-gray-500");

        btnReporte.classList.remove("text-blue-600", "border-blue-600", "border-b-2");
        btnReporte.classList.add("text-gray-500");
    });
});

</script>



@endsection
