<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\puestos_asignados;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\users;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use File;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Redirect;
use Str;
Use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Hash;

class ImportController extends Controller
{
    //

    public function user_to_object($spreadsheet,$i){

        $fila = $spreadsheet->setActiveSheetIndex(1)->rangeToArray('A'.$i.':L'.$i)[0];
        //error_log(json_encode($fila));
        $vacio=true;
        for($n=0;$n<11;$n++){
            if($fila[$n]<>''){
                $vacio=false;
            }
        }
        if ($vacio){ //Fila vacio, ya no hay mas que procesar
            return false;
        }

        $user = new Request([
            'name' => $fila[0],
            'email' => $fila[1],
            'foto' => $fila[2],
            'parking' => $fila[3],
            'puesto1' => $fila[4],
            'puesto2' => $fila[5],
            'puesto3' => $fila[6],
            'supervisor' => $fila[7],
            'plantas_reserva' => $fila[8],
            'plantas_supervisor' => $fila[9],
            'es_supervisor' => $fila[10],
            'update' => $fila[11],
            'id_cliente' => Auth::user()->id_cliente
        ]);
        return $user;
    }

    public function puesto_to_object($spreadsheet,$i){

        $fila = $spreadsheet->setActiveSheetIndex(0)->rangeToArray('A'.$i.':H'.$i)[0];
        //error_log(json_encode($fila));
        $vacio=true;
        for($n=0;$n<7;$n++){
            if($fila[$n]<>''){
                $vacio=false;
            }
        }
        if ($vacio){ //Fila vacio, ya no hay mas que procesar
            return false;
        }
        $puesto = new Request([
            'cod_puesto' => $fila[0],
            'des_puesto' => $fila[1],
            'des_edificio' => $fila[2],
            'des_planta' => $fila[3],
            'mca_anonimo' => $fila[4],
            'mca_reserva' => $fila[5],
            'id_cliente' => Auth::user()->id_cliente,
            'usu_asignado' => $fila[6],
            'update' => $fila[7],
        ]);
        return $puesto;
    }

