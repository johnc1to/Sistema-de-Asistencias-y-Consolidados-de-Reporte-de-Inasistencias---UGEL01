<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iiee_a_evaluar_rie extends Model
{
    use HasFactory;
    /**
     * The database connection used by the model.
     *
     * @var string
     */

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'iiee_a_evaluar_RIE';
    protected $primaryKey = 'codmod';
    //Etc...
}
