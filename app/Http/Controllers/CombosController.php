<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;

class CombosController extends Controller
{
    ////////////MANEJO DE LOS COMBOS DE FILTRO/////////////
    private function filtro_clientes($r){
        if (!isset($r->cliente)){
            if (fullaccess()){
                $r->cliente=DB::table('clientes')->where('id_cliente',session('CL')['id_cliente'])->pluck('id_cliente')->toArray();
            } else {
                $r->cliente=DB::table('clientes')
                    ->wherein('id_cliente',clientes())
                    ->pluck('id_cliente');
            }
        } else{
            if(!fullaccess()){
                $r->cliente=DB::table('clientes')
                ->wherein('id_cliente',clientes())
                ->wherein('id_cliente',$r->cliente)
                ->pluck('id_cliente');
            }
        }
        return $r;
    }
    
    public function loadedificios(Request $r){
        $r=$this->filtro_clientes($r);
        $supervisores_perfil=[0];
        $supervisores_usuario=[0];

    
        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->get()->pluck('id_perfil')->unique();

        $supervisores_usuario=DB::table('permisos_usuarios')->where('id_seccion',$permiso)->get()->pluck('id_usuario')->unique();

        return
        [
            "edificios" => DB::table('edificios')
                ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio')
                ->join('clientes','clientes.id_cliente','edificios.id_cliente')
                ->wherein('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('des_edificio')
                ->get(),

            "plantas" => DB::table('plantas')
                    ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta','plantas.num_orden')
                    ->join('clientes','clientes.id_cliente','plantas.id_cliente')
                    ->join('edificios','edificios.id_edificio','plantas.id_edificio')
                    ->whereIn('clientes.id_cliente',$r->cliente)
                    ->orderby('clientes.nom_cliente')
                    ->orderby('des_edificio')
                    ->orderby('plantas.num_orden')
                    ->get(),

            "puestos" => DB::table('puestos')
                ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta','puestos.id_puesto','puestos.cod_puesto')
                ->join('clientes','clientes.id_cliente','puestos.id_cliente')
                ->join('edificios','edificios.id_edificio','puestos.id_edificio')
                ->join('plantas','plantas.id_planta','puestos.id_planta')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('cod_puesto')
                ->where(function($q){
                    if (isSupervisor(Auth::user()->id)) {
                        $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                        $q->wherein('puestos.id_puesto',$puestos_usuario);
                    }
                })
                ->get(),

            "tags" => DB::table('tags')
                ->select('clientes.id_cliente','clientes.nom_cliente','tags.id_tag','tags.nom_tag')
                ->join('clientes','clientes.id_cliente','tags.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('nom_tag')
                ->get(),

            "users" => DB::table('users')
                ->select('clientes.id_cliente','clientes.nom_cliente','users.id','users.name')
                ->join('clientes','clientes.id_cliente','users.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('name')
                ->get(),

            "tipos" => DB::table('puestos_tipos')
                ->select('clientes.id_cliente','clientes.nom_cliente','puestos_tipos.id_tipo_puesto as id_tipo','puestos_tipos.des_tipo_puesto as des_tipo')
                ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('des_tipo_puesto')
                ->get(),

            "departamentos" => DB::table('departamentos')
                ->select('clientes.id_cliente','clientes.nom_cliente','departamentos.cod_departamento','departamentos.nom_departamento')
                ->join('clientes','clientes.id_cliente','departamentos.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('nom_departamento')
                ->get(),
            
            "perfiles" => DB::table('niveles_acceso')
                ->select('clientes.id_cliente','clientes.nom_cliente','niveles_acceso.cod_nivel as id_perfil','niveles_acceso.des_nivel_acceso as des_perfil')
                ->join('clientes','clientes.id_cliente','niveles_acceso.id_cliente')
                ->where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)
                ->where(function($q) use($r){
                    $q->whereIn('clientes.id_cliente',$r->cliente);
                    $q->orwhere('mca_fijo','S');
                })
                ->orderby('clientes.nom_cliente')
                ->orderby('des_nivel_acceso')
                ->get(),

            "supervisores" => DB::table('users')
                ->select('clientes.id_cliente','clientes.nom_cliente','users.id','users.name')
                ->join('clientes','clientes.id_cliente','users.id_cliente')
                ->where(function ($q) use($supervisores_perfil,$supervisores_usuario){
                    $q->wherein('cod_nivel',$supervisores_perfil);
                    $q->orwherein('id',$supervisores_usuario);
                })
                ->where(function($q){
                    $q->wherein('users.id_cliente',clientes());
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                })
                ->orderby('clientes.nom_cliente')
                ->orderby('name')
                ->get(),

            "turnos" => DB::table('turnos')
                ->select('clientes.id_cliente','clientes.nom_cliente','turnos.id_turno','turnos.des_turno')
                ->join('clientes','clientes.id_cliente','turnos.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('des_turno')
                ->get(),
            
            "colectivos" => DB::table('colectivos')
                ->select('clientes.id_cliente','clientes.nom_cliente','colectivos.cod_colectivo','colectivos.des_colectivo')
                ->join('clientes','clientes.id_cliente','colectivos.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('des_colectivo')
                ->get(),
            
            "zonas" => DB::table('plantas_zonas')
                ->select('clientes.id_cliente','clientes.nom_cliente','plantas_zonas.key_id as id_zona','plantas_zonas.des_zona','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta')
                ->join('plantas','plantas_zonas.id_planta','plantas.id_planta')
                ->join('clientes','clientes.id_cliente','plantas.id_cliente')
                ->join('edificios','plantas.id_edificio','edificios.id_edificio')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('des_edificio')
                ->orderby('des_planta')
                ->orderby('des_zona')
                ->get(),

            "grupos" => DB::table('grupos_trabajos')
                ->select('clientes.id_cliente','clientes.nom_cliente','grupos_trabajos.id_grupo','grupos_trabajos.des_grupo')
                ->join('clientes','clientes.id_cliente','grupos_trabajos.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('des_grupo')
                ->get(),

            "contratas" => DB::table('contratas')
                ->select('clientes.id_cliente','clientes.nom_cliente','contratas.id_contrata','contratas.des_contrata')
                ->join('clientes','clientes.id_cliente','contratas.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('des_contrata')
                ->get(),

            "planes" => DB::table('trabajos_planes')
                ->select('clientes.id_cliente','clientes.nom_cliente','trabajos_planes.id_plan','trabajos_planes.des_plan')
                ->join('clientes','clientes.id_cliente','trabajos_planes.id_cliente')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->orderby('clientes.nom_cliente')
                ->orderby('des_plan')
                ->get(),
        ];
    }

    public function loadplantas(Request $r){

        $r=$this->filtro_clientes($r);
        if (!$r->edificio) {
            $r->edificio=DB::table('edificios')->wherein('id_cliente',$r->cliente)->pluck('id_edificio');
        } else{
            $r->edificio=DB::table('edificios')->wherein('id_edificio',$r->edificio)->wherein('id_cliente',$r->cliente)->pluck('id_edificio');
        }

        return
        [
            "plantas" => DB::table('plantas')
                    ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta')
                    ->join('clientes','clientes.id_cliente','plantas.id_cliente')
                    ->join('edificios','edificios.id_edificio','plantas.id_edificio')
                    ->whereIn('clientes.id_cliente',$r->cliente)
                    ->wherein('edificios.id_edificio',$r->edificio)
                    ->get(),
            
            "zonas" => DB::table('plantas_zonas')
                    ->select('clientes.id_cliente','clientes.nom_cliente','plantas_zonas.key_id as id_zona','plantas_zonas.des_zona','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta')
                    ->join('plantas','plantas_zonas.id_planta','plantas.id_planta')
                    ->join('clientes','clientes.id_cliente','plantas.id_cliente')
                    ->join('edificios','plantas.id_edificio','edificios.id_edificio')
                    ->whereIn('clientes.id_cliente',$r->cliente)
                    ->wherein('edificios.id_edificio',$r->edificio)
                    ->orderby('clientes.nom_cliente')
                    ->orderby('des_edificio')
                    ->orderby('des_planta')
                    ->orderby('des_zona')
                    ->get(),

            "puestos" => DB::table('puestos')
                ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta','puestos.id_puesto','puestos.cod_puesto')
                ->join('clientes','clientes.id_cliente','puestos.id_cliente')
                ->join('edificios','edificios.id_edificio','puestos.id_edificio')
                ->join('plantas','plantas.id_planta','puestos.id_planta')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->wherein('edificios.id_edificio',$r->edificio)
                ->where(function($q){
                    if (isSupervisor(Auth::user()->id)) {
                        $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                        $q->wherein('puestos.id_puesto',$puestos_usuario);
                    }
                })
                ->get()
        ];
    }

    public function loadpuestos(Request $r){

        $r=$this->filtro_clientes($r);
        if (!$r->edificio) {
            $r->edificio=DB::table('edificios')->wherein('id_cliente',$r->cliente)->pluck('id_edificio');
        } else{
            $r->edificio=DB::table('edificios')->wherein('id_edificio',$r->edificio)->wherein('id_cliente',$r->cliente)->pluck('id_edificio');
        }

        if (!$r->planta) {
            $r->planta=DB::table('plantas')->wherein('id_cliente',$r->cliente)->pluck('id_planta');
        } else{
            $r->planta=DB::table('plantas')->wherein('id_planta',$r->planta)->wherein('id_cliente',$r->cliente)->pluck('id_planta');
        }

        return
        [
            "zonas" => DB::table('plantas_zonas')
                    ->select('clientes.id_cliente','clientes.nom_cliente','plantas_zonas.key_id as id_zona','plantas_zonas.des_zona','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta')
                    ->join('plantas','plantas_zonas.id_planta','plantas.id_planta')
                    ->join('clientes','clientes.id_cliente','plantas.id_cliente')
                    ->join('edificios','plantas.id_edificio','edificios.id_edificio')
                    ->whereIn('clientes.id_cliente',$r->cliente)
                    ->wherein('edificios.id_edificio',$r->edificio)
                    ->wherein('plantas.id_planta',$r->planta)
                    ->orderby('clientes.nom_cliente')
                    ->orderby('des_edificio')
                    ->orderby('des_planta')
                    ->orderby('des_zona')
                    ->get(),

            "puestos" => DB::table('puestos')
                ->select('clientes.id_cliente','clientes.nom_cliente','edificios.id_edificio','edificios.des_edificio','plantas.id_planta','plantas.des_planta','puestos.id_puesto','puestos.cod_puesto')
                ->join('clientes','clientes.id_cliente','puestos.id_cliente')
                ->join('edificios','edificios.id_edificio','puestos.id_edificio')
                ->join('plantas','plantas.id_planta','puestos.id_planta')
                ->whereIn('clientes.id_cliente',$r->cliente)
                ->wherein('edificios.id_edificio',$r->edificio)
                ->wherein('plantas.id_planta',$r->planta)
                ->where(function($q){
                    if (isSupervisor(Auth::user()->id)) {
                        $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                        $q->wherein('puestos.id_puesto',$puestos_usuario);
                    }
                })
                ->get()
        ];
    }

    public function combo_limpiadores(Request $r){

        $clientes=DB::table('puestos')
            ->join('clientes','clientes.id_cliente','puestos.id_cliente')
            ->wherein('puestos.id_puesto',$r->lista_id)
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->pluck('nom_cliente','clientes.id_cliente')
            ->unique();

        $usuarios=DB::table('users')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->wherein('users.id_cliente',array_keys($clientes->toarray()))
            ->where(function($q) use($r){
                if($r->tipo=='L')
                    {   
                        $q->where('nivel_acceso',config('app.id_perfil_personal_limpieza')); 
                    } else {
                        $q->where('nivel_acceso',config('app.id_perfil_personal_mantenimiento')); 
                    }
                            
            })            
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();
        
        return view('resources.combo_limpiadores',compact('usuarios','clientes'));
    }

    public function combo_plantas($id_edificio){

        $plantas=DB::table('plantas')
            ->where('id_edificio',$id_edificio)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->get();
        return view('resources.combo_plantas',compact('plantas'));
    }

    public function combo_plantas_usuario($id_edificio){

        $plantas=DB::table('plantas')
            ->select('plantas.id_planta','plantas.des_planta')
            ->leftjoin('plantas_usuario','plantas.id_planta','plantas_usuario.id_planta')
            ->where(function($q){
                $q->where('plantas_usuario.id_usuario',Auth::user()->id);
                $q->orwhere('plantas.mca_publica','S');
            })
            ->where('id_edificio',$id_edificio)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->distinct()
            ->get();
        return view('resources.combo_plantas',compact('plantas'));
    }

    public function combo_plantas_salas($id_edificio){

        $plantas=DB::table('plantas')
            ->select('plantas.id_planta','plantas.des_planta')
            ->join('puestos','plantas.id_planta','puestos.id_planta')
            ->where('plantas.id_edificio',$id_edificio)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_sala'))
            ->distinct()
            ->get();
        return view('resources.combo_plantas',compact('plantas'));
    }


    public function combo_edificios($id_cliente){
        $edificios=DB::table('edificios')
            ->where('id_cliente',$id_cliente)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->get();
        return view('resources.combo_edificios',compact('edificios'));
    }

    public function combo_paises($id_cliente){
        $datos = DB::table('provincias')
            ->select('nom_pais as nombre', 'id_pais as id')
			->leftjoin("edificios", "provincias.id_prov", "edificios.id_provincia")
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
			->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_cliente', $id_cliente)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->orderby('nom_pais')
            ->distinct()
            ->get();
        return view('resources.combo_generico',compact('datos'));
    }

    public function combo_regiones($id_cliente){
        $datos = DB::table('provincias')
            ->select('regiones.cod_region as id', 'nom_region as nombre')
            ->leftjoin("edificios", "provincias.id_prov", "edificios.id_provincia")
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
            ->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_cliente', $id_cliente)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->orderby('nom_region')
            ->distinct()
            ->get();
        return view('resources.combo_generico',compact('datos'));
    }

    public function combo_provincias($id_cliente){
        $datos = DB::table('provincias')
            ->select('id_prov as id', 'nombre')
            ->leftjoin("edificios", "provincias.id_prov", "edificios.id_provincia")
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
            ->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_cliente', $id_cliente)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->orderby('nombre')
            ->distinct()
            ->get();
        return view('resources.combo_generico',compact('datos'));
    }
    
    public function search_clientes_json(Request $r){
        $r->searchTerm=strtoupper($r->searchTerm);
        if(strlen($r->searchTerm)<3 || !isset($r->searchTerm)){
            return json_encode(["1"]);
        }
        $clientes=DB::table('clientes')
        ->select('clientes.id_cliente as id','clientes.nom_cliente as text')
		->where(function($q){
            if (!fullAccess()) {
                $q->WhereIn('id_cliente',clientes());
            }
            if (session('id_cliente')) {
                $q->where('id_cliente',session('id_cliente'));
            }
        })
        ->where('clientes.nom_cliente', 'LIKE', "%{$r->searchTerm}%")
        ->orderby('nom_cliente')
        ->get();
        if((fullAccess() || $clientes->count()>100) && (strlen($r->searchTerm)<3 || !isset($r->searchTerm))){
            return json_encode(["2"]);
        } else {
            return json_encode($clientes);
        }
        
        //return view('combos.fill_combo_clientes',compact('clientes'));
    }

    public function ReloadDepartamentoPadre($cliente,$padre,$id){
        return View('departments.autocomplete_dep',compact('cliente','padre','id'));
    }

}
