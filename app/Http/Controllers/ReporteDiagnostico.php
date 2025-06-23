<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

use DB;

class ReporteDiagnostico extends Controller
{
    private function obtenerRegistradosGlobalPorCodmod()
    {
        $tablas = [
            'app_matrix_evaluacion_respuesta_1baa',
            'app_matrix_evaluacion_respuesta_1pri',
            'app_matrix_evaluacion_respuesta_1sec',
            'app_matrix_evaluacion_respuesta_2baa',
            'app_matrix_evaluacion_respuesta_2pri',
            'app_matrix_evaluacion_respuesta_2sec',
            'app_matrix_evaluacion_respuesta_3baa',
            'app_matrix_evaluacion_respuesta_3bai',
            'app_matrix_evaluacion_respuesta_3pri',
            'app_matrix_evaluacion_respuesta_3sec',
            'app_matrix_evaluacion_respuesta_4baa',
            'app_matrix_evaluacion_respuesta_4pri',
            'app_matrix_evaluacion_respuesta_4sec',
            'app_matrix_evaluacion_respuesta_5pri',
            'app_matrix_evaluacion_respuesta_5sec',
            'app_matrix_evaluacion_respuesta_6pri',
        ];

        $resultados = [];

        foreach ($tablas as $tabla) {
            $filas = DB::connection('evaluacion_diagnostica')
                ->table($tabla . ' as R')
                ->join('app_matrix_detalle as D', 'D.id_matrix_detalle', '=', 'R.id_matrix_detalle')
                ->join('app_matrix_evaluacion_alumno as A', 'A.id_detalle_alumno', '=', 'R.id_detalle_alumno')
                ->join('app_matrix_evaluacion as E', 'E.id_evaluacion', '=', 'A.id_evaluacion')
                ->where('D.estado', 1)
                ->where('A.estado', 1)
                ->whereNotIn('A.EstadoMatricula', ['trasladado', '', '8', '12'])
                ->select('E.codmodce', DB::raw('COUNT(DISTINCT R.id_detalle_alumno) as cantidad'))
                ->groupBy('E.codmodce')
                ->get();

            foreach ($filas as $fila) {
                $codmod = $fila->codmodce;
                $resultados[$codmod] = ($resultados[$codmod] ?? 0) + $fila->cantidad;
            }
        }

        return $resultados;
    }

    private function obtenerTotalAlumnosPorCodmod()
    {
        return DB::connection('evaluacion_diagnostica')
            ->table('app_matrix_evaluacion_alumno as A')
            ->join('app_matrix_evaluacion as E', 'E.id_evaluacion', '=', 'A.id_evaluacion')
            ->where('A.estado', 1)
            ->whereNotIn('A.EstadoMatricula', ['trasladado', '', '8', '12'])
            ->select('E.codmodce', DB::raw('COUNT(DISTINCT A.id_detalle_alumno) as total'))
            ->groupBy('E.codmodce')
            ->pluck('total', 'codmodce')
            ->toArray();
    }

