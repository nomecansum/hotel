<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\clientes;
use App\Models\ferias;
use App\Models\marcas;
use App\Models\contactos;
use App\Models\config_clientes;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Excel;
use App\Exports\ExportExcel;
use PDF;
use Redirect;

class FeriasController extends Controller
{
    //
    /**
     * Display a listing of the plantas.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $ferias=DB::table('ferias')
            ->join('clientes','clientes.id_cliente','ferias.id_cliente')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('ferias.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('ferias.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('ferias.fec_feria','desc')
            ->get();

        return view('ferias.index', compact('ferias'));
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
        
        return view('ferias.create', compact('Clientes'));
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
        $request['fec_feria']=adaptar_fecha($request->fec_feria);
        $data = $this->getData($request);
        try {
            ferias::create($data);
            savebitacora('Feria '.$request->des_feria. ' creada',"Ferias","store","OK");
            return [
                'title' => "Ferias",
                'message' => 'Feria '.$request->des_feria. ' creada',
                'url' => url('ferias')
            ];
        } catch (\Throwable $exception) {

            return [
                'title' => "Ferias",
                'error' => 'ERROR: Ocurrio un error creando la feria '.$request->des_feria.' '.mensaje_excepcion($exception),
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
        $ferias = ferias::findOrFail($id);
        $Clientes = clientes::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('clientes.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->pluck('nom_cliente','id_cliente')
        ->all();


        return view('ferias.edit', compact('ferias','Clientes'));
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
        $request['fec_feria']=adaptar_fecha($request->fec_feria);
        $data = $this->getData($request);
        try {
            validar_acceso_tabla($id,"ferias");
            
            $ferias = ferias::findOrFail($id);

            $ferias->update($data);
            savebitacora('Feria '.$request->des_feria. ' actualizada',"Ferias","update","OK");
            return [
                'title' => "Ferias",
                'message' => 'Feria '.$request->des_feria. ' actualizada',
                'url' => url('ferias')
            ];
            } catch (\Throwable $exception) {

            return [
                'title' => "Ferias",
                'error' => 'ERROR: Ocurrio un error actualizando la feria '.$request->des_feria.' '.mensaje_excepcion($exception),
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
            'des_feria' => 'nullable|string|min:0|max:50',
            'id_cliente' => 'nullable',
            'fec_feria' => 'date',
        ];
        
        $data = $request->validate($rules);


        return $data;
    }

    /////////////GESTION DE MARCAS  //////////////////

    /**
     * Display a listing of marcas.
     *
     * @return Illuminate\View\View
     */
    public function marcas_index()
    {
        $datos=DB::table('ferias_marcas')
            ->select('ferias_marcas.*','clientes.nom_cliente')
            ->leftjoin('clientes','clientes.id_cliente','ferias_marcas.id_cliente')
            // ->where(function($q){
            //     $q->where('ferias_marcas.id_cliente',Auth::user()->id_cliente);
            // })
            ->orderby('ferias_marcas.des_marca')
            ->get();
        return view('ferias.marcas.index', compact('datos'));
    }

