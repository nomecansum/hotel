<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use App\Models\tags;

class TagsController extends Controller
{
    public function index(){
        $tags = DB::table('tags')
        ->join('clientes','clientes.id_cliente','tags.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('tags.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('tags.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->get();
        
        return view('tags.index', compact('tags'));
    }

    public function edit($id=0){
        if($id==0){
            $tag=new tags();
        } else {
            $tag = tags::findorfail($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();

        return view('tags.edit', compact('tag','Clientes','id'));
    }

    public function save(Request $r){
        try {
            if($r->id==0){
                tags::create($r->all());
            } else {
                $tag=tags::find($r->id);
                $tag->update($r->all());
            }
            savebitacora('Tipo de incidencia creado '.$r->nom_tag,"Tags","save","OK");
            return [
                'title' => "Tags",
                'message' => 'Tag '.$r->nom_tag. ' actualizado con exito',
                'url' => url('/tags')
            ];
        } catch (\Throwable $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error creando Tag '.$r->nom_tag.' '.$exception->getMessage() ,"Tags","save","ERROR");
            return [
                'title' => "Tags",
                'error' => 'ERROR: Ocurrio un error actualizando el Tag '.$r->nom_tag.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function delete($id=0){
        try {
            $tag = tags::findorfail($id);

            $tag->delete();
            savebitacora('Tag borrada '.$tag->nom_tag,"Tags","delete","OK");
            flash('Tag '.$tag->nom_tag.' borrado')->success();
            return back()->withInput();
        } catch (\Throwable $exception) {
            flash('ERROR: Ocurrio un error borrando Tag '.$tag->nom_tag.' '.$exception->getMessage())->error();
            return back()->withInput();
        }
    }

}