    private function obtenerEvaluadosPorGrado(Request $request)
    {
        $gradosPorTabla = [
            'app_matrix_evaluacion_respuesta_1pri' => '1° Primaria',
            'app_matrix_evaluacion_respuesta_2pri' => '2° Primaria',
            'app_matrix_evaluacion_respuesta_3pri' => '3° Primaria',
            'app_matrix_evaluacion_respuesta_4pri' => '4° Primaria',
            'app_matrix_evaluacion_respuesta_5pri' => '5° Primaria',
            'app_matrix_evaluacion_respuesta_6pri' => '6° Primaria',
            'app_matrix_evaluacion_respuesta_1sec' => '1° Secundaria',
            'app_matrix_evaluacion_respuesta_2sec' => '2° Secundaria',
            'app_matrix_evaluacion_respuesta_3sec' => '3° Secundaria',
            'app_matrix_evaluacion_respuesta_4sec' => '4° Secundaria',
            'app_matrix_evaluacion_respuesta_5sec' => '5° Secundaria',
            'app_matrix_evaluacion_respuesta_1baa' => '1° BAA',
            'app_matrix_evaluacion_respuesta_2baa' => '2° BAA',
            'app_matrix_evaluacion_respuesta_3baa' => '3° BAA',
            'app_matrix_evaluacion_respuesta_3bai' => '3° BAI',
            'app_matrix_evaluacion_respuesta_4baa' => '4° BAA',
        ];

        $resultados = [];

        foreach ($gradosPorTabla as $tabla => $nombreGrado) {
            $query = DB::connection('evaluacion_diagnostica')
                ->table("$tabla as R")
                ->join('app_matrix_detalle as D', 'D.id_matrix_detalle', '=', 'R.id_matrix_detalle')
                ->join('app_matrix_evaluacion_alumno as A', 'A.id_detalle_alumno', '=', 'R.id_detalle_alumno')
                ->join('app_matrix_evaluacion as E', 'E.id_evaluacion', '=', 'A.id_evaluacion')
                ->join('iiee_a_evaluar_RIE as I', 'I.codmod', '=', 'E.codmodce')
                ->where('D.estado', 1)
                ->where('A.estado', 1)
                ->whereNotIn('A.EstadoMatricula', ['trasladado', '', '8', '12']);

            // Aplicar filtros
            if ($request->filled('codlocal')) {
                $query->where('I.codlocal', $request->codlocal);
            }
            if ($request->filled('codmod')) {
                $query->where('I.codmod', $request->codmod);
            }
            if ($request->filled('red')) {
                $query->where('I.red', $request->red);
            }
            if ($request->filled('institucion')) {
                $query->where('I.institucion', 'like', "%{$request->institucion}%");
            }
            if ($request->filled('nivel')) {
                $query->where('I.nivel', $request->nivel);
            }
            if ($request->filled('distrito')) {
                $query->where('I.distrito', $request->distrito);
            }

            $cantidad = $query->distinct('R.id_detalle_alumno')->count('R.id_detalle_alumno');

            $resultados[$nombreGrado] = $cantidad;
        }

        return $resultados;
    }

    private function obtenerEsperadosPorGrado(Request $request)
    {
        $codigosPorGrado = [
            '1° Primaria' => ['1PRI'],
            '2° Primaria' => ['2PRI'],
            '3° Primaria' => ['3PRI'],
            '4° Primaria' => ['4PRI'],
            '5° Primaria' => ['5PRI'],
            '6° Primaria' => ['6PRI'],
            '1° Secundaria' => ['1SEC'],
            '2° Secundaria' => ['2SEC'],
            '3° Secundaria' => ['3SEC'],
            '4° Secundaria' => ['4SEC'],
            '5° Secundaria' => ['5SEC'],
            '1° BAA' => ['1BAA'],
            '2° BAA' => ['2BAA'],
            '3° BAA' => ['3BAA'],
            '3° BAI' => ['3BAI'],
            '4° BAA' => ['4BAA'],
        ];

        $resultados = [];

        foreach ($codigosPorGrado as $nombreGrado => $codigos) {
            $query = DB::connection('evaluacion_diagnostica')
                ->table('app_matrix_evaluacion_alumno as A')
                ->join('app_matrix_evaluacion as E', 'E.id_evaluacion', '=', 'A.id_evaluacion')
                ->join('iiee_a_evaluar_RIE as I', 'I.codmod', '=', 'E.codmodce')
                ->whereIn('E.cod_grado', $codigos)
                ->where('A.estado', 1)
                ->whereNotIn('A.EstadoMatricula', ['trasladado', '', '8', '12']);

            // Aplicar filtros del request
            if ($request->filled('codlocal')) {
                $query->where('I.codlocal', $request->codlocal);
            }
            if ($request->filled('codmod')) {
                $query->where('I.codmod', $request->codmod);
            }
            if ($request->filled('red')) {
                $query->where('I.red', $request->red);
            }
            if ($request->filled('institucion')) {
                $query->where('I.institucion', 'like', "%{$request->institucion}%");
            }
            if ($request->filled('nivel')) {
                $query->where('I.nivel', $request->nivel);
            }
            if ($request->filled('distrito')) {
                $query->where('I.distrito', $request->distrito);
            }

            $cantidad = $query->distinct('A.id_detalle_alumno')->count('A.id_detalle_alumno');

            $resultados[$nombreGrado] = $cantidad;
        }
        
        return $resultados;
    }

