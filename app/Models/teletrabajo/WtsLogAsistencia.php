<?php

namespace App\Models\teletrabajo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WtsLogAsistencia extends Model
{
    use HasFactory;
    protected $connection = 'sicab1';
    protected $table = 'wts_log_asistencia';
    protected $primaryKey = 'idLogAsistencia';
    protected $fillable = [

        'dni',
        'entrada_salida',
        'hora',
        'fecha',
        'fechadate',
        'verificado',
        'status',
        'minutoTardanza',
        'estado',
        'WorkCode',
        'tipo_asistencia',
        'fecha_registro',
        'idUsuario',
        'fechaActualizacion'
    ];

    public $timestamps = false;
    //protected $dateFormat = 'Y-m-d H:i:s';
   
}
