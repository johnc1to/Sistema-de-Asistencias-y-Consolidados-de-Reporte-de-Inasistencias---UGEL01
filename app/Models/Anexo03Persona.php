<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anexo03Persona extends Model
{
    protected $table = 'anexo03_persona';

    protected $fillable = [
        'id_anexo03',
        'persona_json' // Aquí almacenamos los datos del trabajador como JSON
    ];

    public $timestamps = false;

    // Relación con el reporte (Anexo03)
    public function anexo(): BelongsTo
    {
        return $this->belongsTo(Anexo03::class, 'id_anexo03');
    }

    // Relación con la asistencia
    public function asistencia(): HasOne
    {
        return $this->hasOne(Anexo03Asistencia::class, 'id_persona');
    }
}
