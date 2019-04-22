<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rol;

class RolController extends Controller
{
    //
    public function index(Request $request)
    {
        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $roles = Rol::orderBy('id', 'desc')->paginate(3);
        } else {
            $roles = Rol::where($criterio, 'like', '%' . $buscar . '%')->orderBy('id', 'desc')->paginate(3);
        }
        
        // paginación con query builder, para esto hace falta importar 'use Illuminate\Support\Facades\DB;'
        //$categorias = DB::table('categorias')->paginate(2);
        
        // Paginación con Eloquent, mucho más limpio
        
        
        return [
            'pagination' => [
                'total'         => $roles->total(),
                'current_page'  => $roles->currentPage(),
                'per_page'      => $roles->perPage(),
                'last_page'     => $roles->lastPage(),
                'from'          => $roles->firstItem(),
                'to'            => $roles->lastItem(),
            ],
            'roles'    => $roles
        ];
        
    }

    public function selectRol(Request $request) {
        $roles = Rol::where('condicion', '=', '1')
        ->select('id', 'nombre')
        ->orderBy('nombre', 'asc')->get();

        return ['roles' => $roles];
    }
}
