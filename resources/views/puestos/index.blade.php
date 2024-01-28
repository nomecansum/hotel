@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de puestos</h1>
@endsection

@section('styles')
    <link href="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet">
    {{--  <link href="{{ asset('/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet">  --}}
    <link href="{{ asset('/plugins/fullcalendar/lib/main.css') }}" rel="stylesheet">
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
    
	{{--  <link href="{{ asset('/plugins/fullcalendar/nifty-skin/fullcalendar-nifty.min.css') }}" rel="stylesheet">  --}}
    <style type="text/css">
        td .tooltip {
            position:absolute
        }
        .enfrente {
            z-index: 1100;
        }

    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">espacios</li>
        <li class="breadcrumb-item active">puestos</li>
        {{--  <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection

@section('content')
    
    <div class="row botones_accion">
        <div class="col-md-8">

        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group mr-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fad fa-poll-people pt-2" aria-hidden="true"></i> Acciones</button>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="" id="dropdown-acciones">
                        @if(checkPermissions(['Puestos'],['W']))
                            <li class="dropdown-header">Cambiar estado</li>
                            <li><a href="#" data-estado="1" class="btn_estado_check dropdown-item"><i class="fas fa-square text-success"></i> Disponible</a></li>
                            <li><a href="#" data-estado="2" class="btn_estado_check dropdown-item"><i class="fas fa-square text-danger"></i> Usado</a></li>
                            @if(session('CL')['mca_limpieza']=='S')<li><a href="#" data-estado="3" class="btn_estado_check dropdown-item"><i class="fas fa-square text-info"></i> Limpieza</a></li>@endif
                            <li><a href="#" data-estado="6" class="btn_estado_check dropdown-item"><i class="fas fa-square text-warning"></i> Incidencia</a></li>
                            <li><a href="#" data-estado="5" class="btn_estado_check dropdown-item"><i class="fas fa-square text-secondary"></i> Bloqueado</a></li>
                            <li><a href="#" data-estado="7" class="btn_estado_check dropdown-item"><i class="fas fa-square text-secondary"></i> No usable (encuesta)</a></li>
                        @endif
                        <li class="divider"></li>
                        <li class="dropdown-header">Atributos</li>
                        @if(checkPermissions(['Puestos'],['W']))<li><a href="#anonimo-puesto" class="btn_anonimo btn_toggle_dropdown dropdown-item" data-toggle="modal" data-tipo="M"><i class="fad fa-user-secret"></i></i> Habilitar acceso anonimo</a> </li>@endif
                        @if(checkPermissions(['Puestos'],['W']))<li><a href="#reserva-puesto" class="btn_reserva btn_toggle_dropdown dropdown-item" data-toggle="modal" data-tipo="M"><i class="fad fa-calendar-alt"></i> Habilitar reserva</a></li>@endif
                        @if(checkPermissions(['Puestos'],['W']))<li><a href="#modificar-puesto" class="btn_modificar_puestos btn_toggle_dropdown dropdown-item" data-toggle="modal" data-tipo="M"><i class="fad fa-pencil"></i> Modificar puestos</a></li>@endif
                        @if(checkPermissions(['Reservas'],['C']) && checkPermissions(['Reservas global'],['C']))<li><a href="#modal-reservas" class="btn_crear_reservas btn_toggle_dropdown btn_modal_reserva dropdown-item" data-toggle="modal" data-tipo="C" data-accion="Crear"><i class="fad fa-calendar-alt"></i> Crear reserva</a></li>@endif
                        @if(checkPermissions(['Reservas'],['D']) && checkPermissions(['Reservas global'],['D']))<li><a href="#modal-reservas" class="btn_cancelar_reservas btn_toggle_dropdown btn_modal_reserva dropdown-item" data-toggle="modal" data-tipo="D" data-accion="Cancelar"><i class="fad fa-calendar-times"></i> Cancelar reservas</a></li>@endif
                        <li class="divider"></li>
                        <li class="dropdown-header">Acciones</li>
                        <li><a href="#" class="btn_qr dropdown-item"><i class="fad fa-qrcode"></i> Imprimir QR</a></li>
                        <li><a href="#" class="btn_export_qr dropdown-item"><i class="fad fa-file-export"></i></i> Exportar QR</a></li>
                        @if(checkPermissions(['Rondas de limpieza'],['C']) && session('CL')['mca_limpieza']=='S')<li><a href="#" class="btn_asignar dropdown-item" data-tipo="L" ><i class="fad fa-broom"></i >Ronda de limpieza</a></li>@endif
                        @if(checkPermissions(['Rondas de mantenimiento'],['C']))<li><a href="#" class="btn_asignar dropdown-item" data-tipo="M"><i class="fad fa-tools"></i> Ronda de mantenimiento</a></li>@endif
                        @if(checkPermissions(['Puestos'],['D']))<li><a href="#" class="btn_borrar_puestos btn_toggle_dropdown dropdown-item"  data-tipo="M"><i class="fad fa-trash"></i></i> Borrar puestos</a> </li>@endif
                    </ul>
                </div>
            </div>
            <div class="btn">
                @if(checkPermissions(['Puestos'],['C']))
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo puesto">
                    <i class="fa fa-plus-square pt-2" aria-hidden="true"></i>
                    <span>Nuevo</span>
                </a>
                @endif
            </div>
        </div>
    </div>
    @php $etiqueta_boton="Ver puestos" @endphp
    
    <div id="editorCAM" class="mt-2">

    </div>

    
    <div class="row">
        <div id="div_filtro">
            <form method="post" name="form_puestos" id="formbuscador" action="{{ url('puestos/') }}" class="form-filter no-toast">
                @csrf
                <input type="hidden" name="document" value="pantalla">
                @include('resources.combos_filtro',[$hide=['usu'=>1,'est_inc'=>1,'tip_mark'=>1,'tip_inc'=>1]])
                @include('puestos.scripts_lista_puestos')
            </form>
        </div>
    </div>
    <div id="myFilter">
        @if(!isset($r))
            @include('puestos.fill-tabla')
        @endif
    </div>
    
    <div class="modal fade" id="borrar-puestos" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h1 class="modal-title text-nowrap">Borrar puesto </h1>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>    
                <div class="modal-body">
                    ¿Borrar <span id="cuenta_puestos_borrar"></span> puestos?<br>
                    Esta accion no puede deshacerse
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info" href="javascript:void(0)" id="borrar_muchos">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modificar-puestos" style="display: none;">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <form  action="{{url('puestos/modificar_puestos')}}" method="POST" name="frm_modif_puestos" id="frm_modif_puestos" class="form-ajax">
                    {{csrf_field()}}
                    <input type="hidden" name="lista_id" id=lista_id_modif>
                    <div class="modal-header">
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                        <h3 class="modal-title">Modificar datos de <span id="cuenta_puestos_borrar"></span> puestos </h3>
                        <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                            <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                        </button>
                    </div>    

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="des_puesto">Nombre descriptivo</label>
                                <input type="text" name="des_puesto" id="des_puesto" class="form-control" >
                            </div>
                            <div class="form-group col-md-2">
                                <label for="id_estado">Estado</label>
                                <select name="id_estado" id="modif_id_estado"  class="form-control">
                                    <option value=""></option>
                                    @foreach(DB::table('estados_puestos')->get() as $estado)
                                    <option value="{{ $estado->id_estado}}">{{ $estado->des_estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="val_color">Color</label><br>
                                <input type="hidden" name="val_color" id="val_color" class="form-control" >
                                <input type="color" autocomplete="off" id="modif_val_color" id="modif_val_color"  class="form-control" value="#ffffff" />
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label>Icono</label><br>
                                    <button type="button"  role="iconpicker" name="val_icono"  id="modif_val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker enfrente" data-search="true" data-rows="10" data-cols="20" data-search-text="Buscar..."></button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="id_edificio">Edificio</label>
                                <select name="id_edificio" id="modif_id_edificio" class="form-control enfrente">
                                    <option value=""></option>
                                    @foreach(DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $edificio)
                                        <option value="{{ $edificio->id_edificio}}">{{ $edificio->des_edificio }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="planta">Planta</label>
                                <select name="id_planta" id="modif_id_planta" class="form-control">
                                    <option value=""></option>
                                    @foreach(DB::table('plantas')->where('id_cliente',Auth::user()->id_cliente)->get() as $planta)
                                        <option value="{{ $planta->id_planta}}">{{ $planta->des_planta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="planta">Anonimo</label>
                                <select name="mca_acceso_anonimo" id="modif_mca_acceso_anonimo" class="form-control">
                                    <option value=""></option>
                                    <option value="S">Si</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="planta">Reservar</label>
                                <select name="mca_reservar" id="modif_mca_reservar" class="form-control">
                                    <option value=""></option>
                                    <option value="S">Si</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="planta">Tags</label>
                                <input type="text" class="edit_tag" data-role="tagsinput" placeholder="Type to add a tag" size="17" name="tags">
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="id_perfil">Asignar a perfil</label>
                                <select name="id_perfil" id="modif_id_perfil" class="form-control enfrente">
                                    <option value=""></option>
                                    @foreach(DB::table('niveles_acceso')->wherein('id_cliente',[1,Auth::user()->id_cliente])->where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->orderby('val_nivel_acceso')->orderby('des_nivel_acceso')->get() as $n)
                                        <option value="{{ $n->cod_nivel}}">{{ $n->des_nivel_acceso }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="id_usuario">Tipo de puesto</label>
                                <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                                    <option value=""></option>
                                    @foreach($tipos as $t)
                                        <option value="{{ $t->id_tipo_puesto}}" {{ isset($puesto->id_tipo_puesto) && $puesto->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }}>{{ $t->des_tipo_puesto }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(session('CL')['mca_reserva_horas']=='S')
                                <div class="form-group col-md-3">
                                    <label for="max_horas_reservar">Max reserva(horas)</label>
                                    <input type="text" autocomplete="off" name="max_horas_reservar" id="max_horas_reservar"   class="form-control hourMask" />
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info" id="modificar_muchos">Si</button>
                        <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="eliminar-puesto" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h1 class="modal-title text-nowrap">Borrar puesto </h1>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>    
                <div class="modal-body">
                    ¿Borrar puesto <span id="txt_borrar"></span>?
                </div>
            
                <div class="modal-footer">
                    <a class="btn btn-info" href="" id="link_borrar">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="anonimo-puesto" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h3 class="modal-title">Habilitar acceso anonimo para los puestos </h3>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>    
                
                <div class="modal-body">
                    <div class="form-check pt-2">
                        <input   name="mca_anonimo" data-label="lbl_anonimo" id="mca_anonimo" checked value="S" class="form-check-input" type="checkbox">
                        <label for="mca_anonimo" class="form-check-label">Habilitado</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_anonimo" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reserva-puesto" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <<div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h3 class="modal-title">Habilitar reserva para los puestos </h3>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div> 
                <div class="modal-body">
                    <div class="form-check pt-2">
                        <input  name="mca_reserva" data-label="lbl_reserva" id="mca_reserva" checked value="S" class="form-check-input" type="checkbox">
                        <label for="mca_reserva" class="form-check-label">Habilitado</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_reserva" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ronda-limpieza" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h3 class="modal-title">Crear ronda de <span class="tipo_ronda"></span> </h3>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div> 
                
                <div class="modal-body" style="height: 250px">
                    <input type="hidden" id="listaID">
                    <input type="hidden" id="tip_ronda">
                    Crear ronda de <span class="tipo_ronda"></span> para <span id="cuenta_puestos_limpieza"></span> puestos.
                    <br><br>
                    <div class="form-group">
                        <label> Descripcion del trabajo</label>
                        <input type="text" class="form-control" name="des_ronda" id="des_ronda" id="listaID">
                    </div>
                    <div class="form-group">
                        <label> Asignar a empleado de <span class="tipo_ronda"></span></label>
                        <div id="divlimpiadores"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_crear_ronda" href="javascript:void(0)">Si</a>
                    <button type="button" id="btn_cancel_ronda" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-reservas" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <span class="float-right" id="spin_reserva" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h1 class="modal-title text-nowrap"><span class="tipo_accion"></span> multiples reservas. </h1>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>
                <form  action="{{url('puestos/modificar_puestos')}}" method="POST" name="frm_modif_puestos" id="frm_modif_puestos" class="form-ajax">
                    {{csrf_field()}}
                    <div class="modal-body" id="">
                        <input type="hidden" name="lista_id" id="lista_id_reserva">
                        <input type="hidden" name="accion_reserva" id="accion_reserva">
                        <input type="hidden" name="hora_inicio" id="hora_inicio" value="00:00">
                        <input type="hidden" name="hora_fin" id="hora_fin" value="23:59">
                        <div class="col-md-5">
                            <label><span class="badge badge-primary">1</span> Seleccione fechas </label>
                            <div class="input-group mb-3">
                                <input type="text"  autocomplete="off" class="form-control pull-left rangepicker" id="fechas_reserva" name="fechas">
                                <span class="btn input-group-text btn-secondary btn_fechas"   style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                            </div>
                        </div>
                        <div id="div_usuario_multiple">
                            <label><span class="badge badge-primary">2</span> Seleccione usuario </label>
                            <select name="id_usuario_res_multiple" id="id_usuario_res_multiple" class="form-control select2_res ">
                                <option value=""></option>
                                @foreach($usuarios as $n)
                                    <option value="{{ $n->id}}">{{ $n->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(session('CL')['mca_reserva_horas']=='S')
                        <div class="mb-5 capa_horas">
                            <label><span class="badge badge-primary">3</span> Seleccione horario </label>
                            <div class="form-group col-md-12">
                                <label for="hora-range-drg"><i class="fad fa-clock"></i> Horas [<span id="horas_rango"></span>] <span id="obs" class="text-info"></span></label>
                                <div id="hora-range-drg" style="margin-top: 40px"></div><span id="hora-range-val" style="display: none"></span>
                            </div>
                        </div>
                        @endif
                        
                        <div id="comprobar_puesto_reserva" class="rounded b-all mt-4 pad-all" style="display: none"></div>
                        <div id="mensaje_pulse" style="display: none">Pulse Si para confirmar la reserva</div>
                    </div>
                </form>
                
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_res_multiple" href="javascript:void(0)">Si</a>
                    <button type="button" id="btn_cancel_res_multiple" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
<script src="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
{{--  <script src="{{ asset('plugins/fullcalendar/lib/jquery-ui.custom.min.js') }}"></script>  --}}
{{--  <script src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>  --}}
<script src="{{ asset('plugins/fullcalendar/lib/main.min.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/lib/locales/es.js') }}"></script>
<script src="{{ asset('/plugins/inputmask/dist/inputmask.js') }}"></script>
<script src="{{ asset('/plugins/inputmask/dist/jquery.inputmask.js') }}"></script>
<script src="{{ asset('/plugins/inputmask/dist/bindings/inputmask.binding.js') }}"></script>
<script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
<script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>

<script>

    function comprobar_reserva_multiple(){
        if( $('#fechas_reserva').val()!==null && $('#id_usuario_res_multiple').val()!==""){
            searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
            }).get(); // <----
            fecres=$('#fechas_reserva').val();
            fecres=fecres.split(' - ');
            console.log(fecres);
            fecdesde=moment(fecres[0]+' '+$('#hora_inicio').val(),"DD/MM/YYYY HH:mm:ss").format('YYYY-MM-DD HH:mm:ss');
            fechasta=moment(fecres[1]+' '+$('#hora_fin').val(),"DD/MM/YYYY HH:mm:ss").format('YYYY-MM-DD HH:mm:ss');
            $('#spin_reserva').show();
            $.post('{{ url('reservas/puestos_usuario') }}/'+$('#id_usuario_res_multiple').val()+'/'+fecdesde+'/'+fechasta, {_token: '{{csrf_token()}}',lista_id:searchIDs}, function(data, textStatus, xhr) {
                console.log(data);
                $('#spin_reserva').hide();
                $('#comprobar_puesto_reserva').show();
                $('#comprobar_puesto_reserva').html(data.message+'<br>'+data.recomendacion);
                if(data.lista.length>0){
                    $('#mensaje_pulse').show();
                    $('#btn_res_multiple').show();
                } else {
                    $('#mensaje_pulse').hide();
                    $('#btn_res_multiple').hide();
                }
            })
            .fail(function(err){
                toast_error('Error',err.responseJSON.message);
            });
        }
    }

    //Menu
    $('.configuracion').addClass('active active-sub');
    $('.menu_parametrizacion').addClass('active active-sub');
	$('.espacios').addClass('active active-sub');
    $('.puestos').addClass('active');

    $('.chk_accion').click(function(){
        if($(this).is(':checked')){
            $('#'+$(this).data('label')).html('Habilitado');
        } else {
            $('#'+$(this).data('label')).html('Deshabilitado');
        }
    })

    $('.btn_toggle_dropdown').click(function(){
        $('#dropdown-acciones').toggle();
    })

    

    let searchIDs=[];

    $('#frmpuestos').submit(form_pdf_submit);
    $('#formbuscador').submit(ajax_filter);

	$('#btn_nueva_puesto').click(function(){
       $('#editorCAM').load("{{ url('/puestos/edit/0') }}", function(){
		animateCSS('#editorCAM','bounceInRight');
	   });
	});

	

    $('td').click(function(event){
        editar( $(this).data('id'));
    })


    $("#chktodos").click(function(){
        $('.chkpuesto').not(this).prop('checked', this.checked);
    });

    $('#btn_si_anonimo').click(function(){
        console.log('anomino');
        var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            exit();
        }
        if($('#mca_anonimo').is(':checked')){
            estado='S';
        } else {
            estado='N';
        }
        $.post('{{url('/puestos/anonimo')}}', {_token: '{{csrf_token()}}',estado: estado ,lista_id:searchIDs}, function(data, textStatus, xhr) {
            $('.modal').modal('hide');
            toast_ok('Cambio de estado anonimo',data.mensaje);
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    })

    $('#btn_si_reserva').click(function(){
        console.log('reserva');
        var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            exit();
        }
        if($('#mca_reserva').is(':checked')){
            estado='S';
        } else {
            estado='N';
        }
        $.post('{{url('/puestos/mca_rerserva')}}', {_token: '{{csrf_token()}}',estado: estado ,lista_id:searchIDs}, function(data, textStatus, xhr) {
            $('.modal').modal('hide');
            toast_ok('Permiso de reserva',data.mensaje);
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    })


    $('.btn_estado_check').click(function(){
        console.log('check');
        var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            exit();
        }

        $.post('{{url('/puestos/accion_estado')}}', {_token: '{{csrf_token()}}',estado: $(this).data('estado'),lista_id:searchIDs}, function(data, textStatus, xhr) {
            toast_ok('Acciones',data.mensaje);
            //console.log($('.chkpuesto:checkbox:checked'));
            $('.chkpuesto:checkbox:checked').each(function(){
                //console.log('#estado_'+$(this).data('id'));
                $('#estado_'+$(this).data('id')).removeClass();
                $('#estado_'+$(this).data('id')).addClass('bg-'+data.color);
                $('#estado_'+$(this).data('id')).html(data.label);
                animateCSS('#estado_'+$(this).data('id'),'rubberBand');
            });
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    });

    $('.btn_qr').click(function(){
        //block_espere();
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
        $('#frmpuestos').attr('action',"{{url('/puestos/print_qr')}}");
        $('#frmpuestos').submit();
    });

    $('.btn_export_qr').click(function(){
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
        $('#frmpuestos').attr('action',"{{url('/puestos/export_qr')}}");
        $('#frmpuestos').submit();
    });

    $('.btn_asignar').click(function(){
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
        $('#cuenta_puestos_limpieza').html(searchIDs.length);
        $('#des_ronda').val();
        if($(this).data('tipo')=='L'){
            $('.tipo_ronda').html("limpieza");
        } else {
            $('.tipo_ronda').html("mantenimiento");
        }
        $('#tip_ronda').val($(this).data('tipo'));
        $.post('{{url('/combos/limpiadores')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs, tipo:$(this).data('tipo')}, function(data, textStatus, xhr) {
            $('#divlimpiadores').html(data);
        })
        $('#ronda-limpieza').modal('show');
        //fin_espere();
    });


    $('#tablapuestos').on('click-cell.bs.table', function(e, value, row, $element){
        //console.log(e);
    });

    $('#btn_crear_ronda').click(function(){
        var userIDs = $('.chkuser:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(userIDs.length==0){
            toast_error('Error','Debe seleccionar algún empleado de limpieza');
            return;
        }
        $.post('{{url('/puestos/ronda_limpieza')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs,lista_limpiadores: userIDs,des_ronda: $('#des_ronda').val(),tip_ronda: $('#tip_ronda').val() }, function(data, textStatus, xhr) {
            console.log(data);
            toast_ok(data.title,data.mensaje);
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
        $('#ronda-limpieza').modal('hide');
    });

    $('.btn_borrar_puestos').click(function(){
        //block_espere();
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
        $('#cuenta_puestos_borrar').html(searchIDs.length);
        $('#borrar-puestos').modal('show');
        //fin_espere();
    });

    $('#borrar_muchos').click(function(){
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); // 

        $.post('{{url('/puestos/borrar_puestos')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs}, function(data, textStatus, xhr) {
            console.log(data);
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.alert){
                toast_warning(data.title,data.alert);
            } else{
                toast_ok(data.title,data.message);
            }
        }) 
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        })
        .always(function(data){
            $('#borrar-puestos').modal('hide');  
            if(data.url){
                setTimeout(()=>{window.open(data.url,'_self')},3000);
            } 
            
        });
    })

    $('#modificar_muchos').click(function(){
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); 
        $('#lista_id_modif').val(searchIDs);
        //$('#frm_modif_puestos').submit();
    })

    $('.btn_modificar_puestos').click(function(){
        //block_espere();
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
        $('#cuenta_puestos_modificar').html(searchIDs.length);
        $('#modificar-puestos').modal('show');
        //fin_espere();
    });

    $('#modif_val_icono').on('change', function(e) {
        console.log(e.icon);
        $('.modal').css('z-index', 10000);
    });

    $('#modif_val_icono').click(function() {
        $('.modal').css('z-index', 1000);
    });

    $('.btn_modal_reserva').click(function(){
        $('#id_usuario_res_multiple').val('');
        $('#id_usuario_res_multiple').trigger('change');
        $('#comprobar_puesto_reserva').empty();
        $('#mensaje_pulse').hide();
        $('#accion_reserva').val($(this).data('tipo'));
        $('.tipo_accion').html($(this).data('accion'));
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); 
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            $('#modal-reservas').modal('hide');
            exit();
        }
        $('#lista_id_reserva').val(searchIDs);
        //A ver si estamos cancelando o creando
        
        if($(this).data('accion')=='Cancelar'){
            $('#div_usuario_multiple').hide();
            $('#comprobar_puesto_reserva').html('<b>¿Seguro que quiere cancelar las todas las reservas de estos '+searchIDs.length+' puestos entre las fechas seleccionadas?<br>Esta acción no puede deshacerse</b>');
            $('#comprobar_puesto_reserva').show();
            $('.capa_horas').hide();
        } else {
            $('#div_usuario_multiple').show();
        }
    })


    $('#btn_res_multiple').click(function(){
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); 
        $('#spin_reserva').show();
        $.post('{{ url('reservas/reservas_multiples_admin') }}', {_token: '{{csrf_token()}}',lista_id:searchIDs,id_usuario: $('#id_usuario_res_multiple').val(),rango: $('#fechas_reserva').val(),accion:$('#accion_reserva').val(), hora_inicio:$('#hora_inicio').val() ,hora_fin: $('#hora_fin').val() }, function(data, textStatus, xhr) {
            $('#spin_reserva').hide();
            console.log(data);
            if(data.error){
                toast_error(data.title,data.error);
            } else {
                toast_ok(data.title,data.mensaje);
            }
            $('#modal-reservas').modal('hide');
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    })

    Inputmask({regex:"^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$"}).mask('.hourMask');

    var rangepicker = new Litepicker({
        element: document.getElementById( "fechas_reserva" ),
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
                comprobar_reserva_multiple();
            });
        }
    });

    $('.btn_fechas').click(function(){
        rangepicker.show();
    })


    $('#id_usuario_res_multiple').change(function(){
        comprobar_reserva_multiple();
    })

    $('.select2_res').select2({
        width: '100%',
        dropdownParent: $("#modal-reservas  .modal-body"),
    }      
    );

    $('#modif_val_color').change(function(){
        $('#val_color').val($(this).val());
    });

    @if(session('CL')['mca_reserva_horas']=='S')

        var aproximateHour = function (mins)
        {
        //http://greweb.me/2013/01/be-careful-with-js-numbers/
        var minutes = Math.round(mins % 60);
        if (minutes == 60 || minutes == 0)
        {
            return mins / 60;
        }
        return Math.trunc (mins / 60) + minutes / 100;
        }


        function filter_hour(value, type) {
        return (value % 60 == 0) ? 1 : 0;
        }


        var r_def = document.getElementById('hora-range-drg');
        var r_def_value = document.getElementById('hora-range-val');


        noUiSlider.create(r_def,{
            start : [{{ config_cliente('min_hora_reservas') }}, {{ config_cliente('max_hora_reservas') }}],
            connect: true, 
            behaviour: 'tap-drag', 
            step: 10,
            tooltips: true,
            range : {'min': {{ config_cliente('min_hora_reservas') }}, 'max': {{ config_cliente('max_hora_reservas') }} },
            format:  wNumb({
                    decimals: 2,
                mark: ":",
                    encoder: function(a){
                return aproximateHour(a);
                }
                }),
        });
        r_def.noUiSlider.on('change', function( values, handle ) {
            console.log(values);
            $('#hora_inicio').val(values[0]);
            $('#hora_fin').val(values[1]);
            $('#horas_rango').html(values[0]+' - '+values[1]);
            comprobar_reserva_multiple();
        });

        
        values=r_def.noUiSlider.get();
        $('#hora_inicio').val(values[0]);
        $('#hora_fin').val(values[1]);
        $('#horas_rango').html(values[0]+' - '+values[1]);
    @endif

    $('.dropdown-item').click(function(){
        console.log('show');
        $('.dropdown-menu').show();
    })


    
</script>
@endsection
