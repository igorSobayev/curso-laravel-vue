<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    //
    // Indicamos la tabla a la cual hace referencia el modelo
    protected $table = 'proveedores';
    protected $fillable = ['contacto', 'telefono_contacto'];

    public $timestamps = false;

    // Unimos la tabla proveedores con la tabla persona, lo mismo para persona
    public function persona() {
        return $this->belongsTo('App\Persona');
    }
}
