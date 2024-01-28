@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de cámaras</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item"><a href="{{url('/camaras')}}">Camaras</a></li>
        <li class="breadcrumb-item active">Camara {{ !empty($camara->etiqueta) ? $camara->etiqueta : '' }}</li>
        {{--  <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="card">
           <div class="card">
               <div class="card-body">
                    <div class="col-md-11">
                        <img id="imgcam" style="width:800px; height:600px" data-id="0" class="rounded_cam imgcam">
                    </div>
                    <div class="col-md-1">
                        <div class="btn-group btn-group-sm pull-right" role="group">
                                <a href="javascript:history.back();" id="btn_nueva_camara" class="btn btn-info" title="Volver">
                                <i class="fa fa-arrow-circle-left pt-2" style="font-size: 20px" aria-hidden="true"></i>
                                <span>Vovler</span>
                            </a>
                        </div>
                    </div>
               </div>
           </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>

    $(function(){
        setTimeout(() => {
            $('#imgcam').attr("src","{{  $url  }}");
        }, 1000);

    })


</script>
@endsection
