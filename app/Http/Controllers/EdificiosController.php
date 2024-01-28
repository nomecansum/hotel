<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\clientes;
use App\Models\edificios;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EdificiosController extends Controller
{

    /**
     * Display a listing of the edificios.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $edificiosObjects = DB::table('edificios')
        ->join('clientes','clientes.id_cliente','edificios.id_cliente')
        ->leftjoin('provincias','provincias.id_prov','edificios.id_provincia')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('edificios.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('edificios.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->get();

        return view('edificios.index', compact('edificiosObjects'));
    }

    /**
     * Show the form for creating a new edificios.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        //$Clientes = clientes::pluck('nom_cliente','id_cliente')->all();
        //$Clientes=lista_clientes();
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        $provincias = DB::table('provincias')
            ->select('id_prov', 'nombre', 'nom_pais', 'id_pais', 'regiones.cod_region', 'regiones.nom_region')
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
            ->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_prov', '>', 0)
            ->orderby('nom_pais','desc')
            ->orderby('nom_region')
            ->orderby('nombre')
            ->get();
        
        return view('edificios.create', compact('Clientes','provincias'));
    }

    /**
     * Store a new edificios in the storage.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        try {
            
            $data = $this->getData($request);
            edificios::create($data);
            savebitacora('Edificio '.$request->des_edificio. ' creado',"Edificios","store","OK");
            return [
                'title' => "Edificios",
                'message' => 'Edificio '.$request->des_edificio. ' creado',
                'url' => url('edificios')
            ];
        } catch (\Throwable $exception) {
            return [
                'title' => "Edificios",
                'error' => 'ERROR: Ocurrio un error creando el edificio '.$request->des_edificio.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }
    }

    /**
     * Show the form for editing the specified edificios.
     *
     * @param int $id
     *
     * @return Illuminate\View\View
     */
    public function edit($id)
    {
        validar_acceso_tabla($id,"edificios");
        $edificios = edificios::findOrFail($id);
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        $provincias = DB::table('provincias')
            ->select('id_prov', 'nombre', 'nom_pais', 'id_pais', 'regiones.cod_region', 'regiones.nom_region')
            ->leftjoin("regiones", "provincias.cod_region", "regiones.cod_region")
            ->leftjoin("paises", "paises.id_pais", "provincias.cod_pais")
            ->where('id_prov', '>', 0)
            ->orderby('nom_pais')
            ->orderby('nom_region')
            ->orderby('nombre')
            ->get();
        //$Clientes=lista_clientes();
        return view('edificios.edit', compact('edificios','Clientes','provincias'));
    }

    /**
     * Update the specified edificios in the storage.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        try {
            validar_acceso_tabla($id,"edificios");
            $data = $this->getData($request);
            
            $edificios = edificios::findOrFail($id);
            $edificios->update($data);
            savebitacora('Edificio '.$request->des_edificio. ' actualizado',"Edificios","Update","OK");
            return [
                'title' => "Edificios",
                'message' => 'Edificio '.$request->des_edificio. ' guardado',
                'url' => url('edificios')
            ];
        } catch (\Throwable $exception) {

            return [
                'title' => "Edificios",
                'error' => 'ERROR: Ocurrio un error guardando el edificio '.$request->des_edificio.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }        
    }

    /**
     * Remove the specified edificios from the storage.
     *
     * @param int $id
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        try {
            validar_acceso_tabla($id,"edificios");
            $edificios = edificios::findOrFail($id);
            $edificios->delete();
            savebitacora('Edificio '.$edificios->des_edificio. ' borrado',"Edificios","destroy","OK");

            return redirect()->route('edificios.edificios.index')
                ->with('success_message', 'Edificio borrado');
        } catch (\Throwable $exception) {

            return back()->withInput()
                ->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.']);
        }
    }

    
    /**
     * Get the request's data from the request.
     *
     * @param Illuminate\Http\Request\Request $request 
     * @return array
     */
    protected function getData(Request $request)
    {
        $rules = [
                'des_edificio' => 'nullable|string|min:0|max:200',
                'abreviatura' => 'nullable|string|min:0|max:20',
            'id_cliente' => 'nullable', 
            'id_provincia' => 'integer|min:1', 
        ];
        $data = $request->validate($rules);
        return $data;
    }

}
