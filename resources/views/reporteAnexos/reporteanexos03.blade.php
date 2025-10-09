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
    
    <!-- CONTADOR - CARDS DE ESTADO -->
    <div class="flex flex-wrap justify-center gap-6 mb-6 mt-8">
        {{-- 🔹 Card de contador de instituciones --}}
        <div class="bg-purple-100 border-t-4 border-purple-500 rounded-xl shadow-md p-6 w-72 text-center"
            data-estado="no registro" title="Filtrar por sin registro">
            <h3 class="text-purple-700 text-lg font-bold uppercase mb-2">Instituciones</h3>
            <p class="text-4xl font-extrabold text-purple-600 mb-2">{{ $institucionesUnicas }}</p>
            <p class="text-sm text-gray-700 font-medium">
               Total de instituciones únicas.
            </p>
        </div>
        {{-- 🔴 NO REGISTRARON --}}
        @if($directoresSinAnexo03->count())
        <div class="bg-red-100 border-t-4 border-red-500 rounded-xl shadow-md p-6 w-72 text-center cursor-pointer estado-card"
            data-estado="no registro" title="Filtrar por sin registro">
            <h3 class="text-red-700 text-lg font-bold uppercase mb-2">Sin registro</h3>
            <p class="text-4xl font-extrabold text-red-600 mb-2">{{ $porcSinAnexo03 }}%</p>
            <p class="text-sm text-gray-700 font-medium">
                {{ $directoresSinAnexo03->count() }} directores aún no reportan su Anexo 03.
            </p>
        </div>
        @endif

        {{-- 🟡 EN PROCESO --}}
        @if($directoresEnProceso->count())
        <div class="bg-yellow-100 border-t-4 border-yellow-500 rounded-xl shadow-md p-6 w-72 text-center cursor-pointer estado-card"
            data-estado="en proceso" title="Filtrar por en proceso">
            <h3 class="text-yellow-700 text-lg font-bold uppercase mb-2">En proceso</h3>
            <p class="text-4xl font-extrabold text-yellow-600 mb-2">{{ $porcEnProceso }}%</p>
            <p class="text-sm text-gray-700 font-medium">
                {{ $directoresEnProceso->count() }} directores están registrando su Anexo 03.
            </p>
        </div>
        @endif

        {{-- 🟢 COMPLETOS --}}
        @if($directoresCompletos->count())
        <div class="bg-green-100 border-t-4 border-green-500 rounded-xl shadow-md p-6 w-72 text-center cursor-pointer estado-card"
            data-estado="enviado" title="Filtrar por completados">
            <h3 class="text-green-700 text-lg font-bold uppercase mb-2">Enviados</h3>
            <p class="text-4xl font-extrabold text-green-600 mb-2">{{ $porcCompletos }}%</p>
            <p class="text-sm text-gray-700 font-medium">
                {{ $directoresCompletos->count() }} directores completaron con éxito su Anexo 03.
            </p>
        </div>
        @endif
    </div>
    <!-- filtro por estado (usado por las cards) -->
    <input type="hidden" id="filtroEstado" value="">
    <!-- LEYENDA -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-white shadow-md rounded-xl p-4 border border-gray-200 w-fit">
        <h2 class="text-sm font-semibold text-gray-700 uppercase">Leyenda de Estado</h2>

        <div class="flex flex-col sm:flex-row gap-3 text-sm">
            <!-- Rojo -->
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 bg-red-500 rounded-full"></span>
                <span class="text-gray-700">
                    <strong class="text-red-600">Rojo:</strong> Directores que no ingresaron al módulo del Anexo 03.
                </span>
            </div>

            <!-- Amarillo -->
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 bg-yellow-400 rounded-full"></span>
                <span class="text-gray-700">
                    <strong class="text-yellow-600">Amarillo:</strong> Directores que estan modificaciones del registro y no es información oficial.
                </span>
            </div>

            <!-- Verde -->
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 bg-green-500 rounded-full"></span>
                <span class="text-gray-700">
                    <strong class="text-green-600">Verde:</strong> Directores que reportaron su registro con oficio y expediente E-SINAD.
                </span>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="flex border-b">
            <button id="btnReporte" class="px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600">
                📄 Reporte General
            </button>
        </div>
        <!-- FILTRO -->
        <div class="flex flex-col lg:flex-row items-start justify-between gap-6 mb-6 mt-8">
            {{-- 🔹 Filtros --}}
            <div class="flex-1 bg-white rounded-xl shadow-md p-4">
                <h2 class="text-lg font-semibold mb-3 text-center">Filtros de búsqueda</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <!-- Búsqueda libre -->
                    <input type="text" id="filtroTexto" placeholder="🔎 DNI, Nombre, etc." 
                        class="border rounded-lg px-2 py-1" />

                    <!-- Institución -->
                    <select id="filtroInstitucion" class="border rounded-lg px-2 py-1">
                        <option value="">Institución</option>
                        @foreach($query->pluck('institucion')->unique() as $inst)
                            <option value="{{ strtolower($inst) }}">{{ $inst }}</option>
                        @endforeach
                    </select>

                    <!-- Distrito -->
                    <select id="filtroDistrito" class="border rounded-lg px-2 py-1">
                        <option value="">Distrito</option>
                        @foreach($query->pluck('distrito')->unique() as $dist)
                            <option value="{{ strtolower($dist) }}">{{ $dist }}</option>
                        @endforeach
                    </select>

                    <!-- Nivel -->
                    <select id="filtroNivel" class="border rounded-lg px-2 py-1">
                        <option value="">Nivel</option>
                        @foreach($query->pluck('nivel')->unique() as $niv)
                            <option value="{{ strtolower($niv) }}">{{ $niv }}</option>
                        @endforeach
                    </select>

                    <!-- Filtro de Mes -->
                    <select id="filtroMes" class="border rounded-lg px-2 py-1">
                        <option value="">Mes</option>
                        @foreach ($query->pluck('fecha_creacion')->filter()->unique()->sortDesc() as $mes)
                            <option value="{{ strtolower($mes) }}">{{ ucfirst($mes) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div id="tablaReporte" class="w-full mx-auto bg-white rounded-xl shadow-md p-6 mt-10 block">
            <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte de Cumplimiento de Anexos</h1>
            @if($query->isEmpty())
                <div class="text-center text-red-600 font-semibold mt-4">
                    No se encontraron registros. Los directores no han reportado o no han seguido el procedimiento.
                </div>
            @else
                <!-- Contenedor scrollable con header fijo -->
                <div class="overflow-y-auto max-h-[70vh] border border-gray-300 rounded-lg">
                    <table class="w-full table-auto border-collapse text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs text-center sticky top-0 z-10">
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
                                <th class="px-2 py-1 border">Expediente</th>
                                <th class="px-2 py-1 border">Mes</th>
                                <th class="px-2 py-1 border">PDF</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @foreach($query as $index => $reporte)
                                <tr class="hover:bg-gray-50"
                                    data-fecha="{{ strtolower($reporte->fecha_creacion ?? '') }}"
                                    data-anexo="{{ strtolower($reporte->Anexo03 ?? '') }}">
                                    <td class="px-2 py-1 border">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->codlocal }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->codmod }}</td>
                                    <td class="px-2 py-1 border truncate max-w-[150px]" title="{{ $reporte->institucion }}">{{ $reporte->institucion }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->nivel }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->modalidad }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->distrito }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->descgestie }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->dni_director }}</td>
                                    <td class="px-2 py-1 border truncate max-w-[130px]" title="{{ $reporte->director }}">{{ $reporte->director }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->celular_pers }}</td>
                                    <td class="px-2 py-1 border truncate max-w-[160px]" title="{{ $reporte->correo_inst }}">{{ $reporte->correo_inst }}</td>
                                    <td class="px-2 py-1 border truncate max-w-[160px]" title="{{ $reporte->correo_pers }}">{{ $reporte->correo_pers }}</td>

                                    <td class="px-2 py-1 border font-semibold anexo03
                                        @if($reporte->Anexo03 == 'EN PROCESO') text-yellow-600
                                        @elseif($reporte->Anexo03 == 'ENVIADO') text-green-600
                                        @else text-red-600 @endif">
                                        {{ $reporte->Anexo03 }}
                                    </td>
                                    <td class="px-2 py-1 border">{{ $reporte->expediente }}</td>
                                    <td class="px-2 py-1 border">{{ $reporte->fecha_creacion }}</td>
                                    <td class="px-4 py-2 border">
                                        @if($reporte->ruta_pdf)
                                            <a href="{{ asset('storage/'.$reporte->ruta_pdf) }}" target="_blank" class="text-blue-600 underline">
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



    document.addEventListener('DOMContentLoaded', function () {
        const filtroTexto = document.getElementById('filtroTexto');
        const filtroInstitucion = document.getElementById('filtroInstitucion');
        const filtroDistrito = document.getElementById('filtroDistrito');
        const filtroNivel = document.getElementById('filtroNivel');
        const filtroMes = document.getElementById('filtroMes');
        const filtroEstado = document.getElementById('filtroEstado');
        const filas = document.querySelectorAll('#tablaReporte tbody tr');

        const safeVal = el => el ? (el.value || '').trim().toLowerCase() : '';

        function aplicarFiltros() {
            const texto = safeVal(filtroTexto);
            const inst  = safeVal(filtroInstitucion);
            const dist  = safeVal(filtroDistrito);
            const niv   = safeVal(filtroNivel);
            const mes   = safeVal(filtroMes);
            const estado= safeVal(filtroEstado);

            filas.forEach(row => {
                let mostrar = true;
                const rowText = (row.innerText || '').toLowerCase();

                if (texto && !rowText.includes(texto)) mostrar = false;
                if (inst && !rowText.includes(inst)) mostrar = false;
                if (dist && !rowText.includes(dist)) mostrar = false;
                if (niv && !rowText.includes(niv)) mostrar = false;

                if (mes) {
                    const rowFecha = (row.dataset.fecha || '').toLowerCase();
                    if (!rowFecha.includes(mes)) mostrar = false;
                }

                if (estado) {
                    const rowAnexo = (row.dataset.anexo || '').toLowerCase();
                    if (!rowAnexo.includes(estado)) mostrar = false;
                }

                row.style.display = mostrar ? '' : 'none';
            });
        }

        // 🔹 Detectar si hay un mes en la URL
        const params = new URLSearchParams(window.location.search);
        const mesURL = params.get('mes'); // ej: "octubre 2025"

        // 🔹 Establecer el mes actual solo si no hay mes en la URL
        const mesActual = new Date().toLocaleString('es-ES', { month: 'long', year: 'numeric' }).toLowerCase();
        const mesPorDefecto = mesURL ? mesURL.toLowerCase() : mesActual;

        const opcionMes = Array.from(filtroMes.options).find(opt => opt.value === mesPorDefecto);
        if (opcionMes) {
            filtroMes.value = mesPorDefecto;
        }

        // 🔹 Mostrar el texto del mes actual o seleccionado
        const labelMes = document.getElementById('mesActualLabel');
        if (labelMes) {
            labelMes.textContent = `Mostrando registros del mes: ${mesPorDefecto.charAt(0).toUpperCase() + mesPorDefecto.slice(1)}`;
        }

        // 🔹 Eventos de filtros
        [filtroTexto, filtroInstitucion, filtroDistrito, filtroNivel, filtroMes].forEach(el => {
            if (!el) return;
            el.addEventListener('input', aplicarFiltros);
            el.addEventListener('change', aplicarFiltros);
        });

        // 🔹 Click en cards (estado)
        document.querySelectorAll('.estado-card').forEach(card => {
            card.addEventListener('click', function () {
                const estadoVal = (this.dataset.estado || '').trim().toLowerCase();
                if (!filtroEstado) return;

                if (filtroEstado.value.trim().toLowerCase() === estadoVal) {
                    filtroEstado.value = '';
                    this.classList.remove('ring-2', 'ring-blue-400');
                } else {
                    document.querySelectorAll('.estado-card').forEach(c => c.classList.remove('ring-2', 'ring-blue-400'));
                    this.classList.add('ring-2', 'ring-blue-400');
                    filtroEstado.value = estadoVal;
                }

                aplicarFiltros();
            });
        });

        // 🔹 Cambiar mes sin recargar la página
        if (filtroMes) {
            filtroMes.addEventListener('change', function () {
                const mesSeleccionado = this.value;
                if (!mesSeleccionado) return;

                // Actualizar el texto superior
                const labelMes = document.getElementById('mesActualLabel');
                if (labelMes) {
                    labelMes.textContent = `Mostrando registros del mes: ${mesSeleccionado.charAt(0).toUpperCase() + mesSeleccionado.slice(1)}`;
                }

                // Solo aplicar filtros, sin recargar
                aplicarFiltros();
            });
        }


        // 🔹 Aplicar filtros al cargar
        aplicarFiltros();
    });
</script>



@endsection
