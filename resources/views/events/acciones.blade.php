@php
    if(isset($acciones)){
        $numacciones=$acciones->max('val_iteracion');
    } else {
        $numacciones=0;
    }
    $colores=["#b0fc99","#80dd75","#61d35f","#57ad16","#077712"];
@endphp

{{-- <div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label  for="num_acciones">[{{ $id }}] Numero de acciones</label><br>
            <select name="num_acciones" id="num_acciones" class="form-control col-6">
                @for($n=0; $n<6; $n++)
                <option value="{{ $n }}" {{ $numacciones==$n? 'selected': '' }}>{{ $n }}</option>
                @endfor
            </select>
        </div>
    </div>
</div> --}}
<div class="row">
    <div class="col-md-12">
        
    </div>
</div>

    
<div class="row" id="div_detalle_accion" style="display:none">
    <div class="col-md-12">
        <div class="card totales_resultados b-all" >
            <h4 class="mt-2 ml-2" >{{ __('eventos.parametrizacion_de_accion') }}</h4>
            <div class="card-body" id="param_accion">
            
            </div>
        </div>
    </div>
</div>

<div class="row" id=divacciones>
    @for($n=1; $n<=$numacciones; $n++)
        <div id="it{{ $n }}" data-iteracion="{{ $n }}" data-regla="{{ $id }}" class="col-md-2 b-all rounded m-2 iteracion sortable connectedSortable mr-3" style="background-color: {{ $colores[$n-1]  }}">
            <h6 class="text-center mt-2 text-white">{{ __('eventos.iteracion') }} {{ $n }}</h6>
            @foreach($acciones->where('val_iteracion',$n)->sortby('num_orden') as $acc)
                <div class="b-all rounded m-1 p-1 border-dark accion mb-2 {{ txt_blanco( genColorCodeFromText($acc->nom_accion.$id."AABA")) }}" data-regla="{{ $id }}" data-accion="{{ $acc->cod_accion }}" style="background-color: {{ genColorCodeFromText($acc->nom_accion.$id."AABAS") }}; height: 80px; color: #fff; position: relative; width:100%;">
                    @if(isset($acc->val_icono)) {!! $acc->val_icono !!} @endif
                    {{-- @if(config('app.env')=='local')[{{ $acc->cod_accion }}]@endif --}}
                    {{ str_replace(".php","",str_replace("_"," ",basename($acc->nom_accion))) }}<br>
                    <div style="position: absolute; bottom: 0; width: 100%;">
                        <a href="javascript:void(0)" class="btn_del text-white" data-regla="{{ $id }}" data-accion="{{ $acc->cod_accion }}" title="{{ __('eventos.borrar_accion') }}" data-iteracion="{{ $acc->val_iteracion }}" ><i class="fa fa-trash-alt  float-right ml-2 mr-2 mb-1"></i></a>
                        <a href="javascript:void(0)" class="btn_edit text-white"  data-regla="{{ $id }}" data-accion="{{ $acc->cod_accion }}" title="{{ __('eventos.editar_accion') }}" ><i class="fa fas fa-pencil-alt  float-right ml-2 mr-2 mb-1"></i></a>
                        <a href="javascript:void(0)" class="btn_duplicar text-white"  data-regla="{{ $id }}" data-accion="{{ $acc->cod_accion }}" title="{{ __('eventos.clonar_accion') }}" ><i class="fa-solid fa-clone float-right ml-2 mr-2 mb-1"></i></a>
                        <a href="javascript:void(0)" class="btn_info text-white"  data-regla="{{ $id }}" data-accion="{{ $acc->cod_accion }}" title="{{ __('eventos.info_accion') }}" ><i class="fa-solid fa-info  float-right ml-2 mr-2 mb-1"></i></a>
                    </div>
                </div>
            @endforeach
            <br><br>
            <div style="position: absolute; bottom: 0; width: 100%;">
                <a href="javascript:void(0)" class="btn_del text-white" data-regla="{{ $id }}" data-accion="-1"  data-iteracion="{{ $n }}" ><i class="fa fa-trash-alt  float-right ml-2 mr-4 mb-1"></i></a>
            </div>
        </div>
    @endfor
</div>

