<?php

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\users;
use App\Models\reservas;
use App\Models\salas;
use App\Models\puestos;
use App\Models\config_clientes;
use App\Models\clientes;
use App\Models\notif;
use App\Models\notificaciones_tipos;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;
use Shahonseven\ColorHash;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Illuminate\Support\Facades\Storage;


function stripAccents($str) {
    return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

function getProfilePic()
{
    $e = \DB::table('empleados')->where('cod_empleado',Auth::user()->cod_empleado)->first();
    if ($e) {
        if ($e->img_empleado) {
            return url('uploads/employees/images',$e->img_empleado);
        }
    }
    return url('default.png');
}

function savebitacora($des_bitacora,$modulo=null,$seccion=null,$tipo='OK',$user=0)
{
    if(isset(Auth::user()->name)){
        $user=Auth::user()->name;
    }

    \DB::table('bitacora')->insert([
        'accion' => substr($des_bitacora,0,5000),
        'id_usuario' => $user!=0?$user:Auth::user()->id??0,
        'id_modulo' => $modulo,
        'id_seccion' => $seccion,
        'status' => $tipo,
        'fecha' => Carbon::now()
    ]);
}

function clientes()
{
    try{
        if(fullAccess())
        {
            $clientes = DB::table('clientes')->select('id_cliente')->wherenull('fec_borrado')->pluck('id_cliente')->toarray();
            if(session('id_cliente')){
                $clientes=DB::table('clientes')->where('id_cliente',session('id_cliente'))->pluck('id_cliente')->toarray();
            }
        }
        else
        {
            $clientes = [];
            if(isset(Auth::user()->clientes)){
                foreach (explode(',',Auth::user()->clientes) as $key => $value) {
                    if ($value != "") {
                        $clientes[] = $value;
                    }
                }
            }
        }

        if(count($clientes)==0){
            return [Auth::user()->id_cliente];
        } else{
            return $clientes;
        }
    } catch(\Exception $e){
        return [0];
    }
}

function puede_ver_cliente($id){
    try{
        $cus=\DB::table('clientes')
        ->where('clientes.id_cliente',$id)
        ->whereNull('clientes.fec_borrado')
        ->where(function($q){
            if (!fullAccess()) {
                $q->Wherein('clientes.id_cliente',clientes());
            }
        })->exists();
        return $cus;
    } catch(\Exception $e){
        return false;
    }
}

function lista_clientes(){
    $clientes=DB::table('clientes')
    ->wherein('id_cliente',clientes())
    ->orderby('nom_cliente')
    ->get();
    return $clientes;
}

function isJson($string) {
    try{
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    } catch(\Exception $e){
        return false;
    }
    
}

function isxml($xmlstr){
    try{
        libxml_use_internal_errors(true);

        $doc = simplexml_load_string($xmlstr);
        $xml = explode("\n", $xmlstr);

        if (!$doc) {
            $errors = libxml_get_errors();

            // foreach ($errors as $error) {
            //     echo display_xml_error($error, $xml);
            // }

            libxml_clear_errors();
            return false;
        } else {
            return true;
        }
    } catch(\Exception $e){
        return false;
    }
}

//Añadir ceros por la izquierda\
function lz($num,$cantidad=2)
{
    //return (strlen($num) < $cantidad) ? "0{$num}" : $num;
    return str_pad($num, $cantidad, '0', STR_PAD_LEFT);
}

//Devuelve el valor de cualquiera de los elementos del array de parametros de un comando para tareas/Eventos
function valor($parametros,$nombre){
    $salida=null;
    try{
        foreach($parametros as $param){
            if($param->name==$nombre){
                return $param->value;
            }
        }
    } catch(\Exception $e){

    }
    return $salida;
}

//Decodificar JSON mejorado
function decodeComplexJson($string) { # list from www.json.org: (\b backspace, \f formfeed)
    $string = preg_replace("/[\r\n]+/", "", $string); //Retornos de carro
    $string = preg_replace('/[ ]{2,}|[\t]/', '', trim($string));  //tabs
    $json = utf8_encode($string);
    $json = json_decode($json);
    return $json;
}

//Funcion para que las tareas programadas escriban su log
function log_tarea($mensaje,$id,$tipo='info'){
    DB::table('tareas_programadas_log')->insert([
        'txt_log'=>$mensaje,
        'cod_tarea'=>$id,
        'tip_mensaje'=>$tipo,
        'fec_log'=>Carbon::now()
    ]);
}

function fullAccess(){
    return isAdmin();
}

function isAdmin(){
    try{
        return Auth::User()->nivel_acceso == 200 ? true : false;
    } catch(\Exception $e){
        return false;
    }
}

function isSupervisor($id){
    try{
        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;
        $usuario=users::findorfail($id);

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->where('id_perfil',$usuario->cod_nivel)->first();
        return isset($supervisores_perfil)&&$supervisores_perfil->mca_read=="1"?true:false;
    } catch(\Exception $e){
        return false;
    }
}

function mensaje_excepcion($e){
    if(isAdmin()){
        return $e->getMessage().' {'.get_class($e).'}  ['.debug_backtrace()[1]['function'].']';
    } else {
        return substr($e->getMessage(),1,15);
    }
}

function isCustomerAdmin(){
    try{
        return Auth::User()->val_nivel_acceso >= 100 ? true : false;
    } catch(\Exception $e){
        return false;
    }
}

function formatmysqldate($date){
    return \Carbon\Carbon::createFromFormat('d/m/Y H:i', $date)->format('Y-m-d H:i');
}

function formatmysqldate2($date){
    return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
}

function formatspdate($date){
    return \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date)->format('d/m/Y H:i');
}

function isDesktop(){
    $agent = new \Jenssegers\Agent\Agent;
    return $agent->isDesktop();
}

function isMobile(){
    $agent = new \Jenssegers\Agent\Agent;
    return $agent->isMobile();
}

function decimal_to_time($dec)
{
    // start by converting to seconds
    $seconds = ($dec * 3600);
    // we're given hours, so let's get those the easy way
    $hours = floor($dec);
    // since we've "calculated" hours, let's remove them from the seconds variable
    $seconds -= $hours * 3600;
    // calculate minutes left
    $minutes = floor($seconds / 60);
    // remove those from seconds as well
    $seconds -= $minutes * 60;
    // return the time formatted HH:MM:SS
    if ($hours==0 && $minutes==0){
        return 0;
    } else {
        return lz($hours).":".lz($minutes).":".lz($seconds);
    }
}


function get_local_tz(){
    try{
        $ip = request()->ip();
        $url = 'http://ip-api.com/json/'.$ip;
        $tz = file_get_contents($url);
        $tz = json_decode($tz,true)['timezone'];
    } catch(\Exception $e){
        $tz="Europe/Madrid";
    }
    return $tz;
}

