<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correlativoprueba extends Model
{
    use HasFactory;
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'notificacionprueba';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'correlativo';
    //Etc...
}
