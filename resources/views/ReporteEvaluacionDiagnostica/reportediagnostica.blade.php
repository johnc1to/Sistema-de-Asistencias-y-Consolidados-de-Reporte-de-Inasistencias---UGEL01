@extends('layout_especialista/cuerpo')

@section('html')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    [x-cloak]{
        display:none !important;
    }
</style>
    <div class="grid grid-cols-2 md:grid-cols-7 gap-4 mb-4 text-sm">
        <div class="bg-blue-100 p-3 rounded shadow text-center">
            <strong><div class="font-bold">TOTAL CSV IMPORTADOS</div></strong>
            <div class="text-xl text-blue-700 font-bold">{{ $registros->sum('importo_csv') }}</div>
        </div>
        <div class="bg-green-100 p-3 rounded shadow text-center">
            <strong><div class="font-bold">TOTAL GRADOS</div></strong>
            <div class="text-xl text-green-700 font-bold">{{ $registros->sum('cantidad_grados') }}</div>
        </div>
        <div class="bg-yellow-100 p-3 rounded shadow text-center">
            <strong><div class="font-bold">TOTAL SECCIONES</div></strong>
            <div class="text-xl text-yellow-700 font-bold">{{ $registros->sum('cantidad_secciones') }}</div>
        </div>
        <div class="bg-red-100 p-3 rounded shadow text-center">
            <strong><div class="font-bold">TOTAL REGISTRADOS</div></strong>
            <div class="text-xl text-red-700 font-bold">{{ $registros->sum('cantidad_registrados') }}</div>
        </div>
        <div class="bg-violet-100 p-3 rounded shadow text-center">
            <strong><div class="font-bold">TOTAL REGISTRADOS ESPERADOS</div></strong>
            <div class="text-xl text-violet-700 font-bold">{{ number_format($totalEsperado) }}</div>
        </div>
        <div class="bg-gray-100 p-3 rounded shadow text-center">
            <div class="font-bold">SERVICIOS EDUCATIVOS QUE NO IMPORTARON CSV</div>
            <div class="text-xl text-gray-700 font-bold">
                {{ $registros->where('importo_csv', 0)->count() }}
            </div>
        </div>
        <div class="bg-purple-100 p-3 rounded shadow text-center">
            <div class="font-bold">CANTIDAD DE SERVICIOS EDUCATIVOS</div>
            <div class="text-xl text-purple-700 font-bold">
                {{ $registros->count() }}
            </div>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap gap-4 items-end mb-6">
        {{-- Red --}}
        <div class="flex flex-col">
            <label for="red" class="text-sm font-medium text-gray-700">Red:</label>
            <select name="red" id="red" class="select2 w-64" data-placeholder="Seleccione red">
                <option value="">Seleccione red</option>
                @foreach ($redes as $r)
                    <option value="{{ $r }}" 
                        {{ (request()->has('red') && request('red') !== '' && (string)request('red') === (string)$r) ? 'selected' : '' }}>
                        {{ $r }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Institución --}}
        <div class="flex flex-col">
            <label for="institucion" class="text-sm font-medium text-gray-700">Institución:</label>
            <select name="institucion" id="institucion" class="select2 w-64">
                <option value="">Todas</option>
                @foreach ($instituciones as $i)
                    <option value="{{ $i }}" {{ request('institucion') == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nivel --}}
        <div class="flex flex-col">
            <label for="nivel" class="text-sm font-medium text-gray-700">Nivel:</label>
            <select name="nivel" id="nivel" class="select2 w-64">
                <option value="">Todos</option>
                @foreach ($niveles as $n)
                    <option value="{{ $n }}" {{ request('nivel') == $n ? 'selected' : '' }}>
                        {{ $n }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Distrito --}}
        <div class="flex flex-col">
            <label for="distrito" class="text-sm font-medium text-gray-700">Distrito:</label>
            <select name="distrito" id="distrito" class="select2 w-64">
                <option value="">Todos</option>
                @foreach ($distritos as $d)
                    <option value="{{ $d }}" {{ request('distrito') == $d ? 'selected' : '' }}>
                        {{ $d }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Botones --}}
        <div class="flex gap-2 mt-4">
            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Filtrar</button>
            <a href="{{ route('reporte.unificado') }}" class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600">Limpiar Filtros</a>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="flex justify-center items-center">
            <div class="w-[580px] h-[580px]">
                <canvas id="graficoIIEEcsv" width="180" height="180"></canvas>
            </div>
        </div>
        <div class="flex justify-center items-center">
            <div class="w-[580px] h-[580px]">
                <canvas id="graficoComparativo" width="180" height="180"></canvas>
            </div>
        </div>
        <div class="flex justify-center items-center">
            <div class="w-[580px] h-[580px]">
                <canvas id="graficoAvanceGrado" width="180" height="180"></canvas>
            </div>
        </div>
    </div>

    <div class="w-full max-w-full mx-auto bg-white rounded-xl shadow-md p-6">
        <h1 class="text-2xl font-bold text-center mb-4 uppercase">Reporte de Avance: Carga de Estudiantes y Evaluación Diagnóstica por Redes, IIEE, Grados y Secciones</h1>
            <div class="overflow-x-auto mt-4">
                <table id="tablaDatos" class="min-w-full border rounded-xl text-sm">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs text-center">
                        <tr>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">N°</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Red</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Institución</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Nivel</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Distrito</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Director</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Celular</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Correo Institucional</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Cantidad CSV</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Cantidad Grados</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Cantidad Secciones</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Cantidad Registrados</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Cantidad Estudiantes</th>
                            <th class="px-2 py-1 text-center border whitespace-nowrap">Porcentaje de Avance</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($registros as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-2 py-1">{{ $loop->iteration }}</td>
                                <td class="border px-2 py-1">{{ $item->red }}</td>
                                <td class="border px-2 py-1">{{ $item->institucion }}</td>
                                <td class="border px-2 py-1">{{ $item->nivel }}</td>
                                <td class="border px-2 py-1">{{ $item->distrito }}</td>
                                <td class="border px-2 py-1">{{ $item->director }}</td>
                                <td class="border px-2 py-1">{{ $item->telefono }}</td>
                                <td class="border px-2 py-1">{{ $item->correo_inst }}</td>
                                <td class="border px-2 py-1">{{ $item->importo_csv }}</td>
                                <td class="border px-2 py-1">{{ $item->cantidad_grados}}</td>
                                <td class="border px-2 py-1">{{ $item->cantidad_secciones}}</td>
                                <td class="border px-2 py-1">{{ $item->cantidad_registrados}}</td>
                                <td class="border px-2 py-1">{{ $item->total_alumnos }}</td>
                                <td class="border px-2 py-1">
                                    @php
                                        $avance = $item->porcentaje_avance_alumnos ?? 0;
                                        $texto = number_format($avance, 2) . '%';

                                        // Determinar color según porcentaje
                                        if ($avance == 0) {
                                            $bgBar = 'bg-gray-400'; // barra vacía gris oscuro
                                            $texto = 'No hay avance';
                                            $textColor = 'text-black';
                                            $width = '0%';
                                        } elseif ($avance <= 33) {
                                            $bgBar = 'bg-red-600';
                                            $textColor = 'text-black';
                                            $width = $avance . '%';
                                        } elseif ($avance <= 66) {
                                            $bgBar = 'bg-yellow-400';
                                            $textColor = 'text-black';
                                            $width = $avance . '%';
                                        } else {
                                            $bgBar = 'bg-green-600';
                                            $textColor = 'text-white';
                                            $width = $avance . '%';
                                        }
                                    @endphp

                                    <div class="w-full relative h-6 bg-gray-100 rounded-full overflow-hidden shadow-sm border border-gray-300">
                                        <div class="{{ $bgBar }} h-full transition-all duration-500" style="width: {{ $width }}"></div>
                                        <div class="absolute inset-0 flex items-center justify-center text-xs font-bold {{ $textColor }}">
                                            {{ $texto }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center text-gray-500 py-4">No se encontraron resultados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            
                <div class="flex flex-col md:flex-row justify-between items-center mt-2 mb-4 gap-2">
                    {{-- Selector de cantidad por página --}}
                    <form method="GET" class="flex items-center gap-2">
                        <label for="per_page" class="text-sm text-gray-700">Mostrar:</label>
                        <select name="per_page" id="per_page" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                            @foreach ([10, 20, 30, 40, 50] as $cantidad)
                                <option value="{{ $cantidad }}" {{ request('per_page') == $cantidad ? 'selected' : '' }}>
                                    {{ $cantidad }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-sm text-gray-700">registros</span>

                        {{-- Mantener filtros activos en el GET --}}
                        @foreach(request()->except('per_page', 'page') as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                    </form>
                </div>
                <div class="mt-4">
                    {{ $registros->links() }}
                </div>
            </div>
    </div>

    <!-- Librerias -->
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

        <!-- jQuery (necesario para DataTables) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="https://cdn.tailwindcss.com"></script>

        <!-- CHART JS -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

        <!-- Inicialización -->
<script>
    // Inicializar Select2
    $('.select2').select2({
        allowClear: true,
        placeholder: function () {
            return $(this).data('placeholder');
        },
        width: 'resolve',
        matcher: function (params, data) {
            if ($.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;
            const term = params.term.toLowerCase();
            const text = data.text.toLowerCase();
            return text.includes(term) ? data : null;
        }
        }).on('select2:clear', function (e) {
            $(this).val('').trigger('change');
    });
    
    // TABLA 
    $('#tablaDatos').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
    });

    // Obtener datos desde Blade
    const totalGrados = {{ $registros->sum('cantidad_grados') }};
    const gradosConRegistro = {{ $registros->where('cantidad_registrados', '>', 0)->sum('cantidad_grados') }};
    const totalSecciones = {{ $registros->sum('cantidad_secciones') }};
    const seccionesConRegistro = {{ $registros->where('cantidad_registrados', '>', 0)->sum('cantidad_secciones') }};
    // Gráfico barra (IIEE con o sin CSV)
    const totalIIEE = {{ $registros->count() }};
    const sinCSV = {{ $registros->where('importo_csv', 0)->count() }};
    const conCSV = totalIIEE - sinCSV;
    //Gráfico barra por estudiante
    const avancePorGrado = @json($avancePorGrado);
    const labels = avancePorGrado.map(d => d.grado);
    const esperados = avancePorGrado.map(d => d.esperado);
    const evaluados = avancePorGrado.map(d => d.evaluado);
    const seccionesPorGrado = @json($detalleSecciones);
    
    const ctxComparativo = document.getElementById('graficoComparativo');
    new Chart(ctxComparativo, {
        type: 'bar',
        data: {
            labels: ['GRADOS', 'SECCIONES'],
            datasets: [
                {
                    label: 'ESPERADO',
                    data: [totalGrados, totalSecciones],
                    backgroundColor: 'rgba(217, 219, 221, 0.84)'
                },
                {
                    label: 'REGISTRADO',
                    data: [gradosConRegistro, seccionesConRegistro],
                    backgroundColor: ' #FA9669',
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: function (value, ctx) {
                            const esperado = ctx.chart.data.datasets[0].data[ctx.dataIndex];
                            const porcentaje = esperado > 0 ? ((value / esperado) * 100).toFixed(1) : 0;
                            return `${porcentaje}%`;
                        },
                        color: '#000',
                        font: {
                            weight: 'bold',
                            size: 20
                        }
                    }
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'GRADOS Y SECCIONES: ESPERADO VS REGISTRADO',
                    font: {
                        size: 16
                    }
                },
                datalabels: {
                    display: function (ctx) {
                        return ctx.dataset.label === 'REGISTRADO';
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    const ctxIIEE = document.getElementById('graficoIIEEcsv');
    new Chart(ctxIIEE, {
        type: 'bar',
        data: {
            labels: ['CON CSV', 'SIN CSV'],
            datasets: [{
                label: 'IIEE',
                data: [conCSV, sinCSV],
                backgroundColor: [' #194BE0', ' #E7EAF4'],
                borderRadius: 5,
                borderWidth: 1,
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    offset: 10,
                    formatter: function (value, ctx) {
                        const total = conCSV + sinCSV;
                        const porcentaje = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${porcentaje}%`;
                    },
                    color: '#000',
                    font: {
                        weight: 'bold',
                        size: 20
                    }
                }
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 20,
                        padding: 20,
                        font: {
                            size: 12
                        },
                        color: '#333',
                        generateLabels: function(chart) {
                            return [
                                { text: 'CON CSV', fillStyle: '#194BE0' },
                                { text: 'SIN CSV', fillStyle: '#E7EAF4' }
                            ];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const total = conCSV + sinCSV;
                            const porcentaje = ((ctx.raw / total) * 100).toFixed(2);
                            return `${ctx.label}: ${ctx.raw} SERVICIO EDUCATIVO (${porcentaje}%)`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'SERVICIOS EDUCATIVOS QUE IMPORTARON CSV',
                    font: { size: 16 },
                    padding: { top: 40, bottom: 10 }
                },
                datalabels: {
                    display: true
                }
            },
            scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 },
                suggestedMax: conCSV + sinCSV 
            }
        }
        },
        plugins: [ChartDataLabels]
    });

    new Chart(document.getElementById('graficoAvanceGrado'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Esperado',
                data: esperados,
                backgroundColor: ' #CBD5E0' // gris claro
            },
            {
                label: 'Evaluado',
                data: evaluados,
                backgroundColor: ' #3B82F6' // azul
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Estudiantes esperados vs evaluados por grado',
                font: {
                    size: 18
                },
                padding: {
                    top: 10,
                    bottom: 20
                }
            },
            tooltip: {
                callbacks: {
                    afterLabel: function (context) {
                        const grado = context.label;
                        const detalles = seccionesPorGrado[grado] || [];
                        return detalles; 
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});


</script>


@endsection
