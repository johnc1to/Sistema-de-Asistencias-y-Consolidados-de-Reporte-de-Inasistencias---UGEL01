<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restablecer_correo_director extends Model
{
    use HasFactory;
    /**
    * The database connection used by the model.
    *
    * @var string
    */
   protected $connection = 'mysql';

   /**
   * The database table used by the model.
   *
   * @var string
   */
   protected $table = 'restablecer_correo_director';
}
