<?php

namespace App\Models\bienes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;
    protected $connection = "bienes";
    protected $table = "movimiento";
}
