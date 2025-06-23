<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Oficio</title>
    <style>
        body {
            font-family: 'Arial', serif;
            font-size: 13px;
            line-height: 1.6;
            margin: 2.5cm;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-justify { text-align: justify; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .indent { text-indent: 2em; }
        .mb-4 { margin-bottom: 1em; }
        .mb-6 { margin-bottom: 1.5em; }
        .mt-6 { margin-top: 1.5em; }
        .mt-16 { margin-top: 4em; }
        .-mt-1 { margin-top: -0.25em; }
        .underline { text-decoration: underline; }
        .subrayado {
    text-decoration: underline;
  }
    </style>
</head>
<body>
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
            <!-- Logo -->
           <td style="width: 100px; vertical-align: top; padding-right: 1em;">
                @if(!empty($logo))
                    <img src="{{ asset($logo) }}" alt="Logo IE" style="height: 70px;">
                @else
                    <div style="font-size: 12px; color: #888;">No se encontró logo</div>
                @endif
            </td>

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

    <!-- Número de Oficio alineado a la izquierda -->
    <div class="font-bold uppercase mb-6 subrayado">
        OFICIO N.º {{$numeroOficio}}-{{ $anio }}-DIE-{{ $resultado}}-UGEL 01-SJM
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
                ENVÍO DEL ANEXO 03 FORMATO 01: REPORTE DE ASISTENCIA DETALLADO CORRESPONDIENTE AL MES DE 
                <span class="uppercase">{{ \Carbon\Carbon::create($anio, $mes, 1)->locale('es')->isoFormat('MMMM') }}</span> {{ $anio }} 
                DE LA IE “{{ strtoupper($institucion) }}”
            </span>
        </div>
    </div>

    <!-- Cuerpo del texto -->
    <div class="text-justify indent mb-4">
        Tengo el agrado de dirigirme a usted expresándole mi cordial saludo, asimismo, para informarle sobre el envío a su despacho del <span class="font-bold uppercase">ANEXO 03 FORMATO 01: REPORTE DE ASISTENCIA DETALLADO CORRESPONDIENTE AL <span class="subrayado">MES DE {{ \Carbon\Carbon::create($anio, $mes, 1)->locale('es')->isoFormat('MMMM [] YYYY') }}</span> DE LA IE “{{ strtoupper($institucion) }}”.</span>
    </div>

    <div class="text-justify indent mb-6">
        Es propicia la oportunidad para reafirmarle las muestras de mi especial consideración y estima personal.
    </div>

    <!-- Firma -->
    <div class="text-center mt-16">
        <p><strong>Atentamente,</strong></p>
        <br><br><br>

        @if(isset($firmaGuardada))
            <img src="{{ asset('storage/firmasdirector/' . $firmaGuardada) }}" alt="Firma Guardada" style="height: 100px;">
        @elseif(isset($firmaBase64))
            <img src="{{ $firmaBase64 }}" alt="Firma Temporal" style="height: 100px;">
        @else
            <p>No hay firma cargada.</p>
        @endif
    </div>


    @php
        // Iniciales de apellidos
        $apellidoP = substr(session('siic01.apellipat'), 0, 1);
        $apellidoM = substr(session('siic01.apellimat'), 0, 1);

        // Obtener hasta dos iniciales de los nombres
        $nombrePartes = explode(' ', trim(session('siic01.nombres')));
        $inicialesNombres = collect($nombrePartes)
            ->take(2)
            ->map(fn($nombre) => substr($nombre, 0, 1))
            ->implode('');

            // Construcción final
            $abreviatura = strtoupper($apellidoP . $apellidoM . $inicialesNombres);
    @endphp
       


    <div style="position: absolute; bottom: 30px; left: 50px; font-weight: bold;">
        
        <p>{{ $abreviatura }}/DIR. I.E.</p>
    </div>


</body>
</html>
