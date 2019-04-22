<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ingreso;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\DetalleIngreso;


class IngresoController extends Controller
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
            $ingresos = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
                ->join('users', 'ingresos.idusuario', '=', 'users.id')
                ->select(
                    'ingresos.id',
                    'ingresos.tipo_comprobante',
                    'ingresos.serie_comprobante',
                    'ingresos.num_comprobante',
                    'ingresos.fecha_hora',
                    'ingresos.impuesto',
                    'ingresos.total',
                    'ingresos.estado',
                    'personas.nombre',
                    'users.usuario'
                )
                ->orderBy('ingresos.id', 'desc')->paginate(5);
        } else {
            // Uno las tablas en el where porque es necesario que lleve el nombre de personas antes del criterio
            $ingresos = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
                ->join('users', 'ingresos.idusuario', '=', 'users.id')
                ->select(
                    'ingresos.id',
                    'ingresos.tipo_comprobante',
                    'ingresos.serie_comprobante',
                    'ingresos.num_comprobante',
                    'ingresos.fecha_hora',
                    'ingresos.impuesto',
                    'ingresos.total',
                    'ingresos.estado',
                    'personas.nombre',
                    'users.usuario'
                )
                ->where('ingresos' . '.' . $criterio, 'LIKE', '%' . $buscar . '%')
                ->orderBy('ingresos.id', 'desc')->paginate(5);
        }

        // paginación con query builder, para esto hace falta importar 'use Illuminate\Support\Facades\DB;'
        //$personas = DB::table('personas')->paginate(2);

        // Paginación con Eloquent, mucho más limpio


        return [
            'pagination' => [
                'total'         => $ingresos->total(),
                'current_page'  => $ingresos->currentPage(),
                'per_page'      => $ingresos->perPage(),
                'last_page'     => $ingresos->lastPage(),
                'from'          => $ingresos->firstItem(),
                'to'            => $ingresos->lastItem(),
            ],
            'ingresos'    => $ingresos
        ];


        // Codigo inicial
        //$categorias = Categoria::all();
    }

    public function obtenerCabecera(Request $request)
    {

        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;

        // uno con join las tablas, personas con proveedores y selecciono los datos con select
        $ingreso = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
            ->join('users', 'ingresos.idusuario', '=', 'users.id')
            ->select(
                'ingresos.id',
                'ingresos.tipo_comprobante',
                'ingresos.serie_comprobante',
                'ingresos.num_comprobante',
                'ingresos.fecha_hora',
                'ingresos.impuesto',
                'ingresos.total',
                'ingresos.estado',
                'personas.nombre',
                'users.usuario'
            )
            ->where('ingresos.id', '=', $id)
            ->orderBy('ingresos.id', 'desc')->take(1)->get();

        return [
            'ingreso'    => $ingreso
        ];
    }

    public function obtenerDetalles(Request $request)
    {
        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;

        // uno con join las tablas, personas con proveedores y selecciono los datos con select
        $detalles = DetalleIngreso::join('articulos', 'detalle_ingresos.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ingresos.cantidad',
                'detalle_ingresos.precio',
                'detalle_ingresos.cantidad',
                'articulos.nombre as articulo',
            )
            ->where('detalle_ingresos.idingreso', '=', $id)
            ->orderBy('detalle_ingresos.id', 'desc')->get();

        return [
            'detalles'    => $detalles
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try {
            // Inicio una tarnsaccion
            DB::beginTransaction();

            // coger la hora de forma automática
            $mytime = Carbon::now('Europe/Madrid');

            // Sirve para guardar un objeto de la clase Persona
            $ingreso = new Ingreso();
            $ingreso->idproveedor = $request->idproveedor;
            // Envio de forma automática el usuario que registra
            $ingreso->idusuario = \Auth::user()->id;

            $ingreso->tipo_comprobante = $request->tipo_comprobante;
            $ingreso->serie_comprobante = $request->serie_comprobante;
            $ingreso->num_comprobante = $request->num_comprobante;
            $ingreso->fecha_hora = $mytime->toDateString();
            $ingreso->impuesto = $request->impuesto;
            $ingreso->total = $request->total;
            $ingreso->estado = 'Registrado';

            $ingreso->save();

            $detalles = $request->data; // Array de detalles

            // guardo todos los detalles de los ingresos
            foreach ($detalles as $ep => $det) {

                $detalle = new DetalleIngreso();
                $detalle->idingreso = $ingreso->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->save();
            }


            // Hago commit
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
        $ingreso = Ingreso::findOrFail($request->id);
        $ingreso->estado = 'Anulado';
        $ingreso->save();
    }
}
