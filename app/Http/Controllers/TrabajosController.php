<?php

namespace App\Http\Controllers;

use App\Models\trabajos;
use App\Models\trabajos_tipos;
use App\Models\grupos;
use App\Models\trabajos_grupos;
use App\Models\contratas;
use App\Models\operarios;
use App\Models\niveles_acceso;
use App\Models\planes;
use App\Models\planes_detalle;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\plantas_zonas;
use App\Models\trabajos_programacion;

use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use File;
use Str;
use Illuminate\Support\Facades\Storage;

class TrabajosController extends Controller
{
    //

    ////////////////TIPOS DE TRABAJOS ///////////////////////
    public function tipos_index() {
        $trabajos = DB::table('trabajos')
            ->join('trabajos_tipos', 'trabajos.id_tipo_trabajo', 'trabajos_tipos.id_tipo_trabajo')
            ->join('clientes', 'trabajos.id_cliente', 'clientes.id_cliente')
            ->where(function ($q){
                $q->where('trabajos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.tipos.index', compact('trabajos'));
    }

    public function edit_tipo($id=0 ) {
        if ($id==0){
            $dato = new trabajos();
        } else {
            $dato = trabajos::find($id);
        }
        $tipos = trabajos_tipos::all();
        return view('trabajos.tipos.edit', compact('dato','tipos','id'));
    }

    public function update_tipo(Request $r ) {
        try {
            //Las fechas
            if(isset($r->fechas)) {
                $f = explode(' - ',$r->fechas);
                $r->request->add(['fec_inicio'=>adaptar_fecha($f[0])]);
                $r->request->add(['fec_fin'=>adaptar_fecha($f[1])]);
            } else {
                $r->request->add(['fec_inicio'=>null]);
                $r->request->add(['fec_fin'=>null]);
            }
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }

            if ($r->id==0){
                $dato = trabajos::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"trabajos");
                $dato = trabajos::find($r->id);
                $dato->update($r->all());
                $dato->save();
            }
            savebitacora('Tarea de trabajo creada '.$r->des_trabajo,"Trabajos","update_tipo","OK");
            return [
                'title' => "Tareas de trabajos",
                'message' => 'Tarea '.$r->des_trabajo. ' actualizada con exito',
                'url' => url('/trabajos/tipos')
            ];
        } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando la tarea  '.$dato->des_trabajo.' '.$e->getMessage() ,"Trabajos","update_tipo","ERROR");
            return [
                'title' => "Tareas de trabajos",
                'error' => 'ERROR: Ocurrio un error actualizando la tarea '.$r->des_trabajo.' '.$e->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function delete_tipo($id)
    {
        try {
            validar_acceso_tabla($id,"trabajos");
            $dato = trabajos::findOrFail($id);
            $dato->delete();
            savebitacora('Tarea '.$dato->des_trabajo. ' borrado',"Trabajos","delete","OK");
            flash("Tarea ".$dato->des_trabajo." borrado")->success();
		    return redirect('trabajos');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando la tareaa. '.mensaje_excepcion($exception))->error();
            return redirect('trabajos');
        }
    }


    ///////////////////GRUPOS DE TRABAJOS ///////////////////////
    public function grupos_index() {
        $datos = DB::table('grupos_trabajos')
            ->join('clientes', 'grupos_trabajos.id_cliente', 'clientes.id_cliente')
            ->where(function ($q){
                $q->where('grupos_trabajos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.grupos.index', compact('datos'));
    }

    public function edit_grupo($id=0 ) {
        if ($id==0){
            $dato = new grupos();
        } else {
            $dato = grupos::find($id);
        }
        $ya_estan=trabajos_grupos::where('id_grupo',$id)->pluck('id_trabajo')->toArray();
        $tareas = trabajos::where('id_cliente',Auth::user()->id_cliente)->wherenotin('id_trabajo',$ya_estan)->get();
        return view('trabajos.grupos.edit', compact('dato','tareas','id'));
    }

    public function update_grupo(Request $r ) {
        try {
            //Las fechas
            if(isset($r->fechas)) {
                $f = explode(' - ',$r->fechas);
                $r->request->add(['fec_inicio'=>adaptar_fecha($f[0])]);
                $r->request->add(['fec_fin'=>adaptar_fecha($f[1])]);
            } else {
                $r->request->add(['fec_inicio'=>null]);
                $r->request->add(['fec_fin'=>null]);
            }

            
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }

            if ($r->id==0){
                $dato = grupos::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"grupos_trabajos");
                
                $dato = grupos::find($r->id);
                $dato->update($r->all());
                $dato->save();
            }
            //Ahora los elementos del grupo
            $items = json_decode($r->nestedset);
            $dato->num_trabajos=count($items);
            $dato->save();
            DB::table('trabajos_grupos')->where('id_grupo',$dato->id_grupo)->delete();
            foreach($items as $i=>$item){
                $el=new trabajos_grupos;
                $el->id_grupo=$dato->id_grupo;
                $el->id_trabajo=$item->id;
                $el->num_nivel=$item->depth;
                $el->id_padre=$item->parent_id==""?0:$item->parent_id;
                $el->num_orden=$i;
                $el->save();
            }

            savebitacora('Grupo de trabajos creado '.$r->des_grupo,"Trabajos","update_grupo","OK");
            return [
                'title' => "Grupo de trabajos",
                'message' => 'Grupo '.$r->des_grupo. ' actualizada con exito',
                'url' => url('/trabajos/grupos')
            ];
        } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando el grupo  '.$dato->des_grupo.' '.$e->getMessage() ,"Trabajos","update_grupo","ERROR");
            return [
                'title' => "Grupo de trabajos",
                'error' => 'ERROR: Ocurrio un error actualizando el grupo '.$r->des_grupo.' '.$e->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function delete_grupo($id)
    {
        try {
            validar_acceso_tabla($id,"trabajos");
            $dato = trabajos::findOrFail($id);
            $dato->delete();
            DB::table('trabajos_grupos')->where('id_grupo',$dato->id_grupo)->delete();
            savebitacora('Tarea '.$dato->des_trabajo. ' borrado',"Trabajos","delete","OK");
            flash("Tarea ".$dato->des_trabajo." borrado")->success();
		    return redirect('trabajos_grupos');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando la tareaa. '.mensaje_excepcion($exception))->error();
            return redirect('trabajos_grupos');
        }
    }


    ////////////////CONTRATAS DE TRABAJOS ///////////////////////
    public function contratas_index() {
        $datos = DB::table('contratas')
            ->select('contratas.*','clientes.nom_cliente')
            ->selectraw("(select count(*) from contratas_operarios where id_contrata=contratas.id_contrata) as num_operarios")
            ->join('clientes', 'contratas.id_cliente', 'clientes.id_cliente')
            ->where(function ($q){
                $q->where('contratas.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.contratas.index', compact('datos'));
    }

    public function edit_contrata($id=0 ) {
        if ($id==0){
            $dato = new contratas();
        } else {
            $dato = contratas::find($id);
        }
        $operarios = operarios::where('id_contrata',$id)->get();
        return view('trabajos.contratas.edit', compact('dato','operarios','id'));
    }

    public function update_contrata(Request $r ) {
        try {
            //Las fechas
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }
            if ($r->hasFile('imagen')) {
                $file = $r->file('imagen');
                $path = config('app.ruta_public').'/img/contratas/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
                $r->request->add(['img_logo'=>$img_usuario]);
            }

            if ($r->id==0){
                $dato = contratas::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"contratas");
                $dato = contratas::find($r->id);
                $dato->update($r->all());
                $dato->save();
            }
            savebitacora('Contrata de trabajo creada '.$r->des_contrata,"Trabajos","update_contrata","OK");
            return [
                'title' => "Contratas de trabajos",
                'message' => 'Contrata '.$r->des_contrata. ' actualizada con exito',
                'url' => url('/trabajos/contratas')
            ];
        } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando la contrata  '.$r->des_contrata.' '.$e->getMessage() ,"Trabajos","update_contrata","ERROR");
            return [
                'title' => "Contratas de trabajos",
                'error' => 'ERROR: Ocurrio un error actualizando la contrata '.$r->des_contrata.' '.$e->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function delete_contrata($id)
    {
        try {
            validar_acceso_tabla($id,"contratas");
            $dato = contratas::findOrFail($id);
            $dato->delete();
            savebitacora('Contrata '.$dato->des_contrata. ' borrada',"Trabajos","delete_contrata","OK");
            flash("Contrata ".$dato->des_contrata." borrada")->success();
		    return redirect('contratas');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando la contrata. '.mensaje_excepcion($exception))->error();
            return redirect('contratas');
        }
    }

    public function usuarios_internos($id,$id_perfil=null) {
       
        $usuarios = DB::table('contratas_operarios')
            ->select('contratas_operarios.*','users.name','users.cod_nivel','users.img_usuario','users.id')
            ->join('contratas', 'contratas_operarios.id_contrata', 'contratas.id_contrata')
            ->join('users', 'contratas_operarios.id_usuario', 'users.id')
            ->where(function ($q){
                $q->where('contratas.id_cliente',Auth::user()->id_cliente);
            })
            ->where('contratas_operarios.id_contrata',$id)
            ->get();
        $perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)
        ->where('cod_nivel','>',1)
        ->where(function ($q){
            $q->where('id_cliente',Auth::user()->id_cliente);
            $q->orwhere('mca_fijo','S');
        })
        ->get();

        $lista_usuarios=DB::table('users')
            ->select('users.name','users.cod_nivel','users.img_usuario','users.id')
            ->where('users.id_cliente',Auth::user()->id_cliente)
            ->wherein('cod_nivel',$perfiles->pluck('cod_nivel')->toarray())
            ->where(function($q) use ($id_perfil,$perfiles){
                if($id_perfil!=null){
                    $q->where('users.cod_nivel', $id_perfil);
                } else {
                    $q->where('users.cod_nivel', $perfiles->first()->cod_nivel);
                }
            })
            ->orderby('name')
            ->get();
        
        return view('trabajos.contratas.fill_usuarios_internos', compact('usuarios','perfiles','id_perfil','id','lista_usuarios'));
    }

    public function usuarios_genericos($id) {
        $usuarios = DB::table('contratas_operarios')
            ->select('contratas_operarios.*')
            ->join('contratas', 'contratas_operarios.id_contrata', 'contratas.id_contrata')
            ->where(function ($q){
                $q->where('contratas.id_cliente',Auth::user()->id_cliente);
            })
            ->where('contratas_operarios.id_contrata',$id)
            ->wherenull('id_usuario')
            ->orderby('nom_operario')
            ->get();

        return view('trabajos.contratas.fill_usuarios_genericos', compact('usuarios','id'));
    }

    public function crear_usuarios_genericos(Request $r){
        if($r->val_color=="#000000"){
            $r->request->add(['val_color'=>null]);
        }
        for ($i=($r->num_inicio-1);$i<($r->num_inicio+$r->num_usuarios-1);$i++){
            $usuario = new operarios();
            $usuario->id_contrata = $r->id_contrata;
            $usuario->val_color = $r->val_color;
            $usuario->id_cliente = Auth::user()->id_cliente;
            $usuario->nom_operario = $r->des_prefijo.($i+1);
            $usuario->val_icono = $r->val_icono;
            $usuario->save();
        }
        savebitacora( 'Creados '.$r->num_usuarios. ' operarios de '.$r->des_prefijo,"Trabajos","crear_usuarios_genericos","OK");
        return [
            'title' => "Contratas de trabajos",
            'message' => 'Creados '.$r->num_usuarios. ' operarios de '.$r->des_prefijo,
            'url' => url('/trabajos/contratas')
        ];
       
    }

    public function save_operario_generico(Request $r){
        if($r->val_color=="#000000"){
            $r->request->add(['val_color'=>null]);
        }
       
        $op=operarios::find($r->id_operario);
        $op->val_color=$r->val_color;
        $op->val_icono=$r->val_icono;
        $op->nom_operario=$r->nom_operario;
        $op->save();
        savebitacora( 'Operario '.$r->nom_operario." actualizado","Trabajos","save_operario_generico","OK");
        return [
            'title' => "Contratas de trabajos",
            'message' => 'Operario '.$r->nom_operario." actualizado",
            //'url' => url('/trabajos/contratas')
        ];
    }

    public function del_operario_generico(Request $r){
        if($r->val_color=="#000000"){
            $r->request->add(['val_color'=>null]);
        }
       
        $op=operarios::find($r->id_operario);
        $op->delete();
        savebitacora( 'Operario '.$r->nom_operario." borrado","Trabajos","save_operario_generico","OK");
        return [
            'title' => "Contratas de trabajos",
            'message' => 'Operario '.$r->nom_operario." borrado",
            //'url' => url('/trabajos/contratas')
        ];
    }

    public function set_usuarios_contrata($accion,$id_contrata,$id_operario){
        $contrata=contratas::findOrFail($id_contrata);
        switch ($accion) {
            case 'D':
                $usuario = operarios::where('id_contrata',$id_contrata)->where('id_operario',$id_operario)->first();
                $usuario->delete();
                return [
                    'title' => "Contratas de trabajos",
                    'message' => 'Operario '.$id_operario.' eliminado',
                    'id' => $id_operario,
                ];
                break;
            case 'AI':
                $usuario = new operarios();
                $usuario->id_contrata = $id_contrata;
                $usuario->id_usuario = $id_operario;
                $usuario->nom_operario = \App\Models\users::find($id_operario)->name;
                $usuario->id_cliente = $contrata->id_cliente;
                $usuario->save();
                return [
                    'title' => "Contratas de trabajos",
                    'message' => 'Operario '.$id_operario.' añadido',
                    'id' => $id_operario,
                ];
                break;
            case 'DI':
                $usuario = operarios::where('id_contrata',$id_contrata)->where('id_usuario',$id_operario)->first();
                $usuario->delete();
                return [
                    'title' => "Contratas de trabajos",
                    'message' => 'Operario '.$id_operario.' eliminado',
                    'id' => $id_operario,
                ];
                break;
            default:
                # code...
                break;
        }
    }


    ///////////////////PLANES DE TRABAJOS ///////////////////////
    public function planes_index() {
        $datos = DB::table('trabajos_planes')
            ->join('clientes', 'trabajos_planes.id_cliente', 'clientes.id_cliente')
            ->join('edificios', 'trabajos_planes.id_edificio', 'edificios.id_edificio')
            ->where(function ($q){
                $q->where('trabajos_planes.id_cliente',Auth::user()->id_cliente);
            })
            ->get();

        $detalles= DB::table('trabajos_planes_detalle')
            ->join('trabajos_planes', 'trabajos_planes.id_plan', 'trabajos_planes_detalle.id_plan')
            ->join('trabajos', 'trabajos_planes_detalle.id_trabajo', 'trabajos.id_trabajo')
            ->join('contratas', 'trabajos_planes_detalle.id_contrata', 'contratas.id_contrata')
            ->where(function ($q){
                $q->where('trabajos_planes.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.planes.index', compact('datos','detalles'));
    }

    public function edit_plan($id=0 ) {
        if ($id==0){
            $dato = new planes();
        } else {
            $dato = planes::find($id);
        }

        $edificios = edificios::where('id_cliente',Auth::user()->id_cliente)->get();
        $grupos = grupos::where('id_cliente',Auth::user()->id_cliente)->get();
        $contratas = contratas::where('id_cliente',Auth::user()->id_cliente)->get();
        $detalle=planes_detalle::where('id_plan',$id)->get();


        if($dato->espacios!=null){
            $espacios=json_decode($dato->espacios);
            $sel_plantas=implode(",",$espacios->plantas??[]);
            $sel_grupos=implode(",",$espacios->grupos??[]);
            $sel_zonas=implode(",",$espacios->zonas??[]);
            $sel_contratas=implode(",",$espacios->contratas??[]);
       } else {
            $sel_plantas=null;
            $sel_grupos=null;
            $sel_zonas=null;
            $sel_contratas=null;
       }

        

        return view('trabajos.planes.edit', compact('dato','id','edificios','grupos','contratas','detalle','sel_plantas','sel_grupos','sel_zonas','sel_contratas'));
    }

    public function update_plan(Request $r ) {
        
        try {     
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }

            if(isset($r->mca_activo)){
                $r->request->add(['mca_activo'=>'S']);
            } else {
                $r->request->add(['mca_activo'=>'N']);
            }

            $espacios=new \stdClass();
            $espacios->plantas=$r->planta;
            $espacios->zonas=$r->id_zona;
            $espacios->grupos=$r->id_grupo;
            $espacios->contratas=$r->id_contrata;
            $espacios=json_encode($espacios);
            $r->request->add(['espacios'=>$espacios]);

            if ($r->id==0){
                $dato = planes::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"trabajos_planes");
                
                $dato = planes::find($r->id);
                $dato->update($r->all());
                $dato->save();
            }
            
            //Ahora vamos a regularizar los trabajos del plan, borraremos todos aquellos que no esten en los filtros seleccionados
            //Solo puede habner un detalle por espacio, trabajo y grupo
            $tareas_ok=DB::table('trabajos_planes_detalle')
                ->selectraw("max(key_id) as id_detalle")
                ->where('id_plan',$dato->id_plan)
                ->where(function($q) use($r){
                    $q->wherein('id_zona',$r->id_zona??[]);
                    $q->orwherein('id_planta',$r->planta??[]);
                })
                ->wherein('id_grupo_trabajo',$r->id_grupo??[])
                ->wherein('id_contrata',$r->id_contrata??[])
                ->groupby(['id_trabajo','id_contrata','id_zona','id_planta','id_grupo_trabajo'])
                ->pluck('id_detalle')
                ->toArray();
            planes_detalle::where('id_plan',$dato->id_plan)->whereNotIn('key_id',$tareas_ok)->delete();
           

            savebitacora('Plan de trabajo creado '.$r->des_plan,"Trabajos","update_plan","OK");
            return [
                'title' => "Plan de trabajo",
                'message' => 'Plan '.$r->des_plan. ' actualizada con exito',
                'url' => url('/trabajos/planes')
            ];
            } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando el plan  '.$r->des_plan.' '.$e->getMessage() ,"Trabajos","update_plan","ERROR");
            return [
                'title' => "Plan de trabajo",
                'error' => 'ERROR: Ocurrio un error actualizando el plan '.$r->des_plan.' '.$e->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function delete_plan($id)
    {
        try {
            validar_acceso_tabla($id,"trabajos_planes");
            $dato = planes::findOrFail($id);
            $dato->delete();
            DB::table('trabajos_planes')->where('id_plan',$dato->id_plan)->delete();
            savebitacora('Plan '.$dato->des_plan. ' borrado',"Trabajos","delete_plan","OK");
            flash("Plan ".$dato->des_plan." borrado")->success();
            return redirect('trabajos_planes');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando el plan. '.mensaje_excepcion($exception))->error();
            return redirect('trabajos_planes');
        }
    }

    //Pintar la table de deatlle de un plan
    public function get_plan(Request $r) {
        $dato = planes::find($r->id_plan);
        $tareas = trabajos::where('id_cliente',Auth::user()->id_cliente)->get();
        $grupos = grupos::where('id_cliente',Auth::user()->id_cliente)
            ->where(function($q) use($r){
                if(isset($r->grupos)){
                    $q->wherein('id_grupo',$r->grupos);
                } else {
                    $q->wherein('id_grupo',[]);
                }
            })
            ->get();

        $trabajos= DB::table('trabajos')
            ->join('trabajos_tipos', 'trabajos_tipos.id_tipo_trabajo', 'trabajos.id_tipo_trabajo')
            ->join('trabajos_grupos', 'trabajos_grupos.id_trabajo', 'trabajos.id_trabajo')
            ->where('trabajos.id_cliente',Auth::user()->id_cliente)
            ->orderby('num_orden')
            ->get();
        $contratas = contratas::where('id_cliente',Auth::user()->id_cliente)
            ->when($r->contratas, function($q) use($r){
                $q->wherein('id_contrata',$r->contratas);
            })
            ->get();
        $operarios = operarios::where('id_cliente',Auth::user()->id_cliente)
            ->when($r->contratas, function($q) use($r){
                $q->wherein('id_contrata',$r->contratas);
            })
            ->get();
        $plantas = plantas::where('id_cliente',Auth::user()->id_cliente)
            ->where(function($q) use($r){
                if(isset($r->plantas)){
                    $q->wherein('plantas.id_planta',$r->plantas);
                } else {
                    $q->wherein('plantas.id_planta',[]);
                }
            })
            ->get();
    
        $zonas = DB::table('plantas_zonas')
            ->select('plantas_zonas.*','plantas.des_planta')
            ->join('plantas', 'plantas_zonas.id_planta', 'plantas.id_planta')
            ->where(function ($q){
                $q->where('plantas.id_cliente',Auth::user()->id_cliente);
            })
            ->where(function($q) use($r){
                if(isset($r->zonas)){
                    $q->wherein('plantas_zonas.key_id',$r->zonas);
                } else {
                    $q->wherein('plantas_zonas.key_id',[]);
                }
            })
            ->get();
        

        $detalle=DB::table('trabajos_planes_detalle')
            ->join('contratas','trabajos_planes_detalle.id_contrata','contratas.id_contrata')
            ->where('id_plan',$r->id_plan)
            ->get();

        return view('trabajos.planes.detalle', compact('dato','tareas','grupos','contratas','operarios','plantas','zonas','trabajos','detalle'));
    }

    //Detalle de un trabajo dentro del editor de planes
    public function detalle_trabajo(Request $r){
        $detalle= DB::table('trabajos_planes_detalle')
            ->select('trabajos_planes_detalle.*','contratas.*','trabajos.val_operarios as operarios_teorico','trabajos.val_tiempo as tiempo_teorico')
            ->where('id_plan',$r->id_plan)
            ->join('contratas', 'trabajos_planes_detalle.id_contrata', 'contratas.id_contrata')
            ->join('trabajos', 'trabajos_planes_detalle.id_trabajo', 'trabajos.id_trabajo')
            ->where(function($q) use($r){
                if($r->tipo=='P'){
                    $q->join('plantas', 'trabajos_planes_detalle.id_planta', 'plantas.id_planta');
                    $q->where('trabajos_planes_detalle.id_planta',$r->id);
                } else {
                    $q->join('plantas_zonas', 'trabajos_planes_detalle.id_zona', 'plantas_zonas.key_id');
                    $q->where('trabajos_planes_detalle.id_zona',$r->id);
                }
            })
            ->where('id_grupo_trabajo',$r->grupo)
            ->where('trabajos.id_trabajo',$r->trabajo)
            ->first();

        $trabajo= trabajos::find($r->trabajo);
        $datos_plan= planes::find($r->id_plan);
        

        $contratas=DB::Table('contratas')
            ->when($r->contratas, function($q) use($r){
                $q->wherein('id_contrata',$r->contratas);
            })
            ->where('id_cliente',Auth::user()->id_cliente)
            ->get();
        $operarios_genericos=DB::table('contratas_operarios')
            ->wherein('id_contrata',$contratas->pluck('id_contrata')->toarray())
            ->wherenull('id_usuario')
            ->get();

        $operarios=DB::table('contratas_operarios')
            ->join('users', 'contratas_operarios.id_usuario', 'users.id')
            ->wherein('contratas_operarios.id_contrata',$contratas->pluck('id_contrata')->toarray())
            ->wherenotnull('id_usuario')
            ->get();

        $plan=DB::table('trabajos_planes_detalle')
            ->select('trabajos.des_trabajo','trabajos.id_trabajo','grupos_trabajos.id_grupo','grupos_trabajos.des_grupo')
            ->join('grupos_trabajos','trabajos_planes_detalle.id_grupo_trabajo','grupos_trabajos.id_grupo')
            ->join('trabajos_grupos','trabajos_grupos.id_grupo','grupos_trabajos.id_grupo')
            ->join('trabajos','trabajos_grupos.id_trabajo','trabajos.id_trabajo')
            ->where('id_plan',$r->id_plan)
            ->distinct()
            ->get();
        
        $lista_plantas=DB::Table('plantas')
            ->select('id_planta','des_planta')
            ->where('id_edificio',$datos_plan->id_edificio)
            ->where('id_cliente',Auth::user()->id_cliente)
            ->orderby('num_orden')
            ->get();

        $lista_zonas=DB::Table('plantas_zonas')
            ->select('key_id as id_zona', 'des_zona','plantas.id_planta','des_planta')
            ->join('plantas', 'plantas_zonas.id_planta', 'plantas.id_planta')
            ->where('id_edificio',$datos_plan->id_edificio)
            ->where('id_cliente',Auth::user()->id_cliente)
            ->orderby('des_planta')
            ->get();
           
        
        if(isset($detalle->val_tiempo)){
            $val_tiempo=$detalle->val_tiempo;
        } else {
            $val_tiempo=$trabajo->val_tiempo??0;
        }

        if(isset($detalle->val_operarios)){
            $num_operarios=$detalle->val_operarios;
        } elseif(isset($detalle->list_operarios)&&is_array($detalle->list_operarios)){
            $num_operarios=explode(',',$detalle->list_operarios)->count();
        } else {
            $num_operarios=$trabajo->val_operarios??0;
        }

        $val_periodo=$detalle->val_periodo??'0 20 ? * MON-FRI';

        return view('trabajos.planes.fill_detalle_trabajo', compact('detalle','r','contratas','operarios','operarios_genericos','val_tiempo','num_operarios','val_periodo','plan','lista_plantas','lista_zonas'));
        
    }

    public function mini_detalle($plan,$grupo,$trabajo,$contrata,$mostrar_operarios,$mostrar_tiempo){
        $detalle= DB::table('trabajos_planes_detalle')
            ->select('trabajos_planes_detalle.*','contratas.*','trabajos.val_operarios as operarios_teorico','trabajos.val_tiempo as tiempo_teorico')
            ->where('id_plan',$plan)
            ->join('contratas', 'trabajos_planes_detalle.id_contrata', 'contratas.id_contrata')
            ->join('trabajos', 'trabajos_planes_detalle.id_trabajo', 'trabajos.id_trabajo')
            ->where('id_grupo_trabajo',$grupo)
            ->where('trabajos.id_trabajo',$trabajo)
            ->first();

        $contratas=DB::Table('contratas')
            ->where('id_contrata',$contrata)
            ->where('id_cliente',Auth::user()->id_cliente)
            ->get();

        $operarios_genericos=DB::table('contratas_operarios')
            ->where('id_contrata',$contrata)
            ->wherenull('id_usuario')
            ->get();

        $operarios=DB::table('contratas_operarios')
            ->join('users', 'contratas_operarios.id_usuario', 'users.id')
            ->where('id_contrata',$contrata)
            ->wherenotnull('id_usuario')
            ->get();

        if(isset($detalle->val_tiempo)){
            $val_tiempo=$detalle->val_tiempo;
        } else {
            $val_tiempo=$detalle->tiempo_teorico??0;
        }

        if(isset($detalle->val_operarios)){
            $val_operarios=$detalle->val_operarios;
        } elseif(isset($detalle->list_operarios)){
            $val_operarios=explode(',',$detalle->list_operarios)->count();
        } else {
            $val_operarios=$detalle->operarios_teorico??0;
        }
        return view('trabajos.planes.fill_mini_detalle', compact('detalle','contratas','operarios','operarios_genericos','mostrar_operarios','mostrar_tiempo'));

    }

    //Detalle de la periodicidad de un trabajo en un plan
    public function detalle_periodo ($plan,$grupo,$trabajo){

        $detalle= DB::table('trabajos_planes_detalle')
            ->select('trabajos_planes_detalle.val_periodo')
            ->selectraw("count(val_periodo) as cuenta")
            ->where('id_plan',$plan)
            ->join('contratas', 'trabajos_planes_detalle.id_contrata', 'contratas.id_contrata')
            ->join('trabajos', 'trabajos_planes_detalle.id_trabajo', 'trabajos.id_trabajo')
            ->where('id_grupo_trabajo',$grupo)
            ->where('trabajos.id_trabajo',$trabajo)
            ->groupby('val_periodo')
            ->orderby('cuenta','desc')
            ->first();
        $val_periodo=$detalle->val_periodo??'0 20 ? * MON-FRI';
        return view('trabajos.planes.fill_detalle_periodo',compact('val_periodo'));
    }
    //Guarda el detalle de un trabajo en un plan
    public function detalle_save(Request $r){
            try{ $detalle=planes_detalle::find($r->id_detalle);
                if(!isset($detalle)){
                    $detalle=new planes_detalle;
                }
                $detalle->id_plan=$r->id_plan;
                $detalle->id_grupo_trabajo=$r->id_grupo;
                $detalle->id_trabajo=$r->id_trabajo;
                $detalle->id_contrata=$r->id_contrata;
                $detalle->id_planta=isset($r->id_zona)?null:$r->id_planta;
                $detalle->id_zona=$r->id_zona;
                $detalle->val_tiempo=$r->val_tiempo;
                $detalle->val_periodo=$r->val_periodo;
                $detalle->val_tiempo=$r->val_tiempo;
                $detalle->txt_observaciones=$r->txt_observaciones;
                $detalle->mca_activa=isset($r->mca_activa)?'S':'N';
                if($r->sel_operarios==1 && isset($r->operarios)){
                    $detalle->num_operarios=null;
                    $detalle->list_operarios=implode(',',$r->operarios);
                } else {
                    $detalle->num_operarios=$r->num_operarios;
                    $detalle->list_operarios=null;
                }
                $detalle->save();
                //Ahora buscamos en las programaciones aquellos que sean de este detalle y los eliminamos
                $programaciones=trabajos_programacion::where('id_plan',$r->id_plan)
                    ->where('id_trabajo_plan',$detalle->key_id)
                    ->delete();

                //Ahora las copias
                foreach($r->plantas_copiar??[] as $planta){
                    //Primero borramos las copias que ya existan
                    planes_detalle::where('id_plan',$r->id_plan)
                        ->where('id_grupo_trabajo',$r->id_grupo)
                        ->where('id_trabajo',$r->id_trabajo)
                        ->where('id_planta',$planta)
                        ->wherenot('id_planta',$r->id_planta)
                        ->delete();
                    if($planta!=$r->id_planta){
                       $copia=$detalle->replicate();
                       $copia->id_planta=$planta;
                       $copia->save();
                    }
                }
                foreach($r->zonas_copiar??[] as $zona){
                    //Primero borramos las copias que ya existan
                    planes_detalle::where('id_plan',$r->id_plan)
                        ->where('id_grupo_trabajo',$r->id_grupo)
                        ->where('id_trabajo',$r->id_trabajo)
                        ->where('id_zona',$zona)
                        ->wherenot('id_zona',$r->id_planta)
                        ->delete();
                    if($zona!=$r->id_zona){
                       $copia=$detalle->replicate();
                       $copia->id_zona=$zona;
                       $copia->save();
                    }
                }
                foreach($r->trabajos_copiar??[] as $trabajo){
                    //Primero borramos las copias que ya existan
                    $trabajo=explode('_',$trabajo);
                    planes_detalle::where('id_plan',$r->id_plan)
                        ->where('id_grupo_trabajo',$r->id_grupo)
                        ->where('id_trabajo',$trabajo)
                        ->wherenot('id_trabajo',$r->id_trabajo)
                        ->delete();
                    
                    if($trabajo[1]!=$r->id_trabajo || $trabajo[0]!==$r->id_grupo){
                       $copia=$detalle->replicate();
                       $copia->id_trabajo=$trabajo[1];
                       $copia->id_grupo_trabajo=$trabajo[0];
                       $copia->save();
                    }
                }

                savebitacora('Detalle actualizada con exito para el plan de trabajo '.$r->id_plan. ' borrado',"Trabajos","detalle_save","OK");
                return [
                    'title' => "Plan de trabajo",
                    'message' => 'Detalle actualizada con exito',
                ];
            } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando el detalle  '.$e->getMessage() ,"Trabajos","detalle_save","ERROR");
            return [
                'title' => "Plan de trabajo",
                'error' => 'ERROR: Ocurrio un error actualizando el detalle '.$e->getMessage(),
            ];
        }
    }

    //Cambiar el periodo en todos los detalles de un trabajo
    public function periodo_save(Request $r){
        try{
            DB::table('trabajos_planes_detalle')
                ->where('id_plan',$r->id_plan)
                ->where('id_grupo_trabajo',$r->grupo)
                ->where('id_trabajo',$r->trabajo)
                ->update(['val_periodo' => $r->periodo]);

            //Ahora buscamos en las programaciones aquellos que sean de este detalle y los eliminamos
            $arr_trabajos=planes_detalle::where('id_plan',$r->id_plan)
                ->where('id_grupo_trabajo',$r->grupo)
                ->where('id_trabajo',$r->trabajo)
                ->pluck('key_id');
            $programaciones=trabajos_programacion::where('id_plan',$r->id_plan)
                ->whereIn('id_trabajo_plan',$arr_trabajos)
                ->delete();

            savebitacora('Actualizado periodo para el grupo de trabajos '.$r->grupo. ' del plan '.$r->id_plan,"Trabajos","periodo_save","OK");
            return [
                'title' => "Plan de trabajo",
                'message' => 'Actualizado periodo para el grupo de trabajos '.$r->grupo. ' del plan '.$r->id_plan,
            ];
        } catch (\Throwable $e) {
            savebitacora('ERROR: Ocurrio un error actualizando el detalle  '.$e->getMessage() ,"Trabajos","detalle_save","ERROR");
            return [
                'title' => "Plan de trabajo",
                'error' => 'ERROR: Ocurrio un error actualizando periodo para el grupo de trabajos '.$r->grupo. ' del plan '.$r->id_plan.' '.$e->getMessage(),
            ];
        }
    }

    public function delete_detalle($id){
        try {
            $dato = planes_detalle::findOrFail($id);
            $dato->delete();
            savebitacora('Detalle '.$id.' del plan '.$dato->id_plan. ' borrado',"Trabajos","delete_detalle","OK");
            return [
                'title' => "Plan de trabajo",
                'message' => 'Detalle '.$id.' del plan '.$dato->id_plan. ' borrado',
            ];

        } catch (\Throwable $exception) {
            return [
                'title' => "Plan de trabajo",
                'error' => 'ERROR: Ocurrio un error borrando el detalle '.$id.' del plan '.$dato->id_plan.' '.$e->getMessage(),
            ];

        }
    }

    //Esta es para que si se quita una plnata, zona o grupo de trabajo se borren los detalles que no tienen sentido
    public function quitar_recurso_plan(Request $r){

    }


    //////////////////////// SECCION DE TRABAJOS EN SERVICIOS //////////////////////////
    //MIS TRABAJOS

    public function mis_trabajos($fecha=null){
        if(!isset($fecha)){
            $fecha=Carbon::now();
        } else {
            $fecha=Carbon::parse($fecha);
        }
        return view('trabajos.mistrabajos.index', compact('fecha'));
    }

    public function load_calendario($fecha){
        $fecha=Carbon::parse($fecha);
        $calendario=DB::table('trabajos_programacion')
            ->selectraw('DATE(trabajos_programacion.fec_programada) as fecha_corta,
                        count(trabajos_programacion.id_programacion) as trabajos')
            ->join('trabajos_planes_detalle','trabajos_programacion.id_trabajo_plan','trabajos_planes_detalle.key_id')
            ->join('trabajos_planes','trabajos_planes_detalle.id_plan','trabajos_planes.id_plan')
            ->join('trabajos','trabajos_planes_detalle.id_trabajo','trabajos.id_trabajo')
            ->where('trabajos_planes.id_cliente',Auth::user()->id_cliente)
            ->where(function($q){
                if(session('id_operario')!=null){
                    $q->whereraw("find_in_set(".session('id_operario').",trabajos_planes_detalle.list_operarios)");
                }
                $q->orwherenull('trabajos_planes_detalle.list_operarios');
            })
            ->wherebetween('trabajos_programacion.fec_programada',[Carbon::parse($fecha)->startofmonth(),Carbon::parse($fecha)->endofmonth()])
            ->groupby('fecha_corta')
            ->get();

        return view('trabajos.mistrabajos.fill_calendario', compact('fecha','calendario'));
    }

    public function load_dia($fecha,$vista=null){
        $fecha=Carbon::parse($fecha);
        $datos=DB::table('trabajos_programacion')
            ->select('trabajos.des_trabajo','trabajos.val_icono as icono_trabajo','trabajos.val_color as color_trabajo',
                     'trabajos_programacion.*',
                     'trabajos_planes.des_plan','trabajos_planes.val_icono as icono_plan','trabajos_planes.val_color as color_plan','trabajos_planes.id_edificio',
                     'edificios.des_edificio',
                     'plantas.des_planta',
                     'plantas_zonas.des_zona',
                     'trabajos_planes_detalle.id_planta','trabajos_planes_detalle.id_zona','trabajos_planes_detalle.val_tiempo','trabajos_planes_detalle.num_operarios','trabajos_planes_detalle.list_operarios','trabajos_planes_detalle.txt_observaciones',
                     'grupos_trabajos.id_grupo','grupos_trabajos.des_grupo','grupos_trabajos.val_icono as icono_grupo','grupos_trabajos.val_color as color_grupo',
                     'operarios_ini.nom_operario as nom_operario_ini',
                     'operarios_fin.nom_operario as nom_operario_fin',)
            ->join('trabajos_planes_detalle','trabajos_programacion.id_trabajo_plan','trabajos_planes_detalle.key_id')
            ->join('trabajos_planes','trabajos_planes_detalle.id_plan','trabajos_planes.id_plan')
            ->join('edificios','trabajos_planes.id_edificio','edificios.id_edificio')
            ->leftjoin('contratas_operarios as operarios_ini','trabajos_programacion.id_operario_inicio','operarios_ini.id_operario')
            ->leftjoin('contratas_operarios  as operarios_fin','trabajos_programacion.id_operario_fin','operarios_fin.id_operario')
            ->leftjoin('plantas','trabajos_planes_detalle.id_planta','plantas.id_planta')
            ->leftjoin('plantas_zonas','trabajos_planes_detalle.id_zona','plantas_zonas.key_id')
            ->join('trabajos','trabajos_planes_detalle.id_trabajo','trabajos.id_trabajo')
            ->join('grupos_trabajos','trabajos_planes_detalle.id_grupo_trabajo','grupos_trabajos.id_grupo')
            ->where('trabajos_planes.id_cliente',Auth::user()->id_cliente)
            ->where(function($q){
                if(session('id_operario')!=null){
                    $q->whereraw("find_in_set(".session('id_operario').",trabajos_planes_detalle.list_operarios)");
                }
                $q->orwherenull('trabajos_planes_detalle.list_operarios');
            })
            ->wheredate('trabajos_programacion.fec_programada',$fecha)
            ->get();
        if($vista!==null){
            session(['tipo_vista'=>$vista]);
        }
        if(session('tipo_vista')==null || $vista=='undefined'){
            $vista='card';
            session(['tipo_vista'=>'card']);
        } else {
            $vista=session('tipo_vista');
        }
        return view('trabajos.mistrabajos.trabajos_dia', compact('fecha','datos','vista'));
    }

    public function iniciar_trabajo($id){
        $trabajo=trabajos_programacion::find($id);
        $trabajo->id_operario_inicio=session('id_operario');
        $trabajo->fec_inicio=Carbon::now();
        $trabajo->save();
        return [
            'title' => "Plan de trabajo",
            'message' => 'Iniciado trabajo ',
        ];
    }

    public function finalizar_trabajo($id){
        $trabajo=trabajos_programacion::find($id);
        $trabajo->id_operario_fin=session('id_operario');
        $trabajo->fec_fin=Carbon::now();
        $trabajo->save();
        return [
            'title' => "Plan de trabajo",
            'message' => 'Finalizado trabajo ',
        ];
    }

    public function get_comentarios_trabajo($id){
        $trabajo=trabajos_programacion::find($id);
        return $trabajo->observaciones;
    }

    public function get_observaciones_trabajo($id){
        $detalle=planes_detalle::find($id);
        return $detalle->txt_observaciones;
    }

    public function save_comentarios_trabajo(Request $r){
        $trabajo=trabajos_programacion::find($r->id);
        $operario=operarios::find(session('id_operario'));
        $trabajo->observaciones.='<br>['.$operario->nom_operario.']: '.$r->observaciones;
        $trabajo->save();
        return [
            'title' => "Plan de trabajo",
            'message' => 'Añadido comentario ',
        ];
    }

    //GESTION DE PLANES
    public function servicios_planes(){
        $datos = DB::table('trabajos_planes')
            ->join('clientes', 'trabajos_planes.id_cliente', 'clientes.id_cliente')
            ->join('edificios', 'trabajos_planes.id_edificio', 'edificios.id_edificio')
            ->where(function ($q){
                $q->where('trabajos_planes.id_cliente',Auth::user()->id_cliente);
            })
            ->get();

        $detalles= DB::table('trabajos_planes_detalle')
            ->join('trabajos_planes', 'trabajos_planes.id_plan', 'trabajos_planes_detalle.id_plan')
            ->join('trabajos', 'trabajos_planes_detalle.id_trabajo', 'trabajos.id_trabajo')
            ->join('contratas', 'trabajos_planes_detalle.id_contrata', 'contratas.id_contrata')
            ->where(function ($q){
                $q->where('trabajos_planes.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        return view('trabajos.servicios_planes.index', compact('datos','detalles'));
    }

    public function servicios_ver_plan($id,$fecha=null){
        if($fecha==null){
            $fecha=Carbon::now();
        }
        $fecha=Carbon::parse($fecha);
        $dato = planes::find($id);
        $detalle=DB::table('trabajos_planes_detalle')
            ->where('id_plan',$id)
            ->get();

        $tareas = trabajos::where('id_cliente',Auth::user()->id_cliente)
            ->wherein('id_trabajo',$detalle->pluck('id_trabajo')->unique()->toarray())
            ->get();
        $grupos = grupos::where('id_cliente',Auth::user()->id_cliente)
            ->wherein('id_grupo',$detalle->pluck('id_grupo_trabajo')->unique()->toarray())
            ->get();

        $trabajos= DB::table('trabajos')
            ->join('trabajos_tipos', 'trabajos_tipos.id_tipo_trabajo', 'trabajos.id_tipo_trabajo')
            ->join('trabajos_grupos', 'trabajos_grupos.id_trabajo', 'trabajos.id_trabajo')
            ->wherein('trabajos.id_trabajo',$detalle->pluck('id_trabajo')->unique()->toarray())
            ->where('trabajos.id_cliente',Auth::user()->id_cliente)
            ->orderby('num_orden')
            ->get();
        $contratas = contratas::where('id_cliente',Auth::user()->id_cliente)
            ->wherein('id_contrata',$detalle->pluck('id_contrata')->unique()->toarray())
            ->get();
        $operarios = operarios::where('id_cliente',Auth::user()->id_cliente)
            ->wherein('id_contrata',$detalle->pluck('id_contrata')->unique()->toarray())
            ->get();
        $plantas = plantas::where('id_cliente',Auth::user()->id_cliente)
            ->wherein('id_planta',$detalle->pluck('id_planta')->unique()->toarray())
            ->get();
    
        $zonas = DB::table('plantas_zonas')
            ->select('plantas_zonas.*','plantas.des_planta')
            ->join('plantas', 'plantas_zonas.id_planta', 'plantas.id_planta')
            ->wherein('key_id',$detalle->pluck('id_zona')->unique()->toarray())
            ->get();

        $programaciones=DB::Table('trabajos_programacion')
            ->select('trabajos_programacion.*')
            ->selectraw("date(fec_programada) as fecha_corta")
            ->where('id_plan',$id)
            ->wherebetween('trabajos_programacion.fec_programada',[Carbon::parse($fecha)->startofmonth(),Carbon::parse($fecha)->endofmonth()])
            ->get();

        return view('trabajos.servicios_planes.detalle', compact('dato','tareas','grupos','contratas','operarios','plantas','zonas','trabajos','detalle','fecha','id','programaciones'));
    }

    public function servicios_detalle_trabajo(Request $r){
        $fecha=Carbon::parse($r->fecha);
        $datos=DB::table('trabajos_programacion')
            ->select('trabajos.des_trabajo','trabajos.val_icono as icono_trabajo','trabajos.val_color as color_trabajo', 'trabajos.val_operarios as operarios_previstos','trabajos.fec_inicio as fec_ini_trabajo','trabajos.fec_fin as fec_fin_trabajo',
                     'trabajos_programacion.*',
                     'trabajos_planes.des_plan','trabajos_planes.val_icono as icono_plan','trabajos_planes.val_color as color_plan','trabajos_planes.id_edificio',
                     'edificios.des_edificio',
                     'plantas.des_planta',
                     'plantas_zonas.des_zona',
                     'trabajos_planes_detalle.id_planta','trabajos_planes_detalle.id_zona','trabajos_planes_detalle.val_tiempo','trabajos_planes_detalle.num_operarios','trabajos_planes_detalle.list_operarios','trabajos_planes_detalle.txt_observaciones','trabajos_planes_detalle.val_periodo',
                     'grupos_trabajos.id_grupo','grupos_trabajos.des_grupo','grupos_trabajos.val_icono as icono_grupo','grupos_trabajos.val_color as color_grupo','grupos_trabajos.fec_inicio as fec_ini_grupo','grupos_trabajos.fec_fin as fec_fin_grupo',
                     'operarios_ini.nom_operario as nom_operario_ini',
                     'operarios_fin.nom_operario as nom_operario_fin',
                     'contratas.des_contrata','contratas.img_logo as logo_contrata')
            ->join('trabajos_planes_detalle','trabajos_programacion.id_trabajo_plan','trabajos_planes_detalle.key_id')
            ->join('contratas','contratas.id_contrata','trabajos_planes_detalle.id_contrata')
            ->join('trabajos_planes','trabajos_planes_detalle.id_plan','trabajos_planes.id_plan')
            ->join('edificios','trabajos_planes.id_edificio','edificios.id_edificio')
            ->leftjoin('contratas_operarios as operarios_ini','trabajos_programacion.id_operario_inicio','operarios_ini.id_operario')
            ->leftjoin('contratas_operarios  as operarios_fin','trabajos_programacion.id_operario_fin','operarios_fin.id_operario')
            ->leftjoin('plantas','trabajos_planes_detalle.id_planta','plantas.id_planta')
            ->leftjoin('plantas_zonas','trabajos_planes_detalle.id_zona','plantas_zonas.key_id')
            ->join('trabajos','trabajos_planes_detalle.id_trabajo','trabajos.id_trabajo')
            ->join('grupos_trabajos','trabajos_planes_detalle.id_grupo_trabajo','grupos_trabajos.id_grupo')
            ->where('trabajos_planes.id_cliente',Auth::user()->id_cliente)
            ->where('trabajos_programacion.id_programacion',$r->programa)
            ->where(function($q){
                if(session('id_operario')!=null){
                    $q->whereraw("find_in_set(".session('id_operario').",trabajos_planes_detalle.list_operarios)");
                }
                $q->orwherenull('trabajos_planes_detalle.list_operarios');
            })
            ->wheredate('trabajos_programacion.fec_programada',$fecha)
            ->first();

        $historial=DB::table('trabajos_programacion')
            ->select('trabajos_programacion.*',
                     'trabajos.fec_inicio as fec_ini_trabajo','trabajos.fec_fin as fec_fin_trabajo',
                     'grupos_trabajos.fec_inicio as fec_ini_grupo','grupos_trabajos.fec_fin as fec_fin_grupo',
                     'operarios_ini.nom_operario as nom_operario_ini',
                     'operarios_fin.nom_operario as nom_operario_fin',
                     'trabajos_planes_detalle.id_planta','trabajos_planes_detalle.id_zona','trabajos_planes_detalle.val_tiempo')
            ->join('trabajos_planes_detalle','trabajos_programacion.id_trabajo_plan','trabajos_planes_detalle.key_id')
            ->join('trabajos','trabajos_planes_detalle.id_trabajo','trabajos.id_trabajo')
            ->leftjoin('contratas_operarios as operarios_ini','trabajos_programacion.id_operario_inicio','operarios_ini.id_operario')
            ->leftjoin('contratas_operarios  as operarios_fin','trabajos_programacion.id_operario_fin','operarios_fin.id_operario')
            ->join('grupos_trabajos','trabajos_planes_detalle.id_grupo_trabajo','grupos_trabajos.id_grupo')
            ->where('trabajos_programacion.id_trabajo_plan',$datos->id_trabajo_plan)
            ->where(function($q) use($fecha){
                $q->where('trabajos_programacion.fec_programada','>=',Carbon::parse($fecha)->subdays(10)->format('Y-m-d'));
                $q->where('trabajos_programacion.fec_programada','<=',Carbon::parse($fecha)->addDays(10)->format('Y-m-d'));
            })
            ->where(function($q) use($datos){
                $q->where('trabajos_planes_detalle.id_planta',$datos->id_planta);
                $q->orwhere('trabajos_planes_detalle.id_zona',$datos->id_zona);
            })
            ->orderby('trabajos_programacion.fec_programada','asc')
            ->get();
        return view('trabajos.servicios_planes.fill_detalle_trabajo',compact('datos','fecha','r','historial'));
    }

    //Funcion que devuelve la celda del detalle de un trabajo con el color en funcion de su estado
    public static function celda_plan_trabajos($tarea,$programa,$hoy,$fecha){
        //Ahora vamos a ver si esta bien hecho el trabajo o no y en base a eso rellenaremos el color de la celda
       $color='';
       $icono='';
       $title='';
       $abr='';
       if(isset($programa)){
           if($fecha>$hoy){
               $color='bg-light';
               $icono='';
               $title='El trabajo aun no se ha iniciado';
               $abr='';
           }
           
           if(isset($programa->fec_inicio) && isset($programa->fec_fin)){
               if(Carbon::parse($programa->fec_inicio)->diffinminutes(Carbon::parse($programa->fec_fin))>$tarea->val_tiempo){
                   $color='bg-warning';
                   $icono='fa-regular fa-stopwatch';
                   $title='El trabajo se ha realizado fuera de tiempo';
                   $abr='T';
               } else {
                   $color='bg-success';
                   $icono='';
                   $title='El trabajo se ha realizado en tiempo';
                   $abr='OK';
               }
           } else if(!isset($programa->fec_inicio) && !isset($programa->fec_fin) && $fecha<$hoy){
               $color='bg-danger';
               $icono='';
               $title='El trabajo no se ha realizado';
               $abr='X';
           } else if(isset($programa->fec_inicio) && !isset($programa->fec_fin)){
               $color='bg-warning';
               $icono='fa-solid fa-circle-half-stroke';
               $title='El trabajo se ha iniciado pero no se ha finalizado';
               $abr='I';
           }
           
           if(isset($programa->fec_inicio) && Carbon::parse($programa->fec_inicio)->diffindays(Carbon::parse($programa->fec_programada))>1){
               $color='bg-pink';
               $icono='fa-solid fa-calendar-exclamation';
               $title='El trabajo se ha iniciado pero fuera de la fecha prevista';
               $abr='F';
           }
            //Comprobamos si el dia esta excluido en el rango de fechas a aplicar del grupo o del trabajo
            $in_time=true;
            if($tarea->fec_ini_grupo!=null && $tarea->fec_fin_grupo!=null && !$fecha->between($tarea->fec_ini_grupo,$tarea->fec_fin_grupo)){
                $in_time=false;
                $donde="grupo";
            }
            if($tarea->fec_ini_trabajo!=null && $tarea->fec_fin_trabajo!=null && !$fecha->between($tarea->fec_ini_trabajo,$tarea->fec_fin_trabajo)){
                $in_time=false;
                $donde="trabajo";
            }

            if(!$in_time){
                $color='bg-dark';
                $icono='fa-light fa-calendar-circle-minus';
                $title='La tarea esta fuera del rango de fechas establecido en el '.$donde;
                $abr='R';
            }
       }

       

       return [
           'color'=>$color,
           'icono'=>$icono,
           'title'=>$title,
           'abr'=>$abr
       ];
   }
}
