<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use Auth;

use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    //
	public function profiles()
	{
		$nivel_acceso = \DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->val_nivel_acceso;
		$niveles =
		  DB::table('niveles_acceso')
			->where('val_nivel_acceso','<=',$nivel_acceso)
			->where(function($q){
				if (!isAdmin()) {
					$q->where('niveles_acceso.id_cliente',Auth::user()->id_cliente);
					$q->orwhere('niveles_acceso.mca_fijo','S');
				} else {
					$q->where('niveles_acceso.id_cliente',session('CL')['id_cliente']);
					$q->orwhere('niveles_acceso.mca_fijo','S');
				}
			})
			->get();
		$cuenta =
			DB::table('users')
			->selectraw('count(id) as cuenta,cod_nivel')
			->where('id_cliente',Auth::user()->id_cliente)
			  ->where('nivel_acceso','<=',$nivel_acceso)
			  ->where(function($q){
					if (!isAdmin()) {
						$q->where('users.id_cliente',Auth::user()->id_cliente);
					} else {
						$q->where('users.id_cliente',session('CL')['id_cliente']);
					}
				})
			  ->groupby('cod_nivel')
			  ->get();
		$homepages=DB::table('niveles_acceso')->pluck('home_page')->unique()->toArray();
		return view('permisos.profiles',compact('niveles','nivel_acceso','cuenta','homepages'));
	}

	public function profilesEdit($id)
	{
	    $nivel_acceso = \DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->val_nivel_acceso;
        $niveles =
          DB::table('niveles_acceso')
            ->where('val_nivel_acceso','<=',$nivel_acceso)
			->get();
		$homepages=DB::table('niveles_acceso')->pluck('home_page')->unique()->toArray();
		$n = DB::table('niveles_acceso')->where('cod_nivel',$id)->first();
		return view('permisos.editor_perfiles',compact('n', 'niveles', 'nivel_acceso','homepages'));
	}

	public function profilesSave(Request $r)
	{
		//dd($r);
		if ($r->id!=0) {
			DB::table('niveles_acceso')->where('cod_nivel',$r->id)->update(
				[
				    'des_nivel_acceso' => $r->des_nivel_acceso,
					'val_nivel_acceso' => $r->num_nivel_acceso,
					'id_cliente' => $r->id_cliente,
					'mca_fijo' => isset($r->mca_fijo)?'S':'N',
					'mca_reserva_multiple' => isset($r->mca_reserva_multiple)?'S':'N',
					'mca_liberar_auto' => isset($r->mca_reserva_multiple)?'S':'N',
					'mca_reservar_sabados' => isset($r->mca_reservar_sabados)?'S':'N',
					'mca_reservar_domingos' => isset($r->mca_reservar_domingos)?'S':'N',
					'mca_reservar_festivos' => isset($r->mca_reservar_festivos)?'S':'N',
					'mca_saltarse_antelacion' => isset($r->mca_saltarse_antelacion)?'S':'N',
					'home_page' => $r->home_page
				]
			);

			$n = DB::table('niveles_acceso')->where('cod_nivel',$r->id)->first();
		}
		else
		{
			$n = DB::table('niveles_acceso')->insert(
				[
					'val_nivel_acceso' => $r->num_nivel_acceso,
					'des_nivel_acceso' => $r->des_nivel_acceso,
					'id_cliente' => $r->id_cliente,
					'mca_fijo' => isset($r->mca_fijo)?'S':'N',
					'mca_reserva_multiple' => isset($r->mca_reserva_multiple)?'S':'N',
					'mca_liberar_auto' => isset($r->mca_reserva_multiple)?'S':'N',
					'mca_reservar_sabados' => isset($r->mca_reservar_sabados)?'S':'N',
					'mca_reservar_domingos' => isset($r->mca_reservar_domingos)?'S':'N',
					'mca_reservar_festivos' => isset($r->mca_reservar_festivos)?'S':'N',
					'mca_saltarse_antelacion' => isset($r->mca_saltarse_antelacion)?'S':'N',
					'home_page' => $r->home_page
				]
			);
			$n = DB::table('niveles_acceso')->where('des_nivel_acceso',$r->des_nivel_acceso)->orderby('cod_nivel','desc')->first();
		}


		if($r->hereda_de && $r->hereda_de!=''){
			DB::table('secciones_perfiles')
			->where('id_perfil',$n->cod_nivel)
			->delete();

			$padre=DB::table('secciones_perfiles')->where('id_perfil',$r->hereda_de)->get();
			foreach($padre as $p){
				DB::table('secciones_perfiles')->insert([
					'id_seccion' => $p->id_seccion,
					'id_perfil' => $n->cod_nivel,
					'mca_read' => $p->mca_read,
					'mca_write' => $p->mca_write,
					'mca_create' => $p->mca_create,
					'mca_delete' => $p->mca_delete,
				]);
			}
		}
        else
        {
            DB::table('secciones_perfiles')
            ->where('id_perfil',$n->cod_nivel)
            ->delete();

            $padre = DB::table('secciones_perfiles')->where('id_perfil',1)->get();
            foreach($padre as $p){
                DB::table('secciones_perfiles')->insert([
                    'id_seccion' => $p->id_seccion,
                    'id_perfil' => $n->cod_nivel,
                    'mca_read' => $p->mca_read,
                    'mca_write' => $p->mca_write,
                    'mca_create' => $p->mca_create,
                    'mca_delete' => $p->mca_delete,
                ]);
            }
        }
		savebitacora("Creaado perfil ".$r->des_nivel_acceso,'Permisos',"profilesSave","OK");
		return [
            'title' => "Perfiles",
            'message' => "Perfil ".$r->des_nivel_acceso.': Guardado',
            'url' => url('profiles')
        ];
	}

	public function profilesDelete($id)
	{
		savebitacora("Eliminado perfil ".$id." ".DB::table('niveles_acceso')->where('cod_nivel',$id)->value('des_nivel_acceso'),'Permisos',"profilesDelete","OK");
		DB::table('niveles_acceso')->where('cod_nivel',$id)->delete();
		flash('Perfil '.$id.' Borrado')->success();
		return redirect('profiles');
	}

	public function sections()
	{
		$secciones = DB::table('secciones')
		->where(function($q){
			if (!isAdmin()) {
				$q->wherein('des_seccion',array_column(session('P'), 'des_seccion'));
			}
		})
		->orderby('des_grupo')
		->orderby('des_seccion')
		->get();
		$tipos = ['Seccion','Permiso','Accion'];
		$grupos = DB::table('secciones')->select('des_grupo','icono')->distinct()->get();

		return view('permisos.sections',compact('secciones','grupos','tipos'));
	}

	public function sectionsEdit($id)
	{
	    $secciones = DB::table('secciones')
        ->where(function($q){
            if (!isAdmin()) {
                $q->wherein('des_seccion',array_column(session('P'), 'des_seccion'));
            }
        })
        ->get();
        $tipos = $secciones->pluck('val_tipo', 'val_tipo')->toArray();
        $grupos = DB::table('secciones')->select('des_grupo','icono')->distinct()->get();
		$s = DB::table('secciones')->where('cod_seccion',$id)->first();
		return view('permisos.sections',compact('s','grupos','tipos','secciones'));
	}

	public function sectionsSave(Request $r)
	{
		//dd($r);
		$grupos = DB::table('secciones')->select('des_grupo','icono')->distinct()->get()->pluck('icono', 'des_grupo');
		error_log(json_encode($grupos));
		//dd($r);
		if ($r->id!=0) {
			$n = DB::table('secciones')->where('cod_seccion',$r->id)->update(
				['des_seccion' => $r->des_seccion,
				'val_tipo' => $r->val_tipo,
				'des_grupo' => $r->des_grupo,
				'icono' => $grupos[$r->des_grupo]
				]);
		}else{
			$n = DB::table('secciones')->insert(
				[
					'des_seccion' => $r->des_seccion,
					'val_tipo' => $r->val_tipo,
					'des_grupo' => $r->des_grupo,
					'icono' => $grupos[$r->des_grupo]
				]);
		}
		savebitacora("Actualizado seccion ".$r->des_seccion,'Permisos',"sectionsSave","OK");
		return [
            'title' => "Secciones",
            'message' => "Seccion ".$r->des_seccion." guardada",
            'url' => url('sections')
        ];
	}

	public function sectionsDelete($id)
	{
		savebitacora("Eliminado seccion ".$id." ".DB::table('secciones')->where('cod_seccion',$id)->value('des_seccion'),'Permisos',"sectionsDelete","OK");
		$s = DB::table('secciones')->where('cod_seccion',$id);
		$s->delete();
		return [
            'title' => "Secciones",
            'message' => "Seccion ".$id." borrada",
            'url' => url('sections')
        ];
	}

	public function profilePermissions()
	{
		$permisos = DB::table('secciones')
			->select('secciones_perfiles.id_seccion',
					'secciones_perfiles.id_perfil',
					'secciones_perfiles.mca_read',
					'secciones_perfiles.mca_write',
					'secciones_perfiles.mca_create',
					'secciones_perfiles.mca_delete')
			->join('secciones_perfiles', 'secciones.cod_seccion', '=', 'secciones_perfiles.id_seccion')
			->join('niveles_acceso', 'secciones_perfiles.id_perfil', '=', 'niveles_acceso.cod_nivel')
			->get();

		$secciones = DB::table('secciones')
				->orderby('des_grupo')
				->orderby('des_seccion')
				->get();

		$nivel_acceso = \DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->val_nivel_acceso;
		$niveles = DB::table('niveles_acceso')
				->where('val_nivel_acceso','<=',$nivel_acceso)
				->where(function($q){
					$q->where('id_cliente',Auth::user()->id_cliente);
					$q->orwhere('mca_fijo','S');
				})
				->get();

		$grupos = DB::table('secciones')
			->selectraw('distinct(des_grupo) as des_grupo, icono')
			->orderby('des_grupo')
			->get();


		return view('permisos.profilePermissions',compact('permisos','secciones','niveles','grupos'));
	}

    public function getProfiles(Request $r)
    {
        $nivel_acceso = \DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->val_nivel_acceso;
        $niveles = DB::table('niveles_acceso')
                ->where('val_nivel_acceso', '<=', $nivel_acceso)
                ->where('cod_cliente', $r->cli)
                ->get();
        return $niveles;
    }

	public function addPermissions(Request $r)
	{
		if ($r->type == "R") {$type = "mca_read";}
		if ($r->type == "W") {$type = "mca_write";}
		if ($r->type == "C") {$type = "mca_create";}
		if ($r->type == "D") {$type = "mca_delete";}
		if (DB::table('secciones_perfiles')->where(['id_seccion' => $r->section,'id_perfil' => $r->level])->exists()) {
			DB::table('secciones_perfiles')->where(['id_seccion' => $r->section,'id_perfil' => $r->level])->update([
				$type => 1
			]);
		}else{
			DB::table('secciones_perfiles')->insert([
				'id_seccion' => $r->section,
				'id_perfil' => $r->level,
				$type => 1
			]);
		}
		savebitacora("Añadidos permisos para seccion ".$r->section." y perfil ".$r->level,'Permisos',"addPermissions","OK");
	}

	public function removePermissions(Request $r)
	{
		if ($r->type == "R") {$type = "mca_read";}
		if ($r->type == "W") {$type = "mca_write";}
		if ($r->type == "C") {$type = "mca_create";}
		if ($r->type == "D") {$type = "mca_delete";}
		savebitacora("Eliminados permisos para seccion ".$r->section." y perfil ".$r->level,'Permisos',"removePermissions","OK");
		DB::table('secciones_perfiles')->where(['id_seccion' => $r->section,'id_perfil' => $r->level])->update([
			$type => NULL
		]);
	}

	public function addPermissions_user(Request $r)
	{
		if ($r->type == "R") {$type = "mca_read";}
		if ($r->type == "W") {$type = "mca_write";}
		if ($r->type == "C") {$type = "mca_create";}
		if ($r->type == "D") {$type = "mca_delete";}
		if (DB::table('permisos_usuarios')->where(['id_seccion' => $r->section,'cod_usuario' => $r->level])->exists()) {
			DB::table('permisos_usuarios')->where(['id_seccion' => $r->section,'cod_usuario' => $r->level])->update([
				$type => 1
			]);
		}else{
			DB::table('permisos_usuarios')->insert([
				'id_seccion' => $r->section,
				'cod_usuario' => $r->level,
				$type => 1
			]);
		}
		savebitacora("Añadidos permisos para seccion ".$r->section." y usuario ".$r->level,'Permisos',"addPermissions_user","OK");
	}

	public function removePermissions_user(Request $r)
	{
		if ($r->type == "R") {$type = "mca_read";}
		if ($r->type == "W") {$type = "mca_write";}
		if ($r->type == "C") {$type = "mca_create";}
		if ($r->type == "D") {$type = "mca_delete";}
		savebitacora("permisos_usuarios permisos para seccion ".$r->section." y perfil ".$r->level,'Permisos',"removePermissions_user","OK");
		DB::table('permisos_usuarios')->where(['id_seccion' => $r->section,'cod_usuario' => $r->level])->update([
			$type => NULL
		]);
	}
}
