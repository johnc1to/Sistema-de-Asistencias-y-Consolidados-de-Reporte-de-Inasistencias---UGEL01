@extends('layout_director/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<style>
        #modalDocente {
            z-index: 9999; /* Asegúrate de que el modal esté encima de otros elementos */
        }

</style>
<meta name="guardar-firma-url" content="{{ route('guardar.firma.director') }}">

    <div class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6">
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">ANEXO 03</h1>
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">Formato 01: Reporte de Asistencia Detallado</h1>

        <!-- Información de la institución y nivel -->
        <div class="mb-4 flex flex-wrap justify-between items-center gap-4">
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
        <p class="text-sm font-medium mt-[2px]">PERIODO: MAYO 2025</p>
        <p class="text-sm font-medium">Turno: {{ $d_cod_tur }}</p>
    </div>

            <form method="GET" action="{{ url('/reporte_anexo03') }}" class="flex flex-wrap items-center gap-4">

                <!-- Botón Guardar Asistencia Masiva -->
                <button id="guardarTodo" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Guardar Asistencia Masiva
                </button>

                <!-- Selector de Nivel -->
                <div class="flex items-center ml-auto">
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
    <div class="mb-4">
        <div class="overflow-auto border rounded max-h-[500px] w-full">
            <div class="min-w-[900px] w-full">
                <table class="min-w-[1200px] w-full text-sm table-auto border-collapse">
                    @php
                        use Carbon\Carbon;
                        $mes = $mes ?? 3;
                        $anio = $anio ?? 2025;
                        $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;
                        $feriados = ['2025-05-01'];
                        $diasSemana = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];
                        $fechaHoy = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
                    @endphp

                    <thead class="bg-gray-200 text-gray-700 uppercase text-xs sticky top-0 z-10">
                        <tr>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">Nº</th>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">DNI</th>
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

                    <tbody class="bg-white">
                                @forelse ($registros as $index => $r)
                                    @php
                                $asistenciaPersona = $asistencias[$r->dni] ?? null;
                                @endphp
                                <tr class="hover:bg-gray-100"
                                    data-dni="{{ $r->dni }}"
                                    data-nombres="{{ $r->nombres }}"
                                    data-cargo="{{ $r->cargo }}"
                                    data-condicion="{{ $r->condicion }}"
                                    data-jornada="{{ $r->jornada }}"
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
                                <td class="border px-2 py-1 cursor-pointer text-blue-500" onclick="openModal('{{ $r->dni }}', '{{ $r->nombres }}')">
                                    {{ $r->dni }}
                                </td>
                                <td class="border px-2 py-1 text-left">{{ $r->nombres }}</td>
                                <td class="border px-2 py-1">{{ $r->cargo }}</td>
                                <td class="border px-2 py-1">{{ $r->condicion }}</td>
                                <td class="border px-2 py-1">{{ $r->jornada }}</td>

                                @php
                                    $dni = $r->dni;
                                    $asistenciaPersona = $asistencias[$dni] ?? ['asistencia' => [], 'observacion' => null];
                                    $observacion = $asistenciaPersona['observacion'] ?? null;
                                @endphp

                                @if ($observacion)
                                <td class="text-center text-red-600 font-semibold italic bg-red-50" colspan="{{ $diasEnMes }}">
                                    {{ $observacion }}
                                </td>
                            @else
                                @for ($d = 1; $d <= $diasEnMes; $d++)
                                @php
                                    $fecha = Carbon::create($anio, $mes, $d);
                                    $nombreDia = $diasSemana[$fecha->dayOfWeek];
                                    $esFeriado = in_array($fecha->format('Y-m-d'), $feriados);

                                    if (!empty($asistenciaPersona['asistencia'])) {
                                        $valor = $asistenciaPersona['asistencia'][$d - 1] ?? '';
                                    } else {
                                        $valor = in_array($nombreDia, ['S', 'D']) ? '' : ($esFeriado ? 'F' : 'A');
                                    }

                                    $claseFondo = match (true) {
                                        $valor === 'F' => 'bg-red-100',
                                        in_array($nombreDia, ['S', 'D']) => 'bg-gray-100',
                                        default => '',
                                    };

                                    // Códigos válidos que deben ir en negrita
                                    $codigosNegrita = [ 'I', '3T', 'J', 'L', 'P', 'T', 'H', 'F'];
                                    $claseTexto = in_array(strtoupper($valor), $codigosNegrita) ? 'font-bold' : '';
                                @endphp
                                <td class="border px-1 py-1 text-sm asistencia-celda text-center {{ $claseFondo }}" data-id="{{ $r->dni }}" data-dia="{{ $d }}">
                                    <span class="asistencia-valor {{ $claseTexto }}">{{ $valor ?? '' }}</span>
                                </td>
                            @endfor
                            @endif
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
            <span><strong>3T:</strong> Tercera Tardanza, considerada como inasistencia injustificada</span>
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
    <div class="flex flex-col items-center">
        <button id="btnOficio" onclick="openModal2()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Ingresar número de oficio
        </button>
        <p id="previewOficio" class="mt-2 font-bold text-blue-800"></p>
    </div>
    <!-- Firma: Botón y vista previa -->

    <div class="flex flex-col items-center">
        <button onclick="openFirmaModal()"
            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
            Ingresar firma
        </button>

        @if (!empty($firmaGuardada))
            <img id="firmaPreview" src="{{ asset('storage/firmasdirector/' . $firmaGuardada) }}" alt="Firma"
                class="mt-2" style="height: 80px;">
        @else
            <img id="firmaPreview" src="" alt="Firma temporal" class="hidden mt-2" style="height: 80px;">
        @endif
    </div>
    {{-- Botón de exportar PDF --}}
    <form id="exportarForm" method="POST" action="{{ route('asistencia.exportar.pdf', ['nivel' => $nivelSeleccionado]) }}" target="_blank">
        @csrf
        <input type="hidden" name="numero_oficio" id="campoNumeroOficio">
        <input type="hidden" name="firma_base64" id="campoFirmaBase64">

        <div class="flex flex-col items-center">
            <button type="submit"
                onclick="antesDeExportar()"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Exportar en PDF
            </button>
        </div>
    </form>

    

    
