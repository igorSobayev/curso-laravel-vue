<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Articulo;

class ArticuloController extends Controller
{
    public function index(Request $request)
    {
        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            // Creo la unión con la tabla categorías, al join le paso el nombre de la tabla, luego el nombre de la clave ajena en mi
            // tabla Articulos y finalmente la igualo a la clave primaria de la tabla categorias
            // Hago un select de todos los campos deseados que van a estar ordenados por el id y paginados
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
            -> select('articulos.id', 'articulos.idcategoria', 'articulos.codigo',
             'articulos.nombre', 'categorias.nombre as nombre_categoria', 'articulos.precio_venta',
             'articulos.stock', 'articulos.descripcion', 'articulos.condicion')
            -> orderBy('articulos.id', 'desc')->  paginate(3);
        } else {
            // Implemento el filtro de busqueda para los articulos, cambio el where donde contaceno el criterio con la tabla
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
            -> select('articulos.id', 'articulos.idcategoria', 'articulos.codigo',
             'articulos.nombre', 'categorias.nombre as nombre_categoria', 'articulos.precio_venta',
             'articulos.stock', 'articulos.descripcion', 'articulos.condicion')
             -> where('articulos.' . $criterio, 'like', '%' . $buscar . '%')
             -> orderBy('articulos.id', 'desc')->paginate(3);

        }
        
        // Paginación con Eloquent, mucho más limpio
        
        
        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],
            'articulos'    => $articulos
        ];
        

        // Codigo inicial
        //$articulos = Articulo::all();
    }

    public function buscarArticulo(Request $request) {

        // if ($request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = Articulo::where('codigo', '=', $filtro)
        ->select('id', 'nombre')->orderBy('nombre', 'asc')->take(1)->get();

        return ['articulos' => $articulos];

    }

    public function listarArticulo(Request $request)
    {
        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            // Creo la unión con la tabla categorías, al join le paso el nombre de la tabla, luego el nombre de la clave ajena en mi
            // tabla Articulos y finalmente la igualo a la clave primaria de la tabla categorias
            // Hago un select de todos los campos deseados que van a estar ordenados por el id y paginados
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
            -> select('articulos.id', 'articulos.idcategoria', 'articulos.codigo',
             'articulos.nombre', 'categorias.nombre as nombre_categoria', 'articulos.precio_venta',
             'articulos.stock', 'articulos.descripcion', 'articulos.condicion')
            -> orderBy('articulos.id', 'desc')->  paginate(10);
        } else {
            // Implemento el filtro de busqueda para los articulos, cambio el where donde contaceno el criterio con la tabla
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
            -> select('articulos.id', 'articulos.idcategoria', 'articulos.codigo',
             'articulos.nombre', 'categorias.nombre as nombre_categoria', 'articulos.precio_venta',
             'articulos.stock', 'articulos.descripcion', 'articulos.condicion')
             -> where('articulos.' . $criterio, 'like', '%' . $buscar . '%')
             -> orderBy('articulos.id', 'desc')->paginate(10);

        }
        
        // Paginación con Eloquent, mucho más limpio
        
        
        return [
            'articulos'    => $articulos
        ];
        

        // Codigo inicial
        //$articulos = Articulo::all();
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para guardar un objeto de la clase Articulo
        $articulo = new Articulo();
        $articulo->idcategoria = $request->idcategoria;
        $articulo->codigo = $request->codigo;
        $articulo->nombre = $request->nombre;
        $articulo->precio_venta = $request->precio_venta;
        $articulo->stock = $request->stock;
        $articulo->descripcion = $request->descripcion;
        $articulo->condicion = '1';
        $articulo->save();

    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para actualizar el objeto categoria
        $articulo = Articulo::findOrFail($request->id);
        $articulo->idcategoria = $request->idcategoria;
        $articulo->nombre = $request->nombre;
        $articulo->codigo = $request->codigo;
        $articulo->precio_venta = $request->precio_venta;
        $articulo->stock = $request->stock;
        $articulo->descripcion = $request->descripcion;
        $articulo->condicion = '1';
        $articulo->save();
    }

    // Añadidos por mi

    public function desactivar(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para desactivar la condición
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '0';
        $articulo->save();
    }

    
    public function activar(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para activar la condición
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
}
