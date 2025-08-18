@extends('layout_director/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<!-- Intro.js CSS y JS -->
<link rel="stylesheet" href="https://unpkg.com/intro.js@4.2.2/minified/introjs.min.css">
<script src="https://unpkg.com/intro.js@4.2.2/minified/intro.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
        #modalInasistencia {
            z-index: 9999; 
        }

</style>
<meta name="guardar-firma-url" content="{{ route('guardar.firma.director') }}">

    <div class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6">
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">ANEXO 04 - {{ mb_strtoupper(\Carbon\Carbon::now()->subMonth()->translatedFormat('F '), 'UTF-8') }}</h1>
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">Formato 02: REPORTE CONSOLIDADO DE INASISTENCIAS, TARDANZAS Y PERMISOS SIN GOCE DE REMUNERACIONES</h1>
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
                PERIODO: {{ mb_strtoupper(\Carbon\Carbon::now()->subMonth()->translatedFormat('F Y'), 'UTF-8') }}
            </p>

            <p class="text-sm font-medium">Turno: {{ $d_cod_tur }}</p>
        </div>

        <button id="guardarTodoInasistencia" type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" data-step="17">
            Guardar Reporte de Consolidado Masiva
        </button>
        <div id="loader" class="hidden flex items-center justify-center space-x-2 text-blue-600 mt-4">
                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>Guardando asistencia...</span>
        </div>

        <form method="GET" action="{{ url('/reporte_anexo04') }}" class="flex flex-wrap items-center gap-4">
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
            <div class="min-w-[1200px] w-full">
                <table class="min-w-[1200px] w-full text-sm table-auto border-collapse">
                    @php
                        use Carbon\Carbon;
                        $mes = $mes ?? 3;
                        $anio = $anio ?? 2025;
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
                          <td class="border px-2 py-1 cursor-pointer text-blue-500 dni-tour dni-tour-clickable" onclick="prepararYabrirModal('{{ $r->dni }}', '{{ $r->nombres }}')">{{ $r->dni }}</td>
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
                              <input type="hidden" name="detalleInasistencia[{{ $r->dni }}]" class="detalle-inasistencia-json" data-dni="{{ $r->dni }}" value='@json($r->detalle_inasistencia_json)'>
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
    <div class="flex flex-col items-center" data-step="7">
        <button id="btnOficio" onclick="openModal2()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Ingresar número de oficio
        </button>
        <p id="previewOficio" class="mt-2 font-bold text-blue-800"></p>
    </div>
    {{-- Firma: Botón y vista previa --}}

    <div class="flex flex-col items-center" data-step="11">
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
<form id="exportarFormPreliminar" method="POST"
      action="{{ route('inasistenciapreliminar.exportar.pdf', [
          'mes' => $mes,
          'anio' => $anio,
          'codlocal' => $codlocal,
          'nivel' => $nivelSeleccionado
      ]) }}"
      target="_blank">
    @csrf
    <input type="hidden" name="detalle_inasistencias" id="campoDetalleInasistencias">
    <!-- <input type="hidden" name="firma_base64" id="campoFirmaBase64"> -->
    <div class="flex flex-col items-center" data-step="16">
        <button type="submit" onclick="antesDeExportarPreliminar()"
            class="bg-sky-500 text-white px-4 py-2 rounded hover:bg-sky-600">
            Exportar en PDF PRELIMINAR
        </button>
    </div>
</form>

<form id="exportarFormOficial" method="POST"
      action="{{ route('inasistencia.exportar.pdf', ['nivel' => $nivelSeleccionado]) }}"
      target="_blank">
    @csrf
    <input type="hidden" name="numero_oficio" id="campoNumeroOficio">
    <input type="hidden" name="firma_base64" id="campoFirmaBase64">
    <div class="flex flex-col items-center" data-step="16">
        <button type="submit" onclick="antesDeExportar()"
            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
            Exportar en PDF
        </button>
    </div>
