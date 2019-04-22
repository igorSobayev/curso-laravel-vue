<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('usuario', 191)->unique();
            $table->string('password', 191);
            $table->boolean('condicion')->default(1);
            $table->integer('idrol')->unsigned();

            $table->foreign('id')->references('id')->on('personas')->onDelete('cascade');
            $table->foreign('idrol')->references('id')->on('roles');

            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
