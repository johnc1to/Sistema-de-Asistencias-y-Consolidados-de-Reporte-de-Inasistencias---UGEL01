<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anexo03 extends Model
{
    protected $table = 'anexo03';

    protected $primaryKey = 'id_anexo03';

    protected $fillable = [
        'id_contacto',
        'codlocal',
        'fecha_creacion',
        'fecha_actualizacion'
    ];

    public $timestamps = false; // porque estamos manejando las fechas manualmente

    // Relación con contacto (director)
    public function contacto(): BelongsTo
    {
        return $this->belongsTo(Contacto::class, 'id_contacto');
    }

    // Relación con las personas del reporte
    public function personas(): HasMany
    {
        return $this->hasMany(Anexo03Persona::class, 'id_anexo03');
    }
}
