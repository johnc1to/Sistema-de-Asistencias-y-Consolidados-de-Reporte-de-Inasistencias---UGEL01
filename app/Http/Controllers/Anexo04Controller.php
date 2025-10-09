<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anexo04Inasistencia;
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
use Illuminate\Support\Facades\Storage;
use App\Models\Anexo04Persona;
use App\Models\Anexo04;
use App\Models\Iiee_a_evaluar_rie;


class Anexo04Controller extends Controller
{

    public function mostrarInasistenciaDetallada(Request $request)
    {
        $director = session('siic01');

        if (!$director || empty($director['conf_permisos'][0]['codlocal'])) {
            return redirect()->back()->with('error', 'No se encontró la sesión del director o el codlocal.');
        }

        $codlocal = $director['conf_permisos'][0]['codlocal'];

        $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion', 'codmod')
            ->where('codlocal', $codlocal)
            ->where('dni_director', $director['dni'])
            ->first();
        // Ya tienes $institucion en este punto
        $codmod = $institucion->codmod ?? null;
        // Obtener turno (corregido)
        $d_cod_tur= Iiee_a_evaluar_rie::select('turno')
            ->where('codlocal', $codlocal)
            ->where('codmod', $codmod)
            ->value('turno'); 

        // Obtener firma
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
        
        // Lista de personal
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


        // Niveles únicos
        $niveles = $personal->pluck('nivel')->unique()->sort()->values();

        // Nivel seleccionado
        $nivelSeleccionado = $request->get('nivel');
        if (!$nivelSeleccionado || !$niveles->contains($nivelSeleccionado)) {
            $nivelSeleccionado = $niveles->first();
        }

        // Filtra personal por nivel
        $filtrados = $personal->where('nivel', $nivelSeleccionado)->values();

        // Ordenamiento personalizado
        $filtrados = $filtrados->sort(function ($a, $b) {
            $prioridadCargo = function ($cargo) {
                $cargo = strtoupper(trim($cargo));
                if (Str::startsWith($cargo, 'DIRECTOR')) return 1;
                if (Str::startsWith($cargo, 'SUB-DIRECTOR')) return 2;
                if (Str::startsWith($cargo, 'JEFE')) return 3;
                if (Str::startsWith($cargo, 'COORDINADOR')) return 4;
                if (Str::startsWith($cargo, 'PROFESOR')) return 5;
                if (Str::startsWith($cargo, 'AUXILIAR')) return 6;
                return 99;
            };

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

            $ja = $a->jornada ?? 0;
            $jb = $b->jornada ?? 0;
            if ($ja !== $jb) return $jb <=> $ja;

            return strcmp($a->nombres, $b->nombres);
        })->values();

        $hoy = new \DateTime(); // fecha actual
        $fecha = (clone $hoy)->modify('first day of last month'); // primer día del mes anterior

        $anio = (int) $fecha->format('Y');
        $mes  = (int) $fecha->format('n');

        // 🔹 Buscar oficio SOLO si pertenece al mes/anio de trabajo (no al actual ni anterior)
        $oficioguardado = DB::connection('siic_anexos')->table('anexo04')
            ->where('id_contacto', $director['id_contacto'])
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->orderBy('fecha_creacion', 'desc')
            ->value('oficio');

        // 🔹 Buscar expediente igual
        $expedienteguardado = DB::connection('siic_anexos')->table('anexo04')
            ->where('id_contacto', $director['id_contacto'])
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->orderBy('fecha_creacion', 'desc')
            ->value('expediente');


        // Obtener todos los registros anexo04 que coincidan con el filtro
        $anexos04 = DB::connection('siic_anexos')->table('anexo04')
            ->where('codlocal', $codlocal)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('id_contacto', $director['id_contacto'])
            ->get();

        $idsAnexo04 = $anexos04->pluck('id')->all();

        $personasAnexo04 = DB::connection('siic_anexos')->table('anexo04_persona as ap')
            ->join('anexo04 as a', 'a.id', '=', 'ap.id_anexo04')
            ->where('a.codlocal', $codlocal)
            ->select('ap.id', 'ap.persona_json', 'a.fecha_creacion') 
            ->get()
            ->mapWithKeys(function ($item) {
                $persona = json_decode($item->persona_json);
                $dni = $persona->dni ?? null;
                $codplaza = $persona->cod ?? null;
                $clave = $codplaza ? "{$dni}_{$codplaza}" : $dni;

                return [$clave => [
                    'id' => $item->id,
                    'fecha_creacion' => $item->fecha_creacion
                ]];
            });


            $idPersonas = collect($personasAnexo04)->pluck('id')->all();

            $inasistencias = DB::connection('siic_anexos')->table('anexo04_inasistencia')
                ->whereIn('id_persona', $idPersonas) // <- ahora sí es un array plano
                ->get()
                ->mapWithKeys(function ($item) {
                    $totales = json_decode($item->inasistencia, true);
                    if (is_string($totales)) {
                        $totales = json_decode($totales, true);
                    }

                    $detalle = json_decode($item->detalle, true);
                    if (is_string($detalle)) {
                        $detalle = json_decode($detalle, true);
                    }

                    return [$item->id_persona => [
                        'totales' => $totales,
                        'detalle' => $detalle
                    ]];
                });

            $datosInasistenciaPorPersona = [];

            foreach ($personasAnexo04 as $clave => $infoPersona) {
                $id = $infoPersona['id']; // <-- aquí tomamos solo el ID

                $inasistencia = $inasistencias[$id] ?? null;

                if ($inasistencia) {
                    $datosInasistenciaPorPersona[$clave] = [
                        'inasistencia_total' => $inasistencia['totales']['inasistencia_total'] ?? 0,
                        'huelga_total' => $inasistencia['totales']['huelga_total'] ?? 0,
                        'tardanza_total' => [
                            'horas' => $inasistencia['totales']['tardanza_total']['horas'] ?? 0,
                            'minutos' => $inasistencia['totales']['tardanza_total']['minutos'] ?? 0,
                        ],
                        'permiso_sg_total' => [
                            'horas' => $inasistencia['totales']['permiso_sg_total']['horas'] ?? 0,
                            'minutos' => $inasistencia['totales']['permiso_sg_total']['minutos'] ?? 0,
                        ],
                    ];
                } else {
                    $datosInasistenciaPorPersona[$clave] = [
                        'inasistencia_total' => 0,
                        'huelga_total' => 0,
                        'tardanza_total' => ['horas' => 0, 'minutos' => 0],
                        'permiso_sg_total' => ['horas' => 0, 'minutos' => 0],
                    ];
                }
            }



            // Combinar con personas filtradas
            $filtrados = $filtrados->map(function ($persona) use ($datosInasistenciaPorPersona, $inasistencias, $personasAnexo04) {
                $clave = $persona->dni . '_' . $persona->cod;
                $id_persona = $personasAnexo04[$clave]['id'] ?? null;

                if ($id_persona && isset($inasistencias[$id_persona]['totales'])) {
                    $tot = $inasistencias[$id_persona]['totales'];
                    $persona->inasistencias_dias = $tot['inasistencia_total'] ?? 0;
                    $persona->huelga_paro_dias = $tot['huelga_total'] ?? 0;
                    $persona->tardanzas_horas = $tot['tardanza_total']['horas'] ?? 0;
                    $persona->tardanzas_minutos = $tot['tardanza_total']['minutos'] ?? 0;
                    $persona->permisos_sg_horas = $tot['permiso_sg_total']['horas'] ?? 0;
                    $persona->permisos_sg_minutos = $tot['permiso_sg_total']['minutos'] ?? 0;
                }

                $detalle = $inasistencias[$id_persona]['detalle'] ?? null;
                $persona->detalle_inasistencia_json = $detalle ?: null;

                return $persona;
            });


            return view('reporteAnexo04.formulario04', [
                'registros' => $filtrados,
                'niveles' => $niveles,
                'nivelSeleccionado' => $nivelSeleccionado,
                'modalidad' => $institucion->modalidad ?? '',
                'institucion' => $institucion->institucion ?? '',
                'anio' => $anio,
                'mes' => $mes,
                'configuraciones' => null,
                'codlocal' => $codlocal,
                'd_cod_tur' => $d_cod_tur,
                'firmaGuardada' => $firmaGuardada,
                'inasistencias' => $datosInasistenciaPorPersona,
                'oficioguardado' => $oficioguardado,
                'expedienteguardado' => $expedienteguardado
            ]);
    }