function time_to_dec($time,$out='s'){
    //Devuelve en segundos una fecha pasada en HH:mm:ss
    try{
        $time    = explode(':', $time);
        $result = ($time[0] * 3600 + $time[1] * 60+ $time[2]);
        switch ($out) {
            case 's':
                return $result;
                break;
            case 'm':
                return $result/60;
                break; 
            case 'h':
                return $result/3600;
                break;
            default:
                return $result;
                break;
        }
    } catch (\Exception $e){
        return null;
    }

}

///Convertir fecha en español a mysql  S: devuelve string | C: devuelve Carbon
function adaptar_fecha($d,$formato='S'){
    if(!isset($d)){
        return null;
    }
    try{
        if (Carbon::createFromFormat('d/m/Y', $d)!== false) {
            if($formato=='S'){
                return Carbon::createFromFormat('d/m/Y', $d)->format('Y-m-d');
            } else {
                return Carbon::createFromFormat('d/m/Y', $d);
            }
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::createFromFormat('d/m/Y H:i:s', $d)!== false) {
            if($formato=='S'){
                return Carbon::createFromFormat('d/m/Y H:i:s', $d)->format('Y-m-d H:i:s');
            } else {
                return Carbon::createFromFormat('d/m/Y H:i:s', $d);
            }
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::createFromFormat('d/m/Y H:i', $d)!== false) {
            if($formato=='S'){
                return Carbon::createFromFormat('d/m/Y H:i', $d)->format('Y-m-d H:i');
            } else {
                return Carbon::createFromFormat('d/m/Y H:i', $d);
            }
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::createFromFormat('d/m/Y', $d)!== false) {
            if($formato=='S'){
                return Carbon::createFromFormat('d/m/Y', $d)->format('Y-m-d');
            } else {
                return Carbon::createFromFormat('d/m/Y', $d);
            }
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::createFromFormat('Y-m-d', $d)!== false) {
            if($formato=='S'){
                return Carbon::createFromFormat('Y-m-d', $d)->format('Y-m-d');
            } else {
                return Carbon::createFromFormat('Y-m-d', $d);
            }
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::parse($d)!== false) {
            if($formato=='S'){
                return Carbon::parse($d)->format('Y-m-d');
            } else {
                return Carbon::parse($d);
            }
        }
    } catch (\Exception $e){}
    return  $d;
}

function randomPassword( $len = 16, $ucfirst = true, $spchar = true ){
	if ( $len >= 6 && ( $len % 2 ) !== 0 ) { // Length parameter must be greater than or equal to 6, and a multiple of 2
		$len = 8;
	}
	$length = $len - 2; // Makes room for a two-digit number on the end
	$conso = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z');
	$vocal = array('a','e','i','o','u');
	$spchars = array('!','@','#','$','%','^','*','&','*','-','+','?');
	$password = '';
	$max = $length / 2;
	for ( $i = 1; $i <= $max; $i ++ ) {
		$password .= $conso[ random_int( 0, 19 ) ];
		$password .= $vocal[ random_int( 0, 4 ) ];
	}
	if ( $spchar == true ) {
		$password = substr($password, 0, -1) . $spchars[ random_int( 0, 11 ) ];
	}
	$password .= random_int( 10, 99 );
	if($ucfirst==true){
		$password = ucfirst( $password );
	}
	return $password;
}

//Rellena en el texto pasado los comodines indicados por los corchetes con su correspondiente valor de la lista de datos. Esto es para las notificaciones de evebntos
function comodines_texto($texto,$campos,$datos){
    preg_match_all("/\[([^\]]*)\]/", $texto, $matches);
    
    foreach($matches[0] as $match){
        foreach($campos as $campo){
            if($match==$campo->label){
                $nom_campo=str_replace("[","",$campo->label);
                $nom_campo=str_replace("]","",$nom_campo);
                if($match=="[fecha]")
                {
                    $texto=str_replace("[fecha]",Carbon::now()->format('d/m/Y'),$texto);
                } else if($match=="[hora]")
                {
                    $texto=str_replace("[hora]",Carbon::now()->format('H:i'),$texto);
                } else {
                    $texto=str_replace($match,$datos->$nom_campo,$texto);
                }
            }
        }
    }
    return $texto;
}


function insertar_notificacion_web($user,$tipo,$texto,$id){

    $url_base="/";
    $tipo_notif=notificaciones_tipos::find($tipo);
   
    if(isset($tipo_notif)){
        $url_base=$tipo_notif->url_base;
    }
    
    $notif=DB::table('notificaciones')->insertGetId(
        [
            'id_usuario'=>$user,
            'id_tipo_notificacion'=>$tipo,
            'txt_notificacion'=>$texto,
            'fec_notificacion'=>Carbon::now(),
            'mca_leida'=>'N',
            'url_notificacion'=>url($url_base,$id)
        ]
    );
    return $notif;

}

function metodos_notificacion_usuario($user,$metodo=null){
    $usuario=users::find($user);
    Log::debug('usuario notificacion: '.$usuario->mca_notif_email.'-'.$usuario->mca_notif_push);
    $metodos=[];
    if($usuario->mca_notif_email=='S' && ($metodo==null || $metodo==1)){
        array_push($metodos,1);
    }
    if($usuario->mca_notif_push=='S' && ($metodo==null || $metodo==3)){
        array_push($metodos,3);
    }
   
    Log::debug('metodos notificacion: '.json_encode($metodos));
    return $metodos;
}

function notificar_usuario($user,$subject,$plantilla,$body,$metodo=[],$tipo=1,$attachments=null,$id=null){
    
    try{    
        //Añadimos notificacion en la web
        $id_notif=insertar_notificacion_web($user->id,$tipo,$body,$id);
        Log::debug('Notificacion web insertada '.$id_notif);


        foreach($metodo as $m){
            switch ($m) {
                case 0:
                    //No hacer nada
                break;
                case 1: //Mail
                    if($user->mca_notif_email=='S'){
                        $cliente=clientes::find($user->id_cliente);
                        \Mail::send($plantilla, ['user' => $user,'body'=>$body,'cliente'=>$cliente,'id'=>$id], function ($m) use ($user,$subject,$attachments) {
                            if(config('app.env')=='local' || config('app.env')=='qa'){//Para que en desarrollo solo me mande los mail a mi
                                Log::debug('Capturado email para '.$user->email.' ('.$user->name.')');
                                $m->to('nomecansum@gmail.com', $user->name)->subject(strip_tags($subject));
                            } else {
                                $m->to($user->email, $user->name)->subject(strip_tags($subject));
                            }
                            $m->from(config('mail.from.address'),config('mail.from.name'));
                            if($attachments!==null){
                                if (is_array($attachments)){ //adjuntamos si existe
                                    foreach($attachments as $fichero){
                                        $m->attach($fichero);
                                    }
                                } else {
                                    $m->attach($attachments);
                                }
                            }
                        });
                    }
                    break;
                case 2:
                case 3:  //Notificacion WEBpush
                    if($user->mca_notif_push=='S' && $user->id_onesignal!==null){
                        log::info('notificacion push');
                        $result=OneSignal::sendNotificationToExternalUser(
                            $body,
                            [strval( $user->id )],
                            $url = url("/notif/ver",$id_notif),
                            $data = null,
                            $buttons = null,
                            $schedule = null
                        );
                    }
                    break;
            }

        }
        
        } catch(\Exception $e){
        log::error($e->getMessage());
        return $e->getMessage();
    }
    return true;
}

