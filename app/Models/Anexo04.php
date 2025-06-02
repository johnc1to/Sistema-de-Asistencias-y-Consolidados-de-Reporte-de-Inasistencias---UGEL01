<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anexo04 extends Model
{
    protected $table = 'anexo04';
    protected $connection = 'siic_anexos';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_contacto',
        'codlocal',
        'nivel',
        'mes',
        'anio',
        'firma',
        'fecha_creacion',
        'fecha_actualizacion',
    ];
    public $timestamps = true;
    public function contacto(): BelongsTo
    {
        return $this->belongsTo(Contacto::class, 'id_contacto');
    }

    public function personas(): HasMany
    {
        return $this->hasMany(Anexo04Persona::class, 'id_anexo04');
    }
}
