
<link href="{{url('/plugins/gridstack')}}/dist/gridstack.css" rel="stylesheet"/>
<!-- HTML5 drag&drop (70k) -->
<script src="{{url('/plugins/gridstack')}}/dist/gridstack-h5.js"></script>

@php
//  Dimensiones del fondo


$filename=storage_path()."/app/temp/".$plantas->img_plano;
try{
    if(file_exists($filename))
    {
        $data = getimagesize($filename);
        $width = $data[0];
        $height = $data[1];
        $bg_gridstack='';
    } else {
        $width = 1920;
        $height = 1080;  
        $bg_gridstack=' lightgoldenrodyellow';
    }
} catch (\Exception $e){
    $width = 1920;
    $height = 1080;  
    $bg_gridstack=' lightgoldenrodyellow';
}

@endphp

<style type="text/css">


    .layout{

        width: {{ $width }}px; 
        height: {{ $height }}px;
        background-image: url('{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$plantas->img_plano) }}');
        zoom: {{ $plantas->factor_zoom }}%;
    }
    .grid-stack {
        background: {{ $bg_gridstack  }};
        width: {{ $width }}px; 
        height: {{ $height }}px;
    }

    .grid-stack-item-content {
        color: #fff;
        text-align: center;
        background-color: #a3ffd6 ;
        glyph-orientation-vertical: middle;
        font-size: 1vw;
        font-weight: bolder;
        overflow-y: hidden !important;
        overflow-x: hidden !important;
        opacity: 0.7;
        z-index: 20;
    }

    .grid-stack-item {
        z-index: 20;
    }

    .icono_borrar, .icono_detalle{
        cursor: pointer; 
    }

    .nombre_zona{
        font-size: 3em;
        color: #fff; 
        -webkit-text-stroke: 1px #f00;
        opacity: 1 !important;
    }

    .num_zona{
        border-radius: 50%;
        width: 34px;
        height: 34px;
        line-height: 20px !important;
        font-weight: bolder;
        padding: 4px;
        background: #fff;
        border: 3px solid rgba(0, 0, 0, 0.397);
        color: rgba(0, 0, 0, 0.397);
        text-align: center;
        font: 24px Arial, sans-serif;
    }

    .flpuesto {
        float: left;
        position: absolute;
        z-index: 0;
        color: #FFFFFF;
        font-weight: bold;
        font-size: 9px;
        width: 40px;
        height: 40px;
        overflow: hidden;
        opacity: 0.35;
        border-radius: 0px;
        border: 1px dashed #111;
        background-color: #888;
    }

    .card_plano{
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        padding: 0px 0px 0px 0px;
        margin: 0px 0px 0px 0px;
    }

    .container {
        border: 1px solid #DDDDDD;
        width: 100%;
        position: relative;
        padding: 0px 0px 0px 0px !important;
        margin: 0px 0px 0px 0px !important;
        --bs-gutter-x: 0 !important;
        --bs-gutter-y: 0 !important;
    }

    
    </style>
    
        <div class="card editor mb-5">
            <div class="card-header toolbar">
                <div class="toolbar-start">
                    <h5 class="m-0">Distribucion de zonas en planta {{ $plantas->des_planta }}</h5>
                </div>
                <div class="toolbar-end">
                    <button type="button" class="btn-close btn-close-card">
                        <span class="visually-hidden">Close the card</span>
                    </button>
                </div>
            </div>
            
            @if(isset($error))
            <div class="alert alert-danger">
                {{-- <button class="close" data-dismiss="alert"><i class="pci-cross pci-circle"></i></button> --}}
                <strong>ERROR!</strong> {{ $error }}
            </div>
            @else
            <div class="card-body">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ url('/plantas/save_zonas') }}" id="edit_plantas_form" name="edit_plantas_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="json_zonas" id="json_zonas" value="{{ $plantas->json_zonas??"" }}"> 
                            <input type="hidden" name="id_planta" value="{{ $plantas->id_planta }}">
                            <input type="hidden" name="width" value="{{ $width }}">
                            <input type="hidden" name="height" value="{{ $height }}">
                            <input type="hidden" name="factor_zoom" id="factor_zoom" value="{{ $plantas->factor_zoom }}">
                            <div class="row">
                                <div class="col-md-1 text-end">
                                    <div>
                                        <div class="btn-group btn-group-sm" role="group" style="margin-top: 23px">
                                            <a href="#" id="pre-add-widget"  class="btn btn-success" title="Añadir zona">
                                                <span  class="fa fa-plus-square pt-1" style="font-size: 20px" aria-hidden="true"></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-5" style="display: none" id="editor_nombre">
                                    <label for="">Nombre</label>
                                    <div class="input-group float-right" id="div_fechas">
                                        <input type="text" class="form-control pull-left" id="nombre" name="nombre" style="width: 120px">
                                        <span class="btn input-group-text btn-success" style="height: 40px" id="add-widget"><i class="fa-solid fa-square-arrow-down"></i> Añadir</span>
                                    </div>
                                </div>

                                <div class="col-md-2" class="row b-all"  style="margin-top: 30px">
                                    <label class="control-label float-left mr-2">zoom</label>
                                    <i class="fa fa-minus-square float-left mt-1 mr-1" onclick="zoom(-1)"></i>
                                    <span id="zoom_level" class="float-left">{{ $plantas->factor_zoom }}%</span>
                                    <i class="fa fa-plus-square float-left mt-1 ml-1"  onclick="zoom(+1)"></i>
                                </div>

                                
                                <div class="col-md-9"></div>
                            </div>
                            @if(isset($plantas->img_plano))
                            <div class="card ">
                                <div class="card-body overflow-auto card_plano layout">
                                    <div class="grid-stack container" id="plano">
                                        @php
                                            $left=0;
                                            $top=0;
                                        @endphp
                                        @foreach($puestos as $puesto)
                                            @php
                                            $asignado_usuario=null;
                                            $asignado_miperfil=null;
                                            $asignado_otroperfil=null;
                                            $custom=false;
                                            $reserva=null;
                                            $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"Zonas",Carbon\Carbon::now()->format('d/m/Y'));
                                            if($puesto->top==null && $puesto->left==null){
                                                $puesto->color_estado="secondary";
                                            }
                                            if($puesto->width!=null || $puesto->height!=null || $puesto->border!=null || $puesto->font!=null  || $puesto->roundness!=null){
                                                $custom=true;
                                            }
                                            @endphp
                                            <div class="text-center font-bold  add-tooltip align-middle flpuesto puesto_parent draggable add-tooltip {{ $custom?'custom':'' }}" title="{{ $puesto->des_puesto }}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-factorh="{{ $puesto->factor_puestoh }}" data-factorw="{{$puesto->factor_puestow}}" data-factorr="{{ $puesto->factor_puestor }}" data-width="{{ $puesto->width??0 }}" data-height="{{ $puesto->height??0 }}" style="height: {{ $puesto->factor_puestoh }}% ; width: {{  $puesto->factor_puestow }}%;">
                                                {!! $custom?'<i class="fa-solid fa-circle-small"></i>':'' !!}
                                                <span class="h-100 align-middle texto_puesto puesto_child {{ $puesto->font!=null?'custom':'' }}" style="font-size: {{ $puesto->font!=null?$puesto->font:$puesto->factor_letra }}vw;">{{ nombrepuesto($puesto) }}</span>
                                            </div>
                                            @php
                                                $left+=50;
                                                if($left==500){
                                                    $left=0;
                                                    $top+=50;
                                                }
                                            @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @endif
                            <div class="form-group">
                                <div class="col-md-12 text-end">
                                    <input class="btn btn-primary" id="btn_guardar" type="button" value="Guardar">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
            
            @endif
    
            
        </div>
    
    <script>
        function stringToColor(string, saturation = 100, lightness = 75){
            hash = 0;
            for (let i = 0; i < string.length; i++) {
                hash = string.charCodeAt(i) + ((hash << 5) - hash);
                hash = hash & hash;
            }
            return `hsl(${(hash % 360)}, ${saturation}%, ${lightness}%)`;
        }
        
        serializedFull=[];

        zonas=0;
        function addNewWidget() {
            
            $( ".grid-stack-item-content" ).each(function() {
                if ($(this).attr("idzona")>zonas){zonas=$(this).attr("idzona")}
            });
            let n = items[count] || {
                w: 20,
                h: 12,
                autoPosition : true
            };
            zonas ++;
            id="zona"+zonas;
            n.text=$("#nombre").val();
            n.id="zona"+randomString(10);
            n.content = "<i class='fa-duotone fa-circle-info text-secondary icono_detalle  mr-3' onClick='detallenodo(this.parentNode.parentNode)' title='Info de la zona'></i><i class='fa-solid fa-trash-can text-danger icono_borrar' onClick='grid.removeWidget(this.parentNode.parentNode)' title='Borrar zona'></i><div class='num_zona'>"+zonas+"</div><div class='nombre_zona'>"+n.text+"</div>";
            grid.addWidget(n);
            //item=grid.addWidget($('<div><div class="grid-stack-item-content"  onclick="seleccionada('+zonas+')" idzona='+zonas+' >'+$("#nombre").val()+'</div></div>'), 0, 0, Math.floor(1 + 3 * Math.random()), Math.floor(4 + 3 * Math.random()), true,null,null,null,null,id);
            
        }

        function detallenodo(item){
           $.get("{{ url('/plantas/zonas/detalle') }}/"+$(item).attr('gs-id'), function(data, status){
                data=JSON.parse(data);
                cuenta=data.length;
                texto="";
                for (let i = 0; i < cuenta; i++) {
                    texto=texto+data[i].cod_puesto+"<br>";
                    $('#puesto'+data[i].id_puesto).css('background-color','#ff9f1a');
                    $('#puesto'+data[i].id_puesto).css('opacity','1');
                }
                toast_info(cuenta+ ' puestos',texto);
                setTimeout(function(){
                    for (let i = 0; i < cuenta; i++) {
                        $('#puesto'+data[i].id_puesto).css('background-color','#888');
                        $('#puesto'+data[i].id_puesto).css('opacity','0.35');
                    }
                },5000);
            });
        }

        $("#pre-add-widget").click(function() {
            $("#editor_nombre").val();
            $("#editor_nombre").show();
            animateCSS('#editor_nombre', 'bounceIn');
        });

        $('#add-widget').click(function() {
            addNewWidget();
            $("#editor_nombre").hide();
        });
        
        $('.form-ajax').submit(form_ajax_submit);

        var options = {
            alwaysShowResizeHandle: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
            margin : 1,
            removeTimeout:500,
            disableOneColumnMode:true,
            animate:true,
            removable: false,
            resizable: { handles: 'all'},
            float: true,
            cellHeight: 10,
            column :100,
            acceptWidgets: true,
            dragIn: '.newWidget',  // class that can be dragged from outside
            dragInOptions: { revert: 'invalid', scroll: false, appendTo: 'body', helper: 'clone' }, // clone or can be your function
            removable: '#trash', // drag-out delete class
        };
        grid = GridStack.init(options);

        grid.on('added', function(event, items) {
            zonas=1;
            items.forEach(function(item){
                item.el.style.backgroundColor = stringToColor(item.text);
                item.el.style.opacity=0.7;
                //item.id="zona"+randomString(10);
                zonas++;
            })
        });

        items=[];
        count = 0;

        saveGrid = function() {
            delete serializedFull;
            serializedData = grid.save();
            document.querySelector('#json_zonas').value = JSON.stringify(serializedData, null, '  ');
        }

        loadGrid = function() {
            grid.load(serializedData, true); // update things
            $( ".grid-stack-item-content" ).each(function(item) {
               if ($(this).find('.icono_detalle').length==0){
                    $(this).prepend($('<i class="fa-duotone fa-circle-info text-secondary icono_detalle mr-3" onClick="detallenodo(this.parentNode.parentNode)" title="Info de la zona"></i>'));
                }
            });
            
        }

        @if($plantas->zonas!==null && !is_array($plantas->zonas) )
            serializedData ={!! json_decode(json_encode($plantas->zonas)) !!};
            loadGrid();
            
        @endif

        function zoom(direccion){
            zoom_actual=100*$('.layout').css('zoom');
            zoom_actual=(zoom_actual+(10*direccion));
            $('.layout').css('zoom',zoom_actual+'%');
            $('#zoom_level').text(zoom_actual+'%');
            $('#factor_zoom').val(zoom_actual);
            recolocar_puestos();
        }
       
    
        $('#btn_guardar').click(function(){
            event.preventDefault();
            saveGrid();
            $('#edit_plantas_form').submit();
           
            
        });


        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        try{
            posiciones={!! json_encode($plantas->posiciones)??'[]' !!};
            document.getElementById('plano').setAttribute('data-posiciones',posiciones);
            posiciones=$.parseJSON(posiciones);
        
        } catch($err){
            posiciones=[];
        }

        $(window).resize(function(){
            recolocar_puestos();
        })

        $('.nav-toggler').click(function(){
            recolocar_puestos();
        })

        setTimeout(() => {
            recolocar_puestos();
        }, 1500);
        
    
        document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
    </script>