    public function mostrarReporteUnificado(Request $request)
    {
        if (!session()->get('siic01_admin')) {
            return response('Sesión terminada', 401);
        }

        $session = session()->get('siic01_admin');

        // Base del query 
        $queryBase = DB::connection('evaluacion_diagnostica')
            ->table('iiee_a_evaluar_RIE as R')
            ->leftJoin('app_matrix_evaluacion as E', function ($join) {
                $join->on('E.codmodce', '=', 'R.codmod')
                    ->where('E.estado', '=', 1);
            })
            // ->leftJoin('app_resumen as T', function ($join) {
            //     $join->on('T.id_evaluacion', '=', 'E.id_evaluacion')
            //         ->where('T.id_matrix', '=', 1);
            // })
            ->select(
                'R.codlocal',
                'R.codmod',
                'R.red',
                'R.institucion',
                'R.nivel',
                'R.distrito',
                'R.gestion',
                'R.dni_director',
                'R.director',
                'R.telefono',
                'R.correo_inst',
                DB::raw('COUNT(DISTINCT E.id_evaluacion) AS importo_csv'),
                DB::raw('COUNT(DISTINCT E.cod_grado) AS cantidad_grados'),
                DB::raw('COUNT(DISTINCT E.id_evaluacion) AS cantidad_secciones'),
                // DB::raw('COUNT(DISTINCT T.id_evaluacion) AS cantidad_registrados'),
                // DB::raw("CASE 
                //             WHEN COUNT(DISTINCT E.id_evaluacion) > 0 THEN 
                //                 ROUND((COUNT(DISTINCT T.id_evaluacion) * 100.0) / COUNT(DISTINCT E.id_evaluacion), 2)
                //             ELSE 0
                //         END AS porcentaje_avance")
            )
            ->where('R.estado', 1)
            ->where('R.cant_plazas_nexus', '>', 0);

        // Aplicar filtros comunes
        $queryFiltrado = $this->aplicarFiltrosComunes(clone $queryBase, $request);

        // Obtener todos los registros sin paginar para los totales del gráfico
        $registrosNoPaginados = $queryFiltrado
            ->groupBy(
                'R.codlocal',
                'R.codmod',
                'R.red',
                'R.institucion',
                'R.nivel',
                'R.distrito',
                'R.gestion',
                'R.dni_director',
                'R.director',
                'R.telefono',
                'R.correo_inst'
            )
            ->get();
        

        
        $registradosPorCodmod = $this->obtenerRegistradosGlobalPorCodmod();
        $totalAlumnosPorCodmod = $this->obtenerTotalAlumnosPorCodmod();

        $evaluadosPorGrado = $this->obtenerEvaluadosPorGrado($request);
        $esperadosPorGrado = $this->obtenerEsperadosPorGrado($request);
        $totalEsperado = array_sum($esperadosPorGrado);

        $avancePorGrado = [];

        foreach ($esperadosPorGrado as $grado => $esperado) {
            $evaluado = $evaluadosPorGrado[$grado] ?? 0;
            $avancePorGrado[] = [
                'grado' => $grado,
                'esperado' => $esperado,
                'evaluado' => $evaluado,
                'porcentaje' => $esperado > 0 ? round(($evaluado * 100) / $esperado, 2) : 0
            ];
        }

        $registrosNoPaginados->transform(function ($registro) use ($registradosPorCodmod) {
            $codmod = $registro->codmod;

            $registro->cantidad_registrados = $registradosPorCodmod[$codmod] ?? 0;
            $registro->total_alumnos = $totalAlumnosPorCodmod[$codmod] ?? 0;

            // Porcentaje por alumnos
            $registro->porcentaje_avance_alumnos = ($registro->total_alumnos > 0)
                ? round(($registro->cantidad_registrados * 100) / $registro->total_alumnos, 2)
                : 0;
            // Recalcular porcentaje de avance
            $registro->porcentaje_avance = ($registro->importo_csv > 0)
                ? round(($registro->cantidad_registrados * 100) / $registro->importo_csv, 2)
                : 0;

            return $registro;
        });

        // Cálculos para el gráfico (totales y promedio)
        $total_csv = $registrosNoPaginados->sum('importo_csv');
        $total_grados = $registrosNoPaginados->sum('cantidad_grados');
        $total_registrados = $registrosNoPaginados->sum('cantidad_registrados');
        $promedio_avance = $registrosNoPaginados->count() > 0
            ? round($registrosNoPaginados->avg('porcentaje_avance'), 2)
            : 0;


        // Consulta paginada para la tabla
        $perPage = $request->get('per_page', 700);
        $registros = $queryFiltrado
            ->orderBy('R.red')
            ->paginate($perPage)
            ->appends($request->all());

        $registros->getCollection()->transform(function ($registro) use ($registradosPorCodmod, $totalAlumnosPorCodmod) {
            $codmod = $registro->codmod;

            $registro->cantidad_registrados = $registradosPorCodmod[$codmod] ?? 0;
            $registro->total_alumnos = $totalAlumnosPorCodmod[$codmod] ?? 0;

            $registro->porcentaje_avance_alumnos = ($registro->total_alumnos > 0)
                ? round(($registro->cantidad_registrados * 100) / $registro->total_alumnos, 2)
                : 0;

            $registro->porcentaje_avance = ($registro->importo_csv > 0)
                ? round(($registro->cantidad_registrados * 100) / $registro->importo_csv, 2)
                : 0;

            return $registro;
        });

        // Filtros únicos
        $distritos = DB::connection('evaluacion_diagnostica')
            ->table('iiee_a_evaluar_RIE')->where('estado', 1)
            ->distinct()->pluck('distrito');

        $instituciones = DB::connection('evaluacion_diagnostica')
            ->table('iiee_a_evaluar_RIE')->where('estado', 1)
            ->distinct()->pluck('institucion');

        $niveles = DB::connection('evaluacion_diagnostica')
            ->table('iiee_a_evaluar_RIE')->where('estado', 1)
            ->distinct()->pluck('nivel');

        $redes = DB::connection('evaluacion_diagnostica')
            ->table('iiee_a_evaluar_RIE')->where('estado', 1)
            ->distinct()->pluck('red');

        $detalleSecciones = DB::connection('evaluacion_diagnostica')
            ->table('app_matrix_evaluacion')
            ->select('cod_grado', 'seccion', DB::raw('COUNT(*) as total'))
            ->where('estado', 1)
            ->groupBy('cod_grado', 'seccion')
            ->get()
            ->groupBy('cod_grado')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return "{$item->seccion}: {$item->total}";
                })->toArray();
            })
            ->toArray(); // <- Esto para pasar limpio al JS

        
        return view('ReporteEvaluacionDiagnostica.reportediagnostica', compact(
            'session',
            'registros',
            'distritos',
            'instituciones',
            'niveles',
            'redes',
            'total_csv',
            'total_grados',
            'total_registrados',
            'promedio_avance',
            'avancePorGrado',
            'totalEsperado',
            'detalleSecciones' 
        ));
    }

    private function aplicarFiltrosComunes($query, $request)
    {
        if ($request->filled('codlocal')) {
            $query->where('R.codlocal', $request->codlocal);
        }

        if ($request->filled('codmod')) {
            $query->where('R.codmod', $request->codmod);
        }

        if ($request->filled('red')) {
            $query->where('R.red', $request->red);
        }

        if ($request->filled('institucion')) {
            $query->where('R.institucion', 'like', "%{$request->institucion}%");
        }

        if ($request->filled('nivel')) {
            $query->where('R.nivel', $request->nivel);
        }

        if ($request->filled('distrito')) {
            $query->where('R.distrito', $request->distrito);
        }
        if ($request->filled('filtro_avance')) {
            switch ($request->filtro_avance) {
                case '0%':
                    $query->having('porcentaje_avance', '=', 0);
                    break;
                case '1-20%':
                    $query->havingBetween('porcentaje_avance', [0.01, 20]);
                    break;
                case '21-60%':
                    $query->havingBetween('porcentaje_avance', [21, 60]);
                    break;
                case '61-100%':
                    $query->havingBetween('porcentaje_avance', [61, 100]);
                    break;
            }
        }

        return $query;
    }
}