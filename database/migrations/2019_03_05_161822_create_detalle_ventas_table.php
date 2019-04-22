<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idventa')->unsigned();
            $table->integer('idarticulo')->unsigned();
            $table->integer('cantidad')->unsigned();;
            $table->decimal('precio', 11, 2);
            $table->decimal('descuento', 11, 2);

            $table->foreign('idarticulo')->references('id')->on('articulos');
            $table->foreign('idventa')->references('id')->on('ventas')->onDelete('cascade');



            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_ventas');
    }
}
