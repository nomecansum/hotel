
    @php
        $entidad=($incidencia->origen)=='S'?'solicitud':'incidencia';

    @endphp
    
    <div class="card">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0"> Detalle de la {{ $entidad }} #{{ $incidencia->id_incidencia }}</h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>

        <div class="card-body" style="font-size: 14px">
            <div class="row">
                <div class="col-md-8"><h4>{{ $incidencia->des_incidencia }}</h4></div>
                <div class="col-md-2"><h5>{!! $incidencia->estado_incidencia !!}</h5></div>
                <div class="col-md-2"><h5>{!! beauty_fecha($incidencia->fec_apertura) !!}</h5></div>
            </div>
            <div class="row">
                <div class="col-md-10 text-nowrap"><b>Abierta por: </b>{{ $incidencia->name??'Cliente' }}</div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="rounded {{ txt_blanco($incidencia->val_color) }}" style="background-color: {{ $incidencia->val_color }}; padding: 4px; "><b>Tipo: &nbsp;&nbsp;&nbsp;</b> <i class="{{ $incidencia->val_icono }} {{ txt_blanco($incidencia->val_color) }}" style=""></i> {{ $incidencia->des_tipo_incidencia }} </div>
                </div>
                @if($incidencia->id_puesto!=0)
                    <div class="col-md-3">
                        <span class="font-bold">Edificio: </span><span>{{ $incidencia->des_edificio }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="font-bold">Planta: </span><span>{{ $incidencia->des_planta }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="font-bold">Puesto: </span><span>{{ $incidencia->cod_puesto }}</span>
                    </div>
                @endif
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    {!! $incidencia->txt_incidencia !!}
                </div>
            </div>
            @if(isset($incidencia->fec_cierre))
            <div class="row  mt-3 bg-gray">
                <div class="col-md-3">
                    <span class="font-bold">Resuelta por: </span><span>{{ isset($incidencia->id_usuario_cierre)?App\Models\users::find($incidencia->id_usuario_cierre)->name:'' }}</span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">Fecha: </span><span>{!! beauty_fecha($incidencia->fec_cierre) !!}</span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">Causa cierre: </span><span>{{ App\Models\causas_cierre::find($incidencia->id_causa_cierre)->des_causa??'' }}</span>
                </div>
                <div class="col-md-12 mt-3">
                    {{ $incidencia->comentario_cierre }}
                </div>
            </div>
            @endif
            @if(isset($incidencia->id_incidencia_salas) && Auth::user()->nivel_acceso==200)
            <div class="row  mt-3 bg-gray">
                <div class="col-md-3">
                    <span class="font-bold">ID incidencia salas: </span><span>{{ $incidencia->id_incidencia_salas }}</span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">ID tipo incidencia salas: </span><span>{{ $incidencia->id_tipo_salas }}</span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">Sala ID: </span><span>{{ App\Models\salas::where('id_puesto',$incidencia->id_puesto)->first()->id_externo_salas??'' }}</span>
                </div>
                <div class="col-md-12 mt-3">
                    {{ $incidencia->comentario_cierre }}
                </div>
            </div>
            @endif
            <div class="row mt-3">
                <div class="col-md-6">
                    @if($incidencia->img_attach1)
                        <img onclick="ampliar('{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$incidencia->img_attach1) }}')" src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$incidencia->img_attach1) }}" style="width: 100%">
                    @endif
                </div>
                <div class="col-md-6">
                    @if($incidencia->img_attach2)
                        <img onclick="ampliar('{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$incidencia->img_attach1) }}')" src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$incidencia->img_attach2) }}" style="width: 100%">
                    @endif
                </div>
            </div>
            

            <div class="row mt-3">
                <!-- Timeline -->
                <!--===================================================-->
                <div class="timeline">
        
                    <!-- Timeline header -->
                    <div class="tl-header">
                        <div class="tl-header-title bg-primary text-white">{!! beauty_fecha($incidencia->fec_apertura) !!}</div>
                    </div>
                    <div class="tl-entry">
                        <div class="tl-time">
                            <div class="tl-date">{!! beauty_fecha($incidencia->fec_apertura) !!}</div>
                            <div class="tl-time text-info">{!! Carbon\Carbon::parse($incidencia->fec_apertura)->format('H:i') !!}</div>
                        </div>
                        <div class="tl-media">
                            @if (isset($incidencia->img_usuario ) && $incidencia->img_usuario!='')
                                <img class="img-xs rounded-circle add-tooltip" title="{{ $incidencia->name }}" src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$incidencia->img_usuario) }}" alt="{{ $incidencia->name }}">
                            @else
                            {!! icono_nombre($incidencia->name,32,14) !!}
                            @endif
                        </div>
                        <div class="tl-content card bg-info text-white">
                            <div class="card-body">
                                <i class="fa-solid fa-circle-exclamation"></i> Apertura de la {{ $entidad }}
                            </div>
                        </div>
                    </div>
                    @foreach($acciones as $accion)
                    <div class="tl-entry">
                        <div class="tl-time">
                            <div class="tl-date">{!! beauty_fecha($accion->fec_accion,0) !!}</div>
                            <div class="tl-time">{!! Carbon\Carbon::parse($accion->fec_accion)->format('H:i') !!}</div>
                        </div>
                        <div class="tl-media">
                            @if (isset($accion->img_usuario ) && $accion->img_usuario!='')
                                <img class="img-xs rounded-circle add-tooltip" src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$accion->img_usuario) }}" alt="{{ $accion->name }}">
                            @else
                            {!! icono_nombre($accion->name,32,14) !!}
                            @endif
                        </div>
                        <div class="tl-content card border-light">
                            <div class="card-body">
                                <span class="btn-link">{{ $accion->name }}</span> <i>{{ $accion->des_accion }}</i>
                                <br>
                                <div class="float-right">
                                    @if(isset($accion->img_attach1)  && $accion->img_attach1!='')<a class="link_imagen" onclick="ampliar('{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$accion->img_attach1) }}')" href="#modal_img_accion" data-toggle="modal" data-src="" ><img  src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$accion->img_attach1) }}" style="height: 100px"></a>@endif
                                    @if(isset($accion->img_attach2)  && $accion->img_attach2!='')<a class="link_imagen" onclick="ampliar('{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$accion->img_attach2) }}')" href="#modal_img_accion" data-toggle="modal" data-src="" ><img  src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$accion->img_attach2) }}" style="height: 100px"></a>@endif
                                </div>
                            </div>
                            @if(isset($accion->id_estado))
                                @php
                                    $estado = App\Models\estados_incidencias::find($accion->id_estado);
                                @endphp
                                <span class="ml-1" style="font-size:10px">Cambio de estado a <span style="color:{{ $estado->val_color }}"><i class="{{ $estado->val_icono }}"></i>  {{ $estado->des_estado }} </span></span>
                            @endif
                        </div>

                    </div>
                    @endforeach
                    @if(isset($incidencia->fec_cierre))
                    @php
                        $usuario_cierre=App\Models\users::find($incidencia->id_usuario_cierre??config('app.id_usuario_tareas'));
                    @endphp
                        <div class="tl-entry">
                            <div class="tl-time">
                                <div class="tl-date">{!! beauty_fecha($incidencia->fec_cierre) !!}</div>
                                <div class="tl-time text-info">{!! Carbon\Carbon::parse($incidencia->fec_cierre)->format('H:i') !!}</div>
                            </div>
                            <div class="tl-media">
                                @if (isset($usuario_cierre->img_usuario ) && $usuario_cierre->img_usuario!='')
                                    <img class="img-xs rounded-circle add-tooltip" title="{{ $usuario_cierre->name }}" src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$usuario_cierre->img_usuario) }}" alt="{{ $usuario_cierre->name }}">
                                @else
                                {!! icono_nombre($usuario_cierre->name,32,14) !!}
                                @endif
                            </div>
                            <div class="tl-content card bg-success text-white">
                                <div class="card-body">
                                    <span class="btn-link">{{ $usuario_cierre->name }}</span> <i>{{ $incidencia->comentario_cierre }}</i>
                                    <br>
                                    <i class="fa-solid fa-circle-check"></i> <b>CIERRE DE LA {{ strtoupper($entidad) }}</b
                                </div>
                            </div>
                        </div>
                    @endif
                    
                </div>
                <!--===================================================-->
                <!-- End Timeline -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_img_accion">
       
            <div class="modal-dialog modal-md">
                <div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <div class="modal-body">
                        <img style="width:100%" id="img_accion">
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-warning" onclick="cerrar_modal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        $('.round').css('line-height','33px');

        function ampliar(src){
            $('#modal_img_accion').modal('show');
            $('#img_accion').attr('src',src);
        }
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        $('*[data-dismiss="panel"]').click(function(){
            $(this).closest('.panel').hide('slow');   
        });

        document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    </script>
    @include('layouts.scripts_panel')