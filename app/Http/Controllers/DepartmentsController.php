<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Helpers;
use Auth;
use Carbon\Carbon;
use App\Services\DepartamentoService;
use App\Models\departamentos;

class DepartmentsController extends Controller
{
    //
    public function index(Request $r)
    {
        //DB::enableQueryLog(); // Enable query log
        //$departamentos=lista_departamentos('global',null);

        return view('departments.index',compact('r'));
    }

    public function edit($id)
    {
        $month = Carbon::now()->startOfMonth();

        validar_acceso_tabla($id,"departamentos");

        $empleados=DB::table('users')
            ->where('users.id_departamento',$id)
            ->get();

        $d = DB::table('departamentos')->where('cod_departamento',$id)->join('clientes', 'departamentos.id_cliente', '=', 'clientes.id_cliente')->first();
    	return view('departments.create',compact('d','empleados'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function save(Request $r)
    {
        try{
			//validar_acceso_tabla($r->id,"departamentos");
			$dsvc = new DepartamentoService;
			//Validarlos datos
			$dsvc->validar_request($r,'toast');
			//Insertar el departamento
            $d = $dsvc->insertar($r);

			savebitacora("Creado departamento ".$r->nom_departamento,$r->cod_cliente);

			return [
				'title' => trans('strings.departments'),
				'message' => "Creado departamento ".$r->nom_departamento,$r->cod_cliente,
				'url' => url('departments')

			];
		} catch (\Throwable $e) {
            savebitacora("Error al crear departamento ".$r->nom_departamento. $e->getMessage(),null);
            return [
                'title' => trans('strings.departments'),
                'error' => "Error al crear departamento ".mensaje_excepcion($e),
                'url' => url('departments')
            ];
        }
    }

    public function update(Request $r)
    {
        try{
			validar_acceso_tabla($r->id,"departamentos");
			$dsvc = new DepartamentoService;
			//Validarlos datos
			$dsvc->validar_request($r,'toast');
			//Actualizar el departamento
            $dsvc->actualizar($r);

			savebitacora("Actualizado departamento ".$r->nom_departamento,$r->cod_cliente);

			return [
				'title' => trans('strings.departments'),
				'message' => "Actualizado departamento ".$r->nom_departamento,$r->cod_cliente,
				'url' => url('departments')
			];
		} catch (\Throwable $e) {
            savebitacora("Error al actualizar departamento ".$r->nom_departamento. $e->getMessage(),null);
            return [
                'error' => trans('strings.departments'),
                'message' => "Error al actualizar departamento ".mensaje_excepcion($e),
                'url' => url('departments')
            ];
        }
    }

    public function delete($id)
	{
        $dep=DB::table('departamentos')->where('cod_departamento',$id)->first();
        $hay_cosas=DB::table('users')->where('id_departamento',$id)->count();
        if ($hay_cosas!=0){
            flash("Error al intentar eliminar el departamento  ".DB::table('departamentos')->where('cod_departamento',$id)->value('nom_departamento')." [".$id."] El departamento no esta vacio. Debe borrar o mover los empleados de este departamento a otro")->error();
            return back();
        }
        //Ahora a ver si es el ultimo departamento en la faz de la tierra
        $hay_dep=DB::table('departamentos')->where('id_cliente',$dep->id_cliente)->where('cod_departamento','<>',$id)->count();
        if($hay_dep==0){
            flash("Error al intentar eliminar el departamento  ".$dep->nom_departamento." [".$id."] Es el ultimo departamento de la empresa.<br> <b>La empresa debe tener al menos un departamento</b>")->error();
            return back();
        }
        //A ver si tiene departamentos colgando de el
        $hay_dep=DB::table('departamentos')->where('cod_departamento_padre',$id)->count();
        if($hay_dep!=0){
            flash("Error al intentar eliminar el departamento  ".$dep->nom_departamento." [".$id."]<br> Hay <b>".$hay_dep."</b> departamentos que dependen directamete de éste que quiere eliminar. <br><b>Asignelos primero a otro departamento</b>")->error();
            return back();
        }

        validar_acceso_tabla($id,"departamentos");
        flash("Departamento ".$dep->nom_departamento." borrado con éxito.")->success();
        savebitacora("Eliminado departamento: ".DB::table('departamentos')->where('cod_departamento',$id)->value('nom_departamento')." ".$id,DB::table('departamentos')->where('cod_departamento',$id)->value('id_cliente'));
        DB::table('departamentos')->where('cod_departamento',$id)->delete();
		return back();
    }

    public function estructura($id=0){
        $edificios=DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get();
	    $seleccionado=$id==0?$edificios->first()->id_edificio:$id;
        $cli = DB::table('clientes')->where('id_cliente', Auth::user()->id_cliente)->first();

        return view('departments.estructura',compact('edificios','seleccionado','cli'));
    }

    
}
