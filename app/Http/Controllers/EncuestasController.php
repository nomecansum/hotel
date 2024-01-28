<?php

namespace App\Http\Controllers;
use App\Models\clientes;
use App\Models\encuestas;
use App\Models\encuestas_resultados;
use App\Models\puestos;
use App\Models\niveles_acceso;
use Illuminate\Http\Request;
use Exception;
use Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;

class EncuestasController extends Controller
{
    public function index()
    {
        $encuestas=DB::table('encuestas')
            ->join('clientes','clientes.id_cliente','encuestas.id_cliente')
            ->join('encuestas_tipos','encuestas_tipos.id_tipo_encuesta','encuestas.id_tipo_encuesta')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('encuestas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('encuestas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('encuestas.id_cliente')
            ->orderby('encuestas.id_encuesta')
            ->get();

        return view('encuestas.index', compact('encuestas'));
    }

    /**
     * Show the form for creating a new plantas.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        
        $encuesta=new encuestas();
        $clientes = clientes::where(function($q){
                if (!isAdmin()) {
                    $q->where('id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('clientes.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->pluck('nom_cliente','id_cliente')
            ->all();
        $tipos = DB::table('encuestas_tipos')->get();
        $encuesta->id_tipo_encuesta=$tipos->first()->id_tipo_encuesta;
        $encuesta->des_tipo_encuesta=$tipos->first()->des_tipo_encuesta;
        $encuesta->img_tipo=$tipos->first()->img_tipo;
        $encuesta->fec_inicio=Carbon::now();
        $encuesta->fec_fin=Carbon::now();
        $encuesta->id_encuesta=0;
        $encuesta->token=Str::random(64);
        $perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->wherein('id_cliente',[Auth::user()->id_cliente,1])->get();
        
        return view('encuestas.edit', compact('encuesta','clientes','tipos','perfiles'));
    }

    /**
     * Store a new plantas in the storage.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function store(Request $r)
    {
        //$data = $this->getData($r);
        
            
            $encuesta=encuestas::create($r->all());
            if(isset($r->perfiles)){
                $encuesta->list_perfiles=implode(",",$r->perfiles);
            }
            if(isset($r->edificio)){
                $encuesta->list_edificios=implode(",",$r->edificio);
            }
            if(isset($r->planta)){
                $encuesta->list_plantas=implode(",",$r->planta);
            }
            if(isset($r->tags)){
                $encuesta->list_tags=implode(",",$r->tags);
            }
            if(isset($r->puesto)){
                $encuesta->list_puestos=implode(",",$r->puesto);
            }
            if(isset($r->tipo)){
                $encuesta->list_tipos=implode(",",$r->tipo);
            }

            $fechas=explode(" - ",$r->fechas);
            $encuesta->fec_inicio=Carbon::parse(adaptar_fecha($fechas[0]));
            $encuesta->fec_fin=Carbon::parse(adaptar_fecha($fechas[1]));
            $encuesta->val_color=$r->val_color;
            $encuesta->val_icono=$r->val_icono;
            $encuesta->val_periodo_minimo=$r->val_periodo_minimo;
            $encuesta->mca_activa=$r->mca_activa??'N';
            $encuesta->mca_anonima=$r->mca_anonima??'N';
            $encuesta->mca_mostrar_comentarios=$r->mca_mostrar_comentarios??'N';
            $encuesta->save();
            savebitacora('Encuesta '.$r->titulo. ' creada',"Encuestas","store","OK");
            return [
                'title' => "Encuestas",
                'message' => 'Encuesta '.$r->titulo. ' creada',
                'url' => url('encuestas')
            ];
            try {} catch (\Throwable $exception) {

            return [
                'title' => "Encuestas",
                'error' => 'ERROR: Ocurrio un error creando la encuesta '.$r->titulo.' '.mensaje_excepcion($exception),
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
        $encuesta=DB::table('encuestas')
            ->join('clientes','clientes.id_cliente','encuestas.id_cliente')
            ->join('encuestas_tipos','encuestas_tipos.id_tipo_encuesta','encuestas.id_tipo_encuesta')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('encuestas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('encuestas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where('id_encuesta',$id)
            ->first();
        $tipos = DB::table('encuestas_tipos')->get();
        $clientes = clientes::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('clientes.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->pluck('nom_cliente','id_cliente')
            ->all();
        $perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->wherein('id_cliente',[Auth::user()->id_cliente,1])->get();

        return view('encuestas.edit', compact('encuesta','tipos','clientes','perfiles'));
    }

    /**
     * Update the specified plantas in the storage.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function update(Request $r)
    {
        //$data = $this->getData($r);
        try {      
            validar_acceso_tabla($r->id_encuesta,"encuestas");
            $encuesta = encuestas::findOrFail($r->id_encuesta);
            $encuesta->update($r->all());
            if(isset($r->perfiles)){
                $encuesta->list_perfiles=implode(",",$r->perfiles);
            } else {
                $encuesta->list_perfiles=null;
            }
            if(isset($r->edificio)){
                $encuesta->list_edificios=implode(",",$r->edificio);
            } else {
                $encuesta->list_edificios=null;
            }
            if(isset($r->planta)){
                $encuesta->list_plantas=implode(",",$r->planta);
            } else {
                $encuesta->list_plantas=null;
            }
            if(isset($r->tags)){
                $encuesta->list_tags=implode(",",$r->tags);
            } else {
                $encuesta->list_tags=null;
            }
            if(isset($r->puesto)){
                $encuesta->list_puestos=implode(",",$r->puesto);
            } else {
                $encuesta->list_puestos=null;
            }
            if(isset($r->tipo)){
                $encuesta->list_tipos=implode(",",$r->tipo);
            } else {
                $encuesta->list_tipos=null;
            }

            $fechas=explode(" - ",$r->fechas);
            $encuesta->fec_inicio=Carbon::parse(adaptar_fecha($fechas[0]));
            $encuesta->fec_fin=Carbon::parse(adaptar_fecha($fechas[1]));
            $encuesta->val_color=$r->val_color;
            $encuesta->val_icono=$r->val_icono;
            $encuesta->val_periodo_minimo=$r->val_periodo_minimo;
            $encuesta->mca_activa=$r->mca_activa??'N';
            $encuesta->mca_anonima=$r->mca_anonima??'N';
            $encuesta->mca_mostrar_comentarios=$r->mca_mostrar_comentarios??'N';
            $encuesta->save();

            savebitacora('Encuesta '.$r->titulo. ' actualizada',"Encuestas","update","OK");
            return [
                'title' => "Encuestas",
                'message' => 'Encuesta '.$r->titulo. ' actualizada',
                'url' => url('encuestas')
            ];
           
        } catch (\Throwable $exception) {

            return [
                'title' => "Encuestas",
                'error' => 'ERROR: Ocurrio un error actualizando la encuesta '.$r->titulo.' '.mensaje_excepcion($exception),
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
    public function delete($id)
    {
        try {
            validar_acceso_tabla($id,"encuestas");
            $encuesta = encuestas::findOrFail($id);
            $encuesta->delete();
            savebitacora('Planta '.$encuesta->des_planta. ' borrada',"Encuestas","destroy","OK");
            return redirect()->route('encuestas.index')
                ->with('success_message', 'Encuesta borrada.');
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
                'titulo' => 'required|string|min:0|max:300',
                'id_cliente' => 'required',
                'id_tipo_encuesta' => 'required'
        ];
        $data = $request->validate($rules);
        return $data;
    }

    public function get_encuesta($token){

        $encuesta=encuestas::where('token',$token)->first();

        return view('encuestas.ver_encuesta',compact('encuesta'));
    }



    public function save_data(Request $r){
        //datos de la encuesta
        $encuesta=encuestas::where('token',$r->id_encuesta)->first();
        if (isset($encuesta)){
            $resultado=new encuestas_resultados();
            $resultado->id_encuesta=$encuesta->id_encuesta;
            $resultado->fecha=Carbon::now();
            $resultado->valor=$r->val;
            $resultado->comentario=$r->comentario??null;
            if(Auth::check() && $encuesta->mca_anonima=='N'){
                $resultado->id_usuario=Auth::user()->id;
            }
            $resultado->save();
        }

        Cookie::queue('encuesta', $encuesta->id_encuesta, $encuesta->val_periodo_minimo);
        return [
            'title' => "Encuestas",
            'message' => 'Dato guardado',
            //'url' => url('encuestas')
        ]; 
    }

    public function resultados(Request $r){
        validar_acceso_tabla($r->id_encuesta,"encuestas");
        $encuesta=DB::table('encuestas')
            ->where('id_encuesta',$r->id_encuesta)
            ->first();

        if(isset($r->fechas)){
            $fechas=explode(" - ",$r->fechas);
            $f1=Carbon::parse(adaptar_fecha($fechas[0]));
            $f2=Carbon::parse(adaptar_fecha($fechas[1]));
        } else {
            $f1=Carbon::now()->startOfMonth();
            $f2=Carbon::now()->endOfMonth();
        }
        
        $resultado=DB::table('encuestas_resultados')
            ->whereBetween('fecha',[$f1,$f2])
            ->where('id_encuesta',$r->id_encuesta)
            ->orderby('fecha')
            ->get();

        $resultado_valor=DB::table('encuestas_resultados')
            ->selectraw('valor as des_estado, count(id_resultado) as cuenta')
            ->whereBetween('fecha',[$f1,$f2])
            ->where('id_encuesta',$r->id_encuesta)
            ->groupby('des_estado')
            ->orderby('valor')
            ->get();

        $resultado_fecha=DB::table('encuestas_resultados')
            ->selectraw('date(fecha) as fecha, count(id_resultado) as cuenta')
            ->whereBetween('fecha',[$f1,$f2])
            ->where('id_encuesta',$r->id_encuesta)
            ->groupBy(\DB::raw('date(fecha)'))
            ->orderby('fecha')
            ->get();

        return view('encuestas.fill_resultados',compact('resultado','encuesta','resultado_valor','resultado_fecha','r','f1','f2'));
    }


    
}
