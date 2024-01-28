
@php
    if(!isset($id_check))
        $id_check="";
@endphp

@foreach ($edificios as $e)
@php
    $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
@endphp
<div class="card mt-2" id="panel{{ $e->id_edificio }}" style="{{ $plantas->isempty()?'display:none':'' }}">
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
        @foreach($plantas as $key=>$value)
            <div class="card border-dark mb-3">
                <div class="card-header bg-gray">
                    <h3 >{{ $value }}
                        @if(isset($checks) && $checks==1)    
                            <input type="checkbox" class="form-control chk_planta_puestos magic-checkbox" name="lista_id[]" data-id="{{ $key }}" id="chkpp{{ $key }}" value="{{ $key }}">
                            <label class="custom-control-label" for="chkpp{{ $key }}"></label>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $puestos_planta=$puestos->where('id_planta',$key);
                    @endphp
                    <div class="d-flex flex-wrap">
                        @foreach($puestos_planta as $puesto)
                            @php
                                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                if(isMobile()){
                                    $puesto->factor_puestow=15;
                                    $puesto->factor_puestoh=15;
                                    $puesto->factor_letra=2.8;
                                } else {
                                    $puesto->factor_puestow=3.7;
                                    $puesto->factor_puestoh=3.7;
                                    $puesto->factor_letra=0.8;
                                }
                                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"Mioficina",$r->fecha??Carbon\Carbon::now()->format('d/m/Y'));
                                
                            @endphp
        
                            
                            <div class="text-center rounded add-tooltip flpuesto puesto_parent draggable {{ $cuadradito['clase_disp'] }} p-2 mr-2 mb-2 " id="puesto{{ $puesto->id_puesto }}" title="{!! strip_tags( nombrepuesto($puesto)." \r\n ".$cuadradito['title']) !!}  @if(config('app.env')=='local')[#{{ $puesto->id_puesto }}]@endif" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}"  data-planta="{{ $value }}" style="height: {{ $puesto->factor_puestoh }}vw ; width: {{ $puesto->factor_puestow }}vw; background-color: {{ $cuadradito['color'] }}; color: {{ $cuadradito['font_color'] }}; {{ $cuadradito['borde'] }}">
                                @if(isset($checks) && $checks==1){{-- Mostrar checkbox para seleccionar los puestos --}}
                                    <div style="position: absolute; margin-top: 2em; margin-left: 2em">
                                        <input type="checkbox" class="form-control chkpuesto magic-checkbox" name="lista_id[]" data-id="{{ $puesto->id_puesto }}" id="chkp{{ $puesto->id_puesto }}" data-idedificio="{{ $puesto->id_edificio }}" data-idplanta="{{$puesto->id_planta}}" value="{{ $puesto->id_puesto }}" {{ isset($puestos_check) && array_search($puesto->id_puesto,$puestos_check)?'checked':'' }}>
                                        <label class="custom-control-label"   for="chkp{{ $puesto->id_puesto }}"></label>
                                    </div>
                                @endif
                                <div class="puesto_child " style="font-size: {{ $puesto->factor_letra }}vw; color: {{ $cuadradito['font_color'] }};">{{ $puesto->des_puesto }}
                                    
                                </div>
                                @include('resources.adornos_iconos_puesto')
                            </div>
                            
                        @endforeach
                    </div>
                </div>
            </div>


            
        @endforeach
    </div>
    @endforeach
    @if($edificios->isempty())    

            <i class="fas fa-exclamation-triangle"></i> El usuario no tiene asignado ningun edificio/planta en donde reservar, debe asignarle plantas en los detalles de usuario o utilizando la acción de "Asignar planta"
    @endif
    
</div>

<script>
    
    document.querySelectorAll(".chkpuesto").forEach(item => 
        item.addEventListener("click", () => {
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
    );

    document.querySelectorAll(".chk_edificio_puestos").forEach(item => 
        item.addEventListener("click", () => {
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
    );

    document.querySelectorAll(".chk_planta_puestos").forEach(item => 
        item.addEventListener("click", () => {
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
    );


</script>