</div>
    <!-- CSRF para JS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Campo oculto -->
    <input type="hidden" id="oficio_guardado">

    <!-- Modal para subir la firma -->
<div id="modalFirma" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4 text-center">Subir firma</h2>

        <div class="mb-3 p-2 bg-yellow-100 text-yellow-800 text-sm rounded border border-yellow-300">
            Si no marcas la opción de guardar firma, esta se usará solo de forma temporal en el presente documento y deberás volver a subirla cada vez antes de generar el reporte.
        </div>

        <input type="file" id="firmaInput" accept="image/*"
            class="w-full border border-gray-300 rounded px-3 py-2 mb-4">

        <div class="mb-4">
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

        <div class="flex justify-end space-x-2">
            <button onclick="closeFirmaModal()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded">Cancelar</button>
            <button onclick="guardarFirma()" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded">Guardar</button>
        </div>
    </div>
</div>


    <!-- Modal Oficio-->
    <div id="modalOficio" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4 text-center">Número de Oficio</h2>

            <label for="numeroOficio" class="block text-sm font-medium text-gray-700 mb-1">
                Ingrese el número:
            </label>
            <input type="number" id="numeroOficio"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    min="0">

            <div class="flex justify-end space-x-2">
                <button onclick="closeModal()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded">Cancelar</button>
                <button onclick="guardarOficio()" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal con leyenda, datalist, tabla editable , validación y observación -->
    <div id="modalForm" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex justify-center items-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-[90%] max-w-4xl p-6 overflow-auto max-h-[90vh]">
            <h2 class="text-xl font-bold mb-4" id="modalTitle">Modificar Asistencia</h2>
            <form id="asistenciaForm">
            <input type="hidden" name="dni" id="dni">

            <!-- Leyenda -->
            <div class="mb-4 text-sm bg-blue-50 p-4 rounded-lg">
            <p><strong>Leyenda:</strong></p>
            <div class="mt-1 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2 text-gray-700">
                <span><strong>A:</strong> Día laborado</span>
                <span><strong>I:</strong> Inasistencia injustificada</span>
                <span><strong>3T:</strong> Tercera Tardanza, considerada como inasistencia injustificada</span>
                <span><strong>J:</strong> Inasistencia justificada</span>
                <span><strong>L:</strong> Licencia sin goce de remuneraciones</span>
                <span><strong>P:</strong> Permiso sin goce de remuneraciones</span>
                <span><strong>T:</strong> Tardanza</span>
                <span><strong>H:</strong> Huelga o paro</span>
            </div>

            </div>
            <!-- Aplicar patrón -->
                <div class="mt-4 bg-gray-50 p-4 rounded-lg border">
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

            <!-- Tabla editable -->
            <div class="overflow-auto mb-4 mt-4">
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
            <div class="mt-4">
                <label for="tipo_observacion" class="block text-sm font-semibold mb-1">Tipo de Observación:</label>
                <select id="tipo_observacion" name="tipo_observacion" class="w-full border rounded p-2 text-sm">
                    <option value="" selected>-- Seleccione --</option>
                    <option value="Cese">Cese</option>
                    <option value="Renuncia">Renuncia</option>
                    <option value="Licencia">Licencia</option>
                    <option value="Abandono de Cargo">Abandono de Cargo</option>
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
            <div class="mt-4">
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
const feriados = ['2025-05-01']; 
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


    function openModal2() {
        document.getElementById('modalOficio').classList.remove('hidden');
        const current = document.getElementById('oficio_guardado').value;
        if (current) {
            document.getElementById('numeroOficio').value = current;
        }
    }

    function closeModal() {
        document.getElementById('modalOficio').classList.add('hidden');
    }

    function guardarOficio() {
        const nro = document.getElementById('numeroOficio').value.trim();
        if (!nro) return;

        document.getElementById('oficio_guardado').value = nro;
        document.getElementById('previewOficio').innerText = 'Oficio N° ' + nro;

        // Cambiar texto del botón
        document.getElementById('btnOficio').innerText = 'Editar número de oficio';

        closeModal();
    }

    function antesDeExportar() {
        document.getElementById('campoNumeroOficio').value = document.getElementById('oficio_guardado').value;
        document.getElementById('campoFirmaBase64').value = firmaBase64 ?? '';
    }


