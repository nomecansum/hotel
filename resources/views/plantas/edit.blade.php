


    <div class="card editor mb-5">

        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Editar planta</h5>
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

            <form method="POST" action="{{ route('plantas.plantas.update', $plantas->id_planta) }}" id="edit_plantas_form" name="edit_plantas_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
            {{ csrf_field() }}
            <input name="_method" type="hidden" value="PUT">
            @include ('plantas.form', [
                                        'plantas' => $plantas,
                                      ])

                <div class="form-group">
                    <div class="col-md-12 text-end mt-3">
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