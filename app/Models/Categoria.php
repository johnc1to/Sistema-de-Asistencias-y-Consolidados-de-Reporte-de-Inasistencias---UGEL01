<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    /**
    * The database connection used by the model.
    *
    * @var string
    */
   protected $connection = 'buenaspracticas';

   /**
   * The database table used by the model.
   *
   * @var string
   */
   protected $table = 'categorias';
}