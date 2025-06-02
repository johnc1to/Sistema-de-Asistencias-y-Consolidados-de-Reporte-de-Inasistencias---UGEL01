<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preguntas extends Model
{
    use HasFactory;
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'ficha';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'preguntas';
    //Etc...
}
