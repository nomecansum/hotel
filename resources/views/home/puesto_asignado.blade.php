@php
    $puestos_usu=\App\Http\Controllers\UsersController::mis_puestos(auth()->user()->id);
    
    $mispuestos= $puestos_usu['mispuestos'];
    $reservas= $puestos_usu['reservas'];
    $asignado_usuario= $puestos_usu['asignado_usuario'];
    $asignado_miperfil= $puestos_usu['asignado_miperfil'];
    $asignado_otroperfil= $puestos_usu['asignado_otroperfil'];
    $f1=Carbon\Carbon::now()->startofday();
    $f2=Carbon\Carbon::now()->endofday();
@endphp
@if(isset($mispuestos))
<div class="card mt-3">
    <div class="card-header">
        <div class="card-title"><h2>{{ Carbon\Carbon::now()->locale('es')->isoformat('LLLL') }}</h2></div>
        {{--  ('% %d de %B %Y')  --}}
        
    </div>
    <div class="card-body">
        
        @foreach($mispuestos as $puesto)
            @php
                if(isMobile()){
                    $puesto->factor_puestow=15;
                    $puesto->factor_puestoh=15;
                    $puesto->factor_letra=2.8;
                } else {
                    $puesto->factor_puestow=3.7;
                    $puesto->factor_puestoh=3.7;
                    $puesto->factor_letra=1;
                }
                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();
                $asignado_usuario->name=auth()->user()->name;
                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"home",Carbon\Carbon::now()->format('d/m/Y'));
                if($asignado_usuario->where('id_puesto',$puesto->id_puesto)->count()>0){
                    $es_asignado=true;
                } else {
                    $es_asignado=false;
                }
            @endphp
            <div class="d-flex  hover-this flex-row" data-token="{{ $puesto->token }}" id="puesto{{ $puesto->id_puesto }}" >
                {{-- <div class="col-md-1">
                    <div class="text-center rounded mt-2 add-tooltip puesto_parent bg-{{ $puesto->color_estado }} align-middle flpuesto draggable" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!! nombrepuesto($puesto)." \r\n ".$cuadradito['title'] !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $puesto->id_planta }}" style="height: 3.5vw ; width: 3.5vw; {{ $cuadradito['borde'] }}">
                        <span class="h-100 align-middle text-center puesto_child" style="font-size: 1vw; ">
                                {{ nombrepuesto($puesto) }}
                                @include('resources.adornos_iconos_puesto')
                        </span>
                    </div>
                </div> --}}
                <div class="col-md-1 pt-2 filamipuesto" data-token="{{ $puesto->token }}" >
                    <div class="text-center rounded add-tooltip flpuesto puesto_parent  p-2 " id="puesto{{ $puesto->id_puesto }}" title="{!! strip_tags( nombrepuesto($puesto)." \r\n ".$cuadradito['title']) !!}  @if(config('app.env')=='local')[#{{ $puesto->id_puesto }}]@endif" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" style="height: {{ $puesto->factor_puestoh }}vw ; width: {{ $puesto->factor_puestow }}vw; background-color: {{ $cuadradito['color'] }}; color: {{ $cuadradito['font_color'] }}; {{ $cuadradito['borde'] }}">
                        <div class="puesto_child " style="font-size: {{ $puesto->factor_letra }}vw; color: {{ $cuadradito['font_color'] }};">{{ nombrepuesto($puesto) }}
                            
                        </div>
                        @include('resources.adornos_iconos_puesto')
                    </div>
                </div>
                <div class="col-md-10 filamipuesto" data-token="{{ $puesto->token }}" >
                    <div class="text-primary mt-3">
                        @if(isMobile())<h6> @else <h3> @endif <span style="color: {{ $puesto->color_tipo }}">[<i class="{{ $puesto->icono_tipo }} }}"></i> {!! $puesto->des_tipo_puesto !!}]</span> <i class="fa fa-arrow-right"></i> Tiene el puesto <b>{{ $puesto->cod_puesto }}</b> {!! $cuadradito['title'] !!}@if(isMobile())</h6> @else </h3> @endif
                    </div>
                </div>
                <div class="col-md-1 mt-2">
                    <a class="btn btn-warning btn-liberar" data-bs-toggle="modal" href="#modal-confirmar{{ $es_asignado?'asignado':'noasignado' }}" data-reserva="{{ $reserva->id_reserva??0 }}" data-asignado="{{ $es_asignado?1:0 }}" data-id="{{ $puesto->id_puesto }}" data-nombre="{{ $puesto->cod_puesto }}"> <i class="fa-regular fa-user-slash"></i> Liberar</a>
                </div>
            </div>
            
        @endforeach
    </div>
