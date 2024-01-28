@extends('layout')

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->

    <style type="text-css">
        .show-calendar{
            z-indez: 15000;
        }

       
       
    </style>
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de usuarios</h1>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuración</li>
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">personas</li>
        <li class="breadcrumb-item active">usuarios</li>
        {{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection

@section('content')

    @if(Session::has('success_message'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            {!! session('success_message') !!}

            <button type="button" class="close" data-dismiss="alert" aria-label="close">
                <span aria-hidden="true">&times;</span>
            </button>

        </div>
    @endif

   

    <div class="row botones_accion">
        <div class="col-md-8">

        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" >
                    <i class="fad fa-poll-people pt-2" style="font-size: 20px" aria-hidden="true"></i> Acciones <i class="dropdown-caret"></i>
                </button>
                <ul class="dropdown-menu" id="dropdown-menu-acciones">
                    <li class="dropdown-header">Atributos</li>
                    @if(checkPermissions(['Usuarios'],['W']))<li><a href="#modificar-usuario" class="btn_plantas btn_toggle_dropdown btn_search btn_modif dropdown-item" data-toggle="modal" data-tipo="M"><i class="fa-solid fa-user-pen"></i> Modificar datos de usuario</a> </li>@endif
                    @if(checkPermissions(['Usuarios'],['W']))<li><a href="#asignar-planta" class="btn_plantas btn_toggle_dropdown btn_search dropdown-item" data-toggle="modal" data-tipo="M"><i class="fad fa-layer-plus"></i> Asignar planta</a> </li>@endif
                    {{-- @if(checkPermissions(['Usuarios'],['W']))<li><a href="#asignar-supervisor" class="btn_supervisor btn_toggle_dropdown btn_search" data-toggle="modal" data-tipo="M"><i class="fad fa-user-friends"></i> Asignar supervisor</a></li>@endif --}}
                    <li class="divider"></li>
                    @if(checkPermissions(['Asignar puesto a usuario'],['R']))<li><a href="#asignar-puesto" data-toggle="modal" class="btn_asignar_puesto reserva btn_search dropdown-item" data-tipo="M"><i class="fad fa-desktop-alt"></i> Asignar temporalmente puesto a usuario</a></li>@endif
                    @if(checkPermissions(['Crear reservas para otros'],['R']))<li><a href="#crear-reserva" data-toggle="modal"  class="btn_crear_reserva reserva btn_search dropdown-item" data-tipo="M"><i class="fad fa-calendar-alt"></i> Crear reserva para usuario</a></li>@endif 
                    @if(checkPermissions(['Usuarios'],['R']))<li><a href="#"  class="btn_enviar_recodratorio btn_search dropdown-item" data-tipo="M"><i class="fad fa-envelope"></i> Enviar recordatorio de asignacion</a></li>@endif
                    @if(checkPermissions(['Usuarios'],['D']))<li><a href="#" class="btn_borrar_usuarios btn_toggle_dropdown btn_search dropdown-item"  data-tipo="M"><i class="fad fa-trash"></i></i> Borrar usuarios</a> </li>@endif
            </div>
            <div class="btn">
                @if(checkPermissions(['Usuarios'],['C']))
                <a href="#" onclick="editar(0)" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo usuario">
                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                    <span>Nuevo</span>
                </a>
                @endif
            </div>
        </div>
    </div>
    <div id="editorCAM">

    </div>

    <div class="row mt-2">
        <div id="div_filtro">
            <form method="post" name="form_puestos" id="formbuscador" action="{{ url('users/search') }}" class>
                @csrf
                <input type="hidden" name="document" value="pantalla">
                @include('resources.combos_filtro',[$hide=['tag'=>1,'est_inc'=>1,'pue'=>1,'est'=>1,'tip_mark'=>1,'tip_inc'=>1],$show=['dep'=>1,'perfil'=>1,'tur'=>1,'col'=>1,'u_l'=>1,'sup'=>1,'u_id'=>1]])
            </form>
        </div>
        
    </div>
    <div class="row mt-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Usuarios</h3>
            </div>
    
    
            @if(count($usersObjects) == 0)
                <div class="card-body text-center">
                    <h4>No Users Available.</h4>
                </div>
            @else
            <h5 class="text-muted" id="cuenta_usuarios">{{ $usersObjects->count() }} Usuarios encontrados</h5>
            <div class="card-body panel-body-with-table">
                <div class="table-responsive">
                    <div id="all_toolbar" class="ml-3">
                        <div class="form-check pt-2">
                            <input name="chktodos" id="chktodos" class="form-check-input" type="checkbox">
                            <label for="chktodos" class="form-check-label">Todos</label>
                        </div>
                    </div>
                    <table id="tablausuarios"  
                    data-toggle="table" 
                    data-mobile-responsive="true"
                    data-locale="es-ES"
                    data-search="true"
                    data-show-columns="true"
                    data-show-toggle="true"
                    data-show-columns-toggle-all="true"
                    data-page-list="[20, 50, 100, 300, 500, 750, 1000, 2000, 5000, 10000]"
                    data-page-size="25"
                    data-pagination="true" 
                    data-toolbar="#all_toolbar"
                    data-buttons-class="secondary"
                    data-show-button-text="true"
                        >
                        <thead>
                            <tr>
                                <th style="width: 10px" class="no-sort"  data-switchable="false"></th>
                                <th style="width: 30px" class="no-sort"  data-switchable="false"></th>
                                <th  data-sortable="true">Nombre</th>
                                <th  data-sortable="true">Perfil</th>
                                <th  data-sortable="true">Ult acceso</th>
                                <th  data-sortable="true">Email</th>
                            </tr>
                        </thead>
                        <tbody id="myFilter">
                        @include('users.fill_tabla_usuarios')
                        </tbody>
                    </table>
                </div>
                {{-- {{ $usersObjects->links() }} --}}
            </div>
            @endif
        </div>
    </div>
    

    <div class="modal fade" id="eliminar-usuario" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h1 class="modal-title text-nowrap">Borrar usuario </h1>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>    
                <div class="modal-body">
                    ¿Borrar usuario <span id="txt_borrar"></span>?
                </div>
            
                <div class="modal-footer">
                    <a class="btn btn-info" href="#" id="link_borrar">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="borrar-usuarios" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h1 class="modal-title text-nowrap">Borrar usuarios </h1>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>    
                <div class="modal-body">
                    ¿Borrar <span id="cuenta_usuarios_borrar"></span> usuarios?<br>
                    Esta accion no puede deshacerse
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info" href="javascript:void(0)" id="borrar_muchos">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="asignar-planta" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h3 class="modal-title">Habilitar plantas a los usuarios </h3>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>  

                
                <div class="modal-body" id="">
                    <div class="form-check pt-2">
                        <input  name="chk_anterior" id="chk_anterior" class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="chk_anterior">Borrar plantas que tuvieran ya asignadas los usuarios</label>
                    </div>  
                    <br><br>
                    <div id="txt_cuerpo"></div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_planta" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="crear-reserva" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <span class="float-right" id="spin_reserva" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h3 class="modal-title">Crear reserva para usuario </h3>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>
                
                <div class="modal-body">

                    <label><span class="badge badge-primary">1</span> Seleccione fechas </label>
                    <div class="col-md-5">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control pull-left rangepicker" id="fechas_reserva" name="fechas" style="height: 40px; width: 200px" value="{{ Carbon\Carbon::now()->format('d/m/Y').' - '.Carbon\Carbon::now()->format('d/m/Y') }}">
                            <span class="btn input-group-text btn-secondary btn_fechas"   style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                        </div>
                    </div>
                    
                    <label><span class="badge badge-primary">2</span> Seleccione puesto </label>
                    <div id="comprobar_puesto_reserva" class=" alert rounded b-all alert-warning mb-2 pad-all" style="display: none"></div>
                    <div id="puestos_usuario_reserva"></div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_reserva" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="asignar-puesto" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <span class="float-right" id="spin_asignar" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h3 class="modal-title">Asignar temporalmente puesto a  usuario </h3>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>
                
                <div class="modal-body" id="">

                    <div class="row">
                        <div class="col-md-5">
                            <label><span class="badge badge-primary">1</span> Seleccione fechas </label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control pull-left rangepicker" id="fechas" name="fechas" value="{{ Carbon\Carbon::now()->format('d/m/Y').' - '.Carbon\Carbon::now()->format('d/m/Y') }}">
                                <span class="btn input-group-text btn-secondary btn_fechas2"   style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                            </div>
                        </div>
                        <div class="col-md-8 pt-2">
                            <div class="form-check pt-2">
                                <input name="nocerrar_asig"  id="nocerrar_asig" value="S" class="form-check-input" type="checkbox">
                                <label class="form-check-label" for="nocerrar_asig">No cerrar la ventana (voy a seleccionar varios)</label>
                            </div>
                        </div>
                    </div>
                    
                    <label><span class="badge badge-primary">2</span> Seleccione puesto </label>
                    <div id="comprobar_puesto_asignar" class=" alert rounded b-all alert-warning mb-2 pad-all" style="display: none"></div>
                    <div id="puestos_usuario_asignar"></div>
                </div>
                <div class="modal-footer">
                    {{--  <a class="btn btn-info" id="btn_si_supervisor" href="javascript:void(0)">Si</a>  --}}
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modificar-usuario" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <span class="float-right" id="spin_asignar" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h3 class="modal-title">Modificar datos de <span id="cuenta_usuarios_modificar"></span> usuarios </h3>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>
                
                <div class="modal-body" id="">

                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info" id="btn_si_modificar">Modificar</button>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        var id_usuario_borrar;
        $('.configuracion').addClass('active active-sub');
        $('.menu_parametrizacion').addClass('active active-sub');
        $('.menu_usuarios').addClass('active active-sub');
        $('.usuarios').addClass('active');
        
        var searchIDs;
        $('#formbuscador').submit(ajax_filter);

        //Busqueda de la tabla
        function customSearch(data, text) {
            console.log(text);
            $('#myfilter').empty();
            // return data.filter(function (row) {
            // return row.price.indexOf(text) > -1
        }
        
        function del(id){
            id_usuario_borrar = id;
            $('#eliminar-usuario').modal('show');
        }

        function editar(id){
            $('#editorCAM').load("{{ url('/users/edit') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }

        $('#link_borrar').click(function(){
            console.log("{{ url('users/delete/') }}/"+id_usuario_borrar);
            window.open("{{ url('users/delete/') }}/"+id_usuario_borrar,'_self');
        })

        $('.btn_eliminar').click(function(){
            console.log($(this).data('name'));
            $('#txt_borrar').html($(this).data('name'));
        })

        $('.hover-this').click(function(){
            window.open($(this).data('href'),'_self');
        })

        $("#chktodos").click(function(){
            $('.chkuser').not(this).prop('checked', this.checked);
        });

        $('.btn_plantas').click(function(){
            $('#txt_cuerpo').load("{{ url('/users/plantas',Auth::user()->id) }}/0");
        })

        $('.btn_borrar_usuarios').click(function(){
            //block_espere();
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // <----

            $('#cuenta_usuarios_borrar').html(searchIDs.length);
            $('#borrar-usuarios').modal('show');
            //fin_espere();
        });

        $('.btn_modif').click(function(){
            //block_espere();
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // <----

            $('#cuenta_usuarios_modificar').html(searchIDs.length);
            $('#modificar-usuario').modal('show');
            $.post('{{url('/users/edit_modificar_usuarios')}}', {_token: '{{csrf_token()}}',id_usuario: searchIDs}, function(data, textStatus, xhr) {
                $('#modificar-usuario .modal-body').html(data);
                    //fin_espere();
                });
        });


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
                    searchIDs = $('.chkuser:checkbox:checked').map(function(){
                        return $(this).val();
                    }).get(); // 
                    fecres=$('#fechas_reserva').val();
                    fecres=fecres.split(' - ');
                    console.log(fecres);
                    fecdesde=moment(fecres[0],"DD/MM/YYYY").format('YYYY-MM-DD');
                    fechasta=moment(fecres[1],"DD/MM/YYYY").format('YYYY-MM-DD');
                    $('#puestos_usuario_reserva').load("{{ url('reservas/puestos_usuario') }}/"+searchIDs+'/'+fecdesde+'/'+fechasta, function(data, textStatus, xhr){
                        console.log("reserva");
                        $('#comprobar_puesto_reserva').html('');
                        $('#puestos_usuario_reserva').empty();
                        $('#puestos_usuario_reserva').html(data);
                        $('.flpuesto').click(function(){
                            $('#spin_reserva').show();
                            $.post('{{url('/reservas/asignar_reserva_multiple')}}', {_token: '{{csrf_token()}}',puesto:$(this).data('id'),desde:fecdesde,hasta:fechasta,id_usuario: searchIDs,accion: 'A'}, function(data, textStatus, xhr) {
                                console.log('RESULT');
                                if(data.error){
                                    toast_error(data.title,data.error);
                                } else if(data.alert){
                                    toast_warning(data.title,data.alert);
                                } else{
                                    $('.modal').modal('hide');  
                                    toast_ok(data.title,data.message);
                                }
                                $('#spin_reserva').hide();
                            }) 
                            .fail(function(err){
                                toast_error('Error',err.responseJSON.message);
                                $('#spin_reserva').hide();
                            });                 
                        })
                    });
                });
            }
        });

        $('.btn_fechas').click(function(){
            rangepicker.show();
        })

        var rangepicker2 = new Litepicker({
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
            setup: (rangepicker2) => {
                rangepicker2.on('selected', (date1, date2) => {
                    $('#puestos_usuario_asignar').load("{{ url('users/puestos_usuario/') }}/"+searchIDs, function(){
                        $('#comprobar_puesto_asignar').html('');
                        $('.flpuesto').click(function(){
                            $('#spin_asignar').show();
                            $.post('{{url('/users/asignar_temporal')}}', {_token: '{{csrf_token()}}',puesto:$(this).data('id'),rango: $('#fechas').val(),id_usuario: searchIDs,accion: 'A', nocerrar: $('#nocerrar_asig').is(':checked')}, function(data, textStatus, xhr) {
                                console.log(data);
                                if(data.result){ //Si loque devuelve el controller es una respuesta tripo obbect es que todo ha ido bien o que ha habido un error chungo
                                    
                                    $('#spin_asignar').hide();
                                    if(data.error){
                                        toast_error(data.title,data.error);
                                    } else if(data.alert){
                                        toast_warning(data.title,data.alert);
                                    } else{
                                        if(data.nocerrar===false){
                                            $('.modal').modal('hide');  
                                        }
                                        toast_ok(data.title,data.message);
                                    }
                                } else{ //Si lo que devuelve es una view, implica que hay que preguntar al usuario que vamos a hacer y por lo tanto hay que mostrarla en pantalla
                                    console.log('view');
                                    $('#comprobar_puesto_asignar').show();
                                    $('#comprobar_puesto_asignar').html(data);
                                    animateCSS('#comprobar_puesto_asignar','bounceInRight');
                                    $('#spin_asignar').hide();
                                }
                                
                            }) 
                            .fail(function(err){
                                toast_error('Error',err.responseJSON.message);
                            });                 
                        })
                    });
                });
            }
        });

        $('.btn_fechas2').click(function(){
            rangepicker2.show();
        })

        

        $('#borrar_muchos').click(function(){
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // 

            $.post('{{url('/users/borrar_usuarios')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs}, function(data, textStatus, xhr) {
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
                $('#borrar-usuarios').modal('hide');  
                if(data.url){
                    setTimeout(()=>{window.open(data.url,'_self')},3000);
                } 
                
            });
        })

        $('#btn_si_planta').click(function(){
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get();

            searchPLs= $('.chkplanta:checkbox:checked').map(function(){
                return $(this).val();
            }).get();

            $.post('{{url('/users/asignar_plantas')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs,lista_plantas:searchPLs,borrar_ant: $('#chk_anterior').is(':checked')}, function(data, textStatus, xhr) {
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
                $('#asignar-planta').modal('hide');
                if(data.url){
                    setTimeout(()=>{window.open(data.url,'_self')},3000);
                }
                
            });
        })

        $('.btn_search').click(function(){
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get();
            $('.dropdown-menu').removeClass('show');
            if(searchIDs.length==0){
                $('.modal').modal('hide');
                toast_error('Error','Debe seleccionar algún usuario');
                return;
            }
        })

        $('#btn_si_supervisor').click(function(){
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get();
        })

        $('#btn_si_modificar').click(function(){

            $('#modif_users_form').submit();
        })

        

        $('.btn_asignar_puesto').click(function(){
            $('#spin_asignar').hide();
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get();
            if(searchIDs.length>1){
                $('.modal').modal('hide');
                toast_warning('Error','Solo se puede hacer la asignacion temporal de 1 usuario a la vez');
                return;
            }

            
        })

        $('.btn_crear_reserva').click(function(){
            $('#spin_reserva').hide();
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); //
            if(searchIDs.length>1){
                $('.modal').modal('hide');
                toast_warning('Error','Solo se puede hacer la reserva de 1 usuario a la vez');
                return;
            }
            //$('#fechas_reserva').change();
        })

        $('.btn_enviar_recodratorio').click(function(){
            $('#spin_reserva').hide();
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get();
            $.post('{{url('/users/enviar_recordatorio_asignacion')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs}, function(data, textStatus, xhr) {
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
            });
        })

        $('#formbuscador').submit(function(event){
            $('.fixed-table-footer').hide();
            $('.fixed-table-pagination').hide();
            $("nav[role='navigation']").hide();
        })



    </script>
@endsection