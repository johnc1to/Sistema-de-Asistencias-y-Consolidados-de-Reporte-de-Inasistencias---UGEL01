@extends('layout_especialista/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak]{
        display:none !important;
    }
</style>
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


        <div class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6">
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
</script>



@endsection
