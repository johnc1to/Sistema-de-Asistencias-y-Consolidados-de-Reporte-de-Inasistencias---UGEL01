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
                <span class="text-gray-500"><strong>Reporte Log Ingresos</strong></span>
            </div>
            </li>
        </ol>
    </nav>

    <div class="w-full max-w-6xl mx-auto bg-white rounded-xl shadow-md p-4 mt-6">
        <h2 class="text-lg font-semibold mb-3">Filtros de búsqueda</h2>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <!-- Institución -->
            <select id="filtroDni" class="border rounded-lg px-2 py-1">
                <option value="">DNI</option>
            </select>

            <!-- Sistema -->
            <select id="filtroSistema" class="border rounded-lg px-2 py-1">
                <option value="">SISTEMA</option>
            </select>

            <!-- Cargo -->
            <select id="filtroCargo" class="border rounded-lg px-2 py-1">
                <option value="">CARGO</option>
            </select>
            <!-- Área -->
            <select id="filtroArea" class="border rounded-lg px-2 py-1">
                <option value="">ÁREA</option>
            </select>
            <!-- Regimen -->
            <select id="filtroRegimen" class="border rounded-lg px-2 py-1">
                <option value="">REGIMEN</option>
            </select>
            <!-- Búsqueda libre -->
            <input type="text" id="filtroTexto" placeholder="🔎 DNI, Nombre, etc." 
                class="border rounded-lg px-2 py-1" />
        </div>
    </div>
   
    <div class="mt-4">
        <div class="flex border-b">
            <button id="btnReporte" class="px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600">
                📄 Reporte General de Ingresos
            </button>
            <button id="btnBloque" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                📊 Estadistica
            </button>
            <button id="btnReporte2" class="px-4 py-2 font-semibold text-gray-500 hover:text-blue-600">
                📄 Reporte General de Ultimos Ingresos por Usuario
            </button> 
        </div>

        <div id="tablaReporte" class="w-full max-w-[1530px] mx-auto bg-white rounded-xl shadow-md p-6 mt-10 block">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 text-sm md:text-base">
                <!-- Total registros -->
                <div class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-xl shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 font-medium">Total registros</p>
                        <p id="totalRegistros" class="text-xl font-bold text-blue-700">0</p>
                    </div>
                </div>

                <!-- Sistema más visitado -->
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-xl shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 font-medium">Sistema más visitado</p>
                        <p id="sistemaMasVisitado" class="text-xl font-bold text-green-700">-</p>
                    </div>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte de Ingreso a los Sistemas de la UGEL 01 - SJM</h1>
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full border rounded-xl text-sm">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs text-center">
                        <tr>
                            <th class="px-2 py-1 border whitespace-nowrap">Correo</th>
                            <th class="px-2 py-1 border whitespace-nowrap">DNI</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Nombre</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Sistema</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Ingreso</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Salida</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Duracion</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Cargo</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Area</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Cod. Area</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Regimen</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" id="tablaReporteBody">
                        @foreach($logs as $log)
                            <tr>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->correo }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->extra->DocPer ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->nombre }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->nomSis }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->fecha }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->fecha_salida ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->duracion ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->extra->NomCar ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->extra->DesOrg ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->extra->CodSin ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $log->extra->descripcion ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div id="tablaBloque" class="hidden p-6">
            <h1 class="text-xl font-bold text-center mb-4">
                📊 RESUMEN ESTADÍSTICO DE INGRESOS
            </h1>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Accesos diarios -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-2">Accesos diarios</h3>
                    <canvas id="chartPorDia"></canvas>
                </div>
                <!-- Top 5 usuarios -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-2">Top 5 usuarios más activos</h3>
                    <canvas id="chartTopUsuarios"></canvas>
                </div>
                <!-- Accesos por sistema -->
                <div class="bg-white p-4 rounded-xl shadow col-span-1 md:col-span-2">
                    <h3 class="text-lg font-semibold mb-2">Accesos por sistema</h3>
                    <canvas id="chartPorSistema"></canvas>
                </div>
            </div>
        </div>
        <div id="tablaReporte2" class="hidden w-full max-w-[1530px] mx-auto bg-white rounded-xl shadow-md p-6 mt-10 block">
            <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte de Ultimos Ingresos por Usuario</h1>    
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full border rounded-xl text-sm">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs text-center">
                        <tr>
                            <th class="px-2 py-1 border whitespace-nowrap">Correo</th>
                            <th class="px-2 py-1 border whitespace-nowrap">DNI</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Nombre</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Sistema</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Ingreso</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Cargo</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Area</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Cod. Area</th>
                            <th class="px-2 py-1 border whitespace-nowrap">Regimen</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($ultimosIngresos as $last)
                            <tr>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->correo }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->extra->DocPer ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->nombre }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->nomSis }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->fecha }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->extra->NomCar ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->extra->DesOrg ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->extra->CodSin ?? '-' }}</td>
                                <td class="px-2 py-1 border whitespace-nowrap">{{ $last->extra->descripcion ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
<!-- Carga de Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- CDN de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    //manejo de ventanas
    document.addEventListener("DOMContentLoaded", function () {
        const btnReporte = document.getElementById("btnReporte");
        const btnReporte2 = document.getElementById("btnReporte2");
        const btnBloque = document.getElementById("btnBloque");

        const tablaReporte = document.getElementById("tablaReporte");
        const tablaReporte2 = document.getElementById("tablaReporte2");
        const tablaBloque = document.getElementById("tablaBloque");

        const botones = [btnReporte, btnReporte2, btnBloque];
        const tablas = [tablaReporte, tablaReporte2, tablaBloque];

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
        if (btnBloque) btnBloque.addEventListener("click", () => activarTab(btnBloque, tablaBloque));

        // Opcional: establecer pestaña inicial segura
        if (btnReporte && tablaReporte) activarTab(btnReporte, tablaReporte);
    });
    //filtro
    document.addEventListener("DOMContentLoaded", function () {
        const filtroTexto = document.getElementById("filtroTexto");
        const filtroDni = document.getElementById("filtroDni");
        const filtroSistema = document.getElementById("filtroSistema");
        const filtroCargo = document.getElementById("filtroCargo");
        const filtroArea = document.getElementById("filtroArea");
        const filtroRegimen = document.getElementById("filtroRegimen");

        // 🔹 Función para llenar selects con valores únicos
        function llenarSelect(select, valores, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            valores.forEach(v => {
                if (v && v.trim() !== "-") {
                    select.innerHTML += `<option value="${v}">${v}</option>`;
                }
            });
        }
        // 🔹 Función para llenar selects con valores únicos y ordenados
        function llenarSelect(select, valores, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            valores
                .filter(v => v && v.trim() !== "-") // elimina vacíos y guiones
                .sort((a, b) => a.localeCompare(b, 'es', { sensitivity: 'base' })) // ordena alfabéticamente
                .forEach(v => {
                    select.innerHTML += `<option value="${v}">${v}</option>`;
                });
        }

        // 🔹 Extraer valores únicos de la tabla
        const filas = document.querySelectorAll("#tablaReporteBody tr");
        const setDni = new Set();
        const setSistema = new Set();
        const setCargo = new Set();
        const setArea = new Set();
        const setRegimen = new Set();

        filas.forEach(row => {
            const celdas = row.querySelectorAll("td");

            // Ajusta según el orden de columnas en tu tabla
            setDni.add(celdas[1]?.innerText.trim());     // DocPer
            setSistema.add(celdas[3]?.innerText.trim()); // Sistema
            setCargo.add(celdas[7]?.innerText.trim());   // Cargo
            setArea.add(celdas[8]?.innerText.trim());    // Área
            setRegimen.add(celdas[9]?.innerText.trim()); // Régimen
        });

        // Llenamos los selects
        llenarSelect(filtroDni, [...setDni], "DNI");
        llenarSelect(filtroSistema, [...setSistema], "SISTEMA");
        llenarSelect(filtroCargo, [...setCargo], "CARGO");
        llenarSelect(filtroArea, [...setArea], "ÁREA");
        llenarSelect(filtroRegimen, [...setRegimen], "REGIMEN");

        // 🔹 Filtrado
        function aplicarFiltros() {
            const texto = filtroTexto.value.toLowerCase();
            const dni = filtroDni.value.toLowerCase();
            const sist = filtroSistema.value.toLowerCase();
            const cargo = filtroCargo.value.toLowerCase();
            const area = filtroArea.value.toLowerCase();
            const reg = filtroRegimen.value.toLowerCase();

            let total = 0;
            let conteoSistemas = {};

            filas.forEach(row => {
                let mostrar = true;
                const contenidoFila = row.textContent.toLowerCase();

                if (texto && !contenidoFila.includes(texto)) mostrar = false;
                if (dni && !row.innerText.toLowerCase().includes(dni)) mostrar = false;
                if (sist && !row.innerText.toLowerCase().includes(sist)) mostrar = false;
                if (cargo && !row.innerText.toLowerCase().includes(cargo)) mostrar = false;
                if (area && !row.innerText.toLowerCase().includes(area)) mostrar = false;
                if (reg && !row.innerText.toLowerCase().includes(reg)) mostrar = false;

                row.style.display = mostrar ? "" : "none";

                // Contamos solo las visibles
                if (mostrar) {
                    total++;
                    const sistema = row.cells[3]?.innerText.trim();
                    if (sistema) {
                        conteoSistemas[sistema] = (conteoSistemas[sistema] || 0) + 1;
                    }
                }
            });

            // Actualizar contadores
            document.getElementById("totalRegistros").innerText = total;

            let topSistema = "-";
            let max = 0;
            for (const [sistema, cant] of Object.entries(conteoSistemas)) {
                if (cant > max) {
                    max = cant;
                    topSistema = `${sistema} (${cant})`;
                }
            }
            document.getElementById("sistemaMasVisitado").innerText = topSistema;
        }

        // 🔹 Eventos
        [filtroTexto, filtroDni, filtroSistema, filtroCargo, filtroArea, filtroRegimen].forEach(el => {
            el.addEventListener("input", aplicarFiltros);
            el.addEventListener("change", aplicarFiltros);
        });
    });
    //estadistica
    document.addEventListener("DOMContentLoaded", function () {
        // Datos desde PHP
        const porDia = @json($porDia);
        const porSistema = @json($porSistema);
        const topUsuarios = @json($topUsuarios);

        // Accesos diarios (línea)
        new Chart(document.getElementById('chartPorDia'), {
            type: 'line',
            data: {
                labels: Object.keys(porDia),
                datasets: [{
                    label: 'Ingresos',
                    data: Object.values(porDia),
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0,0,255,0.2)',
                    fill: true,
                    tension: 0.2
                }]
            }
        });

        // Top 5 usuarios (horizontal bar)
        new Chart(document.getElementById('chartTopUsuarios'), {
            type: 'bar',
            data: {
                labels: Object.keys(topUsuarios),
                datasets: [{
                    label: 'Ingresos',
                    data: Object.values(topUsuarios),
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }]
            },
            options: {
                indexAxis: 'y'
            }
        });

        // Accesos por sistema (cada sistema como dataset)
        const sistemasFiltrados = Object.entries(porSistema);
        const labels = sistemasFiltrados.map(([sistema]) => sistema);
        const dataValores = sistemasFiltrados.map(([_, valor]) => valor);

        new Chart(document.getElementById('chartPorSistema'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValores,
                    backgroundColor: labels.map((_, i) => `hsl(${i * 50}, 70%, 60%)`)
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });

    function actualizarContadores() {
        const filas = document.querySelectorAll("#tablaReporteBody tr");
        let total = 0;
        let conteoSistemas = {};

        filas.forEach(fila => {
            if (fila.style.display !== "none") { // solo las visibles
                total++;
                const sistema = fila.cells[3].innerText.trim(); // Columna Sistema
                conteoSistemas[sistema] = (conteoSistemas[sistema] || 0) + 1;
            }
        });

        document.getElementById("totalRegistros").innerText = total;

        // Encontrar el sistema más visitado
        let topSistema = "-";
        let max = 0;
        for (const [sistema, cant] of Object.entries(conteoSistemas)) {
            if (cant > max) {
                max = cant;
                topSistema = `${sistema} (${cant})`;
            }
        }
        document.getElementById("sistemaMasVisitado").innerText = topSistema;
    }

    // Inicializar al cargar
    document.addEventListener("DOMContentLoaded", actualizarContadores);
</script>

@endsection