@extends('layout_email')
@php
use App\Models\puestos;
use App\Models\users;

//$inc=incidencias::find(3);

$puesto=DB::table('puestos')
->join('edificios','puestos.id_edificio','edificios.id_edificio')
->join('plantas','puestos.id_planta','plantas.id_planta')
->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
->join('clientes','puestos.id_cliente','clientes.id_cliente')
->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
->where(function($query) use ($id){
    if(is_array($id)){
        $query->whereIn('id_puesto',$id);
    }else{
        $query->where('id_puesto',$id);
    }
})
->get();
@endphp


@section('logo_cliente')
<img align="left" border="0" src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" alt="Logo cliente" title="Logo cliente" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 50px;max-width: 50px;" width="50" class="v-src-width v-src-max-width"/>
@endsection

@section('saludo')
    Hola {{$user->name}}!
@endsection

@section('titulo')
    <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 20px; line-height: 28px;"><img align="left" border="0" src="{{ url('img/workplace.png') }}" alt="" title="" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 50px;max-width: 50px;" width="50" class="v-src-width v-src-max-width"/>
    <span style="padding-top: 30px; font-weight: bold">Asignacion de puesto</span></span></p>
@endsection

@section('cuerpo')
    <p style="font-size: 14px; line-height: 160%;"> </p>
    <p style="font-size: 14px; line-height: 160%;">{{ $body }}</p>
    <br>
    @foreach($puesto as $p)
    <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:18px"><strong>{{ $p->des_tipo_puesto }}:</strong> {{ nombrepuesto($p) }}, edificio {{ $p->des_edificio  }} | {{ $p->des_planta }}</p>`
    @endforeach
    
@endsection