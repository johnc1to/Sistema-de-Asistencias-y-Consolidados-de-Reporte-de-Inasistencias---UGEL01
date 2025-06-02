<?php

namespace App\Models\bienes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bienes extends Model
{
    use HasFactory;
    protected $connection = "bienes";
    protected $table = "bienes";
    protected $fillable = ["id",
    "id_sede",
                            "id_marca",
                            "id_modelo",
                            'serie',
                            'id_orden',
                            "serie_actual",
                            "serie_anterior",
                            'serie_patrimonial',
                            'codigo_patrimonial',
                            'id_area',
                            'id_equipo',
                            "id_pecosa",
                            "id_documentos",
                            'situacion',
                            'condicion',
                            'id_movimiento',
                            "tipo",
                            'color',
                            'estado_garantia',
                            'observacion',
                            'dimension',"fecha_creacion","correlativo","id_persona","id_tercero","usuario_creador", "flg_no_encontrado","flg",
                            "id_empresa","fecha_retorno","id_documentos_sustentos","sustento","fecha_modificacion"

                        ];
    public $timestamps = false;
    public function tipobien()
    {
        return $this->hasOne(TipoBien::class,"id","tipo")->select(['id', 'descripcion'])->withDefault([
            'descripcion' => 'S/N'
        ]);
    }

    public function marca()
    {
        return $this->hasOne(Marca::class,"id","id_marca")->selectRaw('id, UPPER(descripcion) as descripcion')->withDefault([
            'descripcion' => 'S/M'
        ]);
    }

    public function modelo()
    {
        return $this->hasOne(Modelo::class,"id","id_modelo")->selectRaw('id, UPPER(descripcion) as descripcion')->withDefault([
            'descripcion' => 'S/M'
        ]);
    }

    public function Sede()
    {
        return $this->hasOne(Sede::class,"id","id_sede")->select(['id', 'descripcion'])->withDefault([
            'descripcion' => 'S/N'
        ]);
    }

    public function condicionDescipcion()
    {
        return $this->hasOne(Condicion::class,"id","condicion")->select(['id', 'descripcion'])->withDefault([
            'descripcion' => 'S/N'
        ]);
    }

    public function TerceroPracticante()
    {
        return $this->hasOne(Persona::class,"id","id_tercero")->selectRaw('id, UPPER(NocPer) as NocPer')->withDefault([
            'NocPer' => 'S/M'
        ]);
    }

    public function equipo()
    {
        return $this->hasOne(Organigrama::class,"id","id_equipo")->select(['id', 'DesOrg'])->withDefault([
            'DesOrg' => 'S/M'
        ]);
    }
}
