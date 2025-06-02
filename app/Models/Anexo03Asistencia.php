<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anexo03Asistencia extends Model
{
    protected $table = 'anexo03_asistencia';

    protected $fillable = [
        'id_persona',
        'asistencia', // Este campo es JSON que almacena la asistencia diaria
        'tipo_observacion',
        'observacion',

    ];

    public $timestamps = false;

    // Relación con la persona
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Anexo03Persona::class, 'id_persona');
    }
}
