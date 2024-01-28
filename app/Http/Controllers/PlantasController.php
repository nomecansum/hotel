<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\clientes;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\puestos;
use App\Models\plantas_zonas;

use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\File;

class PlantasController extends Controller
{

    /**
     * Display a listing of the plantas.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $plantasObjects=DB::table('plantas')
            ->select('plantas.id_planta','plantas.des_planta','plantas.img_plano','edificios.des_edificio','clientes.nom_cliente','clientes.id_cliente')
            ->join('clientes','clientes.id_cliente','plantas.id_cliente')
            ->join('edificios','edificios.id_edificio','plantas.id_edificio')
            ->where(function($q){
                $q->where('edificios.id_cliente',Auth::user()->id_cliente);
            })
            ->orderby('plantas.id_cliente')
            ->orderby('plantas.id_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.id_planta')
            ->get();
       
        $puestos=DB::table('puestos')
            ->select('id_planta')
            ->selectraw("ifnull(count(puestos.id_puesto),0) as cnt_puestos")
            ->wherein('id_planta',$plantasObjects->pluck('id_planta')->toArray())
            ->groupby(['id_planta'])
            ->get();
        
        $zonas=DB::table('plantas_zonas')
            ->select('id_planta')
            ->selectraw("ifnull(count(plantas_zonas.key_id),0) as cnt_zonas")
            ->wherein('id_planta',$plantasObjects->pluck('id_planta')->toArray())
            ->groupby(['id_planta'])
            ->get();
            
        return view('plantas.index', compact('plantasObjects','puestos','zonas'));
    }

    /**
     * Show the form for creating a new plantas.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        $Clientes = clientes::where(function($q){
                if (!isAdmin()) {
                    $q->where('id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('clientes.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->pluck('nom_cliente','id_cliente')
            ->all();
        $Edificios = edificios::where(function($q){
                $q->where('id_cliente',Auth::user()->id_cliente);
            })
            ->pluck('des_edificio','id_edificio')
            ->all();
        
        return view('plantas.create', compact('Clientes','Edificios'));
    }

    /**
     * Store a new plantas in the storage.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $img_planta="";
        $data = $this->getData($request);
        try {
            if ($request->hasFile('img_plano')) {
                $file = $request->file('img_plano');
                $path = config('app.ruta_public').'/img/plantas/';
                $img_planta = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_planta);
                //$file->move($path,$img_planta);
                $data['img_plano']=$img_planta;
            }
            $pl=plantas::create($data);
            $pl->mca_publica=isset($request->mca_publica)?"S":"N";
            $pl->save();
            savebitacora('Planta '.$request->des_planta. ' creada',"Plantas","store","OK");
            return [
                'title' => "Plantas",
                'message' => 'Planta '.$request->des_planta. ' creada',
                'url' => url('plantas')
            ];
        } catch (\Throwable $exception) {

            return [
                'title' => "Plantas",
                'error' => 'ERROR: Ocurrio un error creando la planta '.$request->des_planta.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }
    }

    /**
     * Show the form for editing the specified plantas.
     *
     * @param int $id
     *
     * @return Illuminate\View\View
     */
    public function edit($id)
    {
        $plantas = plantas::findOrFail($id);
        $Clientes = clientes::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('clientes.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->pluck('nom_cliente','id_cliente')
        ->all();
        $Edificios = edificios::where(function($q){
            $q->where('id_cliente',Auth::user()->id_cliente);
        })
        ->pluck('des_edificio','id_edificio')
        ->all();

        return view('plantas.edit', compact('plantas','Clientes','Edificios'));
    }

    /**
     * Update the specified plantas in the storage.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        $img_planta = "";
        $data = $this->getData($request);
        try {
            validar_acceso_tabla($id,"plantas");
            if ($request->hasFile('img_plano')) {
                $file = $request->file('img_plano');
                $path = config('app.ruta_public').'/img/plantas/';
                $img_planta = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_planta);
                $data['img_plano']=$img_planta;
            } 
            $plantas = plantas::findOrFail($id);
            $plantas->update($data);

            $plantas->mca_publica=isset($request->mca_publica)?"S":"N";
            $plantas->save();
            savebitacora('Planta '.$request->des_planta. ' actualizada',"Plantas","update","OK");
            return [
                'title' => "Plantas",
                'message' => 'Planta '.$request->des_planta. ' actualizada',
                'url' => url('plantas')
            ];
        } catch (\Throwable $exception) {

            return [
                'title' => "Plantas",
                'error' => 'ERROR: Ocurrio un error actualizando la planta '.$request->des_planta.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }        
    }

    /**
     * Remove the specified plantas from the storage.
     *
     * @param int $id
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        try {
            validar_acceso_tabla($id,"plantas");
            $plantas = plantas::findOrFail($id);
            $plantas->delete();
            savebitacora('Planta '.$plantas->des_planta. ' borrada',"Plantas","destroy","OK");
            return redirect()->route('plantas.plantas.index')
                ->with('success_message', 'Planta borrada.');
        } catch (\Throwable $exception) {

            return back()->withInput()
                ->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.'.mensaje_excepcion($exception)]);
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
                'des_planta' => 'nullable|string|min:0|max:50',
                'abreviatura' => 'nullable|string|min:0|max:20',
                'id_cliente' => 'nullable',
                'id_edificio' => 'nullable', 
                'num_orden' => 'required', 
        ];
        
        $data = $request->validate($rules);


        return $data;
    }

    ///////////////POSICIONES DE PUESTOS EN LAS PLANTAS
    public function puestos($id)
    {
        validar_acceso_tabla($id,'plantas');
        $plantas = plantas::findOrFail($id);
        $puestos= DB::Table('puestos')
            ->select('puestos.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','plantas.factor_puestow','plantas.factor_puestoh','plantas.factor_puestob','plantas.factor_puestor','plantas.factor_letra','puestos.val_color as hex_color')
            ->join('plantas','plantas.id_planta','puestos.id_planta')
            ->leftjoin('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->where('puestos.id_planta',$id)
            ->where('puestos.id_puesto','>',0)
            ->get();
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->where(function($q){
                $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
            })
            ->get();

        return view('plantas.editor_puestos', compact('plantas','puestos','reservas'));
    }

    public function puestos_custom(Request $r){
        $lista_puestos=puestos::wherein('id_puesto',$r->id)->get();
        foreach($lista_puestos as $puesto){
            if($r->has('width')){
                $puesto->width=$r->width;
            }
            if($r->has('height')){
                $puesto->height=$r->height;
            }
            if($r->has('border')){
                $puesto->border=$r->border;
            }
            if($r->has('roundness')){
                $puesto->roundness=$r->roundness;
            }
            if($r->has('border')){
                $puesto->border=$r->border;
            }
            if($r->has('font')){
                $puesto->font=$r->font;
            }
            $puesto->save();
        }
        return [
            'title' => "Plantas",
            'message' => 'Propiedades custom de los puestos ['.implode(",",$r->id).'] actualizadas',
            'result' => 'ok'
        ];
    }

    public function borrar_custom(Request $r){
        $lista_puestos=puestos::wherein('id_puesto',$r->id)->update([
            'width'=>null,
            'height'=>null,
            'border'=>null,
            'roundness'=>null,
            'border'=>null,
        ]);
       
        return [
            'title' => "Plantas",
            'message' => 'Propiedades custom de los puestos ['.implode(",",$r->id).'] borradas',
            'result' => 'ok'
        ];

    }

    public function puestos_save(Request $r){
        validar_acceso_tabla($r->id_planta,'plantas');
        $planta = plantas::findOrFail($r->id_planta);
        $planta->posiciones=$r->json;
        $planta->factor_puestow=$r->factor_puestow;
        $planta->factor_puestoh=$r->factor_puestoh;
        $planta->factor_puestor=$r->factor_puestor;
        $planta->factor_puestob=$r->factor_puestob;
        $planta->factor_grid=$r->factor_grid;
        $planta->factor_letra=$r->factor_letra;
        $planta->factor_transp=$r->factor_transp;
        $planta->save();
        savebitacora('Distribucion de puestos en  '.$planta->des_planta. ' actualizada',"Plantas","puestos_save","OK");
        return [
            'title' => "Plantas",
            'message' => 'Distribucion de puestos en  '.$planta->des_planta. ' actualizada',
            'url' => url('plantas')
        ];
    }

    /////////////////////////ZONAS DE LAS PLANTAS /////////////////////////////////
    public function zonas($id)
    {
        validar_acceso_tabla($id,'plantas');
        $plantas = plantas::findOrFail($id);
        //Nos traemos la imagen de fondo para ver las dimensiones
        try{
            $tempName = tempnam(sys_get_temp_dir(), 'response');
            Storage::disk('local')->put(
                '/temp/'.$plantas->img_plano, 
                Storage::disk(config('app.img_disk'))->get('img/plantas/'.$plantas->img_plano));
        } catch(\Throwable $e){
            $error="Debe añadir una imagen de fondo para poder agregar zonas";
            return view('plantas.zonas', compact('plantas','error'));
        }
        $puestos= DB::Table('puestos')
            ->select('puestos.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','plantas.factor_puestow','plantas.factor_puestoh','plantas.factor_puestob','plantas.factor_puestor','plantas.factor_letra','puestos.val_color as hex_color')
            ->join('plantas','plantas.id_planta','puestos.id_planta')
            ->leftjoin('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->where('puestos.id_planta',$id)
            ->get();
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->where(function($q){
                $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
            })
            ->get();

        return view('plantas.zonas', compact('plantas','puestos','reservas'));
    }

    public function zonas_detalle($id){
        $puestos_zona=DB::table('puestos')
            ->select('id_puesto','cod_puesto')
            ->join('plantas_zonas','plantas_zonas.id_planta','puestos.id_planta')
            ->join('plantas','plantas.id_planta','plantas_zonas.id_planta')
            ->where('plantas_zonas.num_zona',$id)
            ->where(function($q){  //Este rollo viene de que las posiciones de las zonas estan guardadas como valores absolutos sobre los pixeles reales de la imagen y las posiciones de los puestos estan guardadas como valores de porcentaje (offsettop y offsetleft) de las dimensiones de la imagen
                $q->whereraw('puestos.offset_top between (100*plantas_zonas.val_y/plantas.`height`) AND ((100*plantas_zonas.val_y/plantas.`height`) + (100*plantas_zonas.val_alto/plantas.`height`))');
                $q->whereraw('puestos.offset_left between (100*plantas_zonas.val_x/plantas.`width`) AND ((100*plantas_zonas.val_x/plantas.`width`) + (100*plantas_zonas.val_ancho/plantas.`width`))');
            })
            ->get();
        return json_encode($puestos_zona);
    }

    public function save_zonas(Request $r){
        validar_acceso_tabla($r->id_planta,'plantas');
        $planta = plantas::findOrFail($r->id_planta);

        $planta->zonas=$r->json_zonas;
        $planta->width=$r->width;
        $planta->height=$r->height;
        $planta->factor_zoom=$r->factor_zoom;
        $planta->save();

        $zonas=json_decode($planta->zonas,true);
        plantas_zonas::where('id_planta',$planta->id_planta)->delete();
        foreach($zonas as $z){
            $zona=new plantas_zonas;
            $zona->id_planta=$planta->id_planta;
            $zona->num_zona=$z['id'];
            $zona->des_zona=$z['text'];
            $zona->val_ancho=round($r->width*$z['w']/100);
            $zona->val_alto=round($r->height*$z['h']/100);
            $zona->val_x=round($r->width*$z['x']/100);
            $zona->val_y=round($r->height*$z['y']/100);
            $zona->save();
        }

        savebitacora('Acualizadas zonas en  '.$planta->des_planta. ' actualizada',"Plantas","save_zonas","OK");
        return [
            'title' => "Plantas",
            'message' => 'Distribucion de zonas en  '.$planta->des_planta. ' actualizada',
            'url' => url('plantas')
        ];
    }
}
