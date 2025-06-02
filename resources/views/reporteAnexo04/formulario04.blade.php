@extends('layout_director/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<style>
        #modalInasistencia {
            z-index: 9999; /* Asegúrate de que el modal esté encima de otros elementos */
        }

</style>
<meta name="guardar-firma-url" content="{{ route('guardar.firma.director') }}">

    <div class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6">
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">ANEXO 04</h1>
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">Formato 02: REPORTE CONSOLIDADO DE INASISTENCIAS, TARDANZAS Y PERMISOS SIN GOCE DE REMUNERACIONES</h1>

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
       <!-- Ya NO necesitas method="POST" ni action -->
        <button id="guardarTodoInasistencia" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Guardar Reporte de Consolidado Masiva
        </button>


        <form method="GET" action="{{ url('/reporte_anexo04') }}" class="flex flex-wrap items-center gap-4">
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
        <div class="min-w-[1200px] w-full">
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

                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">
                        <div class="flex flex-col h-full">
                            <div class="border-b border-white py-1 text-center">Inasistencias</div>
                            <div class="py-1 text-xs font-semibold text-center">Días</div>
                        </div>
                    </th>

                            <th class="border px-2 py-1 bg-gray-200 text-center" colspan="2">
                                Tardanzas<br>
                            </th>

                            <th class="border px-2 py-1 bg-gray-200 text-center" colspan="2">
                                Permisos SG<br>
                            </th>

                            <th class="border px-2 py-0 bg-gray-200 text-center" rowspan="2">
                        <div class="flex flex-col h-full">
                            <div class="border-b border-white py-1">Huelga / Paro</div>
                            <div class="py-1 text-xs font-semibold">Días</div>
                        </div>
                    </th>
                            <th class="border px-2 py-1 bg-gray-200" rowspan="2">Observaciones</th>
                        </tr>
                        <tr>
                            <th class="border px-2 py-0 bg-gray-200 text-xs font-normal">
                                <div class="flex flex-col items-center justify-center h-full leading-tight">
                                    <div class="font-semibold">Horas</div>
                                    <div>(*)</div>
                                </div>
                            </th>
                            <th class="border px-2 py-0 bg-gray-200 text-xs font-normal">
                                <div class="flex flex-col items-center justify-center h-full leading-tight">
                                    <div class="font-semibold">Minutos</div>
                                    <div>(*)</div>
                                </div>
                            </th>
                            <th class="border px-2 py-0 bg-gray-200 text-xs font-normal">
                                <div class="flex flex-col items-center justify-center h-full leading-tight">
                                    <div class="font-semibold">Horas</div>
                                    <div>(*)</div>
                                </div>
                            </th>
                            <th class="border px-2 py-0 bg-gray-200 text-xs font-normal">
                                <div class="flex flex-col items-center justify-center h-full leading-tight">
                                    <div class="font-semibold">Minutos</div>
                                    <div>(*)</div>
                                </div>
                            </th>
                        </tr>
                </thead>

                <tbody class="bg-white">
                @forelse ($registros as $index => $r)
                <tr class="hover:bg-gray-100"
                    data-dni="{{ $r->dni }}"
                    data-nombres="{{ $r->nombres }}"
                    data-cargo="{{ $r->cargo }}"
                    data-condicion="{{ $r->condicion }}"
                    data-jornada="{{ $r->jornada }}">
                    
                    <td class="border px-2 py-1">{{ $index + 1 }}</td>
                    <td class="border px-2 py-1 cursor-pointer text-blue-500" onclick="abrirModalInasistencia('{{ $r->dni }}', '{{ $r->nombres }}')">{{ $r->dni }}</td>
                    <td class="border px-2 py-1 text-left">{{ $r->nombres }}</td>
                    <td class="border px-2 py-1">{{ $r->cargo }}</td>
                    <td class="border px-2 py-1">{{ $r->condicion }}</td>
                    <td class="border px-2 py-1">{{ $r->jornada }}</td>
                    <td class="border px-2 py-1 text-center" data-tipo="inasistencias_dias">{{ $r->inasistencias_dias ?? '' }}</td>
                    <td class="border px-2 py-1 text-center" data-tipo="tardanzas_horas">{{ $r->tardanzas_horas ?? '' }}</td>
                    <td class="border px-2 py-1 text-center" data-tipo="tardanzas_minutos">{{ $r->tardanzas_minutos ?? '' }}</td>
                    <td class="border px-2 py-1 text-center" data-tipo="permisos_sg_horas">{{ $r->permisos_sg_horas ?? '' }}</td>
                    <td class="border px-2 py-1 text-center" data-tipo="permisos_sg_minutos">{{ $r->permisos_sg_minutos ?? '' }}</td>
                    <td class="border px-2 py-1 text-center" data-tipo="huelga_paro_dias">{{ $r->huelga_paro_dias ?? '' }}</td>
                    <td class="border px-2 py-1" data-tipo="observaciones">{{ e($r->observaciones ?? '') }}</td>

                    <td class="hidden">
                        <input type="hidden" name="detalleInasistencia[{{ $r->dni }}]" class="detalle-inasistencia-json" data-dni="{{ $r->dni }}">
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center py-2">No hay registros disponibles.</td>
                </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>


    <!-- Firma y botón de exportación -->
    <div class="mt-10 text-sm text-right">
        <p>Lugar y Fecha: {{ $fechaHoy }}</p>
    </div>

    <div class="flex items-start gap-4">

    
    {{-- Botón ingresar oficio + vista previa --}}
    <div class="flex flex-col items-center">
        <button id="btnOficio" onclick="openModal2()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Ingresar número de oficio
        </button>
        <p id="previewOficio" class="mt-2 font-bold text-blue-800"></p>
    </div>
    {{-- Firma: Botón y vista previa --}}

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
    <form id="exportarForm" method="POST" action="{{ route('inasistencia.exportar.pdf', ['nivel' => $nivelSeleccionado]) }}" target="_blank">
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
{{-- CSRF para JS --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- Campo oculto --}}
<input type="hidden" id="oficio_guardado">

    {{-- Modal para subir la firma --}}
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

    <!-- Modal para Anexo 04 -->

    <div id="modalInasistencia" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg p-0 relative flex flex-col max-h-[90vh]">

            <!-- Cabecera fija -->
            <div class="p-4 border-b bg-white sticky top-0 z-10 shadow-sm">
            <h2 class="text-base font-semibold">Registrar inasistencia para:</h2>
            <div class="text-blue-600 font-bold text-sm" id="nombreDocente">—</div>
            <input type="hidden" id="dniSeleccionado" name="dniSeleccionado">
            </div>

            <!-- Contenido scrollable -->
            <div id="diasInasistenciaContainer" class="px-6 py-4 overflow-y-auto flex-1 space-y-4">
            <!-- Aquí se añaden los días dinámicamente -->
            </div>

            <!-- Botón Añadir -->
            <div class="px-6 pb-4 border-t bg-white sticky bottom-[70px] z-10">
            <button type="button" id="addDiaBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                + Añadir Consolidado 
            </button>
            </div>

            <!-- Footer fijo -->
            <div class="flex justify-end gap-2 p-4 border-t bg-white sticky bottom-0 z-10">
            <button type="button" id="cerrarModalBtn" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
            <button type="button" id="guardarInasistenciaBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
            </div>

        </div>
    </div>


