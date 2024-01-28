<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Carbon\Carbon;
use App\Services\FestivoService;
use App\Models\festivos;

class FestivesController extends Controller
{
    //
	public function index()
	{

		$festives = DB::table('festivos')
		->join('clientes','clientes.id_cliente','festivos.id_cliente')
		->whereraw('year(val_fecha)='.Carbon::now()->year)
		->where(function($q){
            if (!isAdmin()) {
                $q->where('festivos.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('festivos.id_cliente',session('CL')['id_cliente']);
            }
        })
		->orwhere('mca_fijo','S')
		->orderby('clientes.id_cliente')
		->orderby('val_fecha')
		->get();

		$festives_cal = DB::table('festivos')
		->join('clientes','clientes.id_cliente','festivos.id_cliente')
		->where(function($q){
            if (!isAdmin()) {
                $q->where('festivos.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('festivos.id_cliente',session('CL')['id_cliente']);
            }
        })
		->orwhere('mca_fijo','S')
		->orderby('clientes.id_cliente')
		->orderby('val_fecha')
		->get();

		$kpi=FestivesController::kpi_anio(Carbon::now()->year,Auth::user()->id_cliente);
		$minimo=$kpi['minimo'];
		$maximo=$kpi['maximo'];
		$countFest=$kpi['countFest'];
		$countFestNac=$kpi['countFestNac'];
		$countFestReg=$kpi['countFestReg'];
		$countFestProv=$kpi['countFestProv'];
		$countFestLoc=$kpi['countFestLoc'];
		return view('festives.index',compact('festives','festives_cal','minimo', 'maximo', 'countFest', 'countFestNac', 'countFestReg', 'countFestProv', 'countFestLoc'));
	}

	public function save(Request $r)
	{

		$svc_fes=new FestivoService;

		//$r->val_fecha=adaptar_fecha($r->val_fecha);

		$svc_fes->validar_request($r,'toast');

		$resultado=$svc_fes->insertar($r);

		if($resultado['result'])
			{
				return [
					'title' => trans('strings.festives'),
					'message' => $resultado['mensaje'],
					'url' => url('festives')
				];
			} else{
				return [
					'title' => trans('strings.festives'),
					'error' => $resultado['mensaje'],
					'url' => url('festives')
				];
			}
    }

	public function edit($id, $cli = null)
	{
		if ($id != 0)
			validar_acceso_tabla($id, "festivos");

		if(empty($cli))
            $cli = session('CL')['id_cliente'];


        $fes = DB::table('festivos')->where('cod_festivo', $id)->first();
        if(isset($fes->cod_centro))
            $centros_festivo = explode(",", $fes->cod_centro);
        else $centros_festivo = [];
        if(isset($fes->cod_provincia))
            $prov_festivo = explode(",", $fes->cod_provincia);
        else $prov_festivo = [];

        if(isset($fes->cod_region))
            $regiones_festivo = explode(",", $fes->cod_region);
        else $regiones_festivo = [];

        $clientes = DB::table('clientes')->where('id_cliente', Auth::user()->id_cliente)->get();

		if (!isset($fes)) {
			$fes= new \stdClass;
			$fes->cod_festivo = 0;
			$fes->des_festivo = "";
			$fes->val_fecha = "";
			$fes->mca_interanual = "N";
			$fes->mca_nacional = "N";
			$fes->cod_provincia = "";
            $fes->cod_pais = "";
            $fes->cod_region = "";
			$fes->cod_centro = "";
			$fes->mca_fijo = "N";
            $fes->id_cliente = $cli;

            $centros_festivo = [];
            $prov_festivo = [];
            $regiones_festivo = [];
        }

        $centros = DB::table('edificios')
            ->where('id_cliente', $fes->id_cliente)
            ->where('id_edificio', '>', 0)
            ->orderby('des_edificio')
            ->get();

        $provincias = DB::table('provincias')
            ->select('id_prov', 'nombre', 'nom_pais', 'id_pais', 'regiones.cod_region', 'regiones.nom_region')
			->leftjoin("edificios", "provincias.id_prov", "edificios.id_provincia")
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
			->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_cliente', $fes->id_cliente)
            ->orderby('nom_pais')
            ->orderby('nom_region')
            ->orderby('nombre')
            ->get();


        $paises = $provincias->unique('id_pais', 'nom_pais')->sortby('nom_pais');
        $regiones = $provincias->unique('cod_region', 'nom_region')->sortby('nom_region');
        $provincias = $provincias->unique('id_prov', 'nombre')->sortby('nombre');

		return view('festives.editor',compact('fes','clientes','provincias','paises','centros','regiones','centros_festivo','prov_festivo','regiones_festivo'))->render();
	}

	public function update(Request $r, $id)
	{
		$svc_fes=new FestivoService;

		$r->val_fecha=adaptar_fecha($r->val_fecha);

		$svc_fes->validar_request($r,'toast');

		$resultado=$svc_fes->actualizar($id,$r);

		if($resultado['result'])
			{
				return [
					'title' => trans('strings.festives'),
					'message' => $resultado['mensaje'],
					'url' => url('festives')
				];
			} else{
				return [
					'title' => trans('strings.festives'),
					'error' => $resultado['mensaje'],
					'url' => url('festives')
				];
			}
	}

	public function calendar_regenerar(Request $r)
	{
		if(isset($r->id_cliente)){
			$cliente=$r->id_cliente;
		} else if (session('id_cliente')) {
			$cliente=session('id_cliente');
		} else {
			$cliente=Auth::user()->id_cliente;
		}

		$fest = DB::table('festivos')
		->whereraw('year(val_fecha)='.$r->from)
		->where('id_cliente',$cliente)
		->get();

		DB::table('festivos')
		->whereraw('year(val_fecha)='.$r->to)
		->where('id_cliente',$cliente)
		->delete();

		try{
			foreach ($fest as $key => $value) {
				DB::table('festivos')->insert([
					'des_festivo' => $value->des_festivo,
					'val_fecha' => Carbon::parse($r->to.'-'.Carbon::parse($value->val_fecha)->format('m-d'))->format('Y-m-d'),
					'cod_centro' => $value->cod_centro,
					'cod_provincia' => $value->cod_provincia,
                    'cod_pais' => $value->cod_pais,
                    'cod_region' => $value->cod_region,
					'mca_interanual' => $value->mca_interanual,
					'mca_nacional' => $value->mca_nacional,
					'mca_fijo' => $value->mca_fijo,
					'id_cliente' => $cliente,
				]);
			}
			savebitacora("Se ha creado el calendario para el año ".$r->to." a partir del año ".$r->from ,"Festivos","calendar_regenerar","OK");
			return [
				'title' => trans('strings.festives'),
				'message' => "Se ha creado el calendario para el año ".$r->to." a partir del año ".$r->from,
				'url' => [url('festives')]
			];
		} catch(\Exception $e){
			savebitacora("Error creando calendario para el año ".$r->to." a partir del año ".$r->from ,"Festivos","calendar_regenerar","ERROR");
			return [
				'title' => trans('strings.festives'),
				'error' => "Error creando calendario para el año ".$r->to." a partir del año ".$r->from,
				//'url' => [url('festives')]
			];
		}

		
	}

	public function TablaFilter(Request $r)
	{
		$festives = DB::table('festivos')
		->join('clientes','clientes.id_cliente','festivos.id_cliente')
		->where(function($q) use($r)
		{
			if(isset($r->name))
				$q->where('des_festivo','like','%'.$r->name.'%');

			if(isset($r->year))
				$q->whereraw('year(val_fecha)='.$r->year);

			if(isset($r->center))
				$q->where('festivos.cod_centro', $r->center);

            if(isset($r->id_cliente))
                $q->where('festivos.id_cliente', $r->id_cliente);
		})
		->where(function($q){
			if (!fullAccess()) {
				$q->whereIn('festivos.id_cliente', clientes());
			}
			if (session('id_cliente')) {
				$q->where('festivos.id_cliente',session('id_cliente'));
			}
            else $q->Where('festivos.id_cliente', Auth::user()->id_cliente);
		})
		->orwhere('mca_fijo','S')
		->orderby('clientes.id_cliente')
		->orderby('val_fecha')
		->get();

		$anio=$r->year;

		return view('festives.fill_tabla_festivos',compact('festives','anio'))->render();
	}

	public function calendarFilter(Request $r)
	{
		$festives = DB::table('festivos')
		->join('clientes','clientes.id_cliente','festivos.id_cliente')
		->where(function($q) use($r)
		{
			if(isset($r->name))
				$q->where('des_festivo','like','%'.$r->name.'%');

			if(isset($r->year))
				$q->whereraw('year(val_fecha)='.$r->year);

			if(isset($r->center))
				$q->where('festivos.cod_centro', $r->center);

            if(isset($r->id_cliente))
                $q->where('festivos.id_cliente', $r->id_cliente);
		})
		->where(function($q){
			if (!fullAccess()) {
				$q->whereIn('festivos.id_cliente', clientes());
			}
			if (session('id_cliente')) {
				$q->where('festivos.id_cliente',session('id_cliente'));
			}
            else $q->Where('festivos.id_cliente', Auth::user()->id_cliente);
		})
		->orwhere('mca_fijo','S')
		->orderby('clientes.id_cliente')
		->orderby('val_fecha')
		->get();

		$anio=$r->year;

		return view('festives.fill_calendar_festivos',compact('festives','anio'))->render();
	}

	public function kpiFilter(Request $r)
	{
		$kpi=FestivesController::kpi_anio($r->year,$r->id_cliente);
		$minimo=$kpi['minimo'];
		$maximo=$kpi['maximo'];
		$countFest=$kpi['countFest'];
		$countFestNac=$kpi['countFestNac'];
		$countFestReg=$kpi['countFestReg'];
		$countFestProv=$kpi['countFestProv'];
		$countFestLoc=$kpi['countFestLoc'];

		$anio=$r->year;

		return view('festives.fill_kpi_festivos',compact('minimo', 'maximo', 'countFest', 'countFestNac', 'countFestReg', 'countFestProv', 'countFestLoc'));
	}
	public function kpi_anio($anio,$cliente){


		$minimo = DB::table('festivos')
		->selectraw('min(year(val_fecha)) as minimo')
		->where(function($q) use($cliente){
			if (!fullAccess()) {
	    		$q->wherein('id_cliente', clientes());
			}
			else $q->Where('festivos.id_cliente', $cliente);
		})
		->wherenotnull('val_fecha')
		->whereraw('year(val_fecha)>1')
		->first()->minimo;

		$maximo = DB::table('festivos')
		->selectraw('max(year(val_fecha)) as minimo')
		->where(function($q) use($cliente){
			if (!fullAccess()) {
	    		$q->wherein('id_cliente',clientes());
			}
			else $q->Where('festivos.id_cliente', $cliente);
		})
		->first()->minimo;

        $countFestNac = DB::table('festivos')
            ->join('clientes','clientes.id_cliente','festivos.id_cliente')
            ->whereraw('year(val_fecha)='.$anio)
            // ->whereraw('centros.cod_centro in (festivos.cod_centro)')
            ->where(function($q) use($cliente){
                if (!fullAccess()) {
                    $q->Wherein('festivos.id_cliente', clientes());
                }
                else $q->Where('festivos.id_cliente', $cliente);
            })
            ->where('mca_nacional', 'S')
            ->orwhere('mca_fijo','S')
            ->orderby('clientes.id_cliente')
            ->orderby('val_fecha')
            ->count();

        $countFestReg = DB::table('festivos')
            ->join('clientes','clientes.id_cliente','festivos.id_cliente')
            ->whereraw('year(val_fecha)='.$anio)
            // ->whereraw('centros.cod_centro in (festivos.cod_centro)')
            ->where(function($q) use($cliente){
                if (!fullAccess()) {
                    $q->Wherein('festivos.id_cliente', clientes());
                }
                else $q->Where('festivos.id_cliente', $cliente);
            })
            ->where('mca_nacional', 'N')
            ->where(function($q){
                $q->Where('cod_centro', "");
                $q->orWhereNull('cod_centro');
            })
            ->where(function($q){
                $q->Where('cod_region', "!=", "");
                $q->WherenotNull('cod_region');
            })
            ->where(function($q){
                $q->Where('cod_provincia', "");
                $q->orWhereNull('cod_provincia');
            })
            ->orwhere('mca_fijo','S')
            ->orderby('clientes.id_cliente')
            ->orderby('val_fecha')
            ->count();

        $countFestProv = DB::table('festivos')
            ->join('clientes','clientes.id_cliente','festivos.id_cliente')
            ->whereraw('year(val_fecha)='.$anio)
            // ->whereraw('centros.cod_centro in (festivos.cod_centro)')
            ->where(function($q) use($cliente){
                if (!fullAccess()) {
                    $q->Wherein('festivos.id_cliente', clientes());
                }
                else $q->Where('festivos.id_cliente', $cliente);
            })
            ->where('mca_nacional', 'N')
            ->where(function($q){
                $q->Where('cod_centro', "");
                $q->orWhereNull('cod_centro');
            })

            ->where(function($q){
                $q->Where('cod_provincia', "!=", "");
                $q->WherenotNull('cod_provincia');
            })
            ->orwhere('mca_fijo','S')
            ->orderby('clientes.id_cliente')
            ->orderby('val_fecha')
            ->count();

        $countFestLoc = DB::table('festivos')
            ->join('clientes','clientes.id_cliente','festivos.id_cliente')
            ->whereraw('year(val_fecha)='.$anio)
            // ->whereraw('centros.cod_centro in (festivos.cod_centro)')
            ->where(function($q) use($cliente){
                if (!fullAccess()) {
                    $q->Wherein('festivos.id_cliente', clientes());
                }
                else $q->Where('festivos.id_cliente', $cliente);
            })
            ->where('mca_nacional', 'N')

            ->where(function($q){
                $q->Where('cod_centro', "!=", "");
                $q->WherenotNull('cod_centro');
            })
            ->orwhere('mca_fijo','S')
            ->orderby('clientes.id_cliente')
            ->orderby('val_fecha')
            ->count();

        $countFest = $countFestNac+$countFestReg+$countFestProv+$countFestLoc;

		return [
				'minimo' => $minimo,
				'maximo' => $maximo,
				'countFestNac' => $countFestNac,
				'countFestReg' => $countFestReg,
				'countFestProv' => $countFestProv,
				'countFestLoc' => $countFestLoc,
				'countFest' => $countFest
			];

	}

	public function delete($id)
	{
		validar_acceso_tabla($id,"festivos");
		$svc_fes=new FestivoService;
		$fes=festivos::find($id);
		$resultado=$svc_fes->delete($id);
		
		if($resultado['result'])
			{
				savebitacora("Se ha borrado el festivo ".$fes->des_festivo ,"Festivos","delete","OK");
				flash($resultado['mensaje'])->success();
			} else{
				savebitacora("ERROR borrando festivo ".$resultado['mensaje'] ,"Festivos","delete","ERROR");
				flash($resultado['mensaje'])->error();
			}
		return back();
	}
}