    public function storeMasivo(Request $request)
    {
        \Log::info('Datos recibidos en storeMasivo:', $request->all());

        $request->validate([
            'mes' => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2000',
            'codlocal' => 'required|string',
            'nivel' => 'nullable|string',
            'personas' => 'required|array',
            'personas.*.persona' => 'required|array',
            'personas.*.inasistencia' => 'required|array',
            'personas.*.observacion' => 'nullable|string',
            'numero_oficio' => 'nullable|string',
            'numero_expediente' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // --- 1. Encontrar o crear bloque Anexo04 ---
            if ($request->numero_oficio) {
            // Si llega un expediente, validar si ya existe el mismo oficio sin expediente
                if (!empty($request->numero_expediente)) {
                    $coincideOficio = Anexo04::where('codlocal', $request->codlocal)
                        ->where('mes', $request->mes)
                        ->where('anio', $request->anio)
                        ->where('oficio', $request->numero_oficio)
                        ->whereNull('expediente')
                        ->first();

                    if ($coincideOficio) {
                        //  Actualiza todos los niveles con ese mismo oficio sin expediente
                        Anexo04::where('codlocal', $request->codlocal)
                            ->where('mes', $request->mes)
                            ->where('anio', $request->anio)
                            ->where('oficio', $request->numero_oficio)
                            ->whereNull('expediente')
                            ->update([
                                'expediente' => $request->numero_expediente,
                                'fecha_actualizacion' => now(),
                            ]);

                        // Reasignamos $anexo para continuar usando ese bloque existente
                        $anexo = $coincideOficio;
                    } else {
                        // Si no hay coincidencia de oficio → sigue flujo normal
                        $borrador = Anexo04::where('codlocal', $request->codlocal)
                            ->where('nivel', $request->nivel)
                            ->where('mes', $request->mes)
                            ->where('anio', $request->anio)
                            ->whereNull('oficio')
                            ->first();
                    }
                } else {
                    // Si no hay expediente aún → flujo normal (registrar oficio)
                    $borrador = Anexo04::where('codlocal', $request->codlocal)
                        ->where('nivel', $request->nivel)
                        ->where('mes', $request->mes)
                        ->where('anio', $request->anio)
                        ->whereNull('oficio')
                        ->first();
                }

                if (!isset($anexo)) {
                    if ($borrador ?? null) {
                        // Actualiza todos los niveles del mismo codlocal, mes y año
                        Anexo04::where('codlocal', $request->codlocal)
                            ->where('mes', $request->mes)
                            ->where('anio', $request->anio)
                            ->whereNull('oficio')
                            ->update([
                                'oficio' => $request->numero_oficio,
                                'expediente' => $request->numero_expediente,
                                'fecha_actualizacion' => now(),
                            ]);

                        $anexo = $borrador;
                    } else {
                        // Si no hay borrador → crear nuevo
                        $anexo = Anexo04::create([
                            'id_contacto' => session('siic01')['id_contacto'],
                            'codlocal' => $request->codlocal,
                            'nivel' => $request->nivel,
                            'mes' => $request->mes,
                            'anio' => $request->anio,
                            'oficio' => $request->numero_oficio,
                            'expediente' => $request->numero_expediente,
                            'fecha_creacion' => now(),
                            'fecha_actualizacion' => now(),
                        ]);
                    }
                }
            }else {
                // Si no hay oficio todavía → buscar o crear un borrador
                $anexo = Anexo04::where('codlocal', $request->codlocal)
                    ->where('nivel', $request->nivel)
                    ->where('mes', $request->mes)
                    ->where('anio', $request->anio)
                    ->whereNull('oficio')
                    ->first();

                if (!$anexo) {
                    $anexo = Anexo04::create([
                        'id_contacto' => session('siic01')['id_contacto'],
                        'codlocal' => $request->codlocal,
                        'nivel' => $request->nivel,
                        'mes' => $request->mes,
                        'anio' => $request->anio,
                        'fecha_creacion' => now(),
                        'fecha_actualizacion' => now(),
                    ]);
                }
            }

            // --- 2. Funciones auxiliares ---
            $normalizeDetalle = function (&$inasistencia) {
                if (!isset($inasistencia['detalle'])) {
                    $inasistencia['detalle'] = [
                        'inasistencia' => [],
                        'tardanza' => [],
                        'permiso_sg' => [],
                        'huelga' => [],
                    ];
                }
            };

            $normalizeHorasMinutos = function ($horas, $minutos) {
                $horasExtra = intdiv($minutos, 60);
                $minutos = $minutos % 60;
                $horas += $horasExtra;
                return ['horas' => $horas, 'minutos' => $minutos];
            };

            // --- 3. Cargar todas las personas existentes del bloque ---
            $personasExistentes = Anexo04Persona::where('id_anexo04', $anexo->id)->get();
            $personasMap = [];
            foreach ($personasExistentes as $pe) {
                $data = is_string($pe->persona_json) ? json_decode($pe->persona_json, true) : $pe->persona_json;
                if (isset($data['dni']) && isset($data['cod'])) {
                    $clave = $data['dni'].'_'.$data['cod'];
                    $personasMap[$clave] = $pe;
                }
            }

            // --- 4. Recorrer personas del request ---
            foreach ($request->personas as $p) {
                $dni = $p['persona']['dni'];
                $cod = $p['persona']['codplaza'] ?? $p['persona']['cod'] ?? null; 
                $persona_json = $p['persona']; 

                $clave = $dni.'_'.$cod;

                // Buscar persona por DNI
                if (isset($personasMap[$clave])) {
                    $persona = $personasMap[$clave];

                    // añadir cod = codplaza si está en los datos
                    if (isset($p['persona']['codplaza'])) {
                        $persona_json['cod'] = $p['persona']['codplaza'];
                    }

                    $persona->persona_json = $persona_json;
                    $persona->save();
                } else {
                    if (isset($p['persona']['codplaza'])) {
                        $persona_json['cod'] = $p['persona']['codplaza'];
                    }

                    $persona = Anexo04Persona::create([
                        'id_anexo04' => $anexo->id,
                        'persona_json' => $persona_json,
                    ]);
                    $personasMap[$clave] = $persona;
                }

                // --- 5. Guardar o actualizar inasistencia ---
                $inasistenciaExistente = Anexo04Inasistencia::where('id_persona', $persona->id)->first();
                $normalizeDetalle($p['inasistencia']);

                $nuevoInasistencia = $p['inasistencia'];
                $detalle = $nuevoInasistencia['detalle'] ?? [
                    'inasistencia' => [], 
                    'tardanza' => [], 
                    'permiso_sg' => [], 
                    'huelga' => []
                ];

                $inasistenciaData = [
                    'inasistencia_total' => $nuevoInasistencia['inasistencia_total'] ?? 0,
                    'huelga_total' => $nuevoInasistencia['huelga_total'] ?? 0,
                    'tardanza_total' => $normalizeHorasMinutos(
                        $nuevoInasistencia['tardanza_total']['horas'] ?? 0,
                        $nuevoInasistencia['tardanza_total']['minutos'] ?? 0
                    ),
                    'permiso_sg_total' => $normalizeHorasMinutos(
                        $nuevoInasistencia['permiso_sg_total']['horas'] ?? 0,
                        $nuevoInasistencia['permiso_sg_total']['minutos'] ?? 0
                    ),
                ];

                if ($inasistenciaExistente) {
                    $inasistenciaExistente->inasistencia = $inasistenciaData;
                    $inasistenciaExistente->detalle = $detalle;

                    $inasistenciaExistente->observacion = $p['observacion'] ?? null;
                    $inasistenciaExistente->save();
                } else {
                    Anexo04Inasistencia::create([
                        'id_persona' => $persona->id,
                        'inasistencia' => $inasistenciaData,
                        'detalle' => $detalle,
                        'observacion' => $p['observacion'] ?? null,
                    ]);

                }
            }

            DB::commit();
            return response()->json(['message' => 'Guardado correctamente.']);

        } catch (\Exception $e) {
            \Log::error('Error al guardar Anexo04 masivo: '.$e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Error al guardar: '.$e->getMessage()], 500);
        }
    }


    private function obtenerRegistrosInasistencia($id_contacto)
    {
            // Obtener los registros de contacto que estén asociados al id_contacto
            $contactos = Contacto::where('id_contacto', $id_contacto)->get();
            $registros = [];

            foreach ($contactos as $contacto) {
                // Obtener un único registro de inasistencia para este contacto (ajusta si puede haber varios)
                $inasistencia = Anexo04Inasistencia::where('id_contacto', $contacto->id_contacto)->first();

                // Decodificar el JSON del campo `inasistencia`
                $inasistenciaResumen = json_decode($inasistencia->inasistencia ?? '{}', true);

                // Agregar al array de registros
                $registros[] = (object)[
                    'dni' => $contacto->dni,
                    'nombres' => $contacto->nombres,
                    'cargo' => $contacto->cargo,
                    'condicion' => $contacto->condicion,
                    'jornada' => $contacto->jornada,
                    'inasistencias_dias' => $inasistenciaResumen['inasistencia_total'] ?? '',
                    'tardanzas_horas' => $inasistenciaResumen['tardanza_total']['horas'] ?? '',
                    'tardanzas_minutos' => $inasistenciaResumen['tardanza_total']['minutos'] ?? '',
                    'permisos_sg_horas' => $inasistenciaResumen['permiso_sg_total']['horas'] ?? '',
                    'permisos_sg_minutos' => $inasistenciaResumen['permiso_sg_total']['minutos'] ?? '',
                    'huelga_paro_dias' => $inasistenciaResumen['huelga_total'] ?? '',
                    'observaciones' => $inasistencia->observacion ?? '',
                ];
            }
        return $registros;
    }


    public function exportarInasistenciaPDFPreliminar(Request $request)
    {
        $director = session('siic01');
        $firmaBase64 = $request->input('firma_base64');
        
        if (!$director || empty($director['conf_permisos'][0]['codlocal'])) {
            return redirect()->back()->with('error', 'No se encontró la sesión del director o el codlocal.');
        }

        $codlocal = $director['conf_permisos'][0]['codlocal'];

        // Datos institución
        $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion','codmod')
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
        $logoBD = Iiee_a_evaluar_rie::where('codlocal', $codlocal)->where('codmod', $codmod)->value('logo');
        $nombreLogo = $logoBD ? basename($logoBD) : null;
        $rutaLogoWeb = $nombreLogo ? 'storage/logoie/' . $nombreLogo : null;

        $firmaGuardada = DB::connection('siic_anexos')->table('anexo03')
            ->where('id_contacto', $director['id_contacto'])
            ->value('firma');



        $hoy = new \DateTime(); // fecha actual
        $fecha = (clone $hoy)->modify('first day of last month'); // primer día del mes anterior

        $anio = (int) $fecha->format('Y');
        $mes  = (int) $fecha->format('n');

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
        // Personal de la IE
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


        $niveles = $personal->pluck('nivel')->unique()->sort()->values();

        // Traer registros con resumen y detalle
        $registros = Anexo04Inasistencia::whereHas('persona.anexo04', function($q) use ($mes, $anio, $codlocal) {
                $q->where('mes', $mes)
                ->where('anio', $anio)
                ->where('codlocal', $codlocal);
            })
            ->with(['persona' => function($q) {
                $q->select('id', 'persona_json');
            }])
            ->get();

        // Mapear datos inasistencias con detalle
        $datosInasistenciaPorDniCod = [];

        foreach ($registros as $r) {
            $persona_data = $r->persona->persona_json ?? '{}';
            $persona_decod = is_string($persona_data) ? json_decode($persona_data, true) : $persona_data;

            // Asegurar que sea arreglo
            if (!is_array($persona_decod)) continue;

            // Extraer solo los campos necesarios
            $dni = $persona_decod['dni'] ?? null;
            $cod = $persona_decod['cod'] ?? null;

            if (!$dni || !$cod) continue;

            $resumen = is_array($r->inasistencia) ? $r->inasistencia : json_decode($r->inasistencia, true);
            $detalle = is_array($r->detalle) ? $r->detalle : json_decode($r->detalle, true);

            // Si no hay datos válidos, se inicializa vacío
            $detalle = $detalle ?: [
                'inasistencia' => [],
                'tardanza' => [],
                'permiso_sg' => [],
                'huelga' => [],
            ];

            $datosInasistenciaPorDniCod["{$dni}_{$cod}"] = [
                'dni' => $dni,
                'cod' => $cod,
                'inasistencia_total' => $resumen['inasistencia_total'] ?? 0,
                'huelga_total' => $resumen['huelga_total'] ?? 0,
                'tardanza_total' => $resumen['tardanza_total'] ?? ['horas' => 0, 'minutos' => 0],
                'permiso_sg_total' => $resumen['permiso_sg_total'] ?? ['horas' => 0, 'minutos' => 0],

                // Detalles por fecha
                'inasistencia_fechas' => $detalle['inasistencia'] ?? [],
                'tardanza_fechas' => $detalle['tardanza'] ?? [],
                'permiso_sg_fechas' => $detalle['permiso_sg'] ?? [],
                'huelga_fechas' => $detalle['huelga'] ?? [],
            ];
        }
        
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);

        foreach ($niveles as $nivel) {
            $filtrados = $personal
                ->where('nivel', $nivel)
                ->map(function ($persona) use ($datosInasistenciaPorDniCod) {

                    $dni = $persona->dni;
                    $cod = $persona->cod ?? null; // Aseguramos que exista el código de plaza

                    // Clave combinada
                    $clave = "{$dni}_{$cod}";

                    $inasistencia = $datosInasistenciaPorDniCod[$clave] ?? null;

                    // Asignar totales (si existen)
                    $persona->inasistencias_dias = $inasistencia['inasistencia_total'] ?? 0;
                    $persona->huelga_paro_dias = $inasistencia['huelga_total'] ?? 0;
                    $persona->tardanzas_horas = $inasistencia['tardanza_total']['horas'] ?? 0;
                    $persona->tardanzas_minutos = $inasistencia['tardanza_total']['minutos'] ?? 0;
                    $persona->permisos_sg_horas = $inasistencia['permiso_sg_total']['horas'] ?? 0;
                    $persona->permisos_sg_minutos = $inasistencia['permiso_sg_total']['minutos'] ?? 0;

                    // Asignar detalles diarios
                    $persona->detalle_inasistencia = [
                        'inasistencia_fechas' => $inasistencia['inasistencia_fechas'] ?? [],
                        'tardanza_fechas' => $inasistencia['tardanza_fechas'] ?? [],
                        'permiso_sg_fechas' => $inasistencia['permiso_sg_fechas'] ?? [],
                        'huelga_fechas' => $inasistencia['huelga_fechas'] ?? [],
                    ];

                    return $persona;
                })
                ->sort(function ($a, $b) {
                    // Prioridad por jerarquía del cargo
                    $prioridadCargo = function ($cargo) {
                        $cargo = strtoupper(trim($cargo));
                        if (Str::startsWith($cargo, 'DIRECTOR')) return 1;
                        if (Str::startsWith($cargo, 'SUB-DIRECTOR')) return 2;
                        if (Str::startsWith($cargo, 'JEFE')) return 3;
                        if (Str::startsWith($cargo, 'COORDINADOR')) return 4;
                        if (Str::startsWith($cargo, 'PROFESOR')) return 5;
                        if (Str::startsWith($cargo, 'AUXILIAR')) return 6;
                        return 99;
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

                    $ja = $a->jornada ?? 0;
                    $jb = $b->jornada ?? 0;
                    if ($ja !== $jb) return $jb <=> $ja;

                    return strcmp($a->nombres, $b->nombres);
                })
                ->values();

            $data = [
                'registros' => $filtrados,
                'nivelSeleccionado' => $nivel,
                'modalidad' => $institucion->modalidad ?? '',
                'institucion' => $institucion->institucion ?? '',
                'anio' => $anio,
                'mes' => $mes,
                'codlocal' => $codlocal,
                'd_cod_tur' => $d_cod_tur,
                'logo' => $rutaLogoWeb,
                'firmaBase64' => $firmaBase64,
                'firmaGuardada' => $firmaGuardada,
                'datos_inasistencias' => $datosInasistenciaPorDniCod,
            ];

            $html = View::make('reporteAnexo04.formulario04_pdfpreliminar', $data)->render();
            $mpdf->AddPage('L');
            $mpdf->WriteHTML($html);
        }
        
        return response($mpdf->Output('reporte_inasistencia_preliminar.pdf', 'I'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    
    public function exportarInasistenciaPDF(Request $request)
    {
            $director = session('siic01');
            $numeroOficio = $request->input('numero_oficio');
            $firmaBase64 = $request->input('firma_base64');

            if (!$director || empty($director['conf_permisos'][0]['codlocal'])) {
                return redirect()->back()->with('error', 'No se encontró la sesión del director o el codlocal.');
            }

            $codlocal = $director['conf_permisos'][0]['codlocal'];

            // Obtener datos de la institución
            $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion', 'direccion_ie', 'distrito' , 'codmod')
                ->where('codlocal', $codlocal)
                ->where('dni_director', $director['dni'])
                ->first();

            // Obtener director UGEL 01
            $directorUgel = DB::table('nexus')
                ->select('nombres', 'apellipat', 'apellimat')
                ->where('descargo', 'DIRECTOR DE UNIDAD DE GESTIÓN EDUCATIVA LOCAL')
                ->where('nexus.estado', 1)
                ->first();

            // Obetenr localidad 
            $localidad= DB::table('escale')
                ->select('localidad')
                ->where('codlocal', $codlocal)
                ->value('localidad');
            // Ya tienes $institucion en este punto
            $codmod1 = $institucion->codmod ?? null;
            
            // Obetenr turno 
            $d_cod_tur= Iiee_a_evaluar_rie::select('turno')
                ->where('codlocal', $codlocal)
                ->where('codmod', $codmod1)
                ->value('turno'); 
            
            // Obetenr nombre_anio 
            $anioActual = now()->year;

            $nombreAnio = DB::table('nombre_anio')
                ->where('anio',$anioActual)
                ->value('nombre');

            //obtener resolucion
            $resolucion = Iiee_a_evaluar_rie::select('nro_rdcreacion')
                ->where('codlocal', $codlocal)
                ->value('nro_rdcreacion');
            
            //obtener codmodular        
            $codmod = Iiee_a_evaluar_rie::select('codmod')
                ->where('codlocal', $codlocal)
                ->value('codmod');

            //obtener resolucion
            $resolucion = Iiee_a_evaluar_rie::select('nro_rdcreacion')
                ->where('codlocal', $codlocal)
                ->value('nro_rdcreacion');
            //obtener codmods
            $codmodulares = Iiee_a_evaluar_rie::select('nivel', 'codmod')
                ->where('codlocal', $codlocal)
                ->get();
            
            // Obtener logo 
            $logo= Iiee_a_evaluar_rie::select('logo')
                ->where('codlocal', $codlocal)
                ->where('codmod', $codmod1)
                ->value('logo');

            $logoBD = Iiee_a_evaluar_rie::where('codlocal', $codlocal)
                ->where('codmod', $codmod1)
                ->value('logo'); // Esto puede ser null o un string

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

            $niveles = $personal->pluck('nivel')->unique()->sort()->values();

        // Nivel seleccionado
            $nivelSeleccionado = $niveles;
                if (is_iterable($nivelSeleccionado)) {
                    // Si hay varios niveles (colección o array)
                    $filtrados = $personal->whereIn('nivel', $nivelSeleccionado)->values();
                } else {
                    // Si es un solo nivel
                    $filtrados = $personal->where('nivel', $nivelSeleccionado)->values();
                }

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
                    $hoy = new \DateTime(); // fecha actual
                    $fecha = (clone $hoy)->modify('first day of last month'); // primer día del mes anterior

                    $anio = (int) $fecha->format('Y');
                    $mes  = (int) $fecha->format('n');


            // Obtener todos los registros anexo04 que coincidan con el filtro
            $anexos04 = DB::connection('siic_anexos')->table('anexo04')
                ->where('codlocal', $codlocal)
                ->where('anio', $anio)
                ->where('mes', $mes)
                ->where('id_contacto', $director['id_contacto'])
                ->get();

            $idsAnexo04 = $anexos04->pluck('id')->all();

            $personasAnexo04 = DB::connection('siic_anexos')->table('anexo04_persona as ap')
                ->join('anexo04 as a', 'a.id', '=', 'ap.id_anexo04')
                ->where('a.codlocal', $codlocal)
                ->select('ap.id', 'ap.persona_json', 'a.fecha_creacion') 
                ->get()
                ->mapWithKeys(function ($item) {
                    $persona = json_decode($item->persona_json);
                    $dni = $persona->dni ?? null;
                    $codplaza = $persona->cod ?? null;
                    $clave = $codplaza ? "{$dni}_{$codplaza}" : $dni;

                    return [$clave => [
                        'id' => $item->id,
                        'fecha_creacion' => $item->fecha_creacion
                    ]];
                });
            

            $idPersonas = collect($personasAnexo04)->pluck('id')->all();

            $inasistencias = DB::connection('siic_anexos')->table('anexo04_inasistencia')
                ->whereIn('id_persona', $idPersonas) // <- ahora sí es un array plano
                ->get()
                ->mapWithKeys(function ($item) {
                    $totales = json_decode($item->inasistencia, true);
                    if (is_string($totales)) {
                        $totales = json_decode($totales, true);
                    }

                    $detalle = json_decode($item->detalle, true);
                    if (is_string($detalle)) {
                        $detalle = json_decode($detalle, true);
                    }

                    return [$item->id_persona => [
                        'totales' => $totales,
                        'detalle' => $detalle
                    ]];
                });

            $datosInasistenciaPorPersona = [];

            foreach ($personasAnexo04 as $clave => $infoPersona) {
                $id = $infoPersona['id'];

                $inasistencia = $inasistencias[$id] ?? null;

                if ($inasistencia) {
                    $datosInasistenciaPorPersona[$clave] = [
                        'inasistencia_total' => $inasistencia['totales']['inasistencia_total'] ?? 0,
                        'huelga_total' => $inasistencia['totales']['huelga_total'] ?? 0,
                        'tardanza_total' => [
                            'horas' => $inasistencia['totales']['tardanza_total']['horas'] ?? 0,
                            'minutos' => $inasistencia['totales']['tardanza_total']['minutos'] ?? 0,
                        ],
                        'permiso_sg_total' => [
                            'horas' => $inasistencia['totales']['permiso_sg_total']['horas'] ?? 0,
                            'minutos' => $inasistencia['totales']['permiso_sg_total']['minutos'] ?? 0,
                        ],
                    ];
                } else {
                    $datosInasistenciaPorPersona[$clave] = [
                        'inasistencia_total' => 0,
                        'huelga_total' => 0,
                        'tardanza_total' => ['horas' => 0, 'minutos' => 0],
                        'permiso_sg_total' => ['horas' => 0, 'minutos' => 0],
                    ];
                }
            }
            
            //dd($datosInasistenciaPorPersona);
                $filtrados = $filtrados->map(function ($persona) use ($datosInasistenciaPorPersona) {
                    $dni = $persona->dni ?? null;
                    $cod = $persona->cod ?? null;

                    // misma clave que usaste antes
                    $clave = $cod ? "{$dni}_{$cod}" : $dni;

                    $inasistencia = $datosInasistenciaPorPersona[$clave] ?? null;

                    if ($inasistencia) {
                        $persona->inasistencias_dias   = $inasistencia['inasistencia_total'] ?? 0;
                        $persona->huelga_paro_dias     = $inasistencia['huelga_total'] ?? 0;
                        $persona->tardanzas_horas      = $inasistencia['tardanza_total']['horas'] ?? 0;
                        $persona->tardanzas_minutos    = $inasistencia['tardanza_total']['minutos'] ?? 0;
                        $persona->permisos_sg_horas    = $inasistencia['permiso_sg_total']['horas'] ?? 0;
                        $persona->permisos_sg_minutos  = $inasistencia['permiso_sg_total']['minutos'] ?? 0;
                    } else {
                        $persona->inasistencias_dias   = 0;
                        $persona->huelga_paro_dias     = 0;
                        $persona->tardanzas_horas      = 0;
                        $persona->tardanzas_minutos    = 0;
                        $persona->permisos_sg_horas    = 0;
                        $persona->permisos_sg_minutos  = 0;
                    }

                    return $persona;
                });
                //dd($filtrados);
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
                    'firmaBase64' => $firmaBase64,
                    'firmaGuardada' => $firmaGuardada,
                    'logo' => $rutaLogoWeb,
                    'codmod' => $codmod,
                    'resolucion' => $resolucion,
                    'codmodulares' => $codmodulares,
                    'resolucion' => $resolucion,
                    'inasistencias' => $inasistencias,
                    'datos_inasistencias'=>$datosInasistenciaPorPersona,
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
                    'codlocal' => $codlocal,
                    'nombreAnio' =>$nombreAnio,
                    'logo' => $logo,
                    'numeroOficio' => $numeroOficio,
                    'firmaBase64' => $firmaBase64,
                    'firmaGuardada' => $firmaGuardada,
                    'logo' => $rutaLogoWeb,
                    'codmod' => $codmod,
                    'resolucion' => $resolucion,
                    'codmodulares' => $codmodulares,
                    'resolucion' => $resolucion,
                ];

            // Renderizar vista del Oficio (primera página vertical)
            $htmlOficio = View::make('reporteAnexo04.formulario04_oficio', $datosOficio)->render();

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
                R<br>S<br>G<br>-<br>3<br>2<br>6<br>-<br>2<br>0<br>1<br>7<br>-<br>M<br>I<br>N<br>E<br>D<br>U
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
                    'firmaBase64' => $firmaBase64,
                    'firmaGuardada' => $firmaGuardada,
                    'inasistencias'=> $inasistencias,
                    'datos_inasistencias'=> $datosInasistenciaPorPersona,
                ];

                // Renderizar vista del reporte horizontal
                $htmlReporte = View::make('reporteAnexo04.formulario04_pdf', $data)->render();

                // Establecer encabezado ANTES de agregar la nueva página
                $mpdf->SetHTMLHeader($headerVertical);
                $mpdf->AddPage('L');
                $mpdf->WriteHTML($htmlReporte);
            }
                
            $pdfContent = $mpdf->Output('', 'S');

            $nombreArchivo = 'anexos04/reporte_'.$codlocal.'_'.date('Ymd_His').'.pdf';

            // Guardar en storage/app/public/anexos04
            Storage::disk('public')->put('anexos04/'.$nombreArchivo, $pdfContent);

            DB::connection('siic_anexos')->table('anexo04')
                ->where('codlocal', $codlocal)
                ->update([
                    'ruta_pdf' => 'anexos04/'.$nombreArchivo,
                    'fecha_actualizacion' => now()
                ]);

            // Descargar el PDF generado
            return response($mpdf->Output('reporte_inasistencia_con_oficio.pdf', 'I'), 200)
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