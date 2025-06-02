<?php

namespace App\Models\bienes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $connection = "bienes";
    protected $table = "marca";
}
