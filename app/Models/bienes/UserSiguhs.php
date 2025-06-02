<?php

namespace App\Models\bienes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSiguhs extends Model
{
    use HasFactory;
    protected $connection = "siguhs";
    protected $table = "users";
}
