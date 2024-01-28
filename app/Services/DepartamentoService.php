<?php
namespace App\services;
use DB;
use Validator;
use App\User;
use App\Models\centros;
use App\Models\departamentos;

class DepartamentoService
{
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function validar_request($r,$metodo_notif){
        $validator = Validator::make($r->all(), [
            'nom_departamento' => 'required',
            'cod_cliente' => 'required',
            'cod_centro' => 'nullable',
        ],
        [
            'nom_departamento.required' => 'El campo NOMBRE es obligatorio',
            'cod_cliente.required' => 'El campo CLIENTE es obligatorio',
        ]); 
        if($validator->fails()) {
            
            $mensaje_error="ERROR: Ocurrio un error al validar los datos de departamento ".$r->nom_departamento."<br>".implode("<br>",$validator->messages()->all());
            //dd($mensaje_error);
            switch($metodo_notif){
                case "flash":
                    flash($mensaje_error)->error();  
                    return redirect()->back()->withInput();
                    break;
                
                case "toast":
                //dd($mensaje_error);
                return response()->json(['title' => "Departamentos",
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

    public function subir_imagen($r){
        $img_logo = "";
        if ($r->hasFile('img_logo')) {
            $file = $r->file('img_logo');
            $path = public_path().'/uploads/departments/images';
            $img_logo = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
            $file->move($path,$img_logo);
        }
        return $img_logo;
    }

    public function insertar($r){
        $d=new departamentos;
        $d->nom_departamento=$r->nom_departamento;
        $d->id_cliente=$r->cod_cliente;
        $d->cod_departamento_padre=$r->cod_departamento_padre ? $r->cod_departamento_padre : 0;
        $d->num_nivel=$r->cod_departamento_padre ? ($r->cod_departamento_padre == -1 ? 1 : \DB::table('departamentos')->where('cod_departamento',$r->cod_departamento_padre)->first()->num_nivel+1) : 1;
        $d->save();
        return $d->cod_departamento;
    }

    public function actualizar($r){
        $d=departamentos::findorfail($r->id);
        $d->nom_departamento=$r->nom_departamento;
        $d->id_cliente=$r->cod_cliente;
        $d->cod_departamento_padre=$r->cod_departamento_padre ? $r->cod_departamento_padre : 0;
        $d->num_nivel=$r->cod_departamento_padre ? ($r->cod_departamento_padre == -1 ? 1 : \DB::table('departamentos')->where('cod_departamento',$r->cod_departamento_padre)->first()->num_nivel+1) : 1;
        $d->save();
        return $d->cod_departamento;
    }

    public function delete($id){
        DB::table('departamentos')->where('cod_departamento',$id)->delete();
		return true;
    }

}