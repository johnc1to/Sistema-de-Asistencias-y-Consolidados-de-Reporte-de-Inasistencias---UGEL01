<?php

namespace App\Models\teletrabajo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JmmjPersonalTeletrabajo extends Model
{
    use HasFactory;
    protected $connection = 'sicab1';
    protected $table = 'jmmj_personal_teletrabajo';

    public function urlTeams()
    {
        return $this->hasOne(JmmjUrlTeam::class, 'id_area','id_area')->where("estado", 1);
    }
}
