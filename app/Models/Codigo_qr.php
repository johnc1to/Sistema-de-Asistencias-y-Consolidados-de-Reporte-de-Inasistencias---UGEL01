<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Codigo_qr extends Model
{
    use HasFactory;
     /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'Qr';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'codigo_qr';
}
