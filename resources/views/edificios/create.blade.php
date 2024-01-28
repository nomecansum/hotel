
    <div class="card editor mb-5">

        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Nuevo edificio</h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>

        <div class="card-body">
        
            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('edificios.edificios.store') }}" accept-charset="UTF-8" id="create_edificios_form" name="create_edificios_form" class="form-horizontal form-ajax">
            {{ csrf_field() }}
            @include ('edificios.form', [
                                        'edificios' => null,
                                      ])

                <div class="form-group">
                    <div class="col-md-12 text-end">
                        <input class="btn btn-primary" type="submit" value="Guardar">
                    </div>
                </div>

            </form>

        </div>
    </div>

    <script>
        $('.form-ajax').submit(form_ajax_submit);

        document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
    </script>
@include('layouts.scripts_panel')

