<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\logpuestos;
use App\Models\rondas;
use App\Models\puestos_ronda;
use App\Models\limpiadores;
use App\Models\tags;
use App\Models\tags_puestos;
use App\Models\users;
use App\Models\puestos_tipos;
use App\Models\salas;
use App\Models\config_clientes;
use App\Models\bloqueos;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use PDF;
use Illuminate\Support\Str;
use Redirect;
use Excel;
use App\Exports\ExportExcel;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use InvalidArgumentException;
use Illuminate\Contracts\Container\BindingResolutionException;
use stdClass;


class PuestosController extends Controller
{
    ////////////////BASICAS DE PUEESTOS
    public function index(){

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','estados_puestos.des_estado','estados_puestos.val_color','estados_puestos.hex_color','clientes.nom_cliente','clientes.id_cliente','puestos_asignados.id_usuario','puestos_asignados.id_perfil', 'puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->orderby('edificios.id_edificio')
            ->orderby('plantas.id_planta')
            ->orderby('puestos.cod_puesto')
            ->where('puestos.id_puesto','>',0)
            ->paginate(50);


        $tipos = DB::table('puestos_tipos')
            ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
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

        $usuarios = DB::table('users')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('name')
        ->get();

        return view('puestos.index',compact('puestos','tipos','usuarios'));
    }

    public function search(Request $r){
        if($r->estado){
            $estados=$r->estado;
            $estados=array_filter($r->estado, "ctype_digit");
            $atributos=array_filter($r->estado, "ctype_alpha");
        } else {
            $estados=null;
            $atributos=[];
        }
        
        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','estados_puestos.des_estado','estados_puestos.val_color','estados_puestos.hex_color','clientes.nom_cliente','clientes.id_cliente','puestos_asignados.id_usuario','puestos_asignados.id_perfil', 'puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
            ->where(function($q){
                $q->wherein('puestos.id_cliente',clientes());
            })
            ->where(function($q) use($r){
                if ($r->cliente) {
                    $q->WhereIn('puestos.id_cliente',$r->cliente);
                }
            })
            ->where(function($q) use($r){
                if ($r->edificio) {
                    $q->WhereIn('puestos.id_edificio',$r->edificio);
                }
            })
            ->where(function($q) use($r){
                if ($r->planta) {
                    $q->whereIn('puestos.id_planta',$r->planta);
                }
            })
            ->where(function($q) use($r){
                if ($r->puesto) {
                    $q->whereIn('puestos.id_puesto',$r->puesto);
                }
            })
            ->where(function($q) use($r,$estados){
                if ($estados) {
                    $q->whereIn('puestos.id_estado',$estados);
                }
            })
            ->where(function($q) use($r){
                if ($r->tipo) {
                    $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
                }
            })
            ->where(function($q) use($r,$atributos){
                if(in_array('A',$atributos)){
                    $q->where('mca_acceso_anonimo','S');
                }
                if(in_array('R',$atributos)){
                    $q->where('mca_reservar','S');
                }
                if(in_array('P',$atributos)){
                    $q->wherenotnull('puestos_asignados.id_perfil');
                }
                if(in_array('U',$atributos)){
                    $q->wherenotnull('puestos_asignados.id_usuario');
                }
            })
            ->where(function($q) use($r){
                if ($r->tags) {
                    
                    if($r->andor){//Busqueda con AND
                        $puestos_tags=DB::table('tags_puestos')
                            ->select('id_puesto')
                            ->wherein('id_tag',$r->tags)
                            ->groupby('id_puesto')
                            ->havingRaw('count(id_tag)='.count($r->tags))
                            ->pluck('id_puesto')
                            ->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    } else { //Busqueda con OR
                        $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags); 
                    }
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->orderby('edificios.id_edificio')
            ->orderby('plantas.id_planta')
            ->orderby('puestos.cod_puesto')
            ->paginate(1000);
        return view('puestos.fill-tabla',compact('puestos','r'));
    }

    public function edit($id){
        
        if($id==0){
            $puesto=new puestos;
            $puesto->id=0;
            $tags="";
        } else {
            validar_acceso_tabla($id,"puestos");
            $puesto=DB::table('puestos')
                ->select('puestos.*','plantas.*','puestos_asignados.id_perfil','puestos_asignados.id_usuario','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color',
                'salas.val_capacidad','salas.mca_proyector','salas.mca_pantalla','salas.mca_videoconferencia','salas.id_cliente','salas.obs_sala','salas.mca_manos_libres','salas.mca_pizarra','salas.mca_pizarra_digital','salas.id_sala','salas.id_externo_salas')
                ->join('plantas','puestos.id_planta','plantas.id_planta')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
                ->leftjoin('salas','puestos.id_puesto','salas.id_puesto')
                ->where('puestos.id_puesto',$id)
                ->first();
        }
        $usuarios=DB::table('users')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('name')
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('id')->toArray();
                    $q->wherein('users.id',$usuarios_supervisados);
                }
            })
            ->get();

