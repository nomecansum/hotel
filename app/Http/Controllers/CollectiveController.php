<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use App\Services\ColectivoService;
use App\Models\colectivos;

class CollectiveController extends Controller
{
    //
	public function index(Request $r)
	{
		$colectivos = DB::table('colectivos')
        ->select('colectivos.*','clientes.nom_cliente')
        ->leftjoin('colectivos_usuarios', 'colectivos.cod_colectivo', '=', 'colectivos_usuarios.cod_colectivo')
		->selectraw("count(id_usuario) as cuenta")
		->where(function($q){
            if (!isAdmin()) {
                $q->where('colectivos.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('colectivos.id_cliente',session('CL')['id_cliente']);
            }
        })
		->where(function($q) use ($r){
			if (session('id_cliente'))
				$q->where('colectivos.id_cliente',session('id_cliente'));
			elseif(!empty($r->id_cliente))
				$q->wherein('colectivos.id_cliente', $r->id_cliente);
			else $q->where('colectivos.id_cliente', Auth::user()->id_cliente);
		})
		->join('clientes','clientes.id_cliente','colectivos.id_cliente')
		->wherenull('clientes.fec_borrado')
		->where('colectivos.cod_colectivo', '>', 0)
        ->groupby('colectivos.cod_colectivo')
		->get();
		return view('collective.index',compact('colectivos', 'r'));
	}


	public function edit($id=0)
	{
	    if ($id==0){
            $c=new colectivos;
            $c->id_cliente=Auth::user()->id_cliente;
        } else {
            validar_acceso_tabla($id,"colectivos");
            $c=colectivos::find($id);
        }
        $clientes=DB::table('clientes')
            ->wherein('id_cliente',clientes())
            ->get();

		return view('collective.editor',compact('c','clientes','id'));
	}

	public function update(Request $r,$id)
	{
		$svc_col=new ColectivoService;


        if ($r->id_colectivo==0){
            $c=new colectivos;
            $c->id_cliente=Auth::user()->id_cliente;
            $resultado=$svc_col->insertar($r);
        } else {
            validar_acceso_tabla($id,"colectivos");
            $resultado=$svc_col->actualizar($r,$id);
        }

        if($resultado['result'])
			{
                savebitacora("Guardado colectivo ".$r->des_colectivo. $resultado['mensaje'],null);
				return [
					'title' => trans('strings.collective'),
					'message' => $resultado['mensaje'],
					'url' => url('collective')
				];
			} else{
                savebitacora("Error al crear colectivo ".$r->des_colectivo. $resultado['mensaje'],null);
				return [
					'title' => trans('strings.collective'),
					'error' => $resultado['mensaje'],
					'url' => url('collective')
				];
			}
	}

	public function delete($id)
	{
		$svc_col=new ColectivoService;

        $resultado=$svc_col->delete($id);

        if($resultado['result'])
			{
				flash($resultado['mensaje'])->success();
			} else{
				flash($resultado['mensaje'])->error();
			}
		return redirect('collective');
	}


}
