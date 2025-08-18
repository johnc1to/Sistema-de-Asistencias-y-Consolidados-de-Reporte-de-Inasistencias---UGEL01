@extends('layout_especialista/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak]{
        display:none !important;
    }
</style>
    @if($directoresSinAnexo04->count())
        <div x-data="{ open: false }" class="mb-6">
            <button 
                @click="open = true"
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hover:bg-red-200 transition w-full text-left"
            >
                <strong class="font-bold">¡Atención!</strong>
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
            <a href="{{ route('reporte.anexos04') }}" class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600">Limpiar Filtros</a>
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
            <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte Anexo 04</h1>
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
        
        <div id="tablaObservaciones" class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 hidden">
            <h3 class="text-xl font-bold text-red-600 mb-4">📊 Resumen de inasistencias, tardanzas, permisos, Huelga o paro,</h3> 
            @if($personasConInasistencia->isEmpty())
                <div class="text-center text-red-600 font-semibold mt-4">
                    No se encontraron descuentos. Los directores no han reportado o no han seguido el procedimiento.
                </div>
            @else
                <div class="overflow-x-auto border rounded">
                    <table class="min-w-full text-sm text-center border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
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
        <!-- Modal centrado -->
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

    const bloques = [
        {tipo:'GESTIÓN', inicio:'2025-03-03', fin:'2025-03-14'},
        {tipo:'LECTIVA', inicio:'2025-03-17', fin:'2025-05-16'},
        {tipo:'GESTIÓN', inicio:'2025-05-19', fin:'2025-05-23'},
        {tipo:'LECTIVA', inicio:'2025-05-26', fin:'2025-07-25'},
        {tipo:'GESTIÓN', inicio:'2025-07-28', fin:'2025-08-08'},
        {tipo:'LECTIVA', inicio:'2025-08-11', fin:'2025-10-10'},
        {tipo:'GESTIÓN', inicio:'2025-10-13', fin:'2025-10-17'},
        {tipo:'LECTIVA', inicio:'2025-10-20', fin:'2025-12-19'},
        {tipo:'GESTIÓN', inicio:'2025-12-22', fin:'2025-12-31'},
    ];

    const personasConInasistencia = @json($personasConInasistencia);

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
                const colorTipo = bloque.tipo === 'GESTIÓN' ? 'bg-blue-100 text-blue-800 font-bold' :
                  bloque.tipo === 'LECTIVA' ? 'bg-green-100 text-green-800 font-bold' : '';
                if (bloque) {
                    tbody.innerHTML += `<tr>
                        <td class="border px-2 py-1">${fecha}</td>
                        <td class="border px-2 py-1">${bloques.indexOf(bloque)+1}</td>
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

</script>



@endsection
