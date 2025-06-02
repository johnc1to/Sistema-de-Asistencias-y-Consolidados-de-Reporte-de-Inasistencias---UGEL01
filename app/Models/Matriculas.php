<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matriculas extends Model
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
   protected $table = 'matriculas';
   //Etc...

   /**inicio jmmj 21-06-2023 */
    protected $primaryKey = "idMat";

    public function Alumno()
    {
        return $this->hasOne(Alumnos::class,"idAlu","idAlu");
    }

    public function OfertaFormativa()
    {
        return $this->hasOne(Ofertas_formativas::class,"idOff","idOff");
    }
    
   /**fin jmmj 21-06-2023 */

}
