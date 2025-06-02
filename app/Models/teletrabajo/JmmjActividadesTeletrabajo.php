<?php

namespace App\Models\teletrabajo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JmmjActividadesTeletrabajo extends Model
{
    use HasFactory;
    protected $connection = "sicab1";
    protected $table="jmmj_actividades_teletrabajo";
    protected $fillable =['id_usuario', 'id_usuario_cj', 'fecha_actividad', 'actividad', 'fecha_respuesta', 'respuesta', 'id_area', 'id_equipo', 'situacion', 'estado', 'flg'];
}
