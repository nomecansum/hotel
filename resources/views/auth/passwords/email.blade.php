
@extends('layout_login')

@section('content')
<section id="content" class="content">
    <div class="content__boxed w-100 min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <div class="content__wrap">
            <!-- Login card -->
            <div class="card shadow-lg">
                <div class="card-body">

                    <div class="text-center">
                        <h1 class="h3">Olvide mi password!</h1>
                        <p>Indique su e-mail para recuperar su password.</p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <input type="email" name="email" required class="form-control"  placeholder="Email" value="" autofocus>
                        </div>
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if ($errors )
                            <div class="mb-4 font-medium text-sm text-red-600">
                               {{ $errors->getBag('default')->first('email') }}
                            </div>
                        @endif
                        <div class="d-grid mt-5">
                            <button class="btn btn-warning btn-lg" type="submit">Reset Password</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ url('/login') }}" class="btn-link text-decoration-none">Volver a login</a>
                    </div>

                </div>
            </div>
            <!-- END : Login card -->
        </div>
    </div>
</section>
@endsection

