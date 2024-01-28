<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\niveles_acceso;
use App\Models\users;
use App\Models\puestos;
use App\Models\plantas_usuario;
use App\Models\puestos_asignados;
use App\Models\turnos_usuarios;
use Illuminate\Http\Request;
use Exception;
use DB;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use Auth;
use Session;
use Redirect;
use \Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use stdClass;

class UsersController extends Controller
{


    public function index()
    {
        
        $usersObjects = DB::table('users')
        ->select('users.*','niveles_acceso.*','sup.name as nom_supervisor','sup.id as id_supervisor')
        ->leftjoin('niveles_acceso','users.cod_nivel', 'niveles_acceso.cod_nivel')
        ->leftjoin('users AS sup','users.id_usuario_supervisor', 'sup.id')
        ->where(function($q){
            if (!isAdmin()) {
                $q->wherein('users.id_cliente',clientes());
            } else {
                $q->where('users.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->where(function($q){
            if (isSupervisor(Auth::user()->id)) {
                $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('id')->toArray();
                $q->wherein('users.id',$usuarios_supervisados);
            }
        })
        ->get();            

        //$usersObjects = users::with('grupo','perfile')->paginate(25);

        return view('users.index', compact('usersObjects'));
    }

    public function search(Request $r){
        $usersObjects = DB::table('users')
        ->select('users.*','niveles_acceso.*','sup.name as nom_supervisor','sup.id as id_supervisor')
        ->leftjoin('niveles_acceso','users.cod_nivel', 'niveles_acceso.cod_nivel')
        ->leftjoin('users AS sup','users.id_usuario_supervisor', 'sup.id')
        ->where(function($q){
            $q->wherein('users.id_cliente',clientes());
        })
        ->where(function($q){
            if (isSupervisor(Auth::user()->id)) {
                $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('id')->toArray();
                $q->wherein('users.id',$usuarios_supervisados);
            }
        })
        ->when($r->cliente, function($q) use($r){
            $q->wherein('users.id_cliente',$r->cliente);
        })
        ->when($r->edificio, function($q) use($r){
            $q->wherein('users.id_edificio',$r->edificio);
        })
        ->when($r->cod_nivel, function($q) use($r){
            $q->wherein('users.cod_nivel',$r->cod_nivel);
        })
        ->when($r->planta, function($q) use($r){
            $q->join('plantas_usuario','users.id', 'plantas_usuario.id_usuario');
            $q->wherein('plantas_usuario.id_planta',$r->planta);
        })
        ->when($r->tipo, function($q) use($r){
            $q->wherenotnull('users.tipos_puesto_admitidos');
            $q->where(function($q2) use($r){
                foreach($r->tipo as $tipo){
                    $q2->orwhereraw("FIND_IN_SET(".$tipo.",users.tipos_puesto_admitidos)<>0");
                }
            });
        })
        ->when($r->user, function($q) use($r){
            $q->wherein('users.id',$r->user);
        })
        ->when($r->id_departamento, function($q) use($r){
            $q->wherein('users.id_departamento',$r->id_departamento);
        })
        ->when($r->id_turno, function($q) use($r){
            $q->join('turnos_usuarios','users.id', 'turnos_usuarios.id_usuario');
            $q->wherein('turnos_usuarios.id_turno',$r->id_turno);
        })
        ->when($r->cod_colectivo, function($q) use($r){
            $q->whereRaw('users.id in (select id_usuario from colectivos_usuarios where cod_colectivo in ('.implode(",",$r->cod_colectivo).'))');
        })  
        ->when($r->user_list, function($q) use($r){
            
                $r->user_list=str_replace(" ","",$r->user_list);
                // $r->user_list=str_replace("\r","",$r->user_list);
                // $r->user_list=str_replace("\n","",$r->user_list);
                $r->user_list=strtolower($r->user_list);
                if(strpos($r->user_list,","))
                    $arr_lista=explode(",",$r->user_list);
                else if(strpos($r->user_list,";"))
                    $arr_lista=explode(";",$r->user_list);
                else if(strpos($r->user_list,"|"))
                    $arr_lista=explode("|",$r->user_list);
                else if(strpos($r->user_list,"\r\n"))
                    $arr_lista=explode("\r\n",$r->user_list);
                
                    $q->wherein('users.email',$arr_lista);
                try{ } catch(Exception $e){
            }
           
        })
        ->when($r->user_id_list, function($q) use($r){
            try{     
                $r->user_id_list=str_replace(" ","",$r->user_id_list);
                // $r->user_list=str_replace("\r","",$r->user_list);
                // $r->user_list=str_replace("\n","",$r->user_list);
                $r->user_id_list=strtolower($r->user_id_list);
                if(strpos($r->user_id_list,","))
                    $arr_lista=explode(",",$r->user_id_list);
                else if(strpos($r->user_id_list,";"))
                    $arr_lista=explode(";",$r->user_id_list);
                else if(strpos($r->user_id_list,"|"))
                    $arr_lista=explode("|",$r->user_id_list);
                else if(strpos($r->user_id_list,"\r\n"))
                    $arr_lista=explode("\r\n",$r->user_id_list);
                $q->wherein('users.id',$arr_lista);
            } catch(Exception $e){
                
            }
       
    })
        ->when($r->supervisor, function($q) use($r){
            $q->wherein('users.id_usuario_supervisor',$r->supervisor);
        })
        ->get();
        
        if ($r->wantsJson()) {
            return $usersObjects;
        } else {
            return view('users.fill_tabla_usuarios', compact('usersObjects'));
        }
    }

    //////////////////////////FUNCIONES DEL EDITOR DE USUARIO//////////////////////////////
    public function edit($id)
    {
        if($id==0){
            $users=new users();
            $users->name="";
            $users->email=Str::random(40);
            $users->password="";
            $users->id_cliente=Auth::user()->id_cliente;
            $users->save();
            $id=$users::latest()->first()->id;
            $users->email="";
        } else {
            validar_acceso_tabla($id,"users");
            $users = users::findOrFail($id);
        }
        

       
        $Perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)
            ->where(function($q){
                $q->where('id_cliente',Auth::user()->id_cliente);
                $q->orwhere('mca_fijo','S');
            })
            ->get();

        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->get()->pluck('id_perfil')->unique();

        $supervisores_usuario=DB::table('permisos_usuarios')->where('id_seccion',$permiso)->get()->pluck('id_usuario')->unique();

        $supervisores=DB::table('users')
            ->where(function ($q) use($supervisores_perfil,$supervisores_usuario){
                $q->wherein('cod_nivel',$supervisores_perfil);
                $q->orwherein('id',$supervisores_usuario);
            })
            ->where('id_cliente',$users->id_cliente)
            ->orderby('name')
            ->get();

        $usuarios_supervisables = DB::table('users')
            ->where('users.id_cliente',$users->id_cliente)
            ->where('users.id','<>',Auth::user()->id)
            ->orderby('name')
            ->get();

        $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',$id)->pluck('id')->toarray();


        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','users.id','reservas.id_usuario')
            ->where('reservas.id_usuario',$id)
            ->get();
        
        $asignaciones=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
            ->leftjoin('users','users.id','puestos_asignados.id_usuario')
            ->leftjoin('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')
            ->where('puestos_asignados.id_usuario',$id)
            ->get();
        
        $turnos=DB::table('turnos')
            ->where('id_cliente',$users->id_cliente)
            ->get();

        $turnos_usuario=DB::table('turnos_usuarios')
            ->where('id_usuario',$id)
            ->pluck('id_turno')->toarray();
            try{
                $pref_turnos=json_decode($users->list_puestos_preferidos);
            } catch (\Throwable $e) {
                $pref_turnos=[];
            }
        

        $edificios=DB::table('edificios')
            ->where('id_cliente',$users->id_cliente)
            ->get();
        
        //Esta parte es para el calendario de actividad
        $eventos=[];
        foreach($reservas as $res){
            $e=new stdClass();
            $e->title=$res->cod_puesto;
            $e->start=Carbon::parse($res->fec_reserva)->format('Y-m-d')."T".Carbon::parse($res->fec_reserva)->format('H:i:s');
            if($res->fec_fin_reserva!=null){
                $e->end=Carbon::parse($res->fec_fin_reserva)->format('Y-m-d')."T".Carbon::parse($res->fec_fin_reserva)->format('H:i:s');
            } else {
                $e->end=Carbon::parse($res->fec_reserva)->format('Y-m-d')."T00:00:00";
            }
            $e->className="success";
            $eventos[]=$e;
        }
        foreach($asignaciones as $as){
            $e=new stdClass();
            $e->title=$as->cod_puesto;
            if($as->fec_desde!=null){
                $e->start=Carbon::parse($as->fec_desde)->format('Y-m-d')."T00:00:00";
                $e->end=Carbon::parse($as->fec_hasta)->format('Y-m-d')."T00:00:00";
            } else {
                $e->start=Carbon::parse('2000-01-01')->format('Y-m-d')."T00:00:00";
                $e->end=Carbon::parse('2050-01-01')->format('Y-m-d')."T00:00:00";
            }
            $e->className="warning";
            $eventos[]=$e;
        }
        $eventos=json_encode($eventos);

        $tokens=DB::table('oauth_access_tokens')
            ->where('user_id',$id)
            ->where('expires_at','>',Carbon::now())
            ->orderby('expires_at','desc')
            ->first();

        $tipos_puestos=DB::table('puestos_tipos')
            ->where('id_cliente',$users->id_cliente)
            ->get();
        try{
            $tipos_puesto_usuario=explode(",",$users->tipos_puesto_admitidos);
            } catch (\Throwable $e) {
            $tipos_puesto_usuario=[];
        }
            

        $bitacoras=DB::table('bitacora')
            ->where('id_usuario',$id)
            ->where('fecha','>',Carbon::now()->subdays(60))
            ->get();

        //Para los puestos preferidos del usuario
        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$users->id_cliente)
            ->wherein('id_tipo_puesto',explode(",",$users->tipos_puesto_admitidos))
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $plantas=DB::table('plantas')
            ->where('plantas.id_cliente',$users->id_cliente)
            ->orderby('plantas.id_planta')
            ->get();

        $plantas_usuario=DB::table('plantas_usuario')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->join('edificios','plantas.id_edificio','edificios.id_edificio')
            ->join('plantas_zonas','plantas.id_planta','plantas_zonas.id_planta')
            ->where('id_usuario',$id)
            ->where('plantas.id_cliente',$users->id_cliente)
            ->orderby('edificios.id_edificio')
            ->get();

        $colectivos_cliente=DB::table('colectivos')
            ->where('id_cliente',$users->id_cliente)
            ->get();

        $colectivos_user=DB::table('colectivos_usuarios')
            ->where('id_usuario',$users->id)
            ->pluck('cod_colectivo')
            ->toarray();

        $contratas=DB::table('contratas')
            ->where('id_cliente',$users->id_cliente)
            ->get();

        $operarios_ind=DB::table('contratas_operarios')
            ->join('contratas','contratas_operarios.id_contrata','contratas.id_contrata')
            ->where('contratas_operarios.id_cliente',$users->id_cliente)
            ->wherenotnull('nom_operario')
            ->distinct()
            ->orderby('contratas.id_contrata')
            ->get();

        $operarios_gen=DB::table('contratas_operarios')
            ->where('id_cliente',$users->id_cliente)
            ->wherenotnull('nom_operario')
            ->distinct()
            ->get();

        $operarios_gen->transform(function($item,$key){
            $item->nom_operario=explode("_",$item->nom_operario)[0];
            return $item;
        });
        $operarios_gen=$operarios_gen->unique('nom_operario');
        $zonas_usuario=DB::table('plantas_zonas')->wherein('id_planta',$plantas_usuario->pluck('id_planta'))->wherein('num_zona',explode(",",$users->list_zonas_admitidas))->get();
        return view('users.edit', compact('users','Perfiles','supervisores','usuarios_supervisados','usuarios_supervisables','eventos','tokens','turnos','turnos_usuario','edificios','plantas_usuario','puestos','bitacoras','tipos_puestos','pref_turnos','tipos_puesto_usuario','colectivos_cliente','colectivos_user','contratas','operarios_gen','operarios_ind','zonas_usuario','plantas'));
    }

