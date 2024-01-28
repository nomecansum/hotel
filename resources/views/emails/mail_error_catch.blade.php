@extends('layout_email')
@php

@endphp


@section('logo_cliente')
<img align="left" border="0" src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" alt="Logo cliente" title="Logo cliente" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100px;max-width: 80px;" width="80" class="v-src-width v-src-max-width"/>
@endsection

@section('saludo')
    Hola!
@endsection

@section('titulo')
    <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 20px; line-height: 28px;"><img align="left" border="0" src="{{ url('img/logo.png') }}" alt="" title="" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 50px;max-width: 50px;" width="50" class="v-src-width v-src-max-width"/>
    <span style="padding-top: 30px; font-weight: bold">Error en QRClean a las  {{ Carbon\Carbon::now()->format('d/m/Y H:i') }}</span></span></p>
@endsection

@section('cuerpo')
<p style="font-size: 14px; line-height: 160%;"> </p
<p style="font-size: 14px; line-height: 160%;">
   
    <h2>Datos del error</h2>
    @php
        dump('['.$exception->getCode().'] "'.$exception->getMessage().'" on line '.$exception->getTrace()[0]['line'].' of file '.$exception->getTrace()[0]['file']);
    @endphp
    <h2>URL</h2>
    @php
        dump(url()->current());
    @endphp
    <h2>Datos del request</h2>
    @php
        dump($request);
    @endphp
    <h2>Datos del usuario</h2>
    @php
        dump(Auth::user());
    @endphp
</p>
@endsection
