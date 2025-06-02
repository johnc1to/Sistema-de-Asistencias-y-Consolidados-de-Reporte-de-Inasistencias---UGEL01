<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encargatura_tba_usuario extends Model
{
    use HasFactory;
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'encargatura';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'tba_usuario';

    //Etc...

}
