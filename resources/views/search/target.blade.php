@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Resultados de busqueda</h1>
@endsection

@section('styles')
    <link href="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet">
    {{--  <link href="{{ asset('/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet">  --}}
    <link href="{{ asset('/plugins/fullcalendar/lib/main.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item"> buscar </li>
        <li class="breadcrumb-item active">"{{ !empty($nombre) ? $nombre : '' }}"</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div id="editorCAM" class="mt-2">

</div>
@endsection


@section('scripts')
    <script src="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/lib/main.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/lib/locales/es.js') }}"></script>
    <script src="{{ asset('/plugins/inputmask/dist/inputmask.js') }}"></script>
    <script src="{{ asset('/plugins/inputmask/dist/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('/plugins/inputmask/dist/bindings/inputmask.binding.js') }}"></script>
    <script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
    <script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.ITEM_MENU').addClass('active');

        $('#editorCAM').load("{{ url('/'.$tipo.'/edit',$id) }}", function(){
			animateCSS('#editorCAM','bounceInRight');
		});
    </script>
@endsection
