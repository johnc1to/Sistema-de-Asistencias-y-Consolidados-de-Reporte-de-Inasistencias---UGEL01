<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directores extends Model
{
    use HasFactory;
   protected $connection = "bienes";
    protected $table = "directores";
    protected $fillable = ['nombre', 'cargo', 'dni', 'codlocal', 'estado'];
}
