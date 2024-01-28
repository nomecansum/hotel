
<div class="row">
    <div class="form-group col-md-5 {{ $errors->has('des_feria') ? 'has-error' : '' }}">
        <label for="des_feria" class="control-label">Nombre</label>
            <input class="form-control" required name="des_feria" type="text" id="des_feria" value="{{ old('des_feria', optional($ferias)->des_feria) }}" maxlength="50" placeholder="Enter des_feria here...">
            {!! $errors->first('des_feria', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="form-group  col-md-3">
        <label for="id_cliente" class="control-label">Fecha</label>
        <div class="input-group float-right" id="div_fechas">
            <input type="text" class="form-control pull-left ml-1" id="fec_feria" name="fec_feria" style="width: 100px" value="{{ Carbon\Carbon::now()->format('d/m/Y') }}">
            <span class="btn input-group-text btn-secondary" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
        </div>
    </div>
    <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
        <label for="id_cliente" class="control-label">Cliente</label>
        <select class="form-control" required id="id_cliente" name="id_cliente">
            @foreach ($Clientes as $key => $Cliente)
                <option value="{{ $key }}" {{ old('id_cliente', optional($ferias)->id_cliente) == $key ? 'selected' : '' }}>
                    {{ $Cliente }}
                </option>
            @endforeach
        </select>
        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
    </div>
</div>


<script type="application/javascript">
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });

    $('#id_cliente').change(function(){
        $('#id_edificio').load("{{ url('/combos/edificios') }}/"+$(this).val());
    })

    $('#fec_feria').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput : true,
            //autoApply: true,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            }
        });
</script>