        $perfiles=DB::table('niveles_acceso')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('niveles_acceso.id_cliente',Auth::user()->id_cliente);
                    $q->orwhere('niveles_acceso.mca_fijo','S');
                } else {
                    $q->where('niveles_acceso.id_cliente',session('CL')['id_cliente']);
                    $q->orwhere('niveles_acceso.mca_fijo','S');
                }
            })
            ->where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('cod_nivel')->toArray();
                    $q->wherein('niveles_acceso.cod_nivel',$usuarios_supervisados);
                }
            })
            ->orderby('val_nivel_acceso')
            ->orderby('des_nivel_acceso')
            ->get();
        $tags=DB::table('tags')
            ->join('tags_puestos','tags.id_tag','tags_puestos.id_tag')               
            ->where('tags_puestos.id_puesto',$id)
            ->pluck('nom_tag')
            ->toarray();
        $tags=implode(",",$tags);

        $tags_cliente=DB::table('tags')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('tags.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('tags.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->pluck('nom_tag')
            ->toarray();

        $tipos = DB::table('puestos_tipos')
        ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
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

        $reservas=DB::table('reservas')
            ->join('users','users.id','reservas.id_usuario')
            ->where('id_puesto',$id)
            ->get();
        
        $asignaciones=DB::table('puestos_asignados')
            ->leftjoin('users','users.id','puestos_asignados.id_usuario')
            ->leftjoin('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')
            ->where('id_puesto',$id)
            ->get();
        $eventos=[];
        foreach($reservas as $res){
            $e=new stdClass();
            $e->title=$res->name;
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
            if($as->id_usuario!=null){
                $e->title=$as->name;
            } else {
                $e->title=$as->des_nivel_acceso;
            }
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
        
        return view('puestos.edit',compact('puesto','tags','usuarios','perfiles','tipos','eventos','tags_cliente'));

    }

    public function ver_puesto($id){
        validar_acceso_tabla($id,"puestos");
        $puesto=puestos::find($id);
        $url_puesto=explode("?",$puesto->url);
        $url=$url_puesto[0]."?800X600";
        return view('puestos.view',compact('puesto','url'));

    }

    public function delete($id){
        validar_acceso_tabla($id,"puestos");
        $puesto=puestos::find($id);
        $puesto->delete();
        $sala=salas::where('id_puesto',$id);
        $sala->delete();
        flash('puesto '.$puesto->etiqueta.' Borrada')->success();
        savebitacora('puesto '.$puesto->etiqueta. ' borrado',"Puestos","delete","OK");
        return redirect('/puestos');

    }

    public function update(Request $r){
        try{
            if(session('CL')['mca_reserva_horas']=='S')
                $r['max_horas_reservar']=time_to_dec($r->max_horas_reservar.':00','h');
            if($r->val_color=="#000000"){
                $r->request->add(['val_color'=>null]);
            }
            
            if($r->id_puesto==0){
                $puesto=puestos::create($r->all());
            } else {
                validar_acceso_tabla($r->id_puesto,"puestos");
                $puesto=puestos::find($r->id_puesto);  
                $puesto->update($r->all());

            }

            

            $puesto->mca_acceso_anonimo=$r->mca_acceso_anonimo??'N';
            $puesto->mca_reservar=$r->mca_reservar??'N';
            $r->val_icono=='undefined'?$puesto->val_icono=null:$puesto->val_icono=$r->val_icono;
            if ($r->hasFile('img_puesto')) {
                $file = $r->file('img_puesto');
                $path = '/img/puestos/';
                $img_puesto = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                $puesto->img_puesto=$img_puesto;
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_puesto);
            }
            $puesto->save();

            //Procesamos los tags
            //Borramos los tags que tenga el puesto
            tags_puestos::where('id_puesto',$puesto->id_puesto)->delete();
            //Y ahora insertamos los que vengan
            $arr_tags=explode(",",$r->tags);
            foreach($arr_tags as $tag){
                //Primero a ver si existe el tag para el cleinte
                $esta_tag=tags::where('nom_tag',$tag)->where('id_cliente',$r->id_cliente)->first();

                if(!isset($esta_tag)){
                    DB::table('tags')->insert([
                        'nom_tag'=>$tag,
                        'id_cliente'=>$r->id_cliente
                    ]);
                    $esta_tag=tags::where('nom_tag',$tag)->where('id_cliente',$r->id_cliente)->first();
                }
                DB::table('tags_puestos')->insert([
                    'id_tag'=>$esta_tag->id_tag,
                    'id_puesto'=>$puesto->id_puesto
                ]);
            }

            //Asignacion directa de puesto
            DB::table('puestos_asignados')->where('id_puesto',$puesto->id_puesto)->delete();
            if($r->id_usuario && $r->id_usuario>0){
                DB::table('puestos_asignados')->insert([
                    'id_puesto'=>$puesto->id_puesto,
                    'id_usuario'=>$r->id_usuario
                ]);
                //Notificar al usuario entrante
                $usuario=users::find($r->id_usuario);
                $str_notificacion=Auth::user()->name.' ha creado una nueva asignacion indefinida del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') para usted';
                notificar_usuario($usuario,"Se le ha asignado un nuevo puesto de forma indefinida",'emails.asignacion_puesto',$str_notificacion,metodos_notificacion_usuario($usuario->id),4,[],$puesto->id_puesto);
               
            } else {

            }
            if($r->id_perfil && $r->id_perfil>0){
                DB::table('puestos_asignados')->insert([
                    'id_puesto'=>$puesto->id_puesto,
                    'id_perfil'=>$r->id_perfil
                ]);
            } else {
                
            }

            //Salas de reuniones
            if(in_array($r->id_tipo_puesto,config('app.tipo_puesto_sala'))) {
                $sala=salas::where('id_puesto',$puesto->id_puesto)->first();
                if(!isset($sala)){
                    $sala=new salas;
                }
                $sala->val_capacidad=$r->val_capacidad;
                $sala->obs_sala=$r->obs_sala;
                $sala->id_puesto=$puesto->id_puesto;
                $sala->id_cliente=$r->id_cliente;
                $sala->mca_proyector=$r->mca_proyector?'S':'N';
                $sala->mca_pantalla=$r->mca_pantalla?'S':'N';
                $sala->mca_videoconferencia=$r->mca_videoconferencia?'S':'N';
                $sala->mca_manos_libres=$r->mca_manos_libres?'S':'N';
                $sala->mca_pizarra=$r->mca_pizarra?'S':'N';
                $sala->mca_pizarra_digital=$r->mca_pizarra_digital?'S':'N';
                $sala->id_externo_salas=$r->id_externo_salas;
                $sala->save();
            } else{
                DB::table('salas')->where('id_puesto',$puesto->id_puesto)->delete();
            }

           
            savebitacora('puesto '.$r->etiqueta. ' actualizado',"Puestos","Update","OK");
            return [
                'title' => "puestos",
                'message' => 'puesto '.$r->etiqueta. ' actualizado',
                //'url' => url('puestos')
            ];
         } catch (\Throwable $exception) {
            return [
                'title' => "puestos",
                'error' => 'ERROR: Ocurrio un error actualizando el puesto '.$r->name.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];

        }
    }

    public function print_qr(Request $r){

        if (!isset($r->lista_id)){
            return Redirect::back();
        }
        if(!is_array($r->lista_id)){
            $r->lista_id=explode(",",$r->lista_id);
        }

        if(!isset($r->tam_qr)){
            $config_print=config_clientes::where('id_cliente',session('CL')['id_cliente'])->first()->config_print_qr;
            if (isset($config_print) && isJson($config_print)){
                $config_print=json_decode($config_print,true);
                $r->request->add(['tam_qr' => $config_print['tam_qr']]); //add request
                $r->request->add(['col' => $config_print['col']]); //add request
                $r->request->add(['row' => $config_print['row']??4]); //add request
                $r->request->add(['sel_color' => $config_print['sel_color']]); //add request
                $r->request->add(['color_texto' => $config_print['color_texto']]); //add request
                $r->request->add(['mca_icono' => $config_print['mca_icono']]); //add request
                $r->request->add(['footer' => $config_print['footer']]); //add request
                $r->request->add(['header' => $config_print['header']]); //add request
                $r->request->add(['margen_left' => $config_print['margen_left']]); //add request
                $r->request->add(['margen_top' => $config_print['margen_top']]); //add request
                $r->request->add(['espacio_h' => $config_print['espacio_h']]); //add request
                $r->request->add(['espacio_v' => $config_print['espacio_v']]); //add request
                $r->request->add(['padding_qr' => $config_print['padding_qr']]); //add request
                $r->request->add(['padding_cont' => $config_print['padding_cont']]); //add request
                $r->request->add(['font_size' => $config_print['font_size']]); //add request
                $r->request->add(['page_break' => $config_print['page_break']]); //add request
            } 
        }
        $layout="layout";
            $puestos=DB::table('puestos')
            ->select('puestos.*','puestos_tipos.val_color as color_tipo','puestos_tipos.val_icono as icono_tipo','clientes.nom_cliente','clientes.img_logo')
                ->join('edificios','puestos.id_edificio','edificios.id_edificio')
                ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->where(function($q){
                    if (!isAdmin()) {
                        $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->wherein('id_puesto',$r->lista_id)
                ->get();
            if($r->formato && $r->formato=='PDF'){
                $layout="layout_simple";
                $filename='Codigos_QR Puestos_'.Auth::user()->id_cliente.'_.pdf';
                $pdf = PDF::loadView('puestos.print_qr',compact('puestos','r','layout'));
                return $pdf->download($filename);
            } else if($r->formato && $r->formato=='preview'){
                return view('puestos.fill_printarea',compact('puestos','r','layout'));
            } else {
                return view('puestos.print_qr',compact('puestos','r','layout'));
            }
        try{    
        } catch(\Exception $e){
            return Redirect::back();
        }
    }

    public function export_qr(Request $r){

        if (!isset($r->lista_id)){
            return Redirect::back();
        }
        try{
            $puestos=DB::table('puestos')
                ->join('edificios','puestos.id_edificio','edificios.id_edificio')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->where(function($q){
                    if (!isAdmin()) {
                        $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->wherein('id_puesto',$r->lista_id)
                ->get();
        
            $filename='Codigos_QR Puestos_'.Auth::user()->id_cliente.'_.xlsx';
            libxml_use_internal_errors(true); //añadido por andriy para quitar los errores de libreria
            //return view('puestos.qr_excel',compact('puestos','r'));
            return Excel::download(new ExportExcel('puestos.qr_excel',compact('puestos','r')),$filename);
        } catch(\Exception $e){
            return Redirect::back();
        }
    }
     // GESTION DE TIPOS DE PUESTO
    
     public function index_tipos(){
        $tipos = DB::table('puestos_tipos')
        ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
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
        ->where('id_tipo_puesto','>',0)
        ->get();
        
        return view('puestos.tipos.index', compact('tipos'));
    }

    public function save_config_print(Request $r){
        $config=config_clientes::find($r->id_cliente);
        $config->config_print_qr=json_encode($r->all());
        $config->save();
    }


    ///////////////////TIPOS DE SPUESTOS/////////////////////

    public function tipos_edit($id=0){
        if($id==0){
            $tipo=new puestos_tipos();
        } else {
            $tipo = puestos_tipos::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
       
        return view('puestos.tipos.edit', compact('tipo','Clientes','id'));
    }

    public function tipos_save(Request $r){
        try {
            
            if($r->id==0){
                $tipo=puestos_tipos::create($r->all());
            } else {
                $tipo=puestos_tipos::find($r->id);
                $tipo->update($r->all());
                $tipo->mca_liberar_auto=$r->mca_liberar_auto??'N';
                $tipo->mca_reserva_masiva=$r->mca_reserva_masiva??'N';
                $tipo->mca_reserva_multiple=$r->mca_reserva_multiple??'N';
                $tipo->mca_checkin=$r->mca_checkin??'N';
                $tipo->mca_fijo=$r->mca_fijo??'N';
                $tipo->mca_antelacion_obligatoria=$r->mca_antelacion_obligatoria??'N';
                $tipo->save();
            }
            //Slots de reserva
            if(isset($r->hora_inicio)){
                $slots=[];
                foreach($r->hora_inicio as $key=>$value){
                    if($value!=null){
                        $slot=new stdClass;
                        $slot->hora_inicio=$value;
                        $slot->hora_fin=$r->hora_fin[$key];
                        $slots[]=$slot;
                    }
                }
                $tipo->slots_reserva=$slots;
                $tipo->save();
            }

            savebitacora('Tipo de puesto creado '.$r->des_tipo_puesto,"Puestos","tipos_save","OK");
            return [
                'title' => "Tipos de puesto",
                'message' => 'Tipo de puesto '.$r->des_tipo_puesto. ' actualizado con exito',
                'url' => url('/puestos/tipos')
            ];
        } catch (\Throwable $exception) {
            savebitacora('ERROR: Ocurrio un error creando tipo de puesto '.$r->des_tipo_puesto.' '.$exception->getMessage() ,"Puestos","tipos_save","ERROR");
            return [
                'title' => "Tipos de puesto",
                'error' => 'ERROR: Ocurrio un error actualizando el tipo de puesto '.$r->des_tipo_puesto.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function tipos_delete($id=0){
        try {
            $tipo = puestos_tipos::findorfail($id);
            //Vamos a comprobar si tiene puestos el tipo
            $hay_puestos=DB::table('puestos')->where('id_tipo_puesto',$id)->count();
            if($hay_puestos>0){
                flash('ERROR: Ocurrio un error borrando Tipo de puesto '.$tipo->des_tipo_puesto.' Existen puestos de este tipo. Borrelos primero desde Parametrizacion > Puestos')->error();
                return back()->withInput();
            }

            $tipo->delete();
            savebitacora('Tipo de puesto borrado '.$tipo->des_tipo_puesto,"Puestos","tipos_delete","OK");
            flash('Tipo de puesto '.$tipo->des_tipo_puesto.' borrado')->success();
            return back()->withInput();
        } catch (\Throwable $exception) {
            flash('ERROR: Ocurrio un error borrando Tipo de puesto '.$tipo->des_tipo_puesto.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
    }

////////////////////////VISUALIZACION DE PUESTOS EN LA WEB //////////////////////////////

    public function mapa(Request $r){

        if(isset($r->fecha)){
            $fecha_mirar=Carbon::parse(adaptar_fecha($r->fecha));
        } else {
            $fecha_mirar=Carbon::now();
        }
        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo','puestos_tipos.des_tipo_puesto','users.name as usuario_usando', 'puestos.val_color as color_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->leftjoin('users','puestos.id_usuario_usando','users.id')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->where('puestos.id_puesto','<>',0)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.cod_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('edificios.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('edificios.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q) use($fecha_mirar){
                $q->wheredate('fec_reserva',$fecha_mirar);
                $q->orwhereraw("'".$fecha_mirar->format('Y-m-d')."' between fec_reserva AND fec_fin_reserva");
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('reservas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('reservas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',Auth::user()->id)
            ->where(function($q) use($fecha_mirar){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".$fecha_mirar->format('Y-m-d')."' between fec_desde AND fec_hasta");
            })
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',Auth::user()->cod_nivel)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',Auth::user()->cod_nivel)
            ->get();
        
        return view('puestos.'.collect(request()->segments())->last(),compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','r'));
    }

    public function plano(Request $r){
        if(isset($r->fecha)){
            $fecha_mirar=Carbon::parse(adaptar_fecha($r->fecha));
        } else {
            $fecha_mirar=Carbon::now();
        }
        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado', 'puestos.val_color as color_puesto','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->where('puestos.id_puesto','<>',0)
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('edificios.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('edificios.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q) use($fecha_mirar){
                $q->wheredate('fec_reserva',$fecha_mirar);
                $q->orwhereraw("'".$fecha_mirar->format('Y-m-d')."' between fec_reserva AND fec_fin_reserva");
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('reservas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('reservas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',Auth::user()->id)
            ->where(function($q) use($fecha_mirar){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".$fecha_mirar->format('Y-m-d')."' between fec_desde AND fec_hasta");
            })
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',Auth::user()->cod_nivel)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',Auth::user()->cod_nivel)
            ->get();
        
        return view('puestos.plano',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','r'));
    }

    function ver_en_mapa($id){
        $cualpuesto=puestos::where('token',$id)->first();

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where('puestos.id_planta',$cualpuesto->id_planta)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->where('edificios.id_edificio',$cualpuesto->id_edificio)
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('edificios.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('edificios.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->where('id_edificio',$puestos->first()->id_edificio)
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q){
                $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('reservas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('reservas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',Auth::user()->id)
            ->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
            })
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',Auth::user()->cod_nivel)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',Auth::user()->cod_nivel)
            ->get();
        
        return view('puestos.plano',compact('puestos','cualpuesto','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil'));
    }

    function ver_companeros(Request $r){
     
        if(isset($r->fecha)){
            $fecha_mirar=Carbon::parse(adaptar_fecha($r->fecha));
        } else {
            $fecha_mirar=Carbon::now();
        }
        

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q) use($fecha_mirar){
                $q->wheredate('fec_reserva',$fecha_mirar);
                $q->orwhereraw("'".$fecha_mirar->format('Y-m-d')."' between fec_reserva AND fec_fin_reserva");
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('reservas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('reservas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q){
                $q->where('users.id_departamento',Auth::user()->id_departamento);
            })
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where(function($q) use($fecha_mirar){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".$fecha_mirar->format('Y-m-d')."' between fec_desde AND fec_hasta");
            })
            ->where(function($q){
                $q->where('users.id_departamento',Auth::user()->id_departamento);
            })
            ->get();


        $asignados_miperfil=Collect([]);
        
        $asignados_nomiperfil=Collect([]);

        $puestos_mostrar=array_merge($reservas->pluck('id_puesto')->toArray(),$asignados_usuarios->pluck('id_puesto')->toArray());


        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->wherein('puestos.id_puesto',$puestos_mostrar)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
            ->select('id_edificio','des_edificio')
            ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
            ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('edificios.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->wherein('id_edificio',$puestos->pluck('id_edificio')->toArray())
            ->get();
        
        return view('compas.index',compact('puestos_mostrar','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','r'));
    }

    function save_pos($id,$top,$left,$offtop,$offleft){
        $puesto=puestos::find($id);
        $puesto->top=round($top);
        $puesto->left=round($left);
        $puesto->offset_top=round($offtop);
        $puesto->offset_left=round($offleft);
        $puesto->save();
    }


    ///////////////////////ACCIONES SOBRE PUESTOS///////////////////////////
    public function ronda_limpieza(Request $r){
        //Primero asegurarnos de que tiene acceso para los puestos
        if($r->tip_ronda=='L')
        {
            $tipo_ronda="limpieza";
        } else {
            $tipo_ronda="mantenimiento";
        }
        $puestos=DB::table('puestos')
            ->wherein('id_puesto',$r->lista_id)
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('puestos.id_edificio')
            ->orderby('puestos.id_planta')
            ->orderby('puestos.id_puesto')
            ->get();

        $usuarios=DB::table('users')
            ->wherein('id',$r->lista_limpiadores)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $ronda=rondas::create(['fec_ronda'=>Carbon::now(),'des_ronda'=>$r->des_ronda,'user_creado'=>Auth::user()->id,'id_cliente'=>Auth::user()->id_cliente, 'tip_ronda'=>$r->tip_ronda]);
        
        //Cuerpo del mensaje de notificacion
        $body=Auth::user()->name. "ha creado una nueva ronda de ".$tipo_ronda." en la que usted debe participar <br>";
            $body.="Puestos afectados <br>";
            $planta="";
            $edificio="";
            $cuenta=0;
            foreach($puestos as $p){
                if($edificio!=$p->des_edificio){
                    $edificio=$p->des_edificio;
                    $body.="<br>".chr(13)."EDIFICIO ".$p->des_edificio;
                }
                if($planta!=$p->des_planta){
                    $planta=$p->des_planta;
                    $body.="<br>".chr(13)."PLANTA ".$p->des_planta."<br>".chr(13);
                    $cuenta=0;
                }
                $body.=$p->cod_puesto."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                $cuenta++;
                if($cuenta==8){
                    $body.="<br>".chr(13)."<br>".chr(13);
                    $cuenta=0;
                }
            }


        foreach($usuarios as $u){
            limpiadores::create(['id_ronda'=>$ronda->id_ronda,'id_limpiador'=>$u->id]);
            notificar_usuario($u,'Creada nueva ronda de limpieza','emails.asignacion_puesto',$body,metodos_notificacion_usuario($u->id),5,[],$ronda->id_ronda);
        }
        foreach($puestos as $p){
            puestos_ronda::create(['id_ronda'=>$ronda->id_ronda,'fec_inicio'=>Carbon::now(),'id_puesto'=>$p->id_puesto]);
        }
        

        //dd($ronda);
        savebitacora('Ruta de '.$tipo_ronda.' '.$r->des_ronda.' creada para '.count($r->lista_id).' puestos y '.count($r->lista_limpiadores).' empleados de '.$tipo_ronda,"Puestos","ronda_".$tipo_ronda,"OK");
        return [
            'title' => "Ronda de ".$tipo_ronda,
            'mensaje' => 'Ronda de '.$tipo_ronda.' '.$r->des_ronda.' creada para '.count($r->lista_id).' puestos y '.count($r->lista_limpiadores).' empleados de '.$tipo_ronda,
            //'url' => url('puestos')
        ];
    }

    public function cambiar_anonimo(Request $r){
        $estado=$r->estado??'N';
        $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('puestos.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->wherein('id_puesto',$r->lista_id)
        ->update(['mca_acceso_anonimo'=>$estado]);
        savebitacora('Cambiado el acceso anonimo a '.$estado.' para los puestos '.implode(', ',$r->lista_id),"Puestos","cambiar_anonimo","OK");
        return [
            'title' => "Acceso anonimo",
            'mensaje' => 'Cambiado el acceso anonimo a '.$estado.' para los puestos '.implode(', ',$r->lista_id),
            //'url' => url('puestos')
        ];
    }

    public function cambiar_reserva(Request $r){
        $estado=$r->estado??'N';
        $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('puestos.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->wherein('id_puesto',$r->lista_id)
        ->update(['mca_reservar'=>$estado]);
        savebitacora('Cambiado el permiso de reserva a '.$estado.' para los puestos '.implode(', ',$r->lista_id),"Puestos","cambiar_reserva","OK");
        return [
            'title' => "Acceso anonimo",
            'mensaje' => 'Cambiado el permiso de reserva a '.$estado.' para los puestos '.implode(', ',$r->lista_id),
            //'url' => url('puestos')
        ];
    }

    public function generar_token(){

        $puestos=DB::table('puestos')
            ->wherenull('token')
            ->get();

        foreach($puestos as $p){
            DB::table('puestos')->where('id_puesto',$p->id_puesto)->update([
                'token'=>Str::random(50)
            ]);
        }
    }

    public function modificar_puestos(Request $r){
        //dd($r->all());
        try{
            $listaid=explode(",",$r->lista_id);
            $cosas="";
            $valores=[];
            
            if($r->des_puesto){
                $valores["des_puesto"]=$r->des_puesto;
                $cosas.=" | Nombre: ".$r->des_puesto;
            }
            if($r->id_estado){
                $valores["id_estado"]=$r->id_estado;
                $cosas.=" | Estado: ".$r->id_estado;
            }
            if($r->val_color){
                $valores["val_color"]=$r->val_color;
                $cosas.=" | Color: ".$r->val_color;
            }
            if($r->val_icono && $r->val_icono!="empty"){
                $valores["val_icono"]=$r->val_icono;
                $cosas.=" | Icono: ".$r->val_icono;
            }
            if($r->id_edificio){
                $valores["id_edificio"]=$r->id_edificio;
                $cosas.=" | Edificio: ".$r->id_edificio;
            }
            if($r->id_planta){
                $valores["id_planta"]=$r->id_planta;
                $cosas.=" | Planta: ".$r->id_planta;
            }
            if($r->id_tipo_puesto){
                $valores["id_tipo_puesto"]=$r->id_tipo_puesto;
                $cosas.=" | Tipo de puesto: ".$r->id_tipo_puesto;
            }
            if($r->mca_acceso_anonimo){
                $valores["mca_acceso_anonimo"]=$r->mca_acceso_anonimo;
                $cosas.=" | Acceso anonimo: ".$r->mca_acceso_anonimo;
            }
            if($r->mca_reservar){
                $valores["mca_reservar"]=$r->mca_reservar;
                $cosas.=" | Permitir reserva: ".$r->mca_reservar;
            }
            if($r->max_horas_reservar){
                $valores["max_horas_reservar"]=time_to_dec($r->max_horas_reservar.':00','h');
                $cosas.=" | Tiempo maximo de reserva: ".$r->max_horas_reservar;
            }
            if($r->id_perfil){
                //Asignacion directa de puesto
                foreach($listaid as $id){
                    DB::table('puestos_asignados')->where('id_puesto',$id)->delete();
                    DB::table('puestos_asignados')->insert([
                        'id_puesto'=>$id,
                        'id_perfil'=>$r->id_perfil
                    ]);
                }
                $cosas.=" | Perfil: ".$r->id_perfil;
            }
            if($r->tags){
                //Procesamos los tags
                $arr_tags=explode(",",$r->tags);
                foreach($listaid as $id){
                    //Borramos los tags que tenga el puesto
                    tags_puestos::where('id_puesto',$id)->delete();
                    $id_cliente=puestos::find($id)->id_cliente;
                    //Y ahora insertamos los que vengan
                    foreach($arr_tags as $tag){
                        //Primero a ver si existe el tag para el cleinte
                        $esta_tag=tags::where('nom_tag',$tag)->where('id_cliente',$id_cliente)->first();

                        if(!isset($esta_tag)){
                            DB::table('tags')->insert([
                                'nom_tag'=>$tag,
                                'id_cliente'=>$id_cliente
                            ]);
                            $esta_tag=tags::where('nom_tag',$tag)->where('id_cliente',$id_cliente)->first();
                        }
                        DB::table('tags_puestos')->insert([
                            'id_tag'=>$esta_tag->id_tag,
                            'id_puesto'=>$id
                        ]);
                    }
                }
                
                $cosas.=" | Tags: ".$r->tags;
            }
            if(sizeof($valores)>0)
                $puestos=puestos::wherein('id_puesto',$listaid)->update($valores);

            savebitacora('Actualizacion masiva de puestos '.implode(";",$listaid)." Atributos modificados :".$cosas,"OK");
            return [
                'title' => "Borrar puestos",
                'message' => 'Actualizacion masiva de puestos '.implode(";",$listaid)." Atributos modificados :".$cosas,
                'url' => url('puestos')
            ];
        } catch(\Excveption $e){
            return [
                'title' => "puestos",
                'error' => 'ERROR: Ocurrio un error actualizando los puestos '.$r->name.' '.mensaje_excepcion($e),
                //'url' => url('sections')
            ];
        }
        
    }

    public function borrar_puestos(Request $r){
     
        savebitacora('Borrado masivo de puestos '.implode(";",$r->lista_id),"OK");
        puestos::wherein('id_puesto',$r->lista_id)->delete();
        return [
            'title' => "Borrar puestos",
            'message' => count($r->lista_id).' puestos borrados: '.implode(', ',$r->lista_id),
            'url' => url('puestos')
        ];
    }
    
    
    public function accion_estado(Request $r){

        $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('puestos.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->wherein('id_puesto',$r->lista_id)
        ->get();

        $e=DB::table('estados_puestos')->where('id_estado',$r->estado)->first();

        foreach($puestos as $puesto){
            $p=puestos::find($puesto->id_puesto);
            $p->id_estado=$r->estado;
            $p->fec_ult_estado=Carbon::now();
            if($r->estado==1){
                $p->id_usuario_usando=null;
            }
            $p->save();
            //Lo añadimos al log
            logpuestos::create(['id_puesto'=>$puesto->id_puesto,'id_estado'=>$r->estado,'id_user'=>Auth::user()->id,'fecha'=>Carbon::now()]);
            
        }

        savebitacora('Cambio de puestos '.implode(",",$r->lista_id). ' a estado '.$r->estado,"Puestos","accion_estado","OK");
        return [
            'title' => "Puestos",
            'mensaje' => count($r->lista_id).' puestos actualizados a '.$e->des_estado,
            'label'=>$e->des_estado,
            'color'=>$e->val_color,
            'url' => url('puestos')
        ];
    }

    /////////////////////////////BLOQUEO DE PUESTIOS//////////////////////
    //FUncion para mostrar la lista de bloqueos
    public function index_bloqueo(){

        $bloqueos=DB::table('bloqueo_programado')
            ->join('clientes','bloqueo_programado.id_cliente','clientes.id_cliente')
            ->leftjoin('users','bloqueo_programado.usu_audit','users.id')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('bloqueo_programado.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('bloqueo_programado.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $turnos=DB::table('turnos')
            ->join('clientes','turnos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('turnos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('turnos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $puestos=DB::table('puestos')
            ->join('bloqueo_puestos','puestos.id_puesto','bloqueo_puestos.id_puesto')
            ->join('bloqueo_programado','bloqueo_programado.id_bloqueo','bloqueo_puestos.id_bloqueo')
            ->join('clientes','bloqueo_programado.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('bloqueo_programado.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('bloqueo_programado.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        return view('puestos.bloqueo.index',compact('puestos','turnos','bloqueos'));
    }

    //Mostrar el editor del bloqueo
    public function bloqueo_edit($id=0){
        if($id==0){
            $tipo=new bloqueos();
        } else {
            $tipo = bloqueos::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        $puestos=DB::table('puestos')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $turnos=DB::table('turnos')
            ->where('turnos.id_cliente',Auth::user()->id_cliente)
            ->get();

        $puestos_bloqueados=DB::table('bloqueo_puestos')
            ->join('puestos','puestos.id_puesto','bloqueo_puestos.id_puesto')
            ->join('bloqueo_programado','bloqueo_programado.id_bloqueo','bloqueo_puestos.id_bloqueo')
            ->where('bloqueo_programado.id_bloqueo',$id)
            ->get();

        return view('puestos.bloqueo.edit', compact('tipo','Clientes','puestos','turnos','puestos_bloqueados','id'));
    }
}
