<?php
namespace App\services;
use DB;
use Validator;
use App\User;
use App\Models\centros;
use App\Models\festivos;
use Carbon\Carbon;

class FestivoService
{
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function validar_request($r,$metodo_notif){
        $validator = Validator::make($r->all(), [
            'des_festivo' => 'required',
			'val_fecha' => 'required|date_format:d/m/Y',
            // 'cod_centro' => 'required',

        ],
        [
            'des_festivo.required' => 'El campo DES_FESTIVO es obligatorio',
            'val_fecha.required' => 'El campo VAL_FECHA es obligatorio',
            'cod_centro.required' => 'El campo COD_CENTRO es obligatorio',
        ]);

        if($validator->fails()) {
            $mensaje_error="ERROR: Ocurrio un error al validar los datos de festivo ".$r->nom_departamento."<br>".implode("<br>",$validator->messages()->all());
            switch($metodo_notif){
                case "flash":
                    flash($mensaje_error)->error();
                    return redirect()->back()->withInput();
                    break;

                case "toast":
                return response()->json(['title' => "Festivos",
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
            $c="";
            $p="";
            $rg="";

            if(is_array($r->cod_centro))
                $c = implode(",",$r->cod_centro);
            if(is_array($r->provincia)){
                $p = implode(",",$r->provincia);
            }
            if(is_array($r->region))
                $rg = implode(",",$r->region);

            $fes = new festivos();
            $fes->des_festivo = $r->des_festivo;
            $fes->val_fecha = Carbon::createFromFormat('d/m/Y', $r->val_fecha)->format('Y-m-d');
            $fes->cod_centro = (isset($r->mca_nacional) && $r->mca_nacional == 'S' ? '' : $c);
            $fes->cod_pais = $r->cod_pais;
            $fes->cod_provincia = (isset($r->mca_nacional) && $r->mca_nacional == 'S' ? '' : $p);
            $fes->cod_region = (isset($r->mca_nacional) && $r->mca_nacional == 'S' ? '' : $rg);
            $fes->mca_fijo = $r->mca_fijo;
            $fes->mca_nacional = (isset($r->mca_nacional) && $r->mca_nacional == 'S' ? 'S' : 'N');
            $fes->id_cliente = $r->cod_cliente;
            $fes->save();

            //dd($fes);

            savebitacora("Festivo ".$r->des_festivo. " creado" ,"Festivos","insertar","OK");
            return [
                'result' => true,
                'mensaje' => "Festivo ".$r->des_festivo. " creado",
                'id'=>$fes->cod_festivo
            ];
            } catch(\Exception $e){
            savebitacora("Ocurrio un error procesndo el festivo ".mensaje_excepcion($e) ,"Festivos","insertar","ERROR");
            return [
                'result' => false,
                'mensaje' => "Ocurrio un error procesndo el festivo ".mensaje_excepcion($e)
            ];
        }

    }

    public function actualizar($id,$r){
        try{
            if(is_array($r->cod_centro)){
                $c = implode(",",$r->cod_centro);
            } else {
                $c="";
            }
            if(is_array($r->provincia)){
                $p = implode(",",$r->provincia);
            } else {
                $p="";
            }
            if(is_array($r->region)){
                $rg = implode(",",$r->region);
            } else {
                $rg="";
            }

            $fes= festivos::find($id);
            $fes->des_festivo = $r->des_festivo;
            $fes->val_fecha = $r->val_fecha;
            //$fes->cod_centro=$c;
            $fes->cod_centro = (isset($r->mca_nacional) && $r->mca_nacional == 'S' ? '' : $c);
            $fes->cod_pais = $r->cod_pais;
            //$fes->cod_provincia=$p;
            $fes->cod_provincia = (isset($r->mca_nacional) && $r->mca_nacional == 'S' ? '' : $p);
            //$fes->cod_region=$rg;
            $fes->cod_region = (isset($r->mca_nacional) && $r->mca_nacional == 'S' ? '' : $rg);
            $fes->mca_fijo = $r->mca_fijo;
            $fes->mca_nacional = (isset($r->mca_nacional) && $r->mca_nacional == 'S' ? 'S' : 'N');
            $fes->id_cliente = $r->cod_cliente;
            $fes->save();



            savebitacora("Festivo ".$r->des_festivo. " actualizado" ,"Festivos","insertar","OK");
            return [
                'result' => true,
                'mensaje' => "Festivo ".$r->des_festivo. " actualizado",
                'id'=>$fes->cod_festivo
            ];
        } catch(\Exception $e){
            avebitacora("Ocurrio un error procesndo el festivo ".mensaje_excepcion($e) ,"Festivos","insertar","ERROR");
            return [
                'result' => false,
                'mensaje' => "Ocurrio un error procesndo el festivo ".mensaje_excepcion($e)
            ];
        }
    }

    public function delete($id){
        $fes=festivos::find($id);
        DB::table('festivos')->where('cod_festivo',$id)->delete();
		return [
            'result' => true,
            'mensaje' => "Festivo ".$fes->des_festivo. " borrado",
        ];
    }


}
