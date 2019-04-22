<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Importo los facades db para la transacción
use Illuminate\Support\Facades\DB;

use App\Proveedor;
use App\Persona;



class ProveedorController extends Controller
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
            $personas = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento',
            'personas.direccion', 'personas.telefono', 'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
            ->orderBy('personas.id', 'desc')->paginate(5);
        } else {
            // Uno las tablas en el where porque es necesario que lleve el nombre de personas antes del criterio
            $personas = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento',
            'personas.direccion', 'personas.telefono', 'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
            ->where('personas' . '.' . $criterio, 'like', '%' . $buscar . '%')
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

    public function selectProveedor(Request $request) {
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $proveedores = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
        ->where('personas.nombre', 'like', '%' . $filtro . '%')
        ->orWhere('personas.num_documento', 'like', '%' . $filtro . '%')
        ->select('personas.id', 'personas.nombre', 'personas.num_documento')
        ->orderBy('personas.nombre', 'asc')->get();

        return ['proveedores' => $proveedores];
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
            $proveedor = new Proveedor();
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->id = $persona->id;
            $proveedor->save();

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
            $proveedor = Proveedor::findOrFail($request->id);
            // Si lo encuentro hago la referencia a la clase persona a través del id
            $persona = Persona::findOrFail($proveedor->id);

            // Empiezo a modificar los datos del proveedor y persona
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();


            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->save();

            DB::commit();

        } catch (Exception $e) {
            // En caso de que haya error hago un rollback
            DB::rollBack();
        }
    }

}
