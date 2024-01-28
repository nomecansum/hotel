
    <div class="card editor mb-5">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nuevo grupo de trabajos
                    @else
                        Editar grupo de trabajos
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

            <form method="POST" action="{{ url('/trabajos/grupos/save') }}" id="edit_trabajos_cierre_form" name="edit_trabajos_cierre_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="val_estructura" id="val_estructura"  value="{{ $dato->val_estructura }}">
                        <input type="hidden" name="nestedset" id="nestedset"  value="">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="des_trabajo_cierre" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_trabajo" type="text" id="des_trabajo_cierre" value="{{ old('des_trabajo', optional($dato)->des_grupo) }}" maxlength="200" placeholder="Enter nombre here...">
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
                <div class="row mt-2 mb-4">
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
                    <div class="form-group col-md-2 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                        <label for="val_operarios" class="control-label">Operarios</label>
                        <input class="form-control" required name="val_operarios" type="number" id="val_operarios" value="{{ old('val_operarios', optional($dato)->val_operarios) }}" maxlength="200" placeholder="Enter num_asignados_def here...">
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
                    <div class="form-group col-md-1">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- grid column -->
                    <div class="col-md-6">
                      <!-- .card -->
                      <div class="card">
                        <h3 id="nombre_grupo"> <i class="{{ optional($dato)->val_icono }}"></i> {{ optional($dato)->des_grupo }} </h3><!-- .nestable -->
                        <div id="destino" class="dd" data-toggle="nestable" data-group="1" data-max-depth="3">
                          <!-- .dd-list -->
                          
                        </div><!-- /.nestable -->
                      </div><!-- /.card -->
                    </div><!-- /grid column -->
                    <!-- grid column -->
                    <div class="col-md-6">
                      <!-- .card -->
                      <div class="card">
                        <h4> Tareas disponibles </h4><!-- .nestable -->
                        <div id="origen" class="dd" data-toggle="nestable" data-group="1" data-max-depth="1">
                          <!-- .dd-list -->
                          <ol class="dd-list">
                            @foreach($tareas as $tarea)
                            <li class="dd-item" data-id="{{ $tarea->id_trabajo }}" data-icon="{{ $tarea->val_icono }}" data-des="{{ $tarea->des_trabajo }}" data-operarios="{{ $tarea->val_operarios }}" data-tiempo="{{ $tarea->val_tiempo }}" data-bgcolor="{{ $tarea->val_color }}" data-fontcolor="{{ txt_blanco($tarea->val_color) }}">
                              <div id="{{ $tarea->id_trabajo }}" class="dd-handle {{ txt_blanco($tarea->val_color) }}" style="background-color: {{ $tarea->val_color }}">
                                <span class="drag-indicator"></span>
                                <div class="row">
                                    <div class="col-md-7">
                                        <i class="{{ $tarea->val_icono }}"></i> {{ $tarea->des_trabajo }}
                                    </div>
                                    <div class="col-md-2  text-end">
                                        <i class="fa-solid fa-person-simple"></i> {{ $tarea->val_operarios }}
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <i class="fa-regular fa-stopwatch"></i> {{ $tarea->val_tiempo }}'
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <a href="#" class="btn_del_tarea" onclick="delete_node({{ $tarea->id_trabajo }})" data-id="{{ $tarea->id_trabajo }}" style="display:none"><i class="fa-regular fa-trash"></i></a>
                                    </div>

                                </div>
                              </div>
                              @endforeach
                            </li>
                          </ol><!-- /.dd-list -->
                        </div><!-- /.nestable -->
                      </div><!-- /.card -->
                    </div><!-- /grid column -->
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

        function updateOutput(e){
            console.log('updateOutput');
            $('#val_estructura').val(JSON.stringify($('#destino').nestable('serialize')));
            $('#nestedset').val(JSON.stringify($('#destino').nestable('asNestedSet')));
        }

        $('#destino').nestable({
            @if(isjson($dato->val_estructura)) json:'{!! $dato->val_estructura !!}', @endif
            maxDepth: 3,
            scroll: true,
            effect: { animation: 'fade', time: 'slow'},
            callback: function(l,e){
                // l is the main container
                // e is the element that was moved
                $('#destino').find('.btn_del_tarea').show();
                $('#val_estructura').val(JSON.stringify($('#destino').nestable('serialize')));
                $('#nestedset').val(JSON.stringify($('#destino').nestable('asNestedSet')));
            }
        });

        if('{!! $dato->val_estructura !!}'!=''){
            
            $('#destino').find('li').each(function(item){
                console.log($(this).find('div'));
                $(this).find('div').css('background-color',$(this).data('bgcolor'));
                $(this).find('div').attr('id',$(this).data('id'));
                html="<div class='row'><div class='col-md-7 "+$(this).data('fontcolor')+"'><i class='"+$(this).data('icon')+"'></i>"+$(this).data('des')+"</div>";
                html+="<div class='col-md-2  text-end "+$(this).data('fontcolor')+"'><i class='fa-solid fa-person-simple'></i>"+$(this).data('operarios')+"</div>";
                html+="<div class='col-md-2 text-end "+$(this).data('fontcolor')+"'><i class='fa-regular fa-stopwatch'></i> "+$(this).data('tiempo')+"'</div>";
                html+="<div class='col-md-1 text-end'><a href='#' class='btn_del_tarea' onclick='delete_node("+$(this).data('id')+")' data-id='"+$(this).data('id')+"' style='display:none'><i class='fa-regular fa-trash'></i></a></div></div>";
                $(this).find('div').html(html);
            });
            $('#nestedset').val(JSON.stringify($('#destino').nestable('asNestedSet')));
        }

        

        $('#origen').nestable({
            maxDepth: 1,
            scroll: true,
            effect: { animation: 'fade', time: 'slow'},
            callback: function(l,e){
                $('#val_estructura').val(JSON.stringify($('#destino').nestable('serialize')));
                $('#nestedset').val(JSON.stringify($('#destino').nestable('asNestedSet')));
            }
        });


        
        function delete_node(id){
            console.log(id);
            destino.nestable('remove', id);
        }
        
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