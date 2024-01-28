@php
    use Carbon\Carbon;
    $edificios=$datos->pluck('des_edificio','id_edificio')->unique();
@endphp
<div class="row" style="margin-top: 4rem">
    <div class="col-md-2 mt-2">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" {{ session('tipo_vista')=='card' ||  session('tipo_vista')=='undefined'?'checked':'' }}>
            <label class="btn btn-outline-primary btn-xs boton_modo" onclick="loadDia(fecha_actual,'card')" data-href="comprobar" for="btnradio1"><i class="fa-regular fa-credit-card-blank"></i> Tarjetas</label>
            
            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" {{ session('tipo_vista')=='table'?'checked':'' }}>
            <label class="btn btn-outline-primary btn-xs boton_modo"  onclick="loadDia(fecha_actual,'table')" data-href="comprobar_plano" for="btnradio2" ><i class="fa-light fa-table"></i> Tabla</label>
        </div>
    </div>
    <div class="col-md-10">
        <h3 class="mt-2 float-end w-100 text-end">{{ $fecha->isoFormat('dddd D [de] MMMM [de] YYYY') }}</h2>
    </div>
</div>


@if(!isset($vista) || (isset($vista)&&$vista=="card"))
    @foreach($edificios as $id_edificio=>$des_edificio)
        <h1 class="mt-3"><i class="fad fa-building"></i> {{ $des_edificio }}</h1>
        @php
            $plantas=$datos->where('id_edificio',$id_edificio)->wherenotnull('des_planta')->pluck('des_planta','id_planta')->unique();
        @endphp
        @foreach($plantas as $id_planta=>$des_planta)

            <h3 class="mt-3"><i class="fa-solid fa-layer-group"></i> {{ $des_planta }}</h3>
            @php
                $trabajos=$datos->where('id_planta',$id_planta)->where('id_edificio',$id_edificio);
            @endphp
            @foreach($trabajos as $item)
            <div class="col-md-4 col-lg-3">
                <div class="card text-center mb-3 mb-md-0">
                    <div class="card-body">

                        <div class="h1 my-4"><i class="{{ $item->icono_trabajo }} display-3 text-head text-opacity-20"></i></div>
                        <p class="h4">{{  $item->des_trabajo }} </p>
                        <p class="text-head fw-semibold">
                            <i class="fa-solid fa-person-simple add-tooltip " title="{{ $item->num_operarios }} Operarios asignados"></i> {{ $item->num_operarios }}
                            <i class="fa-regular fa-stopwatch add-tooltip ml-3" title="Duracion total {{ $item->val_tiempo }} minuos"></i> {{ $item->val_tiempo }}'
                            <i class="fa-regular fa-clock add-tooltip ml-3" title="Programado para las {{ Carbon::parse($item->fec_programada)->format('H:i') }}"></i> {{ Carbon::parse($item->fec_programada)->format('H:i') }}
                        </p>
                        <small class="d-block text-muted my-3">
                            @if($item->txt_observaciones!=null)
                                <div class="btn_notas" data-id="{{ $item->id_trabajo_plan }}" {{ $item->fec_fin==null?"data-add=1":'' }}><i class="fa-duotone fa-notes"></i><b>Notas del supervisor:</b> {!! substr($item->txt_observaciones,0,100) !!}<br></div>
                            @endif
                        </small>
                        <small class="d-block text-muted my-3">
                            @if($item->observaciones!=null)
                                <div class="btn_comentarios" data-id="{{ $item->id_programacion }}" {{ $item->fec_fin==null?"data-add=1":'' }}><i class="fa-duotone fa-comments"></i> {!! substr($item->observaciones,0,100) !!}<br></div>
                            @elseif(session('id_operario')!=null && $item->fec_fin==null)
                                <button class="btn btn-light btn_comentarios btn-lg  w-100" data-add="1" data-id="{{ $item->id_programacion }}"><i class="fa-duotone fa-comments"></i> Comentarios</button>
                            @endif
                        </small>
                        <div class="text-center text-info mb-2">
                            @if($item->fec_inicio!=null)
                            <i class="fa-solid fa-play"></i> {{ $item->nom_operario_ini }}<br>
                                {{ Carbon::parse($item->fec_inicio)->format('d/m/Y H:i') }}
                            @elseif(session('id_operario')!=null)
                                <button class="btn btn-info btn_accion btn-lg  w-100 " data-accion="iniciar" data-id="{{ $item->id_programacion }}"><i class="fa-solid fa-play"></i> Iniciar trabajo</button>
                                <span class="ack text-info font-bold" id="inicio{{ $item->id_programacion }}" style="display: none"><i class="fa-solid fa-play"></i> Trabajo iniciado</span>
                            @endif
                        </div>
                        <div class="text-success text-center mb-2">
                            @if($item->fec_fin!=null)
                            <i class="fa-duotone fa-flag-checkered"></i> {{ $item->nom_operario_fin }}<br>
                                {{ Carbon::parse($item->fec_fin)->format('d/m/Y H:i') }}
                            @elseif(session('id_operario')!=null)
                                <button class="btn btn-success btn_accion  btn-lg w-100 "data-accion="finalizar"  data-id="{{ $item->id_programacion }}"><i class="fa-duotone fa-flag-checkered"></i> Fin de trabajo</button>
                                <span class="ack text-success font-bold" id="fin{{ $item->id_programacion }}" style="display: none"><i class="fa-duotone fa-flag-checkered"></i> Trabajo finalizado</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        @endforeach
    @endforeach