    /**
     * @param mixed $Request 
     * @param mixed $r 
     * @return void 
     */
    public function process_import(Request $r){
        try
        {
            $directorio = public_path().'/uploads/temp/';
            if(!File::exists($directorio)) {
                File::makeDirectory($directorio);
            }

            $directorio = public_path().'/uploads/temp/'.Auth::user()->id_cliente.'/';

            if(!File::exists($directorio)) {
                File::makeDirectory($directorio);
            }

            $files = $r->file('file');
            //Subiumos los ficheros a la carpeta temporal
            foreach($files as $file)
            {
                $file->move($directorio,$file->getClientOriginalName());
                if(File::extension($file->getClientOriginalName())=='xls' || File::extension($file->getClientOriginalName())=='xlsx'){
                    $fichero_plantilla=$directorio.$file->getClientOriginalName();
                }
            }

            if (isset($fichero_plantilla))
            {
                savebitacora('Iniciado proceso de importacion',"Importacion","process_import","OK");
                if(File::extension($fichero_plantilla)=='xls')
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                else $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                
                $spreadsheet = $reader->load($fichero_plantilla);
               
                //Vuelta de comprobacion
                $errores = "";
                $cuenta_errores = 0;
                $cuenta_usuarios = 0;
                $cuenta_puestos = 0;
                $mensajes_adicionales = "";
                $nombres_usuarios = "";
                $nombres_puestos = "";
                
                //Comproboamos los datos de puestos
                $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
                for ($i = 2; $i <= $highestRow; $i++) 
                {
                    $puesto = $this->puesto_to_object($spreadsheet, $i);
                    if($puesto == false)
                    {   
                        //El fichero esta vacio
                        break;
                    } 
                    else
                    {
                        if($puesto->cod_puesto!='' && $puesto->des_edificio!='' && $puesto->des_planta!=''){
                            $cuenta_puestos++;
                        } else {
                            $cuenta_errores++;
                            $errores.='PUESTOS - Fila '.$i.' Falta un campo requerido <br>';
                        }
                        
                    }
                }
                //Comprobamos los datos de usuarios
                $highestRow = $spreadsheet->setActiveSheetIndex(1)->getHighestRow();
                for ($i = 3; $i <= $highestRow; $i++) 
                {
                    $user = $this->user_to_object($spreadsheet, $i);
                    if($user == false)
                    {   
                        //El fichero esta vacio
                        break;
                    } 
                    else
                    {
                        if($user->name!='' && $user->email!=''){
                            $cuenta_usuarios++;
                            $u=users::where('email',$user->email)->first();
                            if($u!=null && $user->update!='SI'){
                                $cuenta_errores++;
                                $errores.='USUARIOS - Fila '.$i.' la direccion de email ya existe <br>';
                            }
                        } else {
                            $cuenta_errores++;
                            $errores.='USUARIOS - Fila '.$i.' Falta un campo requerido <br>';
                        }
                        
                    }
                }

                //Vemos si hay errores, si los hay, se acabo, no seguimos procesando
                if ($errores != "")
                {
                    return [
                        'title' => 'Se detectaron errores en el fichero de importacion',
                        'message' => $errores,
                        'tipo' => 'error'
                    ];
                }
                
                //Tutuben, pues a insertar
                DB::beginTransaction();
                try
                {
                    
                    for ($i = 3; $i < ($cuenta_usuarios+3); $i++) 
                    {
                        $user = $this->user_to_object($spreadsheet, $i);
                        if($user->update=='' || $user->update=='N' || $user->update=='NO'){
                            $u=new users;
                        } else {
                            $u=users::where('email',$user->email)->first();
                            if(!isset($u)){
                                $u=new users;
                            }
                        }
                        $es_supervisor=$user->es_supervisor=='SI'?'S':'N';

                        $u->email=$user->email;
                        $u->name=$user->name;
                        $u->id_cliente=Auth::user()->id_cliente;
                        $u->password=Str::random(15);
                        $u->cod_nivel=1;

                        if ($user->foto!='') {
                            $file = File::get($directorio.$user->foto);
                            $path = '/img/users/';
                            $img_usuario = uniqid().rand(000000,999999).'.'.File::extension($directorio.$user->foto);
                            //$file->move($path,$img_usuario);
                            Storage::disk(config('app.img_disk'))->putFileAs($path,$directorio.$user->foto,$img_usuario);
                            $u->img_usuario=$img_usuario;
                        }

                        try{
                            //Ahora rellenamos el supervisor
                            $supervisor=users::where('email',$user->supervisor)->first();
                            if(isset($supervisor)){
                                $u->id_usuario_supervisor=$supervisor->id;
                            }
                        } catch (\Exception $e){
                            $errores.='Supervisor '.$user->supervisor.' No encontrado <br>';
                        }

                        $u->save();
                    }

                    $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
                    for ($i = 2; $i < ($cuenta_puestos+2); $i++) 
                    {
                        
                        
                        $puesto = $this->puesto_to_object($spreadsheet, $i);

                        $anonimo=$puesto->mca_anonimo=='SI'?'S':'N';
                        $m_res=$puesto->mca_reserva=='SI'?'S':'N';
                        if($puesto->update=='' || $puesto->update=='N' || $puesto->update=='NO'){
                            $p=new puestos;
                        } else {
                            $p=puestos::where('cod_puesto',$puesto->cod_puesto)->first();
                            if(!isset($p)){
                                $p=new puestos;
                            }
                        }
                        
                        $p->cod_puesto=$puesto->cod_puesto;
                        $p->des_puesto=$puesto->des_puesto;
                        $p->id_cliente=Auth::user()->id_cliente;
                        $p->mca_reservar=$m_res;
                        $p->mca_acceso_anonimo=$anonimo;
                        $p->token=Str::random(50);
                        //Edificio y planta
                        $edificio=edificios::where('des_edificio',$puesto->des_edificio)->first();
                        if(!$edificio){
                            $edificio = new edificios;
                            $edificio->des_edificio=$puesto->des_edificio;
                            $edificio->id_cliente=$p->id_cliente;
                            $edificio->save();
                        }
                        $planta=plantas::where('des_planta',$puesto->des_planta)->first();
                        if(!$planta){
                            $planta = new plantas;
                            $planta->des_planta=$puesto->des_planta;
                            $planta->id_cliente=$p->id_cliente;
                            $planta->id_edificio=$edificio->id_edificio;
                            $planta->save();
                        }
                        
                        $p->id_edificio=$edificio->id_edificio;
                        $p->id_planta=$planta->id_planta;
                        $p->save();

                        //Asignacion del puesto a usuario
                        $usu_asignado=users::where('email',$puesto->usu_asignado)->first();
                        if(isset($usu_asignado)){
                            DB::table('puestos_asignados')->insert([
                                "id_puesto"=>$p->id_puesto,
                                "id_usuario"=>$usu_asignado->id
                            ]);
                        }
                        
                        savebitacora("Creado puesto en importacion " . $p->id_puesto . " " . $p->cod_puesto, $p->id_cliente);
                        $nombres_puestos .= "[".$p->id_puesto."] " . $p->des_puesto. "<br>";
                    }

                    DB::commit();

                    //Ahora la asignacion de puestos y plantas a usuarios, porque debe hacerse cuando tengamos los ID

                    for ($i = 3; $i < ($cuenta_usuarios+3); $i++) 
                    {
                        $user = $this->user_to_object($spreadsheet, $i);
                        $u=users::where('email',$user->email)->first();

                        savebitacora("Creado usuario en importacion " . $u->id . " " . $u->name, $u->id_cliente);
                        $nombres_usuarios .= "[".$u->id."] " . $u->name. "<br>";

                        try{
                            //Plantas asignadas reserva
                            $lista_plantas=explode(",",$user->plantas_reserva);
                            if(is_array($lista_plantas) && count($lista_plantas)>0){
                                //Borramos las plantas que tenga asignadas el usuario
                                DB::table('plantas_usuario')->where('id_usuario',$u->id)->delete();
                            }
                            $mensajes_adicionales.="<br> Añadir plantas usuario: ";
                            foreach($lista_plantas as $pl){
                                $esta_planta=plantas::where('id_planta',$pl)->orwhere('des_planta',$pl)->where('id_cliente',$user->id_cliente)->first();  
                                $mensajes_adicionales.=" ".$pl;
                                DB::table('plantas_usuario')->insert([
                                    'id_usuario'=>$u->id,
                                    'id_planta'=>$esta_planta->id_planta
                                ]);
                            }
                        } catch (\Exception $e){
                            $errores.='Error asignando planta a usuario '.$u->email.' '.mensaje_excepcion($e);
                        }

                        try{
                            //Plantas asignadas supervisor
                            $lista_plantas=explode(",",$user->plantas_supervisor);
                            if(is_array($lista_plantas) && count($lista_plantas)>0){
                                //Borramos las plantas que tenga asignadas el usuario
                                DB::table('puestos_usuario_supervisor')->where('id_usuario',$u->id)->delete();
                            }
                            $mensajes_adicionales.="<br> Añadir plantas supervision: ";
                            foreach($lista_plantas as $pl){
                                $esta_planta=plantas::where('id_planta',$pl)->orwhere('des_planta',$pl)->where('id_cliente',$user->id_cliente)->first();  
                                $puestos_supervisar=puestos::where('id_planta',$esta_planta->id_planta)->get();
                                $mensajes_adicionales.=" ".$pl;
                                foreach($puestos_supervisar as $ps){
                                    DB::table('puestos_usuario_supervisor')->insert([
                                        'id_usuario'=>$u->id,
                                        'id_puesto'=>$ps->id_puesto
                                    ]);
                                }   
                            }
                        } catch (\Exception $e){
                            $errores.='Error asignando planta a usuario para supervision '.$u->email.' '.mensaje_excepcion($e);
                        }

                        //Puestos asignados
                        $campos_puestos=['parking','puesto1','puesto2','puesto3'];
                        foreach($campos_puestos as $campo){
                            try{
                                //buscamos el puesto
                                $puestoa=puestos::where( function($q) use($user,$campo){
                                        $q->where('cod_puesto',$user->$campo);
                                        $q->orwhere('id_puesto',$user->$campo);
                                })
                                ->where('id_cliente',$user->id_cliente)
                                ->first();
                                if(isset($puestoa)){
                                    $pa=new puestos_asignados;
                                    $pa->id_puesto=$puestoa->id_puesto;
                                    $pa->id_usuario=$u->id;
                                    $pa->id_tipo_asignacion=1;
                                    $pa->save();
                                }
                                $esta=DB::table('plantas_usuario')->where(['id_usuario'=>$u->id,'id_planta'=>$puestoa->id_planta])->first();
                               
                                if(!$esta){
                                    DB::table('plantas_usuario')->insert([
                                        'id_usuario'=>$u->id,
                                        'id_planta'=>$puestoa->id_planta
                                    ]);
                                }
                                   
                            } catch (\Exception $e){
                                $errores.='Puesto para asignar '.$user->$campo.' No encontrado <br>';
                            }
                        }
                        
                    }

                    //Borramos el excel y la carpeta de importacion
                    File::deleteDirectory($directorio);
					savebitacora($cuenta_usuarios . " usuarios importados correctamente:<br>" . $nombres_usuarios . "<br>" .'<br>'. $cuenta_puestos . " puestos importados correctamente:<br>" . $nombres_puestos . "<br>" . $mensajes_adicionales." <br> Errores no criticos encontrados[".$errores."]","Importacion","process_import","OK");
                    return [
                        'title' => 'Proceso de importacion finalizada con exito',
                        'message' => $cuenta_usuarios . " usuarios importados correctamente:<br>" . $nombres_usuarios . "<br>" .'<br>'. $cuenta_puestos . " puestos importados correctamente:<br>" . $nombres_puestos . "<br>" . $mensajes_adicionales. "<br> Errores no criticos encontrados[".$errores."]",
                        'tipo' => 'ok'
                    ];
                } catch (\Throwable $e){
                    DB::rollback();
                    savebitacora("Error en el proceso de importacion ". $e->getMessage(), null);
                    return [
                        'title' => 'Error comprobando los datos de empleados',
                        'message' => "Error en el proceso de importacion " . $e->getMessage(),
                        'tipo' => 'error'
                    ];
                }
            }
            else return \Response::json(array('success' => true));
        } 
        catch(\Throwable $e){
            return \Response::json(array('error' => false));
        }
    }