<script>


    $('#count_acciones').html('({{ $n-1 }} <i class="fad fa-recycle"></i> | {{ $acciones->count() }} <i class="fad fa-shoe-prints"></i>)');

    $('#num_acciones').change(function(){
        console.log('change');
        $('#acciones_regla').html('');
        $.post('{{url(config('app.carpeta_asset')."/acciones/".$id)}}', {_token: '{{csrf_token()}}', 'num_acciones': $(this).val()}, function(data, textStatus, xhr) {
            $('#acciones_regla').html(data);
        })
    });

    $('.btn_edit').click(function(){
        // console.log('regla: '+$(this).data('regla'));
        // console.log('accion: '+$(this).data('accion'));
        $('#param_accion').empty();
        $('#param_accion').load("{{ url(config('app.carpeta_asset').'/param_accion') }}/"+$(this).data('regla')+"/"+$(this).data('accion'));
        $('#div_detalle_accion').show();
        animateCSS('#div_detalle_accion','bounceInRight');
    });

    $('.btn_del').click(function(){
        // console.log('regla: '+$(this).data('regla'));
        // console.log('accion: '+$(this).data('accion'));
        // console.log('iteracion: '+$(this).data('iteracion'));
        $.get("{{ url(config('app.carpeta_asset').'/acciones/delete') }}/"+$(this).data('regla')+"/"+$(this).data('accion')+"/"+$(this).data('iteracion'), function(data, textStatus, xhr) {

        })
        .done(function(data) {
            toast_ok("Borrar","Item borrado correctamente");
            $('#acciones_regla').load("{{ url(config('app.carpeta_asset').'/acciones/'.$id) }}");
        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            console.log(error);
            toast_error("Error",error.error);
        })
        
        $(".accion").click(function(){
            // console.log("it"+$(this).data('iteracion'))
            iteracion_seleccionada=$(this).data('iteracion');
            $(".iteracion").css('border','');
            $(this).css('border','1px solid #1a535b');
        })
    });

    $('.draggable').draggable({
        revert: true,
        start: function(event, ui) {
            //console.log('start');
            $(this).css('z-index', '100');
        }
    });

    function recolocar(){
        iteraciones=[];
        $('.iteracion').each(function(elem){
            item=new Object();
            item.id=$(this).data('iteracion');
            item.data=$( "#it"+$(this).data('iteracion') ).sortable( "toArray", {attribute: 'data-accion'} );
            iteraciones.push(item);
        });
        //console.log(iteraciones);
        
        $.post('{{url(config('app.carpeta_asset')."/acciones/reindex/".$id)}}', {_token: '{{csrf_token()}}', 'data': JSON.stringify(iteraciones)}, function(data, textStatus, xhr) {
            //console.log(data);
        })
    }

    $( function() {
        $( ".sortable" ).sortable({
        connectWith: ".connectedSortable",
        start: function(event, ui) {
            ui.item.startPos = ui.item.index();
        }
        });
    });

    $( ".sortable" ).on( "sortreceive", function( event, ui ) {
        // console.log('sortreceive');
        // console.log('accion: '+ui.item.data('accion'));
        // console.log('from: '+ui.item.startPos);
        // console.log('to: '+ui.item.index());
        recolocar();
    });
 
    $( ".sortable" ).on( "sortupdate", function( event, ui ) {
        //console.log('sortchange');
        // console.log('accion: '+ui.item.data('accion'));
        // console.log('from: '+ui.item.startPos);
        // console.log('to: '+ui.item.index());
        recolocar();
    } );

    $('.btn_duplicar').click(function(){
        // console.log('regla: '+$(this).data('regla'));
        // console.log('accion: '+$(this).data('accion'));
        $.get("{{ url(config('app.carpeta_asset').'/acciones/duplicar') }}/"+$(this).data('regla')+"/"+$(this).data('accion'), function(data, textStatus, xhr) {
            toast_ok(data.title,data.message);
            $('#acciones_regla').load("{{ url(config('app.carpeta_asset').'/acciones/'.$id) }}");
        });
    });

    

    $('.btn_info').mouseenter(function(){
       elemento=$(this);
       $.get('{{url(config('app.carpeta_asset')."/acciones/info/")}}/'+$(this).data('accion'), function(data, textStatus, xhr) { 
            elemento.attr('title',data);

        //elemento.prop('tooltipText', 'w00t');
        })
    });



    
</script>