<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Request;

class SuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
        
        $user=\Auth::user();
        if((!isset($user))||(isset($u) && ($uuser->id_cliente==null || $user->cod_nivel==null))){
            return redirect('login')->withErrors(["email"=>"ERROR: El usuario no existe o la contraseña no es valida"]);
        }
        
        try{
            //Si pasa cualquier cosa durante la autenticacion, se redirige al login con un error diciendole contigo no, bicho
            //dump("aqui en el listener");
            $config_cliente=DB::table('config_clientes')->where('id_cliente',$user->id_cliente)->first();

            $cliente=DB::table('clientes')->where('id_cliente',$user->id_cliente)->first();
            $nivel=DB::table('niveles_acceso')->where('cod_nivel',$user->cod_nivel)->first();
            session(['NIV'=>(array)$nivel]);
            if(isset($cliente->id_distribuidor)){
                $distribuidor=DB::table('distribuidores')->where('id_distribuidor',$cliente->id_distribuidor)->first();
                session(['DIS'=>(array)$distribuidor]);
            }
            session(['CL'=>array_merge((array)$config_cliente,(array)$cliente)]);
            session(['logo_cliente'=>$cliente->img_logo]);
            session(['logo_cliente_menu'=>$cliente->img_logo_menu]);
            Cookie::queue('qrcleanid', $user->id, 999999);

            $user->previous_login = $user->last_login;
            $user->last_login = Carbon::now();
            $user->save();
            session(['lang' => $user->lang]);
            //Permisos del usuario
            $permisos = DB::select("
            SELECT
                    des_seccion,
                    max(mca_read)as mca_read,
                    max(mca_write) as mca_write,
                    max(mca_create) as mca_create,
                    max(mca_delete) as mca_delete
            FROM
                (SELECT
                    `permisos_usuarios`.`id_seccion`,
                    `permisos_usuarios`.`mca_read`,
                    `permisos_usuarios`.`mca_write`,
                    `permisos_usuarios`.`mca_create`,
                    `permisos_usuarios`.`mca_delete`,
                    `secciones`.`des_seccion`
                FROM
                    `permisos_usuarios`
                    INNER JOIN `secciones` ON (`permisos_usuarios`.`id_seccion` = `secciones`.`cod_seccion`)
                WHERE
                    `cod_usuario` = ".$user->id."
                UNION
                SELECT
                    `secciones_perfiles`.`id_seccion`,
                    `secciones_perfiles`.`mca_read`,
                    `secciones_perfiles`.`mca_write`,
                    `secciones_perfiles`.`mca_create`,
                    `secciones_perfiles`.`mca_delete`,
                    `secciones`.`des_seccion`
                FROM
                    `secciones_perfiles`
                    INNER JOIN `secciones` ON (`secciones_perfiles`.`id_seccion` = `secciones`.`cod_seccion`)
                WHERE
                    id_perfil=".$user->cod_nivel.") sq
            GROUP BY sq.des_seccion");
            session(['P' => $permisos]);
            if(isset($request->intended)&&$request->intended!=''){
                session(['redirectTo' => $request->intended]);
            }
        } catch (\Exception $e) {
            return redirect('login')->withErrors(["email"=>"ERROR: El usuario no existe o no esta autorizado a entrar en el sistema"]);
        }
        

        //Vemos las reservas que tiene el usuario y las metemos en sesion
        $reservas=\App\Http\Controllers\UsersController::mis_puestos(auth()->user()->id)['mispuestos'];
        session(['reservas'=>$reservas]);

        try{
            //Planta por defecto del usuario
            $planta=DB::table('reservas')->select('puestos.id_planta')->selectraw("count(reservas.id_reserva) as cuenta")->join('puestos','puestos.id_puesto','reservas.id_puesto')->where('id_usuario',auth()->user()->id)->where('fec_reserva','>',Carbon::now()->subdays(30))->orderby('cuenta','desc')->take(20)->groupby('id_planta')->first();
            if(isset($planta->id_planta)){
                session(['planta_pref'=>$planta->id_planta]);
            } else {
                session(['planta_pref'=>DB::table('plantas_usuario')->where('id_usuario',auth()->user()->id)->first()->id_planta]);
            }
        } catch(\Throwable $e){
            session(['planta_pref'=>0]);
        }
       

        ///Perfil del usuario en session
        session(['perfil'=>$nivel]);

        //Vemos si tiene operario de trabajos
        $operario=DB::table('contratas_operarios')
            ->where(function($q){
                $q->where('id_usuario',auth()->user()->id);
                $q->orwhere('id_operario',auth()->user()->id_operario);
            })
        ->first();
        if(isset($operario)){
            session(['id_operario'=>$operario->id_operario]);
        }

        //Temas del usuario
        try{
            if($user->theme===null){
                session()->put('template',json_decode($config_cliente->theme_name));
            } else {
                session()->put('template',json_decode($user->theme));
            }
        } catch (\Exception $e) {}

        savebitacora('Inicio de sesion del usuario '.$user->name,'SuccessfulLogin','handle','OK');
        
    }
}
