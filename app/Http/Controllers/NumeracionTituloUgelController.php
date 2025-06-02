<?php

namespace App\Http\Controllers;

use App\Models\AprobacionTituloAuxiliarTecnico;
use App\Models\Nexus;
use App\Models\NumeracionTituloUgel;
use App\Models\Titulos;
use Illuminate\Http\Request;

class NumeracionTituloUgelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $codMod = Titulos::selectRaw("pe.codModPee")->join("programas_estudio as pe","pe.idPro","=","titulos.idPro")
        ->where(["estTit"=>1,"estPee"=>1])->groupBy(["pe.codModPee"])->get()
        ->pluck("codModPee")->toArray();
        if(count($codMod)>0)
        {
           $colegio = Nexus::selectRaw("codmodce,nombie")->whereIn("codmodce",$codMod)->where("idnivel",1)
           ->groupBy(["codmodce","nombie"])->get()->pluck("nombie","codmodce")->toArray();
           return view("numeracion-ugel.numeracion",compact("colegio"));

        }
       // dd($codMod);
        return view("numeracion-ugel.numeracion");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $existeTitulo = Titulos::where(["idTit"=>$request->input("titulo_id"),
                                        "estTit"=>1])->firstOrFail();
            if($existeTitulo)
            {
                $existeTitulo->codRegUgelTit=$request->input("codigo_ugel");
                $existeTitulo->save();
                $comentario = AprobacionTituloAuxiliarTecnico::whereFlg(1)->whereEstado(1)
                ->whereTituloId($request->input("titulo_id"))->firstOrFail();
                if($comentario)
                {
                    $comentario->flg=2;
                    $comentario->save();
                }
                $data=["titulo_id"=>$request->input("titulo_id"),"especialista_id"=>$request->session()->get("siic01_admin")["idespecialista"],
                "situacion"=>4,"comentario"=>"Título Numerado por la UGEL 01"];
                $creado = AprobacionTituloAuxiliarTecnico::create($data);

                return response()->json(["mensaje"=>"Numeración guarda con éxito"]);
            }
            return response()->json(["mensaje"=>"El título seleccionado no ha sido encontrado"]);

        } catch (\Throwable $th) {
            return response()->json(["mensaje"=>"Ha ocurrido un error"]);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $pageNumber = $request->input("pageNumber");
            $pageSize = $request->input("pageSize");
            $limit = ($pageNumber-1)*$pageSize;
            $search = $request->input("searchText");
        // $limit1 = (($pageNumber*$pageSize)-$pageSize)+1;
        // echo $limit." - ".$limit1;exit();
            $listaTitulados = Titulos::selectRaw("atat.situacion,idTit,a.docAlu,a.tipDocAlu,concat(a.nomAlu,' ',a.apePatAlu,' ',a.apeMatAlu) as nombre,
            pe.nivForPee,pe.tipSerEduPee,pe.credPee,pe.creVirPee,pe.horPee,pe.perPee,codRegIeTit,
            rdExpTit,codRegUgelTit,fecEgrTit")->join("programas_estudio as pe","pe.idPro","=","titulos.idPro")
            ->join("alumnos as a","a.idAlu","=","titulos.idAlu")
            ->join("aprobacion_titulo_auxiliar_tecnicos as atat","atat.titulo_id","=","titulos.idTit")
            ->where(["estTit"=>1,"estPee"=>1,"estAlu"=>1,"atat.estado"=>1,"atat.flg"=>1])
            ->where("pe.codModPee",$request->input("codmod"))->whereIn("atat.situacion",[2,4]);
            $total=$listaTitulados->count();
            $totalNotFiltered=$listaTitulados->count();
            if($search!="")
            {
                $listaTitulados->where(function($query) use($search)
            {
            return  $query->orWhere('a.apePatAlu','LIKE', '%'.$search.'%')
            ->orWhere('a.apeMatAlu','LIKE', '%'.$search.'%')
                ->orWhere('a.nomAlu','LIKE', '%'.$search.'%')
                ->orWhere('a.docAlu','LIKE', '%'.$search.'%');
            });

                $total=$listaTitulados->count();
                $totalNotFiltered=$listaTitulados->count();
            }
            $dd=$listaTitulados->skip($limit)->take($pageSize)->get();



            return response()->json(["rows"=>$dd,"total"=>$total,"totalNotFiltered"=>$totalNotFiltered]);
        } catch (\Throwable $th) {
            dd($th);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NumeracionTituloUgel  $numeracionTituloUgel
     * @return \Illuminate\Http\Response
     */
    public function show(NumeracionTituloUgel $numeracionTituloUgel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NumeracionTituloUgel  $numeracionTituloUgel
     * @return \Illuminate\Http\Response
     */
    public function edit(NumeracionTituloUgel $numeracionTituloUgel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NumeracionTituloUgel  $numeracionTituloUgel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NumeracionTituloUgel $numeracionTituloUgel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NumeracionTituloUgel  $numeracionTituloUgel
     * @return \Illuminate\Http\Response
     */
    public function destroy(NumeracionTituloUgel $numeracionTituloUgel)
    {
        //
    }
}
