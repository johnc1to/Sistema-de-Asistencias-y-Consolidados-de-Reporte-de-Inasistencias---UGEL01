<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material_educativo extends Model
{
    use HasFactory;
        /**
    * The database connection used by the model.
    *
    * @var string
    */
   protected $connection = 'formularios';

   /**
   * The database table used by the model.
   *
   * @var string
   */
   protected $table = 'material_educativo';
}