</div>
@endif

<div class="modal fade" id="modal-confirmarnoasignado" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    @include('resources.spin_puntitos',['id_spin'=>'spin_confirmarnoasignado'])
                    <img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">Cancelar reserva de puesto </h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                Esta seguro de cancelar su reserva del puesto <span class="font-bold nombre_puesto_liberar"></span> para hoy?<br>
                Esta accion no puede deshacerse
            </div>

            <div class="modal-footer">
                <a class="btn btn-info btn_confirmar_cancelacion" href="javascript:void(0)" data-id_puesto="0">Si</a>
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-confirmarasignado" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    @include('resources.spin_puntitos',['id_spin'=>'spin_confirmarasignado'])
                    <img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">Liberar puesto asignado</h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                Seleccione los dias que quiere liberar su puesto permanentemente asignado <span class="font-bold nombre_puesto_liberar"></span><br>
                @include('resources.combo_fechas')
                Esta accion no puede deshacerse
            </div>

            <div class="modal-footer">
                <a class="btn btn-info btn_confirmar_liberacion" href="javascript:void(0)">Si</a>
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
            </div>
        </div>
    </div>
</div>


@section('scripts4')
<script>
    $('.filamipuesto').click(function(){
        window.location.href="{{ url('/puestos/vmapa') }}/"+$(this).data('token');
    })
    var puesto_sel;
    var reserva_sel;
    var nombre_sel;

    $(".btn-liberar").click(function(){
        puesto_sel=$(this).data('id');
        reserva_sel=$(this).data('reserva');
        nombre_sel=$(this).data('nombre');
        $('.nombre_puesto_liberar').html(nombre_sel);
        console.log(puesto_sel);
    })

    $(".btn_confirmar_cancelacion").click(function(){
        $('#spin_confirmarnoasignado').show();
        var id_puesto=puesto_sel;
        $.post('{{ url('reservas/cancelar') }}', {_token: '{{csrf_token()}}',id:reserva_sel,fecha:"{{ \Carbon\Carbon::now()->format('Y-m-d') }}",des_puesto:nombre_sel}, function(data, textStatus, xhr) {
            toast_ok('Reserva cancelada','Se ha cancelado la reserva del puesto '+nombre_sel);
        })
        .always(function(){
            $('#modal-confirmarnoasignado').modal('hide');
            $('#puesto'+id_puesto).remove();
            $('#spin_confirmarnoasignado').hide();
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    });

    $(".btn_confirmar_liberacion").click(function(){
        var id_puesto=puesto_sel;
        $('#spin_confirmarasignado').show();
        $.post('{{ url('users/anular_asignacion_temporal') }}', {_token: '{{csrf_token()}}',id:puesto_sel,fecha: $("#fechas").val(),des_puesto:nombre_sel}, function(data, textStatus, xhr) {
            toast_ok('Puesto liberado',data.message);
        })
        .always(function(){
            $('#modal-confirmarasignado').modal('hide');
            if(data-ocultar==1){
                $('#puesto'+id_puesto).remove();
            }
            $('#spin_confirmarasignado').hide();
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    });
</script>

@endsection
