<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categoria;


class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $categorias = Categoria::orderBy('id', 'desc')->paginate(3);
        } else {
            $categorias = Categoria::where($criterio, 'like', '%' . $buscar . '%')->orderBy('id', 'desc')->paginate(3);
        }
        
        // paginación con query builder, para esto hace falta importar 'use Illuminate\Support\Facades\DB;'
        //$categorias = DB::table('categorias')->paginate(2);
        
        // Paginación con Eloquent, mucho más limpio
        
        
        return [
            'pagination' => [
                'total'         => $categorias->total(),
                'current_page'  => $categorias->currentPage(),
                'per_page'      => $categorias->perPage(),
                'last_page'     => $categorias->lastPage(),
                'from'          => $categorias->firstItem(),
                'to'            => $categorias->lastItem(),
            ],
            'categorias'    => $categorias
        ];
        

        // Codigo inicial
        //$categorias = Categoria::all();
    }

    // Función que nos devuelve todas las categorías activas
    public function selectCategoria(Request $request) {

        if (!$request->ajax()) return redirect('/');

        $categorias = Categoria::where('condicion', '=', '1')
        -> select('id', 'nombre')->orderBy('nombre', 'asc')->get();

        return ['categorias' => $categorias];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para guardar un objeto de la clase Categoria
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = '1';
        $categoria->save();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para actualizar el objeto categoria
        $categoria = Categoria::findOrFail($request->id);
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = '1';
        $categoria->save();
    }

    // Añadidos por mi

    public function desactivar(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para desactivar la condición
        $categoria = Categoria::findOrFail($request->id);
        $categoria->condicion = '0';
        $categoria->save();
    }

    
    public function activar(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para activar la condición
        $categoria = Categoria::findOrFail($request->id);
        $categoria->condicion = '1';
        $categoria->save();
    }

}