<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
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
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('modalInasistencia');
  const addBtn = document.getElementById('addDiaBtn');
  const container = document.getElementById('diasInasistenciaContainer');
  const cerrarBtn = document.getElementById('cerrarModalBtn');
  const guardarBtn = document.getElementById('guardarInasistenciaBtn');

  const nombreSpan = document.getElementById('nombreDocente');
  const dniInput = document.getElementById('dniSeleccionado');

 window.abrirModalInasistencia = function(dni, nombre, datos = []) {
  dniInput.value = dni;
  nombreSpan.textContent = nombre;

  // Destruir flatpickr de todos los inputs dentro del container antes de limpiar
  container.querySelectorAll('.fecha-dia').forEach(input => {
    if (input._flatpickr) {
      input._flatpickr.destroy();
    }
  });

  container.innerHTML = ''; // limpiar entradas anteriores

  // Por cada entrada, agregar un bloque con las fechas y tipo que corresponden
  datos.forEach(({fecha, tipo, horas = 0, minutos = 0}) => {
    const newEntry = document.createElement('div');
    // Ajustar template para que acepte fechas (un array o string)
    newEntry.innerHTML = template(fecha, tipo, horas, minutos);
    container.appendChild(newEntry);

    // Inicializar flatpickr para este input con las fechas correctas y modo adecuado
    const fechaInput = newEntry.querySelector('.fecha-dia');
    const tipoSelect = newEntry.querySelector('.tipo-dia');

    // Inicializa flatpickr con fechas para esta entrada
    if (tipo === 'inasistencia' || tipo === 'huelga') {
      flatpickr(fechaInput, {
        mode: "multiple",
        dateFormat: "Y-m-d",
        defaultDate: fecha // solo las fechas para este bloque
      });
    } else {
      flatpickr(fechaInput, {
        mode: "single",
        dateFormat: "Y-m-d",
        defaultDate: (fecha && fecha.length > 0) ? fecha[0] : null
      });
    }

    // Mostrar o esconder horas/minutos según tipo
    const detalle = newEntry.querySelector('.detalle-dia');
    if (tipo === 'tardanza' || tipo === 'permiso_sg') {
      detalle.classList.remove('hidden');
      detalle.querySelector('.horas-dia').value = horas;
      detalle.querySelector('.minutos-dia').value = minutos;
    } else {
      detalle.classList.add('hidden');
    }
  });

  modal.classList.remove('hidden');
};



  cerrarBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  const template = (fechas = [], tipo = '', horas = 0, minutos = 0) => {
  const idFecha = `fecha-${Date.now()}`;
  return `
  <div class="dia-entry border p-4 rounded bg-gray-50 relative">
    <button type="button" class="remove-dia-entry absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm">✕</button>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm items-end">
      <div class="fecha-wrapper">
        <label>Fecha:</label>
        <input type="date" name="fecha[]" class="fecha-dia w-full border rounded p-2" placeholder="Seleccione fecha(s)" required>

      </div>

      <div>
        <label>Tipo de inasistencia:</label>
        <select name="tipo[]" class="tipo-dia w-full border rounded p-2" required>
          <option value="">-- Seleccione --</option>
          <option value="inasistencia" ${tipo === 'inasistencia' ? 'selected' : ''}>Inasistencia</option>
          <option value="tardanza" ${tipo === 'tardanza' ? 'selected' : ''}>Tardanza</option>
          <option value="permiso_sg" ${tipo === 'permiso_sg' ? 'selected' : ''}>Permiso SG</option>
          <option value="huelga" ${tipo === 'huelga' ? 'selected' : ''}>Huelga/Paro</option>
        </select>
      </div>

      <div class="detalle-dia flex gap-2 ${tipo === 'tardanza' || tipo === 'permiso_sg' ? '' : 'hidden'}">
        <input type="number" name="horas[]" class="horas-dia w-1/2 border rounded p-2" placeholder="Horas" min="0" value="${horas}">
        <input type="number" name="minutos[]" class="minutos-dia w-1/2 border rounded p-2" placeholder="Minutos" min="0" max="59" value="${minutos}">
      </div>
    </div>
  </div>
  `;
};


 addBtn.addEventListener('click', () => {
  const newEntry = document.createElement('div');
  newEntry.innerHTML = template();
  container.appendChild(newEntry);

  const tipoSelect = newEntry.querySelector('.tipo-dia');
  const fechaInput = newEntry.querySelector('.fecha-dia');

  // Inicialmente flatpickr sin multiDate hasta que se seleccione tipo
  flatpickr(fechaInput, {
    dateFormat: "Y-m-d"
  });

  tipoSelect.addEventListener('change', () => {
    const tipo = tipoSelect.value;

    // Destruir instancia previa y volver a crear con opciones correctas
    if (tipo === 'inasistencia' || tipo === 'huelga') {
      flatpickr(fechaInput, {
        mode: "multiple",
        dateFormat: "Y-m-d"
      });
    } else {
      flatpickr(fechaInput, {
        mode: "single",
        dateFormat: "Y-m-d"
      });
    }
  });
});


  container.addEventListener('change', function (e) {
  if (e.target.classList.contains('tipo-dia')) {
    const tipo = e.target.value;
    const entry = e.target.closest('.dia-entry');
    const detalle = entry.querySelector('.detalle-dia');
    const inputFecha = entry.querySelector('.fecha-dia');

    // Mostrar horas/minutos solo para tardanza o permiso SG
    if (tipo === 'tardanza' || tipo === 'permiso_sg') {
      detalle.classList.remove('hidden');

      // Reinicializa input a modo single
      flatpickr(inputFecha, {
        mode: 'single',
        dateFormat: 'Y-m-d'
      });

    } else {
      detalle.classList.add('hidden');
      detalle.querySelector('.horas-dia').value = '';
      detalle.querySelector('.minutos-dia').value = '';

      // Reinicializa a modo multiple si es inasistencia o huelga
      if (tipo === 'inasistencia' || tipo === 'huelga') {
        flatpickr(inputFecha, {
          mode: 'multiple',
          dateFormat: 'Y-m-d'
        });
      } else {
        // Si vuelve a seleccionar vacío, modo normal
        flatpickr(inputFecha, {
          mode: 'single',
          dateFormat: 'Y-m-d'
        });
      }
    }
  }
});

    // Eliminar entrada de inasistencia (delegado al container)
