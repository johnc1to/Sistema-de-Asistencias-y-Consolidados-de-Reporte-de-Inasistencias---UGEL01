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
use Illuminate\Support\Facades\Storage;


class Anexo03Controller extends Controller
{

    public function mostrarAsistenciaDetallada(Request $request)
    {
        $director = session('siic01');
        //dd($director);
        if (!$director || empty($director['conf_permisos'][0]['codlocal'])) {
            return redirect()->back()->with('error', 'No se encontró la sesión del director o el codlocal.');
        }

        $codlocal = $director['conf_permisos'][0]['codlocal'];
        
        $institucion = Iiee_a_evaluar_rie::join('conf_permisos as P', 'P.esc_codmod', '=', 'iiee_a_evaluar_RIE.codmod')
            ->join('contacto as C', 'C.id_contacto', '=', 'P.id_contacto')
            ->where('C.dni', $director['dni'])
            ->where('C.estado', 1)
            ->where('C.flg', 1)
            ->where('P.estado', 1)
            ->where('iiee_a_evaluar_RIE.estado', 1)
            ->select('iiee_a_evaluar_RIE.*')
            ->first();

        if ($institucion && empty($institucion->dni_director)) {
            $institucion->dni_director = $director['dni'];
            $institucion->save();
        }
        //dd($institucion);
        // Ahora ya consultamos con dni seguro
        $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad','institucion', 'codmod')
            ->where('codlocal', $codlocal)
            ->where('dni_director', $director['dni'])
            ->first();

        // Ya tienes $institucion en este punto
        $codmod = $institucion->codmod ?? null;
        //dd($codmod);
        // Obtener turno con codlocal + codmod
        $d_cod_tur = Iiee_a_evaluar_rie::select('turno')
            ->where('codlocal', $codlocal)
            ->where('codmod', $codmod)
            ->value('turno');
  
        
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
        if (!$institucion) {
            return redirect()->back()->with('error', 'No se encontró institución para este director.');
        }

        //  Aquí ya tenemos idmodalidad de frente
        $idModalidad = $institucion->idmodalidad;

        //  Ahora ya tenemos el idmodalidad de la institución directamente
        $idnivelesModalidad = DB::table('niveles')
            ->where('Idmodalidad', $idModalidad)
            ->pluck('idnivel');
        
        //dd($idnivelesModalidad);
        $personalNexus = DB::table('nexus')
            ->select(
                'nexus.numdocum as dni',
                DB::raw("CONCAT(nexus.apellipat, ' ', nexus.apellimat, ', ', nexus.nombres) as nombres"),
                'nexus.descargo as cargo',
                'nexus.situacion as condicion',
                'nexus.jornlab as jornada',
                'nexus.descniveduc as nivel',
                'nexus.nombreooii as ugel',
                'nexus.codplaza as cod',
                'nexus.obser as obser',
                'nexus.descmovim as mov',
                'nexus.fecinicio as finicio',
                'nexus.fectermino as ftermino',
                DB::raw("'OFICIAL' as fuente") // <- Para saber de dónde viene
            )
            ->where('nexus.codlocal', $codlocal)
            ->whereIn('nexus.idnivel', $idnivelesModalidad)
            ->whereNotNull('nexus.numdocum')
            ->where('nexus.numdocum', '!=', 'VACANTE')
            ->where('nexus.situacion', '!=', 'VACANTE')
            ->where('nexus.estado', 1);

        $personalExcepcional = DB::table('nexus_excepcional')
            ->select(
                'nexus_excepcional.numdocum as dni',
                DB::raw("CONCAT(nexus_excepcional.apellipat, ' ', nexus_excepcional.apellimat, ', ', nexus_excepcional.nombres) as nombres"),
                'nexus_excepcional.descargo as cargo',
                'nexus_excepcional.situacion as condicion',
                'nexus_excepcional.jornlab as jornada',
                'nexus_excepcional.descniveduc as nivel',
                'nexus_excepcional.nombreooii as ugel',
                'nexus_excepcional.codplaza as cod',
                'nexus_excepcional.obser as obser',
                'nexus_excepcional.descmovim as mov',
                'nexus_excepcional.fecinicio as finicio',
                'nexus_excepcional.fectermino as ftermino',
                DB::raw("'EXCEPCIONAL' as fuente")
            )
            ->where('nexus_excepcional.codlocal', $codlocal)
            ->whereIn('nexus_excepcional.idnivel', $idnivelesModalidad)
            ->whereNotNull('nexus_excepcional.numdocum')
            ->where('nexus_excepcional.numdocum', '!=', 'VACANTE')
            ->where('nexus_excepcional.situacion', '!=', 'VACANTE')
            ->where('nexus_excepcional.estado', 1);

        $personal = $personalNexus->unionAll($personalExcepcional)->get();

        //dd($personal);
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

        // Mes que se va a mostrar en el reporte; puede venir del request o usar el mes actual
        $mes = $request->input('mes', Carbon::now()->month);
        $anio = $request->input('anio', Carbon::now()->year);
        // Obtén las personas del reporte anexo03 para este codlocal, incluyendo fecha_creacion del anexo03
        $personasReporte = DB::connection('siic_anexos')->table('anexo03_persona as ap')
            ->join('anexo03 as a', 'a.id_anexo03', '=', 'ap.id_anexo03')
            ->where('a.codlocal', $codlocal)
            ->select('ap.id', 'ap.persona_json', 'a.fecha_creacion') 
            ->get()
            ->mapWithKeys(function ($item) {
                $persona = json_decode($item->persona_json);
                $dni = $persona->dni ?? null;
                $codplaza = $persona->cod ?? null;
                $clave = $codplaza ? "{$dni}_{$codplaza}" : $dni;

                return [$clave => [
                    'id_persona' => $item->id,
                    'fecha_creacion' => $item->fecha_creacion
                ]];
            });

        // Obtén la asistencia y observación
        $asistencias = DB::connection('siic_anexos')->table('anexo03_asistencia')
            ->whereIn('id_persona', collect($personasReporte)->pluck('id_persona')->all())
            ->select('id_persona', 'asistencia', 'observacion','tipo_observacion','observacion_detalle','fecha_inicio','fecha_fin')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id_persona => [
                    'asistencia' => json_decode($item->asistencia),
                    'observacion' => $item->observacion,
                    'tipo_observacion' => $item->tipo_observacion,
                    'observacion_detalle' => $item->observacion_detalle,
                    'fecha_inicio' => $item->fecha_inicio,
                    'fecha_fin' => $item->fecha_fin,
                ]];
            });

        // Combinar datos de asistencia y mes
        $datosAsistenciaPorDni = [];
        foreach ($personasReporte as $clave => $info) {
            $id_persona = $info['id_persona'];
            $fechaCreacion = Carbon::parse($info['fecha_creacion']);
            $mesAsistencia = $fechaCreacion->month;

            $dato = $asistencias[$id_persona] ?? null;

            if ($dato) {
                // Usar siempre la asistencia que haya guardada
                $diasLaborables = [];
                foreach ($dato['asistencia'] as $i => $valor) {
                    if ($valor === 'A') $diasLaborables[] = $i + 1;
                }
                $dato['dias_laborables'] = $diasLaborables;
                $datosAsistenciaPorDni[$clave] = $dato;
            } else {
                // Si no existe asistencia → inicializar con patrón laboral (lunes a viernes)
                $diasLaborables = [];
                $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;
                for ($i = 1; $i <= $diasEnMes; $i++) {
                    $dow = Carbon::create($anio, $mes, $i)->dayOfWeek;
                    if ($dow >= 1 && $dow <= 5) {
                        $diasLaborables[] = $i;
                    }
                }

                $datosAsistenciaPorDni[$clave] = [
                    'asistencia' => [], 
                    'observacion' => null,
                    'tipo_observacion' => null,
                    'observacion_detalle' => null,
                    'dias_laborables' => $diasLaborables
                ];
            }

            $datosAsistenciaPorDni[$clave]['mes'] = $mes;

        }

        return view('reporteAnexo03.formulario03', [
            'registros' => $filtrados,
            'niveles' => $niveles,
            'nivelSeleccionado' => $nivelSeleccionado,
            'modalidad' => $institucion->modalidad ?? '',
            'institucion' => $institucion->institucion ?? '',
            'anio' => now()->year,
            'mes' => $mes,
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
            // Buscar si ya existe un anexo03 para ese contacto + local + nivel
            $anexoExistente = DB::connection('siic_anexos')->table('anexo03')
                ->where('id_contacto', $request->id_contacto)
                ->where('codlocal', $request->codlocal)
                ->where('nivel', $request->nivel)
                ->orderByDesc('fecha_creacion')
                ->first();

            $idAnexo03 = null;

            if ($anexoExistente) {
                if (is_null($anexoExistente->oficio) || is_null($anexoExistente->expediente)) {
                    // Buscar la fecha mínima del último bloque abierto (los que aún no tienen oficio/expediente)
                    $ultimaFecha = DB::connection('siic_anexos')->table('anexo03')
                        ->where('id_contacto', $request->id_contacto)
                        ->where('codlocal', $request->codlocal)
                        ->whereNull('oficio')
                        ->whereNull('expediente')
                        ->min('fecha_creacion'); // primer registro del bloque abierto

                    // Actualizar todos los registros de ese bloque abierto
                    DB::connection('siic_anexos')->table('anexo03')
                        ->where('id_contacto', $request->id_contacto)
                        ->where('codlocal', $request->codlocal)
                        ->where('fecha_creacion', '>=', $ultimaFecha)
                        ->whereNull('oficio')
                        ->whereNull('expediente')
                        ->update([
                            'oficio' => $request->numero_oficio,
                            'expediente' => $request->numero_expediente,
                            'fecha_actualizacion' => now(),
                        ]);

                    $idAnexo03 = $anexoExistente->id_anexo03;
                } else {
                    // Caso 2: Bloque cerrado → crear uno nuevo
                    $idAnexo03 = DB::connection('siic_anexos')->table('anexo03')->insertGetId([
                        'id_contacto' => $request->id_contacto,
                        'codlocal'   => $request->codlocal,
                        'nivel'      => $request->nivel,
                        'oficio'     => $request->numero_oficio,
                        'expediente' => $request->numero_expediente,
                        'fecha_creacion' => now(),
                    ]);
                }
            } else {
                // Caso 3: No existe ninguno → crear primero
                $idAnexo03 = DB::connection('siic_anexos')->table('anexo03')->insertGetId([
                    'id_contacto' => $request->id_contacto,
                    'codlocal'   => $request->codlocal,
                    'nivel'      => $request->nivel,
                    'oficio'     => $request->numero_oficio,
                    'expediente' => $request->numero_expediente,
                    'fecha_creacion' => now(),
                ]);
            }

            // Guardar docentes (sin duplicar)
            foreach ($request->docentes as $docente) {
                if (!isset($docente['dni']) || !isset($docente['asistencia'])) {
                    throw new \Exception("Datos incompletos para docente: " . json_encode($docente));
                }

                // Buscar si ya existe esa persona en este anexo03
                $personaExistente = DB::connection('siic_anexos')->table('anexo03_persona')
                    ->where('id_anexo03', $idAnexo03)
                    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(persona_json, '$.dni')) = ?", [$docente['dni']])
                    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(persona_json, '$.cod')) = ?", [$docente['cod']])
                    ->first();

                if ($personaExistente) {
                    // Actualizar persona_json
                    DB::connection('siic_anexos')->table('anexo03_persona')
                        ->where('id', $personaExistente->id)
                        ->update([
                            'persona_json' => json_encode([
                                'dni' => $docente['dni'],
                                'nombres' => $docente['nombres'] ?? '',
                                'cargo' => $docente['cargo'] ?? '',
                                'condicion' => $docente['condicion'] ?? '',
                                'jornada' => $docente['jornada'] ?? '',
                                'cod' => $docente['cod'] ?? '',
                            ]),
                        ]);

                    $idPersona = $personaExistente->id;

                    // Actualizar asistencia
                    DB::connection('siic_anexos')->table('anexo03_asistencia')
                        ->where('id_persona', $idPersona)
                        ->update([
                            'asistencia' => json_encode($docente['asistencia']),
                            'observacion' => $docente['observacion'] ?? null,
                            'tipo_observacion' => $docente['tipo_observacion'] ?? null,
                            'observacion_detalle' => $docente['observacion_detalle'] ?? null,
                            'fecha_inicio' => $docente['fecha_inicio'] ?? null,
                            'fecha_fin' => $docente['fecha_fin'] ?? null,
                        ]);
                } else {
                    // Crear persona nueva
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

                    // Crear asistencia nueva
                    DB::connection('siic_anexos')->table('anexo03_asistencia')->insert([
                        'id_persona' => $idPersona,
                        'asistencia' => json_encode($docente['asistencia']),
                        'observacion' => $docente['observacion'] ?? null,
                        'tipo_observacion' => $docente['tipo_observacion'] ?? null,
                        'observacion_detalle' => $docente['observacion_detalle'] ?? null,
                        'fecha_inicio' => $docente['fecha_inicio'] ?? null,
                        'fecha_fin' => $docente['fecha_fin'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
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
        $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion')
            ->where('codlocal', $codlocal)
            ->where('dni_director', $director['dni'])
            ->first();

        //Lista de docentes desde nexus (para control de acceso)
        if (!$institucion) {
            return redirect()->back()->with('error', 'No se encontró institución para este director.');
        }
        //  Aquí ya tenemos idmodalidad de frente
        $idModalidad = $institucion->idmodalidad;
        //  Ahora ya tenemos el idmodalidad de la institución directamente
        $idnivelesModalidad = DB::table('niveles')
            ->where('Idmodalidad', $idModalidad)
            ->pluck('idnivel');
        
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
            ->whereIn('nexus.idnivel', $idnivelesModalidad)
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
        $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion', 'direccion_ie', 'distrito', 'codmod')
            ->where('codlocal', $codlocal)
            ->where('dni_director', $director['dni'])
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

        // Ya tienes $institucion en este punto
        $codmod = $institucion->codmod ?? null;
        //dd($codmod);
        // Obtener turno con codlocal + codmod
        $d_cod_tur = Iiee_a_evaluar_rie::select('turno')
            ->where('codlocal', $codlocal)
            ->where('codmod', $codmod)
            ->value('turno'); 

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
        $correo_inst = Iiee_a_evaluar_rie::select('iiee_a_evaluar_RIE')
            ->select('correo_inst')
            ->where('codlocal', $codlocal)
            ->value('correo_inst');

        // Obtener logo 
        $logo= Iiee_a_evaluar_rie::select('logo')
            ->where('codlocal', $codlocal)
            ->where('codmod', $codmod)
            ->value('logo');

        $logoBD = Iiee_a_evaluar_rie::where('codlocal', $codlocal)
        ->where('codmod', $codmod)
            ->value('logo'); 

        $nombreLogo = $logoBD ? basename($logoBD) : null;
        $rutaLogoWeb = $nombreLogo ? 'storage/logoie/' . $nombreLogo : null;

        //Obtener firma
        $firmaGuardada = DB::connection('siic_anexos')->table('anexo03')
        ->where('id_contacto', $director['id_contacto'])
        ->value('firma');


        //Lista de docentes desde nexus (para control de acceso)
            if (!$institucion) {
                return redirect()->back()->with('error', 'No se encontró institución para este director.');
            }
            //  Aquí ya tenemos idmodalidad de frente
            $idModalidad = $institucion->idmodalidad;
            //  Ahora ya tenemos el idmodalidad de la institución directamente
            $idnivelesModalidad = DB::table('niveles')
                ->where('Idmodalidad', $idModalidad)
                ->pluck('idnivel');

        // Obtener docentes y personal
        $personalNexus = DB::table('nexus')
            ->select(
                'nexus.numdocum as dni',
                DB::raw("CONCAT(nexus.apellipat, ' ', nexus.apellimat, ', ', nexus.nombres) as nombres"),
                'nexus.descargo as cargo',
                'nexus.situacion as condicion',
                'nexus.jornlab as jornada',
                'nexus.descniveduc as nivel',
                'nexus.nombreooii as ugel',
                'nexus.codplaza as cod',
                'nexus.obser as obser',
                'nexus.descmovim as mov',
                'nexus.fecinicio as finicio',
                'nexus.fectermino as ftermino',
                DB::raw("'OFICIAL' as fuente")
            )
            ->where('nexus.codlocal', $codlocal)
            ->whereIn('nexus.idnivel', $idnivelesModalidad)
            ->whereNotNull('nexus.numdocum')
            ->where('nexus.numdocum', '!=', 'VACANTE')
            ->where('nexus.situacion', '!=', 'VACANTE')
            ->where('nexus.estado', 1);

        $personalExcepcional = DB::table('nexus_excepcional')
            ->select(
                'nexus_excepcional.numdocum as dni',
                DB::raw("CONCAT(nexus_excepcional.apellipat, ' ', nexus_excepcional.apellimat, ', ', nexus_excepcional.nombres) as nombres"),
                'nexus_excepcional.descargo as cargo',
                'nexus_excepcional.situacion as condicion',
                'nexus_excepcional.jornlab as jornada',
                'nexus_excepcional.descniveduc as nivel',
                'nexus_excepcional.nombreooii as ugel',
                'nexus_excepcional.codplaza as cod',
                'nexus_excepcional.obser as obser',
                'nexus_excepcional.descmovim as mov',
                'nexus_excepcional.fecinicio as finicio',
                'nexus_excepcional.fectermino as ftermino',
                DB::raw("'EXCEPCIONAL' as fuente")
            )
            ->where('nexus_excepcional.codlocal', $codlocal)
            ->whereIn('nexus_excepcional.idnivel', $idnivelesModalidad)
            ->whereNotNull('nexus_excepcional.numdocum')
            ->where('nexus_excepcional.numdocum', '!=', 'VACANTE')
            ->where('nexus_excepcional.situacion', '!=', 'VACANTE')
            ->where('nexus_excepcional.estado', 1);

        $personal = $personalNexus->unionAll($personalExcepcional)->get();

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

                $dni = $persona->dni ?? null;
                $codplaza = $persona->cod ?? ($persona->cod ?? null);

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
        $mpdf->SetHTMLHeader('');
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

        $pdfContent = $mpdf->Output('', 'S');

        $nombreArchivo = 'anexos03/reporte_'.$codlocal.'_'.date('Ymd_His').'.pdf';

        // Guardar en storage/app/public/anexos03
        Storage::disk('public')->put('anexos03/'.$nombreArchivo, $pdfContent);

        DB::connection('siic_anexos')->table('anexo03')
            ->where('codlocal', $codlocal)
            ->update([
                'ruta_pdf' => 'anexos03/'.$nombreArchivo,
                'fecha_actualizacion' => now()
            ]);

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





