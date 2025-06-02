<?php

namespace App\Http\Controllers;

use App\Exports\ConCertificadoExports;
use App\Exports\ConTituloExports;
use App\Exports\MatriculadosExports;
use App\Exports\TituladosAnexo7Exports;
use App\Models\Matriculas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ruta= route("exportExcel");
        return view("reports.matriculados",compact("ruta"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         // SELECT m.idMat,m.codMat,m.fecMat,a.tipDocAlu,a.docAlu,a.nomAlu,a.apePatAlu,a.apeMatAlu,of.codModOff,of.perOff,
        //of.turOff,pe.nivForPee,pe.tipSerEduPee,pe.credPee,pe.horPee  FROM `matriculas` as m
        // JOIN alumnos as a on a.idAlu=m.idAlu
        // JOIN ofertas_formativas of on of.idOff=m.idOff
        // join programas_estudio as pe on pe.idPro=of.idPro
        // WHERE `estMat` = 1 and estOff=1 and estPee=1;
        $pageNumber = $request->input("pageNumber");
        $pageSize = $request->input("pageSize");
        $limit = ($pageNumber-1)*$pageSize;
        $search = $request->input("searchText");
        $periodo = $request->input("periodo");

        if($request->has("offset"))
        {
            $limit=$request->input("offset");
            $search = $request->input("search");
        }

        if($request->has("limit"))
        {
            $pageSize=$request->input("limit");
            $search = $request->input("search");
        }
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $ruta= route("exportExcel");
//dd($limit." - ".$pageSize);
        $reporte= Matriculas::selectRaw("pe.perPee,idTit,a.idAlu,pe.idPro,codMat,fecMat,a.tipDocAlu,a.fecNacAlu,a.sexAlu,a.docAlu,a.nomAlu,a.apePatAlu,a.apeMatAlu,of.codModOff,of.perOff,
        of.turOff,pe.proEstPee,pe.tipSerEduPee,pe.credPee,pe.horPee")
        ->join("alumnos as a","a.idAlu","=","matriculas.idAlu")
        ->join("ofertas_formativas as of","of.idOff","=","matriculas.idOff")
        ->join("programas_estudio as pe","pe.idPro","=","of.idPro")

        ;


        if($request->input("codmod")==4)
        {
            $reporte->join("registro_notas as rn","rn.idMat","=","matriculas.idMat");

           // $ruta= route("exportsCertificados");

        }

        $reporte->where("estMat",1) ->where("estOff",1) ->where("estPee",1)->where("estAlu",1)
        //->where("pe.CodModPee","0694646")

        ->where("perOff",$periodo);
        if($request->input("codmod")==2)
        {
            $reporte->whereNotNull("matriculas.idCmm");
            $ruta= route("exportsCertificados");

        }
        if($request->input("codmod")==3)
        {
            $reporte->whereNotNull("matriculas.idTit");
        }

        if($request->input("codmod")==3)
        {
            $ruta= route("exportsTitulados");
            $reporte->groupBy(["matriculas.idTit"]);
           // dd();
        }
        if($request->input("codmod")==4)
        {
            $reporte->whereRaw("rn.notaReg>=12.5");
            $reporte->groupBy(["rn.idMat"]);
           // $ruta= route("exportsCertificados");

        }
       // $orden=$reporte->orderBy("a.idAlu","ASC");

       $total=$reporte->get()->count();
       $totalNotFiltered=$reporte->get()->count();


       if($search!="")
        {
            $reporte->where(function($query) use($search)
            {
            return  $query->orWhere('nomAlu','LIKE', '%'.$search.'%')
            ->orWhere('a.apeMatAlu','LIKE', '%'.$search.'%')
            ->orWhere('a.nomAlu','LIKE', '%'.$search.'%')
            ->orWhere('a.docAlu','LIKE', '%'.$search.'%');
            });

            if($request->input("codmod")==3)
            {
                $reporte->groupBy(["matriculas.idTit"]);
                $ruta= route("exportsTitulados");

            }

            if($request->input("codmod")==2)
            {

                $ruta= route("exportsCertificados");

            }

            if($request->input("codmod")==4)
            {
                $reporte->whereRaw("rn.notaReg>=12.5");
                $reporte->groupBy(["rn.idMat"]);
            // $ruta= route("exportsCertificados");

            }
                $total=$reporte->count();
                $totalNotFiltered=$reporte->count();
        }

           $limites= $reporte->skip($limit)->take($pageSize);
           $dd=$limites->get();

        //dd($dd->toArray());
       return response()->json(["rows"=>$dd,"total"=>$total,"totalNotFiltered"=>$totalNotFiltered,"ruta"=>$ruta]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return Excel::download(new MatriculadosExports, 'Matriculados.xlsx');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportsTitulados()
    {
        return Excel::download(new ConTituloExports, 'Titulados.xlsx');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportsCertificados()
    {
        return Excel::download(new ConCertificadoExports, 'ConCertificados.xlsx');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportsTituladosAnexo7(Request $request)
    {
        $codmod = $request['codmod'];
        $anio   = $request['anio'];
        return Excel::download(new TituladosAnexo7Exports($codmod,$anio), 'TituladosAnexo7A.xlsx');

    }
}
