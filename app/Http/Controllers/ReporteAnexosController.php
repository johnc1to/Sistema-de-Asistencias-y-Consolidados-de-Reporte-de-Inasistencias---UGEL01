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
use Illuminate\Support\Arr;
use App\Exports\ObservacionesCriticasExport;

use DB;
use PDF;

use App\Models\Anexo04Persona;
use App\Models\Anexo04;
use App\Models\Iiee_a_evaluar_rie;


class ReporteAnexosController extends Controller
{
    public function mostrarReporteAnexos03(Request $request)
    {
        if (!session()->get('siic01_admin')) {
            return response('Sesión terminada', 401);
        }

        $session = session()->get('siic01_admin');

        // Filtros
        $filtroDistrito = $request->input('distrito');
        $filtroInstitucion = $request->input('institucion');
        $filtroNivel = $request->input('nivel');

        // Obtener todos los reportes desde siic01ugel01gob_anexos
        $reportes = DB::connection('siic_anexos')->table('anexo03')
            ->when($filtroNivel, fn($q) => $q->where('nivel', $filtroNivel))
            ->whereNotNull('oficio')
            ->where('oficio', '!=', '')
            ->whereNotNull('expediente')
            ->where('expediente', '!=', '')
            ->get();

        // Obtener codlocal únicos
        $codlocales = $reportes->pluck('codlocal')->unique();

        // Obtener información de instituciones desde otra base (ej: siic_2024)
        $iiees = Iiee_a_evaluar_rie::whereIn('codlocal', $codlocales)
            ->get()
            ->keyBy('codlocal');
        //dd($iiees);
        // Obtener coordenadas desde la tabla escale
        $coordenadas = DB::table('escale')
            ->whereIn('codlocal', $codlocales)
            ->select('codlocal', 'nlat_ie', 'nlong_ie')
            ->get()
            ->map(function ($item) {
                $item->nlat_ie = floatval(str_replace(',', '.', $item->nlat_ie));
                $item->nlong_ie = floatval(str_replace(',', '.', $item->nlong_ie));
                return $item;
            })
            ->keyBy('codlocal');

        // Filtrar en memoria según los filtros del request (distrito e institución)
        $reportes = $reportes->filter(function ($reporte) use ($iiees, $filtroDistrito, $filtroInstitucion) {
            $iiee = $iiees[$reporte->codlocal] ?? null;

            if (!$iiee) return false;

            $coincideDistrito = !$filtroDistrito || $iiee->distrito === $filtroDistrito;
            $coincideInstitucion = !$filtroInstitucion || 
            str_contains(mb_strtolower(trim($iiee->institucion)), mb_strtolower(trim($filtroInstitucion)));

            return $coincideDistrito && $coincideInstitucion;
        });

        // Obtener contactos por id_contacto
        $idsContacto = $reportes->pluck('id_contacto')->unique()->filter();
        $contactos = DB::table('contacto')
            ->whereIn('id_contacto', $idsContacto)
            ->get()
            ->keyBy('id_contacto');

        // Enriquecer cada reporte con datos de contacto e institución
        $reportes = $reportes->map(function ($reporte) use ($contactos, $iiees, $coordenadas) {
            $contacto = $contactos[$reporte->id_contacto] ?? null;
            $iiee = $iiees[$reporte->codlocal] ?? null;
            $coord = $coordenadas[$reporte->codlocal] ?? null;

            $reporte->dni = $contacto->dni ?? null;
            $reporte->nombres = $contacto->nombres ?? null;
            $reporte->apellipat = $contacto->apellipat ?? null;
            $reporte->apellimat = $contacto->apellimat ?? null;
            $reporte->celular_pers = $contacto->celular_pers ?? null;

            $reporte->institucion = $iiee->institucion ?? null;
            $reporte->codmod = $iiee->codmod ?? null;
            $reporte->correo_inst = $iiee->correo_inst ?? null;
            $reporte->distrito = $iiee->distrito ?? null;

            // Coordenadas para Leaflet
            $reporte->latitud = $coord->nlat_ie ?? null;
            $reporte->longitud = $coord->nlong_ie ?? null;

            return $reporte;
        });

        // Listas para filtros (extraídas desde la tabla de instituciones)
        $distritos = Iiee_a_evaluar_rie::distinct()->pluck('distrito')->sort()->values();
        $instituciones = Iiee_a_evaluar_rie::select('codmod', 'institucion')->distinct()->orderBy('institucion')->get();
        $niveles = DB::connection('siic_anexos')->table('anexo03')->distinct()->pluck('nivel')->sort()->values();

        // Reporte de directores sin anexo03
        $anexo03Ids = DB::connection('siic_anexos')->table('anexo03')->select('id_contacto');

        $anioMes = $request->input('anioMes') ?? now()->format('Y-m');

        $query = DB::table('iiee_a_evaluar_rie as R')
            ->select(
                'R.codlocal',
                'R.red',
                DB::raw("GROUP_CONCAT(DISTINCT R.institucion SEPARATOR '-') as institucion"),
                DB::raw("IFNULL(MAX(ANEXO03.situacion),'NO REGISTRO') as Anexo03"),
                DB::raw("IFNULL(MAX(ANEXO03.ruta_pdf),'') as ruta_pdf"),
                DB::raw("IFNULL(MAX(ANEXO03.expediente),'') as expediente"),
                'R.modalidad',
                DB::raw("GROUP_CONCAT(DISTINCT R.nivel SEPARATOR '-') as nivel"),
                DB::raw("GROUP_CONCAT(DISTINCT R.codmod SEPARATOR '-') as codmod"),
                'R.distrito',
                'R.descgestie',
                'R.dni_director',
                'R.director',
                'C.celular_pers',
                'R.correo_inst',
                'C.correo_pers',
            )
            ->leftJoin(DB::raw("
                (
                    SELECT 
                        codlocal,
                        IF(
                            nivel IN('Inicial - Jardín','Primaria','Inicial - Cuna-jardín','Secundaria'),'EBR',
                            IF(
                                nivel IN('Básica Especial-Inicial','Básica Especial-Primaria','Básica Especial'),'EBE',
                                IF(nivel='ETP','ETP',
                                    IF(nivel IN('Básica Alternativa-Avanzado','Básica Alternativa-Inicial e Intermedio'),'EBA','')
                                )
                            )
                        ) as modalidad,
                        IF(expediente IS NULL,'EN PROCESO','ENVIADO') as situacion,
                        ruta_pdf,  
                        expediente
                    FROM siic01ugel01gob_anexos.anexo03
                    WHERE fecha_creacion LIKE '%$anioMes%'
                ) ANEXO03
            "), function ($join) {
                $join->on('R.codlocal','=','ANEXO03.codlocal')
                    ->on('R.modalidad','=','ANEXO03.modalidad');
            })
            ->leftJoin('contacto as C','R.id_contacto_dir','=','C.id_contacto')
            ->where('R.estado',1)
            ->where('R.cant_plazas_nexus','>',0)
            ->groupBy(
                'R.codlocal',
                'R.red',
                'R.modalidad',
                'R.distrito',
                'R.descgestie',
                'R.dni_director',
                'R.director',
                'C.celular_pers',
                'R.correo_inst',
                'C.correo_pers'
            )
            ->get();


        // Agrupamos según la situación
        $directoresSinAnexo03 = $query->where('Anexo03', 'NO REGISTRO');
        $directoresEnProceso  = $query->where('Anexo03', 'EN PROCESO');
        $directoresCompletos  = $query->where('Anexo03', 'ENVIADO');

        // 1. Observaciones críticas del Anexo 03
        $observacionesCriticas = DB::connection('siic_anexos')
            ->table('anexo03_asistencia as a')
            ->join('anexo03_persona as p', 'a.id_persona', '=', 'p.id')
            ->join('anexo03 as x', 'p.id_anexo03', '=', 'x.id_anexo03')
            ->select(
                'a.tipo_observacion',
                'a.observacion',
                'a.observacion_detalle',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.dni')) as dni"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.nombres')) as nombres"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.cargo')) as cargo"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.condicion')) as condicion"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.cod')) as codplaza"),
                'x.nivel',
                'x.codlocal',
                'x.id_contacto', 
                'x.fecha_creacion',
                'x.oficio',
                'x.expediente'
            )
            ->whereIn(DB::raw('UPPER(a.tipo_observacion)'), [
                'INASISTENCIAJUSTIFICADA', 'ABANDONOCARGO', 'VACACIONES',
                'LICENCIA', 'CESE','PERMISOSINGOCE'
            ])
            ->whereNotNull('x.oficio')->where('x.oficio','!=','')
            ->whereNotNull('x.expediente')->where('x.expediente','!=','')
            ->where('x.fecha_creacion', 'like', $anioMes . '%')   // <-- filtro por mes
            ->get();

        // 2. Docentes Nexus activos
        $docentesNexus = DB::table('nexus')
            ->select('numdocum as dni', 'codlocal', 'desctipotrab', 'estado', 'descniveduc as nivel')
            ->where('desctipotrab', 'DOCENTE')
            ->where('estado', 1)
            ->get();

        // 3. Datos de IE
        $iiee = DB::table('iiee')
            ->select('cod_local', 'nombre_iiee', 'distrito')
            ->get()
            ->keyBy('cod_local');
        

        $observacionesCriticas->transform(function ($item) use ($contactos, $iiees, $coordenadas) {
            // Agrega datos de contacto
            $contacto = $contactos[$item->id_contacto] ?? null;
            $item->distrito_contacto = $contacto?->distrito ?? null;
            $item->red_contacto = $contacto?->red ?? null;

            // Agrega datos de IIEE
            $iiee = $iiees[$item->codlocal] ?? null;
            $item->institucion = $iiee?->institucion ?? null;
            $item->distrito_iiee = $iiee?->distrito ?? null;
            $item->red_iiee = $iiee?->red ?? null;

            $coord = $coordenadas[$item->codlocal] ?? null;
            $item->latitud = $coord->nlat_ie ?? null;
            $item->longitud = $coord->nlong_ie ?? null;
            return $item;
        });

        // 4. Niveles a evaluar
        $iieeEvaluar = Iiee_a_evaluar_rie::select('codlocal', 'nivel')
            ->get()
            ->groupBy('codlocal');
        
        // 5. Agrupamos docentes Nexus por colegio
        $resultado = $docentesNexus
            ->groupBy('codlocal')
            ->map(function ($docentes, $codlocal) use ($iiee, $iieeEvaluar) {
                $ie   = $iiee[$codlocal] ?? null;
                $niveles = $iieeEvaluar[$codlocal] ?? collect();

                $totalDocentes = $docentes->count();

                $porNivel = $niveles->mapWithKeys(function ($nivelObj) use ($docentes) {
                    $conteo = $docentes->where('nivel', $nivelObj->nivel)->count();
                    return [$nivelObj->nivel => $conteo];
                });

                return [
                    'codlocal'        => $codlocal,
                    'nombre_ie'       => $ie->nombre_iiee ?? '',
                    'distrito'        => $ie->distrito ?? '',
                    'total_docentes'  => $totalDocentes,
                    'docentes_por_nivel' => $porNivel,
                ];
            });

        // 6. Agrupamos docentes con observaciones críticas por colegio
        $observacionesPorColegio = $observacionesCriticas
            ->groupBy('codlocal')
            ->map(fn($items) => collect($items)->pluck('dni')->unique()->count());

        // 7. Calculamos cumplimiento (con observaciones críticas)
        $cumplimiento = $resultado->map(function($item) use ($observacionesPorColegio) {
            $codlocal = $item['codlocal'];
            $total = $item['total_docentes'];
            $conObservacion = $observacionesPorColegio[$codlocal] ?? 0;

            $porcentaje = $total > 0
                ? round((($total - $conObservacion) / $total) * 100, 2)
                : 0;

            return array_merge($item, [
                'docentes_con_observacion' => $conObservacion,
                'cumplimiento' => $porcentaje,
            ]);
        })->values();
        // =========================
        //  MES ANTERIOR
        // =========================
        $anioMesAnterior = now()->subMonth()->format('Y-m'); // Ej. 2025-08
        $observacionesCriticasAnterior = DB::connection('siic_anexos')
            ->table('anexo03_asistencia as a')
            ->join('anexo03_persona as p','a.id_persona','=','p.id')
            ->join('anexo03 as x','p.id_anexo03','=','x.id_anexo03')
            ->select(
                'a.tipo_observacion',
                'a.observacion',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.dni')) as dni"),
                'x.codlocal'
            )
            ->whereIn(DB::raw('UPPER(a.tipo_observacion)'), [
                'INASISTENCIAJUSTIFICADA','ABANDONOCARGO','VACACIONES',
                'LICENCIA','CESE','PERMISOSINGOCE'
            ])
            ->whereNotNull('x.oficio')->where('x.oficio','!=','')
            ->whereNotNull('x.expediente')->where('x.expediente','!=','')
            ->where('x.fecha_creacion','like',$anioMesAnterior.'%')
            ->get();

        $observacionesPorColegioAnterior = $observacionesCriticasAnterior->groupBy('codlocal')
            ->map(fn($items)=>collect($items)->pluck('dni')->unique()->count());

        $cumplimientoAnterior = $resultado->map(function($item) use ($observacionesPorColegioAnterior){
            $codlocal = $item['codlocal'];
            $total = $item['total_docentes'];
            $conObservacion = $observacionesPorColegioAnterior[$codlocal] ?? 0;

            $porcentaje = $total>0 ? round((($total-$conObservacion)/$total)*100,2) : 0;

            return array_merge($item,[
                'docentes_con_observacion'=>$conObservacion,
                'cumplimiento'=>$porcentaje,
            ]);
        })->values();
        // =========================
        // Observaciones por tipo (MES ACTUAL)
        // =========================
        $observacionesPorTipo = $observacionesCriticas
            ->groupBy(fn($item) => strtoupper($item->tipo_observacion))
            ->map->count();

        // =========================
        // Observaciones por tipo (MES ANTERIOR)
        // =========================
        $observacionesPorTipoAnterior = $observacionesCriticasAnterior
            ->groupBy(fn($item) => strtoupper($item->tipo_observacion))
            ->map->count();

        return view('reporteAnexos.reporteanexos03', compact(
            'session',
            'reportes',
            'distritos',
            'instituciones',
            'niveles',
            'directoresSinAnexo03',
            'observacionesCriticas',
            'directoresEnProceso',
            'directoresCompletos',
            'cumplimiento',
            'cumplimientoAnterior',
            'query',
            'anioMes',
            'anioMesAnterior',
            'observacionesPorTipo',
            'observacionesPorTipoAnterior'
        ));
    }


    public function mostrarReporteAnexos04(Request $request)
    {
        if (!session()->get('siic01_admin')) {
            return response('Sesión terminada', 401);
        }

        $session = session()->get('siic01_admin');

        $filtroDistrito = $request->input('distrito');
        $filtroInstitucion = $request->input('institucion');
        $filtroNivel = $request->input('nivel');

        // Paso 1: Obtener reportes de anexo04 sin unión externa
        $reportes = DB::connection('siic_anexos')->table('anexo04')
            ->when($filtroNivel, fn($query) => $query->where('nivel', $filtroNivel))
            ->select(
                'id',
                'id_contacto',
                'fecha_creacion',
                'codlocal',
                'nivel',
                'oficio',
                'expediente',
                'ruta_pdf'
            )
            ->whereNotNull('oficio')
            ->where('oficio', '!=', '')
            ->whereNotNull('expediente')
            ->where('expediente', '!=', '')
            ->get();

        // Obtener codlocal y nivel únicos
        $codlocalNivel = $reportes->map(fn($r) => [$r->codlocal, $r->nivel]);

        // Paso 2: Obtener datos de iiee_a_evaluar_rie desde la base principal
        $iiees = Iiee_a_evaluar_rie::whereIn(DB::raw("CONCAT(codlocal,'-',nivel)"), $codlocalNivel->map(fn($c) => "{$c[0]}-{$c[1]}"))
            ->get()
            ->keyBy(fn($iiee) => "{$iiee->codlocal}-{$iiee->nivel}");
        //dd($iiees);
        // Paso 3: Obtener contactos
        $idsContacto = $reportes->pluck('id_contacto')->unique()->filter();
        $contactos = DB::table('contacto')
            ->whereIn('id_contacto', $idsContacto)
            ->get()
            ->keyBy('id_contacto');

        // Paso 4: Armar datos finales y aplicar filtro de distrito después
        $reportes = $reportes->map(function ($reporte) use ($iiees, $contactos) {
            $key = "{$reporte->codlocal}-{$reporte->nivel}";
            $iiee = $iiees[$key] ?? null;
            $contacto = $contactos[$reporte->id_contacto] ?? null;

            $reporte->institucion = $iiee->institucion ?? null;
            $reporte->correo_inst = $iiee->correo_inst ?? null;
            $reporte->distrito = $iiee->distrito ?? null;
            $reporte->codmod = $iiee->codmod ?? null;

            $reporte->dni = $contacto->dni ?? null;
            $reporte->nombres = $contacto->nombres ?? null;
            $reporte->apellipat = $contacto->apellipat ?? null;
            $reporte->apellimat = $contacto->apellimat ?? null;
            $reporte->celular_pers = $contacto->celular_pers ?? null;

            return $reporte;
        })->filter(function ($reporte) use ($filtroDistrito) {
            if ($filtroDistrito) {
                return $reporte->distrito === $filtroDistrito;
            }
            return true;
        })->values();

        // Datos para filtros
        $distritos = Iiee_a_evaluar_rie::distinct()->pluck('distrito')->sort()->values();
        $instituciones = DB::table('iiee_a_evaluar_RIE')
            ->select('codmod', 'institucion')->distinct()->orderBy('institucion')->get();
        $niveles = DB::connection('siic_anexos')->table('anexo04')->distinct()->pluck('nivel')->sort()->values();

        $anioMes = '2025-09';

        $query = DB::table('iiee_a_evaluar_RIE as R')
            ->select(
                'R.codlocal',
                'R.red',
                DB::raw("GROUP_CONCAT(DISTINCT R.institucion SEPARATOR '-') as institucion"),
                DB::raw("IFNULL(MAX(ANEXO04.situacion),'NO REGISTRO') as Anexo04"),
                DB::raw("IFNULL(MAX(ANEXO04.ruta_pdf),'') as ruta_pdf"),
                DB::raw("IFNULL(MAX(ANEXO04.expediente),'') as expediente"),
                'R.modalidad',
                DB::raw("GROUP_CONCAT(DISTINCT R.nivel SEPARATOR '-') as nivel"),
                DB::raw("GROUP_CONCAT(DISTINCT R.codmod SEPARATOR '-') as codmod"),
                'R.distrito',
                'R.descgestie',
                'R.dni_director',
                'R.director',
                'C.celular_pers',
                'R.correo_inst',
                'C.correo_pers',
            )
            ->leftJoin(DB::raw("
                (
                    SELECT 
                        codlocal,
                        IF(
                            nivel IN('Inicial - Jardín','Primaria','Inicial - Cuna-jardín','Secundaria'),'EBR',
                            IF(
                                nivel IN('Básica Especial-Inicial','Básica Especial-Primaria','Básica Especial'),'EBE',
                                IF(nivel='ETP','ETP',
                                    IF(nivel IN('Básica Alternativa-Avanzado','Básica Alternativa-Inicial e Intermedio'),'EBA','')
                                )
                            )
                        ) as modalidad,
                        IF(expediente IS NULL,'EN PROCESO','ENVIADO') as situacion,
                        ruta_pdf,
                        expediente
                    FROM siic01ugel01gob_anexos.anexo04
                    WHERE fecha_creacion LIKE '%$anioMes%'
                ) ANEXO04
            "), function ($join) {
                $join->on('R.codlocal','=','ANEXO04.codlocal')
                    ->on('R.modalidad','=','ANEXO04.modalidad');
            })
            ->leftJoin('contacto as C','R.id_contacto_dir','=','C.id_contacto')
            ->where('R.estado',1)
            ->where('R.cant_plazas_nexus','>',0)
            ->groupBy(
                'R.codlocal',
                'R.red',
                'R.modalidad',
                'R.distrito',
                'R.descgestie',
                'R.dni_director',
                'R.director',
                'C.celular_pers',
                'R.correo_inst',
                'C.correo_pers'
            )
            ->get();

        //  Agrupamos según la situación
        $directoresSinAnexo04 = $query->where('Anexo04', 'NO REGISTRO');
        $directoresEnProceso  = $query->where('Anexo04', 'EN PROCESO');
        $directoresCompletos  = $query->where('Anexo04', 'ENVIADO');

        $docentesTotales = DB::connection('siic_anexos')
            ->table('anexo04_persona as p')
            ->join('anexo04 as a', 'a.id', '=', 'p.id_anexo04')
            ->select('a.codlocal', 'a.nivel', 'p.persona_json')
            ->get()
            ->map(function($r) {
                $jsonPersona = preg_replace('/^"+|"+$/', '', $r->persona_json);
                $persona = json_decode($jsonPersona, true) ?? [];
                $r->dni = $persona['dni'] ?? '';
                $r->nombres = $persona['nombres'] ?? '';
                $r->cargo = $persona['cargo'] ?? '';
                return $r;
            });

        $personasConInasistencia = DB::connection('siic_anexos')
            ->table('anexo04_persona as p')
            ->join('anexo04_inasistencia as i', 'i.id_persona', '=', 'p.id')
            ->join('anexo04 as a', 'a.id', '=', 'p.id_anexo04')
            ->select('a.codlocal', 'a.nivel', 'p.persona_json','a.fecha_creacion', 'i.inasistencia', 'i.detalle', 'a.oficio', 'a.expediente')
            ->whereNotNull('a.oficio')
            ->where('a.oficio', '!=', '')
            ->whereNotNull('a.expediente')
            ->where('a.expediente', '!=', '')
            ->get()
            ->map(function($r) {
                // Limpiar todas las comillas externas
                $jsonPersona = preg_replace('/^"+|"+$/', '', $r->persona_json);

                // Decodificar persona y asignar campos básicos
                $persona = json_decode($jsonPersona, true) ?? [];
                $r->dni = $persona['dni'] ?? '';
                $r->nombres = $persona['nombres'] ?? '';
                $r->cargo = $persona['cargo'] ?? '';

                // Decodificar inasistencia
                $inasistencia = json_decode($r->inasistencia, true) ?? [];
                $r->inasistencia = [
                    'inasistencia_total' => $inasistencia['inasistencia_total'] ?? 0,
                    'tardanza_total' => [
                        'horas' => $inasistencia['tardanza_total']['horas'] ?? 0,
                        'minutos' => $inasistencia['tardanza_total']['minutos'] ?? 0,
                    ],
                    'permiso_sg_total' => [
                        'horas' => $inasistencia['permiso_sg_total']['horas'] ?? 0,
                        'minutos' => $inasistencia['permiso_sg_total']['minutos'] ?? 0,
                    ],
                    'huelga_total' => $inasistencia['huelga_total'] ?? 0,
                ];

                return $r;
            })
            // Filtrar solo los que tengan algo registrado
            ->filter(function($r) {
                $detalle = json_decode($r->detalle, true) ?? [];
                return !empty($detalle['inasistencia'])
                    || !empty($detalle['tardanza'])
                    || !empty($detalle['permiso_sg'])
                    || !empty($detalle['huelga']);
            })
            ->values();

        //dd($personasConInasistencia);
        // Conteo mensual de docentes con inasistencia
        $inasistenciasPorMes = $personasConInasistencia
            ->groupBy(function($r) {
                return \Carbon\Carbon::parse($r->fecha_creacion)->format('Y-m');
            })
            ->map(function($items) {
                return collect($items)->pluck('dni')->unique()->count();
            });
        

        // Traer información de IIEE solo para los codlocal-nivel que necesitamos
        $codlocalNivel = $personasConInasistencia->map(fn($r) => "{$r->codlocal}-{$r->nivel}")->unique();
        $iiees = Iiee_a_evaluar_rie::whereIn(DB::raw("CONCAT(codlocal,'-',nivel)"), $codlocalNivel)
            ->get()
            ->keyBy(fn($i) => "{$i->codlocal}-{$i->nivel}");

        // Asignar datos de IIEE
        $personasConInasistencia = $personasConInasistencia->map(function($r) use ($iiees) {
            $key = "{$r->codlocal}-{$r->nivel}";
            $iiee = $iiees[$key] ?? null;
            $r->institucion = $iiee->institucion ?? '';
            $r->distrito = $iiee->distrito ?? '';
            $r->red = $iiee->red ?? '';
            return $r;
        })

        // Aplicar filtros opcionales
        ->filter(function($r) use ($filtroDistrito, $filtroInstitucion, $filtroNivel) {
            if ($filtroDistrito && $r->distrito !== $filtroDistrito) return false;
            if ($filtroInstitucion && $r->institucion !== $filtroInstitucion) return false;
            if ($filtroNivel && $r->nivel !== $filtroNivel) return false;
            return true;
        })
        ->values();
        $totalDocentes = $docentesTotales->unique('dni')->count();
        $totalDeficientes = $personasConInasistencia->count();
        $totalCumplen = $totalDocentes - $totalDeficientes;

        // Sacamos docentes desde NEXUS (solo docentes)
        $docentesNexus = DB::table('nexus')
            ->select('numdocum as dni', 'codlocal', 'desctipotrab', 'estado', 'descniveduc as nivel')
            ->where('desctipotrab', 'DOCENTE')
            ->where('estado', 1)
            ->get();
 
        // Sacamos IE y distritos
        $iiee = DB::table('iiee')
            ->select('cod_local', 'nombre_iiee', 'distrito')
            ->get()
            ->keyBy('cod_local');

        // Sacamos niveles a evaluar
        $iieeEvaluar = Iiee_a_evaluar_rie::select('codlocal', 'nivel')
            ->get()
            ->groupBy('codlocal'); 

        // Agrupar docentes por colegio
        $resultado = $docentesNexus
            ->groupBy('codlocal')
            ->map(function ($docentes, $codlocal) use ($iiee, $iieeEvaluar) {
                $ie   = $iiee[$codlocal] ?? null;
                $niveles = $iieeEvaluar[$codlocal] ?? collect();

                // Conteo total
                $totalDocentes = $docentes->count();

                // Conteo por nivel (comparando con descniveduc del nexus)
                $porNivel = $niveles->mapWithKeys(function ($nivelObj) use ($docentes) {
                    $conteo = $docentes->where('nivel', $nivelObj->nivel)->count();
                    return [$nivelObj->nivel => $conteo];
                });
                    return [
                        'codlocal'      => $codlocal,
                        'nombre_ie'     => $ie->nombre_iiee ?? '',
                        'distrito'      => $ie->distrito ?? '',
                        'total_docentes'=> $totalDocentes,
                        'docentes_por_nivel' => $porNivel,
                    ];
                });
        //dd($resultado->toJson(JSON_PRETTY_PRINT));

        // Agrupar docentes con inasistencia por colegio
        $inasistenciasPorColegio = $personasConInasistencia
            ->groupBy('codlocal')
            ->map(function($items) {
                return collect($items)->pluck('dni')->unique()->count();
            });

        // Calcular cumplimiento
        $cumplimiento = $resultado->map(function($item) use ($inasistenciasPorColegio) {
            $codlocal = $item['codlocal'];
            $total = $item['total_docentes'];
            $conDescuento = $inasistenciasPorColegio[$codlocal] ?? 0;

            $porcentaje = $total > 0
                ? round((($total - $conDescuento) / $total) * 100, 2)
                : 0;

            return array_merge($item, [
                'docentes_con_descuento' => $conDescuento,
                'cumplimiento' => $porcentaje,
            ]);
        });
        $cumplimiento = $cumplimiento->map(function ($item) {
            $item['docentes_por_nivel'] = collect($item['docentes_por_nivel'])->toArray();
            return $item;
        })->values();
        //dd($cumplimiento);
        
        $docentesCriticos = $personasConInasistencia->filter(function($r) {
            // Decodificar o usar directo si ya es array
            $detalle = is_string($r->detalle) 
                ? (json_decode($r->detalle, true) ?? []) 
                : ($r->detalle ?? []);

            $totales = is_string($r->inasistencia) 
                ? (json_decode($r->inasistencia, true) ?? []) 
                : ($r->inasistencia ?? []);

            $inasistenciasTotal = $totales['inasistencia_total'] ?? 0;
            $inasistenciasDetalle = $detalle['inasistencia'] ?? [];

            // Si no hay inasistencias registradas, descartar
            if ($inasistenciasTotal <= 0 || empty($inasistenciasDetalle)) {
                return false;
            }

            // Normalizar fechas del detalle
            $fechas = collect($inasistenciasDetalle)
                ->map(fn($f) => \Carbon\Carbon::parse($f)->format('Y-m-d'))
                ->unique()
                ->sort()
                ->values();

            if ($fechas->isEmpty()) {
                return false;
            }

            // Regla 1: 5 o más inasistencias (discontinuas)
            if ($fechas->count() >= 5) {
                return true;
            }

            // Regla 2: 3 inasistencias consecutivas
            $consecutivas = 1;
            for ($i = 1; $i < $fechas->count(); $i++) {
                $prev = \Carbon\Carbon::parse($fechas[$i-1]);
                $curr = \Carbon\Carbon::parse($fechas[$i]);

                if ($prev->diffInDays($curr) === 1) {
                    $consecutivas++;
                    if ($consecutivas >= 3) {
                        return true;
                    }
                } else {
                    $consecutivas = 1;
                }
            }

            return false;
        })->values();

        $totalCriticos = $docentesCriticos->count();
        $mesActual = now()->format('Y-m'); // Ej: 2025-09

        $personasMesActual = $personasConInasistencia->filter(function($r) use ($mesActual) {
            return \Carbon\Carbon::parse($r->fecha_creacion)->format('Y-m') === $mesActual;
        });
        //dd($totalCriticos);
        return view('reporteAnexos.reporteanexos04', compact(
            'session',
            'reportes',
            'distritos',
            'instituciones',
            'niveles',  
            'directoresSinAnexo04',
            'personasConInasistencia',
            'totalDocentes',
            'totalDeficientes',
            'totalCumplen',
            'docentesTotales',
            'resultado',
            'cumplimiento',
            'directoresEnProceso',
            'directoresCompletos',
            'docentesCriticos',
            'totalCriticos',
            'inasistenciasPorMes',
            'personasMesActual',
        ));
    }

    public function mostrarAsistenciaConObservaciones(Request $request)
    {
        $codlocal = $request->get('codlocal'); 
        $nivelSeleccionado = $request->get('nivel'); 
        $tipo = $request->get('tipo', 'anexo03');

        // Filtros de mes/año (puedes traerlos del request o usar valores por defecto)
        $mes = $request->get('mes', date('n')); 
        $anio = $request->get('anio', date('Y')); 

        // Buscar institución
        $institucion = Iiee_a_evaluar_rie::select('modalidad', 'institucion')
            ->where('codlocal', $codlocal)
            ->first();

        // Lista de personal desde nexus
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

        if ($tipo === 'anexo03') {
            // ========================
            // Lógica para ANEXO 03
            // ========================
            $personasReporte = DB::connection('siic_anexos')->table('anexo03')
                ->join('anexo03_persona', 'anexo03.id_anexo03', '=', 'anexo03_persona.id_anexo03')
                ->where('anexo03.codlocal', $codlocal)
                ->select('anexo03_persona.id', 'anexo03_persona.persona_json')
                ->get()
                ->mapWithKeys(function ($item) {
                    $persona = json_decode($item->persona_json);
                    $dni = $persona->dni ?? null;
                    $codplaza = $persona->cod ?? null;
                    $clave = $codplaza ? "{$dni}_{$codplaza}" : $dni;
                    return [$clave => $item->id];
                });

            $asistencias = DB::connection('siic_anexos')->table('anexo03_asistencia')
                ->whereIn('id_persona', $personasReporte->values()->all())
                ->select('id_persona','asistencia','observacion','tipo_observacion','observacion_detalle')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->id_persona => [
                        'asistencia' => json_decode($item->asistencia),
                        'observacion' => $item->observacion,
                        'tipo_observacion' => $item->tipo_observacion,
                        'observacion_detalle' => $item->observacion_detalle,
                    ]];
                });

            $datosAsistenciaPorDni = [];
            foreach ($personasReporte as $clave => $id_persona) {
                if (!empty($asistencias[$id_persona]['observacion']) 
                    || !empty($asistencias[$id_persona]['tipo_observacion'])) {
                    $datosAsistenciaPorDni[$clave] = $asistencias[$id_persona];
                }
            }

            return response()->json([
                'tipo' => 'anexo03',
                'institucion' => $institucion->institucion ?? '',
                'modalidad' => $institucion->modalidad ?? '',
                'registros' => $personal,
                'asistencias' => $datosAsistenciaPorDni,
            ]);

        } else {
            // ========================
            // Lógica para ANEXO 04
            // ========================
            $bloques2025 = [
                ['tipo' => 'gestion',  'inicio' => '2025-03-03', 'fin' => '2025-03-14'],
                ['tipo' => 'lectiva',  'inicio' => '2025-03-17', 'fin' => '2025-05-16'],
                ['tipo' => 'gestion',  'inicio' => '2025-05-19', 'fin' => '2025-05-23'],
                ['tipo' => 'lectiva',  'inicio' => '2025-05-26', 'fin' => '2025-07-25'],
                ['tipo' => 'gestion',  'inicio' => '2025-07-28', 'fin' => '2025-08-08'],
                ['tipo' => 'lectiva',  'inicio' => '2025-08-11', 'fin' => '2025-10-10'],
                ['tipo' => 'gestion',  'inicio' => '2025-10-13', 'fin' => '2025-10-17'],
                ['tipo' => 'lectiva',  'inicio' => '2025-10-20', 'fin' => '2025-12-19'],
                ['tipo' => 'gestion',  'inicio' => '2025-12-22', 'fin' => '2025-12-31'],
            ];
            
            $personasReporte = DB::connection('siic_anexos')->table('anexo04')
                ->join('anexo04_persona', 'anexo04.id', '=', 'anexo04_persona.id_anexo04')
                ->leftJoin('anexo04_inasistencia', 'anexo04_persona.id', '=', 'anexo04_inasistencia.id_persona')
                ->where('anexo04.codlocal', $codlocal)
                ->where('anexo04.mes', 8)
                ->where('anexo04.anio', $anio)
                ->select(
                    'anexo04_persona.id',
                    'anexo04_persona.persona_json',
                    'anexo04_inasistencia.inasistencia',
                    'anexo04_inasistencia.detalle'
                )
                ->get();

            $registros = [];
            $inasistencias = [];

            foreach ($personasReporte as $p) {
                $persona = json_decode($p->persona_json, true);
                $totales = $p->inasistencia ? json_decode($p->inasistencia, true) : [];
                $detalle = $p->detalle ? json_decode($p->detalle, true) : [];

                $inasistenciaNormalizado = [
                    'inasistencia_total' => intval($totales['inasistencia_total'] ?? 0),
                    'tardanza_total' => [
                        'horas' => intval($totales['tardanza_total']['horas'] ?? 0),
                        'minutos' => intval($totales['tardanza_total']['minutos'] ?? 0),
                    ],
                    'permiso_sg_total' => [
                        'horas' => intval($totales['permiso_sg_total']['horas'] ?? 0),
                        'minutos' => intval($totales['permiso_sg_total']['minutos'] ?? 0),
                    ],
                    'huelga_total' => intval($totales['huelga_total'] ?? 0),
                    'observaciones' => $totales['observaciones'] ?? '',
                    'detalle' => $detalle
                ];

                // Cálculo de cumplimiento
                $inasistenciaNormalizado['cumplimiento'] =
                    ($inasistenciaNormalizado['inasistencia_total'] > 0 ||
                    $inasistenciaNormalizado['tardanza_total']['horas'] > 0 ||
                    $inasistenciaNormalizado['tardanza_total']['minutos'] > 0 ||
                    $inasistenciaNormalizado['permiso_sg_total']['horas'] > 0 ||
                    $inasistenciaNormalizado['permiso_sg_total']['minutos'] > 0 ||
                    $inasistenciaNormalizado['huelga_total'] > 0 ||
                    !empty($inasistenciaNormalizado['observaciones']))
                    ? 'INCUMPLE'
                    : 'CUMPLE';

                $docente = [
                    'dni' => $persona['dni'] ?? null,
                    'nombres' => $persona['nombres'] ?? null,
                    'cargo' => $persona['cargo'] ?? null,
                    'condicion' => $persona['condicion'] ?? null,
                    'jornada' => $persona['jornada'] ?? null,
                    'inasistencia' => $inasistenciaNormalizado,
                ];

                // evitar duplicados por dni
                $registros[$persona['dni']] = $docente;

                if ($inasistenciaNormalizado['cumplimiento'] === 'INCUMPLE') {
                    $inasistencias[$persona['dni']] = $inasistenciaNormalizado;
                }
            }

            return response()->json([
                'tipo' => 'anexo04',
                'anio' => $anio,
                'mes'  => 8,      
                'institucion' => $institucion->institucion ?? '',
                'modalidad' => $institucion->modalidad ?? '',
                'registros' => array_values($registros),
                'inasistencias' => $inasistencias,
            ]);
        }
    }


    public function exportarObservacionesPDF(Request $request)
    {
        if (!session()->get('siic01_admin')) {
            return response('Sesión terminada', 401);
        }

        $session = session()->get('siic01_admin');

        //dd($session);
        // 1. Catálogos auxiliares (de otra BD/tablas)
        $contactos = DB::table('contacto')
            ->select('id_contacto')
            ->get()
            ->keyBy('id_contacto');

        $iiees = Iiee_a_evaluar_rie::select('codlocal', 'institucion', 'distrito', 'red')
            ->get()
            ->keyBy(function($item) {
                return strtolower(trim($item->codlocal));
            });

        $nexus = DB::table('nexus')
        ->select('numdocum', 'codmodce')
        ->get()
        ->keyBy(function($item){
            return trim($item->numdocum); // clave por DNI
        });
        $anioMes = $request->input('anioMes') ?? now()->format('Y-m'); // e.g. '2025-09'
        // 2. Base query observaciones
        $query = DB::connection('siic_anexos')
            ->table('anexo03_asistencia as a')
            ->join('anexo03_persona as p', 'a.id_persona', '=', 'p.id')
            ->join('anexo03 as x', 'p.id_anexo03', '=', 'x.id_anexo03')
            ->select(
                'a.tipo_observacion',
                'a.observacion',
                'a.observacion_detalle',
                'a.fecha_inicio',
                'a.fecha_fin',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.dni')) as dni"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.nombres')) as nombres"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.cargo')) as cargo"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.condicion')) as condicion"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(p.persona_json, '$.cod')) as codplaza"),
                'x.nivel',
                'x.codlocal',
                'x.id_contacto',
                'x.fecha_creacion',
                'x.oficio',
                'x.expediente'
            )
            ->whereIn(DB::raw('UPPER(a.tipo_observacion)'), [
                'INASISTENCIAJUSTIFICADA', 'ABANDONOCARGO', 'VACACIONES',
                'LICENCIA', 'CESE', 'PERMISOSINGOCE'
            ])
            ->whereNotNull('x.oficio')->where('x.oficio', '!=', '')
            ->whereNotNull('x.expediente')->where('x.expediente', '!=', '')
            ->where('x.fecha_creacion', 'like', $anioMes . '%');   // <-- filtro por mes

        // 3. Filtro por fechas (sí se puede directo)
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('x.fecha_creacion', [
                $request->fecha_inicio . ' 00:00:00',
                $request->fecha_fin . ' 23:59:59'
            ]);
        }

        $observacionesCriticas = $query->get();

        // 4. Transformación + filtros de institución/red/distrito a nivel PHP
        $observacionesCriticas = $observacionesCriticas->filter(function ($item) use ($request, $iiees) {
            $cod = strtolower(trim($item->codlocal));
            $iiee = $iiees[$cod] ?? null;

            if ($request->filled('distrito') && strcasecmp($iiee?->distrito, $request->distrito) !== 0) {
                return false;
            }
            if ($request->filled('red') && strcasecmp($iiee?->red, $request->red) !== 0) {
                return false;
            }
            if ($request->filled('institucion') && stripos($iiee?->institucion ?? '', $request->institucion) === false) {
                return false;
            }
            return true;
        });

        $observacionesCriticas->transform(function ($item) use ($contactos, $iiees, $nexus) {
            $contacto = $contactos[$item->id_contacto] ?? null;
            $item->distrito_contacto = $contacto?->distrito ?? null;
            $item->red_contacto = $contacto?->red ?? null;

            $iiee = $iiees[strtolower(trim($item->codlocal))] ?? null;
            $item->institucion = $iiee?->institucion ?? null;
            $item->distrito_iiee = $iiee?->distrito ?? null;
            $item->red_iiee = $iiee?->red ?? null;

            $item->codmodce = $nexus[trim($item->dni)]->codmodce ?? '';

            // Calcular días de licencia
            $item->dias_licencia = 0;
            if(!empty($item->fecha_inicio) && !empty($item->fecha_fin)){
                $inicio = \Carbon\Carbon::parse($item->fecha_inicio);
                $fin = \Carbon\Carbon::parse($item->fecha_fin);
                $item->dias_licencia = $inicio->diffInDays($fin) + 1;
            }

            return $item;
        });

        // 5. Render Blade
        $html = view('reporteAnexos.reporteObservaciones', compact('observacionesCriticas'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4-L',
            'margin_top' => 20,
            'margin_bottom' => 15,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        // Construir abreviatura
        $apellidoP = substr(session('siic01_admin.esp_apellido_paterno') ?? '', 0, 1);
        $apellidoM = substr(session('siic01_admin.esp_apellido_materno') ?? '', 0, 1);

        $nombrePartes = array_filter(explode(' ', trim(session('siic01_admin.nombre') ?? '')));
        $inicialesNombres = '';
        foreach ($nombrePartes as $i => $nombre) {
            if ($i >= 2) break;
            $inicialesNombres .= substr($nombre, 0, 1);
        }

        $abreviatura = strtoupper($apellidoP . $apellidoM . $inicialesNombres) . '/ESP';

        // Footer: tabla inline sin bordes
        $footer = '
        <table width="100%" style="border:0; padding:0; font-size:9pt;">
        <tr>
        <td style="text-align:left; border:0;">' . $abreviatura . '</td>
        <td style="text-align:right; border:0;">Página {PAGENO}-{nbpg}</td>
        </tr>
        </table>
        ';

        $mpdf->SetHTMLFooter($footer);

        // Título del PDF
        $mpdf->SetTitle('Reporte de Observaciones Críticas');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('observaciones.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }

}