    /**
     * Show the form for editing the specified marca.
     *
     * @param int $id
     *
     * @return Illuminate\View\View
     */
    public function marcas_edit($id)
    {
        if($id!=0){
            $datos = marcas::findOrFail($id);
        } else {
            $datos=new marcas;
        }
        
        $clientes = clientes::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('clientes.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->pluck('nom_cliente','id_cliente')
        ->all();
        return view('ferias.marcas.editor', compact('datos','clientes'));
    }

    public function marcas_save(Request $r)
    {
    try {    
        $img=isset($r->old_logo)?$r->old_logo:"";

        if ($r->hasFile('img_logo')) {
            $file = $r->file('img_logo');
            $path = '/img/ferias/marcas/';
            $img = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
            //$file->move($path,$img_usuario);
            Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img);
        }
        
        $data = $this->getData_marcas($r);
        if($r->id_marca!=0){
            $datos = marcas::findOrFail($r->id_marca);
            $datos->update($data);
        } else {
            $datos=new marcas;
            $datos=marcas::create($data);
        }
        $datos->img_logo=$img;
        $datos->token=Str::random(50);
        $datos->save();
        return [
            'title' => "Marcas",
            'message' => 'Marca '.$datos->des_marca. ' actualizada',
            'url' => url('/ferias/marcas')
        ];
        
        } catch (\Throwable $exception) {

            return [
                'title' => "Ferias",
                'error' => 'ERROR: Ocurrio un error actualizando la marca '.$r->des_marca.' '.mensaje_excepcion($exception),
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
    public function marcas_delete($id)
    {
        try {
            validar_acceso_tabla($id,"ferias_marcas");
            $dato = marcas::findOrFail($id);
            $dato->delete();
            savebitacora('Marca '.$dato->des_marca. ' borrada',"Ferias","marcas_delete","OK");
            return redirect()->route('marcas.index')
                ->with('success_message', 'Marca borrada.');
        } catch (\Throwable $exception) {

            return back()->withInput()
                ->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.'.mensaje_excepcion($exception)]);
        }
    }

    protected function getData_marcas(Request $request)
    {
        $rules = [
            'des_marca' => 'nullable|string|min:0|max:500',
            'id_cliente' => 'nullable',
            'img_logo' => 'nullable',
            'observaciones' => 'nullable|string|min:0|max:500',
            'url' => 'nullable',
        ];
        $data = $request->validate($rules);
        return $data;
    }

    public function print_qr_marcas(Request $r){
        if(!isset($r->tam_qr)){
            $r->request->add(['tam_qr' => session('CL')['tam_qr']]); //add request
        }
        $layout="layout";
        
        $datos=DB::table('ferias_marcas')
            ->select('ferias_marcas.*','clientes.nom_cliente')
            ->leftjoin('clientes','clientes.id_cliente','ferias_marcas.id_cliente')
            // ->where(function($q){
            //     $q->where('ferias_marcas.id_cliente',Auth::user()->id_cliente);
            // })
            ->where(function($q) use($r){
                $q->wherein('id_marca',$r->lista_id);
            })
            ->orderby('ferias_marcas.des_marca')
            ->get();

        if($r->formato && $r->formato=='PDF'){
            $layout="layout_simple";
            $filename='Codigos_QR Marcas_'.Auth::user()->id_cliente.'_.pdf';
            $pdf = PDF::loadView('ferias.marcas.print_qr',compact('datos','r','layout'));
            return $pdf->download($filename);
        } else {
            return view('ferias.marcas.print_qr',compact('datos','r','layout'));
        }
        try{    
        } catch(\Exception $e){
            return Redirect::back();
        }
    }

    public function export_qr_marcas(Request $r){
        try{
            $datos=DB::table('ferias_marcas')
                ->select('ferias_marcas.*','clientes.nom_cliente')
                ->leftjoin('clientes','clientes.id_cliente','ferias_marcas.id_cliente')
                // ->where(function($q){
                //     $q->where('ferias_marcas.id_cliente',Auth::user()->id_cliente);
                // })
                ->where(function($q) use($r){
                    $q->wherein('id_marca',$r->lista_id);
                })
                ->orderby('ferias_marcas.des_marca')
                ->get();
        
            $filename='Codigos_QR Marcas_'.Auth::user()->id_cliente.'_.xlsx';
            libxml_use_internal_errors(true); //añadido por andriy para quitar los errores de libreria
            //return view('puestos.qr_excel',compact('puestos','r'));
            return Excel::download(new ExportExcel('ferias.marcas.qr_excel',compact('datos','r')),$filename);
            } catch(\Exception $e){
            return Redirect::back();
        }
    }
    
    /////////////GESTION DE ASISTENTES  //////////////////

    /**
     * Display a listing of marcas.
     *
     * @return Illuminate\View\View
     */
    public function asistentes_index()
    {
        $feria_toca=ferias::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('ferias.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->where('fec_feria','>',Carbon::now()->subday(30))->first();

        
        $datos=DB::table('contactos')
            ->select('contactos.*','clientes.nom_cliente','users.name','ferias.des_feria')
            ->leftjoin('clientes','clientes.id_cliente','contactos.id_cliente')
            ->leftjoin('users','contactos.id_usuario','users.id')
            ->join('ferias','ferias.id_feria','contactos.id_feria')
            ->where(function($q) use($feria_toca){
                if(isset($feria_toca)){
                    $q->where('ferias.id_feria',$feria_toca->id_feria);
                }
            })
            ->orderby('contactos.nombre')
            ->get();
        return view('ferias.asistentes.index', compact('datos'));
    }

    public function asistentes_search(Request $r)
    {
        $datos=DB::table('contactos')
            ->select('contactos.*','clientes.nom_cliente','users.name','ferias.des_feria')
            ->leftjoin('clientes','clientes.id_cliente','contactos.id_cliente')
            ->leftjoin('users','contactos.id_usuario','users.id')
            ->join('ferias','ferias.id_feria','contactos.id_feria')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('ferias.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q) use($r){
                if(isset($r->cliente)){
                    $q->wherein('ferias.id_cliente',$r->cliente);
                }
            })
            ->where(function($q) use($r){
                if(isset($r->tipoferia)){
                    $q->wherein('ferias.id_feria',$r->tipoferia);
                }
            })
            ->orderby('contactos.nombre')
            ->get();

        return view('ferias.asistentes.index', compact('datos','r'));
    }

    /**
     * Show the form for editing the specified marca.
     *
     * @param int $id
     *
     * @return Illuminate\View\View
     */
    public function  asistentes_edit($id)
    {
        if($id!=0){
            $datos = contactos::findOrFail($id);
        } else {
            $feria_toca=ferias::where(function($q){
                if (!isAdmin()) {
                    $q->where('id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('ferias.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where('fec_feria','>',Carbon::now()->subday(30))->first();
            $datos=new contactos;
            $datos->id_feria=$feria_toca->id_feria;
        }
        
        $clientes = clientes::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('clientes.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->pluck('nom_cliente','id_cliente')
        ->all();

        $ferias=ferias::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('ferias.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->get();

        return view('ferias.asistentes.editor', compact('datos','clientes','ferias'));
    }

    public function  asistentes_save(Request $r)
    {
        try {    
            
            $data = $this->getData_asistentes($r);
            if($r->id_contacto!=0){
                $datos = contactos::findOrFail($r->id_contacto);
                $datos->update($data);
            } else {
                $datos=new contactos;
                $datos=contactos::create($data);
                $datos->token=Str::random(50);
                $datos->mca_acepto='S';
                $datos->mca_enviar='S';
                $datos->save();
                
            }
            
            return [
                'title' => "Contactos",
                'message' => 'Contacto '.$datos->nombre. ' actualizado',
                'url' => url('/ferias/asistentes')
            ];
            
            } catch (\Throwable $exception) {

                return [
                    'title' => "Ferias",
                    'error' => 'ERROR: Ocurrio un error actualizando la contacto '.$r->nombre.' '.mensaje_excepcion($exception),
                ];
            }
    }

    public function asistentes_delete($id)
    {
        try {
            validar_acceso_tabla($id,"contactos");
            $dato = contactos::findOrFail($id);
            $dato->delete();
            savebitacora('Contacto '.$dato->nombre. ' borrado',"Ferias","asistentes_delete","OK");
            return redirect()->route('asistentes.index')
                ->with('success_message', 'Contacto borrado.');
        } catch (\Throwable $exception) {

            return back()->withInput()
                ->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.'.mensaje_excepcion($exception)]);
        }
    }

    protected function getData_asistentes(Request $request)
    {
        $rules = [
            'nombre' => 'string|min:0|max:500',
            'empresa' => 'string|min:0|max:500',
            'email' => 'email:rfc|min:0|max:500',
            'mensaje' => 'nullable|string',
            'id_feria' => 'nullable|integer',
            'id_cliente' => 'integer',
        ];
        $data = $request->validate($rules);
        return $data;
    }

    public function print_qr_asistentes(Request $r){
        try{       
            $layout="layout";

            if (!isset($r->lista_id)){
                return Redirect::back();
            }
            if(!is_array($r->lista_id)){
                $r->lista_id=explode(",",$r->lista_id);
            }


            $config_print=config_clientes::where('id_cliente',$r->id_cliente??Auth::user()->id_cliente)->first()->config_print_qr_ferias;
            if (isset($config_print) && isJson($config_print)){
                $config_print=json_decode($config_print,true);
                $r->request->add(['tam_qr' => $config_print['tam_qr']]); //add request
                $r->request->add(['tam_h_ficha' => $config_print['tam_h_ficha']]); //add request
                $r->request->add(['tam_w_ficha' => $config_print['tam_w_ficha']]); //add request
                $r->request->add(['border' => $config_print['border']]); //add request
                $r->request->add(['col' => $config_print['col']]); //add request
                $r->request->add(['row' => $config_print['row']??4]); //add request
                $r->request->add(['sel_color' => $config_print['sel_color']]); //add request
                $r->request->add(['sel_color_txt' => $config_print['sel_color_txt']]); //add request
                $r->request->add(['footer' => $config_print['footer']??'']); //add request
                $r->request->add(['header' => $config_print['header']??'']); //add request
                $r->request->add(['margen_left' => $config_print['margen_left']]); //add request
                $r->request->add(['margen_top' => $config_print['margen_top']]); //add request
                $r->request->add(['espacio_h' => $config_print['espacio_h']]); //add request
                $r->request->add(['espacio_v' => $config_print['espacio_v']]); //add request
                $r->request->add(['padding_qr' => $config_print['padding_qr']]); //add request
                $r->request->add(['padding_cont' => $config_print['padding_cont']]); //add request
                $r->request->add(['font_size' => $config_print['font_size']]); //add request
                $r->request->add(['font_size_resto' => $config_print['font_size_resto']]); //add request
                $r->request->add(['page_break' => $config_print['page_break']]); //add request
            }
        
            $datos=DB::table('contactos')
                ->select('contactos.*','clientes.nom_cliente','users.name')
                ->leftjoin('clientes','clientes.id_cliente','contactos.id_cliente')
                ->leftjoin('users','contactos.id_usuario','users.id')
                ->where(function($q) use($r){
                    if(isset($r->lista_id)){
                        $q->wherein('id_contacto',$r->lista_id);
                    }
                })
                ->orderby('contactos.nombre')
                ->get();

            if($r->formato && $r->formato=='PDF'){
                $layout="layout_simple";
                $filename='Codigos_QR Ferias_'.Auth::user()->id_cliente.'_.pdf';
                $pdf = PDF::loadView('ferias.asistentes.print_qr',compact('datos','r','layout'));
                return $pdf->download($filename);
            } else if($r->formato && $r->formato=='preview'){
                return view('ferias.asistentes.fill_printarea',compact('datos','r','layout'));
            } else {
                return view('ferias.asistentes.print_qr',compact('datos','r','layout'));
            }
       
        } catch(\Exception $e){
            return Redirect::back();
        }
    }

    public function export_qr_asistentes(Request $r){
        try{
            $datos=DB::table('contactos')
                ->select('contactos.*','clientes.nom_cliente','users.name')
                ->leftjoin('clientes','clientes.id_cliente','contactos.id_cliente')
                ->leftjoin('users','contactos.id_usuario','users.id')
                // ->where(function($q){
                //     $q->where('contactos.id_cliente',Auth::user()->id_cliente);
                // })
                ->where(function($q) use($r){
                    if(isset($r->lista_id)){
                        $q->wherein('id_contacto',$r->lista_id);
                    }
                })
                ->orderby('contactos.nombre')
                ->get();
        
            $filename='Codigos_QR Marcas_'.Auth::user()->id_cliente.'_.xlsx';
            libxml_use_internal_errors(true); //añadido por andriy para quitar los errores de libreria
            //return view('puestos.qr_excel',compact('puestos','r'));
            return Excel::download(new ExportExcel('ferias.asistentes.qr_excel',compact('datos','r')),$filename);
            } catch(\Exception $e){
            return Redirect::back();
        }
    }

    public function save_config_print(Request $r){
        $config=config_clientes::find($r->id_cliente);
        $config->config_print_qr_ferias=json_encode($r->all());
        $config->save();
        return [
            'title' => "save_config_print",
            'message' => 'OK',
        ];
    }

    public function subir_imagen(Request $r){
        $data['header']=null;
        $data['footer']=null;

        if ($r->hasFile('header')) {
            $file = $r->file('header');
            $path = config('app.ruta_public').'/img/ferias/';
            $img_final = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
            //$file->move($path,$img_usuario);
            Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_final);
            $data['header']=$img_final;
        }

        if ($r->hasFile('footer')) {
            $file = $r->file('footer');
            $path = config('app.ruta_public').'/img/ferias/';
            $img_final = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
            //$file->move($path,$img_usuario);
            Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_final);
            $data['footer']=$img_final;
        }

        return [
            'title' => "subir_imagen",
            'message' => 'OK',
            'header' => $data['header'],
            'footer' => $data['footer']
        ];
    }
}
