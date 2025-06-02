<?php

namespace App\Models\bienes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BienTemporalDevolucion extends Model
{
    use HasFactory;
    protected $connection = "bienes";
    protected $table = "bien_temporal_devolucion";
    protected $fillable = ['correlativo_id', 'persona_id', 'numero_firma', 'estado',"tipo"];
    public $timestamps = false;
}
