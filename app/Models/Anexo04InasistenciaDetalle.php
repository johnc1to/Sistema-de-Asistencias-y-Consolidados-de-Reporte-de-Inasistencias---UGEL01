<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo04InasistenciaDetalle extends Model
{
    use HasFactory;

    protected $table = 'anexo04_inasistencia_detalle';

    protected $fillable = [
        'id_inasistencia',
        'fecha',
        'tipo',
        'horas',
        'minutos',
    ];

    // Relación con la tabla anexo04_inasistencia
    public function inasistencia()
    {
        return $this->belongsTo(Anexo04Inasistencia::class, 'id_inasistencia');
    }
}
