
    <div class="card editor mb-5">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nuevo plan de trabajo
                    @else
                        Editar plan de trabajo
                    @endif
                </h5>
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

            <form method="POST" action="{{ url('/trabajos/planes/save') }}" id="edit_trabajos_cierre_form" name="edit_trabajos_cierre_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_plan') ? 'has-error' : '' }}">
                            <label for="des_plan" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_plan" type="text" id="des_plan" value="{{ old('des_plan', optional($dato)->des_plan) }}" maxlength="500" placeholder="Enter des_plan here...">
                            {!! $errors->first('des_plan', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                            <label for="id_cliente" class="control-label">Cliente</label>
                            <select class="form-control" required id="id_cliente" name="id_cliente">
                                @foreach (lista_clientes() as $cliente)
                                    <option value="{{ $cliente->id_cliente }}" {{ old('id_cliente', optional($dato)->id_cliente) == $cliente->id_cliente||$id==0 && $cliente->id_cliente==session('CL')['id_cliente'] ? 'selected' : '' }}>
                                        {{  $cliente->nom_cliente }}
                                    </option>
                                @endforeach
                            </select>
                                
                            {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                        </div>

                        
                </div>
                <div class="row mt-2 mb-4">
                    <div class="col-md-4">
                        <label for="id_edificio" class="control-label">Edificio</label>
                        <select class="form-control" required id="id_edificio" name="id_edificio">
                                <option value="" style="display: none;" {{ old('id_edificio', optional($dato)->id_edificio ?: '') == '' ? 'selected' : '' }} disabled selected>Enter id edificio here...</option>
                            @foreach ($edificios as $edificio)
                                <option value="{{ $edificio->id_edificio }}" {{ old('id_edificio', optional($dato)->id_edificio) == $edificio->id_edificio ? 'selected' : '' }}>
                                    {{ $edificio->des_edificio }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2 {{ $errors->has('id_trabajo_externo') ? 'has-error' : '' }}">
                        <label for="id_trabajo_externo" class="control-label">ID Externo</label>
                        <input class="form-control" name="id_externo" type="text" id="id_externo" value="{{ old('id_externo', optional($dato)->id_externo) }}" maxlength="100" placeholder="Enter id_externo here...">
                        {!! $errors->first('id_trabajo_externo', '<p class="help-block">:message</p>') !!}
                    </div>
                    
                    <div class="form-group col-md-1" >
                        <label for="val_color">Color</label><br>
                        <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($dato->val_color)?$dato->val_color:''}}" />
                    </div>
                    <div class="form-group col-md-1">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="num_dias_programar" class="control-label">Dias a programar</label>
                        <input class="form-control"   type="number" id="num_dias_programar" name="num_dias_programar" value="{{ old('id_externo', optional($dato)->num_dias_programar) }}" min=1 max=400  placeholder="">
                    </div>
                    <div class="col-md-1" style="padding-top: 30px">
                        <div class="form-check">
                            <input name="mca_activo"  id="mca_activo" value="S" {{ isset($dato->mca_activo)&&$dato->mca_activo=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                            <label class="form-check-label" for="mca_activo">Activo</label>
                        </div>
                    </div>
                </div>
                <h5>Ambito del plan</h5>
                <div class="row mt-2 mb-4 b-all rounded pb-3">
                    <div class="form-group  col-md-12 mt-3">
                        <label>Plantas</label>
                        <div class="input-group select2-bootstrap-append">
                            <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="planta[]" id="multi-planta" all="0" >

                            </select>
                            <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                        </div>
                    </div>
                    <div class="form-group  col-md-12 mt-3">
                        <label>Zonas</label>
                        <div class="input-group select2-bootstrap-append">
                            <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="id_zona[]" id="multi-zonas" >
                                
                            </select>
                            <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                        </div>
                    </div>
                    <div class="form-group  col-md-12 mt-3">
                        <label>Grupos de trabajos</label>
                        <div class="input-group select2-bootstrap-append">
                            <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="id_grupo[]" id="multi-grupos" >
                                @foreach($grupos as $grupo)
                                    <option value="{{$grupo->id_grupo}}">{{$grupo->des_grupo}}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                        </div>
                    </div>
                    <div class="form-group  col-md-12 mt-3">
                        <label>Contratas</label>
                        <div class="input-group select2-bootstrap-append">
                            <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="id_contrata[]" id="multi-contratas" >
                                @foreach($contratas as $contrata)
                                    <option value="{{$contrata->id_contrata}}">{{$contrata->des_contrata}}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 text-end">
                        @if(checkPermissions(['Trabajos'],['W']))<input class="btn btn-primary btn-lg" type="submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
                    </div>
                </div>

                <h4>Detalle del plan</h4>
                <div class="row mt-2 mb-4 b-all rounded pb-3 overflow-auto" id="detalle_plan">
                    
                </div>
            </form>

        </div>
    </div>

    <script>
        $('.form-ajax').submit(form_ajax_submit);

        $(document).ready(function() {
            //console.log($("#multi-planta"));
            $('.scroll-top').click();
            s=setTimeout('end_update_filtros()',1000);
        
        });

        $(".select2").select2();
        

        $('#id_cliente').change(function(){
            $('#id_edificio').load("{{ url('/combos/edificios') }}/"+$(this).val());
        })

        $('#val_icono').iconpicker({
            icon:'{{isset($dato) ? ($dato->val_icono) : ''}}'
        });

        //Carga del plan
        $('.select2-multiple').change(function(){
            $.post('{{url('/trabajos/planes/detalle')}}', {_token:'{{csrf_token()}}',id_plan:{{ $id }},plantas:$('#multi-planta').val(),zonas:$('#multi-zonas').val(),grupos:$('#multi-grupos').val(),contratas:$('#multi-contratas').val()}, function(data, textStatus, xhr) {
                $('#detalle_plan').html(data);
            });
        })

        ///////RECARGA DE COMBOZ DE FILTRO///////////////
        $('#id_edificio').change(function(event) {
            $('#loadfilter').show();
            $('#multi-planta').empty();
            $('#multi-puesto').empty();
            $('#multi-zonas').empty();
            $.post('{{url('/filters/loadplantas')}}', {_token:'{{csrf_token()}}',cliente:[$('#id_cliente').val()],edificio:[$('#id_edificio').val()]}, function(data, textStatus, xhr) {
                cliente_e="";
                edificio_e="";
                $.each(data.plantas, function(index, val) {
                    $('#multi-planta').append('<option value="'+val.id_planta+'">'+val.des_planta+'</option>');
                });
                
                cliente_z="";
                edificio_z="";
                planta_z="";
               
                $.each(data.zonas, function(index, val) {
                    if(planta_z!=val.id_planta){
                        $('#multi-zonas').append('</optgroup><optgroup label="'+val.des_planta+'">');
                        planta_z=val.id_planta;
                    }
                    $('#multi-zonas').append('<option value="'+val.id_zona+'">'+val.des_zona+'</option>');
                });
                $('#loadfilter').hide();
                $('#multi-zonas').trigger('change');
            });
        });

        $('#multi-planta').change(function(event) {
            $('#loadfilter').show();
            $('#multi-puesto').empty();
            $('#multi-zonas').empty();
            $.post('{{url('/filters/loadpuestos')}}', {_token:'{{csrf_token()}}',centros:$(this).val(),cliente:$('#multi-cliente').val(),edificio:$('#multi-edificio').val(),planta:$('#multi-planta').val()}, function(data, textStatus, xhr) {
                cliente_z="";
                edificio_z="";
                planta_z="";
                string="{{ $sel_zonas }}"
                var arr = string.split(',');
                $.each(data.zonas, function(index, val) {
                    if(planta_z!=val.id_planta){
                        $('#multi-zonas').append('</optgroup><optgroup label="'+val.des_planta+'">');
                        planta_z=val.id_planta;
                    }
                    if(arr.includes(val.id_zona.toString())){
                        $('#multi-zonas').append('<option value="'+val.id_zona+'" selected>'+val.des_zona+'</option>');
                    } else {
                        $('#multi-zonas').append('<option value="'+val.id_zona+'">'+val.des_zona+'</option>');
                    }
                });
                $('#loadfilter').hide();
                $('#multi-zonas').trigger('change');

            });
        });

        $('.select-all').click(function(event) {
            $(this).parent().parent().find('select option').prop('selected', true)
            $(this).parent().parent().find('select').select2({
                placeholder: "Todos",
                allowClear: true,
                @desktop width: "90%", @elsedesktop width: "75%", @enddesktop 
            });
            $(this).parent().parent().find('select').change();
        });
        ////////////////////////////////////////////////

        $(function(){
            $('#id_edificio').change();
            $.post('{{url('/trabajos/planes/detalle')}}', {_token:'{{csrf_token()}}',id_plan:{{ $id }},plantas:$('#multi-planta').val(),zonas:$('#multi-zonas').val(),grupos:$('#multi-grupos').val(),contratas:$('#multi-contratas').val()}, function(data, textStatus, xhr) {
                $('#detalle_plan').html(data);
            });
        })

        function end_update_filtros(entidad){
            //window.scrollTo(0,0);
            console.log('end_update');

            string="{{ $sel_plantas }}"
            var arr = string.split(',');
            $('#multi-planta').select2().val(arr);

            string="{{ $sel_zonas }}"
            var arr = string.split(',');
            $('#multi-zonas').select2().val(arr);

            string="{{ $sel_grupos }}"
            var arr = string.split(',');
            $('#multi-grupos').select2().val(arr);

            string="{{ $sel_contratas }}"
            var arr = string.split(',');
            $('#multi-contratas').select2().val(arr);

            $('#multi-planta').select2().val();
            $('#multi-zonas').select2().val();
            $('#multi-grupos').select2().val();
            $('#multi-contratas').select2().val();
            $('.select2-multiple').trigger('change');
            
        }
        

        document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
    </script>