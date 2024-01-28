@extends('layout')




@section('styles')
<link href="{{url('/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">{{ $incidencia->des_incidencia }}</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
	<li class="breadcrumb-item">mantenimiento</li>
	<li class="breadcrumb-item">incidencias</li>
    <li class="breadcrumb-item active">detalle de incidencia</li>
	{{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
</ol>
@endsection

@section('content')

@endsection

@section('scripts')
	

@endsection