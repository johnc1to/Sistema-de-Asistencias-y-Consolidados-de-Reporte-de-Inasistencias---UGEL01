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
        $reportes = $reportes->map(function ($reporte) use ($contactos, $iiees) {
            $contacto = $contactos[$reporte->id_contacto] ?? null;
            $iiee = $iiees[$reporte->codlocal] ?? null;

            $reporte->dni = $contacto->dni ?? null;
            $reporte->nombres = $contacto->nombres ?? null;
            $reporte->apellipat = $contacto->apellipat ?? null;
            $reporte->apellimat = $contacto->apellimat ?? null;
            $reporte->celular_pers = $contacto->celular_pers ?? null;

            $reporte->institucion = $iiee->institucion ?? null;
            $reporte->codmod = $iiee->codmod ?? null;
            $reporte->correo_inst = $iiee->correo_inst ?? null;
            $reporte->distrito = $iiee->distrito ?? null;

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

        return view('reporteAnexos.reporteanexos03', compact(
            'session',
            'reportes',
            'distritos',
            'instituciones',
            'niveles',
            'directoresSinAnexo03'
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
                    'nivel'
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

            return view('reporteAnexos.reporteanexos04', compact(
                'session',
                'reportes',
                'distritos',
                'instituciones',
                'niveles',
                'directoresSinAnexo04'
            ));
        }

}