<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // Estos campos ya estan definidos por defecto en Laravel

    // nombre de la tabla en la bd
    // protected $table = 'categorias';

    // nombre de la primaryKey
    // protected $primaryKey = 'id';

    // columnas a las que vamos estar enviando valores
    protected $fillable = ['nombre', 'descripcion', 'condicion'];

    // Relacionamos la tabla artículos con la tabla categorías, esto indica que la tabla
    // artículos puede tener varias categorías
    public function articulos() {
        return $this->hasMany('App\Articulo');
    }
}
