<?php
namespace App\services;
use DB;
use Validator;
use App\User;
use App\Models\colectivos;
use Carbon\Carbon;
use Log;

class ColectivoService
{
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function validar_request($r,$metodo_notif){
        $validator = Validator::make($r->all(), [
            'des_colectivo' => 'required',
            'id_cliente' => 'required|exists:clientes,id_cliente',
        ],
        [
            'des_colectivo.required' => 'El campo DES_FESTIVO es obligatorio',
            'id_cliente.required' => 'El campo COD_CENTRO es obligatorio',
        ]);
        if($validator->fails()) {
            $mensaje_error="ERROR: Ocurrio un error al validar los datos de colectivo ".$r->nom_departamento."<br>".implode("<br>",$validator->messages()->all());
            switch($metodo_notif){
                case "flash":
                    flash($mensaje_error)->error();
                    return redirect()->back()->withInput();
                    break;

                case "toast":
                return response()->json(['title' => "Colectivos",
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


    public function insertar($r){
        try{
            if(!isset($r->mca_noinformes)){
                $r->mca_noinformes='N';
            }

            $col=new colectivos();
            $col->des_colectivo=$r->des_colectivo;
            $col->mca_noinformes=$r->mca_noinformes;
            $col->id_cliente=$r->id_cliente;
            $col->save();

            savebitacora("Colectivo ".$r->des_colectivo. " creado con exito para el cliente ".$r->id_cliente ,"Colectivos","insertar","OK");
            return [
                'result' => true,
                'mensaje' => "Colectivo ".$r->des_colectivo. " creado con éxito.",
                'id'=>$col->cod_colectivo
            ];
        } catch(\Exception $e){
            savebitacora("Ocurrio un error procesando el colectivo ".mensaje_excepcion($e).$r->id_cliente ,"Colectivos","insertar","ERROR");
            return [
                'result' => false,
                'mensaje' => "Ocurrio un error procesando el colectivo ".mensaje_excepcion($e)
            ];
        }

    }

    public function actualizar($r, $id){
        try{
            if(empty($r->mca_noinformes)){
                $r->mca_noinformes='N';
            }
            $col=colectivos::find($id);
            $col->des_colectivo=$r->des_colectivo;
            $col->mca_noinformes=$r->mca_noinformes;
            $col->id_cliente=$r->id_cliente;
            $col->save();
            savebitacora("Colectivo ".$r->des_colectivo. " actualizado con exito para el cliente ".$r->id_cliente ,"Colectivos","actualizar","OK");
            return [
                'result' => true,
                'mensaje' => "Colectivo ".$r->des_colectivo. " actualizado con exito para el cliente ".$r->id_cliente,
                'id'=>$col->cod_colectivo
            ];
        } catch(\Exception $e){
            savebitacora("Ocurrió un error procesando el colectivo ".mensaje_excepcion($e) ,"Colectivos","actualizar","ERROR");
            return [
                'result' => false,
                'mensaje' => "Ocurrió un error procesando el colectivo ".mensaje_excepcion($e)
            ];
        }
    }

    public function delete($id){
        try{
            $col=colectivos::find($id);
            DB::table('colectivos_usuarios')->where('cod_colectivo',$id)->delete();
            DB::table('colectivos')->where('cod_colectivo',$id)->delete();
            savebitacora("Colectivo ".$col->des_colectivo. " borrado" ,"Colectivos","delete","OK");
            return [
                'result' => true,
                'mensaje' => "Colectivo ".$col->des_colectivo. " borrado",
            ];
        } catch(\Exception $e){
            savebitacora("Ocurrio un error borrando el colectivo ".mensaje_excepcion($e) ,"Colectivos","delete","ERROR");
            return [
                'result' => false,
                'mensaje' => "Ocurrio un error borrando el colectivo ".mensaje_excepcion($e)
            ];
        }
    }

    public function limpiar_empleados($id){
        try{
            $col=colectivos::find($id);
            DB::table('colectivos_usuarios')->where('cod_colectivo',$id)->delete();
            savebitacora("Usuarios del colectivo ".$col->des_colectivo. " borrados" ,"Colectivos","limpiar_empleados","OK");
            return [
                'result' => true,
                'mensaje' => "Usuarios del colectivo ".$col->des_colectivo. " borrados",
            ];
        } catch(\Exception $e){
            savebitacora("Ocurrio un error borrando usuarios del colectivo ".mensaje_excepcion($e) ,"Colectivos","limpiar_empleados","ERRROR");
            return [
                'result' => false,
                'mensaje' => "Ocurrio un error borrando usuarios del colectivo ".mensaje_excepcion($e)
            ];
        }
    }

    public function add_empleados($id,$emp){
        try{
            $col=colectivos::find($id);
            foreach($emp as $e){
                DB::table('colectivos_usuarios')->insert([
                    'cod_colectivo'=>$id,
                    'cod_empleado'=> $e
                    ]);
            }
            savebitacora("Añadidos ".sizeof($emp)." usuarios al colectivo ".$col->des_colectivo ,"Colectivos","add_empleados","OK");
            return [
                'result' => true,
                'mensaje' => "Añadidos ".sizeof($emp)." usuarios al colectivo ".$col->des_colectivo ,
            ];
        } catch(\Exception $e){
            savebitacora("Ocurrio un error añadiendo usuarios del colectivo ".mensaje_excepcion($e) ,"Colectivos","add_empleados","ERROR");
            return [
                'result' => false,
                'mensaje' => "Ocurrio un error añadiendo usuarios del colectivo ".mensaje_excepcion($e)
            ];
        }
    }

    public function del_empleados($id,$emp){
        try{
            $col=colectivos::find($id);
            foreach($emp as $e){
                DB::table('colectivos_usuarios')->where(['cod_colectivo'=>$id,'cod_empleado'=> $e])->delete();
            }
            savebitacora("Borrados ".sizeof($emp)." usuarios del colectivo ".$col->des_colectivo ,"Colectivos","del_empleados","OK");
            return [
                'result' => true,
                'mensaje' => "Borrados ".sizeof($emp)." usuarios del colectivo ".$col->des_colectivo ,
            ];
        } catch(\Exception $e){
            savebitacora("Ocurrio un error borrando usuarios del colectivo ".mensaje_excepcion($e) ,"Colectivos","del_empleados","ERROR");
            return [
                'result' => false,
                'mensaje' => "Ocurrio un error borrando usuarios del colectivo ".mensaje_excepcion($e)
            ];
        }
    }


}
