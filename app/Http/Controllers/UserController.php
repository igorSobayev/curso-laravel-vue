<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Persona;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            // uno con join las tablas, personas con proveedores y selecciono los datos con select
            $personas = User::join('personas', 'users.id', '=', 'personas.id')
            ->join('roles', 'users.idrol', '=', 'roles.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento',
            'personas.direccion', 'personas.telefono', 'personas.email', 'users.usuario', 'users.password',
            'users.condicion', 'users.idrol', 'roles.nombre as rol')
            ->orderBy('personas.id', 'desc')->paginate(5);
        } else {
            // Uno las tablas en el where porque es necesario que lleve el nombre de personas antes del criterio
            $personas = User::join('personas', 'users.id', '=', 'personas.id')
            ->join('roles', 'users.idrol', '=', 'roles.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento',
            'personas.direccion', 'personas.telefono', 'personas.email', 'users.usuario', 'users.password',
            'users.condicion', 'users.idrol', 'roles.nombre as rol')
            ->where('personas' . '.' . $criterio, 'LIKE', '%' . $buscar . '%')
            ->orderBy('personas.id', 'desc')->paginate(5);
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

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try {
            // Inicio una tarnsaccion
            DB::beginTransaction();
            // Sirve para guardar un objeto de la clase Persona
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();

            // Quiero guardar a la vez tanto a la persona como al proveedor,
            // por eso mismo primero guardo los datos de la persona y luego los utilizo
            // para la creación del proveedor en el cual recuperaré
            // la id del proveedor como la id de la persona creada anteriormente
            $user = new User();
            $user->usuario = $request->usuario;
            $user->password = bcrypt($request->password);
            $user->condicion = '1';
            $user->idrol = $request->idrol;
            $user->id = $persona->id;
            $user->save();

            // Hago commit
            DB::commit();

        } catch (Exception $e) {
            // En caso de que haya error hago un rollback
            DB::rollBack();
        }
        

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

        try {

            // Busco al proveedor
            $user = User::findOrFail($request->id);
            // Si lo encuentro hago la referencia a la clase persona a través del id
            $persona = Persona::findOrFail($user->id);

            // Empiezo a modificar los datos del proveedor y persona
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();


            $user->usuario = $request->usuario;
            $user->password = bcrypt($request->password);
            $user->condicion = '1';
            $user->idrol = $request->idrol;
            $user->id = $persona->id;
            $user->save();

            DB::commit();

        } catch (Exception $e) {
            // En caso de que haya error hago un rollback
            DB::rollBack();
        }
    }

    public function desactivar(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para desactivar la condición
        $user = User::findOrFail($request->id);
        $user->condicion = '0';
        $user->save();
    }

    
    public function activar(Request $request) 
    {
        if (!$request->ajax()) return redirect('/');
        // Sirve para activar la condición
        $user = User::findOrFail($request->id);
        $user->condicion = '1';
        $user->save();
    }
}
