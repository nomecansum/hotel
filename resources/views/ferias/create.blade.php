
    <div class="card editor">

        <div class="card-header">
            <div class="card-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="card-title">Nueva feria</h3>
        </div>

        <div class="card-body">
        
            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="/ferias/save" accept-charset="UTF-8" id="create_plantas_form" name="create_plantas_form" class="form-horizontal form-ajax"  enctype="multipart/form-data">
            {{ csrf_field() }}
            @include ('ferias.form', [
                                        'ferias' => null,
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
        $('.demo-psi-cross').click(function(){
            $('.editor').hide();
        });
    </script>
    @include('layouts.scripts_panel')