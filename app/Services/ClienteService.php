<?php
namespace App\services;
use DB;
use Validator;
use App\User;
use App\Models\clientes;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ClienteService
{
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function validar_request($r,$metodo_notif){
        $validator = Validator::make($r->all(), [
            'nom_cliente' => 'required|string|max:500',
            //'cod_sistema' => 'required|int|unique:cug_sistema,cod_sistema,id_cliente,'.$r->id_cliente,
        ],
        [
            'nom_cliente.required' => 'El campo NOMBRE es obligatorio',
        ]); 
        if($validator->fails()) {
            $mensaje_error="ERROR: Ocurrio un error al validar los datos de cliente ".$r->nom_cliente."<br>".implode("<br>",$validator->messages()->all());

            switch($metodo_notif){
                case "flash":
                    flash($mensaje_error)->error();  
                    return redirect()->back()->withInput();
                    break;
                
                case "toast":
                return response()->json(['title' => "Clientes",
                        'error' => $mensaje_error,
                    ],200)->throwResponse();  
                    break;
                
                case "texto":
                    return $mensaje_error;
                    break;
                
                case "json":
                    $mensaje_error=str_replace("<br>"," ",$mensaje_error);
                    return response()->json([
                        "response" => "ERROR",
                        "message" => "Error de validacion de datos ". $mensaje_error,
                        "TS" => Carbon::now()->format('Y-m-d h:i:s')
                        ],400)->throwResponse();  
                    break;
                    
                default:
                    return redirect()->to($this->getRedirectUrl())
                    ->withInput($r->input())
                    ->withErrors($mensaje_error, $this->errorBag());
                    break;
            }
        }  else{
            return true;
        }     
    }

    public function subir_imagen($r,$campo){
        $img_logo='';
        if ($r->hasFile($campo)) {
            $file = $r->file($campo);
            $path = config('app.ruta_public').'/img/clientes/images/';
            $img_logo = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
            Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_logo);
            //$file->move($path,$img_logo);
        }
        return $img_logo;
    }

    public function insertar($r){
        $cl=new clientes;
        if ($r->hasFile('img_logo')) {
            $cl->img_logo=$this->subir_imagen($r,'img_logo');
        }
        if ($r->hasFile('img_logo_menu')) {
            $cl->img_logo_menu=$this->subir_imagen($r,'img_logo_menu');
        }
        $cl->nom_cliente = $r->nom_cliente;
        $cl->nom_contacto = $r->nom_contacto;
        $cl->val_apikey = $r->val_apikey;
        $cl->token_1uso = $r->token_1uso;
        $cl->token_acceso_salas = $r->token_acceso_salas;
        $cl->mca_appmovil = isset($r->mca_appmovil) ? $r->mca_appmovil : 'N';
        $cl->tel_cliente = $r->tel_cliente;
        $cl->cif = isset($r->cif) ? $r->cif : '';
        $cl->save();
        return $cl->id_cliente;
    }

    public function actualizar($r){
        $cl=clientes::find($r->id);
        if ($r->hasFile('img_logo')) {
            $cl->img_logo=$this->subir_imagen($r,'img_logo');
        }
        if ($r->hasFile('img_logo_menu')) {
            $cl->img_logo_menu=$this->subir_imagen($r,'img_logo_menu');
        }
        $cl->nom_cliente = $r->nom_cliente;
        $cl->nom_contacto = $r->nom_contacto;
        $cl->val_apikey = $r->val_apikey;
        $cl->token_1uso = $r->token_1uso;
        $cl->token_acceso_salas = $r->token_acceso_salas;
        $cl->mca_appmovil = isset($r->mca_appmovil) ? $r->mca_appmovil : 'N';
        $cl->tel_cliente = $r->tel_cliente;
        $cl->cif = isset($r->cif) ? $r->cif : '';
        $cl->save();
        return $cl->id_cliente;
    }

    public function delete($id){

        //Quitamos el cliente de la lista de acceso de los usuarios
        $clientes = clientes::find($id);
        $app = $clientes->mca_appmovil;
        $clientes->fec_borrado = Carbon::now();
        $clientes->mca_appmovil = "N";
        $clientes->save();
        return true;
    }

    public function add_a_supracliente($id,$r){
        //damos permisos para este cliente a todos los usuarios del supracliente
        DB::table('users')->where('id_cliente',$r->cod_supracliente)->update([
            'clientes' => DB::raw('CONCAT(clientes,\','.$id.',\')')
            ]);
    }

    public function add_a_usuario($id,$usuario){
        $u = User::find($usuario);
        $clientes = explode(',',$u->clientes);
        $aux = [];
        foreach ($clientes as $key => $value) {
            if ($value != "") {
                $aux[] = $value;
            }
        }
        $aux[] = $id;
        $u->clientes = ','.implode(',',$aux).',';
        $u->save();
        return true;
    }
}
