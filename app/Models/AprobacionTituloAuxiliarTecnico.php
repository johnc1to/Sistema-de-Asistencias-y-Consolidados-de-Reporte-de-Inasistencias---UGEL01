<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprobacionTituloAuxiliarTecnico extends Model
{
    protected $connection ="cetpromin";
    use HasFactory;
    protected $fillable =['titulo_id', 'especialista_id', 'situacion', 'comentario', 'flg'];
}
