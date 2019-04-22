<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // se utiliza para agregar nuevas tablas y nuevas columnas a la bd
    public function up()
    {
        // dos argumentos: nombre de la tabla en plural y el segundo una funcion
        // con el objeto $table
        // increments significa que será la clave primaria de la tabla
        // string es para los campos varchar y el número equivale al valor máximo
        //  nullable() significa que puede ser null
        // boolean para los de tipo boolean y default el valor por defecto
        Schema::create('categorias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 50);
            $table->string('descripcion', 256)->nullable();
            $table->boolean('condicion')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // revierte realizado por up
    public function down()
    {
        Schema::dropIfExists('categorias');
    }
}
