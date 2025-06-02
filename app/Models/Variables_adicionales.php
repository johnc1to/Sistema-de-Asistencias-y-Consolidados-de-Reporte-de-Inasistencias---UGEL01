<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variables_adicionales extends Model
{
    use HasFactory;
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    public $timestamps = false;
    
    protected $connection = 'ficha';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'variables_adicionales';
    //Etc...
}
