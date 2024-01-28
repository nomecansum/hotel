@extends('layout_email')
@php
use App\Models\puestos;
use App\Models\users;

//$inc=incidencias::find(3);

$puesto=DB::table('reservas')
->join('puestos','puestos.id_puesto','reservas.id_puesto')
->join('edificios','puestos.id_edificio','edificios.id_edificio')
->join('plantas','puestos.id_planta','plantas.id_planta')
->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
->join('clientes','puestos.id_cliente','clientes.id_cliente')
->where('id_reserva',$id)
->first();
@endphp


@section('logo_cliente')
<img align="left" border="0" src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" alt="Logo cliente" title="Logo cliente" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100px;max-width: 80px;" width="80" class="v-src-width v-src-max-width"/>
@endsection

@section('saludo')
    Hola {{$user->name}}!
@endsection

@section('titulo')
    <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 20px; line-height: 28px;"><img align="left" border="0" src="{{ url('img/reservation.png') }}" alt="" title="" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 50px;max-width: 50px;" width="50" class="v-src-width v-src-max-width"/>
    <span style="padding-top: 30px; font-weight: bold">{{ $titulo_email??'Reserva de puestos' }}</span></span></p>
@endsection

@section('cuerpo')
    <p style="font-size: 14px; line-height: 160%;"> </p>
    <p style="font-size: 14px; line-height: 160%;">{!! $body !!}</p>
    <br>
    <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:18px"><strong>Puesto:</strong> {{ nombrepuesto($puesto) }}, edificio {{ $puesto->des_edificio  }} | {{ $puesto->des_planta }}</p>
    <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px"><strong>Fecha:</strong> {!! beauty_fecha($puesto->fec_reserva)!!} - {!! beauty_fecha($puesto->fec_fin_reserva)!!}</p>
@endsection

