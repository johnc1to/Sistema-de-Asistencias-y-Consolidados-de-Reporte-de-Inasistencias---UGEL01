<?php

namespace App\Models\bienes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienesMovimiento extends Model
{
    use HasFactory;
    protected $connection = "bienes";
    protected $table = "bienes_movimiento1";

    /**inicio jmmj 27-01-2025  */

    public function Movimiento1()
    {
        return $this->hasOne(Movimiento::class,"id","id_movimiento")->select(['id', 'descripcion'])->withDefault([
            'descripcion' => 'S/N'
        ]);
    }

    public function firmas()
    {
        return $this->hasOne(PdfFirmaDesplazamiento::class,"correlativo_id","correlativo");
    }
    /**fin jmmj 27-01-2025  */
}
