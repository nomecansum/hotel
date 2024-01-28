@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Notificaciones</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">usuario</li>
        <li class="breadcrumb-item">notificaciones</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Notificaciones</h3>
    </div>
    <div class="card-body">
        @foreach($notif as $n)
            <div class="list-group-item list-group-item-action d-flex align-items-start mb-3">
                <div class="flex-shrink-0 me-3">
                    <i class="{{ $n->img_notificacion }} fs-2" style="color:#{{ $n->val_color }}"></i>
                </div>
                <div class="flex-grow-1 ">
                    <div class="d-flex justify-content-between align-items-start">
                        <a href="{{ $n->url_notificacion }}" class="h6 mb-0 stretched-link text-decoration-none">{!! beauty_fecha($n->fec_notificacion) !!} - {{ $n->des_tipo_notificacion }}</a>
                        @if($n->mca_leida=='N')<span class="badge bg-info rounded ms-auto">NEW</span>@endif
                    </div>
                    <small class="text-muted">{!! $n->txt_notificacion !!}</small>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.ITEM_MENU').addClass('active');
    </script>
@endsection