    public function import_from_db(){
        $id_cliente=5;
            $original=DB::table('tmp_carga_puestos')->get();
            foreach($original as $item){
                $tipo=DB::table('puestos_tipos')->where(function($q) use($item){$q->whereraw("UPPER(abreviatura)='".strtoupper($item->tipo)."'");$q->orwhereraw("UPPER(des_tipo_puesto)='".strtoupper($item->tipo)."'");})->where('id_cliente',$id_cliente)->first();
                $edificio=DB::table('edificios')->where(function($q) use($item){$q->whereraw("UPPER(abreviatura)='".strtoupper($item->edificio)."'");$q->orwhereraw("UPPER(des_edificio)='".strtoupper($item->edificio)."'");})->where('id_cliente',$id_cliente)->first();
                $planta=DB::table('plantas')->where(function($q) use($item){$q->whereraw("UPPER(abreviatura)='".strtoupper($item->planta)."'");$q->orwhereraw("UPPER(des_planta)='".strtoupper($item->planta)."'");$q->orwhere('id_planta',$item->planta);})->where('id_cliente',$id_cliente)->first();
                $esta=DB::table('puestos')->where('cod_puesto',$item->cod_puesto)->where('id_cliente',$id_cliente)->first();

                $arr_nombre=explode("-",$item->cod_puesto);
                if($tipo!=null && $edificio!=null && $planta!=null and $esta==null){
                    DB::table('puestos')->insert([
                        'id_tipo_puesto'=>$tipo->id_tipo_puesto,
                        'id_edificio'=>$edificio->id_edificio,
                        'id_planta'=>$planta->id_planta,
                        'id_cliente'=>$id_cliente,
                        'id_estado'=>1,
                        'cod_puesto'=>$item->cod_puesto,
                        'des_puesto'=>$arr_nombre[2].' '.$arr_nombre[3],
                        'token'=>Str::random(50)
                    ]);
                } else {
                    if (!$esta){
                        dump('Error con el puesto '.$item->cod_puesto);
                    }
                    
                }
            }
    }
}
