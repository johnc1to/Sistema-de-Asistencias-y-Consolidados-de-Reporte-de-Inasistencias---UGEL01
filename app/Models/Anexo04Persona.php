<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anexo04Persona extends Model
{
    protected $table = 'anexo04_persona';
    protected $connection = 'siic_anexos';

    protected $fillable = [
        'id_anexo04',
        'persona_json',
    ];
    public $timestamps = false;
    protected $casts = [
        'persona_json' => 'array',
    ];

    public function anexo04(): BelongsTo
    {
        return $this->belongsTo(Anexo04::class, 'id_anexo04');
    }

    public function inasistencia(): HasOne
    {
        return $this->hasOne(Anexo04Inasistencia::class, 'id_persona');
    }
}
