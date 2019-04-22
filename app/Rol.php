<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    //
    // Este modelo hace referencia a la tabla roles
    protected $table = 'roles';
    protected $fillable = ['nombre', 'descripcion', 'condicion'];
    public $timestamp = false;

    public function users() {
        return $this->hasMany('App\User');
    }
}
