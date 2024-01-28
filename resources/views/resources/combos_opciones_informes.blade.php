

<div class="row">
    @if($show['output']==1)
    <div class="col-md-2 form-group" >
        <label>Formato</label><br>
        <div class="btn-group form">
            <button type="button" class="btn btn-sm btn btn-outline-primary btn_output w-100"><i class='fas fa-desktop' style='color: #4682b4'></i> Pantalla</button>
            <button type="button" class="btn btn-sm btn btn-outline-primary dropdown-toggle dropdown-toggle-split dd_output" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" style="font-size: 14px">
                <li><a class="dropdown-item output" href="#" data-value="pantalla"><i class='fas fa-desktop' style='color: #4682b4'></i> Pantalla</a></li>
                <li><a class="dropdown-item output" href="#" data-value="pdf"><i class='fas fa-file-pdf' style='color: #b22222'></i> PDF</a></li>
                <li><a class="dropdown-item output" href="#" data-value="excel"><i class='fas fa-file-excel' style='color: #2e8b57'></i> Excel</a></li>
            </ul>
            <input type="hidden" id="output" name="output" value="pantalla">
        </div>
    </div>
    @endif

    @if($show['orientation']==1)
    <div class="col-md-3 form-group grp_orientacion" style="display:none">
        <label>Orientacion</label><br>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn btn-outline-primary btn_orientation w-100"><i class='far fa-rectangle-landscape'></i> Horizontal</button>
            <button type="button" class="btn btn-sm btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" style="font-size: 14px">
                <li><a class="dropdown-item orientation" href="#" data-value="h"><i class='far fa-rectangle-landscape'></i> Horizontal</a></li>
                <li><a class="dropdown-item orientation" href="#" data-value="v"><i class='far fa-rectangle-portrait'></i> Vertical</a></li>
            </ul>
            <input type="hidden" id="orientation" name="orientation" value="h">
        </div>
    </div>
    @endif
</div>

@section('scripts')
<script>

        $('.output').click(function () {
            console.log($(this).data('value'));
            $('#output').val($(this).data('value'));
            $('.btn_output').html($(this).html());
            if($('#output').val()=="pdf"){
                $('.grp_orientacion').show();
            } else $('.grp_orientacion').hide();
        });

        $('.orientation').click(function () {
            console.log($(this).data('value'));
            $('.btn_orientation').html($(this).html());
            $('#orientation').val($(this).data('value'));
        });

</script>
@endsection