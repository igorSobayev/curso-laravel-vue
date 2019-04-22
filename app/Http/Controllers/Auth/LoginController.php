<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // verificar inicio de sesión
    public function login(Request $request)
    {
        echo $request;
        
        $this->validateLogin($request);

        // verificamos si el usuario esta activo
        // por tanto para acceder a la aplicación el usuario tiene que coincidir con el usuario y contraseña y además
        // tener condicion = 1, osea activo
        if (Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password, 'condicion' => 1])) {
            return redirect()->route('main');
        }
        // en caso de no ser correcto el login, lo retornamos
        // withErrors: espera un parámetro, necesita un identificador de la plantilla blade en la que vamos
        // a mostrar el error y el error a mostrar
        // trans es para traducir el error
        return back()->withErrors(['usuario' => trans('auth.failed')])
            ->withInput(request(['usuario']));
    }

    protected function validateLogin(Request $request)
    {
        // dos parámetros, el primero será el request y el segundo los campos a validar
        $this->validate($request, [
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
    // /*
    // |--------------------------------------------------------------------------
    // | Login Controller
    // |--------------------------------------------------------------------------
    // |
    // | This controller handles authenticating users for the application and
    // | redirecting them to your home screen. The controller uses a trait
    // | to conveniently provide its functionality to your applications.
    // |
    // */

    // use AuthenticatesUsers;

    // /**
    //  * Where to redirect users after login.
    //  *
    //  * @var string
    //  */
    // protected $redirectTo = '/home';

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }
}
