<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\bitacora;
use Illuminate\Http\Request;
use Exception;
use DB;
use Carbon\Carbon;

class BitacorasController extends Controller
{

    /**
     * Display a listing of the bitacoras.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $bitacoras = DB::table('bitacora')
            ->join('users','bitacora.id_usuario','users.name')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->wherein('clientes.id_cliente',clientes());
                } else {
                    $q->where('clientes.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where('fecha','>=',Carbon::now()->subDays(10))
            ->orderby('fecha','desc')
            ->get();
        
        $usuarios= DB::table('bitacora')
            ->join('users','bitacora.id_usuario','users.name')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->orderby('name')
            ->pluck('name','id_usuario')
            ->unique();

        $modulos= DB::table('bitacora')
            ->join('users','bitacora.id_usuario','users.name')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->orderby('id_modulo')
            ->pluck('id_modulo')
            ->unique();

        $min=$bitacoras->min('fecha');
        $max=$bitacoras->max('fecha');
        return view('bitacoras.index', compact('bitacoras','usuarios','modulos','min','max'));
    }
    
    public function search(Request $r)
    {
        try {      //dd($r->all());
            $usuarios= DB::table('bitacora')
                ->join('users','bitacora.id_usuario','users.name')
                ->join('clientes','clientes.id_cliente','users.id_cliente')
                ->orderby('name')
                ->pluck('name','id_usuario')
                ->unique();

            $modulos= DB::table('bitacora')
                ->join('users','bitacora.id_usuario','users.name')
                ->join('clientes','clientes.id_cliente','users.id_cliente')
                ->orderby('id_modulo')
                ->pluck('id_modulo')
                ->unique();

            //D($r->modulos);
           
            if (isset($r->fechas)){
                $f = explode(' - ',$r->fechas);
                $f1 = adaptar_fecha($f[0]);
                $f2 = adaptar_fecha($f[1]);
            }

            $bitacoras=DB::table('bitacora')
                ->join('users','bitacora.id_usuario','users.name')
                ->join('clientes','clientes.id_cliente','users.id_cliente')
                ->when($r->tipo_log, function($query) use ($r) {
                return  $query->where('status', $r->tipo_log);
                })
                ->when($r->usuario, function($query) use ($r) {
                    return  $query->where('users.id', $r->usuario);
                })
                ->when($r->fechas, function($query) use ($f1,$f2) {
                    $query->where('fecha','>=',$f1);
                    $query->where('fecha','<=',Carbon::parse($f2)->endofDay());
                })
                ->when($r->modulos, function($query) use ($r) {
                    return  $query->whereIn('id_modulo', $r->modulos);
                    })
                ->when($r->texto && $r->texto!='', function($query) use ($r) {
                    return  $query->whereraw("UPPER(accion) like '%".strtoupper($r->texto)."%'");
                    })
                ->orderby('fecha','desc')
                ->get();
            $min=$bitacoras->min('fecha');
            $max=$bitacoras->max('fecha');
            return view('bitacoras.index', compact('bitacoras'), compact('r','usuarios','modulos','min','max'));
        } catch (\Throwable $exception) {
            flash('ERROR: Ocurrio un error al hacer la busqueda '.$exception->getMessage())->error();
            return back()->withInput();
        }        
    }

    public function ver_entrada($id){
        $bitacora=bitacora::find($id);
        return view('bitacoras.ver_entrada', compact('bitacora'));
    }

    protected function getData(Request $request)
    {
        $rules = [
                'id_usuario' => 'required|string|min:1|max:100',
            'id_modulo' => 'required|string|min:1|max:50',
            'accion' => 'required|string|min:1|max:200',
            'status' => 'required|string|min:1|max:10',
            'fecha' => 'required|date_format:j/n/Y g:i A',
        ];

        
        $data = $request->validate($rules);




        return $data;
    }

}
