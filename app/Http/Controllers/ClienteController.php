<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;

class ClienteController extends Controller
{
    //

    public function index(Request $request)
    {
        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $personas = Persona::orderBy('id', 'desc')->paginate(5);
        } else {
            $personas = Persona::where($criterio, 'like', '%' . $buscar . '%')->orderBy('id', 'desc')->paginate(5);
        }
        
        // paginación con query builder, para esto hace falta importar 'use Illuminate\Support\Facades\DB;'
        //$personas = DB::table('personas')->paginate(2);
        
        // Paginación con Eloquent, mucho más limpio
        
        
        return [
            'pagination' => [
                'total'         => $personas->total(),
                'current_page'  => $personas->currentPage(),
                'per_page'      => $personas->perPage(),
                'last_page'     => $personas->lastPage(),
                'from'          => $personas->firstItem(),
                'to'            => $personas->lastItem(),
            ],
            'personas'    => $personas
        ];
        

        // Codigo inicial
        //$categorias = Categoria::all();
    }

    public function selectCliente(Request $request) {
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $clientes = Persona::where('nombre', 'like', '%' . $filtro . '%')
        ->orWhere('num_documento', 'like', '%' . $filtro . '%')
        ->select('id', 'nombre', 'num_documento')
        ->orderBy('nombre', 'asc')->get();

        return ['clientes' => $clientes];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para guardar un objeto de la clase Persona
        $persona = new Persona();
        $persona->nombre = $request->nombre;
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        $persona->save();

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
        $persona = Persona::findOrFail($request->id);
        $persona->nombre = $request->nombre;
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        $persona->save();
    }
}
