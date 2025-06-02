<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToAnexo03ConfiguracionDiasAsistencia extends Migration
{
    public function up()
    {
        Schema::table('anexo03_configuracion_dias_asistencia', function (Blueprint $table) {
            $table->timestamps(); // Esto agregará las columnas 'created_at' y 'updated_at'
        });
    }

    public function down()
    {
        Schema::table('anexo03_configuracion_dias_asistencia', function (Blueprint $table) {
            $table->dropTimestamps(); // Esto eliminará las columnas 'created_at' y 'updated_at'
        });
    }
}
