<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\logpuestos;
use App\Models\rondas;
use App\Models\users;
use App\Models\salas;
use App\Models\incidencias_tipos;
use App\Models\incidencias;
use App\Models\causas_cierre;
use App\Models\incidencias_acciones;
use App\Models\estados_incidencias;
use App\Models\incidencias_postprocesado;
use App\Http\Controllers\APIController;

use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use PDF;
use File;
use Str;
use Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class IncidenciasController extends Controller
{
  //////////////FUNCIONES AUXILIARES////////////////////
  //Ejemplo de JSON de respuesta
//   {
//     "incidencias.id_incidencia_externo": "@R:id_incidencia",
//     "incidencias.url_detalle_incidencia": "@R:url_detalle",
//     "incidencias.mca_sincronizada": "S"
//   }
    //Busca dentro de un array una clave definida por la variable search_path y devuelve el valor si lo encuentra, null si no
    function find_in_ArrayRecursive($someArray,$search_path) {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($someArray), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $k => $v) {
            $indent = str_repeat('&nbsp;', 10 * $iterator->getDepth());
            // Not at end: show key only
            if (!$iterator->hasChildren()) {
                for ($p = array(), $i = 0, $z = $iterator->getDepth(); $i <= $z; $i++) {
                    $p[] = $iterator->getSubIterator($i)->key();
                }
                $path = implode('|', $p);
                if($path === $search_path){
                    return $v;
                }
            }
        }
        return null;
    }

    private function procesar_respuesta($reglas,$respuesta,$id_incidencia,$id_puesto){
        try{
            $reglas=json_decode($reglas,true);
            if(isjson($respuesta)){
                $respuesta=json_decode($respuesta,true);
            }
            //Cada una de las reglas de la respuesta se trata por separado
            foreach($reglas as $key=>$value){
                $tabla=explode(".",$key)[0];
                $campo=explode(".",$key)[1];
                $pk=DB::select("SHOW KEYS FROM ".$tabla." WHERE Key_name = 'PRIMARY'")[0]->Column_name;
                //Ahora a ver si es un valor o parte de la respuesta
                if(strpos($value,'@R:')!==false){
                    $campo_respuesta=str_replace("@R:","",$value);
                    $dato=$this->find_in_ArrayRecursive($respuesta,$campo_respuesta);
                } else {
                    $dato=$value;
                }
                //Y ahora actualizamos BDD
                DB::table($tabla)->where($pk,${$pk})->update([$campo=>$dato]);
            }
        } catch(\Throwable $e){
            Log::error("Postprocesado de RESPUESTA HTTP POST de incidencia  ".$id_incidencia." ERROR: ".$e->getMessage());
        }
    }

    private function reemplazar_parametros($subject,$inc){
        $data=DB::table('incidencias')
            ->join('users','users.id','incidencias.id_usuario_apertura')
            ->join('puestos','puestos.id_puesto','incidencias.id_puesto')
            ->join('plantas','plantas.id_planta','puestos.id_planta')
            ->join('edificios','edificios.id_edificio','plantas.id_edificio')
            ->join('estados_incidencias','estados_incidencias.id_estado','incidencias.id_estado')
            ->join('incidencias_tipos','incidencias_tipos.id_tipo_incidencia','incidencias.id_tipo_incidencia')
            ->where('id_incidencia',$inc->id_incidencia)
            ->first();
        $data->url_base=env('APP_URL');
        $accion=DB::table('incidencias_acciones')
            ->where('id_incidencia',$inc->id_incidencia)
            ->first();
            
        if(isset($accion)){
            $data=(object) array_merge((array) $data, (array) $accion);
        }
        preg_match_all("/(?<=#).*?(?=#)/", $subject, $match);
        foreach($match[0] as $value){
            if(isset($data->$value)){
                $subject=str_replace('#'.$value.'#',$data->$value,$subject);
            } else {
                Log::debug("\$data->".$value." no existe");
            }
            
        }
        return $subject;
    }
        
   
    //////////////////////////////////////////////////////
  
    //LISTADO DE INCIDENCIAS
    public function index($f1=0,$f2=0){
        $f1=$f1==0?Carbon::now()->startOfMonth()->subMonth():Carbon::parse($f1);
        $f2=$f2==0?Carbon::now()->endOfMonth():Carbon::parse($f2);
        $fhasta=clone($f2);
        $fhasta=$fhasta->addDay();
        $incidencias=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','users.name')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->whereBetween('fec_apertura',[$f1,$fhasta])
            ->wherenull('incidencias.fec_cierre')
            ->orderby('fec_apertura','desc')
            ->get();

        $solicitudes=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','users.name')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('clientes','incidencias.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('incidencias.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('incidencias.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where('incidencias.id_puesto',0)
            ->whereBetween('fec_apertura',[$f1,$fhasta])
            ->wherenull('incidencias.fec_cierre')
            ->orderby('fec_apertura','desc')
            ->get();
        
        $puestos=DB::table('puestos')
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
            ->get();
        $mostrar_graficos=1;
        $mostrar_filtros=1;
        if(\Request::route()->getName()=='incidencias.index'){
            $titulo_pagina="Gestión de incidencias";
            $pagina="incidencias";
        } else {
            $titulo_pagina="Gestión de solicitudes";
            $pagina="solicitudes";
        }
        
        $tipo='embed';
        return view('incidencias.index',compact('incidencias','f1','f2','puestos','mostrar_graficos','mostrar_filtros','titulo_pagina','tipo','solicitudes','pagina'));
    }

    public function mis_incidencias($f1=0,$f2=0){
        $f1=$f1==0?Carbon::now()->startOfMonth()->subMonth():Carbon::parse($f1);
        $f2=$f2==0?Carbon::now()->endOfMonth():Carbon::parse($f2);
        $fhasta=clone($f2);
        $fhasta=$fhasta->addDay();
        $incidencias=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','users.name')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->whereBetween('fec_apertura',[$f1,$fhasta])
            ->where('incidencias.id_usuario_apertura',Auth::user()->id)
            ->wherenull('incidencias.fec_cierre')
            ->orderby('fec_apertura','desc')
            ->get();

        $solicitudes=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','users.name')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('clientes','incidencias.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('incidencias.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('incidencias.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where('incidencias.id_puesto',0)
            ->where('incidencias.id_usuario_apertura',Auth::user()->id)
            ->whereBetween('fec_apertura',[$f1,$fhasta])
            ->wherenull('incidencias.fec_cierre')
            ->orderby('fec_apertura','desc')
            ->get();
        
        $puestos=DB::table('puestos')
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
            ->get();
        $mostrar_graficos=0;
        $mostrar_filtros=0;
        if(\Request::route()->getName()=='incidencias.mis_incidencias'){
            $titulo_pagina="Mis incidencias";
            $pagina="incidencias";
        } else {
            $titulo_pagina="Mis solicitudes";
            $pagina="solicitudes";
        }
        $tipo='mis';
        return view('incidencias.index',compact('incidencias','f1','f2','puestos','mostrar_graficos','mostrar_filtros','titulo_pagina','tipo','solicitudes','pagina'));
    }
    
    //BUSCAR INCIDENCIAS
    public function search(Request $r){
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        if($r->estado){
            $estados=$r->estado;
            $estados=array_filter($r->estado, "ctype_digit");
            $atributos=array_filter($r->estado, "ctype_alpha");
        } else {
            $estados=null;
            $atributos=[];
        }

        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
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
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $lista_puestos=$puestos->pluck('id_puesto')->toArray();

        $incidencias=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*','estados_incidencias.des_estado as estado_incidencia','estados_incidencias.id_estado_salas as id_estado_salas','causas_cierre.des_causa','users.name')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->wherein('incidencias.id_puesto',$lista_puestos)
            ->whereBetween('fec_apertura',[Carbon::parse($f1),Carbon::parse($f2)])
            ->where(function($q) use($r){
                if($r->ac=='C'){
                    $q->wherenotnull('fec_cierre');
                }
                if($r->ac=='A'){
                    $q->wherenull('fec_cierre');
                }
            })
            ->where(function($q) use($r){
                if ($r->estado_inc) {
                    $q->whereIn('incidencias.id_estado',$r->estado_inc);
                }
            })
            ->where(function($q) use($r){
                if ($r->procedencia) {
                    $q->whereIn('incidencias.val_procedencia',$r->procedencia);
                }
            })
            ->where(function($q) use($r){
                if ($r->tipoinc) {
                    $q->whereIn('incidencias.id_tipo_incidencia',$r->tipoinc);
                }
            })
            ->where(function($q) use($r){
                if ($r->user) {
                    $q->whereIn('incidencias.id_usuario_apertura',$r->user);
                }
            })
            ->orderby('fec_apertura','desc')
            ->where('incidencias.id_puesto','>',0)
            ->get();

        $solicitudes=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','estados_incidencias.des_estado as estado_incidencia','estados_incidencias.id_estado_salas as id_estado_salas','causas_cierre.des_causa','users.name')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->join('clientes','incidencias.id_cliente','clientes.id_cliente')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('incidencias.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where('incidencias.id_puesto',0)
            ->whereBetween('fec_apertura',[Carbon::parse($f1),Carbon::parse($f2)])
            ->where(function($q) use($r){
                if($r->ac=='C'){
                    $q->wherenotnull('fec_cierre');
                }
                if($r->ac=='A'){
                    $q->wherenull('fec_cierre');
                }
            })
            ->where(function($q) use($r){
                if ($r->cliente) {
                    $q->WhereIn('incidencias.id_cliente',$r->cliente);
                }
            })
            ->where(function($q) use($r){
                if ($r->estado_inc) {
                    $q->whereIn('incidencias.id_estado',$r->estado_inc);
                }
            })
            ->where(function($q) use($r){
                if ($r->procedencia) {
                    $q->whereIn('incidencias.val_procedencia',$r->procedencia);
                }
            })
            ->where(function($q) use($r){
                if ($r->tipoinc) {
                    $q->whereIn('incidencias.id_tipo_incidencia',$r->tipoinc);
                }
            })
            ->where(function($q) use($r){
                if ($r->user) {
                    $q->whereIn('incidencias.id_usuario_apertura',$r->user);
                }
            })
            ->orderby('fec_apertura','desc')
            ->get();
        $f1=Carbon::parse($f1);
        $f2=Carbon::parse($f2);
        $mostrar_graficos=1;
        $mostrar_filtros=1;
        if(\Request::route()->getName()=='incidencias.search'){
            $titulo_pagina="Ver incidencias";
            $pagina="incidencias";
            $template='incidencias.fill_tabla_incidencias';
        } else {
            $titulo_pagina="Ver solicitudes";
            $pagina="solicitudes";
            $template='incidencias.fill_tabla_solicitudes';
        }

        if ($r->wantsJson()) {
            return ["incidencias"=>$incidencias,"solicitudes"=>$solicitudes];
        } else {
            return view($template,compact('incidencias','f1','f2','puestos','r','mostrar_graficos','mostrar_filtros','titulo_pagina','pagina','solicitudes'));
        }
        
    }

    public function show(Request $r,$id){
        $incidencias=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*','estados_incidencias.des_estado as estado_incidencia','estados_incidencias.id_estado_salas as id_estado_salas','causas_cierre.des_causa')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where('incidencias.id_puesto','<>',0)
            ->where('incidencias.id_incidencia',$id)
            ->get();


        $solicitudes=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','users.name')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('clientes','incidencias.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('incidencias.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('incidencias.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where('incidencias.id_puesto',0)
            ->where('incidencias.id_incidencia',$id)
            ->get();

        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_puesto',$incidencias->first()->id_puesto??0)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $lista_puestos=$puestos->pluck('id_puesto')->toArray();

        
        $f1=Carbon::now()->startOfmonth();
        $f2=Carbon::now()->endOfmonth();
        $mostrar_graficos=0;
        $mostrar_filtros=0;
        $open=$id;
        $titulo_pagina=$incidencias->first()->des_incidencia??'';
        $tipo='embed';

        if($incidencias->count()>0){
            $titulo_pagina="Ver incidencia";
            $pagina="incidencias";
        } else {
            $titulo_pagina="Ver solicitud";
            $pagina="solicitudes";
        }

        return view('incidencias.index',compact('incidencias','f1','f2','puestos','r','mostrar_graficos','mostrar_filtros','titulo_pagina','open','tipo','solicitudes','pagina'));
    }

    //USUARIOS ABRIR INCIDENCIAS
    public function nueva_incidencia($puesto,$tipo='normal'){
        $referer = request()->headers->get('referer');
        if(strpos($referer,'/puesto/')){
            $referer='scan';
        } else {
            $referer='incidencias';
        }

        if(strlen($puesto)>10){  //Es un token
            $puesto=DB::table('puestos')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->where('token',$puesto)
                ->first();
        } else { //Es un id
            $puesto=DB::table('puestos')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->where('id_puesto',$puesto)
                ->first();
        }
        if(!isset($puesto)){
            return view('scan.puesto_no_encontrado',compact('puesto'));
        }
        validar_acceso_tabla($puesto->id_puesto,'puestos');
        if($puesto->id_puesto==0){
            $idcliente=Auth::user()->id_cliente;
        } else {
            $idcliente=$puesto->id_cliente;
        }
        $config=DB::table('config_clientes')->where('id_cliente',$idcliente)->first();
        $tipos=DB::table('incidencias_tipos')
            ->join('clientes','incidencias_tipos.id_cliente','clientes.id_cliente')
            ->where(function($q) use($puesto,$idcliente){
                $q->where('incidencias_tipos.id_cliente',$idcliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('incidencias_tipos.mca_fijo','S');
                }
                
                })
            ->where(function($q) use($puesto){
                if($puesto->id_puesto!=0){
                    $q->orwhereraw('FIND_IN_SET('.$puesto->id_tipo_puesto.', list_tipo_puesto) <> 0');
                }
            })
            ->where(function($q) use($puesto){
                if($puesto->id_puesto!=0){
                    $q->wherein('incidencias_tipos.mca_aplica',['I','A']);
                } else {
                    $q->wherein('incidencias_tipos.mca_aplica',['S','A']);
                }
            })
            ->orderby('mca_fijo')
            ->orderby('nom_cliente')
            ->orderby('incidencias_tipos.des_tipo_incidencia')
            ->get();

        if($tipo=='embed' || $tipo=='mis'){
            return view('incidencias.fill_frm_incidencia',compact('puesto','tipos','referer','config','tipo'));
        } else {
            return view('incidencias.nueva_incidencia',compact('puesto','tipos','referer','config','tipo'));
        }
        
    }

    //Funcion para abrir incidencia cuando no has seleccionado puesto 
    public function selector_puestos(){
        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();
        return view('incidencias.selector_puestos',compact('puestos'));
    }

    /**
     * Get the request's data from the request.
     *
     * @param Illuminate\Http\Request\Request $request
     * @return array
     */
    protected function getDataincidencia(Request $request)
    {
        $rules = [
            'des_incidencia' => 'nullable|string|min:1|max:500',
            'txt_incidencia' => 'nullable|string|min:1|max:65000',
            'img_attach1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,mp4,avi,mpg,doc,docx,pdf|max:14096',
            'img_attach2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,mp4,avi,mpg,doc,docx,pdf|max:14096',
            'id_puesto'=> 'required',
            'img1'=>'nullable',
            'img2'=>'nullable',
            'adjuntos'=>'nullable',
            'procedencia'=>'nullable',
            'url_detalle_incidencia'=>'nullable',
            'origen'=>'nullable',
            'pin'=>'nullable',
        ];
        $data = $request->validate($rules);
        return $data;
    }
    
    public function save(Request $r){
        $data=$this->getDataincidencia($r);
        $puesto=puestos::find($r->id_puesto);
        if($puesto->id_puesto==0){
            $idcliente=Auth::user()->id_cliente;
            $destipo="Solicitud";
        } else {
            $idcliente=$puesto->id_cliente;
            $destipo="Incidencia";
        }
        $cambio_estado=null;
        $procedencia=$data['procedencia']??'web';
        if(isset($r->referer) && $r->referer=='scan'){
            $procedencia=$r->referer;
        }
        $tipo=incidencias_tipos::find($r->id_tipo_incidencia);
        try{     
            if(isset($r->adjuntos) and is_array($r->adjuntos)){
                $adjuntos=$r->adjuntos[0];
                $adjuntos=explode(",",$adjuntos);
                $indice=1;
                foreach($adjuntos as $key=>$value){
                    $var="img".$indice;
                    $$var=$value;
                    $indice++;
                }
            }

            $inc=new incidencias;
            $inc->des_incidencia=$data['des_incidencia']??null;
            $inc->txt_incidencia=$data['txt_incidencia']??null;
            $inc->id_cliente=$idcliente;
            $inc->id_usuario_apertura=$r->id_usuario??Auth::user()->id;
            $inc->fec_apertura=Carbon::now();
            $inc->id_tipo_incidencia=$r->id_tipo_incidencia;
            $inc->id_puesto=$puesto->id_puesto;
            $inc->img_attach1=$img1??null;
            $inc->img_attach2=$img2??null;
            $inc->id_estado=$data['id_estado']??$tipo->id_estado_inicial;
            $inc->id_estado_vuelta_puesto=$puesto->id_estado;
            $inc->val_procedencia=$procedencia;
            $inc->id_incidencia_salas=$r->id_incidencia_salas??null;
            $inc->id_incidencia_externo=$r->id_incidencia_externo??null;
            $inc->url_detalle_incidencia=$data['url_detalle_incidencia']??null;
            $inc->origen=$data['origen']??null;
            $inc->pin=$data['pin']??null;
            $inc->save();

            //Marcamos el puesto como chungo
            $puesto->mca_incidencia='S';
            $puesto->save();
            if($r->referer=='incidencias'){
                if($r->tipo??'normal'=='mis'){
                    $url_vuelta='incidencias/mis_incidencias';
                } else {
                    $url_vuelta='incidencias';
                }
            } else if($r->referer=='solicitudes'){
                if($r->tipo??'normal'=='mis'){
                    $url_vuelta='solicitudes/mis_solicitudes';
                } else {
                    $url_vuelta='solicitudes';
                }
            } else {
                $url_vuelta=null;
            }
            try{
                $this->post_procesado_incidencia($inc,'C',$procedencia);
                savebitacora($destipo.' de tipo '.$tipo->des_tipo_incidencia. ' '.$r->des_incidencia.' creada por '.Auth::user()->name,"Incidencias","save","OK");
                return [
                    'title' => "Crear ".$destipo." en puesto ".$puesto->cod_puesto,
                    'message' => $destipo." de tipo ".$tipo->des_tipo_incidencia.' creada. Muchas gracias',
                    'url' => url($url_vuelta),
                    'id' => $inc->id_incidencia,
                    'result'=>'ok',
                    'timestamp'=>Carbon::now(),
                ];
            } catch(\Exception $exception){
                savebitacora('ERROR: Ocurrio un error en el postprocesado de '.$destipo.' del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(). ' La incidencia se ha registrado correctamente pero no se ha podido procesar la accion de notificacion programada' ,"Incidencias","save","ERROR");
                //dump($exception);
                return [
                    'title' => "Crear ".$destipo." en puesto ".$puesto->cod_puesto,
                    'error' => 'ERROR: Ocurrio un error creando '.$destipo.' del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(),
                    'url' => url($url_vuelta),
                    'id' => $inc->id_incidencia,
                    'result'=>'error',
                    'timestamp'=>Carbon::now(),
                ];
            }
            
            } catch (\Throwable $exception) {

            savebitacora('ERROR: Ocurrio un error creando '.$destipo.' del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage() ,"Incidencias","save","ERROR");
            return [
                'title' => "Crear ".$destipo." en puesto ".$puesto->cod_puesto,
                'error' => 'ERROR: Ocurrio un error creando '.$destipo.' del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(),
                //'url' => url('sections')
                'result'=>'error',
                'timestamp'=>Carbon::now(),
            ];
        } 
    }

    public function add_accion(Request $r){
        $data=[];
        $accion_postprocesado="A";
        $incidencia=incidencias::find($r->id_incidencia);
        $tipo=incidencias_tipos::find($incidencia->id_tipo_incidencia);
        $cambio_estado=null;
        $procedencia=$r->procedencia??'web';
        if($incidencia->id_puesto==0){
            $cosa="Solicitud";
        } else {
            $cosa="Incidencia";
        }
        try{    
            
            if(isset($r->adjuntos) and is_array($r->adjuntos)){
                $adjuntos=$r->adjuntos[0];
                $adjuntos=explode(",",$adjuntos);
                $indice=1;
                foreach($adjuntos as $key=>$value){
                    $var="img".$indice;
                    $$var=$value;
                    $indice++;
                }
            }

            $acciones=incidencias_acciones::where('id_incidencia',$r->id_incidencia);
            if($acciones){
                $cuenta=$acciones->count()+1;
            } else {
                $cuenta=1;
            }
           
            //Vamos a insertar
            $accion=new incidencias_acciones;
            $accion->id_incidencia=$incidencia->id_incidencia;
            $accion->num_accion=$cuenta;
            $accion->des_accion=$r->des_accion;
            $accion->fec_accion=Carbon::now();
            $accion->id_usuario=$r->id_usuario??Auth::user()->id;
            $accion->img_attach1=$img1??null;
            $accion->img_attach2=$img2??null;
            //A ver si la accion cierra la incidencia
            if(isset($r->id_estado)){
                $datos_estado=estados_incidencias::find($r->id_estado);
                if($r->id_estado!=$incidencia->id_estado){
                    $accion->id_estado=$r->id_estado;
                    $cambio_estado=$r->id_estado;
                    $accion_postprocesado="E";
                }
                if($datos_estado->mca_cierre=='S'){
                    $accion->mca_resuelve='S';
                    $incidencia->comentario_cierre=$r->des_accion;
                    $incidencia->fec_cierre=Carbon::now();
                    $incidencia->id_usuario_cierre=$r->id_usuario??Auth::user()->id;
                    $accion_postprocesado="F";
                    //Actualizamos el puesto para que deje ed estar en estado de "con incidencia"
                    $puesto=puestos::find($incidencia->id_puesto);
                    $puesto->mca_incidencia='N';
                    $puesto->id_estado=$incidencia->id_estado_vuelta_puesto??1;
                    $puesto->save();

                }
                $incidencia->fec_audit=Carbon::now();
                $incidencia->id_estado=$r->id_estado;
                $incidencia->save();
                
            }
            $accion->save();
            $this->post_procesado_incidencia($incidencia,$accion_postprocesado,$procedencia,$cambio_estado);
            savebitacora("Añadida accion para la ".$cosa." ".$r->id_incidencia,"Incidencias","add_accion","OK");
            return [
                'title' => "Añadir accion a la ".$cosa." ".$r->id_incidencia,
                'message' => "Añadida accion para la ".$cosa." ".$r->id_incidencia,
                //'url' => url($url_vuelta)
                'result'=>'ok',
                'timestamp'=>Carbon::now(),
            ];

        } catch (\Exception $e) {

            savebitacora('ERROR: Ocurrio un error añadiendo la accion '.$e->getMessage() ,"Incidencias","add_accion","ERROR");
            return [
                'title' => "Añadir accion",
                'error' => 'ERROR: Ocurrio un error añadiendo la accion '.$e->getMessage(),
                //'url' => url('sections')
                'result'=>'error',
                'timestamp'=>Carbon::now(),
            ];
        } 
    }

    public function subir_adjuntos(Request $r){
		try{
			if(isset($r->id_cliente)){
				$path = config('app.ruta_public').'/uploads/incidencias/'.$r->id_cliente;
				$file = $r->file('file')[0];

                    $original = $file->getClientOriginalName();
                    $extension = File::extension($file->getClientOriginalName());
                    $newfile = $r->id_cliente.'_'.Str::random(24).'.'.$extension;
                    Storage::disk(config('app.upload_disk'))->putFileAs($path,$file,$newfile);
				return \Response::json(array('success' => true, 'filename'=>$original,'newfilename'=>$newfile));
			}
		} catch(\Exception $e){
		response()->json([
			"error" => "Error subiendo adjunto ".mensaje_excepcion($e),
			"TS" => Carbon::now()->format('Y-m-d h:i:s')
			],400)->throwResponse();
		return \Response::json(array('error' => false));
		}

	}

    //PROCESADO DE INCIDENCIAS->ENVIARLA A TERCEROS SISTEMAS

    public function post_procesado_incidencia($inc,$momento,$procedencia,$estado=null){
        $tipo=incidencias_tipos::find($inc->id_tipo_incidencia);
        $usuario_abriente=users::find($inc->id_usuario_apertura);
        $puesto=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('id_puesto',$inc->id_puesto)
            ->first();
        $postprocesado=DB::table('incidencias_postprocesado')
            ->where('id_tipo_incidencia',$tipo->id_tipo_incidencia)
            ->where('val_momento',$momento)
            ->when($estado,function($q) use($estado){
                $q->where(function($q2) use($estado){
                    $q2->where('id_estado',$estado);
                    $q2->orwhere('id_estado',-1);
                });
            })
            ->get();
    
        
        foreach($postprocesado as $p){
            Log::debug('Postprocesado ['.$p->id_proceso.']'.$p->tip_metodo. ' prodecencia: '.$procedencia);
            if (($procedencia=='api' && $p->mca_api!='S') || ($procedencia=='salas' && $p->mca_salas!='S')){
                //Esto es para no mandarla al mismo sitio del que viene;
                Log::debug('saltamos ['.$p->id_proceso.']'.$p->tip_metodo. ' porque la procedencia de la solicitud es'.$procedencia);
                continue;
            }
            try{
                switch ($p->tip_metodo) {
                    case 'S':  //Mandar SMS
                        break;

                    case 'M':  //Mandar e-mail
                        $to_email = explode(";",$p->txt_destinos);
                        //Ahora vamos a ver si se ha marcado para que se envie al usuario abriente o a los afectados.
                        $abriente=DB::table('users')
                            ->where('id',$inc->id_usuario_apertura)
                            ->first();

                        $implicados=DB::table('users')
                            ->join('incidencias_acciones','users.id','incidencias_acciones.id_usuario')
                            ->where('id_incidencia',$inc->id_incidencia)
                            ->pluck('email')
                            ->toarray(); 

                        if($abriente->id_usuario_supervisor!=null){
                            $supervisor=DB::table('users')
                                ->where('id',$abriente->id_usuario_supervisor)
                                ->first()
                                ->email;
                        } else {
                            $supervisor=null;
                        }

                        

                        Log::info("Iniciando postprocesado MAIL de incidencia ".$inc->id_incidencia);
                        //Si se han marcado las casillas de enviar al abriente o a los afectados, vamos a ver quienes son
                        //y los añadimos al to_email
                        if($p->mca_abriente=='S'){
                            $to_email[]=$abriente->email;
                        }
                        if($p->mca_implicados=='S'){
                            foreach($implicados as $i){
                                $to_email[]=$i;
                            }
                        }
                        if($p->mca_responsable=='S' &&  $supervisor!=null){
                            $to_email[]=$supervisor->email;
                        }
                        //Por ultimo, quitamos los duplicados y limpiamos si hubiera algun nulo y  los pasamos a string
                        $to_email=array_unique($to_email);
                        $to_email=array_filter($to_email);
                        $to_email=implode(";",$to_email);
                        Log::debug('Destinatarios '.$to_email);

                        //Ahora adaptamos el subject en funncion de si es incidnecia o solicitud
                        if($inc->id_puesto==0){
                            $subject='Solicitud #'.$inc->id_incidencia.' de '.$tipo->des_tipo_incidencia;
                        } else {
                            $subject='Incidencia #'.$inc->id_incidencia.'en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta;
                        }

                        //Y le añadimos al subject indicacion de en que estado esta
                        switch($momento){
                            case 'C':
                                $subject.=' (Abierta)';
                                break;
                            case 'A':
                                $subject.=' (Nueva accion/estado)';
                                break;
                            case 'F':
                                $subject.=' (Finalizada)';
                                break;
                            case 'E':
                                $subject.=' (Cambio de estado)';
                                break;
                            case 'R':
                                $subject.=' (Reapertura)';
                                break;
                        }

                        Mail::send('emails.mail_incidencia'.$momento, ['inc'=>$inc,'tipo'=>$tipo], function($message) use ($tipo, $to_email, $inc, $puesto,$momento,$subject) {
                            if(config('app.env')=='local'|| config('app.env')=='qa'){//Para que en desarrollo solo me mande los mail a mi
                                Log::debug('modo mail debug '.$to_email);
                                $message->to('nomecansum@gmail.com')->subject($subject.' '.count(explode(';',$to_email)).' destinatarios');
                            } else {
                                Log::debug('modo mail pro '.$to_email);
                                $message->to(explode(';',$to_email), '')->subject($subject);
                            }
                            $message->from(config('mail.from.address'),config('mail.from.name'));
                            if($momento=='C'){
                                if($inc->img_attach1!==null && strlen($inc->img_attach1)>5){
                                    $adj1=Storage::disk(config('app.upload_disk'))->get('/uploads/incidencias/'.$puesto->id_cliente.'/'.$inc->img_attach1);
                                    $message->attachData($adj1,$inc->img_attach1);
                                }     
                                if($inc->img_attach2!==null && strlen($inc->img_attach2)>5){
                                    $adj2=Storage::disk(config('app.upload_disk'))->get('/uploads/incidencias/'.$puesto->id_cliente.'/'.$inc->img_attach2);
                                    $message->attachData($adj2,$inc->img_attach2);
                                }
                            } else if($momento=='A'){
                                $accion=incidencias_acciones::where('id_incidencia',$inc->id_incidencia)->orderBy('id_accion','desc')->first();
                                if($accion->img_attach1!==null && strlen($accion->img_attach1)>5){
                                    $adj1=Storage::disk(config('app.upload_disk'))->get('/uploads/incidencias/'.$puesto->id_cliente.'/'.$accion->img_attach1);
                                    $message->attachData($adj1,$accion->img_attach1);
                                }     
                                if($accion->img_attach2!==null && strlen($accion->img_attach2)>5){
                                    $adj2=Storage::disk(config('app.upload_disk'))->get('/uploads/incidencias/'.$puesto->id_cliente.'/'.$accion->img_attach2);
                                    $message->attachData($adj2,$accion->img_attach2);
                                }
                            }
                        });

                        break;
                    case 'P': //HTTP Post
                    case 'U': //HTTP Put
                        try{
                            $inc->mca_sincronizada='N';
                            Log::info("Iniciando postprocesado HTTP POST de incidencia ".$inc->id_incidencia);
                            
                            //Ahora sustituimos las variables por sus valores
                            $p->val_url=$this->reemplazar_parametros($p->val_url,$inc);
                            $p->param_url=$this->reemplazar_parametros($p->param_url,$inc);
                            $p->val_body=$this->reemplazar_parametros($p->val_body,$inc);
                            log::debug($p->val_body);
                            if(isset($p->param_url) && strlen($p->param_url)>0){
                                $p->val_url.='?'.$p->param_url;
                            }
                            Log::debug("URL: ".$p->val_url);
                            $metodo=$p->tip_metodo=='P'?'POST':'PUT';
                            $response=Http::withOptions(['verify' => false])
                                ->withHeaders(json_decode($p->val_header,true))
                                ->withbody($p->val_body,'application/json')
                                ->$metodo($p->val_url);
                            if($response->status()==200){
                                Log::info("Postprocesado HTTP POST de incidencia ".$inc->id_incidencia." OK");
                            } else {
                                Log::error("Postprocesado HTTP POST de incidencia ".$inc->id_incidencia." ERROR: ".$response->status());
                            }
                            Log::debug($response->body());
                            
                            if($p->val_respuesta!=null){
                                $this->procesar_respuesta($p->val_respuesta,$response->body(),$inc->id_incidencia,$puesto->id_puesto);
                            }
                        
                        } catch(\Throwable $e){
                            Log::error("Postprocesado HTTP POST de incidencia ".$inc->id_incidencia." "." ERROR: ".$e->getMessage());
                            dd($e);
                        }
                        $inc->mca_sincronizada='S';
                        break;

                    case 'G': //HTTP Get
                        try{
                            Log::info("Iniciando postprocesado HTTP GET de incidencia ".$inc->id_incidencia);
                            $p->val_url=$this->reemplazar_parametros($p->val_url,$inc);
                            $p->param_url=$this->reemplazar_parametros($p->param_url,$inc);
                            if(isset($p->param_url) && strlen($p->param_url)>0){
                                $p->val_url.='?'.$p->param_url;
                            }
                            Log::debug("URL: ".$p->val_url);
                            $response=Http::withOptions(['verify' => false])
                                ->withHeaders(json_decode($p->val_header,true))
                                ->get($p->val_url);
                           
                            if($response->status()==200){
                                Log::info("Postprocesado HTTP POST de incidencia ".$inc->id_incidencia." OK");
                            } else {
                                Log::error("Postprocesado HTTP POST de incidencia ".$inc->id_incidencia." ERROR: ".$response->status());
                            }
                            Log::debug($response->body());
                            if($p->val_respuesta!=null){
                                $this->procesar_respuesta($p->val_respuesta,$response->body(),$inc->id_incidencia,$puesto->id_puesto);
                            }

                        } catch(\Throwable $e){
                            Log::error("Postprocesado HTTP POST de incidencia ".$inc->id_incidencia." ".$response->status()." ERROR: ".$e->getMessage());
                            //dd($e);
                        }
                        $inc->mca_sincronizada='S';
                        break;

                    case 'W': //Web Push
                        Log::info("Iniciando postprocesado WEB Push de incidencia ".$inc->id_incidencia);
                        //Incidencia en puesto '.$puesto->cod_puesto.' '.$puesto->des_edificio.' - '.$puesto->des_planta
                        notificar_usuario( $usuario_abriente,null,null,$this->reemplazar_parametros($p->val_body,$inc)??'SIN TEXTO',metodos_notificacion_usuario($usuario_abriente->id,3),9,[],$inc->id_incidencia);
                        break;

                    case 'L': //Spotlinker
                        Log::info("Iniciando postprocesado SALAS de incidencia ".$inc->id_incidencia);
                        try{
                            switch ($momento) {
                                case 'C': //Creacion
                                case 'A':  //Accion/Modificacion
                                    //Averuiguar si el puesto tiene sala
                                    $sala=salas::where('id_puesto',$inc->id_puesto)->first();
                                    $estado=estados_incidencias::find($inc->id_estado);
                                    $tipo=incidencias_tipos::find($inc->id_tipo_incidencia);
                                    $notas_admin=$inc->txt_incidencia;
                                    $endpoint="add_or_set_incidencia_empresa";
                                    if($momento=='A'){
                                        $accion=incidencias_acciones::where('id_incidencia',$inc->id_incidencia)->orderBy('id_accion','desc')->first();
                                        $notas_admin.=$accion->txt_accion;
                                    }
                                    if($sala!=null && $sala->id_externo_salas!=null){
                                        $body=new \stdClass;
                                        $body->sala_id=$sala->id_externo_salas;
                                        $body->fecha=Carbon::parse($inc->fec_apertura)->toISOString();
                                        $body->tipo_incidencia_id=$tipo->id_tipo_salas;
                                        $body->estado=$estado->id_estado_salas;
                                        $body->descripcion_adicional=$inc->des_incidencia;
                                        $body->notas_admin=$notas_admin;
                                        $body->incidencia_id_puestos=$inc->id_incidencia;
                                        $body=json_encode($body);
                                        log::debug($body);
                                        $response=APIController::enviar_request_salas('post',$endpoint,"",$body,$inc->id_cliente);
                                        if(isset($response['status']) && $response['status']==200){
                                            $resp=json_decode($response['body']);
                                            $inc->id_incidencia_salas=$resp->incidencia_sala_id;
                                            Log::info("Respuesta OK de salas: ".$response['body']);
                                        } else {
                                            Log::error("Error en el request a spotlinker salas");
                                        }
                                    } else { //El puesto no tiene sala asociada
                                        Log::error("No hay sala asociada para el puesto o la sala no esta asociada a una sala de spotlinker salas".$inc->id_puesto);
                                    }
                                    
                                    break;
                                case 'F':  //Cierre
                                    # code...
                                    break;
                                case 'R':  //Reapertura
                                    # code...
                                    break;
                                default:
                                    # code...
                                    break;
                            }
    
                            $inc->mca_sincronizada='S';
                        } catch(\Throwable $e){
                            Log::error("Postprocesado SALAS de incidencia ".$inc->id_incidencia."  ERROR: ".$e->getMessage());
                            //dump($e);
                        }
                        
                        break;

                    default:
                        # code...
                        break;
                }
            } catch(\Throwable $e){
                Log::error("Postprocesado de incidencia ".$inc->id_incidencia." ERROR: ".$e->getMessage());
                //dump($e);
            }
        }
        $inc->save();

    }

    //FORMULARIO DE CIERRE DE INCIDENCIA
    public function form_cierre($id){
        validar_acceso_tabla($id,'incidencias');
        $incidencia=incidencias::find($id);
        $causas_cierre=DB::table('causas_cierre')
            ->where('id_cliente',Auth::user()->id_cliente)
            ->where(function($q) use($incidencia){
                if($incidencia->id_puesto!=0){
                    $q->wherein('causas_cierre.mca_aplica',['I','A']);
                } else {
                    $q->wherein('causas_cierre.mca_aplica',['S','A']);
                }
            })
            ->get();
        return view('incidencias.fill-form-cerrar',compact('id','causas_cierre'));
    }

    //FORMULARIO DE AÑADIR NUEVA ACCIOM
    public function form_accion($id){
        validar_acceso_tabla($id,'incidencias');
        $incidencia=incidencias::find($id);
        $estados = DB::table('estados_incidencias')
            ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
            ->where(function($q) use($incidencia){
                $q->where('estados_incidencias.id_cliente',$incidencia->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('estados_incidencias.mca_fijo','S');
                }
            })
            ->where(function($q) use($incidencia){
                if($incidencia->id_puesto!=0){
                    $q->wherein('estados_incidencias.mca_aplica',['I','A']);
                } else {
                    $q->wherein('estados_incidencias.mca_aplica',['S','A']);
                }
            })
            ->orderby('des_estado')
        ->get();

        return view('incidencias.fill-form-accion',compact('id','estados','incidencia'));
    }

    public function detalle_incidencia(Request $r,$id){
        validar_acceso_tabla($id,"incidencias");
        $incidencia=DB::table('incidencias')
            ->select('incidencias.*','edificios.des_edificio','plantas.des_planta','users.name','users.img_usuario','puestos.cod_puesto','puestos.des_puesto','incidencias_tipos.*','estados_incidencias.des_estado as estado_incidencia')
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->wherein('incidencias.id_cliente',clientes());
            })
            ->where('incidencias.id_incidencia',$id)
            ->first();

        $acciones=DB::table('incidencias_acciones')
            ->join('users','incidencias_acciones.id_usuario','users.id')
            ->where('id_incidencia',$id)
            ->get();
        if($incidencia==null){
            $e=new \stdClass;
            $e->mensaje="La incidencia que intenta ver no existe";
            $e->titulo="Incidencia no encontrada";
            $e->code=404;
            $e->icono="fas fa-exclamation-triangle";
            $e->grande="Error";
            $e->mensaje_largo="";
            return view('errors.generic_include',compact('e'));
            //return redirect()->route('incidencias.index')->with('error_message', 'Incidencia no encontrada');
        }
        if(strpos($_SERVER['REQUEST_URI'],'/show/')){
            return view('incidencias.show',compact('incidencia','acciones'));
        } else {
            return view('incidencias.fill-detalle-incidencia',compact('incidencia','acciones'));
        }
        
    }


    public function get_detalle_scan($id){
        if(strlen($id)>10){  //Es un token
            $puesto=DB::table('puestos')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->where('token',$id)
                ->first();
        } else { //Es un id
            $puesto=DB::table('puestos')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->where('id_puesto',$id)
                ->first();
        }
        $incidencia=DB::table('incidencias')
            ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->where('incidencias.id_puesto',$puesto->id_puesto)
            ->wherenull('incidencias.fec_cierre')
            ->first();
        return view('incidencias.get_detalle_scan',compact('incidencia','puesto'));
    }

    public function delete($id){
        try {
            validar_acceso_tabla($id,"incidencias");
            $incidencia = incidencias::findOrFail($id);
            $puesto=puestos::find($incidencia->id_puesto);
           
            $incidencia->delete();
            $puesto->mca_incidencia='N';
            $puesto->id_estado=$incidencia->id_estado_vuelta_puesto;
            $puesto->save();
            if($incidencia->id_puesto==0){
                $destino="solicitud";
                $cosa="Solicitud";
            } else {
                $destino="incidencia";
                $cosa="Incidencia";
            }
            savebitacora($cosa.' ['.$incidencia->id_incidencia.'] '.$incidencia->des_incidencia.' borrada',"Incidencias","delete","OK");
            return redirect()->route($destino.'.index')->with('success_message', $cosa.' ['.$id.'] '.$incidencia->des_incidencia.' borrada.');
        } catch (\Throwable $exception) {
            savebitacora('ERROR: Ocurrio un error borrando la '.$cosa.' ['.$incidencia->id_incidencia.'] '.$exception->getMessage() ,"Incidencias","delete","ERROR");
            return back()->withInput()
                ->withErrors(['unexpected_error' => 'Ocurrio un error al borrar la '.$cosa.' ['.$id.'] '.mensaje_excepcion($exception)]);
        }
    }

    public function cerrar(Request $r){
        try {
            $procedencia=$r->procedencia??'web';
            validar_acceso_tabla($r->id_incidencia,'incidencias');
            $inc=incidencias::find($r->id_incidencia);
            $inc->id_causa_cierre=$r->id_causa_cierre;
            $inc->comentario_cierre=$r->comentario_cierre;
            $inc->fec_cierre=Carbon::now();
            $inc->id_usuario_cierre=Auth::user()->id;
            $inc->save();
            $puesto=puestos::find($inc->id_puesto);
            $puesto->mca_incidencia='N';
            $puesto->id_estado=$inc->id_estado_vuelta_puesto??1;
            $puesto->save();
            $this->post_procesado_incidencia($inc,'F',$procedencia);
            savebitacora('Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' cerrada',"Incidencias","cerrar","OK");

            return [
                'title' => "Cerrar incidencia",
                'message' => 'Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' cerrada',
                'id'=> $inc->id_incidencia
                //'url' => url('/incidencias')
            ];
        } catch (\Throwable $exception) {
            savebitacora('ERROR: Ocurrio un error cerrando la incidencia ['.$r->id_incidencia.'] '.$exception->getMessage() ,"Incidencias","cerrar","ERROR");
            return [
                'title' => "Cerrar incidencia",
                'error' => 'ERROR: Ocurrio un error cerrando la incidencia ['.$r->id_incidencia.'] '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function reabrir(Request $r){
        try {
            $procedencia=$r->procedencia??'web';
            validar_acceso_tabla($r->id_incidencia,'incidencias');
            $inc=incidencias::find($r->id_incidencia);
            $puesto=puestos::find($inc->id_puesto);
            $puesto->mca_incidencia='S';
            $puesto->save();

            $inc=incidencias::find($r->id_incidencia);
            $inc->id_causa_cierre=null;
            $inc->comentario_cierre=null;
            $inc->fec_cierre=null;
            $inc->id_usuario_cierre=null;
            $inc->id_estado_vuelta_puesto=$puesto->id_estado;
            $inc->save();
            $this->post_procesado_incidencia($inc,'R',$procedencia);
            savebitacora('Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' reabierta',"Incidencias","reabrir","OK");
            //Ponemos el estado del puesto a operativo
            
            return [
                'title' => "Reabrir incidencia",
                'message' => 'Incidencia ['.$inc->id_incidencia.'] '.$inc->des_incidencia.' reabierta',
                'id'=> $inc->id_incidencia
                //'url' => url('/incidencias')
            ];
        } catch (\Throwable $exception) {
            savebitacora('ERROR: Ocurrio un error reabriendo la incidencia ['.$r->id_incidencia.'] '.$exception->getMessage() ,"Incidencias","reabrir","ERROR");
            return [
                'title' => "Reabrir incidencia",
                'error' => 'ERROR: Ocurrio un error reabriendo la incidencia ['.$r->id_incidencia.'] '.$exception->getMessage(),
                'url' => url('incidencias')
            ];
        }
    }


    // GESTION DE TIPOS DE INCIDENCIA
    public function index_tipos(){
        $tipos = DB::table('incidencias_tipos')
        ->join('clientes','clientes.id_cliente','incidencias_tipos.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('incidencias_tipos.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('incidencias_tipos.mca_fijo','S');
                }
            } else {
                $q->where('incidencias_tipos.id_cliente',session('CL')['id_cliente']);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('incidencias_tipos.mca_fijo','S');
                }
            }
        })
        ->orderby('incidencias_tipos.des_tipo_incidencia')
        ->get();
        
        return view('incidencias.tipos.index', compact('tipos'));
    }

    public function tipos_edit($id=0){
        if($id==0){
            $tipo=new incidencias_tipos();
        } else {
            $tipo = incidencias_tipos::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        $estados = DB::table('estados_incidencias')
            ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
            ->where(function($q){
                $q->where('estados_incidencias.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('estados_incidencias.mca_fijo','S');
                }
            })
            ->get();
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
        $tipos_incidencia = DB::table('incidencias_tipos')
            ->join('clientes','clientes.id_cliente','incidencias_tipos.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('incidencias_tipos.id_cliente',Auth::user()->id_cliente);
                    if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                        $q->orwhere('incidencias_tipos.mca_fijo','S');
                    }
                } else {
                    $q->where('incidencias_tipos.id_cliente',session('CL')['id_cliente']);
                    if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                        $q->orwhere('incidencias_tipos.mca_fijo','S');
                    }
                }
            })
            ->orderby('des_tipo_incidencia')
            ->get();
        return view('incidencias.tipos.edit', compact('tipo','Clientes','id','estados','tipos','tipos_incidencia'));
    }

    public function tipos_save(Request $r){
        try {
            if($r->id==0){
                incidencias_tipos::create($r->all());
            } else {
                $tipo=incidencias_tipos::find($r->id);
                $tipo->update($r->all());
                $tipo->list_tipo_puesto=isset($r->tipos_puesto)?implode(",",$r->tipos_puesto):null;
                $tipo->save();
                
            }
            savebitacora('Tipo de incidencia creado '.$r->des_tipo_incidencia,"Incidencias","tipos_save","OK");
            return [
                'title' => "Tipos de incidencia",
                'message' => 'Tipo de incidencia '.$r->des_tipo_incidencia. ' actualizado con exito',
                'url' => url('/incidencias/tipos')
            ];
        } catch (\Throwable $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error creando tipo de incidencia '.$r->des_tipo_incidencia.' '.$exception->getMessage() ,"Incidencias","tipos_save","ERROR");
            return [
                'title' => "Tipos de incidencia",
                'error' => 'ERROR: Ocurrio un error actualizando el tipo de incidencia '.$r->des_tipo_incidencia.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function tipos_delete($id=0){
        try {
            $tipo = incidencias_tipos::findorfail($id);

            $tipo->delete();
            savebitacora('Tipo de incidencia borrado '.$tipo->des_tipo_incidencia,"Incidencias","tipos_delete","OK");
            flash('Tipo de incidencia '.$tipo->des_tipo_incidencia.' borrado')->success();
            return back()->withInput();
        } catch (\Throwable $exception) {
            flash('ERROR: Ocurrio un error borrando Tipo de incidencia '.$tipo->des_tipo_incidencia.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
    }

    public function edit_postprocesado($id,$momento){

        $tipo=incidencias_tipos::findorfail($id);
        $data=DB::table('incidencias_postprocesado')->where('id_tipo_incidencia',$id)->where('val_momento',$momento)->get();
        $estados = DB::table('estados_incidencias')
            ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
            ->where(function($q){
                $q->where('estados_incidencias.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('estados_incidencias.mca_fijo','S');
                }
            })
            ->wherein('estados_incidencias.mca_aplica',[$tipo->mca_aplica,'A'])
            ->orderby('des_estado')
        ->get();

        return view('incidencias.tipos.fill_post_tipo', compact('data','id','momento','estados'));
    }

    public function add_postprocesado($id,$momento){
        
        $accion=new incidencias_postprocesado;
        $accion->id_tipo_incidencia=$id;
        $accion->tip_metodo='N';
        $accion->val_momento=$momento;
        $accion->save();
        savebitacora('Añadida accion de postprocesado para el tipo de incidencia ['.$id.'] momento '.$momento,"Incidencias","add_postprocesado","OK");
        return;
    }

    public function fila_postprocesado($id,$metodo,$momento){
        $tipo=incidencias_postprocesado::findorfail($id);
        $tipo->tip_metodo=$metodo;
        $tipo->val_momento=$momento;
        $tipo->save();
        $estados = DB::table('estados_incidencias')
            ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
            ->where(function($q){
                $q->where('estados_incidencias.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('estados_incidencias.mca_fijo','S');
                }
            })
            ->wherein('estados_incidencias.mca_aplica',[$tipo->mca_aplica,'A'])
            ->orderby('des_estado')
        ->get();
        return view('incidencias.tipos.fila_procesado_tipo', compact('tipo','id','momento','estados'));
    }

    public function del_fila_postprocesado($id){
        $tipo=incidencias_postprocesado::findorfail($id);
        $tipo->delete();
        savebitacora('Borrada accion de postprocesado para el tipo de incidencia ['.$tipo->id_tipo_incidencia.'] momento '.$tipo->val_momento,"Incidencias","add_postprocesado","OK");
        return [
            'id' => $id,
        ];
    }

    public function save_postprocesado(Request $r){

        if(isset($r->txt_destinos) && is_array($r->txt_destinos)){
            $destinos=implode(",",$r->txt_destinos);
        } else {
            $destinos=$r->txt_destinos;
        }

        $tipo=incidencias_postprocesado::findorfail($r->id);
        $tipo->tip_metodo=$r->tip_metodo;
        $tipo->txt_destinos=$destinos??null;
        $tipo->val_url=$r->val_url??null;
        $tipo->param_url=$r->param_url??null;
        $tipo->val_body=$r->val_body??null;
        $tipo->val_header=$r->val_header??null;
        $tipo->val_respuesta=$r->val_respuesta??null;
        $tipo->mca_api=isset($r->mca_api)?'S':'N';
        $tipo->mca_web=isset($r->mca_web)?'S':'N';
        $tipo->mca_salas=isset($r->mca_salas)?'S':'N';
        $tipo->mca_scan=isset($r->mca_scan)?'S':'N';
        $tipo->mca_implicados=isset($r->mca_implicados)?'S':'N';
        $tipo->mca_abriente=isset($r->mca_abriente)?'S':'N';
        $tipo->mca_responsable=isset($r->mca_responsable)?'S':'N';
        $tipo->id_estado=$r->id_estado??null;
        $tipo->save();
        savebitacora('Modificada accion de postprocesado para el tipo de incidencia ['.$tipo->id_tipo_incidencia.'] momento '.$tipo->val_momento,"Incidencias","add_postprocesado","OK");
        return [
            'result'=>'OK',
            'mensaje'=>'Accion guardada con exito',
            'id' => $r->id,
        ];
    }

    public function copiar_postprocesado(Request $r){
        if($r->data_importar==='T'){
            incidencias_postprocesado::where('id_tipo_incidencia',$r->tipo_destino)->delete();
            $datos=incidencias_postprocesado::where('id_tipo_incidencia',$r->tipo_origen)->get();
            $detalles=' TODO el postprocesado';
        } else {
            incidencias_postprocesado::where('id_tipo_incidencia',$r->tipo_destino)->where('val_momento',$r->momento)->delete();
            $datos=incidencias_postprocesado::where('id_tipo_incidencia',$r->tipo_origen)->where('val_momento',$r->momento)->get();
            savebitacora('Copiada informacion de postprocesado para la incidencia ['.$tipo->id_tipo_incidencia.'] momento '.$tipo->val_momento,"Incidencias","add_postprocesado","OK");
            $detalles=' momento '.$r->momento;
        }
        foreach($datos as $dato){
            $nuevo=$dato->replicate();
            $nuevo->id_tipo_incidencia=$r->tipo_destino;
            $nuevo->save();
        }
        savebitacora('Copiada informacion de postprocesado para el tipo de incidencia ['.$r->tipo_destino.'] desde el tipo ['.$r->tipo_origen.']: '.$detalles.' '.count($datos).' acciones copiadas',"Incidencias","copiar_postprocesado","OK");
        return [
            'result'=>'OK',
            'message'=>'Postprocesado copiado '.count($datos).' acciones copiadas',
            'id' => $r->tipo_destino,
        ];
    }


    // GESTION DE CAUSAS DE CIERRE DE INCIDENCIA
    public function index_causas(){
        $causas = DB::table('causas_cierre')
        ->join('clientes','clientes.id_cliente','causas_cierre.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('causas_cierre.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('causas_cierre.mca_fija','S');
                }
            } else {
                $q->where('causas_cierre.id_cliente',session('CL')['id_cliente']);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('causas_cierre.mca_fija','S');
                }
            }
        })
        ->get();
        return view('incidencias.causas.index', compact('causas'));
    }


    public function causas_edit($id=0){
        if($id==0){
            $causa=new causas_cierre();
        } else {
            $causa = causas_cierre::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        return view('incidencias.causas.edit', compact('causa','Clientes','id'));
    }

    public function causas_save(Request $r){
        try {
            if($r->id==0){
                causas_cierre::create($r->all());
            } else {
                $causa=causas_cierre::find($r->id);
                $causa->update($r->all());
            }
            savebitacora('Causa de cierre actualizada '.$r->des_causa,"Incidencias","causas_save","OK");
            return [
                'title' => "Causas de cierre",
                'message' => 'Causa de cierre '.$r->des_causa. ' actualizada',
                'url' => url('/incidencias/causas')
            ];
        } catch (\Throwable $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error actualizando causa de cierre '.$r->des_causa.' '.$exception->getMessage() ,"Incidencias","causas_save","ERROR");
            return [
                'title' => "Causas de cierre",
                'error' => 'ERROR: Ocurrio un error actualizando causa de cierre '.$r->des_causa.' '.$exception->getMessage(),
                //'url' => url('causas')
            ];

        }
    }

    public function causas_delete($id=0){
        try {
            $causa = causas_cierre::findorfail($id);

            $causa->delete();
            savebitacora('Causa de cierre borrada '.$causa->des_causa,"Incidencias","causas_save","OK");
            flash('Causa de cierre '.$causa->des_causa.' borrada')->success();
            return back()->withInput();
        } catch (\Throwable $exception) {
            flash('ERROR: Ocurrio un error borrando causa de cierre '.$causa->des_causa.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
    }

    // GESTION DE ESTADOS DE INCIDENCIA
    public function index_estados(){
        $estados = DB::table('estados_incidencias')
        ->join('clientes','clientes.id_cliente','estados_incidencias.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('estados_incidencias.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('estados_incidencias.mca_fijo','S');
                }
            } else {
                $q->where('estados_incidencias.id_cliente',session('CL')['id_cliente']);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('estados_incidencias.mca_fijo','S');
                }
            }
        })
        ->get();
        return view('incidencias.estados.index', compact('estados'));
    }

    public function estados_edit($id=0){
        if($id==0){
            $estado=new estados_incidencias();
        } else {
            $estado = estados_incidencias::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        return view('incidencias.estados.edit', compact('estado','Clientes','id'));
    }

    public function estados_save(Request $r){
        try {
            if($r->id==0){
                estados_incidencias::create($r->all());
            } else {
                $estado=estados_incidencias::find($r->id);
                $estado->update($r->all());
            }
            savebitacora('Estado de incidencia actualizada '.$r->des_estado,"Incidencias","estados_save","OK");
            return [
                'title' => "Estados de incidencia",
                'message' => 'Estado de incidencia '.$r->des_estado. ' actualizado',
                'url' => url('/incidencias/estados')
            ];
        } catch (\Throwable $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error actualizando estado de incidencia '.$r->des_estado.' '.$exception->getMessage() ,"Incidencias","estados_save","ERROR");
            return [
                'title' => "Estados de incidencia",
                'error' => 'ERROR: Ocurrio un error actualizando estado de incidencia '.$r->des_estado.' '.$exception->getMessage(),
                //'url' => url('causas')
            ];

        }
    }

    public function estados_delete($id=0){
        try {
            $estado = estados_incidencias::findorfail($id);

            $estado->delete();
            savebitacora('Estado de incidencia borrado '.$estado->des_estado,"Incidencias","causas_save","OK");
            flash('Estado de incidencia '.$estado->des_estado.' borrada')->success();
            return back()->withInput();
        } catch (\Throwable $exception) {
            flash('ERROR: Ocurrio un error borrando causa de cierre '.$estado->des_estado.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
    }


    //Funnciones para los clientes sin  login
    public function incidencia_cliente($puesto,$pin){
        $puesto=DB::table('puestos')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$puesto)
            ->first();
        $mireserva=DB::table('reservas')
            ->where('id_puesto',$puesto->id_puesto)
            ->where('fec_reserva','<=',Carbon::now()->format('Y-m-d'))
            ->where('fec_fin_reserva','>=',Carbon::now()->format('Y-m-d'))
            ->where('pin',strtoupper($pin))
            ->first();
        if(!isset($mireserva)){
            savebitacora('Puesto ['.$puesto->id_puesto.'] Error al verificar PIN. '.url()->full(),"Scan","getpuesto","ERROR");
            return back()->withInput()
            ->withErrors(['error' => 'PIN Incorrecto.']);
        }
        $idcliente=$puesto->id_cliente;
        $config=DB::table('config_clientes')->where('id_cliente',$idcliente)->first();
        $tipos=DB::table('incidencias_tipos')
            ->join('clientes','incidencias_tipos.id_cliente','clientes.id_cliente')
            ->where(function($q) use($puesto,$idcliente){
                $q->where('incidencias_tipos.id_cliente',$idcliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('incidencias_tipos.mca_fijo','S');
                }
                
                })
            ->where(function($q) use($puesto){
                if($puesto->id_puesto!=0){
                    $q->orwhereraw('FIND_IN_SET('.$puesto->id_tipo_puesto.', list_tipo_puesto) <> 0');
                }
            })
            ->where(function($q) use($puesto){
                $q->wherein('incidencias_tipos.mca_aplica',['I','A']);
            })
            ->orderby('mca_fijo')
            ->orderby('incidencias_tipos.des_tipo_incidencia')
            ->get();
        return view('incidencias.fill_frm_incidencia_cliente',compact('puesto','tipos','config','mireserva'));

    }

    public function solicitud_cliente($puesto,$pin){
        $puesto=DB::table('puestos')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$puesto)
            ->first();
        $mireserva=DB::table('reservas')
            ->where('id_puesto',$puesto->id_puesto)
            ->where('fec_reserva','<=',Carbon::now()->format('Y-m-d'))
            ->where('fec_fin_reserva','>=',Carbon::now()->format('Y-m-d'))
            ->where('pin',strtoupper($pin))
            ->first();
        if(!isset($mireserva)){
            savebitacora('Puesto ['.$puesto->id_puesto.'] Error al verificar PIN. '.url()->full(),"Scan","getpuesto","ERROR");
            return back()->withInput()
            ->withErrors(['error' => 'PIN Incorrecto.']);
        }
        $idcliente=$puesto->id_cliente;
        $config=DB::table('config_clientes')->where('id_cliente',$idcliente)->first();
        $tipos=DB::table('incidencias_tipos')
            ->join('clientes','incidencias_tipos.id_cliente','clientes.id_cliente')
            ->where(function($q) use($puesto,$idcliente){
                $q->where('incidencias_tipos.id_cliente',$idcliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('incidencias_tipos.mca_fijo','S');
                }
                
                })
            ->where(function($q) use($puesto){
                if($puesto->id_puesto!=0){
                    $q->orwhereraw('FIND_IN_SET('.$puesto->id_tipo_puesto.', list_tipo_puesto) <> 0');
                }
            })
            ->where(function($q) use($puesto){
                $q->wherein('incidencias_tipos.mca_aplica',['S','A']);
            })
            ->orderby('mca_fijo')
            ->orderby('incidencias_tipos.des_tipo_incidencia')
            ->get();
        return view('incidencias.fill_frm_solicitud_cliente',compact('puesto','tipos','config','mireserva'));

    }

    public function save_clientes(Request $r){
        $data=$this->getDataincidencia($r);
        $puesto=puestos::find($r->id_puesto);
        $idcliente=$puesto->id_cliente;
        $procedencia=$data['procedencia']??'web';
        if(isset($r->referer) && $r->referer=='scan'){
            $procedencia=$r->referer;
        }
        $tipo=incidencias_tipos::find($r->id_tipo_incidencia);
        try{     
            if(isset($r->adjuntos) and is_array($r->adjuntos)){
                $adjuntos=$r->adjuntos[0];
                $adjuntos=explode(",",$adjuntos);
                $indice=1;
                foreach($adjuntos as $key=>$value){
                    $var="img".$indice;
                    $$var=$value;
                    $indice++;
                }
            }

            $inc=new incidencias;
            $inc->des_incidencia=$data['des_incidencia']??null;
            $inc->txt_incidencia=$data['txt_incidencia']??null;
            $inc->id_cliente=$idcliente;
            $inc->id_usuario_apertura=null;
            $inc->fec_apertura=Carbon::now();
            $inc->id_tipo_incidencia=$r->id_tipo_incidencia;
            $inc->id_puesto=$puesto->id_puesto;
            $inc->img_attach1=$img1??null;
            $inc->img_attach2=$img2??null;
            $inc->id_estado=$data['id_estado']??$tipo->id_estado_inicial;
            $inc->id_estado_vuelta_puesto=$puesto->id_estado;
            $inc->val_procedencia=$procedencia;
            $inc->id_incidencia_salas=$r->id_incidencia_salas??null;
            $inc->id_incidencia_externo=$r->id_incidencia_externo??null;
            $inc->url_detalle_incidencia=$data['url_detalle_incidencia']??null;
            $inc->origen=$data['origen']??null;
            $inc->pin=$data['pin']??null;
            $inc->save();


            try{
                $this->post_procesado_incidencia($inc,'C',$procedencia);
                savebitacora($r->tipo.' de tipo '.$tipo->des_tipo_incidencia. ' '.$r->des_incidencia.' creada por cliente',"Incidencias","save","OK");
                return [
                    'title' => "Crear ".$r->tipo." en espacio ".$puesto->cod_puesto,
                    'message' => $r->tipo." de tipo ".$tipo->des_tipo_incidencia.' creada. Muchas gracias',
                    'id' => $inc->id_incidencia,
                    'result'=>'ok',
                    'url'=> null,
                    'timestamp'=>Carbon::now(),
                ];
            } catch(\Exception $exception){
                savebitacora('ERROR: Ocurrio un error en el postprocesado de '.$r->tipo.' del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(). ' La incidencia se ha registrado correctamente pero no se ha podido procesar la accion de notificacion programada' ,"Incidencias","save","ERROR");
                return [
                    'title' => "Crear ".$r->tipo." en espacio ".$puesto->cod_puesto,
                    'error' => 'ERROR: Ocurrio un error creando '.$r->tipo.' del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(),
                    'id' => $inc->id_incidencia,
                    'result'=>'error',
                    'url'=> null,
                    'timestamp'=>Carbon::now(),
                ];
            }
            
            } catch (\Throwable $exception) {

            savebitacora('ERROR: Ocurrio un error creando '.$r->tipo.' del tipo '.$tipo->des_tipo_incidencia.' '.$exception->getMessage() ,"Incidencias","save","ERROR");
            return [
                'title' => "Crear ".$r->tipo." en espacio ".$puesto->cod_puesto,
                'error' => 'ERROR: Ocurrio un error creando '.$r->tipo.' del tipo'.$tipo->des_tipo_incidencia.' '.$exception->getMessage(),
                'result'=>'error',
                'timestamp'=>Carbon::now(),
            ];
        } 
    }

    public function listado_clientes($puesto,$pin){

        $puesto=DB::table('puestos')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$puesto)
            ->first();
        $mireserva=DB::table('reservas')
            ->where('id_puesto',$puesto->id_puesto)
            ->where('fec_reserva','<=',Carbon::now()->format('Y-m-d'))
            ->where('fec_fin_reserva','>=',Carbon::now()->format('Y-m-d'))
            ->where('pin',strtoupper($pin))
            ->first();
        if(!isset($mireserva)){
            savebitacora('Puesto ['.$puesto->id_puesto.'] Error al verificar PIN. '.url()->full(),"Scan","getpuesto","ERROR");
            return back()->withInput()
            ->withErrors(['error' => 'PIN Incorrecto.']);
        }
        $f1=$mireserva->fec_reserva;
        $f2=$mireserva->fec_fin_reserva;
        $incidencias=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','users.name')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->whereBetween('fec_apertura',[$f1,$f2])
            ->where('incidencias.id_puesto',$puesto->id_puesto)
            ->where('incidencias.pin',$pin)
            ->wherenull('incidencias.fec_cierre')
            ->orderby('fec_apertura','desc')
            ->get();
        return view('incidencias.list_incidencias_cliente',compact('incidencias','f1','f2','mireserva','puesto'));
    }
    
    public function detalle_clientes($puesto,$pin,$id){

        $puesto=DB::table('puestos')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$puesto)
            ->first();
        $mireserva=DB::table('reservas')
            ->where('id_puesto',$puesto->id_puesto)
            ->where('fec_reserva','<=',Carbon::now()->format('Y-m-d'))
            ->where('fec_fin_reserva','>=',Carbon::now()->format('Y-m-d'))
            ->where('pin',strtoupper($pin))
            ->first();
        if(!isset($mireserva)){
            savebitacora('Puesto ['.$puesto->id_puesto.'] Error al verificar PIN. '.url()->full(),"Scan","getpuesto","ERROR");
            return back()->withInput()
            ->withErrors(['error' => 'PIN Incorrecto.']);
        }

        $incidencia=DB::table('incidencias')
            ->select('incidencias.*','edificios.des_edificio','plantas.des_planta','users.name','users.img_usuario','puestos.cod_puesto','puestos.des_puesto','incidencias_tipos.*','estados_incidencias.des_estado as estado_incidencia')
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('incidencias.id_incidencia',$id)
            ->where('incidencias.id_puesto',$puesto->id_puesto)
            ->first();

        $acciones=DB::table('incidencias_acciones')
            ->join('users','incidencias_acciones.id_usuario','users.id')
            ->where('id_incidencia',$id)
            ->get();
        if($incidencia==null){
            $e=new \stdClass;
            $e->mensaje="La incidencia que intenta ver no existe";
            $e->titulo="Incidencia no encontrada";
            $e->code=404;
            $e->icono="fas fa-exclamation-triangle";
            $e->grande="Error";
            $e->mensaje_largo="";
            return view('errors.generic_include',compact('e'));
            //return redirect()->route('incidencias.index')->with('error_message', 'Incidencia no encontrada');
        }
        return view('incidencias.fill-detalle-incidencia_cliente',compact('incidencia','acciones'));

    }


}