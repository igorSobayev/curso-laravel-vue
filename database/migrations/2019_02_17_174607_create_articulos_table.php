<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idcategoria')->unsigned();
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 100)->unique();
            $table->decimal('precio_venta', 11, 2);
            $table->integer('stock');
            $table->string('descripcion', 256)->nullable();
            $table->boolean('condicion')->default(1);
            $table->timestamps();
            // Clave ajena de la tabla categorias, esto quiere decir que un artículo
            // tendrá en su tabla un campo categorias, foreing('idcategoria') es el campo en ESTA tabla
            // reference('id') es el nombre del campo en la OTRA tabla
            // on('categorias) nombre de la OTRA tabla
            $table->foreign('idcategoria')->references('id')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}
