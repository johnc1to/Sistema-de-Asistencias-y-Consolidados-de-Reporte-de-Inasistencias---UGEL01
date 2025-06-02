<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wts_log_asistencia extends Model
{
    use HasFactory;
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'sicab';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'wts_log_asistencia';
    //Etc...
}