container.addEventListener('click', function (e) {
  if (e.target.classList.contains('remove-dia-entry')) {
    const entry = e.target.closest('.dia-entry');
    if (entry) {
      entry.remove();
    }
  }
});


guardarBtn.addEventListener('click', () => {
  const dni = dniInput.value;
  const nombre = nombreSpan.textContent;
  const entradas = container.querySelectorAll('.dia-entry');

  let totalInasistencias = 0;
  let totalTardanzasHoras = 0;
  let totalTardanzasMinutos = 0;
  let totalPermisosHoras = 0;
  let totalPermisosMinutos = 0;
  let totalHuelgas = 0;

  const detalle = {
    inasistencia: [],
    tardanza: [],
    permiso_sg: [],
    huelga: [],
  };

  entradas.forEach(entry => {
    const tipo = entry.querySelector('.tipo-dia').value;
    const horas = parseInt(entry.querySelector('.horas-dia')?.value || 0);
    const minutos = parseInt(entry.querySelector('.minutos-dia')?.value || 0);
    const fecha = entry.querySelector('.fecha-dia').value;

    if (!fecha || !tipo) return;

    if (tipo === 'inasistencia') {
  const fechas = fecha.split(',').map(f => f.trim()).filter(f => f);
  totalInasistencias += fechas.length;
  detalle.inasistencia.push(...fechas);
}
    else if (tipo === 'tardanza') {
      totalTardanzasHoras += horas;
      totalTardanzasMinutos += minutos;
      detalle.tardanza.push({ fecha, horas, minutos });
    }
    else if (tipo === 'permiso_sg') {
      totalPermisosHoras += horas;
      totalPermisosMinutos += minutos;
      detalle.permiso_sg.push({ fecha, horas, minutos });
    }
    else if (tipo === 'huelga') {
  const fechas = fecha.split(',').map(f => f.trim()).filter(f => f);
  totalHuelgas += fechas.length;
  detalle.huelga.push(...fechas);
}
  });

  // Normalizar minutos
  if (totalTardanzasMinutos >= 60) {
    totalTardanzasHoras += Math.floor(totalTardanzasMinutos / 60);
    totalTardanzasMinutos %= 60;
  }
  if (totalPermisosMinutos >= 60) {
    totalPermisosHoras += Math.floor(totalPermisosMinutos / 60);
    totalPermisosMinutos %= 60;
  }

  // Actualizar totales en la tabla
  const fila = document.querySelector(`tr[data-dni="${dni}"]`);
  if (fila) {
    fila.children[6].textContent = totalInasistencias || '';
    fila.children[7].textContent = totalTardanzasHoras || '';
    fila.children[8].textContent = totalTardanzasMinutos || '';
    fila.children[9].textContent = totalPermisosHoras || '';
    fila.children[10].textContent = totalPermisosMinutos || '';
    fila.children[11].textContent = totalHuelgas || '';
  }

  const inputDetalle = document.querySelector(`input.detalle-inasistencia-json[data-dni="${dni}"]`);
  if (inputDetalle) {
    inputDetalle.value = JSON.stringify(detalle);
  }
  // 🔍 Mostrar en consola el detalle completo por si quieres revisar
    console.log('Detalle generado para DNI:', dni, detalle);
  modal.classList.add('hidden');
});



});


