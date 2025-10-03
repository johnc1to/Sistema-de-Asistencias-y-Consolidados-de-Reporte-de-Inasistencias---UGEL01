@extends('layout_director/cuerpo')
@section('html')

<!-- dependencias -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700&display=swap" rel="stylesheet">

<!-- jQuery + Select2 (asegúrate de cargarlos antes de usar $ y select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Intro.js y SweetAlert (si las usas) -->
<link rel="stylesheet" href="https://unpkg.com/intro.js@4.2.2/minified/introjs.min.css">
<script src="https://unpkg.com/intro.js@4.2.2/minified/intro.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Estilos para que la "hoja" se vea como A4 y entre en el contenedor */
.preview-wrapper { overflow:auto; display:flex; justify-content:center; padding:12px; }
.doc {
  width: 793px;            /* ~210mm */
  min-height: 1122px;     /* ~297mm */
  background: #fff;
  padding: 48px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  font-family: 'Noto Serif', serif;
  font-size: 14px;
  color: #111827;
}

/* Paneles estilo "tabla" */
.panel-table { border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; background: #fff; }
.panel-table .panel-header { background: #f9fafb; padding: 0.75rem 1rem; font-weight: 600; border-bottom: 1px solid #e5e7eb; }
.panel-table .panel-body { padding: 1rem; }

/* Firma */
.firma-preview { max-width: 220px; max-height: 120px; display:block; margin-top:12px; object-fit:contain; border:1px solid #e5e7eb; padding:6px; background:#fff; }
.label { font-weight:600; color:#111827; }

/* Responsivo: si la pantalla es chica, apilar */
@media (max-width: 768px) {
  .doc { width: 100%; min-height: 600px; padding: 24px; }
}

.doc {
  width: 793px;        /* 210mm */
  min-height: 1122px;  /* 297mm */
  background: white;
  padding: 48px;
  font-family: 'Noto Serif', serif;
  font-size: 14px;
  line-height: 1.6;
}
.firma-preview {
  max-width: 220px;
  max-height: 120px;
  object-fit: contain;
  margin-left: auto;
}
</style>

<div class="max-w-7xl mx-auto p-6 flex gap-6">
  <!-- IZQUIERDA: FORMULARIO -->
  <div class="w-[380px] flex-shrink-0">
    <div class="panel-table">
      <div class="panel-header">Registrar Oficio - Abandono de Cargo</div>
      <div class="panel-body">
        <form id="formOficio" enctype="multipart/form-data" onsubmit="return false;">
          <!-- Nivel -->
          <div class="mb-4">
            <label class="label block mb-1">Nivel</label>
            <select id="nivel" name="nivel" class="w-full select2">
              <option value="">Seleccione nivel</option>
              @foreach($niveles as $n)
                <option value="{{ $n }}" {{ $n == $nivelSeleccionado ? 'selected' : '' }}>
                  {{ $n }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Docente -->
          <div class="mb-4">
            <label class="label block mb-1">Docente</label>
            <select id="docente" name="docente" class="w-full select2">
              <option value="">Seleccione docente</option>
              @foreach($personal->filter(fn($p) => strtolower(trim($p->nivel)) === strtolower(trim($nivelSeleccionado))) as $p)
                  <option value="{{ $p->dni }}">
                    {{ $p->nombres }} - {{ $p->cargo }}
                  </option>
              @endforeach
            </select>
          </div>

          <!-- Fecha -->
          <div class="mb-4">
            <label class="label block mb-1">Fecha de registro</label>
            <input type="date" id="fecha" name="fecha" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
          </div>

          <!-- Observaciones -->
          <div class="mb-4">
            <label class="label block mb-1">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="4" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400"></textarea>
          </div>

          <!-- Firma -->
          <div class="mb-4">
            <label class="label block mb-1">Firma del director (imagen)</label>
            <input type="file" accept="image/*" id="firma" name="firma" class="w-full">
            <small class="text-sm text-gray-500">Se mostrará en la vista previa.</small>
          </div>

          <!-- Botones -->
          <div class="flex gap-2">
            <button id="guardar" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Guardar</button>
            <button id="limpiar" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300">Limpiar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- DERECHA: VISTA PREVIA -->
  <div class="flex-1 overflow-y-auto">
    <div class="panel-table">
      <div class="panel-header">Vista previa del Oficio</div>
        <div class="panel-body preview-wrapper">
          <div class="doc shadow-lg">
            <div class="max-w-full overflow-hidden">
              @include('Registropagos.oficio_abandono_preview')
            </div>
        </div>
      </div>
  </div>
</div>

<script>
  $(function () {
    // Inicializa Select2
    $('.select2').select2({ placeholder: 'Seleccione...' });

    const personal = @json($personalJson);

    // Si cambias nivel: filtra docentes en frontend
    $('#nivel').on('change', function () {
      const nivelText = $('#nivel option:selected').text() || '---';
      $('#prevNivel').text(nivelText);

      const nivel = $(this).val();
      $('#docente').empty().append('<option value="">Seleccione docente</option>');

      if (nivel) {
        const filtrados = personal.filter(
          p => p.nivel.trim().toLowerCase() === nivel.trim().toLowerCase()
        );
        filtrados.forEach(d => {
          $('#docente').append(
            `<option value="${d.dni}">${d.nombres} - ${d.cargo}</option>`
          );
        });
      }

      $('#docente').val(null).trigger('change');
      $('#prevDocente').text('---');
    });

    // Docente -> vista previa
    $('#docente').on('change', function () {
      const text = $('#docente option:selected').text() || '---';
      $('#prevDocente').text(text);
    });

    // Fecha -> vista previa (formato dd/mm/yyyy)
    $('#fecha').on('input change', function () {
      const val = $(this).val();
      if (!val) {
        $('#prevFecha').text('---');
        return;
      }
      const d = new Date(val);
      const dd = String(d.getDate()).padStart(2, '0');
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      const yyyy = d.getFullYear();
      $('#prevFecha').text(`${dd}/${mm}/${yyyy}`);
    });

    // Observaciones -> vista previa
    $('#observaciones').on('input', function () {
      const v = $(this).val().trim();
      $('#prevObs').text(v ? v : '---');
    });

    // Firma -> mostrar imagen en la vista previa
    $('#firma').on('change', function () {
      const file = this.files && this.files[0];
      if (!file) {
        $('#firmaPreview').hide().attr('src', '');
        return;
      }
      const reader = new FileReader();
      reader.onload = function (e) {
        $('#firmaPreview').attr('src', e.target.result).show();
      };
      reader.readAsDataURL(file);
    });

    // Limpiar (solo UI)
    $('#limpiar').on('click', function () {
      $('#formOficio')[0].reset();
      $('#nivel').val(null).trigger('change');
      $('#docente')
        .empty()
        .append('<option value="">Seleccione docente</option>')
        .val(null)
        .trigger('change');
      $('#prevNivel,#prevFecha,#prevDocente').text('---');
      $('#prevObs').text('---');
      $('#firmaPreview').hide().attr('src', '');
    });

    // Guardar
    $('#guardar').on('click', function () {
      const fd = new FormData($('#formOficio')[0]);
      fd.append('tipo', 'abandono');
      $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      });
      $.ajax({
        url: '/oficios/guardar',
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function (res) {
          Swal.fire({ icon: 'success', text: 'Oficio guardado correctamente' });
        },
        error: function (xhr) {
          Swal.fire({ icon: 'error', text: 'Error al guardar. Revisa la consola.' });
          console.error(xhr.responseText);
        },
      });
    });
  });

</script>

@endsection
