


    <div class="card editor">

        <div class="card-header">
            <div class="card-control">
                <button class="btn btn-default" data-panel="dismiss" data-dismiss="panel"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="card-title">Editar feria</h3>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ url("/ferias/update", $ferias->id_feria) }}" id="edit_plantas_form" name="edit_plantas_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
            {{ csrf_field() }}
            @include ('ferias.form', [
                                        'plantas' => $ferias,
                                      ])

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
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