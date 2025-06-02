<?php

namespace App\Models\teletrabajo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WtsUsuario extends Model
{
    use HasFactory;
    protected $connection = 'sicab1';
    protected $primaryKey = 'idUsuario';

    public function personalTeletrabajo()
    {
        return $this->hasOne(JmmjPersonalTeletrabajo::class,  'id_usuario','idUsuario')->where("estado", 1);
    }
}