@else
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th>Trabajo</th>
                    <th>Edificio</th>
                    <th>Espacio</th>
                    <th class="d-none d-sm-table-cell text-center" style="width: 5%;">Hora</th>
                    <th class="d-none d-sm-table-cell text-center" style="width: 5%;">Operarios</th>
                    <th class="d-none d-sm-table-cell text-center" style="width: 5%;">Duracion</th>
                    <th class="d-none d-sm-table-cell" style="width: 15%;">Inicio</th>
                    <th class="d-none d-sm-table-cell" style="width: 15%;">Fin</th>
                    <th class="d-none d-sm-table-cell" style="width: 15%;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $item)
                <tr>
                    <td class="font-w600">
                        <i class="{{ $item->icono_trabajo }} text-muted mr-1"></i> {{ $item->des_trabajo }}
                    </td>
                    <td class="d-none d-sm-table-cell">
                        <i class="fa-solid fa-building"></i> {{ $item->des_edificio }}
                    </td>
                    <td class="d-none d-sm-table-cell">
                        @if(isset($item->id_planta))
                            <i class="fa-solid fa-layer-group"></i> {{ $item->des_planta }}
                        @else
                            <i class="fa-solid fa-draw-square"></i> {{ $item->des_zona }}

                        @endif
                    </td>
                    <td class="d-none d-sm-table-cell text-start" nowrap>
                        <i class="fa-regular fa-clock add-tooltip" title="Programado para las {{ Carbon::parse($item->fec_programada)->format('H:i') }}"></i> {{ Carbon::parse($item->fec_programada)->format('H:i') }}
                    </td>
                    <td class="d-none d-sm-table-cell text-center">
                        <i class="fa-solid fa-person-simple add-tooltip " title="{{ $item->num_operarios }} Operarios asignados"></i> {{ $item->num_operarios }}
                    </td>
                    <td class="d-none d-sm-table-cell text-center">
                        <i class="fa-regular fa-stopwatch add-tooltip " title="Duracion total {{ $item->val_tiempo }} minuos"></i> {{ $item->val_tiempo }}'
                    </td>
                    <td class="text-info">
                        @if($item->fec_inicio!=null)
                        <i class="fa-solid fa-play"></i><span style="font-size:12px">  {{ $item->nom_operario_ini }}</span><br>
                            {{ Carbon::parse($item->fec_inicio)->format('d/m/Y H:i') }}
                        @elseif(session('id_operario')!=null)
                            <button class="btn btn-info btn_accion btn-sm " data-accion="iniciar" data-id="{{ $item->id_programacion }}"><i class="fa-solid fa-play"></i> Iniciar trabajo</button>
                            <span class="ack text-info font-bold" id="inicio{{ $item->id_programacion }}" style="display: none"><i class="fa-solid fa-play"></i> Trabajo iniciado</span>
                        @endif
                    </td>
                    <td class="text-success">
                        @if($item->fec_fin!=null)
                        <i class="fa-duotone fa-flag-checkered"></i><span style="font-size:12px"> {{ $item->nom_operario_fin }}</span><br>
                            {{ Carbon::parse($item->fec_fin)->format('d/m/Y H:i') }}
                        @elseif(session('id_operario')!=null)
                            <button class="btn btn-success btn_accion  btn-sm "data-accion="finalizar"  data-id="{{ $item->id_programacion }}"><i class="fa-duotone fa-flag-checkered"></i> Fin de trabajo</button>
                            <span class="ack text-success font-bold" id="fin{{ $item->id_programacion }}" style="display: none"><i class="fa-duotone fa-flag-checkered"></i> Trabajo finalizado</span>
                        @endif
                    </td>
                    <td>
                        @if($item->observaciones!=null)
                            <div class="btn_comentarios" data-id="{{ $item->id_programacion }}" {{ $item->fec_fin==null?"data-add=1":'' }} style="font-size: 12px"><i class="fa-duotone fa-comments"></i> {!! substr($item->observaciones,0,50) !!}<br></div>
                        @elseif(session('id_operario')!=null && $item->fec_fin==null)
                            <button class="btn btn-light btn_comentarios btn-sm" data-add="1" data-id="{{ $item->id_programacion }}"><i class="fa-duotone fa-comments"></i> Observaciones</button> 
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

