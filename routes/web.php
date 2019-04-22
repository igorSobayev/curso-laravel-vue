<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Creamos rutas para Middleware
// Ruta para los que no se han identificado en la aplicación
Route::group(['middleware' => ['guest']], function () {
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
});

// -------------------------------------------------------------------------------------------------------------------------- //
// Rutas para todos lo que se han identificado correctamente, esto abarca los tres grupos disponibles
Route::group(['middleware' => ['auth']], function () {

    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    // Modificamos la ruta y ponemos que nos sirva contenido como principal
    // Añadimos un alias, main
    Route::get('/main', function () {
        return view('contenido/contenido');
    })->name('main');

    // -----------------------------------------------------------------------------------------------------------------------------------------------//
    // Rutas para el rol de Almacenero
    Route::group(['middleware' => ['Almacenero']], function () {
        // Cada vez que se ingrese en /categoria, se va a llamar al controlador indicado
        // Rutas para categorias
        Route::get('/categoria', 'CategoriaController@index');

        Route::post('/categoria/registrar', 'CategoriaController@store');

        Route::put('/categoria/actualizar', 'CategoriaController@update');

        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');

        Route::put('/categoria/activar', 'CategoriaController@activar');

        // Ruta para el select de las categorias activas
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        // Rutas para articulos
        Route::get('/articulo', 'ArticuloController@index');

        Route::post('/articulo/registrar', 'ArticuloController@store');

        Route::put('/articulo/actualizar', 'ArticuloController@update');

        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');

        Route::put('/articulo/activar', 'ArticuloController@activar');

        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');

        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');

        // Rutas para el proveedor
        Route::get('/proveedor', 'ProveedorController@index');

        Route::post('/proveedor/registrar', 'ProveedorController@store');

        Route::put('/proveedor/actualizar', 'ProveedorController@update');

        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');

        // Rutas para el ingreso
        Route::get('/ingreso', 'IngresoController@index');

        Route::post('/ingreso/registrar', 'IngresoController@store');

        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');

        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');

        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

    });

    // ------------------------------------------------------------------------------------------------------------ //
    // Rutas de acceso para el vendedor
    Route::group(['middleware' => ['Vendendor']], function () {
        // Rutas para personas
        Route::get('/cliente', 'ClienteController@index');

        Route::post('/cliente/registrar', 'ClienteController@store');

        Route::put('/cliente/actualizar', 'ClienteController@update');

        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');

        // Rutas para las ventas
        Route::get('/venta', 'VentaController@index');

        Route::post('/venta/registrar', 'VentaController@store');

        Route::put('/venta/desactivar', 'VentaController@desactivar');

        Route::get('/venta/obtenerCabecera', 'IngresoController@obtenerCabecera');

        Route::get('/venta/obtenerDetalles', 'IngresoController@obtenerDetalles');
    });

    // ------------------------------------------------------------------------------------------------------------- //
    // Middleware para el Administrador, acceso a todo
    Route::group(['middleware' => ['Administrador']], function () {
        // Cada vez que se ingrese en /categoria, se va a llamar al controlador indicado
        // Rutas para categorias
        Route::get('/categoria', 'CategoriaController@index');

        Route::post('/categoria/registrar', 'CategoriaController@store');

        Route::put('/categoria/actualizar', 'CategoriaController@update');

        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');

        Route::put('/categoria/activar', 'CategoriaController@activar');
        // Ruta para el select de las categorias activas
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        // Rutas para articulos
        Route::get('/articulo', 'ArticuloController@index');

        Route::post('/articulo/registrar', 'ArticuloController@store');

        Route::put('/articulo/actualizar', 'ArticuloController@update');

        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');

        Route::put('/articulo/activar', 'ArticuloController@activar');

        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');

        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');

        // Rutas para el proveedor
        Route::get('/proveedor', 'ProveedorController@index');

        Route::post('/proveedor/registrar', 'ProveedorController@store');

        Route::put('/proveedor/actualizar', 'ProveedorController@update');

        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');

        // Rutas para personas
        Route::get('/cliente', 'ClienteController@index');

        Route::post('/cliente/registrar', 'ClienteController@store');

        Route::put('/cliente/actualizar', 'ClienteController@update');
        
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');

        // Rutas para roles
        Route::get('/rol', 'RolController@index');

        Route::get('/rol/selectRol', 'RolController@selectRol');

        // Rutas para users
        Route::get('/user', 'UserController@index');

        Route::post('/user/registrar', 'UserController@store');

        Route::put('/user/actualizar', 'UserController@update');

        Route::put('/user/desactivar', 'UserController@desactivar');

        Route::put('/user/activar', 'UserController@activar');

        // Rutas para el ingreso
        Route::get('/ingreso', 'IngresoController@index');
        
        Route::post('/ingreso/registrar', 'IngresoController@store');

        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');

        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');

        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

        //Rutas para las ventas
        Route::get('/venta', 'VentaController@index');

        Route::post('/venta/registrar', 'VentaController@store');

        Route::put('/venta/desactivar', 'VentaController@desactivar');

        Route::get('/venta/obtenerCabecera', 'IngresoController@obtenerCabecera');

        Route::get('/venta/obtenerDetalles', 'IngresoController@obtenerDetalles');
    });
});
// -----------------------------------------------------------------
// Modificamos la ruta y ponemos que nos sirva contenido como principal
// Añadimos un alias, main
// Route::get('/main', function () {
//     return view('contenido/contenido');
// })->name('main');

// // Cada vez que se ingrese en /categoria, se va a llamar al controlador indicado
// // Rutas para categorias
// Route::get('/categoria', 'CategoriaController@index');

// Route::post('/categoria/registrar', 'CategoriaController@store');

// Route::put('/categoria/actualizar', 'CategoriaController@update');

// Route::put('/categoria/desactivar', 'CategoriaController@desactivar');

// Route::put('/categoria/activar', 'CategoriaController@activar');

// // Ruta para el select de las categorias activas
// Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

// // Rutas para articulos
// Route::get('/articulo', 'ArticuloController@index');

// Route::post('/articulo/registrar', 'ArticuloController@store');

// Route::put('/articulo/actualizar', 'ArticuloController@update');

// Route::put('articulo/desactivar', 'ArticuloController@desactivar');

// Route::put('articulo/activar', 'ArticuloController@activar');

// // Rutas para personas
// Route::get('/cliente', 'ClienteController@index');

// Route::post('/cliente/registrar', 'ClienteController@store');

// Route::put('/cliente/actualizar', 'ClienteController@update');

// // Rutas para el proveedor
// Route::get('/proveedor', 'ProveedorController@index');

// Route::post('/proveedor/registrar', 'ProveedorController@store');

// Route::put('/proveedor/actualizar', 'ProveedorController@update');

// // Rutas para roles
// Route::get('/rol', 'RolController@index');

// Route::get('/rol/selectRol', 'RolController@selectRol');

// // Rutas para users
// Route::get('/user', 'UserController@index');

// Route::post('/user/registrar', 'UserController@store');

// Route::put('/user/actualizar', 'UserController@update');

// Route::put('/user/desactivar', 'UserController@desactivar');

// Route::put('/user/activar', 'UserController@activar');


// Rutas para login, no necesarias al añadir function en LoginController
// Auth::routes();

// Route::get('/', 'Auth\LoginController@showLoginForm');
// Route::post('/login', 'Auth\LoginController@login')->name('login');

// Route::get('/home', 'HomeController@index')->name('home');
