@extends('layout_email')
@php
use App\Models\incidencias;
use App\Models\incidencias_acciones;
use App\Models\users;


//$inc=incidencias::find(3);
$accion=incidencias_acciones::where('id_incidencia',$inc->id_incidencia)->where('mca_resuelve','S')->where('fec_accion','>',Carbon\Carbon::parse($inc->fec_cierre)->subMinutes(1))->orderby('num_accion','desc')->first();
if (isset($accion)){
    $user=users::find($accion->id_usuario);
   // $nombre_usuario= $user->name .' ( <a href="mailto:'$user->email.'"> '.$user->email.' </a> )';
   $nombre_usuario="Se ";
} else {
    $nombre_usuario="Se ";
}
$entidad=$inc->id_puesto==0?'solicitud':'incidencia';
$puesto=DB::table('puestos')
->join('edificios','puestos.id_edificio','edificios.id_edificio')
->join('plantas','puestos.id_planta','plantas.id_planta')
->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
->join('clientes','puestos.id_cliente','clientes.id_cliente')
->where('id_puesto',$inc->id_puesto)
->first();

@endphp


@section('logo_cliente')
<img align="left" border="0" src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" alt="Logo cliente" title="Logo cliente" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100px;max-width: 80px;" width="80" class="v-src-width v-src-max-width"/>
@endsection

@section('saludo')
    Hola {{$usuario->name??''}}!
@endsection

@section('titulo')
    <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 20px; line-height: 48px;"><img align="left" border="0" src="{{ url('img/green_check.png') }}" alt="" title="" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 50px;max-width: 50px;" width="50" class="v-src-width v-src-max-width"/>
    <span style="margin-top: 30px; font-weight: bold">&nbsp;&nbsp;&nbsp;Cierre de {{ $entidad }}</span></span></p>
@endsection

@section('cuerpo')
    <p style="font-size: 14px; line-height: 160%;"> </p>
    <p style="font-size: 14px; line-height: 160%;">{{ $nombre_usuario }} ha cerrado la incidencia [{{ $inc->id_incidencia }}] {{ $inc->des_incidencia }}</p></p>
    <br>
    <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:18px"><strong>{!! $inc->comentario_cierre !!}</strong></p>
    <p><br></p>
    <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><strong>Fecha:</strong> {!! beauty_fecha($inc->fec_cierre)!!}</p>
@endsection

@section('enlace')
    @if(isset($inc->url_detalle_incidencia))
        <div class="v-text-align" align="left">
              <a href="{{ $inc->url_detalle_incidencia }}" target="_blank"  rel="noopener noreferrer" class="v-size-width" style="box-sizing: border-box;display: inline-block;font-family:'Rubik',sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #ffffff; background-color: #805997; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; 80px; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;">
                <span style="display:block;padding:10px 20px;line-height:120%;"><strong><span style="font-size: 14px; line-height: 16.8px;">Ver detalle</span></strong></span>
              </a>
        </div>
    @else
        <div class="v-text-align" align="left">
            <a href="{{url('/incidencias/show',$inc->id_incidencia) }}" target="_blank"  rel="noopener noreferrer" class="v-size-width" style="box-sizing: border-box;display: inline-block;font-family:'Rubik',sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #ffffff; background-color: #805997; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; 80px; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;">
            <span style="display:block;padding:10px 20px;line-height:120%;"><strong><span style="font-size: 14px; line-height: 16.8px;">Ver detalle</span></strong></span>
            </a>
        </div>
    @endif
@endsection