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


class ReporteLogsIngresoController extends Controller
{

public function mostrarReporteLogIngreso(Request $request)
{
    if (!session()->get('siic01_admin')) {
        return response('Sesión terminada', 401);
    }

    $session = session()->get('siic01_admin');

    // --- 1. Últimos 500 accesos (como lo tienes) ---
    $logs = DB::connection('sua_extranet')
        ->table('log_accesos')
        ->orderBy('fecha', 'desc')
        ->limit(500)
        ->get();

    $logs = $logs->map(function ($log) {
        $extra = DB::connection('siguhs')->selectOne("
            SELECT u.EmaUsu, p.*,
                c.NomCar, o.DesOrg, o.CodSin, r.descripcion
            FROM persona p
            LEFT JOIN persona_organigrama_cargo oc on oc.CodPer=p.id
            LEFT JOIN persona_regimen_laboral pr on pr.CodPer=p.id
            LEFT JOIN cargo c on c.id=oc.CodCar
            LEFT JOIN organigrama o on o.id=oc.CodOrg
            LEFT JOIN regimen_laboral r on r.id = pr.CodReg
            LEFT JOIN users u ON u.CodPer = p.id
            WHERE p.EstPer=1 AND oc.EstPor=1 AND pr.estado=1
            AND u.EmaUsu = ?
            LIMIT 1
        ", [$log->correo]);

        $log->extra = $extra;
        return $log;
    });

    // --- 2. Último ingreso por usuario ---
    $subquery = DB::connection('sua_extranet')
        ->table('log_accesos as l1')
        ->select('l1.correo', DB::raw('MAX(l1.fecha) as ultimo_ingreso'))
        ->groupBy('l1.correo');

    $ultimosIngresos = DB::connection('sua_extranet')
        ->table('log_accesos as l2')
        ->joinSub($subquery, 'ultimos', function ($join) {
            $join->on('l2.correo', '=', 'ultimos.correo')
                 ->on('l2.fecha', '=', 'ultimos.ultimo_ingreso');
        })
        ->orderBy('l2.fecha', 'desc')
        ->get();

    $ultimosIngresos = $ultimosIngresos->map(function ($log) {
        $extra = DB::connection('siguhs')->selectOne("
            SELECT u.EmaUsu, p.*,
                c.NomCar, o.DesOrg, o.CodSin, r.descripcion
            FROM persona p
            LEFT JOIN persona_organigrama_cargo oc on oc.CodPer=p.id
            LEFT JOIN persona_regimen_laboral pr on pr.CodPer=p.id
            LEFT JOIN cargo c on c.id=oc.CodCar
            LEFT JOIN organigrama o on o.id=oc.CodOrg
            LEFT JOIN regimen_laboral r on r.id = pr.CodReg
            LEFT JOIN users u ON u.CodPer = p.id
            WHERE p.EstPer=1 AND oc.EstPor=1 AND pr.estado=1
            AND u.EmaUsu = ?
            LIMIT 1
        ", [$log->correo]);

        $log->extra = $extra;
        return $log;
    });

    // --- Estadísticas de los últimos 500 ---
    $porDia = $logs->groupBy(function($log) {
        return \Carbon\Carbon::parse($log->fecha)->format('Y-m-d');
    })->map->count();

    $porSistema = $logs->groupBy('nomSis')->map->count();

    $topUsuarios = $logs->groupBy('correo')->map->count()->sortDesc()->take(5);

    return view('ReporteLogsIngreso.reportelogingreso', compact(
        'session',
        'logs',
        'ultimosIngresos',
        'porDia',
        'porSistema',
        'topUsuarios'
    ));
}


}