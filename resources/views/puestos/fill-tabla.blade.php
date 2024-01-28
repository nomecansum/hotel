
<div class="row">
    <div class="card" style="width: 100%">
        <div class="card-header">
            <h3 class="card-title">Puestos</h3>
        </div>
        <div class="card-body w-100" id="main_panel">
            <div class="table-responsive w-100" style="position: relative">
                <form action="{{url('/puestos/print_qr')}}" method="POST"  id="frmpuestos" enctype='multipart/form-data'>
                    <h5>{{ $puestos->count() }} Puestos encontrados</h5>
                    @csrf
                        {{-- <div class="td"><div class="loader"></div></div> --}}
                     {{-- <table class="table table-striped table-hover table-vcenter" id="tablapuestos"  style="width: 98%" data-toggle="table" data-mobile-responsive="true"  data-pagination="true" data-search="true"> --}}
                        <div id="all_toolbar" class="ml-3">
                            <div class="form-check pt-2">
								<input id="chktodos" name="chktodos" class="form-check-input" type="checkbox">
								<label for="chktodos" class="form-check-label">Todos</label>
							</div>
                        </div>
                        <table id="tablapuestos"  
                        data-toggle="table" 
                        data-mobile-responsive="true" 
                        onclick="tabla_click()"
                        data-locale="es-ES"
                        data-search="true"
                        data-show-columns="true"
                        data-show-toggle="true"
                        data-show-columns-toggle-all="true"
                        data-toolbar="#all_toolbar"
                        data-buttons-class="secondary"
                        data-show-button-text="true"
                        >
                        <thead>
                            <tr>
                                <th style="width: 10px" class="no-sort"  data-switchable="false"></th>
                                <th style="width: 20px" class="no-sort"  data-switchable="false"></th>
                                <th data-sortable="true">Puesto</th>
                                <th data-sortable="true" @if(isMobile()) data-visible="false" @endif>Edificio</th>
                                <th data-sortable="true" @if(isMobile()) data-visible="false" @endif>Planta</th>
                                <th data-sortable="true" title="Puesto con asignacion fija" @if(isMobile()) data-visible="false" @endif>Fijo</th>
                                <th data-sortable="true">Alias</th>
                                {{-- <th data-sortable="true" title="Acceso anonimo permitido en el puesto" style="width: 20px"  @if(isMobile()) data-visible="false" @endif>Anonimo</th> --}}
                                {{-- <th data-sortable="true" title="Reserva permitida en el puesto" @if(isMobile()) data-visible="false" @endif>Reserva</th> --}}
                                
                                <th data-sortable="true"class="text-center" style="width: 100px">Estado</th>
                                <th  data-switchable="false"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($puestos as $puesto)
                            <tr class="hover-this" >
                                <td class="text-center">
                                    <div class="form-check">
                                        <input name="lista_id[]" data-id="{{ $puesto->id_puesto }}" id="chkp{{ $puesto->id_puesto }}" value="{{ $puesto->id_puesto }}" class="form-check-input chkpuesto" type="checkbox">
                                        <label class="form-check-label" for="chkp{{ $puesto->id_puesto }}"></label>
                                    </div>
                                </td>
                                
                                <td class="thumb text-center" data-id="">
                                    @if(isset($puesto->val_icono))
                                        <i class="{{ $puesto->val_icono }} fa-2x"  @if(isset($puesto->val_color))style="color: {{ $puesto->val_color }}" @else style="color: {{ $puesto->color_tipo }}"  @endif></i>
                                    @else
                                        <i class="{{ $puesto->icono_tipo }} fa-2x" @if(isset($puesto->val_color))style="color: {{ $puesto->val_color }}" @else style="color: {{ $puesto->color_tipo }}"  @endif></i>
                                    @endisset
                                </td>
                                <td class="td" data-id="">
                                    <div class="m-0 badge pl-1e"  style="width: 100%; heigth: 100%; @if($puesto->color_puesto) background-color: {{ $puesto->color_puesto }}@endif; color: {{ $puesto->color_puesto && txt_blanco($puesto->color_puesto)=='text-white'?'#FFF':'navy' }}">
                                        {{ $puesto->cod_puesto }}
                                    </div>
                                </td>
                                <td class="td" data-id="">{{ $puesto->des_edificio }}</td>
                                <td class="td" data-id="">{{$puesto->des_planta}}</td>
                                <td class="text-center text-muted" >@if(isset($puesto->id_usuario))<i class="fad fa-user add-tooltip"  title="Puesto asignado al usuario {{ \App\Models\users::find($puesto->id_usuario)->name }}" style="color: #f4a462"></i> {{ iniciales(\App\Models\users::find($puesto->id_usuario)->name,3) }} @endif @if(isset($puesto->id_perfil))<i class="fad fa-users add-tooltip" title="Puesto asignado a perfil {{ \App\Models\niveles_acceso::find($puesto->id_perfil)->des_nivel_acceso }}" style="color: #f4a462"></i> {{ iniciales(\App\Models\niveles_acceso::find($puesto->id_perfil)->des_nivel_acceso,3) }}@endif</td>
                                <td class="td" data-id="">
                                    <div class="m-0 badge pl-1e"  style="width: 100%; heigth: 100%; @if($puesto->color_puesto) background-color: {{ $puesto->color_puesto }}@endif; color: {{ $puesto->color_puesto && txt_blanco($puesto->color_puesto)=='text-white'?'#FFF':'navy' }}">
                                        {{ nombrepuesto($puesto) }}
                                    </div>
                                </td>
                                {{-- <td class="text-center text-muted" >@if($puesto->mca_acceso_anonimo=='S') <i class="fas fa-circle"></i> @endif</td> --}}
                                {{-- <td class="text-center text-muted" >@if($puesto->mca_reservar=='S') <i class="fas fa-circle"  style="color: #70c2b4"></i> @endif</td> --}}
                                
                                <td class="td text-center" data-id="">
                                    @if($puesto->mca_incidencia=='N')
                                        @switch($puesto->id_estado)
                                            @case(1)
                                                <div class="badge bg-success rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(2)
                                                <div class="badge bg-danger rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(3)
                                                <div class="badge bg-info rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(4)
                                                <div class="badge bg-dark rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(5)
                                                <div class="badge bg-dark rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(7)
                                                <div class="badge bg-white rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @default
                                        @endswitch
                                        {{ $puesto->des_estado }}
                                    @else
                                        <div class="badge bg-warning rounded"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;"><i class="fad fa-exclamation-triangle"></i>
                                        Incidencia
                                    @endif
                                    
                                    </div>
                                </td>
                                <td class="text-center opts" style="position: relative">
                                    
                                    <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                        <div class="btn-group btn-group pull-right ml-1" role="group">
                                            @if(isAdmin() || config('app.env')=='local')<a href="#"  class="btn btn-warning btn_scan add-tooltip toolbutton"  title="Scan" onclick="scan('{{ $puesto->token }}')"  data-id="{{ $puesto->id_puesto }}"> <span class="fa fa-qrcode" aria-hidden="true"></span> </a>  @endif
                                            @if(checkPermissions(['Puestos'],['W']))<a href="#"  class="btn btn-info btn_editar add-tooltip toolbutton ml-2" onclick="editar({{ $puesto->id_puesto }})" title="Editar puesto" data-id=""> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                            {{-- @if(checkPermissions(['Puestos'],['D']))<a href="#" data-target="#eliminar-puesto" title="Borrar puesto" data-toggle="modal" class="btn btn-danger add-tooltip btn_del toolbutton"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif --}}
                                            {{-- @if(checkPermissions(['Reservas'],['D']))<a href="#"  title="Cancelar Reserva" class="btn btn-pink add-tooltip btn_del toolbutton" onclick="cancelar('{{ $puesto->token }}')"><span class="fad fa-calendar-times" aria-hidden="true"></span> Res</a>@endif --}}
                                            @if(checkPermissions(['Puestos'],['W']))
                                                <a href="#"  class="btn btn-success btn_estado add-tooltip toolbutton"  onclick="estado(1,'{{ $puesto->token }}')" title="Disponible" data-token="{{ $puesto->token }}"  data-estado="1" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-thumbs-up" aria-hidden="true"></span></a>
                                                <a href="#"  class="btn btn-danger btn_estado add-tooltip toolbutton"  onclick="estado(2,'{{ $puesto->token }}')" title="Usado"  data-token="{{ $puesto->token }}"  data-estado="2" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-lock-alt" aria-hidden="true"></span></a>
                                                <a href="#"  class="btn btn-info btn_estado add-tooltip toolbutton"  onclick="estado(3,'{{ $puesto->token }}')" title="Limpiar"  data-token="{{ $puesto->token }}"  data-estado="3" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-broom" aria-hidden="true"></span></a>
                                                
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            {{ $puestos->links() }}
        </div>
    </div>
</div>

<script>
    @if(isset($r))  //Solo se ejecuta cuando se pide por ajax
        $('#tablapuestos').bootstrapTable();
        $("#chktodos").click(function(){
            $('.chkpuesto').not(this).prop('checked', this.checked);
        });
        
        
        tooltipTriggerList = [...document.querySelectorAll( '.add-tooltip' )];
        tooltipList = tooltipTriggerList.map( tooltipTriggerEl => new bootstrap.Tooltip( tooltipTriggerEl ));
    @else
    window.onload=()=>{
            tooltipTriggerList = [...document.querySelectorAll( '.add-tooltip' )];
            tooltipList = tooltipTriggerList.map( tooltipTriggerEl => new bootstrap.Tooltip( tooltipTriggerEl ));
        };
    @endif
   
   

    
    
</script>