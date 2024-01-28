
    <div class="card editor mb-5">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nueva tarea de trabajos
                    @else
                        Editar tarea de trabajos
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

            <form method="POST" action="{{ url('/trabajos/tipos/save') }}" id="edit_trabajos_cierre_form" name="edit_trabajos_cierre_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="des_trabajo_cierre" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_trabajo" type="text" id="des_trabajo_cierre" value="{{ old('des_trabajo', optional($dato)->des_trabajo) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_trabajo_cierre', '<p class="help-block">:message</p>') !!}
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
                <div class="row mt-2">
                    <div class="form-group col-md-3">
                        <label for="">Tipo de actividad</label>
                        <select class="form-control" required id="id_tipo_trabajo" name="id_tipo_trabajo">
                            @foreach ($tipos as $t)
                                <option value="{{ $t->id_tipo_trabajo }}" {{ old('id_tipo_trabajo', optional($dato)->id_tipo_trabajo) == $t->id_tipo_trabajo ? 'selected' : '' }}>
                                    {{  $t->des_tipo_trabajo }}
                                </option>
                            @endforeach
                        </select>
                            
                        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-4">
                        <label for="">Fecha de efectividad (sin año)</label>
                        <div class="input-group">
                            <input type="text" class="form-control pull-left" id="fechas" name="fechas" value="@if(isset($dato->fec_inicio)){{ Carbon\Carbon::parse($dato->fec_inicio)->format('d/m/Y').' - '.Carbon\Carbon::parse($dato->fec_fin)->format('d/m/Y') }}@endif">
                            <span class="btn input-group-text btn-secondary btn_fechas"  style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                        </div>
                    </div>
                    <div class="form-group col-md-2 {{ $errors->has('id_trabajo_externo') ? 'has-error' : '' }}">
                        <label for="id_trabajo_externo" class="control-label">ID Externo</label>
                        <input class="form-control" name="id_externo" type="text" id="id_externo" value="{{ old('id_externo', optional($dato)->id_externo) }}" maxlength="100" placeholder="Enter id_externo here...">
                        {!! $errors->first('id_trabajo_externo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="row mt-2">
                    
                    <div class="form-group col-md-2 {{ $errors->has('val_operarios') ? 'has-error' : '' }}">
                        <label for="val_operarios" class="control-label">Operarios</label>
                        <input class="form-control" required name="val_operarios" type="number" id="val_operarios" value="{{ old('val_operarios', optional($dato)->val_operarios) }}" maxlength="200" placeholder="Enter val_operarios here...">
                        {!! $errors->first('val_operarios', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-2 {{ $errors->has('val_tiempo') ? 'has-error' : '' }}">
                        <label for="val_tiempo" class="control-label">Tiempo</label>
                        <input class="form-control" required name="val_tiempo" type="number" id="val_tiempo" value="{{ old('val_tiempo', optional($dato)->val_tiempo) }}" placeholder="Enter val_tiempo here...">
                        {!! $errors->first('val_tiempo', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-1" >
                        <label for="val_color">Color</label><br>
                        <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($dato->val_color)?$dato->val_color:''}}" />
                    </div>
                    <div class="form-group col-md-1" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12 text-end">
                        @if(checkPermissions(['Trabajos'],['W']))<input class="btn btn-primary" type="submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        $('.form-ajax').submit(form_ajax_submit);

        //Date range picker
        var rangepicker = new Litepicker({
            element: document.getElementById( "fechas" ),
            singleMode: false,
            @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
            @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
            autoApply: true,
            format: 'DD/MM/YYYY',
            lang: "es-ES",
            tooltipText: {
                one: "day",
                other: "days"
            },
            tooltipNumber: (totalDays) => {
                return totalDays - 1;
            },
            setup: (rangepicker) => {
                rangepicker.on('selected', (date1, date2) => {
                    //comprobar_puestos();
                });
            }
        });

        $('.btn_fechas').click(function(){
            rangepicker.show();
        })

        $(".select2").select2();
        

        

        $('#val_icono').iconpicker({
            icon:'{{isset($dato) ? ($dato->val_icono) : ''}}'
        });
        document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
    </script>