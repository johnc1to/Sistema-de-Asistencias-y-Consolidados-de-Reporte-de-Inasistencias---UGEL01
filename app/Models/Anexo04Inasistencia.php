<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anexo04Inasistencia extends Model
{
    protected $table = 'anexo04_inasistencia';
    protected $connection = 'siic_anexos';
    protected $fillable = [
        'id_persona',
        'inasistencia',
        'detalle',
        'observacion',
        
    ];
    public $timestamps = false;
    protected $casts = [
        'inasistencia' => 'array',
        'detalle' => 'array',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Anexo04Persona::class, 'id_persona');
    }
    public function detalles()
{
    return $this->hasMany(Anexo04InasistenciaDetalle::class, 'id_inasistencia');
}

}
