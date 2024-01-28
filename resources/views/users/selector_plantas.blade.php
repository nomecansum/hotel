@php
    $edificio_ahora=0;
    $planta_ahora=0;
@endphp

@foreach ($edificios as $e)
<div class="card">
    <div class="card-header bg-gray-dark">
        <div class="row">
            <div class="col-md-5">
                <div class="form-check pt-2 float-left">
                    <input  name="lista_id[]" data-id="{{ $e->id_edificio }}" id="chke{{ $e->id_edificio }}" value="{{ $e->id_edificio }}" class="form-check-input chk_edificio" type="checkbox">
                    <label class="form-check-label" for="chke{{ $e->id_edificio }}">
                        <span class="ml-2 mt-2  fs-4"><i class="fad fa-building"></i> {{ $e->des_edificio }}
                    
                        </span>
                    </label>
                </div>
                
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-3 text-end">
                <h4 class="text-white">
                    <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                    <span class="mr-2"><i class="fa-solid fa-door-open"></i> {{ $e->puestos }}</span>
                </h4>
            </div>
        </div>
    </div>
    <div class="card-body">
        @php
            $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
        @endphp
        <div class="d-flex flex-wrap">
            @foreach($plantas as $key=>$value)
                @php
                    $zonas_planta=$zonas->where('id_planta',$key);
                @endphp
                <div class="font-bold rounded mr-2 mb-2 align-middle pad-all pastilla  add-tooltip" title="{{ $value }}" id="pastilla{{ $key }}" style="font-size: 12px; width: 200px; overflow: hidden;  {{ $check&&in_array($key,$plantas_usuario)?'background-color: #02c59b': 'background-color: #eae3b8' }}">
                    <span class="h-100 align-middle pl-1" >
                        <div class="form-check pt-2 ml-2 mt-1">
                            <input   name="lista_id[]" data-id="{{ $key }}" data-edificio="{{ $e->id_edificio }}" id="chkpl{{ $key }}" value="{{ $key }}" {{ $check&&in_array($key,$plantas_usuario)?' checked ': '' }} class="form-check-input chkplanta" type="checkbox">
                            <label class="form-check-label" for="chkpl{{ $key }}">{{ substr($value,0,21) }} {{ strlen($value)>19?'...':'' }}</label>
                        </div>
                        @foreach($zonas_planta as $z)
                        @php
                            
                        @endphp
                            <div class="form-check pt-2 ml-5 mt-1">
                                <input   name="lista_zonas[]" data-id="{{ $z->num_zona }}"  data-planta="{{ $key }}" id="chkzo{{ $z->key_id }}" value="{{ $z->num_zona }}" {{ $check&&in_array($z->num_zona,$zonas_usuario)?' checked ': '' }} class="form-check-input chkzona" type="checkbox">
                                <label class="form-check-label" style="font-weight: normal" for="chkpl{{ $z->num_zona }}">{{ substr($z->des_zona,0,21) }} {{ strlen($z->des_zona)>19?'...':'' }}</label>
                            </div>
                        @endforeach
                        
                    </span>
                </div>
            @endforeach
        </div>
        
    </div>
</div>
@endforeach


    <script>
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();
        @if($check)
        //{{-- Esta logica solo se ejecuta cuando estamos en detalle de usuario, para los multiples la pongo en la pagina del listado de usuarios --}}
        $('.chkplanta').click(function(){
            if($(this).is(':checked')){
                $.get("{{ url('users/addplanta/'.$id) }}/"+$(this).data('id'),function(data){
                    $('#pastilla'+data.id).css("background-color",'#02c59b');
                })
            } else {
                $.get("{{ url('users/delplanta/'.$id) }}/"+$(this).data('id'),function(data){
                    $('#pastilla'+data.id).css("background-color",'#eae3b8');
                })
            }
        })
        @endif

        $('.chkzona').click(function(){

            if($(this).is(':checked')){
                $('#chkpl'+$(this).data('planta')).attr('checked',true);
            }
        })

        $('.chk_edificio').click(function(){
            estado=$(this).is(':checked');
            
            console.log(estado);
            $('[data-edificio='+$(this).data('id')+']').each(function(){
                $(this).attr('checked',estado);
                @if($check)
                 //{{-- Esta logica solo se ejecuta cuando estamos en detalle de usuario, para los multiples la pongo en la pagina del listado de usuarios --}}
                    if(estado){
                        $.get("{{ url('users/addplanta/'.$id) }}/"+$(this).data('id'),function(data){
                            $('#pastilla'+data.id).css("background-color",'#02c59b');
                        })
                    }
                    else  {
                        $.get("{{ url('users/delplanta/'.$id) }}/"+$(this).data('id'),function(data){
                            $('#pastilla'+data.id).css("background-color",'#eae3b8');
                        })
                    } 
                @endif
            })
        })
    </script>