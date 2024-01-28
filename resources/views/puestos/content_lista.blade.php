
@php
if(!isset($id_check))
    $id_check="";
@endphp

@foreach ($edificios as $e)
    @php
        $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
        $cuenta_fila=1;
    @endphp
    <div class="card" id="panel{{ $e->id_edificio }}" style="{{ $plantas->isempty()?'display:none':'' }}">
        <div class="card-header bg-gray-dark">
            <div class="row">
                <div class="col-md-5">
                    <span class="fs-2 ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}
                        @if(isset($checks) && $checks==1)    
                            <input type="checkbox" class="form-control chk_edificio_puestos magic-checkbox" name="lista_id[]" data-id="{{ $e->id_edificio }}" id="chkep{{ $e->id_edificio }}" value="{{ $e->id_edificio }}">
                            <label class="custom-control-label" for="chkep{{ $e->id_edificio }}"></label>
                        @endif
                    </span>
                </div>
                <div class="col-md-5"></div>
                <div class="col-md-2 text-end  sp_edificio">
                    <h4 class="text-white">
                        <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                        <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $e->puestos }}</span>
                    </h4>
                </div>
            </div>
        </div>

        <div class="card-body">
            
            @if($plantas->isempty())
            
            <div class="row">
                <div class="col-md-12  alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> El usuario no tiene asignada ninguna planta en la que pueda reservar, debe asignarle plantas en los detalles de usuario o utilizando la acción de "Asignar planta"
                </div>
            </div>
            <script>
                document.getElementById('panel{{ $e->id_edificio }}').display='none';
            </script>
            @endif
            <table id="tablapuestos{{ $e->id_edificio }}" class="tabla w-100 table-responsive"  
                {{-- data-toggle="table" 
                data-mobile-responsive="true" 
                onclick="tabla_click()"
                data-locale="es-ES"
                data-search="true"
                data-show-columns="true"
                data-show-toggle="true"
                data-show-columns-toggle-all="true"
                data-page-list="[5, 10, 20, 30, 40, 50, 75, 100]"
                data-page-size="50"
                data-pagination="false" 
                data-show-pagination-switch="true"
                data-show-button-icons="true"
                data-toolbar="#all_toolbar"
                data-buttons-class="secondary"
                data-show-button-text="true"
                data-group-by="true"
                data-group-by-field="shape" --}}
                >
                <thead>
                    <tr data-fila="{{ $cuenta_fila }}">
                        @if(isset($checks) && $checks==1)<th></th>@endif
                        <th class="no-sort"></th>
                        <th data-sortable="true" >Puesto</th>
                        <th data-sortable="true" >Tipo</th>
                        <th data-sortable="true" >Estado</th>
                        <th data-sortable="true" >Ocupado por</th>
                        <th data-sortable="true" >Fecha</th>
                        <th data-sortable="true" >Reservado por</th>
                        <th data-sortable="true" >Fecha</th>
                        <th class="no-sort"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($plantas as $key=>$value)
                    @php
                        $cuenta_fila++;
                    @endphp    
                    
                    <tr class="ocultable" data-fila="{{ $cuenta_fila }}" data-tabla="tablapuestos{{ $e->id_edificio }}">
                        <td colspan="{{ isset($checks) && $checks==1?10:9 }}">
                            <h3 class="pad-all bg-gray card-header" style="font-size: 2vh">{{ $value }}
                                @if(isset($checks) && $checks==1)    
                                    <input type="checkbox" class="form-control chk_planta_puestos magic-checkbox" name="lista_id[]" data-id="{{ $key }}" id="chkpp{{ $key }}" value="{{ $key }}">
                                    <label class="custom-control-label" for="chkpp{{ $key }}"></label>
                                @endif
                            </h3>
                        </td>
                    </tr>
                    
                    @php
                        $cuenta_fila++;
                        $puestos_planta=$puestos->where('id_planta',$key);
                    @endphp
                    {{-- <table id="tabla" class="table table-condensed table-hover  table-responsive-lg table-vcenter"> --}}
                        <tr class="font-bold ocultable" data-fila="{{ $cuenta_fila }}" data-tabla="tablapuestos{{ $e->id_edificio }}">
                            <td></td>
                            <td>Puesto</td>
                            <td>Tipo</td>
                            <td class="text-center">Estado</td>
                            <td>Ocupado por</td>
                            <td>Fecha</td>
                            <td>Reservado por</td>
                            <td>Fecha</td>
                            {{-- <td></td> --}}
                        </tr>

                    
                        @foreach($puestos_planta as $puesto)
                            @php
                                $cuenta_fila++;

                                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $fec_res="";
                                if(isset($reserva)){
                                    $fec_res=beauty_fecha($reserva->fec_reserva);
                                    if(isset($reserva->fec_fin_reserva)){
                                        $fec_res.=' <i class="fa fa-arrow-right"></i> '.Carbon\Carbon::parse($reserva->fec_fin_reserva)->format('H:i');
                                    }
                                }
                                
                                if(isMobile()){
                                    if($puesto->factor_puestow<3.5){
                                        $puesto->factor_puestow=12;
                                        $puesto->factor_puestoh=12;
                                        $puesto->factor_letra=2.8;
                                    } else {
                                        //En  mosaico los queremos curadrados siempre
                                        $puesto->factor_puestow=$puesto->factor_puestow*4;
                                        $puesto->factor_puestoh=$puesto->factor_puestow*4;
                                        $puesto->factor_letra=$puesto->factor_letra*4;
                                    }
                                    
                                    
                                } else if($puesto->factor_puestow<3.5){
                                    $puesto->factor_puestow=3.7;
                                    $puesto->factor_puestoh=3.7;
                                    $puesto->factor_letra=0.8;
                                }
                                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"Mioficina",Carbon\Carbon::now()->format('d/m/Y'));
                            @endphp
                            <tr  id="puesto{{ $puesto->id_puesto }}" title="{!!  nombrepuesto($puesto)." \r\n ".$cuadradito['title'] !!}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}"  data-planta="{{ $value }}" data-fila="{{ $cuenta_fila }}">

                                <td   style="color: {{ $cuadradito['font_color'] }};">
                                    @if(isset($puesto->val_icono))
                                        <i class="{{ $puesto->val_icono }} fa-2x"  @if(isset($puesto->val_color))style="color: {{ $puesto->val_color }}" @else style="color: {{ $puesto->color_tipo }}"  @endif></i>
                                    @else
                                        <i class="{{ $puesto->icono_tipo }} fa-2x" @if(isset($puesto->val_color))style="color: {{ $puesto->val_color }}" @else style="color: {{ $puesto->color_tipo }}"  @endif></i>
                                    @endisset
                                </td>
                                <td>
                                    <div class="m-0 badge pl-1e text-start"  style="width: 100%; heigth: 100%; @if($puesto->color_puesto) background-color: {{ $puesto->color_puesto }}@endif; color: {{ $puesto->color_puesto && txt_blanco($puesto->color_puesto)=='text-white'?'#FFF':'navy' }} ">
                                        {{ $puesto->cod_puesto }}
                                    </div>
                                </td>
                                <td>{{ $puesto->des_tipo_puesto }}</td>
                                <td>
                                    @if($puesto->mca_incidencia=='N')
                                        @switch($puesto->id_estado)
                                            @case(1)
                                                <div class="bg-success badge rounded-pill"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(2)
                                                <div class="bg-danger badge rounded-pill"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(3)
                                                <div class="bg-info badge rounded-pill"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break     
                                            @case(4)
                                                <div class="bg-dark badge rounded-pill"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(5)
                                                <div class="bg-dark badge rounded-pill"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @case(7)
                                                <div class="bg-white badge rounded-pill"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;">
                                                @break
                                            @default
                                        @endswitch
                                        {{ $puesto->des_estado }}
                                    @else
                                        <div class="bg-warning badge rounded-pill"  id="estado_{{ $puesto->id_puesto }}" style="width: 100%; height: 100%;"><i class="fad fa-exclamation-triangle"></i>
                                        Incidencia
                                    @endif
                                        </div>
                                </td>
                                <td>{{ $puesto->usuario_usando }}</td>
                                <td>{!! beauty_fecha($puesto->fec_ult_estado) !!}</td>
                                <td id="nreserva_{{ $puesto->id_puesto }}">{{ $reserva->name??'' }}</td>
                                <td id="freserva_{{ $puesto->id_puesto }}">{!! $fec_res !!}</td>
                                {{-- <a href="javascript:void(0)" onclick="hoverdiv($(this),event,'toolbutton',{{ $puesto->id_puesto }},'{{ $puesto->cod_puesto }}','{{ $puesto->token }}');"><i class="fa fa-bars add-tooltip opts" title="Acciones"></i></a> --}}
                                {{-- <td class="text-center opts" style="position: relative">
                                    <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                        <div class="btn-group btn-group pull-right ml-1" role="group">
                                            @if(isAdmin() || config('app.env')=='local')<a href="#"  class="btn btn-warning btn_scan add-tooltip toolbutton"  title="Scan" onclick="scan('{{ $puesto->token }}')"  data-id="{{ $puesto->id_puesto }}"> <span class="fa fa-qrcode" aria-hidden="true"></span> Scan</a>  @endif
                                            @if(checkPermissions(['Puestos'],['W']))<a href="#"  class="btn btn-info btn_editar add-tooltip toolbutton ml-2" onclick="editar({{ $puesto->id_puesto }})" title="Editar puesto" data-id=""> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                            @if(checkPermissions(['Puestos'],['D']))<a href="#" data-target="#eliminar-puesto" title="Borrar puesto" data-toggle="modal" class="btn btn-danger add-tooltip btn_del toolbutton"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
                                            @if(checkPermissions(['Reservas'],['D']))<a href="#"  title="Cancelar Reserva" class="btn btn-pink add-tooltip btn_del toolbutton" onclick="cancelar('{{ $puesto->token }}')"><span class="fad fa-calendar-times" aria-hidden="true"></span> Res</a>@endif
                                        
                                            @if(checkPermissions(['Puestos'],['W']))
                                                <a href="#"  class="btn btn-success btn_estado add-tooltip toolbutton"  onclick="estado(1,'{{ $puesto->token }}')" title="Disponible" data-token="{{ $puesto->token }}"  data-estado="1" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-thumbs-up" aria-hidden="true"></span></a>
                                                <a href="#"  class="btn btn-danger btn_estado add-tooltip toolbutton"  onclick="estado(2,'{{ $puesto->token }}')" title="Usado"  data-token="{{ $puesto->token }}"  data-estado="2" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-lock-alt" aria-hidden="true"></span></a>
                                                <a href="#"  class="btn btn-info btn_estado add-tooltip toolbutton"  onclick="estado(3,'{{ $puesto->token }}')" title="Limpiar"  data-token="{{ $puesto->token }}"  data-estado="3" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-broom" aria-hidden="true"></span></a>
                                                
                                            @endif
                                        </div>
                                    </div>
                                </td> --}}
                            </tr>
                        @endforeach
                    
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach
{{-- <div id="toolbutton"  style="display: none;position: absolute; ">
    <div style="display: flex; flex-direction: row;">
        <div class="pad-all rounded bg-white" style="border: 3px solid navy; background-color: #fff; ">
            <label>Acciones<span class="font-bold ml-2" id="nombrepuesto"></span></label><br>
            <div class="btn-group btn-group pull-right ml-1" role="group">
                @if(isAdmin() || config('app.env')=='local')<a href="#"  class="btn btn-warning btn_scan add-tooltip toolbutton"  title="Scan" onclick="scan()"  data-id=""> <span class="fa fa-qrcode" aria-hidden="true"></span></a>@endif
                @if(checkPermissions(['Puestos'],['W']))<a href="#"  class="btn btn-info btn_editar add-tooltip toolbutton ml-2" onclick="editar()" title="Editar puesto" data-id=""> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                @if(checkPermissions(['Reservas'],['D']))<a href="#"  title="Cancelar Reserva" class="btn btn-pink add-tooltip btn_del toolbutton" onclick="cancelar()"><span class="fad fa-calendar-times" aria-hidden="true"></span> Res</a>@endif
            </div>
            <div class="btn-group btn-group pull-right" role="group">
                @if(checkPermissions(['Puestos'],['W']))
                    <a href="#"  class="btn btn-success btn_estado add-tooltip toolbutton"  onclick="estado(1)" title="Disponible" data-token=""  data-estado="1" data-id=""> <span class="fad fa-thumbs-up" aria-hidden="true"></span></a>
                    <a href="#"  class="btn btn-danger btn_estado add-tooltip toolbutton"  onclick="estado(2)" title="Usado"  data-token=""  data-estado="2" data-id=""> <span class="fad fa-lock-alt" aria-hidden="true"></span></a>
                    <a href="#"  class="btn btn-info btn_estado add-tooltip toolbutton"  onclick="estado(3)" title="Limpiar"  data-token=""  data-estado="3" data-id=""> <span class="fad fa-broom" aria-hidden="true"></span></a>
                    
                @endif
            </div>
        </div>
        <div style="color: navy; padding-top:30px">
            <i class="fas fa-caret-right fa-3x"></i>
        </div>
    </div>
</div> --}}
@section('scripts5')
    <script>
        left_toolbar=300;
        top_toolbar=216;
    </script>
    @include('puestos.scripts_lista_puestos')
