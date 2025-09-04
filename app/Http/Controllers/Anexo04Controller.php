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

        $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion')
            ->where('codlocal', $codlocal)
            ->where('dni_director', $director['dni'])
            ->first();

        // Obtener turno (corregido)
        $d_cod_tur = DB::table('escale')
            ->where('codlocal', $codlocal)
            ->value('d_cod_tur');

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

            $fecha = new \DateTime('first day of last month');
            $anio = (int) $fecha->format('Y');
            $mes = (int) $fecha->format('n');

        // Obtener todos los registros anexo04 que coincidan con el filtro
        $anexos04 = DB::connection('siic_anexos')->table('anexo04')
            ->where('codlocal', $codlocal)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('id_contacto', $director['id_contacto'])
            ->get();

        $idsAnexo04 = $anexos04->pluck('id')->all();

        $personasAnexo04 = DB::connection('siic_anexos')->table('anexo04_persona')
            ->whereIn('id_anexo04', $idsAnexo04)
            ->get()
            ->mapWithKeys(function ($item) {
                
                $json = json_decode($item->persona_json);

                if (is_string($json)) {
                    $json = json_decode($json);
                }

                if (!is_object($json) || !isset($json->dni)) {
                    dd('Error en persona_json', $item->persona_json, $json);
                }

                return [$json->dni => $item->id];
            });

        $inasistencias = DB::connection('siic_anexos')->table('anexo04_inasistencia')
            ->whereIn('id_persona', $personasAnexo04->values()->all())
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
        

        $datosInasistenciaPorDni = [];

        foreach ($personasAnexo04 as $dni => $id_persona) {
            $inasistencia = $inasistencias[$id_persona] ?? null;

            if ($inasistencia) {
                $datosInasistenciaPorDni[$dni] = [
                    'inasistencia_total' => $inasistencia['inasistencia_total'] ?? 0,
                    'huelga_total' => $inasistencia['huelga_total'] ?? 0,
                    'tardanza_total' => [
                        'horas' => $inasistencia['tardanza_total']['horas'] ?? 0,
                        'minutos' => $inasistencia['tardanza_total']['minutos'] ?? 0,
                    ],
                    'permiso_sg_total' => [
                        'horas' => $inasistencia['permiso_sg_total']['horas'] ?? 0,
                        'minutos' => $inasistencia['permiso_sg_total']['minutos'] ?? 0,
                    ],
                ];
            } else {
                $datosInasistenciaPorDni[$dni] = [
                    'inasistencia_total' => 0,
                    'huelga_total' => 0,
                    'tardanza_total' => ['horas' => 0, 'minutos' => 0],
                    'permiso_sg_total' => ['horas' => 0, 'minutos' => 0],
                ];
            }
        }

        // Combinar con personas filtradas
       $filtrados = $filtrados->map(function ($persona) use ($datosInasistenciaPorDni, $inasistencias, $personasAnexo04) {
            $dni = $persona->dni;
            $id_persona = $personasAnexo04[$dni] ?? null;

            if ($id_persona && isset($inasistencias[$id_persona]['totales'])) {
                $tot = $inasistencias[$id_persona]['totales'];
                $persona->inasistencias_dias = $tot['inasistencia_total'] ?? 0;
                $persona->huelga_paro_dias = $tot['huelga_total'] ?? 0;
                $persona->tardanzas_horas = $tot['tardanza_total']['horas'] ?? 0;
                $persona->tardanzas_minutos = $tot['tardanza_total']['minutos'] ?? 0;
                $persona->permisos_sg_horas = $tot['permiso_sg_total']['horas'] ?? 0;
                $persona->permisos_sg_minutos = $tot['permiso_sg_total']['minutos'] ?? 0;
            }

            // Pasar el detalle al Blade
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
                'inasistencias' => $datosInasistenciaPorDni,

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
                $borrador = Anexo04::where('codlocal', $request->codlocal)
                    ->where('nivel', $request->nivel)
                    ->where('mes', $request->mes)
                    ->where('anio', $request->anio)
                    ->whereNull('oficio')
                    ->first();

                if ($borrador) {
                    $borrador->oficio = $request->numero_oficio;
                    $borrador->expediente = $request->numero_expediente;
                    $borrador->fecha_actualizacion = now();
                    $borrador->save();
                }

                $anexo = Anexo04::create([
                    'id_contacto' => session('siic01')['id_contacto'],
                    'codlocal' => $request->codlocal,
                    'nivel' => $request->nivel,
                    'mes' => $request->mes,
                    'anio' => $request->anio,
                    'oficio' => null,
                    'expediente' => null,
                    'fecha_creacion' => now(),
                    'fecha_actualizacion' => now(),
                ]);

            } else {
                $anexo = Anexo04::firstOrCreate(
                    [
                        'codlocal' => $request->codlocal,
                        'nivel' => $request->nivel,
                        'mes' => $request->mes,
                        'anio' => $request->anio,
                        'oficio' => null,
                    ],
                    [
                        'id_contacto' => session('siic01')['id_contacto'],
                        'fecha_creacion' => now(),
                        'fecha_actualizacion' => now(),
                    ]
                );
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
                if (isset($data['dni'])) {
                    $personasMap[$data['dni']] = $pe;
                }
            }

            // --- 4. Recorrer personas del request ---
            foreach ($request->personas as $p) {
                $dni = $p['persona']['dni'];
                $persona_json = $p['persona']; 


                // Buscar persona por DNI
                if (isset($personasMap[$dni])) {
                    $persona = $personasMap[$dni];
                    $persona->persona_json = $persona_json;
                    $persona->save();
                } else {
                    $persona = Anexo04Persona::create([
                        'id_anexo04' => $anexo->id,
                        'persona_json' => $persona_json,
                    ]);
                    $personasMap[$dni] = $persona;
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
        $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion')
            ->where('codlocal', $codlocal)
            ->where('dni_director', $director['dni'])
            ->first();
        $d_cod_tur = DB::table('escale')->where('codlocal', $codlocal)->value('d_cod_tur');
        $logoBD = Iiee_a_evaluar_rie::where('codlocal', $codlocal)->value('logo');
        $nombreLogo = $logoBD ? basename($logoBD) : null;
        $rutaLogoWeb = $nombreLogo ? 'storage/logoie/' . $nombreLogo : null;

        $firmaGuardada = DB::connection('siic_anexos')->table('anexo03')
            ->where('id_contacto', $director['id_contacto'])
            ->value('firma');

        $anio = now()->subMonth()->year;
        $mes = now()->subMonth()->month;
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
        $personal = DB::table('nexus')
            ->select(
                'nexus.numdocum as dni',
                DB::raw("CONCAT(nexus.apellipat, ' ', nexus.apellimat, ', ', nexus.nombres) as nombres"),
                'nexus.descargo as cargo',
                'nexus.situacion as condicion',
                'nexus.jornlab as jornada',
                'nexus.descniveduc as nivel'
            )
            ->where('nexus.codlocal', $codlocal)
            ->whereIn('nexus.idnivel', $idnivelesModalidad)
            ->whereNotNull('nexus.numdocum')
            ->where('nexus.numdocum', '!=', 'VACANTE')
            ->where('nexus.situacion', '!=', 'VACANTE')
            ->where('nexus.estado', 1)
            ->get();

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
        $datosInasistenciaPorDni = [];
        foreach ($registros as $r) {
            $persona_data = $r->persona->persona_json ?? [];

            $dni = $persona_data['dni'] ?? null;
            if (!$dni) continue;

            $resumen = $r->inasistencia ?? [];
            $detalle = $r->detalle ?? [
                'inasistencia' => [],
                'tardanza' => [],
                'permiso_sg' => [],
                'huelga' => [],
            ];


            $datosInasistenciaPorDni[$dni] = [
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
        // $headerVertical = '
        //     <div style="position: fixed; top: 18%; right: 0; transform: translateY(-50%) rotate(180deg);
        //         writing-mode: vertical-rl; text-orientation: mixed; width: 40px; font-weight: bold;
        //         font-size: 23px; line-height: 1.2; color: #999; text-align: center;">
        //         R<br>S<br>G<br>-<br>3<br>2<br>6<br>-<br>2<br>0<br>1<br>7<br>-<br>M<br>I<br>N<br>E<br>D<br>U
        //     </div>';

        foreach ($niveles as $nivel) {
            $filtrados = $personal
                ->where('nivel', $nivel)
                ->map(function ($persona) use ($datosInasistenciaPorDni) {
                    $dni = $persona->dni;
                    $inasistencia = $datosInasistenciaPorDni[$dni] ?? null;

                    $persona->inasistencias_dias = $inasistencia['inasistencia_total'] ?? 0;
                    $persona->huelga_paro_dias = $inasistencia['huelga_total'] ?? 0;
                    $persona->tardanzas_horas = $inasistencia['tardanza_total']['horas'] ?? 0;
                    $persona->tardanzas_minutos = $inasistencia['tardanza_total']['minutos'] ?? 0;
                    $persona->permisos_sg_horas = $inasistencia['permiso_sg_total']['horas'] ?? 0;
                    $persona->permisos_sg_minutos = $inasistencia['permiso_sg_total']['minutos'] ?? 0;

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
                'datos_inasistencias' => $datosInasistenciaPorDni,
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
            $institucion = Iiee_a_evaluar_rie::select('idmodalidad','modalidad', 'institucion', 'direccion_ie', 'distrito')
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

            // Obetenr turno 
            $d_cod_tur= DB::table('escale')
                ->select('d_cod_tur')
                ->where('codlocal', $codlocal)
                ->value('d_cod_tur');

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
            
            // Obetenr logo 
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
                ->where('nexus.numdocum', '!=', 'VACANTE')
                ->where('nexus.situacion', '!=', 'VACANTE')
                ->where('nexus.estado', 1)
                ->get();

            

            $niveles = $personal->pluck('nivel')->unique()->sort()->values();


        // Nivel seleccionado
            $nivelSeleccionado = $niveles;
                

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
                    $fecha = new \DateTime('first day of last month');
                    $anio = (int) $fecha->format('Y');
                    $mes = (int) $fecha->format('n');

            // Obtener todos los registros anexo04 que coincidan con el filtro
            $anexos04 = DB::connection('siic_anexos')->table('anexo04')
                ->where('codlocal', $codlocal)
                ->where('anio', $anio)
                ->where('mes', $mes)
                ->where('id_contacto', $director['id_contacto'])
                ->get();

            $idsAnexo04 = $anexos04->pluck('id')->all();

            $personasAnexo04 = DB::connection('siic_anexos')->table('anexo04_persona')
                ->whereIn('id_anexo04', $idsAnexo04)
                ->get()
                ->mapWithKeys(function ($item) {
                    // Primera decodificación
                    $json = json_decode($item->persona_json);

                    // Si el resultado aún es un string, hacer segunda decodificación
                    if (is_string($json)) {
                        $json = json_decode($json);
                    }

                    if (!is_object($json) || !isset($json->dni)) {
                        dd('Error en persona_json', $item->persona_json, $json);
                    }

                    return [$json->dni => $item->id];
                });

            $inasistencias = DB::connection('siic_anexos')->table('anexo04_inasistencia')
                ->whereIn('id_persona', $personasAnexo04->values()->all())
                ->get()
                ->mapWithKeys(function ($item) {
                    $decoded = json_decode($item->inasistencia, true);
                    // Si $decoded sigue siendo string (JSON anidado), decodifica otra vez
                    if (is_string($decoded)) {
                        $decoded = json_decode($decoded, true);
                    }
                    return [$item->id_persona => $decoded];
                });

            $datosInasistenciaPorDni = [];

            foreach ($personasAnexo04 as $dni => $id_persona) {
                $inasistencia = $inasistencias[$id_persona] ?? null;

                if ($inasistencia) {
                    $datosInasistenciaPorDni[$dni] = [
                        'inasistencia_total' => $inasistencia['inasistencia_total'] ?? 0,
                        'huelga_total' => $inasistencia['huelga_total'] ?? 0,
                        'tardanza_total' => [
                            'horas' => $inasistencia['tardanza_total']['horas'] ?? 0,
                            'minutos' => $inasistencia['tardanza_total']['minutos'] ?? 0,
                        ],
                        'permiso_sg_total' => [
                            'horas' => $inasistencia['permiso_sg_total']['horas'] ?? 0,
                            'minutos' => $inasistencia['permiso_sg_total']['minutos'] ?? 0,
                        ],
                    ];
                } else {
                    $datosInasistenciaPorDni[$dni] = [
                        'inasistencia_total' => 0,
                        'huelga_total' => 0,
                        'tardanza_total' => ['horas' => 0, 'minutos' => 0],
                        'permiso_sg_total' => ['horas' => 0, 'minutos' => 0],
                    ];
                }
            }
            
            // Combinar con personas filtradas
            $filtrados = $filtrados->map(function ($persona) use ($datosInasistenciaPorDni) {
                $dni = $persona->dni;
                $inasistencia = $datosInasistenciaPorDni[$dni] ?? null;

                $persona->inasistencias_dias = $inasistencia['inasistencia_total'] ?? 0;
                $persona->huelga_paro_dias = $inasistencia['huelga_total'] ?? 0;

                $persona->tardanzas_horas = $inasistencia['tardanza_total']['horas'] ?? 0;
                $persona->tardanzas_minutos = $inasistencia['tardanza_total']['minutos'] ?? 0;

                $persona->permisos_sg_horas = $inasistencia['permiso_sg_total']['horas'] ?? 0;
                $persona->permisos_sg_minutos = $inasistencia['permiso_sg_total']['minutos'] ?? 0;

                return $persona;
                
            });

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
                    'datos_inasistencias'=>$datosInasistenciaPorDni,
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
                    'datos_inasistencias'=> $datosInasistenciaPorDni,
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