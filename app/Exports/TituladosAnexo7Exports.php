<?php

namespace App\Exports;

use App\Models\Iiee_a_evaluar_rie;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
class TituladosAnexo7Exports implements FromView
{

    public $codmod;
    public $anio;

    public function __construct($codmod,$anio)
    {
        $this->codmod = $codmod;
        $this->anio = $anio;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function data()
    {
        $reporte["iiee"]     = Iiee_a_evaluar_rie::where(['estado'=>1,'codmod'=>$this->codmod])->get()->first();
        $reporte["report"]  = DB::connection('cetpromin')->select("SELECT
        A.tipDocAlu,
        A.docAlu,
        CONCAT(A.apePatAlu,' ',A.apeMatAlu,', ',A.nomAlu) as estudiante,
        A.sexAlu,
        DATE_FORMAT(A.fecNacAlu,'%d/%m/%Y') as fecNacAlu,
        P.proEstPee,
        P.credPee,
        T.cantModTit,
        P.tipSerEduPee,
        DATE_FORMAT(T.fecEgrTit,'%d/%m/%Y') as fecEgrTit,
        T.codRegIeTit,
        T.rdExpTit,
        T.codRegUgelTit
        FROM titulos T
        INNER JOIN alumnos            A ON T.idAlu=A.idAlu
        INNER JOIN programas_estudio  P ON T.idPro=P.idPro
        WHERE T.estTit=1 and  P.codModPee='".$this->codmod."' and T.rdExpTit <>'' and YEAR(T.fecEgrTit)='".$this->anio."'");
        $reporte["anio"] = $this->anio;
        return $reporte;
    }




    public function view(): View
    {
        //dd($this->data()["report"][0]->tipDocAlu);
        return view('exports.tituladosAnexo7', [
            'data' =>$this->data()
        ]);
    }
}
