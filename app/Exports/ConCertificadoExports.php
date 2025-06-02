<?php

namespace App\Exports;

use App\Models\Matriculas;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ConCertificadoExports implements FromView,WithCustomStartCell,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function data()
    {
        return  Matriculas::selectRaw("codMat,fecMat,a.tipDocAlu,a.docAlu,a.nomAlu,a.apePatAlu,a.apeMatAlu,a.fecNacAlu,a.sexAlu,
        of.codModOff,of.perOff, of.turOff,pe.proEstPee,pe.tipSerEduPee,pe.credPee,pe.horPee")
        ->join("alumnos as a","a.idAlu","=","matriculas.idAlu")
        ->join("ofertas_formativas as of","of.idOff","=","matriculas.idOff")
        ->join("programas_estudio as pe","pe.idPro","=","of.idPro")
        ->where("estMat",1) ->where("estOff",1) ->where("estPee",1)->whereNotNull("idCmm")
        ->where("estAlu",1)->orderBy("a.idAlu","ASC")->get();
    }




    public function view(): View
    {
        return view('exports.matriculados', [
            'data' =>$this->data()
        ]);
    }


    public function startCell(): string
    {
        return 'B2';
    }
    public function headings(): array
    {
        return [
            'Cod. Matricula',
            'Fec. Matricula',
            'Tip. Doc.',
            'N° Doc.',
            'Nombre',
            'Ape. Paterno',
            'Ape. Materno',
            'Fec. Nacimiento',
            'Sexo(F/M)',
            'Cod. Modular Cetpro',
            'Periodo',
            'Turno',
            'Prog. Estudio',
            'Tip. Servicio Educ.',
            'N° creditos',
            'N° Horas'
        ];
    }
}
