<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    //
    protected $fillable = [
        'idcategoria', 'codigo', 'nombre', 'precio_venta', 'stock', 'descripcion', 'condicion'
    ];

    // Creamos la relación de la tabla artículos con la tabla categorías
    // Esto significa que un artículo pertenece a una única categoría
    public function categoria() {
        return $this->belongsTo('App\Categoria');
    }
}
