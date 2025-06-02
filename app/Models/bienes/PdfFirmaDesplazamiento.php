<?php

namespace App\Models\bienes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfFirmaDesplazamiento extends Model
{
    use HasFactory;
    protected $connection = "bienes";
    protected $table = "pdf_firma_desplazamiento";
    protected $fillable = ['correlativo_id', 'persona_id', 'numero_firma', 'estado',"tipo"];
}
