<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionDiasAsistencia extends Model
{
    protected $table = 'anexo03_configuracion_dias_asistencia';

    protected $fillable = [
        'id_persona',
        'dias_laborables',
    ];

    protected $casts = [
        'dias_laborables' => 'array',
    ];

    public $timestamps = true;

    // Si deseas la relación con el modelo de persona:
    public function persona()
    {
        return $this->belongsTo(Anexo03Persona::class, 'id_persona');
    }
}