@endif
<div class="modal fade" id="modal-comentarios" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h3 class="modal-title text-nowrap" id="tit_modal_comentarios">Comentarios del trabajo </h3>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>    
            <div class="modal-body">
                <div id="body_comentario">

                </div>
                <div id="form_comentario">
                    <form id="form_comentarios" method="post" class="form-ajax" action="{{ route('trabajos.save_comentarios') }}">
                        @csrf
                        <input type="hidden" name="id" id="id_programacion">
                        <div class="form-group">
                            <label for="observaciones">Comentarios</label>
                            <textarea class="form-control" name="observaciones" id="observaciones" rows="3"></textarea>
                        </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-info" id="btn_guardar_comentario">Guardar</button>
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cerrar</button>
            </div>
        </div>

    </div>
</div>

<script>
    var fecha_actual="{{ Carbon::parse($fecha)->format('Y-m-d') }}";
    $('.form-ajax').submit(form_ajax_submit);
    $('#btn_guardar_comentario').click(function(){
        $('#form_comentarios').submit();
    });

    $('.btn_accion').click(function(){
        iniciador=$(this);
        $.get('{{ url('trabajos/mistrabajos') }}/'+$(this).data('accion')+'/'+$(this).data('id'),function(data){
            if(!data.error){
                toast_ok(data.title,data.message);
                $('#modal').modal('hide');
                $('#modal').on('hidden.bs.modal', function (e) {
                    $('#modal').remove();
                });
                iniciador.next('span').show();
                animateCSS('#'+iniciador.next('span').attr('id'),'animate__rubberBand');
            }else{
                toast_error(data.title,data.error);
            }
        });
        $(this).hide();
    })

    $('.btn_comentarios').click(function(){
        $('#tit_modal_comentarios').html('Comentarios del trabajo ');
        if($(this).data('add')==1){
            $('#form_comentario').show();
            $('#btn_guardar_comentario').show();
            $('#id_programacion').val($(this).data('id'));
        } else {
            $('#form_comentario').hide();
            $('#btn_guardar_comentario').hide();
        }
        $.get('{{ url('trabajos/mistrabajos') }}/comentarios/'+$(this).data('id'),function(data){
            if(!data.error){
                $('#body_comentario').html(data);
                $('#modal-comentarios').modal('show');
            }else{
                toast_error(data.title,data.error);
            }
        });
    })

    $('.btn_notas').click(function(){
        $('#tit_modal_comentarios').html('Notas del supervisor ');
        $('#form_comentario').hide();
            $('#btn_guardar_comentario').hide();
        $.get('{{ url('trabajos/mistrabajos') }}/observaciones/'+$(this).data('id'),function(data){
            if(!data.error){
                $('#body_comentario').html(data);
                $('#modal-comentarios').modal('show');
            }else{
                toast_error(data.title,data.error);
            }
        });
    })

</script>