</form>

    <!-- Botón ingresar expediente + vista previa -->
        <div class="flex flex-col items-center">
            <button id="btnExpediente" onclick="openExpedienteModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Ingresar número de expediente
            </button>
            <p id="previewExpediente" class="mt-2 font-bold text-indigo-800"></p>
        </div>
          <input type="hidden" name="numero_expediente" id="campoNumeroExpediente">
        </div>
    {{-- CSRF para JS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Campo oculto --}}
    <input type="hidden" id="oficio_guardado" value="{{ $numeroOficio ?? '' }}">

    {{-- Modal para subir la firma --}}
    <div id="modalFirma" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden" data-step="12">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4 text-center">Subir firma</h2>

            <div class="mb-3 p-2 bg-yellow-100 text-yellow-800 text-sm rounded border border-yellow-300">
                Si no marcas la opción de guardar firma, esta se usará solo de forma temporal en el presente documento y deberás volver a subirla cada vez antes de generar el reporte.
            </div>

            <input type="file" id="firmaInput" accept="image/*"
                class="w-full border border-gray-300 rounded px-3 py-2 mb-4" data-step="13">

            <div class="mb-4" data-step="14">
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

            <div class="flex justify-end space-x-2" data-step="15">
                <button onclick="closeFirmaModal()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded">Cancelar</button>
                <button onclick="guardarFirma()" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal Oficio-->
    <div id="modalOficio" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden" data-step="8">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-xl font-semibold mb-4 text-center">Número de Oficio</h2>

            <label for="numeroOficio" class="block text-sm font-medium text-gray-700 mb-1">
                Ingrese el número:
            </label>
            <input type="number" id="numeroOficio"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    min="0" data-step="9">

            <div class="flex justify-end space-x-2" data-step="10">
                <button onclick="closeModal()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded">Cancelar</button>
                <button onclick="guardarOficio()" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para ingresar número de expediente -->
    <div id="expedienteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Ingresar número de expediente</h2>
            <input type="text" id="inputExpediente" placeholder="Ej. 123456"
                class="w-full border rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300">
            <div class="flex justify-end gap-2 mt-4">
                <button onclick="cerrarExpedienteModal()"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                <button onclick="guardarExpediente()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Anexo 04 -->
    <div id="modalInasistencia" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg p-0 relative flex flex-col max-h-[90vh]">

            <!-- Cabecera fija mejorada -->
            <div class="p-4 border-b bg-white sticky top-0 z-10 shadow-sm">
              <h2 class="text-base font-semibold">Registrar descuento en mérito al <strong>RSG-326-2017-MINEDU</strong> para:</h2>
              <div class="text-blue-600 font-bold text-sm" id="nombreDocente">—</div>
              <input type="hidden" id="dniSeleccionado" name="dniSeleccionado">

              <!-- NUEVO: Estado de cumplimiento dinámico -->
              <div id="estadoBloques" class="mt-2 text-sm text-gray-700 bg-yellow-100 border border-yellow-400 rounded p-2">
                Estado de cumplimiento: <span id="resumenBloques">Correcto</span>
              </div>
            </div>

            <!-- Contenido scrollable -->
            <div id="diasInasistenciaContainer" class="px-6 py-4 overflow-y-auto flex-1 space-y-4">
            <!-- Aquí se añaden los días dinámicamente -->
            </div>

            <!-- Botón Añadir -->
            <div class="px-6 pb-4 border-t bg-white sticky bottom-[70px] z-10" data-step="4">
            <button type="button" id="addDiaBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                + Añadir Consolidado 
            </button>
            </div>

            <!-- Footer fijo -->
            <div class="flex justify-end gap-2 p-4 border-t bg-white sticky bottom-0 z-10" data-step="6">
            <button type="button" id="cerrarModalBtn" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
            <button type="button" id="guardarInasistenciaBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
            </div>

        </div>
    </div>


