<?php

namespace App\Http\Controllers;

use App\Models\AprobacionTituloAuxiliarTecnico;
use App\Models\Nexus;
use App\Models\Titulos;
use App\Models\Iiee_a_evaluar_rie;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;


class AprobacionTituloAuxiliarTecnicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index():View
    {
       $codMod = Titulos::selectRaw("pe.codModPee")->join("programas_estudio as pe","pe.idPro","=","titulos.idPro")
        ->where(["estTit"=>1,"estPee"=>1])->groupBy(["pe.codModPee"])->get()
        ->pluck("codModPee")->toArray();
        if(count($codMod)>0)
        {
           $colegio = Nexus::selectRaw("codmodce,nombie")->whereIn("codmodce",$codMod)->where("idnivel",1)
           ->groupBy(["codmodce","nombie"])->get()->pluck("nombie","codmodce")->toArray();
           return view("titulos.AprobacionEspecialistaUgel",compact("colegio"));

        }
       // dd($codMod);
        return view("titulos.AprobacionEspecialistaUgel");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function titulo(Request $request)
    {
        $id=$request->input("idTit");
        return view("titulos.ModalPdf",compact("id"));
        //Muestra una vista de https://siapcetpro.ugel01.gob.pe/public/pdf_titulo?idTit=
    }
    public function saveObservacionTitulo(Request $request)
    {
        //`titulo_id`, `especialista_id`, `situacion`, `comentario`, `flg`
        $existe = AprobacionTituloAuxiliarTecnico::where(["titulo_id"=>$request->input("idTit"),"estado"=>1,"flg"=>1])->first();
        if($existe)
        {
            if($existe->situacion==4)
            {
                return response()->json(["estado"=>0, "mensaje"=>"El título ya se encuentra numerado por la UGEL 01, no se puede observar"]);
            }
            $historico = AprobacionTituloAuxiliarTecnico::findOrFail($existe->id);
            $historico->flg=2;
            $historico->save();
        }

        $data=["titulo_id"=>$request->input("idTit"),"especialista_id"=>$request->session()->get("siic01_admin")["idespecialista"],
                "situacion"=>3,"comentario"=>$request->input("observacion")];
        $creado = AprobacionTituloAuxiliarTecnico::create($data);

        if($creado)
        {
            return response()->json(["estado"=>1, "mensaje"=>"Observacion exitosa"]);
        }else{
            return response()->json(["estado"=>0, "mensaje"=>"Observacion no guardada"]);

        }
       // dd($request->session()->get("siic01_admin")["idespecialista"]);
    }

    public function saveAprobarTitulo(Request $request)
    {
        $existe = AprobacionTituloAuxiliarTecnico::where(["titulo_id"=>$request->input("idTit"),"estado"=>1,"flg"=>1])->first();
        if($existe)
        {
            if($existe->situacion==4)
            {
                return response()->json(["estado"=>0, "mensaje"=>"El título ya se encuentra numerado por la UGEL 01, no se puede volver aprobar"]);
            }
            $historico = AprobacionTituloAuxiliarTecnico::findOrFail($existe->id);
            $historico->flg=2;
            $historico->save();
        }

        $data=["titulo_id"=>$request->input("idTit"),"especialista_id"=>$request->session()->get("siic01_admin")["idespecialista"],
                "situacion"=>2,"comentario"=>"Título aprobado"];
        $creado = AprobacionTituloAuxiliarTecnico::create($data);

        if($creado)
        {
            return response()->json(["estado"=>1, "mensaje"=>"Aprobación exitosa"]);
        }else{
            return response()->json(["estado"=>0, "mensaje"=>"Aprobación no guardada"]);

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
        $listaTitulados = Titulos::selectRaw("idTit,a.docAlu,a.tipDocAlu,concat(a.nomAlu,' ',a.apePatAlu,' ',a.apeMatAlu) as nombre,
        pe.nivForPee,pe.tipSerEduPee,pe.credPee,pe.creVirPee,pe.horPee,pe.perPee,codRegIeTit,
        rdExpTit,codRegUgelTit,fecEgrTit")->join("programas_estudio as pe","pe.idPro","=","titulos.idPro")
        ->join("alumnos as a","a.idAlu","=","titulos.idAlu")
        ->where(["estTit"=>1,"estPee"=>1,"estAlu"=>1])->where("pe.codModPee",$request->input("codmod"));
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

        foreach ($dd as $key => $value) {
           $dd[$key]->situacion = $this->situacion($value->idTit);
        }

        return response()->json(["rows"=>$dd,"total"=>$total,"totalNotFiltered"=>$totalNotFiltered]);
        } catch (\Throwable $th) {
            dd($th);
        }


    }

    protected function situacion($idTit)
    {
        return AprobacionTituloAuxiliarTecnico::where(["titulo_id"=>$idTit,"flg"=>1,"estado"=>1])->first();
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AprobacionTituloAuxiliarTecnico  $aprobacionTituloAuxiliarTecnico
     * @return \Illuminate\Http\Response
     */
    public function show(AprobacionTituloAuxiliarTecnico $aprobacionTituloAuxiliarTecnico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AprobacionTituloAuxiliarTecnico  $aprobacionTituloAuxiliarTecnico
     * @return \Illuminate\Http\Response
     */
    public function edit(AprobacionTituloAuxiliarTecnico $aprobacionTituloAuxiliarTecnico)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AprobacionTituloAuxiliarTecnico  $aprobacionTituloAuxiliarTecnico
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AprobacionTituloAuxiliarTecnico $aprobacionTituloAuxiliarTecnico)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AprobacionTituloAuxiliarTecnico  $aprobacionTituloAuxiliarTecnico
     * @return \Illuminate\Http\Response
     */
    public function destroy(AprobacionTituloAuxiliarTecnico $aprobacionTituloAuxiliarTecnico)
    {
        //
    }
}
