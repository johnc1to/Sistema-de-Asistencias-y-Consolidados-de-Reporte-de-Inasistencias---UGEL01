<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form_equipos_informaticos extends Model
{
    use HasFactory;
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    public $timestamps = false;
    
    protected $connection = 'formularios';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'form_equipos_informaticos';

    //Etc...
}
