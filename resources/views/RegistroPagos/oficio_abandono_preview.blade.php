{{-- resources/views/Registropagos/oficio_abandono_preview.blade.php --}}

<!-- Fecha alineada a la derecha -->
@php
    $partes = explode(' ', $institucion);
    $primerElemento = $partes[0];

    if (is_numeric($primerElemento)) {
        // Si comienza con un número
        $codigo = $primerElemento;
        $iniciales = collect(array_slice($partes, 1))
                        ->map(fn($palabra) => Str::substr(Str::upper($palabra), 0, 1))
                        ->implode('');
        $resultado = $codigo . ' ' . $iniciales;
    } else {
        // Si no comienza con número
        $iniciales = collect($partes)
                        ->map(fn($palabra) => Str::substr(Str::upper($palabra), 0, 1))
                        ->implode('');
        $resultado = $iniciales;
    }
@endphp

<table style="width: 100%;">
    <tr>
    <!-- Logo a la izquierda -->
    <td style="width: 80px; vertical-align: top; border: none;">
        @if(!empty($logo))
                <img src="{{ asset($logo) }}" alt="Logo IE" style="height: 70px;">
            @else
                <div style="font-size: 12px; color: #888;">No se encontró logo</div>
        @endif

        <!-- Columna del texto -->
        <td style="text-align: center;">
            <div style="font-weight: bold; margin-bottom: 0.4em;">
                “{{ $nombreAnio }}”
            </div>
            <div>
                <p style="font-weight: bold;">INSTITUCIÓN EDUCATIVA</p>
                <p style="font-weight: bold; text-transform: uppercase; font-size: 15px;">“{{ $institucion }}”</p>
                <div style="font-size: 11px;">
                    <p>
                        {{ ucwords(strtolower($direccion_ie)) }}
                        @if (!empty($localidad))
                            - {{ ucwords(strtolower($localidad)) }}
                        @endif
                        - {{ $distrito }}
                    </p>

                    @php
                        $orden = ['inicial - jardín' => 1, 'primaria' => 2, 'secundaria' => 3];
                        $codmodularesOrdenados = collect($codmodulares)->sortBy(function($item) use ($orden) {
                            return $orden[strtolower($item->nivel)] ?? 999;
                        });
                    @endphp

                    @if($codmodularesOrdenados->isNotEmpty())
                        <p>
                            @foreach($codmodularesOrdenados as $cm)
                                @php
                                    $abreviatura = '';
                                    switch (strtolower($cm->nivel)) {
                                        case 'inicial - jardín': $abreviatura = 'I'; break;
                                        case 'primaria': $abreviatura = 'P'; break;
                                        case 'secundaria': $abreviatura = 'S'; break;
                                    }
                                @endphp
                                <strong>C.M. {{ $abreviatura }}</strong> {{ $cm->codmod }}
                                @if (!$loop->last) / @endif
                            @endforeach
                        </p>
                    @else
                        <p><strong>C.M.</strong> No disponible</p>
                    @endif

                    <p> <strong>C.L. </strong>{{ $codlocal }} - <strong>RD Creación: </strong>{{ $resolucion }}</p>
                    <strong>{{$correo_inst}}</strong>
                </div>
            </div>
        </td>
    </tr>
</table>

<!-- Fecha alineada a la derecha -->
<div class="text-right mt-6 mb-6">
    {{ $distrito }}, {{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
</div>

<!-- Número de Oficio -->
<div class="font-bold uppercase mb-6 subrayado">
    OFICIO N.º ____-{{ date('Y') }}-DIE-{{ $resultado}}-UGEL 01-SJM
</div>

<!-- Destinatario -->
<div class="mb-6">
    Señor<br>
    <span class="font-bold uppercase">
        @if ($directorUgel)
            {{ "Mg. {$directorUgel->nombres} {$directorUgel->apellipat} {$directorUgel->apellimat}" }}
        @else
            NOMBRE DEL DIRECTOR UGEL
        @endif
    </span><br>
    Director de UGEL 01 - SJM<br>
    Presente. -
</div>

<!-- Contenedor flexible para el asunto -->
<div class="mb-6 flex items-start">

    <!-- Etiqueta "Asunto:" con negrita y subrayado -->
    <div class="font-bold underline mr-4 shrink-0" style="min-width: 70px;">
        Asunto:
    </div>

    <!-- Texto justificado, en mayúsculas donde toca -->
    <div class="text-justify flex-1">
        <span class="font-bold">
            ENVÍO DEL REGISTRO DE PAGO: ABANDONO DE CARGO DE LA IE “{{ strtoupper($institucion) }}”
        </span>
    </div>
</div>

<!-- Cuerpo -->
<div class="text-justify indent mb-4">
    Tengo el agrado de dirigirme a usted expresándole mi cordial saludo, asimismo,
    para informarle sobre el presunto <span class="font-bold uppercase">ABANDONO DE CARGO</span>
    de un docente de nuestra IE “{{ strtoupper($institucion) }}” (<span id="prevDocente">DOCENTE</span>, nivel <span id="prevNivel">?</span>),
    hecho que pongo en su conocimiento a fin de que se realicen las acciones correspondientes según normativa vigente.
</div>

<!-- Observaciones -->
<div class="mb-6">
    <span class="font-bold">Observaciones:</span><br>
    <span id="prevObs" class="italic text-gray-700">---</span>
</div>

<!-- Firma -->
<div class="text-center mt-16">
    <p><strong>Atentamente,</strong></p>
    <br><br><br>

    <!-- Imagen de la firma (oculta por defecto) -->
    <img id="firmaPreview"
        class="mx-auto hidden h-24"
        alt="Firma del Director">
</div>