let dniActual = '';
let mes = 5, anio = 2025;

function openModal(dni, nombres) {
    dniActual = dni;

    const title = document.getElementById('modalTitle');
    const filaNumeros = document.getElementById('dias-numeros');
    const filaLetras = document.getElementById('dias-letras');
    const filaInputs = document.getElementById('fila-asistencia');

    title.textContent = `Modificar Asistencia de ${nombres}`;
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

        // 🔍 Buscar valor actual en la tabla principal
        const selector = `.asistencia-celda[data-id="${dni}"][data-dia="${dia}"] .asistencia-valor`;
        const celda = document.querySelector(selector);
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

    // 🟨 Recuperar observación y tipo de observación si existen en el <tr>
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
        .map(cb => parseInt(cb.value)); // [1, 2, 4]

    const inputs = document.querySelectorAll('.asistencia-input');

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

document.getElementById('saveAsistencia').addEventListener('click', () => {
    const observacion = document.getElementById('observacion')?.value.trim() || '';
    const tipoObservacion = document.getElementById('tipo_observacion')?.value || '';
    const fila = document.querySelector(`tr[data-dni="${dniActual}"]`);

    if (fila) {
        if (observacion !== '') {
            const diasEnMes = new Date(anio, mes, 0).getDate();

            // Eliminar TODAS las celdas desde la 7ma (índice 6) en adelante
            let celda = fila.children[6];
            while (celda) {
                const siguiente = celda.nextSibling;
                celda.remove();
                celda = siguiente;
            }

            // Insertar una sola celda con solo la observación (NO mostrar tipo)
            const td = document.createElement('td');
            td.colSpan = diasEnMes;
            td.className = "text-center text-red-600 font-semibold italic bg-red-50";
            td.textContent = observacion; // ← solo la observación
            fila.appendChild(td);

            // Guardar también el tipo de observación como atributo en la fila
            fila.setAttribute('data-tipo-observacion', tipoObservacion); // ← para el guardado
            fila.setAttribute('data-observacion', observacion); // ← para el guardado
        } else {
            // Procesar inputs normalmente
            const inputs = document.querySelectorAll('.asistencia-input');

            // Eliminar observación previa si existe
            const celdaObservacion = fila.querySelector('td[colspan]');
            if (celdaObservacion) {
                celdaObservacion.remove();
                // Aquí podrías regenerar las celdas de asistencia si necesitas
            }

            // Limpiar atributo de tipo observación si se eliminó
            fila.removeAttribute('data-tipo-observacion');

            inputs.forEach(input => {
                const dia = input.dataset.dia;
                const valor = input.value.toUpperCase();
                const celda = document.querySelector(`.asistencia-celda[data-id="${dniActual}"][data-dia="${dia}"]`);
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
    }

    // Limpiar y cerrar modal
    document.getElementById('observacion').value = '';
    document.getElementById('tipo_observacion').value = '';
    document.getElementById('modalForm').classList.add('hidden');
});








document.getElementById('guardarTodo').addEventListener('click', function () {
    const filas = document.querySelectorAll('tr[data-dni]');
    const docentes = [];

    filas.forEach(fila => {
        const dni = fila.getAttribute('data-dni');
        const nombres = fila.getAttribute('data-nombres');
        const cargo = fila.getAttribute('data-cargo');
        const condicion = fila.getAttribute('data-condicion');
        const jornada = fila.getAttribute('data-jornada');

        let asistencia = [];
        let observacion = null;
        let tipo_observacion = null;

        const celdaObservacion = fila.querySelector('td[colspan]');

        if (celdaObservacion) {
            observacion = celdaObservacion.textContent.trim();
            tipo_observacion = fila.getAttribute('data-tipo-observacion') || null;
        } else {
            asistencia = Array.from(fila.querySelectorAll('.asistencia-valor'))
                .map(cell => cell.textContent.trim());
        }

        docentes.push({
            dni,
            nombres,
            cargo,
            condicion,
            jornada,
            asistencia,
            observacion,
            tipo_observacion
        });
    });

    const payload = {
        id_contacto: {{ session('siic01.id_contacto') }},
        codlocal: "{{ session('siic01.conf_permisos')[0]['codlocal'] ?? '000000' }}",
        nivel: document.getElementById('nivel').value,
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
    .then(response => {
        if (!response.ok) throw new Error("Error al guardar");
        return response.json();
    })
    .then(data => {
        alert("Guardado exitoso.");
    })
    .catch(async error => {
    if (error.response) {
        // Error de servidor con respuesta
        try {
            const errorText = await error.response.text();
            console.error("Error de servidor:", errorText);
        } catch (e) {
            console.error("Error al leer respuesta del servidor:", e);
        }
    } else {
        // Error de red o excepción en JavaScript
        console.error("Error de red o JS:", error);
    }
});

});


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
        observacionTextarea.value = observacionTextarea.value.trim() + ` Licencia del ${inicio} al ${fin}`;
    }
}

function limpiarFechasEnObservacion() {
    observacionTextarea.value = observacionTextarea.value.replace(/\[Licencia del .*? al .*?\]/g, '').trim();
}

function formatFecha(fechaStr) {
    const [a, m, d] = fechaStr.split('-');
    return `${d}-${m}-${a}`;
}


</script>


@endsection
