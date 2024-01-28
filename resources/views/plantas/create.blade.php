
    <div class="card editor mb-5">

        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Nueva planta</h5>
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

            <form method="POST" action="{{ route('plantas.plantas.store') }}" accept-charset="UTF-8" id="create_plantas_form" name="create_plantas_form" class="form-horizontal form-ajax"  enctype="multipart/form-data">
            {{ csrf_field() }}
            @include ('plantas.form', [
                                        'plantas' => null,
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
        $('.demo-psi-cross').click(function(){
            $('.editor').hide();
        });
    </script>
    @include('layouts.scripts_panel')