document.getElementById('guardarTodoInasistencia').addEventListener('click', async () => {
  const filas = document.querySelectorAll('tbody tr[data-dni]');
  const data = [];

  // Solo un modal con id
  const modal = document.getElementById('modalInasistencia');
  const dniModal = modal ? modal.querySelector('#dniSeleccionado').value : null;

  filas.forEach(tr => {
    const dni = tr.dataset.dni;
    const nombres = tr.dataset.nombres;
    const cargo = tr.dataset.cargo;
    const condicion = tr.dataset.condicion;
    const jornada = tr.dataset.jornada;

    const persona = { dni, nombres, cargo, condicion, jornada };

    const inasistencias = {
      inasistencia: [],
      tardanza: [],
      permiso_sg: [],
      huelga: [],
      inasistencia_total: 0,
      huelga_total: 0,
      tardanza_total: { horas: 0, minutos: 0 },
      permiso_sg_total: { horas: 0, minutos: 0 },
      detalle: {
        inasistencia: [],
        tardanza: [],
        permiso_sg: [],
        huelga: []
      }
    };

    let horasTardanza = 0;
    let minutosTardanza = 0;
    let horasPermiso = 0;
    let minutosPermiso = 0;
    let obs = '';

    tr.querySelectorAll('[data-tipo]').forEach(td => {
      const tipo = td.dataset.tipo;
      const valor = td.textContent.trim();

      if (tipo === 'inasistencias_dias') {
        inasistencias.inasistencia_total = parseInt(valor) || 0;
      } else if (tipo === 'tardanzas_horas') {
        horasTardanza = parseInt(valor) || 0;
      } else if (tipo === 'tardanzas_minutos') {
        minutosTardanza = parseInt(valor) || 0;
      } else if (tipo === 'permisos_sg_horas') {
        horasPermiso = parseInt(valor) || 0;
      } else if (tipo === 'permisos_sg_minutos') {
        minutosPermiso = parseInt(valor) || 0;
      } else if (tipo === 'huelga_paro_dias') {
        inasistencias.huelga_total = parseInt(valor) || 0;
      } else if (tipo === 'observaciones') {
        obs = valor;
      }
    });

    inasistencias.tardanza_total = { horas: horasTardanza, minutos: minutosTardanza };
    inasistencias.permiso_sg_total = { horas: horasPermiso, minutos: minutosPermiso };

    // Solo si el modal está abierto y corresponde a este DNI, extraer detalle
    if (modal && dniModal === dni) {
      const detalles = modal.querySelectorAll('.dia-entry');

      inasistencias.detalle = {
        inasistencia: [],
        tardanza: [],
        permiso_sg: [],
        huelga: []
      };

      detalles.forEach(entry => {
        const fechaInput = entry.querySelector('.fecha-dia');
        const tipoInput = entry.querySelector('.tipo-dia');
        const horasInput = entry.querySelector('.horas-dia');
        const minutosInput = entry.querySelector('.minutos-dia');

        const tipo = tipoInput.value;
        if (!tipo) return;

        const fecha = fechaInput.value.trim();
        if (!fecha) return;

        if (tipo === 'inasistencia' || tipo === 'huelga') {
          const fechas = fecha.split(',').map(f => f.trim()).filter(f => f);
          inasistencias[tipo].push(...fechas);
          inasistencias.detalle[tipo].push(...fechas);
        } else if (tipo === 'tardanza' || tipo === 'permiso_sg') {
          const obj = {
            fecha,
            horas: parseInt(horasInput.value) || 0,
            minutos: parseInt(minutosInput.value) || 0
          };
          inasistencias[tipo].push(obj);
          inasistencias.detalle[tipo].push(obj);
        }
      });

      const obsInput = modal.querySelector('.observaciones-textarea');
      if (obsInput) obs = obsInput.value.trim();
    }

    console.log(`Detalle FINAL enviado para DNI ${dni}:`, inasistencias.detalle);

    data.push({
      persona,
      inasistencia: inasistencias,
      observacion: obs,
      detalle: inasistencias.detalle
    });
  });

  console.log('Datos que se enviarán al backend:', {
    mes: {{ $mes }},
    anio: {{ $anio }},
    codlocal: '{{ $codlocal }}',
    nivel: '{{ $nivel }}',
    personas: data
  });
  try {
    const response = await fetch('{{ route("anexo04.storeMasivo") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        mes: {{ $mes }},
        anio: {{ $anio }},
        codlocal: '{{ $codlocal }}',
        nivel: '{{ $nivel }}',
        personas: data
      })
    });

    if (response.ok) {
      alert('Guardado correctamente.');
      location.reload();
    } else {
      const error = await response.json();
      console.error('Error en respuesta:', error);
      alert('Error al guardar: ' + error.message);
    }
  } catch (error) {
    console.error('Error en fetch:', error);
    alert('Error en la comunicación: ' + error.message);
  }
});




</script>


@endsection
