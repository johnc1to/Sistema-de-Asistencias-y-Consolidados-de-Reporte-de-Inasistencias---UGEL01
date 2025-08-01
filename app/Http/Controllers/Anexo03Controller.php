<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anexo03Asistencia;
use App\Models\Contacto;
use App\Models\ConfiguracionDiasAsistencia; 
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\ReporteAsistenciaExport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use \Mpdf\Mpdf;
use DB;
use PDF;
use App\Models\Anexo03Persona;
use App\Models\Anexo03;
use App\Models\Iiee_a_evaluar_rie;



class Anexo03Controller extends Controller
{

    public function mostrarAsistenciaDetallada(Request $request)
    {
        $director = session('siic01');

        if (!$director || empty($director['conf_permisos'][0]['codlocal'])) {
            return redirect()->back()->with('error', 'No se encontró la sesión del director o el codlocal.');
        }

        $codlocal = $director['conf_permisos'][0]['codlocal'];

        $institucion = Iiee_a_evaluar_rie::select('modalidad', 'institucion')
            ->where('codlocal', $codlocal)
            ->first();

        //Obtener turno 
            $d_cod_tur= DB::table('escale')
                ->select('d_cod_tur')
                ->where('codlocal', $codlocal)
                ->value('tud_cod_turrno');  
        
        //Obtener firma
            $firmaGuardada = DB::connection('siic_anexos')->table('anexo03')
            ->where('id_contacto', $director['id_contacto'])
            ->value('firma');

        //Obtener oficio
            $oficioguardado = DB::connection('siic_anexos')->table('anexo03')
            ->where('id_contacto', $director['id_contacto'])
            ->value('oficio');
        
        //Obtener expediente
            $expedienteguardado = DB::connection('siic_anexos')->table('anexo03')
            ->where('id_contacto', $director['id_contacto'])
            ->value('expediente');
            
        
        //Lista de docentes desde nexus (para control de acceso)
        $personal = DB::table('nexus')
            ->select(
                'nexus.numdocum as dni',
                DB::raw("CONCAT(nexus.apellipat, ' ', nexus.apellimat, ', ', nexus.nombres) as nombres"),
                'nexus.descargo as cargo',
                'nexus.situacion as condicion',
                'nexus.jornlab as jornada',
                'nexus.descniveduc as nivel',
                'nexus.nombreooii as ugel',
                'nexus.codplaza as cod'
            )
            ->where('nexus.codlocal', $codlocal)
            ->whereNotNull('nexus.numdocum')
            ->where('nexus.numdocum', '!=', 'VACANTE')
            ->where('nexus.situacion', '!=', 'VACANTE')
            ->where('nexus.estado', 1)
            ->get();

        // Niveles únicos
        $niveles = $personal->pluck('nivel')->unique()->sort()->values();

        // Nivel seleccionado
        $nivelSeleccionado = $request->get('nivel');
        if (!$nivelSeleccionado || !$niveles->contains($nivelSeleccionado)) {
            $nivelSeleccionado = $niveles->first();
        }

        // Filtra por nivel
        $filtrados = $personal->where('nivel', $nivelSeleccionado)->values();

        $filtrados = $filtrados->sort(function ($a, $b) {
        // Prioridad por jerarquía del cargo
        $prioridadCargo = function ($cargo) {
            $cargo = strtoupper(trim($cargo));
            if (Str::startsWith($cargo, 'DIRECTOR')) return 1;
            if (Str::startsWith($cargo, 'SUB-DIRECTOR')) return 2;
            if (Str::startsWith($cargo, 'JEFE')) return 3;
            if (Str::startsWith($cargo, 'COORDINADOR')) return 4;
            if (Str::startsWith($cargo, 'PROFESOR')) return 5;
            if (Str::startsWith($cargo, 'AUXILIAR')) return 6;
            return 99; // Otros
        };

        // Prioridad por condición laboral
        $prioridadCondicion = function ($condicion) {
            $condicion = strtoupper(trim($condicion));
            if ($condicion === 'NOMBRADO') return 1;
            if ($condicion === 'CONTRATADO') return 2;
            if (in_array($condicion, ['ASIGNADO', 'ASIGNADA'])) return 3;
            return 99;
        };

        $pa = $prioridadCargo($a->cargo);
        $pb = $prioridadCargo($b->cargo);

        if ($pa !== $pb) return $pa <=> $pb;

        $ca = $prioridadCondicion($a->condicion);
        $cb = $prioridadCondicion($b->condicion);

        if ($ca !== $cb) return $ca <=> $cb;

        // Comparar por jornada laboral (de mayor a menor)
        $ja = $a->jornada ?? 0;
        $jb = $b->jornada ?? 0;

        if ($ja !== $jb) return $jb <=> $ja;

        // Finalmente, comparar por nombres
        return strcmp($a->nombres, $b->nombres);
        })->values();


        // Obtén las personas del reporte anexo03 para este codlocal
        $personasReporte = DB::connection('siic_anexos')->table('anexo03')
            ->join('anexo03_persona', 'anexo03.id_anexo03', '=', 'anexo03_persona.id_anexo03')
            ->where('anexo03.codlocal', $codlocal)
            ->select('anexo03_persona.id', 'anexo03_persona.persona_json')
            ->get()
            ->mapWithKeys(function ($item) {
                $persona = json_decode($item->persona_json);

                // Asegúrate que codplaza exista en el JSON
                $dni = $persona->dni ?? null;
                $codplaza = $persona->cod ?? ($persona->cod ?? null); // por si el campo se llama 'cod'

                // Si no hay codplaza, usar solo DNI, pero idealmente siempre debería haber
                $clave = $codplaza ? "{$dni}_{$codplaza}" : $dni;

                return [$clave => $item->id];
                
            });


        // Obtén la asistencia y observación para las personas que están en el reporte
        $asistencias = DB::connection('siic_anexos')->table('anexo03_asistencia')
            ->whereIn('id_persona', $personasReporte->values()->all())
            ->select('id_persona', 'asistencia', 'observacion')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id_persona => [
                    'asistencia' => json_decode($item->asistencia),
                    'observacion' => $item->observacion,
                ]];
            });

        // Finalmente construye un array para usar en la vista que relacione DNI con asistencia y observacion
        $datosAsistenciaPorDni = [];
        foreach ($personasReporte as $clave => $id_persona) {
            $datosAsistenciaPorDni[$clave] = $asistencias[$id_persona] ?? ['asistencia' => [], 'observacion' => null];
        }
       
        return view('reporteAnexo03.formulario03', [
            'registros' => $filtrados,
            'niveles' => $niveles,
            'nivelSeleccionado' => $nivelSeleccionado,
            'modalidad' => $institucion->modalidad ?? '',
            'institucion' => $institucion->institucion ?? '',
            'anio' => now()->year,
            'mes' => now()->month,
            'codlocal' => $codlocal,
            'asistencias' => $datosAsistenciaPorDni,
            'd_cod_tur' => $d_cod_tur,
            'firmaGuardada' => $firmaGuardada,
            'oficio'=>$oficioguardado,
            'expediente'=>$expedienteguardado,
        ]);
    }


    public function guardarReporteMasivo(Request $request)
    {
        DB::beginTransaction();
        try {
            // Insertar cabecera con nivel
            $idAnexo03 = DB::connection('siic_anexos')->table('anexo03')->insertGetId([
                'id_contacto' => $request->id_contacto,
                'codlocal' => $request->codlocal,
                'nivel' => $request->nivel,
                'oficio' => $request->numero_oficio,
                'expediente' => $request->numero_expediente,
                'fecha_creacion' => now(),
            ]);

            foreach ($request->docentes as $docente) {
                // REGISTRA EN LOGS EL DOCENTE ACTUAL
                // \Log::info('Procesando docente:', $docente);

                // VALIDACIÓN BÁSICA
                if (!isset($docente['dni']) || !isset($docente['asistencia'])) {
                    throw new \Exception("Datos incompletos para docente: " . json_encode($docente));
                }

                // INSERTAR EN anexo03_persona
                $idPersona = DB::connection('siic_anexos')->table('anexo03_persona')->insertGetId([
                    'id_anexo03' => $idAnexo03,
                    'persona_json' => json_encode([
                        'dni' => $docente['dni'],
                        'nombres' => $docente['nombres'] ?? '',
                        'cargo' => $docente['cargo'] ?? '',
                        'condicion' => $docente['condicion'] ?? '',
                        'jornada' => $docente['jornada'] ?? '',
                        'cod' => $docente['cod'] ?? '',
                    ]),
                ]);

                // INSERTAR EN anexo03_asistencia
                DB::connection('siic_anexos')->table('anexo03_asistencia')->insert([
                    'id_persona' => $idPersona, // Relacionamos con la tabla anexo03_persona
                    'asistencia' => json_encode($docente['asistencia']),
                    'observacion' => $docente['observacion'] ?? null, // puede ser texto o null
                    'tipo_observacion' => $docente['tipo_observacion'] ?? null, // nuevo campo, puede ser null si no viene
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
           // \Log::error('Error al guardar reporte masivo: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    private function obtenerRegistros($id_contacto)
    {
        // Obtener los registros de contacto que estén asociados al id_contacto
        $contactos = Contacto::where('id_contacto', $id_contacto)->get();

        $registros = [];

        foreach ($contactos as $contacto) {
            // Ahora que tenemos la información del contacto, obtenemos la asistencia relacionada
            // Verificamos si hay algún registro de asistencia asociado a cada contacto

            $asistencia = Anexo03Asistencia::where('id_contacto', $contacto->id_contacto)->get();

            // Por cada contacto, asignamos los datos necesarios (dni, nombres, cargo, jornada, etc.)
            $registros[] = [
                'dni' => $contacto->dni, // Suponiendo que el contacto tiene un campo 'dni'
                'nombres' => $contacto->nombres, // Suponiendo que el contacto tiene un campo 'nombres'
                'cargo' => $contacto->cargo, // Suponiendo que el contacto tiene un campo 'cargo'
                'condicion' => $contacto->condicion, // Suponiendo que el contacto tiene un campo 'condicion'
                'jornada' => $contacto->jornada, // Suponiendo que el contacto tiene un campo 'jornada'
                'asistencia' => $asistencia->pluck('asistencia')->toArray(), // Obtenemos las asistencias relacionadas al contacto
            ];
        }

        return $registros;
    }

    
    public function obtenerPersonalSesion()
    {
        $director = session('siic01');

        if (!$director || empty($director['conf_permisos'][0]['codlocal'])) {
            return response()->json(['error' => 'No se encontró la sesión del director o el codlocal.'], 400);
        }

        $codlocal = $director['conf_permisos'][0]['codlocal'];

        // Obtener datos únicos de la institución
        $institucion = Iiee_a_evaluar_rie::select('modalidad', 'institucion')
            ->where('codlocal', $codlocal)
            ->first();

        // Obtener trabajadores filtrando VACANTE y DNI vacío
        $personal = DB::table('nexus')
            ->select(
                'nexus.numdocum as dni',
                DB::raw("CONCAT(nexus.apellipat, ' ', nexus.apellimat, ', ', nexus.nombres) as nombres"),
                'nexus.descargo as cargo',
                'nexus.situacion as condicion',
                'nexus.jornlab as jornada',
                'nexus.descniveduc as nivel',
                'nexus.nombreooii as ugel'
            )
            ->where('nexus.codlocal', $codlocal)
            ->whereNotNull('nexus.numdocum')
            ->where('nexus.numdocum', '!=', '')
            ->where('nexus.numdocum', '!=', 'VACANTE')
            ->where('nexus.situacion', '!=', 'VACANTE')
            ->where('nexus.estado', 1)
            ->get();

        $totalResultados = $personal->count();

        $datos = $personal->map(function ($item) use ($institucion) {
            return (object) array_merge((array) $item, [
                'modalidad' => $institucion->modalidad ?? null,
                'institucion' => $institucion->institucion ?? null,
            ]);
        });

        return response()->json([
            'total_resultados' => $totalResultados,
            'datos' => $datos
        ]);
    }


    public function exportarAsistenciaPDF(Request $request)
    {
        $director = session('siic01');
        $numeroOficio = $request->input('numero_oficio');
        $firmaBase64 = $request->input('firma_base64');

        if (!$director || empty($director['conf_permisos'][0]['codlocal'])) {
            return redirect()->back()->with('error', 'No se encontró la sesión del director o el codlocal.');
        }

        $codlocal = $director['conf_permisos'][0]['codlocal'];

        // Obtener datos de la institución
        $institucion = Iiee_a_evaluar_rie::select('modalidad', 'institucion', 'direccion_ie', 'distrito')
            ->where('codlocal', $codlocal)
            ->first();

        // Obtener director UGEL 01
        $directorUgel = DB::table('nexus')
            ->select('nombres', 'apellipat', 'apellimat')
            ->where('descargo', 'DIRECTOR DE UNIDAD DE GESTIÓN EDUCATIVA LOCAL')
            ->where('nexus.estado', 1)
            ->first();

        // Obtener localidad 
        $localidad= DB::table('escale')
            ->select('localidad')
            ->where('codlocal', $codlocal)
            ->value('localidad');

        // Obtener turno 
        $d_cod_tur= DB::table('escale')
            ->select('d_cod_tur')
            ->where('codlocal', $codlocal)
            ->value('d_cod_tur');

        // Obtener nombre_anio 
        $anioActual = now()->year;

        $nombreAnio = DB::table('nombre_anio')
            ->where('anio',$anioActual)
            ->value('nombre');

        // Obtener resolucion
        $resolucion = Iiee_a_evaluar_rie::select('nro_rdcreacion')
            ->where('codlocal', $codlocal)
            ->value('nro_rdcreacion');
        //Bbtener codmods
        $codmodulares = Iiee_a_evaluar_rie::select('nivel', 'codmod')
            ->where('codlocal', $codlocal)
            ->get();
        
        //correo institucional
        $correo_inst = Iiee_a_evaluar_rie::select('iiee_a_evaluar_rie')
            ->select('correo_inst')
            ->where('codlocal', $codlocal)
            ->value('correo_inst');

        // Obtener logo 
        $logo= Iiee_a_evaluar_rie::select('logo')
            ->where('codlocal', $codlocal)
            ->value('logo');

        $logoBD = Iiee_a_evaluar_rie::where('codlocal', $codlocal)
            ->value('logo'); // Esto puede ser null o un string

        $nombreLogo = $logoBD ? basename($logoBD) : null;
        $rutaLogoWeb = $nombreLogo ? 'storage/logoie/' . $nombreLogo : null;


        //Obtener firma
        $firmaGuardada = DB::connection('siic_anexos')->table('anexo03')
        ->where('id_contacto', $director['id_contacto'])
        ->value('firma');

        // Obtener docentes y personal
        $personal = DB::table('nexus')
            ->select(
                'nexus.numdocum as dni',
                DB::raw("CONCAT(nexus.apellipat, ' ', nexus.apellimat, ', ', nexus.nombres) as nombres"),
                'nexus.descargo as cargo',
                'nexus.situacion as condicion',
                'nexus.jornlab as jornada',
                'nexus.descniveduc as nivel',
                'nexus.nombreooii as ugel',
                'nexus.codplaza as cod',
            )
            ->where('nexus.codlocal', $codlocal)
            ->whereNotNull('nexus.numdocum')
            ->where('nexus.numdocum', '!=', 'VACANTE')
            ->where('nexus.situacion', '!=', 'VACANTE')
            ->where('nexus.estado', 1)
            ->get();

        $niveles = $personal->pluck('nivel')->unique()->sort()->values();


        $nivelSeleccionado = $niveles;
        // Obtener personas con id_persona en anexo03_persona para este codlocal (igual que en mostrarAsistenciaDetallada)
        // Obtén las personas del reporte anexo03 para este codlocal
        $personasReporte = DB::connection('siic_anexos')->table('anexo03')
            ->join('anexo03_persona', 'anexo03.id_anexo03', '=', 'anexo03_persona.id_anexo03')
            ->where('anexo03.codlocal', $codlocal)
            ->select('anexo03_persona.id', 'anexo03_persona.persona_json')
            ->get()
            ->mapWithKeys(function ($item) {
                $persona = json_decode($item->persona_json);

                // Asegúrate que codplaza exista en el JSON
                $dni = $persona->dni ?? null;
                $codplaza = $persona->cod ?? ($persona->cod ?? null); // por si el campo se llama 'cod'

                // Si no hay codplaza, usar solo DNI, pero idealmente siempre debería haber
                $clave = $codplaza ? "{$dni}_{$codplaza}" : $dni;

                return [$clave => $item->id];
                
            });

        // Obtener asistencia para esas personas
        $asistencias = DB::connection('siic_anexos')->table('anexo03_asistencia')
            ->whereIn('id_persona', $personasReporte->values()->all())
            ->select('id_persona', 'asistencia', 'observacion')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id_persona => [
                    'asistencia' => json_decode($item->asistencia),
                    'observacion' => $item->observacion,
                ]];
            });

        // Mapear dni con asistencia para pasar a la vista PDF
        $datosAsistenciaPorDni = [];
        foreach ($personasReporte as $clave => $id_persona) {
            $datosAsistenciaPorDni[$clave] = $asistencias[$id_persona] ?? ['asistencia' => [], 'observacion' => null];
        }

        $filtrados = $personal->where('nivel', $nivelSeleccionado)->values();

        $filtrados = $filtrados->sort(function ($a, $b) {
        // Prioridad por jerarquía del cargo
        $prioridadCargo = function ($cargo) {
            $cargo = strtoupper(trim($cargo));
            if (Str::startsWith($cargo, 'DIRECTOR')) return 1;
            if (Str::startsWith($cargo, 'SUB-DIRECTOR')) return 2;
            if (Str::startsWith($cargo, 'JEFE')) return 3;
            if (Str::startsWith($cargo, 'COORDINADOR')) return 4;
            if (Str::startsWith($cargo, 'PROFESOR')) return 5;
            if (Str::startsWith($cargo, 'AUXILIAR')) return 6;
            return 99; // Otros
        };

        // Prioridad por condición laboral
        $prioridadCondicion = function ($condicion) {
            $condicion = strtoupper(trim($condicion));
            if ($condicion === 'NOMBRADO') return 1;
            if ($condicion === 'CONTRATADO') return 2;
            if (in_array($condicion, ['ASIGNADO', 'ASIGNADA'])) return 3;
            return 99;
        };

        $pa = $prioridadCargo($a->cargo);
        $pb = $prioridadCargo($b->cargo);

        if ($pa !== $pb) return $pa <=> $pb;

        $ca = $prioridadCondicion($a->condicion);
        $cb = $prioridadCondicion($b->condicion);

        if ($ca !== $cb) return $ca <=> $cb;

        // Comparar por jornada laboral (de mayor a menor)
        $ja = $a->jornada ?? 0;
        $jb = $b->jornada ?? 0;

        if ($ja !== $jb) return $jb <=> $ja;

        // Finalmente, comparar por nombres
        return strcmp($a->nombres, $b->nombres);
        })->values();

        $mes = now()->month;
        $anio = now()->year;

        // Datos comunes para ambas vistas
        $data = [
            'registros' => $filtrados,
            'niveles' => $niveles,
            'nivelSeleccionado' => $nivelSeleccionado,
            'modalidad' => $institucion->modalidad ?? '',
            'institucion' => $institucion->institucion ?? '',
            'anio' => $anio,
            'mes' => $mes,
            'codlocal' => $codlocal,
            'd_cod_tur' => $d_cod_tur,
            'logo' => $logo,
            'asistencias' => $datosAsistenciaPorDni,
            'firmaBase64' => $firmaBase64,
            'firmaGuardada' => $firmaGuardada,
            'logo' => $rutaLogoWeb,
            'codmodulares' => $codmodulares,
            'resolucion' => $resolucion,
            'correo_inst' =>$correo_inst,
        ];

        // Datos específicos para el oficio
        $datosOficio = [
            'direccion_ie' => $institucion->direccion_ie ?? '',
            'distrito' => $institucion->distrito ?? '',
            'directorUgel' => $directorUgel,
            'anio' => $anio,
            'mes' => $mes,
            'institucion' => $institucion->institucion ?? '',
            'localidad' => $localidad,
            'nombreAnio' =>$nombreAnio,
            'codlocal' => $codlocal,
            'logo' => $logo,
            'numeroOficio' => $numeroOficio,
            'firmaBase64' => $firmaBase64,
            'firmaGuardada' => $firmaGuardada,
            'logo' => $rutaLogoWeb,
            'codmodulares' => $codmodulares,
            'resolucion' => $resolucion,
            'correo_inst' =>$correo_inst,
        ];

        // Renderizar vista del Oficio (primera página vertical)
        $htmlOficio = View::make('reporteAnexo03.formulario03_oficio', $datosOficio)->render();

        // Crear instancia de mPDF con orientación vertical inicial
        $mpdf = new Mpdf([
            'format' => 'A4',
            'orientation' => 'P'
        ]);

        // Añadir footer dinámico con numeración tipo 1-3, 2-3, etc.
        $mpdf->SetHTMLFooter('
            <div style="text-align: right; font-size: 10px; color: #666;">
                Página {PAGENO}-{nbpg}
            </div>
        ');

        // Página 1: Oficio sin encabezado
        $mpdf->SetHTMLHeader(''); // vacío para la portada
        $mpdf->WriteHTML($htmlOficio);

        // Definir encabezado vertical para las siguientes páginas
        $headerVertical = '
        <div style="
            position: fixed;
            top: 18%;
            right: 0;
            transform: translateY(-50%) rotate(180deg);
            writing-mode: vertical-rl;
            text-orientation: mixed;
            width: 40px;
            font-weight: bold;
            font-size: 23px;
            line-height: 1.2;
            color: #999;
            text-align: center;
        ">
            R<br>S<br>G<br>-<br>1<br>2<br>1<br>-<br>2<br>0<br>1<br>8<br>-<br>M<br>I<br>N<br>E<br>D<br>U
        </div>
        ';

        // Recorrer niveles seleccionados y generar páginas por nivel
        foreach ($nivelSeleccionado as $nivel) {
            // Filtrar y ordenar datos del personal por nivel
            $filtrados = $personal->where('nivel', $nivel)->values();
            $filtrados = $filtrados->sort(function ($a, $b) {
                // Prioridad por jerarquía del cargo
                $prioridadCargo = function ($cargo) {
                    $cargo = strtoupper(trim($cargo));
                    if (Str::startsWith($cargo, 'DIRECTOR')) return 1;
                    if (Str::startsWith($cargo, 'SUB-DIRECTOR')) return 2;
                    if (Str::startsWith($cargo, 'JEFE')) return 3;
                    if (Str::startsWith($cargo, 'COORDINADOR')) return 4;
                    if (Str::startsWith($cargo, 'PROFESOR')) return 5;
                    if (Str::startsWith($cargo, 'AUXILIAR')) return 6;
                    return 99; // Otros
                };

                // Prioridad por condición laboral
                $prioridadCondicion = function ($condicion) {
                    $condicion = strtoupper(trim($condicion));
                    if ($condicion === 'NOMBRADO') return 1;
                    if ($condicion === 'CONTRATADO') return 2;
                    if (in_array($condicion, ['ASIGNADO', 'ASIGNADA'])) return 3;
                    return 99;
                };

                $pa = $prioridadCargo($a->cargo);
                $pb = $prioridadCargo($b->cargo);

                if ($pa !== $pb) return $pa <=> $pb;

                $ca = $prioridadCondicion($a->condicion);
                $cb = $prioridadCondicion($b->condicion);

                if ($ca !== $cb) return $ca <=> $cb;

                // Comparar por jornada laboral (de mayor a menor)
                $ja = $a->jornada ?? 0;
                $jb = $b->jornada ?? 0;

                if ($ja !== $jb) return $jb <=> $ja;

                // Finalmente, comparar por nombres
                return strcmp($a->nombres, $b->nombres);
            })->values();

            // Preparar datos para la vista del PDF
            $data = [
                'registros' => $filtrados,
                'niveles' => $niveles,
                'nivelSeleccionado' => $nivel,
                'modalidad' => $institucion->modalidad ?? '',
                'institucion' => $institucion->institucion ?? '',
                'anio' => $anio,
                'mes' => $mes,
                'codlocal' => $codlocal,
                'd_cod_tur' => $d_cod_tur,
                'logo' => $logo,
                'asistencias' => $datosAsistenciaPorDni,
                'firmaBase64' => $firmaBase64,
                'firmaGuardada' => $firmaGuardada,
            ];

            // Renderizar vista del reporte horizontal
            $htmlReporte = View::make('reporteAnexo03.formulario03_pdf', $data)->render();

            // Establecer encabezado ANTES de agregar la nueva página
            $mpdf->SetHTMLHeader($headerVertical);
            $mpdf->AddPage('L');
            $mpdf->WriteHTML($htmlReporte);
        }

        // Descargar el PDF generado
        return response($mpdf->Output('reporte_asistencia_con_oficio.pdf', 'I'), 200)
            ->header('Content-Type', 'application/pdf');
    }


    public function guardarFirma(Request $request)
    {
        $session = session('siic01');

        if (!$session || !isset($session['id_contacto'])) {
            return response()->json(['success' => false, 'error' => 'Sesión no válida']);
        }

        $idContacto = $session['id_contacto'];

        if ($request->hasFile('firma')) {
            $file = $request->file('firma');
            $extension = $file->getClientOriginalExtension();
            $filename = 'firma_' . $idContacto . '.' . $extension;

            // Guardar archivo
            $file->storeAs('public/firmasdirector', $filename);

            // Actualizar el campo firma en la tabla anexo03 del director
            DB::connection('siic_anexos')->table('anexo03')
                ->where('id_contacto', $idContacto)
                ->update(['firma' => $filename]);

            return response()->json(['success' => true, 'path' => $filename]);
        }

        return response()->json(['success' => false, 'error' => 'No se recibió el archivo']);
    }
}





