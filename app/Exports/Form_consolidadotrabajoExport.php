<?php

namespace App\Exports;

use App\Models\Form_consolidadotrabajo;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
class Form_consolidadotrabajoExport implements FromCollection
{
    public $where=array();
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function collection()
    {
        return Form_consolidadotrabajo::where($this->where)->get();
       // return DB::connection('formularios')->select("SELECT*FROM form_consolidadotrabajo");
        /*return DB::connection('formularios')->select("SELECT 
            E.ddni,
            E.esp_nombres,
            E.esp_apellido_paterno,
            E.esp_apellido_materno,
            E.regimen_laboral,
            E.especialista_creo,
            E.idespecialista
            FROM siic01.especialistas E 
            LEFT JOIN siic01_formularios.form_consolidadotrabajo_dias D ON E.idespecialista = D.idespecialista and D.estado=1
            WHERE E.regimen_laboral <> 'PERMISO' and E.estado=1
            GROUP BY E.idespecialista");*/
    }
}