function enviar_email_error($request, $from, $to, $to_name, $subject, $plantilla, $exception){
    try
    {
    	if(!is_array($to)){
    		$destinatarios = array($to);
    	} else {
            $destinatarios = $to;
        }

		foreach ($destinatarios as $recipient)
		{
	        $resp = \Mail::send($plantilla, ['request'=>$request, 'exception'=>$exception], function ($m) use ($from, $recipient, $to_name, $subject) {
	            $m->from($from, 'Spotlinker');
	            if (config('app.manolo')){
	                $m->to("nomecansum@gmail.com", $to_name)->subject($subject);
	            }
	            else {
	                $m->to($recipient, $to_name)->subject($subject);
	            }

	        });
        }
    } catch(\Exception $e){
    	Log::info("Error envio");
    	Log::info($e->getMessage());
        return $e->getMessage();
    }
    return true;
}

function tags($string, $encoding = 'UTF-8'){
    $string = trim(strip_tags(html_entity_decode(urldecode($string))));
    if(empty($string)){ return false; }

    $stopWords = array('a','ante', 'bajo', 'con', 'contra','de', 'desde', 'durante','en', 'entre','hacia', 'hasta', 'mediante', 'para', 'por', 'pro', 'segun','sin', 'sobre', 'tras', 'via','los', 'las', 'una', 'unos', 'unas', 'este', 'estos', 'ese',
    'esos', 'aquel', 'aquellos', 'esta', 'estas', 'esa', 'esas','aquella', 'aquellas', 'usted', 'nosotros', 'vosotros','ustedes', 'nos', 'les', 'nuestro', 'nuestra', 'vuestro','vuestra', 'mis', 'tus', 'sus', 'nuestros', 'nuestras',
   'vuestros', 'vuestras','esto', 'que','Planta', '/','-');
 
    $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
   
    $string = trim($string); // trim the string

    $string = preg_replace('/[^a-zA-Z0-9À-ÿ -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
    
    $string = mb_strtolower($string,$encoding); // make it lowercase

    $matchWords=preg_replace('/\b(' . implode('|', $stopWords) . ')\b/u', '', $string);
    
    return $matchWords;
}

function get_mime_type($filename) {
    try{
        $filename=strtolower($filename);
        $idx = explode( '.', $filename );
        $count_explode = count($idx);
        $idx = strtolower($idx[$count_explode-1]);

        $mimet = array( 
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'webp' => 'image/webp',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mp4' => 'video/mpeg4',
            'mpeg4' => 'video/mpeg4',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',


            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        if (isset( $mimet[$idx] )) {
        return $mimet[$idx];
        } else {
        return 'application/octet-stream';
        }
    } catch(\Exception $e){
        return 'application/octet-stream';
    }
}

function js_array($array,$tipo='str')
{
    if(!function_exists('js_str')){
        function js_str($s)
        {
            return '"' . addcslashes($s, "\0..\37\"\\") . '"';
        }
    }

    if($tipo=='str'){
        $temp = array_map('js_str', $array);
    } else {
        $temp=$array;
    }
    return '[' . implode(',', $temp) . ']';
}

function replace_extension($filename, $new_extension) {
    $info = pathinfo($filename);
    return $info['filename'] . '.' . $new_extension;
}

function acronimo($nombre,$height=10){

    try{
        $words=tags($nombre);
        // $words = explode(" ", $nombre);
        // if(sizeof($words)==0){
        //     $words = explode(".", $nombre);
        // }
        $acronym = "";
        if(sizeof($words)<4){
            $corte=round(12/sizeof($words));
        } else {
            $corte=3;
        }
        if(sizeof($words)>1){
            foreach ($words as $w) {
                $acronym .= ucfirst(substr($w,0,$corte));
            }
        } else {
            $acronym=$nombre;
        }

        $acronym=substr($acronym,0,$height);
    } catch(\Exception $e){
        $acronym=substr($nombre,0,$height);
    }
    return $acronym;
}

function iniciales ($nombre,$cantidad){
    try{
        $nombre=stripAccents($nombre);
        $words = explode(" ", $nombre);
        $acronym = "";
        $i = 0;
        foreach ($words as $w) {
            $acronym .= $w[0];
            if (++$i == $cantidad) break;
        }
    } catch(\Exception $e){
        $acronym=substr($nombre,1,$cantidad);
    }
    return $acronym;
}

function icono_nombre($nombre,$height=50,$font=18,$top=-1){

    $padding=intdiv($height,11);
    $rand=Str::random(9);
    $acronym = iniciales($nombre,2);
    // if($height<30){
    //     $top_letras=-1;
    //     $left_letras=0;
    // } elseif($height<50){
    //     $top_letras=-10;
    //     $left_letras=0;
    // } else {
    //     $top_letras=0;
    //     $left_letras=2;
    // }
    $top_letras=$top*($height/10);
    $left_letras=2;

   
    //return '<span class="round" id="'.$rand.'" style="text-transform: uppercase; background-color: '.App\Classes\RandomColor::one().'">'.$acronym.'</span>';
    return '<div class="round add-tooltip" id="'.$rand.'" style="line-height: 50px; padding: 0px; font-weight: bold; font-size: '.$font.'px; width: '.$height.'px;height: '.$height.'px; text-transform: uppercase; background-color: '.genColorCodeFromText($nombre).'" data-toggle="tooltip" data-placement="bottom" title="'.$nombre.'"><span style="position: relative; top: '.$top_letras.'px; left:'.$left_letras.'%;">'.$acronym.'</span></div>';
}

function randomcolor(){
    return App\Classes\RandomColor::one(['luminosity' => 'light']);
}

function imagen_usuario($user,$height=50){
    if(isset($user->img_usuario) && $user->img_usuario<>'')
        return '<img class="direct-chat-img b-all" src="'.url("/img/users/",$user->img_usuario).'" height="'.$height.'px" alt="" onerror="this.remove()" style="height:'.$height.'px; width:'.$height.'px; object-fit: cover;">';
    else
        return icono_nombre($user->name);
}

function genColorCodeFromText($text,$min_brightness=100,$spec=10)
{
    $colorHash = new Shahonseven\ColorHash();
    return $colorHash->hex($text);

}

function checkPermissions($secciones = [],$permisos = [])
{
    $a = 0;
    $b = 0;
    if (!$secciones) {
        return false;
    }
    if (!$permisos) {
        return false;
    }
    $encontrado=false;
    $mispermisos=Session::get('P');
    if(!isset($mispermisos)){
        return false;
    }
    foreach ($secciones as $key => $s) {
        foreach($mispermisos as $per){
            if(strtoupper($per->des_seccion)==strtoupper($s)){
                $encontrado=true;
            }
        }
       
        foreach ($permisos as $key => $p) {
            if ($p == "R") {$type = "mca_read";}
            if ($p == "W") {$type = "mca_write";}
            if ($p == "C") {$type = "mca_create";}
            if ($p == "D") {$type = "mca_delete";}
            foreach($mispermisos as $per){
                if($per->des_seccion==$s && $per->$type==1){
                    $b++;
                }
            }
        }
    }
    if ($encontrado && $b > 0) {
        return true;
    }
    return false;
}

function checkPermissionsAPI($secciones = [],$permisos = [])
{
    $a = 0;
    $b = 0;
    if (!$secciones) {
        return false;
    }
    if (!$permisos) {
        return false;
    }
    $encontrado=false;
    $mispermisos=DB::table('secciones_perfiles')
        ->select('des_seccion','mca_read','mca_write','mca_create','mca_delete')
        ->join('secciones','secciones_perfiles.id_seccion','secciones.cod_seccion')
        ->where('id_perfil',Auth::user()->cod_nivel)->get();
    if(!isset($mispermisos)){
        return false;
    }
    foreach ($secciones as $key => $s) {
        foreach($mispermisos as $per){
            if($per->des_seccion===$s){
                $encontrado=true;
            }
        }
        if (!$encontrado){
            return false;
        }
        foreach ($permisos as $key => $p) {
            if ($p == "R") {$type = "mca_read";}
            if ($p == "W") {$type = "mca_write";}
            if ($p == "C") {$type = "mca_create";}
            if ($p == "D") {$type = "mca_delete";}
            foreach($mispermisos as $per){
                if($per->des_seccion==$s && $per->$type==1){
                    $b++;
                }
            }
        }
    }
    if ($encontrado && $b >= (count($permisos)*count($secciones))) {
        return true;
    }
    return false;
}

function beauty_fecha($date,$mostrar_hora=-1){
    setlocale(LC_TIME, App::getLocale());
    $hora="";
    if((strlen($date)>10 && $mostrar_hora==-1) || $mostrar_hora==1){
        $hora=Carbon::parse($date)->format('H:i');
    }
    if(Carbon::parse($date)->format('Y')==Carbon::now()->format('Y')){
        $fecha=Carbon::parse($date)->formatLocalized('%d %b');
    } else {
        $fecha=Carbon::parse($date)->format('d/m/Y');
    }
    if($mostrar_hora=="2"){
        $fecha=Carbon::parse($date)->formatLocalized('%d %b');
    }
    return "<b>".$fecha."</b> ".$hora;
}

function validar_request($r,$metodo_notif,$tipo,$reglas,$mensajes=[]){
    $validator = Validator::make($r->all(), $reglas,$mensajes);
    if($validator->fails()) {
        $mensaje_error="ERROR: Ocurrio un error al validar los datos de ".$tipo." <br>".implode("<br>",$validator->messages()->all());

        switch($metodo_notif){
            case "flash":
                flash($mensaje_error)->error();
                return redirect()->back()->withInput();
                break;

            case "toast":
            return response()->json(['title' => $tipo,
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

function validar_acceso_tabla($id,$tabla){
    switch($tabla){
        case "clientes":
            $descriptivo="cliente";
            $campo="id_cliente";
            $ruta="clientes.index";
            break;
        case "plantas":
            $descriptivo="planta";
            $campo="id_planta";
            $ruta="plantas.plantas.index";
            break;
        case "edificios":
            $descriptivo="edificio";
            $campo="id_edificio";
            $ruta="edificios.edificios.index";
            break;
        case "users":
            $descriptivo="usuario";
            $campo="id";
            $ruta="users.index";
            break;
        case "puestos":
            $descriptivo="puesto";
            $campo="id_puesto";
            $ruta="puestos.index";
            break;
        case "rondas_limpieza":
            $descriptivo="ronda";
            $campo="id_ronda";
            $ruta="rondas.index";
            break;
        case "incidencias_tipos":
            $descriptivo="tipo";
            $campo="id_tipo_incidencia";
            $ruta="incidencias_tipos.index";
            break;
        case "incidencias":
            $descriptivo="incidencia";
            $campo="id_incidencia";
            $ruta="incidencias.index";
            break;
        case "encuestas":
            $descriptivo="encuesta";
            $campo="id_encuesta";
            $ruta="encuestas.index";
            break;
        case "ferias":
            $descriptivo="feria";
            $campo="id_feria";
            $ruta="ferias.index";
            break;
        case "contactos":
            $descriptivo="contacto";
            $campo="id_contacto";
            $ruta="contactos.index";
            break;
        case "ferias_marcas":
            $descriptivo="marca";
            $campo="id_marca";
            $ruta="marcas.index";
            break;
        case "festivos":
            $descriptivo="festivo";
            $campo="cod_festivo";
            $ruta="festivos.index";
            break;
        case "turnos":
            $descriptivo="turno";
            $campo="id_turno";
            $ruta="turnos.index";
            break;
        case "departamentos":
            $descriptivo="departamento";
            $campo="cod_departamento";
            $ruta="departamentos.index";
            break;
        case "colectivos":
            $descriptivo="colectivo";
            $campo="cod_colectivo";
            $ruta="colectivos.index";
            break;
        case "trabajos":
            $descriptivo="trabajo";
            $campo="id_trabajo";
            $ruta="trabajos.index";
            break;
        case "grupos_trabajos":
            $descriptivo="grupo de trabajos";
            $campo="id_grupo";
            $ruta="trabajos_grupos.index";
            break;
        case "contratas":
            $descriptivo="contrata";
            $campo="id_contrata";
            $ruta="contratas.index";
            break;
        case "trabajos_planes":
            $descriptivo="plan";
            $campo="id_plan";
            $ruta="trabajos_planes.index";
            break;
        case "puestos":
            $descriptivo="puestos";
            $campo="id_puesto";
            $ruta="puestos.index";
            break;
        case "incidencias":
            $descriptivo="incidencia";
            $campo="id_incidencia";
            $ruta="incidencia.index";
            break;
            
        default:
            $descriptivo=$tabla;
    }

    $c = DB::table($tabla)
        ->where($campo,$id)
        ->when($tabla!='clientes', function($q) use($tabla){
            $q->join('clientes','clientes.id_cliente',$tabla.'.id_cliente');
        })
        ->whereNull('clientes.fec_borrado')
        ->where(function($q){
            if (!isAdmin()){
                $q->orWhereIn('clientes.id_cliente',clientes());
            }
        })
    ->first();
    if(empty($c))
    {
        savebitacora("BLOQUEO DE ACCESO --> ERROR: El ".$descriptivo." ".$id." no existe o no tienes acceso",'validar_acceso_tabla',$tabla);
        flash("ERROR: El ".$descriptivo." ".$id." no existe o no tienes acceso")->error();
        return redirect()->route($ruta);
    }
}

//Devuelve texto blanco o negro en funcion del color sobre el que esta
function txt_blanco($hex) {
    // returns brightness value from 0 to 255
    // strip off any leading #
    try{
        $hex = str_replace('#', '', $hex);

        $c_r = hexdec(substr($hex, 0, 2));
        $c_g = hexdec(substr($hex, 2, 2));
        $c_b = hexdec(substr($hex, 4, 2));

        $result=(($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
        if($result<130){
            return "text-white";
        }
    } catch(\Exception $e){

    }
    
}

function color_porcentaje($pct,$modo="bootstrap"){
    
    if($modo=="bootstrap"){
        if($pct<50){
            return "danger";
        }
    
        if($pct>=50 && $pct<75){
            return "warning";
        }
    
        if($pct>=75 && $pct<100){
            return "info";
        }
    
        if($pct==100){
            return "success";
        }
    
        if($pct>100){
            return "mint";
        }
    }
    if($modo=="hex"){
        if($pct<50){
            return "#ff6347";
        }
    
        if($pct>=50 && $pct<75){
            return "#ffd700";
        }
    
        if($pct>=75 && $pct<100){
            return "#1e90ff";
        }
    
        if($pct==100){
            return "#adff2f";
        }
    
        if($pct>100){
            return "#008000";
        }
    }   
}

function color_porcentaje_inv($pct,$modo="bootstrap"){
    if($modo=="bootstrap"){
        if($pct<50){
            return "success";
        }

        if($pct>=50 && $pct<75){
            return "warning";
        }

        if($pct>=75 && $pct<=100){
            return "danger";
        }
    }
    if($modo=="hex"){
        if($pct<50){
            return "#008000";
        }

        if($pct>=50 && $pct<75){
            return "#ffd700";
        }

        if($pct>=75 && $pct<=100){
            return "#ff6347";
        }
    }
}

function authbyToken($token){
    $usuario=users::where('token_acceso',$token)->first();
    if($usuario){
        Auth::logout();
        Auth::loginUsingId($usuario->id);
        session(['lang' => auth()->user()->lang]);
        //Permisos del usuario
        $permisos = DB::select("
        SELECT
                des_seccion,
                max(mca_read)as mca_read,
                max(mca_write) as mca_write,
                max(mca_create) as mca_create,
                max(mca_delete) as mca_delete
        FROM
            (SELECT
                `permisos_usuarios`.`id_seccion`,
                `permisos_usuarios`.`mca_read`,
                `permisos_usuarios`.`mca_write`,
                `permisos_usuarios`.`mca_create`,
                `permisos_usuarios`.`mca_delete`,
                `secciones`.`des_seccion`
            FROM
                `permisos_usuarios`
                INNER JOIN `secciones` ON (`permisos_usuarios`.`id_seccion` = `secciones`.`cod_seccion`)
            WHERE
                `cod_usuario` = ".auth()->user()->id."
            UNION
            SELECT
                `secciones_perfiles`.`id_seccion`,
                `secciones_perfiles`.`mca_read`,
                `secciones_perfiles`.`mca_write`,
                `secciones_perfiles`.`mca_create`,
                `secciones_perfiles`.`mca_delete`,
                `secciones`.`des_seccion`
            FROM
                `secciones_perfiles`
                INNER JOIN `secciones` ON (`secciones_perfiles`.`id_seccion` = `secciones`.`cod_seccion`)
            WHERE
                id_perfil=".auth()->user()->cod_nivel.") sq
        GROUP BY sq.des_seccion");
        session(['P' => $permisos]);
        return true;
    } else {
        return false;
    }
}

function nombrepuesto($puesto){
    try{
        if(isset(session('CL')['val_campo_puesto_mostrar'])){
            switch (session('CL')['val_campo_puesto_mostrar']) {
                case 'D':
                    return $puesto->des_puesto;
                    break;
                case 'I':
                    return $puesto->cod_puesto;
                    break;
                case 'A':
                    return '['.$puesto->cod_puesto.'] '.$puesto->des_puesto ;
                    break;
                
                default:
                    return '['.$puesto->cod_puesto.'] '.$puesto->des_puesto ;
                    break;
            }
        } else {
            return $puesto->des_puesto;
        }
    } catch(\Exception $e){
        return $puesto->des_puesto;
    }  

}

function config_cliente($clave,$cliente=null){
    try{
        if (!isset($cliente)){
           
            if (!isAdmin()) {
                $cliente=Auth::user()->id_cliente;
            } else {
                $cliente=session('CL')['id_cliente'];
            }
        }
        $config=config_clientes::find($cliente)->$clave;
        return $config;
    } catch (\Exception $e){
        return null;
    }
}    

//Funcion para que los eventos escriban su log
function log_evento($texto,$cod_regla,$tipo="info"){
    DB::table('eventos_log')->insert([
        'fec_log'=>Carbon::now(),
        'txt_log'=>$texto,
        'cod_regla'=>$cod_regla,
        'tip_mensaje'=>$tipo
    ]);
 }




 function dayOfWeek($num){
    $days = array(
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday'
    );
    return $days[$num];
}

//Funciones para la gestion de departamentos
function lista_departamentos($tipo, $id, $r = null){
    global $arr_dep;
    function pintarhijos($dep,$padre){
        global $arr_dep;
        foreach($dep as $d){
            if($d->cod_departamento_padre==$padre){
                $nodo= new \StdClass();
                $nodo=(object) ["cod_departamento"=>$d->cod_departamento,"nom_departamento"=>$d->nom_departamento,"num_nivel"=>$d->num_nivel,"cod_padre"=>$d->cod_departamento_padre,"des_centro"=>"","empleados"=>$d->empleados,"nom_cliente"=>$d->nom_cliente,"cod_cliente"=>$d->cod_cliente];
                $arr_dep[]=$nodo;
                pintarhijos($dep,$d->cod_departamento);
            }
        }
    }
    $dep = DB::table('departamentos')
    ->select('departamentos.cod_departamento', 'departamentos.nom_departamento', 'departamentos.cod_departamento_padre', 'clientes.nom_cliente','clientes.img_logo','clientes.id_cliente as cod_cliente','departamentos.num_nivel')
    ->selectraw('(select count(id) from users where id_departamento=departamentos.cod_departamento) as empleados')
    ->join('clientes','clientes.id_cliente','departamentos.id_cliente')
    ->where('departamentos.cod_departamento','>',0)
    ->whereNull('clientes.fec_borrado')
    ->groupBy('departamentos.cod_departamento',
    'departamentos.nom_departamento',
    'clientes.nom_cliente',
    'departamentos.cod_departamento_padre',
    'departamentos.num_nivel',
    'clientes.img_logo',
    'clientes.id_cliente')
    ->orderby('clientes.id_cliente')
    ->orderby('departamentos.nom_departamento');

    switch($tipo){
        case "cliente":
            $dep = $dep->where('departamentos.id_cliente', $id);
            $dep=$dep->get();
            pintarhijos($dep,0);
        break;
        case "departamento":
            $cliente=departamentos::find($id)->cod_cliente;
            $dep = $dep->where('departamentos.id_cliente', $cliente);
            $dep=$dep->get();
            pintarhijos($dep,$id);
        break;
        case "global":
            $dep = $dep->when(!isadmin(), function($query){
                $query->wherein('departamentos.id_cliente', clientes());
            });

			$dep = $dep->where(function($q) use ($r){
				if (session('cod_cliente'))
					$q->where('departamentos.id_cliente', session('cod_cliente'));
				elseif(!empty($r->cod_cliente))
					$q->wherein('departamentos.id_cliente', $r->cod_cliente);
				else $q->where('departamentos.id_cliente', Auth::user()->id_cliente);
			});
            $dep=$dep->get();
            pintarhijos($dep,0);
        break;
        default:
            return;
    }
    return($arr_dep);
}

function departamentos_hijos($id){
    $data = DB::select("
    with recursive dep (cod_departamento, nom_departamento, num_nivel, cod_departamento_padre) as (
        select     departamentos.cod_departamento,
                   departamentos.nom_departamento,
                   departamentos.num_nivel,
                   departamentos.cod_departamento_padre
        from       departamentos
        where      cod_departamento_padre = ".$id."
        union all
        select     p.cod_departamento,
                   p.nom_departamento,
                   p.num_nivel,
                   p.cod_departamento_padre
        from       departamentos p
        inner join dep
                on p.cod_departamento_padre = dep.cod_departamento
      )
      select * from dep order by cod_departamento_padre;");

      return $data;
}

function departamentos_padres($id,$salida='collect'){
    $data = DB::select("
    with recursive dep (cod_departamento, nom_departamento, num_nivel, cod_departamento_padre) as (
        select     departamentos.cod_departamento,
                     departamentos.nom_departamento,
                   departamentos.num_nivel,
                   departamentos.cod_departamento_padre
        from       departamentos
        where      cod_departamento = ".$id."
        union all
        select     p.cod_departamento,
                   p.nom_departamento,
                   p.num_nivel,
                   p.cod_departamento_padre
        from       departamentos p
        inner join dep
                on p.cod_departamento = dep.cod_departamento_padre
      )
      select * from dep order by cod_departamento_padre;");

      if($salida!='simple'){
        return $data;
      } else {
          return Collect($data)->pluck('cod_departamento')->toarray();
      }
}

function departamentos_centro_hijos($id,$centro,$depth=10,$salida='collect'){
    $data = DB::select("
    with recursive dep (cod_departamento, nom_departamento, num_nivel, id_edificio, cod_departamento_padre,depth) as (
        select     departamentos.cod_departamento,
                   departamentos.nom_departamento,
                   departamentos.num_nivel,
                   ".$centro." as id_edificio,
                   departamentos.cod_departamento_padre,
                   1 as depth
        from       departamentos
        where      cod_departamento_padre = ".$id." 
        union all
        select     p.cod_departamento,
                   p.nom_departamento,
                   p.num_nivel,
                   ".$centro." as id_edificio,
                   p.cod_departamento_padre,
                   dep.depth + 1 as depth
        from       departamentos p
        inner join dep
                on p.cod_departamento_padre = dep.cod_departamento
      )
      select * from dep
      where dep.depth<".$depth." order by cod_departamento_padre");

      if($salida!='simple'){
        return $data;
      } else {
          return Collect($data)->pluck('cod_departamento')->toarray();
      }
}

//Comprobar si un usuario tiene reserva para un dia y un tipo
function comprobar_reserva_usuario($id_user,$fecha,$tipo,$hora_inicio="00:00",$hora_fin="23:59"){
    $usuario=users::find($id_user);
    
    $fec_desde=Carbon::parse(Carbon::parse($fecha)->format('Y-M-d').' '.$hora_inicio.':00');
    $fec_hasta=Carbon::parse(Carbon::parse($fecha)->format('Y-M-d').' '.$hora_fin.':00');
    //Primero comprobamos si tiene una reserva para ese dia de ese tipo de puesto
    $reservas=DB::table('reservas')
    ->join('puestos','puestos.id_puesto','reservas.id_puesto')
    ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
    ->join('users','reservas.id_usuario','users.id')
    ->where('puestos.id_tipo_puesto',$tipo)
    ->where('reservas.id_usuario',$id_user)
    ->where(function($q) use($fec_desde,$fec_hasta,$fecha){
        $q->where(function($q) use($fec_desde,$fec_hasta,$fecha){
            $q->wherenull('fec_fin_reserva');
            $q->where('fec_reserva',adaptar_fecha($fecha));
        });
        $q->orwhere(function($q) use($fec_desde,$fec_hasta){
            $q->whereraw("'".$fec_desde->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
            $q->orwhereraw("'".$fec_hasta->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
            $q->orwherebetween('fec_reserva',[$fec_desde,$fec_hasta]);
            $q->orwherebetween('fec_fin_reserva',[$fec_desde,$fec_hasta]);
        });
    })
    ->where('mca_anulada','N')
    ->where('puestos.id_cliente',$usuario->id_cliente)
    ->first();

    if(isset($reservas)){
        return true;
    }

    //Despues comprobamos si tiene una asignacion para ese dia de ese tipo de puesto
    $asignado=DB::table('puestos_asignados')
        ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
        ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
        ->join('users','puestos_asignados.id_usuario','users.id')
        ->where('puestos.id_tipo_puesto',$tipo)
        ->where('puestos_asignados.id_usuario',$id_user)
        ->where(function($q) use($fec_desde,$fec_hasta){
            $q->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwherenull('fec_hasta');
            });
            $q->orwhereraw("'".$fec_desde->format('Y-m-d')."' between fec_desde AND fec_hasta");
        })
        ->where('puestos.id_cliente',$usuario->id_cliente)
        ->first();

    if(isset($asignado)){
        return true;
    }
    return false;
}

//Lista de puestos disponibles para reserva en un dia y un tipo
function puestos_disponibles($cliente,$fecha,$tipo,$user,$hora_inicio="00:00",$hora_fin="23:59"){
    
    $fec_desde=Carbon::parse(Carbon::parse($fecha)->format('Y-M-d').' '.$hora_inicio.':00');
    $fec_hasta=Carbon::parse(Carbon::parse($fecha)->format('Y-M-d').' '.$hora_fin.':00');
    $datos_tipo=DB::table('puestos_tipos')->where('id_tipo_puesto',$tipo)->first();
    //Primero comprobamos si tiene una reserva para ese dia de ese tipo de puesto
    $reservas=DB::table('reservas')
    ->join('puestos','puestos.id_puesto','reservas.id_puesto')
    ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
    ->where('puestos.id_tipo_puesto',$tipo)
    ->where(function($q) use($fec_desde,$fec_hasta,$fecha){
        $q->where(function($q) use($fec_desde,$fec_hasta,$fecha){
            $q->wherenull('fec_fin_reserva');
            $q->where('fec_reserva',adaptar_fecha($fecha));
        });
        $q->orwhere(function($q) use($fec_desde,$fec_hasta){
            $q->whereraw("'".$fec_desde->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
            $q->orwhereraw("'".$fec_hasta->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
            $q->orwherebetween('fec_reserva',[$fec_desde,$fec_hasta]);
            $q->orwherebetween('fec_fin_reserva',[$fec_desde,$fec_hasta]);
        });
    })
    ->where('mca_anulada','N')
    ->where('puestos.id_cliente',$cliente)
    ->pluck('puestos.id_puesto')
    ->toArray();

    
    //Despues comprobamos si tiene una asignacion para ese dia de ese tipo de puesto
    $asignados=DB::table('puestos_asignados')
        ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
        ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
        ->where('puestos.id_tipo_puesto',$tipo)
        ->where(function($q) use($fec_desde,$fec_hasta){
            $q->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwherenull('fec_hasta');
            });
            $q->orwhereraw("'".$fec_desde->format('Y-m-d')."' between fec_desde AND fec_hasta");
        })
       ->where(function($q) use($user){
            $q->wherenot('puestos_asignados.id_usuario',$user->id);
            $q->where('puestos_asignados.id_perfil',null);
       })
       ->where(function($q) use($user){
            $q->wherenot('puestos_asignados.id_perfil',$user->cod_nivel);
            $q->where('puestos_asignados.id_usuario',null);
        })
        ->where('puestos.id_cliente',$cliente)
        ->pluck('puestos.id_puesto')
        ->toArray();    
    $no_disponibles=array_merge($reservas,$asignados);
    
    $puestos_disponibles=DB::table('puestos')
        ->where('id_tipo_puesto',$tipo)
        ->whereNotIn('id_puesto',$no_disponibles)
        ->where('id_cliente',$cliente)
        ->where(function($q){
            if(session('CL')!=null && session('CL')['mca_incidencia_reserva']=='N'){
                $q->where('mca_incidencia','N');
            }
            
        })
        ->where('mca_reservar','S')
        ->get();
    return $puestos_disponibles;
}

function enviar_mail_reserva($id_reserva,$mca_ical,$sender_name=null){
    $det_reserva=reservas::find($id_reserva);
    $det_puesto=puestos::find($det_reserva->id_puesto);
    $salas=salas::find($det_puesto->id_sala);
    $user=users::find($det_reserva->id_usuario);
    if(isset($salas)){
        $des_evento="Reserva de sala de reunion [".$det_puesto->cod_puesto."] ".$det_puesto->des_puesto;
        $tipo="la sala de reunion";
    } else {
        $des_evento="Reserva de puesto [".$det_puesto->cod_puesto."] ".$det_puesto->des_puesto;
        $tipo="el puesto";
    }
    // $body="Tiene reservado ".$tipo. " [".$det_puesto->cod_puesto."] ".$det_puesto->des_puesto." con el siguiente identificador de reserva: ".$id_reserva;
    // if($id_reserva!=null){
    //     $body.=".\n\n Su reserva anterior ha sido anulada";
    // }
    $subject="Detalles de su reserva con Spotdesking";
    $str_notificacion=$sender_name??$user->name.' ha creado una Reserva  del puesto ['.$det_puesto->cod_puesto.'] '.$det_puesto->des_puesto.' para usted en el periodo  '.beauty_fecha($det_reserva->fec_reserva).' - '.beauty_fecha($det_reserva->fec_fin_reserva).' con identificador #'.$id_reserva;

    if(isset($mca_ical) && $mca_ical=='S'){
        $cal=Calendar::create('Reserva de puestos Spotdesking');
       
        $evento=Event::create()
            ->name($des_evento)
            ->description($str_notificacion)
            ->uniqueIdentifier($id_reserva)
            ->organizer($user->email, $user->name)
            ->createdAt(Carbon::now())
            ->startsAt(Carbon::parse($det_reserva->fec_reserva))
            ->endsAt(Carbon::parse($det_reserva->fec_fin_reserva));
        $cal->event($evento);
        
        $cal=$cal->get();
        $nombre_ical="reserva_".Auth::user()->id."_".Carbon::now()->format('Ymdhi').".ics";
        $attach=Storage::disk('local')->put("ical/".$nombre_ical,$cal);
        if($attach){
            $attach=storage_path('app/ical/'.$nombre_ical);
        }
        //$attach=['nombre'=>"reserva_".Auth::user()->id."_".Carbon::now()->format('Ymdhi').".ics","tipo"=>'text/calendar','dato'=>$cal];
    } else {
        $attach=null;
    }
    notificar_usuario($user,$des_evento,'emails.mail_reserva',$str_notificacion,metodos_notificacion_usuario($user->id),2,$attach,$det_reserva->id_reserva);
}

//Funcion para aplicar los colores de la pagina
function clase_root(){
    try{
        $clase=session('template')->rootClass;
        foreach(explode(",",$clase) as $c){
            echo "hd--".$c." ";
        }
        } catch(\Throwable $e){
        return 'hd--expanded';
    }
}

function clase_body(){
    try{
        return session('template')->layout;
        } catch(\Throwable $e){
        return '';
    }
}

function image_body(){
    try{
        return ' style="margin-bottom: 30px; background-image: '.session('template')->boximg.';"';
        } catch(\Throwable $e){
        return '';
    }
}

function clase_menu(){
    try{
        return session('template')->menu;
        } catch(\Throwable $e){
        return 'mn-max';
    }
}

function clase_sticky(){
    try{
        return session('template')->menu_sticky;
        } catch(\Throwable $e){
        return '';
    }
}

function cuenta_notificaciones(){
    $notificaciones=DB::table('notificaciones')
        ->select('notificaciones.id_notificacion')
        ->join('notificaciones_tipos','notificaciones.id_tipo_notificacion','notificaciones_tipos.id_tipo_notificacion')
        ->where('id_usuario',Auth::user()->id)
        ->where('mca_leida','N')
        ->orderby('notificaciones_tipos.val_prioridad')
        ->orderby('notificaciones.fec_notificacion','desc')
        ->get();

    return $notificaciones;
    
}

//Devuelve para cada dia indicado si el usuario tiene fiesta o no
function estadefiesta($id,$fecha_inicio,$fecha_fin=null,$comprobar_reserva_festivo=1){
    if($fecha_fin==null){
        $fecha_fin=$fecha_inicio;
    }
    //Devolverá un array con los dias indicados y si esta de fiesta o no, en funcion de los festivos del cliente y de la configuracion de sabados/domingos
    // primero buscamos todas las pertenencias del usuario

    $pertenencias=DB::table('users')
        ->selectraw("ifnull(provincias.id_prov,0) as id_provincia,ifnull(provincias.cod_pais,0) as cod_pais,ifnull(provincias.cod_region,0) as cod_region,ifnull(edificios.id_edificio,0) as id_edificio,users.id_cliente,niveles_acceso.mca_reservar_sabados,niveles_acceso.mca_reservar_domingos,niveles_acceso.mca_reservar_festivos")
        ->leftjoin('edificios','users.id_edificio','edificios.id_edificio')
        ->leftjoin('niveles_acceso','users.cod_nivel','niveles_acceso.cod_nivel')
        ->leftjoin('provincias','edificios.id_provincia','provincias.id_prov')
        ->where('users.id',$id)
        ->first();
        

    $festivos=DB::table('festivos')
        ->select('des_festivo')
        ->selectraw("IFNULL(MAX(cod_festivo), 0) as idfestivo,
                    date(val_fecha) as val_fecha")
        ->where(function($q) use($pertenencias){
            $q->whereraw("FIND_IN_SET(CONVERT(IFNULL(".$pertenencias->id_provincia.",-1),char), cod_provincia) <> 0");
            $q->orwhereraw("FIND_IN_SET(CONVERT(IFNULL(".$pertenencias->cod_pais.",-1),char), cod_pais) <> 0");
            $q->orwhereraw("FIND_IN_SET(CONVERT(IFNULL(".$pertenencias->cod_region.",-1),char), cod_region) <> 0");
            $q->orwhereraw("(FIND_IN_SET(CONVERT(IFNULL(".$pertenencias->id_edificio.",-1),char), cod_centro) <> 0  OR (IFNULL(".$pertenencias->id_edificio.",0) = 0))");
        })
        ->wheredate('val_fecha','>=',$fecha_inicio)
        ->wheredate('val_fecha','<=',$fecha_fin)
        ->where('festivos.id_cliente',$pertenencias->id_cliente)
        ->groupby(['val_fecha','des_festivo'])
        ->get();


    
    $resultado=[];
    $periodo=CarbonPeriod::create($fecha_inicio,$fecha_fin);
    foreach($periodo as $fecha){
        $desc=null;
        $es_festivo=0;
        if($festivos->where('val_fecha',$fecha->format('Y-m-d'))->first() && $pertenencias->mca_reservar_festivos=='N'){
            $es_festivo=1;
            $desc=$festivos->where('val_fecha',$fecha->format('Y-m-d'))->first()->des_festivo;
        }
        if($fecha->dayOfWeek==0 && $pertenencias->mca_reservar_domingos=='N'){
            $es_festivo=1;
        }
        if($fecha->dayOfWeek==6 && $pertenencias->mca_reservar_sabados=='N'){
            $es_festivo=1;
        }
        if($festivos->where('val_fecha',$fecha->format('Y-m-d'))->first() && $comprobar_reserva_festivo==0){
            $es_festivo=1;
            $desc=$festivos->where('val_fecha',$fecha->format('Y-m-d'))->first()->des_festivo;
        }
        $item=new \stdClass();
        $item->date=$fecha->format('Y-m-d');
        $item->festivo=$es_festivo;
        $item->desc=$desc;
        $resultado[]=$item;
    }

    
    return $resultado;
}

//Convierte un color de hexadecumal RGB a decimal RGB
function hexToRgb($hex, $alpha = false) {
    $hex      = str_replace('#', '', $hex);
    $length   = strlen($hex);
    $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
    $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
    $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
    if ( $alpha ) {
       $rgb['a'] = $alpha;
    }
    return $rgb;
}

//Ejecuta el NODE.JS que devuelve las siguientes iteraciones de una expresion cron
 function next_cron($expresion,$veces=1,$inicio=null){
    //Metodo 1 usando la libreria de Laravel
     try{
        $cron = new Cron\CronExpression($expresion);
        $resultado=[];
        foreach(range(1,$veces) as $i){
            if($inicio==null){
                $inicio=Carbon::now();
            }
            $inicio=$cron->getNextRunDate($inicio);
            $resultado[]=$inicio->format('Y-m-d H:i:s');
        }
        return $resultado;
     } catch (Exception $e) {
       //Si este falla, opcion node
        $process = new Process(['node', base_path('public/js/cron/cron.js'), $expresion, $veces, $inicio]);
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        //log::debug($process->getOutput());
        return json_decode($process->getOutput());
     }
     
    
}

//Enviar peticiones al API de Salas
function enviar_request_salas($metodo,$accion,$param,$body,$id_cliente){
    $cliente=clientes::find($id_cliente);
    Log::debug($metodo.' '.config('app.url_base_api_salas').$accion);
    $response=Http::withOptions(['verify' => false])
        ->withHeaders(['Accept' => 'application/json', 'Content-Type' => 'application/json','Authorization'=>$cliente->token_acceso_salas])
        ->withbody($body,'application/json')
        ->$metodo(config('app.url_base_api_salas').$accion);
    if($response->status()!=200){
        Log::error('Error en la respuesta de la API de salas: '.$metodo.' '.$accion.' '.$param);
        return [
            "error"=>'Error en la respuesta de la API de salas: '.$metodo.' '.$accion.' '.$param,
            "status"=>$response->status()
           ];
    } 
    return [
        "body"=>$response->body(),
        "status"=>$response->status()
       ];
}

function gitVersion()
{
    // exec('git describe --always',$version_mini_hash);
    // exec('git log -1',$line);
    // $version=Tremby\LaravelGitVersion\GitVersionHelper::getVersion()
    // // $version['short'] = "v1.".trim($version_number[0]);//.".".$version_mini_hash[0];
    // // $version['full'] = "v1.".trim($version_number[0]).".$version_mini_hash[0] (".str_replace('commit ','',$line[0]).")";
    // return $version_mini_hash.' '.$version;
    
}

function getFullSql($query) {
    $sqlStr = $query->toSql();
    foreach ($query->getBindings() as $iter=>$binding) {
  
      $type = gettype($binding);
      switch ($type) {
        case "integer":
        case "double":
          $bindingStr = "$binding";
          break;
        case "string":
          $bindingStr = "'$binding'";
          break;
        case "object":
          $class = get_class($binding);
          switch ($class) {
            case "DateTime":
            case "Illuminate\Support\Carbon":
              $bindingStr = "'" . $binding->format('Y-m-d H:i:s') . "'";
              break;
            default:
              throw new \Throwable("Unexpected binding argument class ($class)");
          }
          break;
        default:
          throw new \Throwable("Unexpected binding argument type ($type)");
         
      }
  
      $currentPos = strpos($sqlStr, '?');
      if ($currentPos === false) {
        throw new \Throwable("Cannot find binding location in Sql String for bundung parameter $binding ($iter)");
      }
  
      $sqlStr = substr($sqlStr, 0, $currentPos) . $bindingStr . substr($sqlStr, $currentPos + 1);
    }
  
    $search = ["select", "distinct", "from", "where", "and", "order by", "asc", "desc", "inner join", "join"];
    $replace = ["SELECT", "DISTINCT", "\n  FROM", "\n    WHERE", "\n    AND", "\n    ORDER BY", "ASC", "DESC", "\n  INNER JOIN", "\n  JOIN"];
    $sqlStr = str_replace($search, $replace, $sqlStr);
  
    return $sqlStr;
  }