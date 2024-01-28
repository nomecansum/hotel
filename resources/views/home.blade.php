@extends('layout')

@section('styles')
<style>
    #qr {
        width: 640px;
        border: 1px solid silver
    }
    @media(max-width: 600px) {
        #qr {
            width: 300px;
            border: 1px solid silver
        }
    }
    button:disabled,
    button[disabled]{
      opacity: 0.5;
    }
    .scan-type-region {
        display: block;
        border: 1px solid silver;
        padding: 10px;
        margin: 5px;
        border-radius: 5px;
    }
    .scan-type-region.disabled {
        opacity: 0.5;
    }
    .empty {
        display: block;
        width: 100%;
        height: 20px;
    }
    #qr .placeholder {
        padding: 50px;
    }
    </style>
@endsection

@section('title')
{{--  <h1 class="page-header text-overflow pad-no">Helper Classes</h1>  --}}
@endsection

@if(isset($error_cuenta_no_activada))
    {{-- El usuario aun no tiene la cuenta activada, no esta asociado a un cliente o no tiene perfil --}}
   @section('content')
   <div class="text-center">
    <div class="card col-md-6">
        <h5 class="card-header bg-warning text-white">Cuenta no activada</h5>
        <div class="card-body">
            <p>{{ $error_cuenta_no_activada }}</p>
        </div>
    </div>
   </div> 
   @endsection

   @section('scripts6')
   <script>
        $('#mainnav-menu-wrap').hide();
    </script>
   @endsection
@else

    @section('content')
        <div id="page-head">
            <div class="row">
                <div class="col-md-12 text-center">
                    @if(session('logo_cliente'))
                    <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" class="rounded" style="max-width:400px; width: 10vw" alt="">
                    @else   
                    <img src="{{ url('/img/Mosaic_brand_white.png') }}" style="height: 100px">
                    @endif
                </div>
            </div>
        
            <div class="pad-all text-center text-primary mt-3">
                <div class="text-primary text-3x font-bold">Bienvenido de nuevo {{ Auth::user()->name }}</div>
                <p1>Su ultima visita fue el {!! beauty_fecha(Auth::user()->last_login) !!}<p></p>
            </p1></div>
        </div>
        @include($contenido_home)
    @endsection

    @section('onesignal')
        <script>
            // inicializacion de onesignal
            window.OneSignal = window.OneSignal || [];
            OneSignal.push(function() {
                OneSignal.init({
                    appId: "{{ env('ONESIGNAL_APP_ID') }}",
                    // Bonton de suscripcion de onesignal
                    promptOptions: {
                        customlink: {
                                enabled: true, /* Required to use the Custom Link */
                                style: "link", /* Has value of 'button' or 'link' */
                                size: "small", /* One of 'small', 'medium', or 'large' */
                                color: {
                                button: '#E12D30', /* Color of the button background if style = "button" */
                                text: '#FFFFFF', /* Color of the prompt's text */
                            },
                                text: {
                                subscribe: "Subscribe to push", /* Prompt's text when not subscribed */
                                unsubscribe: "Unsubscribe push", /* Prompt's text when subscribed */
                                explanation: "Get updates from all sorts of things that matter to you", /* Optional text appearing before the prompt button */
                            },
                            unsubscribeEnabled: true, /* Controls whether the prompt is visible after subscription */
                        }
                    }
                });
                OneSignal.setExternalUserId("{{Auth::user()->id}}");
                OneSignal.sendTags({"cliente": "{{Auth::user()->id_cliente}}"});
            });
            
            OneSignal.push(function() {               
                OneSignal.getUserId().then(function(userId) {
                    console.log("User ID:", userId);
                    $.post('{{url('/users/osid')}}', {_token: '{{csrf_token()}}', data: userId}, function(data, textStatus, xhr) {
                
                    })
                });
            });
        </script>

    @endsection
@endif



