<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTableSituacionLaboral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("cetpromin")->create('situacion_laborals', function (Blueprint $table) {
            $table->bigIncrements("id")->comment("codigo principal de la tabla");
            $table->string("descripcion")->nullable()->comment("respuestas");
            $table->bigInteger("idAlu")->nullable()->comment("ide alumno");
            $table->bigInteger("idTit")->nullable()->comment("id Titulo");
            $table->bigInteger("idPro")->nullable()->comment("id programa estudio");
            $table->integer("flg")->default(1)->comment("determina el historico:2 y el actual:1");
            $table->boolean("estado")->default(1)->comment("Estado del registro en la tabla");
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment("Fecha de creacion del  menu usuario");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment("Fecha de actualizacion o modificacion del menu usuario");
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection("cetpromin")->dropIfExists('situacion_laborals');
    }
}
