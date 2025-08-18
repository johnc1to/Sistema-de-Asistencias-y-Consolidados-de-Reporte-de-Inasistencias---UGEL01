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
            ->get();

        // Obtener codlocal únicos
        $codlocales = $reportes->pluck('codlocal')->unique();

        // Obtener información de instituciones desde otra base (ej: siic_2024)
        $iiees = Iiee_a_evaluar_rie::whereIn('codlocal', $codlocales)
            ->get()
            ->keyBy('codlocal');

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

        $directoresSinAnexo03 = DB::table('contacto as c')
            ->select(
                'c.dni',
                'c.apellipat',
                'c.apellimat',
                'c.nombres',
                'c.celular_pers',
                'i.institucion as nombre_inst'
            )
            ->join('iiee_a_evaluar_RIE as i', 'c.dni', '=', 'i.dni_director')
            ->where('c.estado', 1)
            ->where('c.cargo', 'LIKE', '%DIRECTOR%')
            ->where('c.cargo', 'NOT LIKE', '%SUB-DIRECTOR%')
            ->whereNotIn('c.id_contacto', $anexo03Ids)
            ->groupBy(
                'c.dni',
                'c.apellipat',
                'c.apellimat',
                'c.nombres',
                'c.celular_pers',
                'i.institucion'
            )
            ->orderBy('c.apellipat')
            ->get();

        $observacionesCriticas = DB::connection('siic_anexos')->table('anexo03_asistencia as a')
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
            'x.fecha_creacion'
        )
        ->whereIn(DB::raw('UPPER(a.tipo_observacion)'), [
            'INASISTENCIAJUSTIFICADA', 'ABANDONOCARGO', 'VACACIONES', 'LICENCIA', 'CESE','PERMISOSINGOCE'
        ])
        ->orderBy('x.fecha_creacion', 'desc')
        ->get();

        $contactos = DB::table('contacto')
        ->select('id_contacto') 
        ->get()
        ->keyBy('id_contacto');
        $iiees = DB::table('iiee_a_evaluar_rie')
        ->select('codlocal', 'institucion', 'distrito','red')
        ->get()
        ->keyBy('codlocal');

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

        return view('reporteAnexos.reporteanexos03', compact(
            'session',
            'reportes',
            'distritos',
            'instituciones',
            'niveles',
            'directoresSinAnexo03',
            'observacionesCriticas'
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
                    'expediente'
                )
                ->get();

            // Obtener codlocal y nivel únicos
            $codlocalNivel = $reportes->map(fn($r) => [$r->codlocal, $r->nivel]);

            // Paso 2: Obtener datos de iiee_a_evaluar_rie desde la base principal
            $iiees = Iiee_a_evaluar_rie::whereIn(DB::raw("CONCAT(codlocal,'-',nivel)"), $codlocalNivel->map(fn($c) => "{$c[0]}-{$c[1]}"))
                ->get()
                ->keyBy(fn($iiee) => "{$iiee->codlocal}-{$iiee->nivel}");

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

            // Subconsulta de directores con reporte anexo04
            $anexo04Ids = DB::connection('siic_anexos')
                ->table('anexo04')
                ->select('id_contacto');

            // Directores sin anexo04
            $directoresSinAnexo04 = DB::table('contacto as c')
                ->select(
                    'c.dni',
                    'c.apellipat',
                    'c.apellimat',
                    'c.nombres',
                    'c.celular_pers',
                    'i.institucion as nombre_inst'
                )
                ->join('iiee_a_evaluar_RIE as i', 'c.dni', '=', 'i.dni_director')
                ->where('c.estado', 1)
                ->where('c.cargo', 'LIKE', '%DIRECTOR%')
                ->where('c.cargo', 'NOT LIKE', '%SUB-DIRECTOR%')
                ->whereNotIn('c.id_contacto', $anexo04Ids)
                ->groupBy(
                    'c.dni',
                    'c.apellipat',
                    'c.apellimat',
                    'c.nombres',
                    'c.celular_pers',
                    'i.institucion' 
                )
                ->orderBy('c.apellipat')
                ->get();

            $personasConInasistencia = DB::connection('siic_anexos')
                ->table('anexo04_persona as p')
                ->join('anexo04_inasistencia as i', 'i.id_persona', '=', 'p.id')
                ->join('anexo04 as a', 'a.id', '=', 'p.id_anexo04')
                ->select('a.codlocal', 'a.nivel', 'p.persona_json', 'i.inasistencia', 'i.detalle')
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
            
            
            return view('reporteAnexos.reporteanexos04', compact(
                'session',
                'reportes',
                'distritos',
                'instituciones',
                'niveles',
                'directoresSinAnexo04',
                'personasConInasistencia',
            ));
    }

}