    public function update($id, Request $request)
    {
        validar_acceso_tabla($id,"users");
         //Vamos a comprbar el email, porque no puedo pasarlo por el validado
         if(DB::table('users')->where('email',$request->email)->where('id','<>',$id)->exists()){
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: El e-mail ya existe '.$request->email,
                //'url' => url('users/'.$usuario.'/edit')
            ];
        }
        
        $data = $this->getData($request);
        try {
            if ($request->hasFile('img_usuario')) {
                $file = $request->file('img_usuario');
                $path = config('app.ruta_public').'/img/users/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
                $data['img_usuario']=$img_usuario;
            }
            
            if (isset($request->password)){
                $data["password"]=Hash::make($request->password);
            }
            $users = users::findOrFail($id);
            $data["email_verified_at"]=Carbon::now();
            $data["nivel_acceso"]=DB::table('niveles_acceso')->where('cod_nivel',$data['cod_nivel']??1)->first()->val_nivel_acceso;
            $data["id_usuario_supervisor"]=$request->id_usuario_supervisor??null;
            $data["mca_notif_push"] = isset($data["mca_notif_push"]) ? 'S' : 'N';
            $data["mca_notif_email"] = isset($data["mca_notif_email"]) ? 'S' : 'N';
            $data["mca_compartido"] = isset($data["mca_compartido"]) ? 'S' : 'N';
            $data["val_prefijo_compartido"] = $data["mca_compartido"]=="S" ? $data["val_prefijo_compartido"] : null;
            $data["id_operario"] = $data["mca_compartido"]=="N" ? $data["id_operario"] : null;
            $data["sso_override"] = isset($data["sso_override"]) ? 'S' : 'N';
            $data["mca_reserva_multiple"] = isset($data["mca_reserva_multiple"]) ? 'S' : 'N';
            if(isset($data['tipos_puesto_admitidos']) && is_array($data['tipos_puesto_admitidos'])){
                $data['tipos_puesto_admitidos']=implode(",",$data['tipos_puesto_admitidos']);
            }
            if(isset($data['lista_zonas']) && is_array($data['lista_zonas'])){
                $data['list_zonas_admitidas']=implode(",",$data['lista_zonas']);
            } else {
                $data['list_zonas_admitidas']=null;
            }

            $users->update($data);

            //Añadimos los usuarios supervisados
            if(isset($request->lista_id)){
                DB::table('users')->where('id_usuario_supervisor',$id)->update([
                    'id_usuario_supervisor'=>null
                ]);
                foreach($request->lista_id as $id_supervisado){
                    DB::table('users')->where('id',$id_supervisado)->update([
                        'id_usuario_supervisor'=>$id
                    ]);
                }
            }

            //Colectivos
            if (isset($request->val_colectivo)) {
                if (!is_array($request->val_colectivo)){
                    $request->val_colectivo=explode(",",$request->val_colectivo);
                }
                DB::table('colectivos_usuarios')->where('id_usuario',$id)->delete();
                foreach ($request->val_colectivo as $col) {
                    DB::table('colectivos_usuarios')->insert([
                        'cod_colectivo' => $col,
                        'id_usuario' => $id
                    ]);
                }
            }

            savebitacora('Usuario '.$request->email. ' actualizado',"Usuarios","Update","OK");
            return [
                'title' => "Usuarios",
                'message' => 'Usuario '.$request->name. ' actualizado con exito',
                'url' => url('users/'),
                'id' => $id
            ];
            // flash('Usuario '.$request->name. 'actualizado con exito')->success();
            // return redirect()->route('users.users.index');
        } catch (\Throwable $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage() ,"Usuarios","Update","ERROR");
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function update_perfil($id, Request $request)
    {
        validar_acceso_tabla($id,"users");
        $data = $this->getData($request);
        $users = users::findOrFail($id);
        $img_usuario = $users->img_usuario;

        try {
            if ($request->hasFile('img_usuario')) {
                $file = $request->file('img_usuario');
                $path = config('app.ruta_public').'/img/users/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
                $users->img_usuario=$img_usuario;
            }

            if (isset($request->password)){
                $users->password=Hash::make($request->password);
            }

            $users->name=$request->name;
            $users->email=$request->email;
            $users->val_timezone=$request->val_timezone;
            $users->save();

            savebitacora('Perfil de usuario '.$request->email. ' actualizado',"Usuarios","update_perfil","OK");
            return [
                'title' => "Usuarios",
                'message' => 'Perfil de usuario '.$request->name. ' actualizado con exito',
                'url' => url('/')
            ];
        } catch (\Throwable $exception) {
            savebitacora('ERROR: Ocurrio un error actualizando el perfil de usuario '.$request->name.' '.$exception->getMessage() ,"Usuarios","update_perfil","ERROR");
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error actualizando el perfil deusuario '.$request->name.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function soft_delete(Request $r,$id)
    {
        validar_acceso_tabla($id,"users");
        try {
            $users = users::findOrFail($id);
            $users->deleted_at=Carbon::now();
            $users->save();
            savebitacora('Usuario '.$users->email. ' marcado como borrado',"Usuarios","soft_delete","OK");
            flash('Usuario '.$id. ' marcado como borrado con exito')->success();
            if ($r->wantsJson()) {
                return [
                    'title' => "Usuarios",
                    'message' => 'Usuario '.$id. ' marcado como borrado con exito',
                    'id' => $id
                ];
            } else {
                return redirect()->route('users.index');
            }
            
            
        } catch (\Throwable $exception) {
            flash('ERROR: Ocurrio un error al marcar como borrado el usuario '.$id.' '.$exception->getMessage())->error();
            savebitacora('ERROR: Ocurrio un error marcando como borrado el usuario '.$id.' '.$exception->getMessage() ,"Usuarios","soft_delete","ERROR");
            if ($r->wantsJson()) {
                return [
                    'title' => "Usuarios",
                    'error' => 'ERROR: Ocurrio un error borrando el usuario '.$id.' '.$exception->getMessage(),
                    //'url' => url('sections')
                ];
            } else {
                return back()->withInput();
            }
            
                //->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.']);
        }
    }

    public function destroy(Request $r,$id)
    {
        validar_acceso_tabla($id,"users");
        try {
            $users = users::findOrFail($id);
            $users->delete();
            savebitacora('Usuario '.$users->email. ' borrado',"Usuarios","Destroy","OK");
            flash('Usuario '.$id. ' eliminado con exito')->success();
            if ($r->wantsJson()) {
                return [
                    'title' => "Usuarios",
                    'message' => 'Usuario '.$id. ' borrado con exito',
                    'id' => $id
                ];
            } else {
                return redirect()->route('users.index');
            }
            
        } catch (\Throwable $exception) {
            flash('ERROR: Ocurrio un error al eliminar el usuario '.$id.' '.$exception->getMessage())->error();
            savebitacora('ERROR: Ocurrio un error borrando el usuario '.$id.' '.$exception->getMessage() ,"Usuarios","destroy","ERROR");
            if ($r->wantsJson()) {
                return [
                    'title' => "Usuarios",
                    'error' => 'ERROR: Ocurrio un error borrando el usuario '.$id.' '.$exception->getMessage(),
                    //'url' => url('sections')
                ];
            } else {
                return back()->withInput();
            }
                //->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.']);
        }
    }

    public function borrar_usuarios(Request $r)
    {
        try {
            $id_ok=[];
            foreach($r->lista_id as $id){
                validar_acceso_tabla($id,"users");
                $users = users::find($id);
                if($users){
                    $id_ok[]=$users->name;
                    $users->delete();
                    savebitacora('Usuario '.$users->email. ' borrado',"Usuarios","Destroy","OK");
                }
                
                return [
                    'title' => "Usuarios",
                    'message' => 'Usuarios '.implode(",",$id_ok). ' borrados',
                    'url' => url('users')
                ];
            }
        } catch (\Throwable $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error borrando los usuarios el usuario '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }

        
    }

    protected function getData(Request $request)
    {
        $rules = [
            'name'=>'required',
            'collapse' => 'nullable|numeric|min:-2147483648|max:2147483647',
            'email' => 'required|string|min:1|max:255',
            'email_verified_at' => 'nullable|date_format:j/n/Y g:i A',
            'id_cliente' => 'required',
            'cod_nivel' => 'nullable',
            'remember_token' => 'nullable|string|min:0|max:100',
            'theme' => 'nullable|string|min:0|max:150',
            'val_timezone' => 'nullable|string|min:0|max:100',
            'nivel_acceso'=>'nullable',
            'token_acceso'=>'nullable',
            'id_edificio'=>'nullable|numeric',
            'id_turno'=>'nullable',
            'token_expires'=>'nullable|numeric',
            'list_puestos_preferidos'=>'nullable',
            'id_departamento'=>'nullable',
            'val_colectivo'=>'nullable',
            'id_usuario_externo'=>'nullable',
            'id_onesignal'=>'nullable',
            'mca_notif_push'=>'nullable',
            'mca_notif_email'=>'nullable',
            'tipos_puesto_admitidos'=>'nullable',
            'id_contrata'=>'nullable',
            'mca_compartido'=>'nullable',
            'val_prefijo_compartido'=>'nullable',
            'id_operario'=>'nullable',
            'sso_override'=>'nullable',
            'lista_zonas'=>'nullable',
            'mca_reserva_multiple'=>'nullable',
            'img_usuario' => 'nullable|max:10000|mimes:image/jpeg,image/png,image/jpg,image/gif,image/svg',
        ];


        $data = $request->validate($rules);
        return $data;
    }
    ////////////////////////////FUNCIONES PARA EL LISTADO DE USUARIOS
    public function plantas_usuario($id,$check){
        validar_acceso_tabla($id,'users');
        $user=users::findorfail($id);

        $puestos=DB::table('puestos')
            ->select('puestos.id_puesto','puestos.id_edificio','puestos.id_planta','puestos.des_puesto','edificios.des_edificio','plantas.des_planta','estados_puestos.des_estado','clientes.nom_cliente','puestos.cod_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$user->id_cliente)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$user->id_cliente)
        ->get();

        $plantas_cliente=DB::table('plantas')->wherein('id_edificio',$edificios->pluck('id_edificio'))->get();
        $zonas=DB::table('plantas_zonas')->wherein('id_planta',$plantas_cliente->pluck('id_planta'))->get();

        $plantas_usuario=DB::table('plantas_usuario')->where('id_usuario',$id)->pluck('id_planta')->toarray();
        $zonas_usuario=explode(",",$user->list_zonas_admitidas);
        return view('users.selector_plantas',compact('puestos','edificios','plantas_usuario','id','check','zonas','zonas_usuario'));
    }

    public function addplanta($usuario,$planta){
        validar_acceso_tabla($usuario,'users');
        savebitacora('Permiso de reserva en planta '.$planta. ' para el usuario '.$usuario,"Usuarios","addplanta","OK");
        try{
            $pl=plantas_usuario::insert(['id_planta'=>$planta,'id_usuario'=>$usuario]);
            return [
                'title' => "Asociar planta a usuario",
                'message' => 'Planta asociada ',
                'id' =>$planta,
                'user' =>$usuario
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            return [
                'title' => "Asociar planta a usuario",
                'message' => 'Planta ya asociada ',
                'id' =>$planta,
                'user' =>$usuario
            ];
        }
        
    }

    public function delplanta($usuario,$planta){
        validar_acceso_tabla($usuario,'users');
        $pl=plantas_usuario::where(['id_planta'=>$planta,'id_usuario'=>$usuario])->delete();
        savebitacora('Quitado permiso de reserva en planta '.$planta. ' para el usuario '.$usuario,"Usuarios","addplanta","OK");
        return [
            'title' => "Asociar planta a usuario",
            'message' => 'Planta eliminada ',
            'id' =>$planta,
            'user' =>$usuario
        ];
    }

    public function addtodaplanta($estado,$planta){
        validar_acceso_tabla($planta,'plantas');
        
        $usuarios=DB::table('users')->where('id_cliente',Auth::user()->id_cliente)->get();
            if($estado==true){
                foreach($usuarios as $u){
                    try{
                        plantas_usuario::insert(['id_planta'=>$planta,'id_usuario'=>$u->id]);
                    } catch(\Exception $e){}
                }
            } else {
                DB::table('plantas_usuario')->where('id_planta',$planta)->delete();
            }
        savebitacora('Permiso de reserva en planta '.$planta. ' para todos los usuarios ',"Usuarios","addtodaplanta","OK");
        return [
            'title' => "Asociar planta a todos los usuarios",
            'message' => 'Planta asociada ',
            'planta' =>$planta,
            'estado' =>$estado
        ];
    }

    public function addtodouser($estado,$usuario){
        validar_acceso_tabla($usuario,'users');

        $plantas=DB::table('plantas')->where('id_cliente',Auth::user()->id_cliente)->get();
        if($estado){
            foreach($plantas as $p){
                try{
                        DB::table('plantas_usuario')->insert(['id_planta'=>$p->id_planta,'id_usuario'=>$usuario]);
                    } catch(\Exception $e){}
            }
        } else {
            DB::table('plantas_usuario')->where('id_usuario',$usuario)->delete();
        }
        savebitacora('Permiso de reserva del usuario '.$usuario. ' para todas las plantas ',"Usuarios","addtodouser","OK");
        return [
            'title' => "Asociar usuario a todas las plantas",
            'message' => 'Usuario asociado ',
            'usuario' =>$usuario,
            'estado' =>$estado
        ];
    }

    public function editor_modificar_usuarios(Request $r){
        $Perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->wherein('id_cliente',[Auth::user()->id_cliente,1])->get();
        $turnos=DB::table('turnos')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('turnos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('turnos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();
        $edificios=DB::table('edificios')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('edificios.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();
        $tipos_puestos=DB::table('puestos_tipos')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                    if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                        $q->orwhere('puestos_tipos.mca_fijo','S');
                    }
                } else {
                    $q->where('puestos_tipos.id_cliente',session('CL')['id_cliente']);
                    if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                        $q->orwhere('puestos_tipos.mca_fijo','S');
                    }
                }
            })
            ->get();
        $colectivos_cliente=DB::table('colectivos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('colectivos.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('colectivos.id_cliente',session('CL')['id_cliente']);
            }
        })
            ->get();
        $clientes=DB::table('clientes')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('clientes.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('clientes.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();
        $usuarios=DB::table('users')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('name')
            ->get();
        $plantas=DB::table('plantas')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('plantas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $zonas=DB::table('plantas_zonas')
            ->select('plantas_zonas.*')
            ->join('plantas','plantas.id_planta','=','plantas_zonas.id_planta')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('plantas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();
        return view('users.fill_modificar_datos_usuario', compact('Perfiles','turnos','edificios','tipos_puestos','colectivos_cliente','clientes','usuarios','plantas','r','zonas'));
    }

    public function modificar_usuarios(Request $r){
        $datos_actualizados="";
        if(!is_array($r->id_usuario)){
            $r->id_usuario=explode(",",$r->id_usuario);
        }
        if(isset($r->id_cliente)){
            DB::table('users')->wherein('id',$r->id_usuario)->update(['id_cliente'=>$r->id_cliente]);
            $datos_actualizados.='Cliente='.$r->id_cliente.', ';
        }
        if(isset($r->cod_nivel)){
            DB::table('users')->wherein('id',$r->id_usuario)->update(['cod_nivel'=>$r->cod_nivel]);
            $datos_actualizados.='Perfil='.$r->cod_nivel.', ';
        }
        if(isset($r->val_timezone)){
            DB::table('users')->wherein('id',$r->id_usuario)->update(['val_timezone'=>$r->val_timezone]);
            $datos_actualizados.='Zona horaria='.$r->val_timezone.', ';
        }
        if(isset($r->id_edificio)){
            DB::table('users')->wherein('id',$r->id_usuario)->update(['id_edificio'=>$r->id_edificio]);
            $datos_actualizados.='Edificio='.$r->id_edificio.', ';
        }
        if(isset($r->id_departamento)){
            DB::table('users')->wherein('id',$r->id_usuario)->update(['id_departamento'=>$r->id_departamento]);
            $datos_actualizados.='Departamento='.$r->id_departamento.', ';
        }

        if(isset($r->val_colectivo)){
            switch ($r->colectivo_accion) {
                case 'add':
                    foreach($r->id_usuario as $user){
                        foreach($r->val_colectivo as $dato){
                            DB::table('colectivos_usuarios')->insert(['id_usuario'=>$user, 'cod_colectivo'=>$dato]);
                        }
                    }
                    break;
                case 'del':
                    DB::table('colectivos_usuarios')->wherein('id_usuario',$r->id_usuario)->wherein('cod_colectivo',$r->val_colectivo)->delete();
                    break;
                case 'set':
                    DB::table('colectivos_usuarios')->wherein('id_usuario',$r->id_usuario)->delete();
                    foreach($r->id_usuario as $user){
                        foreach($r->val_colectivo as $dato){
                            DB::table('colectivos_usuarios')->insert(['id_usuario'=>$user, 'cod_colectivo'=>$dato]);
                        }
                    }
                    break;
            }
            $datos_actualizados.='<b>'.strtoupper($r->colectivo_accion).'</b> colectivo=['.implode(",",$r->val_colectivo).'], ';
        }

        if(isset($r->turno)){
            switch ($r->turno_accion) {
                case 'add':
                    foreach($r->id_usuario as $user){
                        foreach($r->turno as $dato){
                            DB::table('turnos_usuarios')->insert(['id_usuario'=>$user, 'id_turno'=>$dato]);
                        }
                    }
                    break;
                case 'del':
                    DB::table('turnos_usuarios')->wherein('id_usuario',$r->id_usuario)->wherein('id_turno',$r->turno)->delete();
                    break;
                case 'set':
                    DB::table('turnos_usuarios')->wherein('id_usuario',$r->id_usuario)->delete();
                    foreach($r->id_usuario as $user){
                        foreach($r->turno as $dato){
                            DB::table('turnos_usuarios')->insert(['id_usuario'=>$user, 'id_turno'=>$dato]);
                        }
                    }
                    break;
            }
            $datos_actualizados.='<b>'.strtoupper($r->turno_accion).'</b> turno=['.implode(",",$r->turno).'], ';
        }

        if(isset($r->id_usuario_asig_auto)){
            $config_asignacion=users::find($r->id_usuario_asig_auto);
            DB::table('users')->wherein('id',$r->id_usuario)->update(['list_puestos_preferidos'=>$config_asignacion->list_puestos_preferidos]);
            $datos_actualizados.='Reglas asignacion automatica de puestos igual que el usuario '.$r->id_usuario_asig_auto.', ';
        }

        if(isset($r->plantas)){
            switch ($r->planta_accion) {
                case 'add':
                    foreach($r->id_usuario as $user){
                        foreach($r->plantas as $dato){
                            DB::table('plantas_usuario')->insert(['id_usuario'=>$user, 'id_planta'=>$dato]);
                        }
                    }
                    break;
                case 'del':
                    DB::table('plantas_usuario')->wherein('id_usuario',$r->id_usuario)->wherein('id_planta',$r->plantas)->delete();
                    break;
                case 'set':
                    DB::table('plantas_usuario')->wherein('id_usuario',$r->id_usuario)->delete();
                    foreach($r->id_usuario as $user){
                        foreach($r->plantas as $dato){
                            DB::table('plantas_usuario')->insert(['id_usuario'=>$user, 'id_planta'=>$dato]);
                        }
                    }
                    break;
            }
            $datos_actualizados.='<b>'.strtoupper($r->planta_accion).'</b> planta=['.implode(",",$r->plantas).'], ';
        }

        if(isset($r->zonas)){
            switch ($r->zona_accion) {
                case 'add':
                    foreach($r->id_usuario as $user){
                        $u=users::find($user);
                        try{
                            $zonas=explode(",",$u->list_zonas_admitidas??[]);
                        } catch(\Throwable $e){
                            $zonas=[];
                        }
                        $zonas=array_unique(array_merge($zonas,$r->zonas));
                        $u->list_zonas_admitidas=implode(",",$zonas);
                        $u->save();
                    }
                    break;
                case 'del':
                    foreach($r->id_usuario as $user){
                        $u=users::find($user);
                        try{
                            $zonas=explode(",",$u->list_zonas_admitidas??[]);
                        } catch(\Throwable $e){
                            $zonas=[];
                        }
                        foreach($r->list_zonas_admitidas as $t){
                            foreach (array_keys($zonas, $t) as $key) {
                                unset($zonas[$key]);
                            }
                        }
                        $u->list_zonas_admitidas=implode(",",$zonas);
                        $u->save();
                    }
                    break;
                case 'set':
                    DB::table('users')->wherein('id',$r->id_usuario)->update([
                        'list_zonas_admitidas'=>implode(",",$r->zonas)
                    ]);
                    break;
            }
            $datos_actualizados.='<b>'.strtoupper($r->zona_accion).'</b> zona=['.implode(",",$r->zonas).'], ';
        }

        if(isset($r->tipos_puesto_admitidos)){
            switch ($r->tipo_puesto_accion) {
                case 'add':
                    foreach($r->id_usuario as $user){
                        $u=users::find($user);
                        try{
                            $tipos=explode(",",$u->tipos_puesto_admitidos??[]);
                        } catch(\Throwable $e){
                            $tipos=[];
                        }
                        $tipos=array_unique(array_merge($tipos,$r->tipos_puesto_admitidos));
                        $u->tipos_puesto_admitidos=implode(",",$tipos);
                        $u->save();
                    }
                    break;
                case 'del':
                    foreach($r->id_usuario as $user){
                        $u=users::find($user);
                        try{
                            $tipos=explode(",",$u->tipos_puesto_admitidos??[]);
                        } catch(\Throwable $e){
                            $tipos=[];
                        }
                        foreach($r->tipos_puesto_admitidos as $t){
                            foreach (array_keys($tipos, $t) as $key) {
                                unset($tipos[$key]);
                            }
                        }
                        $u->tipos_puesto_admitidos=implode(",",$tipos);
                        $u->save();
                    }
                    break;
                case 'set':
                    DB::table('users')->wherein('id',$r->id_usuario)->update([
                        'tipos_puesto_admitidos'=>implode(",",$r->tipos_puesto_admitidos)
                    ]);
                    break;
            }
            $datos_actualizados.='<b>'.strtoupper($r->tipo_puesto_accion).'</b> tipo de puesto=['.implode(",",$r->tipos_puesto_admitidos).'], ';
        }
        
        savebitacora($datos_actualizados.'actualizado en '.count($r->id_usuario).' usuarios',"Usuarios","modificar_usuarios","OK");
        return [
            'title' => "Actualizacion de datos de usuarioss",
            'message' => $datos_actualizados.' en '.count($r->id_usuario).' usuarios',
            //'url' => url('users')
        ];
        try{

        } catch(\Throwable $e){
            return [
                'title' => "Usuarios",
                'message' => 'Error al actualizar los datos de usuarios '.mensaje_exception($e),
                'url' => url('users')
            ];
        }
    }

    public function setdefcamera($id){
        $u=users::find(Auth::user()->id);
        $u->def_camera=$id;
        $u->save();
        return;
    }

    public function pwd_hash($pwd){

        return Hash::make($pwd);
    }

    public function reback(){
        $this->authwith(session('back_id'));
        session(['back_id'=>null]);
        return redirect('/');
    }

    public function authwith($id)
    {
        $back_id=Auth::user()->id;
        //validar_acceso_tabla($id,"cug_usuarios");
        savebitacora("Relogin al usuario [".$id."]");
        
        //Auth::logout();
        Auth::loginUsingId($id);
        Session::forget('P');
        Session::forget('CL');
        Session::forget('logo_cliente');
        Session::forget('logo_cliente_menu');
        Session::forget('id_cliente');
        Session::forget('template');

        //session(['lang' => Auth::user()->lang]);
        session(['back_id'=>$back_id]);
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
                `cod_usuario` = ".auth()->user()->id."
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
                id_perfil=".auth()->user()->cod_nivel.") sq
        GROUP BY sq.des_seccion");
        session(['P' => $permisos]);

        //Imagen del usuario
        $imagen=DB::table('users')
        ->select('img_usuario')
        ->where('id',Auth::user()->cod_usuario)
        ->value('img_usuario');
        session(['profile_pic'=>$imagen]);
        Session::forget('id_cliente');

        //Logo de cliente
        $config_cliente=DB::table('config_clientes')->where('id_cliente',Auth::user()->id_cliente)->first();  
        $cliente=DB::table('clientes')->where('id_cliente',Auth::user()->id_cliente)->first();  
        if(isset($cliente->id_distribuidor)){
            $distribuidor=DB::table('distribuidores')->where('id_distribuidor',$cliente->id_distribuidor)->first();
            session(['DIS'=>(array)$distribuidor]);
        }
        session(['CL'=>array_merge((array)$config_cliente,(array)$cliente)]);
        session(['logo_cliente'=>$cliente->img_logo]);
        session(['logo_cliente_menu'=>$cliente->img_logo_menu]);
        session(['id_cliente'=>$cliente->id_cliente]);

        if(!empty($cliente->fec_borrado))
           return response()->json(["Tu empresa se encuentra dada de baja en el sistema, para cualquier pregunta contacte  "],422);

       //Temas del usuario
       try{
            if($user->theme===null){
                session()->put('template',json_decode($config_cliente->theme_name));
            } else {
                session()->put('template',json_decode($user->theme));
            }
        } catch (\Exception $e) {}

    

        savebitacora("Cambio de sesion del usuario",null);
        return redirect('/');
    }

    public function plantas_usuarios(){
        $plantas=DB::table('plantas')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('plantas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('des_planta')
            ->get();

        $zonas=DB::table('plantas_zonas')->wherein('id_planta',$plantas->pluck('id_planta'))->get();

        $usuarios=DB::table('users')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->join('niveles_acceso','niveles_acceso.cod_nivel', 'users.cod_nivel')
            ->orderby('name')
            ->get();

        $plantas_users=DB::table('plantas_usuario')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->select('plantas_usuario.*')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('plantas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        return view('users.gestor_plantas_usuarios',compact('plantas','usuarios','plantas_users','zonas'));
    }

    public function asignar_plantas(Request $r){

        try {
            //Primero borramos lo que ya tuvieran si se ha escogido la opcion
            if($r->borrar_ant){
                DB::table('plantas_usuario')->wherein('id_usuario',$r->lista_id)->wherein('id_planta',$r->lista_plantas)->delete();
            }
            foreach($r->lista_id as $id){
                foreach($r->lista_plantas as $p){
                    DB::table('plantas_usuario')->insert([
                        "id_usuario"=>$id,
                        "id_planta"=>$p
                    ]);
                }
            }
            savebitacora('Asignado permiso de  de reserva en plantas '.implode(",",$r->lista_plantas). ' para los usuarios '.implode(",",$r->lista_id),"Usuarios","asignar_plantas","OK");

            return [
                'title' => "Usuarios",
                'message' => 'Asignado permiso de  de reserva en plantas '.implode(",",$r->lista_plantas). ' para los usuarios '.implode(",",$r->lista_id),
                //'url' => url('users')
            ];

        } catch (\Throwable $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error asignando permisos de planta a los usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function asignar_supervisor(Request $r){
        try {
            $supervisor=$r->supervisor==0?null:$r->supervisor;
            foreach($r->lista_id as $id){
                DB::table('users')->where('id',$id)->update([
                    "id_usuario_supervisor"=>$supervisor
                ]);
            }
            savebitacora('Asignado supervisor' .$r->supervisor.' para los usuarios '.implode(",",$r->lista_id),"Usuarios","asignar_supervisor","OK");

            return [
                'title' => "Usuarios",
                'message' =>'Asignado supervisor' .$r->supervisor.' para los usuarios '.implode(",",$r->lista_id),
                //'url' => url('users')
            ];

        } catch (\Throwable $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error asignando permisos de planta a los usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function puestos_supervisor($id){
        $usuario=users::find($id);

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$usuario->id_cliente)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$usuario->id_cliente)
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q){
                $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
            })
            ->where('reservas.id_cliente',$usuario->id_cliente)
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',$id)
            ->where(function($q) {
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
            })
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',$usuario->cod_nivel)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',$usuario->cod_nivel)
            ->get();

        $puestos_check=DB::table('puestos_usuario_supervisor')->where('id_usuario',$id)->pluck('id_puesto')->toarray();
        $checks=1; //Para que la vista muestre los checkbox
        $id_check=$id;
        $url_check="users/add_puesto_supervisor/";

        
        return view('puestos.content_mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','checks','puestos_check','id_check','url_check'));
    }

    public function puestos_usuario($id){
        $usuario=users::find($id);

        $puestos_check=DB::table('puestos_usuario_supervisor')->where('id_usuario',$id)->pluck('id_puesto')->toarray();

        

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$usuario->id_cliente)
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q){
                $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
            })
            ->where('reservas.id_cliente',0)
            ->get();
        if(isset($reservas)){
            $puestos_reservados=$reservas->pluck('id_puesto')->toArray();
        } else{
            $puestos_reservados=[];
        }

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',$id)
            ->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
            })
            ->get();
        if(isset($asignados_usuarios)){
            $puestos_usuarios=$asignados_usuarios->pluck('id_puesto')->toArray();
        } else{
            $puestos_usuarios=[];
        }

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',0)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',0)
            ->get();
        if(isset($asignados_nomiperfil)){
            $puestos_nomiperfil=$asignados_nomiperfil->pluck('id_puesto')->toArray();
        } else{
            $puestos_nomiperfil=[];
        }

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$usuario->id_cliente)
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->wherenotin('puestos.id_estado',[4,5,6])
            ->wherenotin('puestos.id_puesto',$puestos_usuarios)
            ->wherenotin('puestos.id_puesto',$puestos_nomiperfil)
            ->wherenotin('puestos.id_puesto',$puestos_reservados)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $puestos_check=[];
        $checks=0; //Para que la vista muestre los checkbox
        $id_check=$id;
        $url_check="users/add_puesto_usuario/";

        
        return view('puestos.content_mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','checks','puestos_check','id_check','url_check'));
    }

    public function add_puesto_supervisor($id,$puesto,$accion){
        try {
            if($accion=="A"){
                DB::table('puestos_usuario_supervisor')->insert([
                    "id_puesto"=>$puesto,
                    "id_usuario"=>$id
                ]);
            } else if($accion="D") {
                DB::table('puestos_usuario_supervisor')->where(["id_puesto"=>$puesto,"id_usuario"=>$id])->delete();
            }
            return [
                'result' => "OK"
            ];
        } catch (\Throwable $exception) {
            return [
                'result' => "ERROR",
                'error' => 'ERROR: Ocurrio un error asignando puestos al usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function supervisor_planta($id,$planta,$accion){
        try { 
            $puestos=DB::table('puestos')->where('id_planta',$planta)->pluck('id_puesto')->toArray();
            if($accion=="A"){
                foreach($puestos as $p){
                    try { 
                        DB::table('puestos_usuario_supervisor')->insert([
                            "id_puesto"=>$p,
                            "id_usuario"=>$id
                        ]);
                    } catch (\Throwable $exception) {}
                }
                $estado=true;
            } else if($accion="D") {
                DB::table('puestos_usuario_supervisor')->wherein("id_puesto",$puestos)->where("id_usuario",$id)->delete();
                $estado=false;
            }
            return [
                'result' => "OK",
                "estado" => $estado,
                "planta" => $planta,
                "usuario" => $id
            ];
        } catch (\Throwable $exception) {
            return [
                'result' => "ERROR",
                'error' => 'ERROR: Ocurrio un error asignando puestos al usuarios '.$exception->getMessage(),
                "planta" => $planta,
                "usuario" => $id,
                //'url' => url('sections')
            ];
        }
    }

    public function supervisor_edificio($id,$edificio,$accion){
        try { 
            $puestos=DB::table('puestos')->where('id_edificio',$edificio)->pluck('id_puesto')->toArray();
            if($accion=="A"){
                foreach($puestos as $p){
                    try { 
                        DB::table('puestos_usuario_supervisor')->insert([
                            "id_puesto"=>$p,
                            "id_usuario"=>$id
                        ]);
                    } catch (\Throwable $exception) {}
                }
                $estado=true;
            } else if($accion="D") {
                DB::table('puestos_usuario_supervisor')->wherein("id_puesto",$puestos)->where("id_usuario",$id)->delete();
                $estado=false;
            }
            return [
                'result' => "OK",
                "estado" => $estado,
                "edificio" => $edificio,
                "usuario" => $id
            ];
        } catch (\Throwable $exception) {
            return [
                'result' => "ERROR",
                'error' => 'ERROR: Ocurrio un error asignando puestos al usuarios '.$exception->getMessage(),
                "edificio" => $edificio,
                "usuario" => $id,
                //'url' => url('sections')
            ];
        } 
    }

    public function puestos_supervisores(){
        $puestos=DB::table('puestos')
            ->where('puestos.id_cliente',Auth::user()->id_cliente)
            ->orderby('id_edificio')
            ->orderby('id_planta')
            ->orderby('cod_puesto')
            ->get();

        $plantas=DB::table('plantas')
            ->where('plantas.id_cliente',Auth::user()->id_cliente)
            ->orderby('plantas.id_planta')
            ->get();

        $edificios=DB::table('edificios')
            ->where('edificios.id_cliente',Auth::user()->id_cliente)
            ->orderby('edificios.id_edificio')
            ->get();

        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->get()->pluck('id_perfil')->unique();

        $supervisores_usuario=DB::table('permisos_usuarios')->where('id_seccion',$permiso)->get()->pluck('id_usuario')->unique();

        $usuarios=DB::table('users')
            ->where(function ($q) use($supervisores_perfil,$supervisores_usuario){
                $q->wherein('cod_nivel',$supervisores_perfil);
                $q->orwherein('id',$supervisores_usuario);
            })
            ->where(function($q){
                $q->where('users.id_cliente',Auth::user()->id_cliente);
            })
            ->orderby('name')
            ->get();

        $lista_usuarios=$usuarios->pluck('id');

        $puestos_users=DB::table('puestos_usuario_supervisor')
            ->wherein('id_usuario',$lista_usuarios)
            ->get();

        return view('users.gestor_puestos_supervisores',compact('usuarios','puestos_users','puestos','plantas','edificios'));
    }

    public function asignar_temporal(Request $r){
        try {
            $puesto=puestos::find($r->puesto);
            $idusuario=is_array($r->id_usuario)?$r->id_usuario[0]:$r->id_usuario;
            $usuario=users::find($idusuario);
            $f = explode(' - ',$r->rango);
            $f1 = Carbon::parse(adaptar_fecha($f[0]));
            $f2 = Carbon::parse(adaptar_fecha($f[1]));
           
            //Vamos a ver si alguien tiene ese puesto asignado permanentemente
            $puesto_asignado=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                ->join('users','puestos_asignados.id_usuario','users.id')
                ->where('puestos.id_puesto',$puesto->id_puesto)
                ->wherenull('puestos_asignados.id_perfil')
                ->where(function($q) use($f1,$f2){
                    $q->orwhere(function($q) {
                        $q->wherenull('fec_desde');
                        $q->wherenull('fec_hasta');
                    });
                    $q->orwhere(function($q) use($f1,$f2){
                        $q->whereraw("'".$f1."' between fec_desde AND fec_hasta");
                        $q->orwhereraw("'".$f2."' between fec_desde AND fec_hasta");
                        $q->orwherebetween('fec_desde',[$f1,$f2]);
                        $q->orwherebetween('fec_hasta',[$f1,$f2]);
                    });
                })
                ->get();
            
            $reservas=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->where('puestos.id_puesto',$puesto->id_puesto)
                ->wherebetween('fec_reserva',[$f1,$f2])
                ->where('puestos.id_cliente',$usuario->id_cliente)
                ->get();
            
            if($r->accion=="A"){  //Alta, Añadir
                if(!$puesto_asignado->isempty()){
                    $respuesta=[];
                    foreach ($puesto_asignado as $p){
                        if(!$p->fec_desde){  //El usuario saliente tenia el puesto asignado indefinidamente
                            $str_respuesta="El puesto esta asignado permanentemente a ".$p->name.' como esta asignacion tiene prioridad sobre la permanante, se le notificará a '.$p->name.' que entre '.Carbon::parse($f1)->format('d/m/Y').' y '.Carbon::parse($f2)->format('d/m/Y').' no podrá usar su puesto';
                        } else {
                            $p->fec_desde=Carbon::parse($p->fec_desde);
                            $p->fec_hasta=Carbon::parse($p->fec_hasta); 
                            $str_respuesta='El puesto esta asignado temporalmente a '.$p->name.' entre el '.Carbon::parse($p->fec_desde)->format('d/m/Y').' y '.Carbon::parse($p->fec_hasta)->format('d/m/Y').' si continúa, se modificará la asignacion temporal de puesto y se notificará a '.$p->name.' que ';
                            if(($p->fec_desde>=$f1 && $p->fec_hasta<=$f2) || ($p->fec_desde==$f1 && $p->fec_hasta==$f2) || ($p->fec_desde>$f1 && $p->fec_hasta=$f2)){ //La nueva asignacion es mas grande que la actual--> Se cancela la existente
                                $str_respuesta.=' se cancelará la asignacion temporal de puesto que tenía';
                            }else if($p->fec_desde<=$f1 && $p->fec_hasta>$f2){ //La nueva asignacion es mas pequeña que la actual y esta en medio de esta, se parte la existente en dos
                                $str_respuesta.=' se producirá una interrupcion en el intervalo de su asignacion entre el '.$f1->format('d/m/Y').' y '.$f2->format('d/m/Y');
                            }else if($p->fec_desde>$f1 && $p->fec_hasta>$f2){ //La nueva asignacion empieza enmedio de la actual y acaba despues --> se corta por la izquierda
                                $str_respuesta.=' su asignacion de puesto comenzará el '.$f2->addDay()->format('d/m/Y');
                            }else if($p->fec_hasta>$f1 && $p->fec_hasta<$f2){  //La nueva asignacion  empieza antes de la actual y acaba en medio ->se corta por la derecha
                                $str_respuesta.=' su asignacion de puesto finalizará el '.$f1->subDay()->format('d/m/Y');
                            }
                        }
                        $respuesta[]=$str_respuesta;
                    }
                    foreach($reservas as $res){
                        $respuesta[]="Se cancelará la reserva que ".$res->name."  tiene para el día ".Carbon::parse($res->fec_reserva)->format('d/m/Y');
                    }
                    return view('users.asignacion_temporal_pedir_confirmacion',compact('respuesta','r'));
                }
                //Si no hay nada mas, creamos la asignacion para el usuario
                DB::table('puestos_asignados')->insert([
                    'id_puesto'=>$puesto->id_puesto,
                    'id_usuario'=>$usuario->id,
                    'fec_desde'=>$f1,
                    'fec_hasta'=>$f2,
                    'id_tipo_asignacion'=>1
                ]);
                savebitacora('Asignado puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango,"Usuarios","asignar_temporal","OK");
                //Notificar al usuario entrante
                $str_notificacion=Auth::user()->name.' ha creado una nueva asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') para usted en el intervalo '.$r->rango;
                notificar_usuario($usuario,"<span class='super_negrita'>Asignacion de puesto....<br></span>Estimado usuario:<br><span class='super_negrita'>Se le ha asignado un nuevo puesto</span>",'emails.asignacion_puesto',$str_notificacion,metodos_notificacion_usuario($usuario->id),4,[],$puesto->id_puesto);
                return [
                    'result' => "OK",
                    'title' => "Usuarios",
                    'nocerrar'=> $r->nocerrar,
                    'message' =>'Asignado puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango,
                ];
            } else if($r->accion=="B") {  //Baja, borrar
                
                DB::table('puestos_asignados')->where('key_id',$r->key_id)->delete();
                savebitacora('Borrada asignacion del puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango,"Usuarios","asignar_temporal","OK");
                //Notificar al usuario
                $str_notificacion=Auth::user()->name.' ha eliminado la asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') que tenía';
                notificar_usuario($usuario,"<span class='super_negrita'>Se ha eliminado su asignacion de puesto....<br></span>Estimado usuario:<br><span class='super_negrita'>Se eliminado la asignacion temporal de su puesto</span>",'emails.asignacion_puesto',$str_notificacion,metodos_notificacion_usuario($usuario->id),4,[],$puesto->id_puesto);

                return [
                    'result' => "OK",
                    'title' => "Usuarios",
                    'nocerrar'=> $r->nocerrar,
                    'message' =>'Borrada asignacion de puesto '.$puesto->cod_puesto.' para el usuario '.$usuario->name.' en el intervalo '.$r->rango,
                ];

            } else if($r->accion=="C") { //Confirmar  en caso de que en el alta haya habido algun liop
                $str_respuesta="";
                
                foreach ($puesto_asignado as $p){
                    $p->fec_desde=Carbon::parse($p->fec_desde);
                    $p->fec_hasta=Carbon::parse($p->fec_hasta);
                    $str_notificacion=Auth::user()->name.' ha creado una nueva asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') que entra en conflicto con una que usted tenia entre '.Carbon::parse($p->fec_desde)->format('d/m/Y').' y '.Carbon::parse($p->fec_hasta)->format('d/m/Y');
                    if(($p->fec_desde>=$f1 && $p->fec_hasta<=$f2) || ($p->fec_desde==$f1 && $p->fec_hasta==$f2) || ($p->fec_desde>$f1 && $p->fec_hasta=$f2)){ //Se borra la asigancion que tenia
                        DB::table('puestos_asignados')->where('key_id',$p->key_id)->delete();
                        $str_respuesta='Se ha cancelado la asignacion temporal de puesto que tenía '.$p->name.' para el intervalo entre '.$f1->format('d/m/Y').' y '.$f2->format('d/m/Y');
                    }else if($p->fec_desde<$f1 && $p->fec_hasta>$f2){ //Se parte la asignacion que tenia en dos, metiendo en medio la nueva
                        $asignacion=puestos_asignados::find($p->key_id); //Cacho 1
                        $fec_hasta_temp=$asignacion->fec_hasta;
                        $asignacion->fec_hasta=$f1->subDay();
                        $asignacion->save();

                        $asignacion= new puestos_asignados;  //Creamos el cacho 2
                        $asignacion->fec_desde->$f2->addDay();
                        $asignacion->fec_hasta=$fec_hasta_temp;
                        $asignacion->id_puesto=$puesto->id_puesto;
                        $asignacion->id_usuario=$usuario->id;
                        $asignacion->id_tipo_asignacion=1;
                        $asignacion->save();
                        $str_respuesta=' Se ha interrumpido  el intervalo de asignacion de puesto de '.$p->name.' entre el '.$f1->format('d/m/Y').' y '.$f2->format('d/m/Y');
                    }else if($p->fec_desde>$f1 && $p->fec_hasta>$f2){ 
                        $asignacion=puestos_asignados::find($p->key_id); 
                        $asignacion->fec_desde=Carbon::parse($f2)->addDay();
                        $asignacion->save();
                        $str_respuesta=' su asignacion de puesto comenzará el '.$f2->addDay()->format('d/m/Y');
                    }else if($p->fec_hasta>$f1 && $p->fec_hasta<$f2){
                        $asignacion=puestos_asignados::find($p->key_id); 
                        $asignacion->fec_hasta=Carbon::parse($f1)->subDay();
                        $asignacion->save();
                        $str_respuesta=' su asignacion de puesto acabará el '.$f1->subDay()->format('d/m/Y');
                    }
                    //Notificar al usuario saliente
                    $user_puesto=users::find($p->id_usuario);
                    notificar_usuario($user_puesto,"<span class='super_negrita'>Cambio en su asignacion de puesto....<br></span>Estimado usuario:<br><span class='super_negrita'>Se han producido cambios en su asignacion de puesto</span>",'emails.asignacion_puesto',$str_notificacion.$str_respuesta,[1,3],4,[],$puesto->id_puesto); 
                    savebitacora($str_notificacion.$str_respuesta,"Usuarios","asignar_temporal","OK");
                }

                foreach($reservas as $res){
                    DB::table('reservas')->where('id_reserva',$res->id_reserva)->delete();
                    $user_puesto=users::find($res->id_usuario);
                    $str_respuesta=' Se ha cancelado su reserva de puesto que tenía para el día entre '.Carbon::parse($res->fec_reserva)->format('d/m/Y');
                    savebitacora(' Se ha cancelado su reserva de puesto '.$puesto->cod_puesto.' al usuario '.$user_puesto->name.' para el dia  '.Carbon::parse($res->fec_reserva)->format('d/m/Y').' por una asignacion temporal de puesto creada por '.Auth::user()->name,"Usuarios","asignar_temporal","OK");
                    notificar_usuario($user_puesto,"<span class='super_negrita'>Cambio en su reserva de puesto....<br></span>Estimado usuario:<br><span class='super_negrita'>Se han producido cambios en su reserva de puesto</span>",'emails.mail_reserva',$str_notificacion.$str_respuesta,metodos_notificacion_usuario($user_puesto->id),4,[],$res->id_reserva); 
                }
                //Si no hay nada mas, creamos la asignacion para el usuario
                DB::table('puestos_asignados')->insert([
                    'id_puesto'=>$puesto->id_puesto,
                    'id_usuario'=>$usuario->id,
                    'fec_desde'=>$f1,
                    'fec_hasta'=>$f2,
                    'id_tipo_asignacion'=>1
                ]);
                savebitacora('Asignado puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango.' '.$str_respuesta,"Usuarios","asignar_temporal","OK");
                //Notificar al usuario entrante
                $str_notificacion=Auth::user()->name.' ha creado una nueva asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') para usted';
                notificar_usuario($usuario,"<span class='super_negrita'>Nueva asignacion de puesto....<br></span>Estimado usuario:<br><span class='super_negrita'>Se le ha asignado un nuevo puesto</span>",'emails.asignacion_puesto',$str_notificacion,metodos_notificacion_usuario($usuario->id),4,[],$puesto->id_puesto);
                return [
                    'result' => "OK",
                    'title' => "Usuarios",
                    'nocerrar'=> $r->nocerrar,
                    'message' =>'Asignado puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango,
                ];
            }
            
        } catch (\Throwable $exception) {
            return [
                'result' => "ERROR",
                'error' => 'ERROR: Ocurrio un error asignando temporalmente puestos al usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }


    }

    public function anular_asignacion_temporal(Request $r){

        $f = explode(' - ',$r->fecha);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        $puesto=puestos::find($r->id);

        //Buscamos la asignacion de puesto
        $asignaciones=DB::Table('puestos_asignados')
            ->where('id_puesto',$puesto->id_puesto)
            ->where(function($q) use($r,$f1){
                $q->where('fec_desde','<=',$f1);
                $q->orwherenull('fec_desde');
            })
            ->where(function($q) use($r,$f2){
                $q->where('fec_hasta','>=',$f2);
                $q->orwherenull('fec_hasta');
            })
            ->where('id_tipo_asignacion',1)
            ->get();

        if($asignaciones->count()>0){
            foreach($asignaciones as $asignacion){
                $usuario=users::find($asignacion->id_usuario);
                DB::table('puestos_asignados')->where('key_id',$asignacion->key_id)->update([
                    'fec_hasta'=>Carbon::parse($f1)->subDay()
                ]);
                if($asignacion->fec_desde==null && $asignacion->fec_hasta==null){
                    //Es un puesto permanente hacemos un hueco
                    //E insertamos una nueva despues del hueco
                    DB::table('puestos_asignados')->insert([
                        'id_puesto'=>$puesto->id_puesto,
                        'id_usuario'=>$usuario->id,
                        'fec_desde'=>Carbon::parse($f2)->addDay(),
                        'fec_hasta'=>null,
                        'id_tipo_asignacion'=>1
                    ]);
                } else {
                    //Esto es si la assignacion ya tenia fechas
                    DB::table('puestos_asignados')->insert([
                        'id_puesto'=>$puesto->id_puesto,
                        'id_usuario'=>$usuario->id,
                        'fec_desde'=>Carbon::parse($f2)->addDay(),
                        'fec_hasta'=>$asignacion->fec_hasta,
                        'id_tipo_asignacion'=>1
                    ]);
                }
                $str_respuesta=' Se ha anulado su asignacion de puesto '.$puesto->cod_puesto.' para el intervalo '.$r->fecha;
                savebitacora('Anulada asignacion temporal del puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->fecha,"Usuarios","anular_asignacion_temporal","OK");
                //Notificar al usuario entrante
                $str_notificacion=Auth::user()->name.' ha anulado la asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') para usted en el intervalo '.$r->fecha;
                notificar_usuario($usuario,"<span class='super_negrita'>Anulada asignacion de puesto....<br></span>Estimado usuario:<br><span class='super_negrita'>Se le ha anulado temporalmente una asignacion de puesto</span>",'emails.asignacion_puesto',$str_notificacion,metodos_notificacion_usuario($usuario->id),4,[],$puesto->id_puesto);
            }
            return [
                'result' => "OK",
                'title' => "Usuarios",
                'message' =>'Anulada asignacion temporal del puesto '.$puesto->cod_puesto.' para el intervalo '.$r->fecha,
                'ocultar'=>Carbon::now()>=$f1&&Carbon::now()<=$f2?1:0,
            ];
        } else {
            return [
                'result' => "ERROR",
                'title' => "Usuarios",
                'message' =>'No se ha encontrado ninguna asignacion temporal del puesto '.$puesto->cod_puesto.' para el intervalo '.$r->fecha,
            ];
        }
        
    }

    //FUNCIONES ESPECIALES USUARIOS
    public function miperfil($id){
        validar_acceso_tabla($id,"users");
        if($id!=Auth::user()->id){
            return back();
        }
        $users = users::findOrFail($id);

        return view('users.miperfil', compact('users',));
    }

    public function gen_password($usuario)
    {
        $pwd = randomPassword(16,true,true);

        return response()->json([
            'pwd' => $pwd,
            'result'=>'ok',
            'timestamp'=>Carbon::now()
        ]);
    }

    public function turno_usuario($usuario,$turno,$estado){
        
        if($estado==="true"){
            $turno=db::table('turnos_usuarios')->insert([
                'id_usuario'=>$usuario,
                'id_turno'=>$turno,
                'fec_audit'=>Carbon::now()
            ]);
            return [
                'result' => "OK",
                "action" => "insert turno",
            ];
        }else{
            $turno=turnos_usuarios::where('id_usuario',$usuario)->where('id_turno',$turno)->delete();
            return [
                'result' => "OK",
                "action" => "del turno",
            ];
        }
    }

    //Devuelve los puestos que tiene el usuario para hoy
    public static function mis_puestos($id){
        $mispuestos=null;

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->wheredate('fec_reserva',Carbon::now()->format('Y-m-d'))
            ->where('reservas.id_usuario',Auth::user()->id)
            ->get();
        
        $asignado_usuario=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('puestos_asignados.id_usuario','=',Auth::user()->id)
            ->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
            })
            ->get();
        $asignado_miperfil=null;
        $asignado_otroperfil=null;

        if(!$reservas->isempty()){
            $mispuestos=DB::table('puestos')
                ->select('puestos.*','plantas.*','puestos_asignados.id_perfil','puestos_asignados.id_usuario','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color','puestos_tipos.val_icono as icono_tipo', 'puestos_tipos.val_color as color_tipo','puestos_tipos.des_tipo_puesto')
                ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
                ->join('plantas','puestos.id_planta','plantas.id_planta')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
                ->wherein('puestos.id_puesto',$reservas->pluck('id_puesto')->toArray())
                ->get();
        }

        if(isset($asignado_usuario)){
            $misasignados=DB::table('puestos')
                ->select('puestos.*','plantas.*','puestos_asignados.id_perfil','puestos_asignados.id_usuario','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color','puestos_tipos.val_icono as icono_tipo', 'puestos_tipos.val_color as color_tipo','puestos_tipos.des_tipo_puesto')
                ->join('plantas','puestos.id_planta','plantas.id_planta')
                ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
                ->where('puestos_asignados.id_usuario','=',Auth::user()->id)
                ->where(function($q){
                    $q->wherenull('puestos_asignados.fec_desde');
                    $q->orwhereraw("'".Carbon::now()."' between puestos_asignados.fec_desde AND puestos_asignados.fec_hasta");
                })
                ->wherein('puestos.id_puesto',$asignado_usuario->pluck('id_puesto')->toArray())
                ->get();
                if(isset($mispuestos)){
                    $mispuestos=$mispuestos->merge($misasignados);
                } else {
                    $mispuestos=$misasignados;
                }
        }
        return([
                "mispuestos"=>$mispuestos,
                "reservas"=>$reservas,
                "asignado_otroperfil"=>$asignado_otroperfil,
                "asignado_miperfil"=>$asignado_miperfil,
                "asignado_usuario"=>$asignado_usuario
                ]);
    }

    ///Funciones para el control de los settings de usuario
    public static function tema_usuario(Request $r){
        $u=users::find(Auth::user()->id);
        if($u->theme==null){
            $u->theme=json_encode($r->all());
        }else{
            $u->theme=json_decode($u->theme);
            //El tema azul que no lleva nada
            if(isset($r->tema) && $r->tema=="/color-schemes/"){
                $r->tema="";
            }
            if (isset($r->tema)) $u->theme->tema=$r->tema;
            if (isset($r->tema) && ($r->tema=='/color-schemes/light') ){
                $u->theme->esquema='';
            }  else {
                $u->theme->esquema=$r->tema;
            }
            if (isset($r->rootClass)) $u->theme->rootClass=$r->rootClass;
            if (isset($r->layout)) $u->theme->layout=$r->layout;
            if (isset($r->boximg)) $u->theme->boximg=$r->boximg;
            if (isset($r->menu)) $u->theme->menu=$r->menu;
            if (isset($r->menu_sticky)) $u->theme->menu_sticky=$r->menu_sticky;
            $u->theme=json_encode($u->theme);
        }
        $u->save();
        session()->put('template',json_decode($u->theme));

        return [
            'result' => "OK",
            'tema' => json_encode($r->all())
        ];
    }

    ///Funciones para el control de los settings de usuario
    public static function osid_usuario(Request $r){
        $u=users::find(Auth::user()->id);
        $u->id_onesignal=$r->data;
        $u->save();
        return [
            'result' => "OK",
            'data' => $r->data
        ];
    }

    public function content_2fa($id){
        $users=users::find($id);
        return view('users.fill_content_2fa',compact('users'));
    }

    public function activar_2fa($id,$accion){
        
        
        if($accion=='D'){
            $users=users::find($id);
            $users->two_factor_secret=null;
            $users->two_factor_recovery_codes=null;
            $users->two_factor_confirmed_at=null;
            $users->save();
            return [
                'result' => "OK",
                'data' => $id
            ];
        }
        
    }

    public function setzoom($id,$zoom,$mobile){
        $users=users::find($id);
        if($mobile==1){
            $users->zoom_mobile=$zoom;
        }else{
            $users->zoom_desktop=$zoom;
        }
        $users->save();
        return [
            'result' => "OK",
            'data' => $id
        ];
    }

    public function enviar_recordatorio_asignacion(Request $r){
        ini_set('max_execution_time', 1000);
        foreach($r->lista_id as $id){
            $usuario=users::find($id);
            $str_notificacion='Recordatorio de su asignacion permanente de puesto';
            $puestos_asignados=puestos_asignados::where('id_usuario',$id)->where('fec_desde',null)->pluck('id_puesto')->toArray();
            if(count($puestos_asignados)>0){
                notificar_usuario($usuario,"Tiene los siguientes puestos asignados de forma indefinida",'emails.asignacion_puesto',$str_notificacion,metodos_notificacion_usuario($usuario->id),4,[],$puestos_asignados);
            }
        }
        return [
            'title' => "Recordatorio de asignacion permanente de puesto",
            'message' => 'e-mail recordatorio enviado a '.count($r->lista_id). ' usuarios: '.implode(",",$r->lista_id),
            //'url' => url('users')
        ];
    }
}
