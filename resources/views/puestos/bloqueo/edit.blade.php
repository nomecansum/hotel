
<style type="text/css">
    .popover {
        z-index: 100000;
    }
</style>

    <div class="card editor mb-5">

        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nuevo tipo de puesto
                    @else
                        Editar tipo de puesto
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
            <form method="POST" action="{{ url('/puestos/tipos/save') }}" id="edit_tipos_puesto_form" name="edit_tipos_puesto_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row mt-2">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-10 {{ $errors->has('des_tipo_puesto') ? 'has-error' : '' }}">
                            <label for="des_tipo_puesto" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_tipo_puesto" type="text" id="dedes_tipo_puestos_edificio" value="{{ old('des_tipo_puesto', optional($tipo)->des_tipo_puesto) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_tipo_puesto', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group col-md-2 {{ $errors->has('abreviatura') ? 'has-error' : '' }}">
                            <label for="abreviatura" class="control-label">Alias</label>
                            <input class="form-control" name="abreviatura" type="text" id="abreviatura_edificio" value="{{ old('abreviatura', optional($tipo)->abreviatura) }}" maxlength="200" placeholder="Enter abreviatura here...">
                            {!! $errors->first('abreviatura', '<p class="help-block">:message</p>') !!}
                        </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                        <label for="id_cliente" class="control-label">Cliente</label>
                        <select class="form-control" required id="id_cliente" name="id_cliente">
                            @foreach ($Clientes as $key => $Cliente)
                                <option value="{{ $key }}" {{ old('id_cliente', optional($tipo)->id_cliente) == $key||$id==0 && $key==session('CL')['id_cliente'] ? 'selected' : '' }}>
                                    {{ $Cliente }}
                                </option>
                            @endforeach
                        </select>
                            
                        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-1">
                        <label for="val_color">Color</label><br>
                        <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($tipo->val_color)?$tipo->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    
                    <div class="form-group col-md-1" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" required name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-align="right" data-placement="inline" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    @if(isAdmin())
                    <div class="col-md-1 p-t-30 ">
                        <div class="form-check">
                            <input name="mca_fijo"  id="mca_fijo" value="S" {{ $tipo->mca_fijo=='S'?'checked':'' }}  class="form-check-input" type="checkbox">
                            <label for="mca_fijo" class="form-check-label">Fijo</label>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="row mt-2">
                    
                    <div class="form-group col-md-2">
                        <label for="max_usos">Cortesia (min)</label><br>
                        <input type="number" autocomplete="off" name="hora_liberar" id="hora_liberar" style="width: 120px" min="0" max="1440"  class="form-control" value="{{$tipo->hora_liberar??config_cliente('hora_liberar_puestos',$tipo->id_cliente)}}" />
                    </div>
                    <div class="form-group col-md-2">
                        <label for="max_usos">Tiempo limpieza(min)</label><br>
                        <input type="number" autocomplete="off" name="val_tiempo_limpieza" id="val_tiempo_limpieza" style="width: 120px" min="0" max="1440"  class="form-control" value="{{$tipo->val_tiempo_limpieza}}" />
                    </div>
                    <div class="form-group col-md-2" >
                        <label for="max_usos">Usos simultaneo</label><br>
                        <input type="number" autocomplete="off" min="1" max="20" style="width: 100px"  name="max_usos" id="max_usos"  class="form-control" value="{{isset($tipo->max_usos)?$tipo->max_usos:1}}" />
                    </div>
                    <div class="form-group col-md-2">
                        <label for="max_usos">Tiempo antelac.(dias)</label><br>
                        <input type="number" autocomplete="off" name="val_dias_antelacion" id="val_dias_antelacion" style="width: 120px" min="0" max="365"  class="form-control" value="{{$tipo->val_dias_antelacion}}" />
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3 p-t-20 mt-2">
                        <div class="form-check pt-1">
                            <input name="mca_liberar_auto"  id="mca_liberar_auto" value="S" {{ $tipo->mca_liberar_auto=='S'?'checked':'' }}  class="form-check-input" type="checkbox">
                            <label for="mca_liberar_auto" class="form-check-label">Liberar reservas AUTO</label>
                        </div>
                    </div>
                    <div class="col-md-3 p-t-20 mt-2">
                        <div class="form-check pt-1">
                            <input name="mca_reserva_masiva"  id="mca_reserva_masiva" value="S" {{ $tipo->mca_reserva_masiva=='S'?'checked':'' }}  class="form-check-input" type="checkbox">
                            <label for="mca_reserva_masiva" class="form-check-label">Permitir reserva supervisores</label>
                        </div>
                    </div>
                    <div class="col-md-3 p-t-20 mt-2">
                        <div class="form-check pt-1">
                            <input name="mca_reserva_multiple"  id="mca_reserva_multiple" value="S" {{ $tipo->mca_reserva_multiple=='S'?'checked':'' }}  class="form-check-input" type="checkbox">
                            <label for="mca_reserva_multiple" class="form-check-label">Reserva multiple</label>
                        </div>
                    </div>
                    <div class="col-md-3 p-t-20 mt-2">
                        <div class="form-check pt-1">
                            <input name="mca_checkin"  id="mca_checkin" value="S" {{ $tipo->mca_checkin=='S'?'checked':'' }}  class="form-check-input" type="checkbox">
                            <label for="mca_checkin" class="form-check-label">Funcion de check-in</label>
                        </div>
                    </div>
                    <div class="col-md-3 p-t-20 mt-2">
                        <div class="form-check pt-1">
                            <input name="mca_antelacion_obligatoria"  id="mca_antelacion_obligatoria" value="S" {{ $tipo->mca_antelacion_obligatoria=='S'?'checked':'' }}  class="form-check-input" type="checkbox">
                            <label for="mca_antelacion_obligatoria" class="form-check-label">Antelacion obligatoria</label>
                        </div>
                    </div>
                </div>
                
                

                
                <div class="card panel-bordered">
                    <div class="card-header">
                        <div class="form-group">
                            <div class="form-check pt-2">
                                <input  name="mca_slots"   id="mca_slots" value="S" {{ isset($tipo->slots_reserva)?'checked':'' }}  class="form-check-input" type="checkbox">
                                <label for="mca_slots" class="form-check-label">Slots de reserva</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body bg-gray-light" id="body_slots" style="{{ !isset($tipo->slots_reserva)?'display:none':'' }}">
                        <div class="row d-flex flex-wrap" id="slots">
                            @if(isset($tipo->slots_reserva))
                                @php
                                    $tipo->slots_reserva=json_decode($tipo->slots_reserva);
                                @endphp
                                @foreach($tipo->slots_reserva as $slot)
                                <div class="form-group text-center p-10 col-md-2 b-all rounded" style="margin-left: 5px; margin-right: 5px">
                                    <div>
                                        <label for="">Inicio</label><br>
                                        <input type="time" autocomplete="off"   name="hora_inicio[]"  class="form-control hora_inicio" value="{{ $slot->hora_inicio }}" />
                                    </div>
                                    <div>
                                        <label for="">Fin</label><br>
                                        <input type="time" autocomplete="off"  name="hora_fin[]"  class="form-control fin" value="{{ $slot->hora_fin }}" />
                                    </div>
                                </div>
                                @endforeach
                            @endif
                            <div class="form-group text-center p-10 col-md-1 rounded" style="margin-left: 10px; border: 1px dashed #ddd" id="div_nuevo" >
                                <div class="text-muted p-t-30">
                                    <label for="">Nuevo</label><br>
                                    <a href="#" class="add_nuevo"><i class="fa-solid fa-circle-plus fa-3x"></i></a>
                                </div>
                            </div> 
                            <div style="display: none" id="editor_nuevo">
                                <div class="form-group text-center p-10 col-md-2 b-all rounded" style="margin-left: 5px; margin-right: 5px">
                                    <div>
                                        <label for="">Inicio</label><br>
                                        <input type="time" autocomplete="off"   name="hora_inicio[]"  class="form-control hora_inicio" value="" />
                                    </div>
                                    <div>
                                        <label for="">Fin</label><br>
                                        <input type="time" autocomplete="off"  name="hora_fin[]"  class="form-control fin" value="" />
                                    </div>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
                
                

                <div class="row ">
                    <div class="form-group col-md-12">
                        <label for="des_tipo_puesto" class="control-label">Observaciones</label>
                        <input class="form-control" name="observaciones" type="text" id="observaciones" value="{{ old('observaciones', optional($tipo)->observaciones) }}" maxlength="200" placeholder="Enter observaciones here...">
                        
                    </div>
                </div>
                

                <div class="form-group mt-3">
                    <div class="col-md-12 text-end">
                        @if(checkPermissions(['Tipos de puesto'],['W']) && ($tipo->mca_fijo!='S' || ($tipo->mca_fijo=='S' && fullAccess()))) <input class="btn btn-primary" type="submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
    $('.form-ajax').submit(form_ajax_submit);

    //$('#frm_contador').on('submit',form_ajax_submit);
    $('#frm_contador').submit(form_ajax_submit);

    $('#val_icono').iconpicker({
        icon:'{{isset($tipo) ? ($tipo->val_icono) : ''}}'
    });

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    $('.add_nuevo').click(function(){
        $($('#editor_nuevo').html()).insertBefore("#div_nuevo");
        //$('#editor_nuevo').show();
    });

    $('#mca_slots').click(function(){
        if($(this).is(':checked')){
            $('#body_slots').show();
        }else{
            $('#body_slots').hide();
        }
    });
    
    $('.fin').change(function(){
        var hora_inicio=moment($(this).parents(':eq(1)').find('.hora_inicio').val(), ['h:m a', 'H:m']);
        var hora_fin=moment($(this).val(), ['h:m a', 'H:m']);
        console.log(hora_inicio+' '+hora_fin);
        if(hora_inicio>hora_fin){
            toast_warning('Hora incorrecta','La hora de inicio no puede ser mayor a la hora de fin');
            $(this).val(hora_inicio.add(1,'hours').format('H:m'));
        }
    });

    
    </script>