@endsection
@section('scripts2')
@include('resources.leyenda_reservas')
<script>
    
$('.tabla').on('sort.bs.table', function(){
    

})


$('.chkpuesto').click(function(){
    estado=$(this).is(':checked');
    if(estado){
        $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/A",function(data){
            $('#puesto'+data.id).css("background-color",'#02c59b');
        })
    }
    else  {
        $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/D",function(data){
            $('#puesto'+data.id).css("background-color",'#eae3b8');
        })
    } 
})
var tooltip = $('.add-tooltip');
if (tooltip.length)tooltip.tooltip();

$('.chk_edificio_puestos').click(function(){
    estado=$(this).is(':checked');
    console.log(estado);
    $('[data-idedificio='+$(this).data('id')+']').each(function(){
        $(this).attr('checked',estado);
        if(estado){
            $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/A",function(data){
                $('#puesto'+data.id).css("background-color",'#02c59b');
            })
        }
        else  {
            $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/D",function(data){
                $('#puesto'+data.id).css("background-color",'#eae3b8');
            })
        } 
    })
})

$('.chk_planta_puestos').click(function(){
    estado=$(this).is(':checked');
    console.log(estado);
    $('[data-idplanta='+$(this).data('id')+']').each(function(){
        $(this).attr('checked',estado);

        if(estado){
            $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/A",function(data){
                $('#puesto'+data.id).css("background-color",'#02c59b');
            })
        }
        else  {
            $.get("{{ url($url_check??'')."/".$id_check }}/"+$(this).data('id')+"/D",function(data){
                $('#puesto'+data.id).css("background-color",'#eae3b8');
            })
        } 
    })
})

$('tr').click(function(event){
       $('#toolbutton').hide();
    })
</script>
@endsection