@extends('layout_email')
@php
use App\Models\incidencias;
use App\Models\incidencias_tipos;
use App\Models\users;


@endphp


@section('logo_cliente')
<img align="left" border="0" src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" alt="Illustration" title="Illustration" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 25%;max-width: 80px;" width="80" class="v-src-width v-src-max-width"/>
@endsection

@section('saludo')
    Hola {{$user->name}}!
@endsection

@section('cuerpo')
    <p style="font-size: 14px; line-height: 160%;"> </p>
    <p style="font-size: 14px; line-height: 160%;">{{ $body }}</p>
@endsection