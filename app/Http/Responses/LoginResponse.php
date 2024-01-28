<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\DB;

class LoginResponse implements LoginResponseContract
{

    public function toResponse($request)
    {
        
        // below is the existing response
        // replace this with your own code
        // the user can be located with Auth facade
        $user=Auth::user();
        //dump("aqui en el listener");
        //Vamos a ver si es un usuario que se ha registrado y hay que activarlo
        
        
        if($user->id_cliente == null || $user->nivel_acceso == null){
            $error_cuenta_no_activada="Su cuenta aun no ha sido activada, contacte con el administrador";
            return view('home',compact('error_cuenta_no_activada'));
        }
        if($user->deleted_at!=null){
            $error_cuenta_desactivada="Su cuenta esta desactivada";
            Auth::logout();
            return redirect('login')->withErrors(["email"=>"ERROR: Usuario desactivado"]);
        }

        $config_cliente=DB::table('config_clientes')->where('id_cliente',$user->id_cliente)->first();  

        //Si el cliente requiere 2FA y el usuario aun lo lo ha configurado
        if($config_cliente->mca_requerir_2fa=='S' && $user->two_factor_secret == null){
             return redirect('/2fa');
        }
        return $request->wantsJson()
                    ? response()->json(['two_factor' => false])
                    : redirect()->intended(config('fortify.home'));
    }

}