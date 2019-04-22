<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    //
    protected $fillable = [ 'nombre', 'tipo_documento', 'num_documento', 'direccion', 'telefono', 'email'];

    public function proveedor() {
        // Una persona esta relacionada con un solo proveedor
        return $this->hasOne('App\Proveedor');
    }

    public function user() {
        return $this->hasOne('App\User');
    }
}
