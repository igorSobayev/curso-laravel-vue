<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Venta;
use App\DetalleVenta;

class VentaController extends Controller
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
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'personas.nombre',
                    'users.usuario'
                )
                ->orderBy('ventas.id', 'desc')->paginate(5);
        } else {
            // Uno las tablas en el where porque es necesario que lleve el nombre de personas antes del criterio
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'personas.nombre',
                    'users.usuario'
                )
                ->where('ventas' . '.' . $criterio, 'LIKE', '%' . $buscar . '%')
                ->orderBy('ventas.id', 'desc')->paginate(5);
        }

        // paginación con query builder, para esto hace falta importar 'use Illuminate\Support\Facades\DB;'
        //$personas = DB::table('personas')->paginate(2);

        // Paginación con Eloquent, mucho más limpio


        return [
            'pagination' => [
                'total'         => $ventas->total(),
                'current_page'  => $ventas->currentPage(),
                'per_page'      => $ventas->perPage(),
                'last_page'     => $ventas->lastPage(),
                'from'          => $ventas->firstItem(),
                'to'            => $ventas->lastItem(),
            ],
            'ventas'    => $ventas
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
        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.serie_comprobante',
                'ventas.num_comprobante',
                'ventas.fecha_hora',
                'ventas.impuesto',
                'ventas.total',
                'ventas.estado',
                'personas.nombre',
                'users.usuario'
            )
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')->take(1)->get();

        return [
            'venta'    => $venta
        ];
    }

    public function obtenerDetalles(Request $request)
    {
        // Listamos todos los registros y añadimos función de seguridad
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;

        // uno con join las tablas, personas con proveedores y selecciono los datos con select
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.cantidad',
                'detalle_ventas.descuento',
                'articulos.nombre as articulo',
            )
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();

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
            $venta = new Venta();
            $venta->idcliente = $request->idcliente;
            // Envio de forma automática el usuario que registra
            $venta->idusuario = \Auth::user()->id;

            $venta->tipo_comprobante = $request->tipo_comprobante;
            $venta->serie_comprobante = $request->serie_comprobante;
            $venta->num_comprobante = $request->num_comprobante;
            $venta->fecha_hora = $mytime->toDateString();
            $venta->impuesto = $request->impuesto;
            $venta->total = $request->total;
            $venta->estado = 'Registrado';

            $venta->save();

            $detalles = $request->data; // Array de detalles

            // guardo todos los detalles de los ingresos
            foreach ($detalles as $ep => $det) {

                $detalle = new DetalleVenta();
                $detalle->idventa = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
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
        $venta = Venta::findOrFail($request->id);
        $venta->estado = 'Anulado';
        $venta->save();
    }
}
