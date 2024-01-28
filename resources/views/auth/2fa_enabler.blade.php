
@extends('layout')

@section('content')
<section id="content" class="content">
    <div class="content__boxed w-100 min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <div class="content__wrap">

            <!-- Login card -->
            <div class="card shadow-lg" style="width: 500px" >
                <div class="card-body">
                    <img src="{{url('/img/Mosaic_brand_300.png')}}" style="width:150px">
                    <div class="text-center">
                        <h1 class="h3">Inicio de sesion</h1>
                        <h5>Se requiere que habilite la autenticacion en dos pasos</h5>
                        @if (!auth()->user()->two_factor_secret)
                            <p>Para ello, deberá descargar en su telefono movil una aplicacion de autentificacion, le recomendamos:</p>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-nowrap"><img src="{{ url('/img/gauth.png') }}" style="width: 40px">Google authenticator:</h5>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-nowrap"><img src="{{ url('/img/mauth.png') }}" style="width: 40px">Microsoft authenticator:</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=es&gl=US" class="btn"><img src="{{ url('/img/play_store.png') }}" style="width: 120px"></a>
                                <a href="https://apps.apple.com/es/app/google-authenticator/id388497605" class="btn"><img src="{{ url('/img/apple_store.png') }}"></a>
                            </div>
                            <div class="col-md-6 text-center">
                                <a href="https://play.google.com/store/apps/details?id=com.azure.authenticator&hl=es&gl=US" class="btn"><img src="{{ url('/img/play_store.png') }}"  style="width: 120px"></a>
                                <a href="https://apps.apple.com/es/app/microsoft-authenticator/id983156458" class="btn"><img src="{{ url('/img/apple_store.png') }}"></a>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                
                                    @if (auth()->user()->two_factor_secret)
                                        <div class="pb-3">
                                            Su cuenta está protegida ahora por algo que usted <b>SABE</b> (su contraseña) y por algo que <b>TIENE</b> (su dispositivo de autenticacin multifactorial)                
                                        </div>
                                        
                                        <div class="pb-3">
                                            <h4>Escanee este codigo con la aplicacion descargada para activar su cuenta</h4>
                                        </div>
                                        <div class="pb-3">
                                            {!! auth()->user()->twoFactorQrCodeSvg() !!}   <br>
                                            {{ decrypt(auth()->user()->two_factor_secret)}}                             
                                        </div>
                                        @if (session('status') == 'two-factor-authentication-confirmed')
                                            <div class="mb-4 font-medium text-sm alert alert-success">
                                                ¡Autenticacion de doble factor confirmada!
                                            </div>
                                        @else
                                            <div class="pb-3 text-center">
                                                <form method="post" action="{{ route('two-factor.confirm') }}">
                                                    @csrf
                                                    <div class="col-md-12">
                                                        Verifique la activacion, con el codigo que le muestra la aplicacion:
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-2"></div>
                                                        <div class="col-md-8">
                                                            <div class="input-group mb-3">
                                                                <input type="text" name="code"   id="code"  class="form-control">
                                                                <div class="input-group-btn">
                                                                    <button class="btn btn-warning" type="submit">Verificar</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        <div class="pb-3">
                                            A continuacion se muestran los codigos de respaldo, ¡guardelos en lugar seguro!. Estos codigos no se volverán a mostrar. Puede utilizarlos como ultimo recurso para acceder a su cuenta si por algun motivo pierde su dispositivo o los codigos proporcionados por el dispositivo no fucionan. Cada codigo de respaldo solo funcionará <b>UNA VEZ</b>      
                                        </div>
                                        <div class="mt-4">
                                            <h3>Recovery Codes</h3>
                                            <ul class="list-group mb-2">
                                                @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes)) as $code)
                                                <li class="list-group-item">{{ $code }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <form method="post" action="/user/two-factor-authentication">
                                            @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger">
                                                    Deshabilitar
                                                </button>
                                        </form>
                                    @else
                                        <form method="post" action="/user/two-factor-authentication">
                                            @csrf
                                                <button class="btn btn-success">
                                                    Habilitar
                                                </button>
                                        </form>
                                    @endif
                                
                            </div>
                        </div>
                    </div>

                    

                </div>
            </div>
            <!-- END : Login card -->
</section>

@endsection

@section('scripts')
<script>
    $('.mainnav__menu').hide();
    $('#root').addClass('mn--min');
</script>
@endsection