<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

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

    function openExpedienteModal() {
      document.getElementById('expedienteModal').classList.remove('hidden');
      document.getElementById('inputExpediente').value = '';
      document.getElementById('inputExpediente').focus();
    }

    function cerrarExpedienteModal() {
        document.getElementById('expedienteModal').classList.add('hidden');
    }

    function guardarExpediente() {
        const numero = document.getElementById('inputExpediente').value.trim();
        if (numero !== '') {
            document.getElementById('campoNumeroExpediente').value = numero;
            document.getElementById('previewExpediente').innerText = 'Expediente MPD2025-EXP-' +numero;
            cerrarExpedienteModal();
        } else {
            alert('Por favor, ingrese un número de expediente válido.');
        }
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
  
    function antesDeExportarPreliminar() {
      // Recorrer todos los inputs ocultos con detalle de inasistencia
      const datos = [];
      document.getElementById('campoFirmaBase64').value = firmaBase64 ?? '';
      document.querySelectorAll('input.detalle-inasistencia-json').forEach(input => {
          const dni = input.dataset.dni;
          let detalle = {};
          try {
              detalle = JSON.parse(input.value || '{}');
          } catch (e) {
              console.warn("Error parseando JSON de " + dni, e);
          }

          const fila = document.querySelector(`tr[data-dni="${dni}"]`);
          if (fila) {
              datos.push({
                  dni: dni,
                  nombres: fila.dataset.nombres,
                  cargo: fila.dataset.cargo,
                  condicion: fila.dataset.condicion,
                  jornada: fila.dataset.jornada,
                  detalle: detalle
              });
          }
      });

      // Guardar en el campo oculto como string JSON
      document.getElementById('campoDetalleInasistencias').value = JSON.stringify(datos);
  }

    function getAllSelectedFechas(actualInput) {
      const fechas = [];
      document.querySelectorAll('.fecha-dia').forEach(input => {
        if (input !== actualInput && input._flatpickr) {
          input._flatpickr.selectedDates.forEach(date => {
            fechas.push(date.toISOString().split('T')[0]);
          });
        }
      });
      return fechas;
    }

    function restarUnMes(fechaStr) {
      const fecha = new Date(fechaStr);
      fecha.setMonth(fecha.getMonth() - 1);
      return fecha;
    }
const listaFeriados = ["2025-07-28","2025-07-29","2025-08-06","2025-08-30","2025-10-08" ,"2025-11-01","2025-12-08","2025-12-09","2025-12-25","2025-12-26"];

document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('modalInasistencia');
  const addBtn = document.getElementById('addDiaBtn');
  const container = document.getElementById('diasInasistenciaContainer');
  const cerrarBtn = document.getElementById('cerrarModalBtn');
  const guardarBtn = document.getElementById('guardarInasistenciaBtn');

  const nombreSpan = document.getElementById('nombreDocente');
  const dniInput = document.getElementById('dniSeleccionado');

    const bloques = [
    { tipo: "GESTIÓN", inicio: "2025-03-03", fin: "2025-03-14" },
    { tipo: "LECTIVA",  inicio: "2025-03-17", fin: "2025-05-16" },
    { tipo: "GESTIÓN", inicio: "2025-05-19", fin: "2025-05-23" },
    { tipo: "LECTIVA",  inicio: "2025-05-26", fin: "2025-07-25" },
    { tipo: "GESTIÓN", inicio: "2025-07-28", fin: "2025-08-08" },
    { tipo: "LECTIVA",  inicio: "2025-08-11", fin: "2025-10-10" },
    { tipo: "GESTIÓN", inicio: "2025-10-13", fin: "2025-10-17" },
    { tipo: "LECTIVA",  inicio: "2025-10-20", fin: "2025-12-19" },
    { tipo: "GESTIÓN", inicio: "2025-12-22", fin: "2025-12-31" }
  ];
  function detectarBloque(fechaStr) {
    const fecha = new Date(fechaStr);
    for (let bloque of bloques) {
      const inicio = new Date(bloque.inicio);
      const fin = new Date(bloque.fin);
      if (fecha >= inicio && fecha <= fin) {
        return bloque.tipo;
      }
    }
    return null; 
  }

  let conteoBloques = { "GESTIÓN": 0, "LECTIVA": 0 };
  function esDiaNoLaborable(fechaStr) {
    const fecha = new Date(fechaStr + 'T00:00:00'); 
    const diaSemana = fecha.getDay(); 

    const esFinDeSemana = (diaSemana === 0 || diaSemana === 6);
    const esFeriado = listaFeriados.includes(fechaStr);

    return esFinDeSemana || esFeriado;
  }

  function actualizarResumenBloques(fechas, contenedor) {
    let conteoBloques = { "GESTIÓN": 0, "LECTIVA": 0 };

    fechas.forEach(f => {
      if (!esDiaNoLaborable(f)) {
        const tipo = detectarBloque(f);
        if (tipo && conteoBloques[tipo] !== undefined) {
          conteoBloques[tipo]++;
        }
      }
    });

    const totalLectiva = conteoBloques["LECTIVA"];
    const totalGestion = conteoBloques["GESTIÓN"];

    const estado = [];

    if (totalLectiva > 0 && totalLectiva < 5) estado.push("Incumple semana lectiva");
    if (totalGestion > 0 && totalGestion < 5) estado.push("Incumple semana de gestión");

    let resumen = `Inasistencias válidas — Gestión: ${totalGestion} día(s), Lectiva: ${totalLectiva} día(s).`;
    
    const estadoBloques = document.getElementById("estadoBloques");
    const resumenBloques = document.getElementById("resumenBloques");

    if (totalLectiva === 0 && totalGestion === 0) {
      resumen = "✅ Cumple con ambas semanas. Sin inasistencias válidas.";
      estadoBloques.className = "mt-2 text-sm text-green-700 bg-green-100 border border-green-400 rounded p-2";
    } else if (estado.length > 0) {
      resumen += ` ⚠️ ${estado.join(" e ")}`;
      estadoBloques.className = "mt-2 text-sm text-yellow-700 bg-yellow-100 border border-red-400 rounded p-2";
    } else {
      resumen += ` ⚠️ Tiene inasistencias, que incumple semanas completas.`;
      estadoBloques.className = "mt-2 text-sm text-red-800 bg-red-100 border border-yellow-400 rounded p-2";
    }

    resumenBloques.innerText = resumen;
  }

  function getFechasSeleccionadas() {
    const entradas = container.querySelectorAll('.dia-entry');
    const todasFechas = [];

    entradas.forEach(entry => {
      const tipo = entry.querySelector('.tipo-dia').value;
      const fechaInput = entry.querySelector('.fecha-dia');
      const fechas = fechaInput.value.split(',').map(f => f.trim()).filter(f => f);
      todasFechas.push(...fechas);
    });

    
    return [...new Set(todasFechas)];
  }

  window.abrirModalInasistencia = function(dni, nombre, datos = []) {
  dniInput.value = dni;
  nombreSpan.textContent = nombre;

  // Resetear estado
  document.getElementById('resumenBloques').textContent = 'Correcto';
  container.querySelectorAll('.fecha-dia').forEach(input => {
    if (input._flatpickr) {
      input._flatpickr.destroy();
    }
  });
  container.innerHTML = '';

  // Cargar datos del docente
  datos.forEach(({ fecha, tipo, horas = 0, minutos = 0 }) => {
    const newEntry = document.createElement('div');
    newEntry.innerHTML = template(fecha, tipo, horas, minutos);
    container.appendChild(newEntry);

    const fechaInput = newEntry.querySelector('.fecha-dia');

    flatpickr(fechaInput, {
      mode: (tipo === 'inasistencia' || tipo === 'huelga') ? "multiple" : "single",
      dateFormat: "Y-m-d",
      locale: 'es',
      defaultDate: (tipo === 'inasistencia' || tipo === 'huelga') ? fecha : (fecha && fecha.length > 0 ? fecha[0] : null),
      disable: getAllSelectedFechas( fechaInput),
      onOpen: function (_, __, instance) {
        instance.set('disable', getAllSelectedFechas( fechaInput));
      },
      onChange: function () {
        actualizarResumenBloques(getFechasSeleccionadas(), document.getElementById('modalInasistencia'));
      }
    });
  });
  
  actualizarResumenBloques(getFechasSeleccionadas(), document.getElementById('modalInasistencia'));

  modal.classList.remove('hidden');
  };

  cerrarBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  const template = (fechas = [], tipo = '', horas = 0, minutos = 0) => {
  const idFecha = `fecha-${Date.now()}`;
  return `
    <div class="dia-entry border p-4 rounded bg-gray-50 relative" data-step="5">
      <button type="button" class="remove-dia-entry absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm">✕</button>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm items-end">
        <div>
          <label>Tipo de descuento:</label>
          <select name="tipo[]" class="tipo-dia w-full border rounded p-2" required>
            <option value="">-- Seleccione --</option>
            <option value="inasistencia" ${tipo === 'inasistencia' ? 'selected' : ''}>Inasistencias</option>
            <option value="tardanza" ${tipo === 'tardanza' ? 'selected' : ''}>Tardanzas</option>
            <option value="permiso_sg" ${tipo === 'permiso_sg' ? 'selected' : ''}>Permisos SG</option>
            <option value="huelga" ${tipo === 'huelga' ? 'selected' : ''}>Huelga/Paro</option>
          </select>
        </div>
        <div class="fecha-wrapper">
          <label>Fecha:</label>
          <input type="date" name="fecha[]" class="fecha-dia w-full border rounded p-2" placeholder="Seleccione fecha(s)" required>
        </div>
        <div class="detalle-dia flex gap-4 ${tipo === 'tardanza' || tipo === 'permiso_sg' ? '' : 'hidden'}">
          <div class="flex flex-col w-1/2">
              <label class="text-sm font-medium mb-1">Horas:</label>
              <input type="number" name="horas[]" class="horas-dia border rounded p-2" placeholder="Horas" min="0" value="${horas}">
          </div>
          <div class="flex flex-col w-1/2">
              <label class="text-sm font-medium mb-1">Minutos:</label>
              <input type="number" name="minutos[]" class="minutos-dia border rounded p-2" placeholder="Minutos" min="0" max="59" value="${minutos}">
          </div>
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

      // Inicializar con modo simple inicialmente
      flatpickr(fechaInput, {
        dateFormat: "Y-m-d",
        locale: 'es',
        disable: getAllSelectedFechas(fechaInput),
        onOpen: function(_, __, instance) {
          instance.set('disable', getAllSelectedFechas(fechaInput));
        },
        onReady: function(selectedDates, _, instance) {
          const baseFecha = selectedDates.length > 0 ? selectedDates[0] : new Date();
          const mostrarFecha = restarUnMes(baseFecha);
          instance.jumpToDate(mostrarFecha);
        },
        onChange: function () {
          const fechas = getFechasSeleccionadas();
          const modalContainer = document.getElementById('modalInasistencia');

          actualizarResumenBloques(fechas, modalContainer);
        }

      });

    tipoSelect.addEventListener('change', () => {
      const tipo = tipoSelect.value;

      flatpickr(fechaInput, {
        mode: (tipo === 'inasistencia' || tipo === 'huelga') ? "multiple" : "single",
        dateFormat: "Y-m-d",
        locale: 'es',
        disable: getAllSelectedFechas(fechaInput),
        onOpen: function(_, __, instance) {
          instance.set('disable', getAllSelectedFechas(fechaInput));
        },
        onReady: function(selectedDates, _, instance) {
          const baseFecha = selectedDates.length > 0 ? selectedDates[0] : new Date();
          const mostrarFecha = restarUnMes(baseFecha);
          instance.jumpToDate(mostrarFecha);
        },
        onChange: function () {
          const fechas = getFechasSeleccionadas();
          const modalContainer = document.getElementById('modalInasistencia');

          actualizarResumenBloques(fechas, modalContainer);
        }
      });
    });
  });

  container.addEventListener('change', function (e) {
      if (e.target.classList.contains('tipo-dia')) {
        const tipo = e.target.value;
        const entry = e.target.closest('.dia-entry');
        const detalle = entry.querySelector('.detalle-dia');
        const inputFecha = entry.querySelector('.fecha-dia');

        if (tipo === 'tardanza' || tipo === 'permiso_sg') {
          detalle.classList.remove('hidden');

          flatpickr(inputFecha, {
            mode: 'single',
            dateFormat: 'Y-m-d',
            locale: 'es',
            disable: getAllSelectedFechas(inputFecha),
            onOpen: function(_, __, instance) {
              instance.set('disable', getAllSelectedFechas(inputFecha));
            },
            onReady: function(selectedDates, _, instance) {
              const baseFecha = selectedDates.length > 0 ? selectedDates[0] : new Date();
              const mostrarFecha = restarUnMes(baseFecha);
              instance.jumpToDate(mostrarFecha);
            },
            onChange: function () {
              const fechas = getFechasSeleccionadas();
              const modalContainer = document.getElementById('modalInasistencia');

              actualizarResumenBloques(fechas, modalContainer);
            }
          });

        } else {
          detalle.classList.add('hidden');
          detalle.querySelector('.horas-dia').value = '';
          detalle.querySelector('.minutos-dia').value = '';

          flatpickr(inputFecha, {
            mode: (tipo === 'inasistencia' || tipo === 'huelga') ? 'multiple' : 'single',
            dateFormat: 'Y-m-d',
            locale: 'es',
            disable: getAllSelectedFechas(inputFecha),
            onOpen: function(_, __, instance) {
              instance.set('disable', getAllSelectedFechas(inputFecha));
            },
            onReady: function(selectedDates, _, instance) {
              const baseFecha = selectedDates.length > 0 ? selectedDates[0] : new Date();
              const mostrarFecha = restarUnMes(baseFecha);
              instance.jumpToDate(mostrarFecha);
            },
            onChange: function () {
              const fechas = getFechasSeleccionadas();
              const modalContainer = document.getElementById('modalInasistencia');
              actualizarResumenBloques(fechas, modalContainer);
            }
          });
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
      modal.classList.add('hidden');
    });
});

window.prepararYabrirModal = function(dni, nombre) {
    const inputDetalle = document.querySelector(`input.detalle-inasistencia-json[data-dni="${dni}"]`);
    let datos = [];

    if (inputDetalle && inputDetalle.value) {
        try {
            const detalle = JSON.parse(inputDetalle.value) || {};

            if (detalle.inasistencia) {
                detalle.inasistencia.forEach(f => datos.push({ fecha: f, tipo: 'inasistencia' }));
            }
            if (detalle.huelga) {
                detalle.huelga.forEach(f => datos.push({ fecha: f, tipo: 'huelga' }));
            }
            if (detalle.tardanza) {
                detalle.tardanza.forEach(({ fecha, horas, minutos }) => {
                    if (fecha) {
                        datos.push({ fecha: [fecha], tipo: 'tardanza', horas, minutos });
                    }
                });
            }
            if (detalle.permiso_sg) {
                detalle.permiso_sg.forEach(({ fecha, horas, minutos }) => {
                    if (fecha) {
                        datos.push({ fecha: [fecha], tipo: 'permiso_sg', horas, minutos });
                    }
                });
            }
        } catch (e) {
            console.warn('No se pudo parsear JSON de inasistencia:', e);
        }
    }

    abrirModalInasistencia(dni, nombre, datos);

    try {
        let conteoBloques = { "GESTIÓN": 0, "LECTIVA": 0 };

        datos.forEach(d => {
            if (!esDiaNoLaborable(d.fecha)) {
                const tipo = detectarBloque(d.fecha);
                if (tipo && conteoBloques[tipo] !== undefined) {
                    conteoBloques[tipo]++;
                }
            }
        });

        const totalLectiva = conteoBloques["LECTIVA"];
        const totalGestion = conteoBloques["GESTIÓN"];
        const estado = [];

        if (totalLectiva > 0 && totalLectiva < 5) estado.push("Incumple semana lectiva");
        if (totalGestion > 0 && totalGestion < 5) estado.push("Incumple semana de gestión");

        let resumen = `Inasistencias válidas — Gestión: ${totalGestion} día(s), Lectiva: ${totalLectiva} día(s).`;

        const estadoBloques = document.getElementById("estadoBloques");
        const resumenBloques = document.getElementById("resumenBloques");

        if (estadoBloques && resumenBloques) {
            if (totalLectiva === 0 && totalGestion === 0) {
                resumen = "✅ Cumple con las semanas.";
                estadoBloques.className = "mt-2 text-sm text-green-700 bg-green-100 border border-green-400 rounded p-2";
            } else if (estado.length > 0) {
                resumen += ` ⚠️ ${estado.join(" e ")}`;
                estadoBloques.className = "mt-2 text-sm text-yellow-700 bg-yellow-100 border border-red-400 rounded p-2";
            } else {
                resumen += ` ⚠️ Tiene inasistencias, que incumple semanas completas.`;
                estadoBloques.className = "mt-2 text-sm text-red-800 bg-red-100 border border-yellow-400 rounded p-2";
            }

            resumenBloques.innerText = resumen;
        }
    } catch (err) {
        console.warn("Error al calcular resumen de bloques:", err);
    }
};

document.getElementById('guardarTodoInasistencia').addEventListener('click', async function () {
  const boton = this;
  const loader = document.getElementById('loader');

  boton.disabled = true;
  boton.classList.add('opacity-50', 'cursor-not-allowed');
  loader.classList.remove('hidden');

  try {
    const filas = document.querySelectorAll('tbody tr[data-dni]');
    const data = [];

    // Nivel dinámico
    const nivelSeleccionado = document.getElementById('nivel')?.value || '';

    filas.forEach(tr => {
      const dni = tr.dataset.dni;
      const nombres = tr.dataset.nombres;
      const cargo = tr.dataset.cargo;
      const condicion = tr.dataset.condicion;
      const jornada = tr.dataset.jornada;

      const persona = {
        dni: dni,
        nombres: nombres,
        cargo: cargo,
        condicion: condicion,
        jornada: jornada
      };


      const inasistencias = {
        inasistencia: [],
        tardanza: [],
        permiso_sg: [],
        huelga: [],
        inasistencia_total: 0,
        huelga_total: 0,
        tardanza_total: { horas: 0, minutos: 0 },
        permiso_sg_total: { horas: 0, minutos: 0 },
        detalle: { inasistencia: [], tardanza: [], permiso_sg: [], huelga: [] }
      };

      let horasTardanza = 0;
      let minutosTardanza = 0;
      let horasPermiso = 0;
      let minutosPermiso = 0;
      let obs = '';

      tr.querySelectorAll('[data-tipo]').forEach(td => {
        const tipo = td.dataset.tipo;
        const valor = td.textContent.trim();

        if (tipo === 'inasistencias_dias') inasistencias.inasistencia_total = parseInt(valor) || 0;
        else if (tipo === 'tardanzas_horas') horasTardanza = parseInt(valor) || 0;
        else if (tipo === 'tardanzas_minutos') minutosTardanza = parseInt(valor) || 0;
        else if (tipo === 'permisos_sg_horas') horasPermiso = parseInt(valor) || 0;
        else if (tipo === 'permisos_sg_minutos') minutosPermiso = parseInt(valor) || 0;
        else if (tipo === 'huelga_paro_dias') inasistencias.huelga_total = parseInt(valor) || 0;
        else if (tipo === 'observaciones') obs = valor;
      });

      inasistencias.tardanza_total = { horas: horasTardanza, minutos: minutosTardanza };
      inasistencias.permiso_sg_total = { horas: horasPermiso, minutos: minutosPermiso };

      const inputDetalle = document.querySelector(`input.detalle-inasistencia-json[data-dni="${dni}"]`);
      if (inputDetalle && inputDetalle.value) {
        try {
          const detalleGuardado = JSON.parse(inputDetalle.value);
          inasistencias.detalle = detalleGuardado;
          inasistencias.inasistencia = detalleGuardado.inasistencia || [];
          inasistencias.tardanza = detalleGuardado.tardanza || [];
          inasistencias.permiso_sg = detalleGuardado.permiso_sg || [];
          inasistencias.huelga = detalleGuardado.huelga || [];
        } catch (e) {
          console.warn('No se pudo parsear detalle guardado:', e);
        }
      }

      data.push({
        persona,
        inasistencia: inasistencias,
        observacion: obs,
        detalle: inasistencias.detalle
      });
    });

    const numeroOficio = document.getElementById('oficio_guardado')?.value.trim() || null;
    const numeroExpediente = document.getElementById('campoNumeroExpediente')?.value.trim() || null;

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
        nivel: nivelSeleccionado,
        personas: data,
        numero_oficio: numeroOficio,
        numero_expediente: numeroExpediente
      })
    });

    if (response.ok) {
      alert('Guardado correctamente.');
    } else {
      const error = await response.json();
      console.error('Error en respuesta:', error);
      alert('Error al guardar: ' + error.message);
    }

  } catch (error) {
    console.error('Error en fetch:', error);
    alert('Error en la comunicación: ' + error.message);
  } finally {
    boton.disabled = false;
    boton.classList.remove('opacity-50', 'cursor-not-allowed');
    loader.classList.add('hidden');
  }
});





</script>


@endsection