<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titulos extends Model
{
    use HasFactory;
    /**
    * The database connection used by the model.
    *
    * @var string
    */
   protected $connection = 'cetpromin';

   /**
   * The database table used by the model.
   *
   * @var string
   */
   protected $table = 'titulos';
   /**inicio jmmj 20-06-203 */
   protected $primaryKey = "idTit";
   /**fin jmmj 20-06-2023 */
   //Etc...
}
