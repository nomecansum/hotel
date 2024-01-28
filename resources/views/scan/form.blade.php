@extends('layout')

@section('title')
   
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .container {
            border: 1px solid #DDDDDD;
            width: 100%;
            position: relative;
            padding: 0px;
        }
        .flpuesto {
            float: left;
            position: absolute;
            z-index: 1000;
            color: #FFFFFF;
            font-size: 9px;
            width: 40px;
            height: 40px;
            overflow: hidden;
        }
        .blink_me {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
        
    </style>
    <link href="{{url('/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
@endsection

@section('breadcrumb')

@endsection

@section('content')
@php
    $puesto=$respuesta['puesto']??null;
    $cookie=Cookie::get('encuesta');
    //dump($mireserva);
@endphp
    
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center text-white">
            @if(isset($puesto) && ($puesto->img_logo!=null))
            <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$puesto->img_logo) }}" style="width: 13vw" alt="" onerror="this.src='{{ url('/img/logo.png') }}';">
            @else
            <h2>{{ $puesto->nom_cliente??'' }}</h2>
            @endif
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row" style="margin-top: 70px">
        <div class="col-md-12 text-center">
            @if(isset($puesto))
            <div class="pad-all text-center font-bold" style="color: {{ $puesto->val_color }}; font-size: 22px">
                @if(config('app.env')=='local') [{{ $puesto->id_puesto }}] @endif<i class="fa-duotone fa-building"></i> {{ $puesto->des_edificio }} <i class="fa-duotone fa-layer-group"></i> {{ $puesto->des_planta }}  <i class="{{ $puesto->val_icono }}"></i>  {{ nombrepuesto($puesto) }}
            </div>
            @endif
        </div>
    </div>
    <div class="row" id="div_respuesta">
       
    </div>

    @if(isset($respuesta) && $respuesta['encuesta']!=0 && (!isset($cookie) || (isset($cookie) && $cookie!=$respuesta['encuesta'])))
        @php
            $encuesta=DB::table('encuestas')->where('id_encuesta',$respuesta['encuesta'])->first();
        @endphp
        <div class="row" id="div_encuesta"  @if($encuesta->val_momento=='D') style="display: none" @endif>
            <div class="col-md-12 text-center" id="pregunta">
                <h4>{!! $encuesta->pregunta !!}</h4>
            </div>
            <div class="col-md-12 text-center" id="selector">
                @include('encuestas.selector',['tipo'=>$encuesta->id_tipo_encuesta,'comentarios'=>$encuesta->mca_mostrar_comentarios])
            </div>
            <div class="col-md-12 text-center"  id="respuesta" style="display: none">
                <h4><i class="fad fa-thumbs-up fa-2x text-success"></i> ¡Muchas gracias por su colaboracion!</h4>
            </div>
        </div>
        
    @endif
    
   
    @if(isset($puesto))
        
        <div id="div_botones">

                <div class="row mt-5 mb-5">
                    <div class="col-md-12 pt-3 pb-3 fs-2 text-center">
                        ¿{{ $mireserva->nombre??'' }} Que quiere hacer?
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-lg btn-primary text-bold btn_solicitud" data-id="{{$puesto->token}}" style="width: 250px"><i class="fa-duotone fa-square-question"></i> Relizar una solicitud</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-lg btn-warning text-bold btn_incidencia"  data-id="{{$puesto->token}}" style="width: 250px"><i class="fad fa-exclamation-triangle"></i> Notificar una incidencia</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-lg btn-secondary text-bold btn_revisar"  data-id="{{$puesto->token}}" style="width: 250px"><i class="fa-duotone fa-circle-info"></i> Estado de mis solicitudes</button>
                    </div>
                </div>

            
           
            {{-- <div class="row">
                <div class="col-md-12 text-center mt-3">
                    <a class="btn btn-primary rounded btn_otravez" href="{{ url('/scan_usuario/') }} " style="width: 250px"><i class="fad fa-qrcode"></i> Escanear otra vez</a>
                </div>
            </div> --}}
        </div>
    @endif
    <div class="row mt-3" id="boton_home" style="display:none">
        <div class="col-md-12 text-center">
            <a class="btn btn-lg btn-secondary fs-2 rounded btn_home" href="{{ url('/') }} "><i class="fa fa-home"></i> Inicio</a>
        </div>
    </div>
@endsection


@section('scripts')
    <script type="text/javascript"  src="{{url('/plugins/dropzone/dropzone.js')}}"></script>
    <script>
        

        $('.btn_incidencia').click(function(){
           console.log('Abir incidencia');
           $('#div_respuesta').load("{{ url('/incidencia/'.$puesto->token.'/'.$mireserva->pin) }}", function(){
                animateCSS('#div_respuesta','bounceInRight');
            });
        })

        $('.btn_solicitud').click(function(){
           console.log('Abir incidencia');
           $('#div_respuesta').load("{{ url('/solicitud/'.$puesto->token.'/'.$mireserva->pin) }}", function(){
                animateCSS('#div_respuesta','bounceInRight');
            });
        })

        $('.btn_revisar').click(function(){
           console.log('Abir incidencia');
           $('#div_respuesta').load("{{ url('/incidencia/listado_clientes/'.$puesto->token.'/'.$mireserva->pin) }}", function(){
                animateCSS('#div_respuesta','bounceInRight');
            });
        })

    

        $(function(){
            $('#footer').hide();
            // setTimeout(function(){
            //     window.location.href = '/';
            // }, 90000);
        })

        @if($respuesta['encuesta']!=0 && (!isset($cookie) || (isset($cookie) && $cookie!=$respuesta['encuesta'])))
            //Scripts para manejar la encuesta
            id_encuesta='{{ $encuesta->token }}';
            mca_anonima='{{ $encuesta->mca_anonima }}';
            $('.valor').click(function(){
                $(this).css('background-color','#7fff00')
                console.log($(this).data('value'));
                $.post('{{url('/encuestas/save_data')}}', {_token:'{{csrf_token()}}',val: $(this).data('value'), id_encuesta: id_encuesta, mca_anonima: mca_anonima,comentario: $('#comentario').val()}, function(data, textStatus, xhr) {
                    console.log(data);
                    $('#selector').hide();
                    $('#pregunta').hide();
                    $('#respuesta').show();
                    animateCSS('#respuesta','bounceInRight');
                });
            })
            $('.valor').css('cursor', 'pointer');
        @endif
    </script>
@endsection
