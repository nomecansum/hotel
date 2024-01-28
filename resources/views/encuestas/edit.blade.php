<link rel="stylesheet" href="{{ url('/plugins/html5-editor/bootstrap-wysihtml5.css') }}" />

<div class="card editor mb-5">

    <div class="card-header toolbar">
        <div class="toolbar-start">
            <h5 class="m-0">Editar encuesta</h5>
        </div>
        <div class="toolbar-end">
            <button type="button" class="btn-close btn-close-card">
                <span class="visually-hidden">Close the card</span>
            </button>
        </div>
    </div>

    <div class="card-body">

        @if ($errors->any())
            <ul class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @php
         //dd($encuesta);   
        @endphp
        
        @if($encuesta->id_encuesta==0)
            <form method="POST" action="{{ url('/encuestas/save') }}" id="edit_encuesta_form" name="edit_encuesta_form" accept-charset="UTF-8" class="form-horizontal form-ajax" >
        @else
            <form method="POST" action="{{ url('/encuestas/update') }}" id="edit_encuesta_form" name="edit_encuesta_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            <input name="id_encuesta" type="hidden" value="{{ $encuesta->id_encuesta }}">
        @endif
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('titulo') ? 'has-error' : '' }}">
                    <label for="des_planta" class="control-label">Titulo</label>
                        <input class="form-control" required name="titulo" type="text" id="titulo" value="{{ $encuesta->titulo }}" maxlength="300" placeholder="Enter titulo here...">
                        {!! $errors->first('titulo', '<p class="help-block">:message</p>') !!}
                </div>
            </div>

            <div class="row mt-2">
                <div class="form-group col-md-12 {{ $errors->has('pregunta') ? 'has-error' : '' }}">
                    <label for="pregunta" class="control-label">Pregunta</label>
                    <textarea  class="textarea_editor form-control" required name="pregunta" id="pregunta" rows="8" maxlength="5000" placeholder="Enter text ...">{!! $encuesta->pregunta !!}</textarea>
                        {!! $errors->first('pregunta', '<p class="help-block">:message</p>') !!}
                </div>
            </div>

            <div class="row mt-2">
                <div class="form-group col-md-3">
                    <label for="id_cliente" class="control-label">Cliente</label>
                    <select class="form-control" required id="id_cliente" name="id_cliente">
                        @foreach ($clientes as $key => $cliente)
                            <option value="{{ $key }}" {{ $encuesta->id_cliente == $key ? 'selected' : '' }}>
                                {{ $cliente }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-5">
                    <label for="" class="control-label">Tipo</label><br>
                    <input type="hidden" name="id_tipo_encuesta" id="id_tipo_encuesta" value="{{$encuesta->id_tipo_encuesta}}"></inpout>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"  title="Tipo de encuesta">
                            <img src="{{ url('/img',$encuesta->img_tipo) }}" id="img_tipo">  <span id="des_tipo" class="ml-3">{{ $encuesta->des_tipo_encuesta }}</span> <i class="dropdown-caret"></i>
                        </button>
                        <ul class="dropdown-menu" id="dropdown-acciones">
                            @foreach($tipos as $tipo)
                                <li class="dropdown-item"><a href="#" data-tipo="{{ $tipo->id_tipo_encuesta }}" data-imagen="{{ $tipo->img_tipo }}" data-desc="{{ $tipo->des_tipo_encuesta }}" class="btn_tipo_check"> <img src="{{ url('/img',$tipo->img_tipo) }}"><div>{{ $tipo->des_tipo_encuesta }}</div> </a></li>
                            @endforeach
                        </ul>
                    </div>
                    

                </div>
                <div class="form-group col-md-1" style="margin-top: 7px">
                    <label for="val_color">Color</label><br>
                    <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($encuesta->val_color)?$encuesta->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                </div>
                <div class="form-group col-md-1 mt-2" style="margin-left: 10px">
                    <div class="form-group">
                        <label>Icono</label><br>
                        <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                    </div>
                </div>
                
               
            </div>
            <div class="row mt-2">
                <div class="form-group col-md-4" >
                    <label>Fechas </label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control pull-left rangepicker" id="fechas" name="fechas" value="{{  Carbon\Carbon::parse($encuesta->fec_inicio)->format('d/m/Y').' - '.Carbon\Carbon::parse($encuesta->fec_fin)->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-secondary btn_fechas" ><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </div>
                
                <div class="form-group col-md-3">
                    <label for="val_momento" class="control-label">Momento para mostrar</label>
                    <select class="form-control" required id="val_momento" name="val_momento">
                            <option value="0" {{ $encuesta->val_momento == '0' ? 'selected' : '' }}>Independiente</option>
                            <option value="A" {{ $encuesta->val_momento == 'A' ? 'selected' : '' }}>Al escanear sitio</option>
                            <option value="D" {{ $encuesta->val_momento == 'D' ? 'selected' : '' }}>Al dejar sitio</option>
                    </select>
                </div>
                <div class="form-group col-md-3 {{ $errors->has('val_periodo_minimo') ? 'has-error' : '' }}">
                    <label for="val_periodo_minimo" class="control-label">Intervalo minimo votos (min)</label>
                        <input class="form-control" required name="val_periodo_minimo" type="number" id="val_periodo_minimo" value="{{ $encuesta->val_periodo_minimo }}" min="0" max="1440" placeholder="Enter periodo minimo">
                        {!! $errors->first('val_periodo_minimo', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 p-t-30 mt-1">
                    <div class="form-check pt-1">
                        <input name="mca_mostrar_comentarios"  id="mca_mostrar_comentarios" value="S" {{ $encuesta->mca_mostrar_comentarios=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                        <label for="mca_mostrar_comentarios" class="form-check-label">Pedir feedback</label>
                    </div>
                </div>
                <div class="col-md-2 p-t-30 mt-1">    
                    <div class="form-check pt-1">
                        <input name="mca_anonima"  id="mca_anonima" value="S" {{ $encuesta->mca_anonima=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                        <label for="mca_anonima" class="form-check-label">Anonima</label>
                    </div>
                </div>
                <div class="col-md-2 p-t-30 mt-1">
                    <div class="form-check pt-1">
                        <input name="mca_activa"  id="mca_activa" value="S" {{ $encuesta->mca_activa=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                        <label for="mca_activa" class="form-check-label">Activa</label>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="form-group col-md-12">
                    <label class="control-label">Token</label>
                    <div class="input-group mb-3">
                        <input type="text" name="token" readonly=""  id="token_1uso"  class="form-control" value="{{$encuesta->token }}">
                        <div class="input-group-btn">
                            <button class="btn btn-secondary" type="button"  id="btn_generar_token">Generar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row b-all mt-2" style="width: 98%">
                <div class="form-group col-md-11">
                    <label>URL de acceso</label><br>
                    <a href="{{ url('encuestas/get',$encuesta->token) }}" id="link_url" target="_blank"  rel="noopener noreferrer"><h5 id="span_url">{{ url('encuestas/get',$encuesta->token) }}</h5></a>
                </div>
                <div class="form-group col-md-1 text-end mt-3">
                    <a href="#modal_img"  class="btn  btn-warning add-tooltip  btn_url text-nowrap" id="btn_gen_qr" data-toggle="modal" title="Generar QR" data-id="{{ $encuesta->id_encuesta }}" data-url="{{ url('encuestas/get',$encuesta->token) }}" style="width: 86px"> <span class="fad fa-qrcode pt-1" aria-hidden="true"></span> Ver QR</a>
                    <a href="#"  class="btn  btn-info  add-tooltip btn_url text-nowrap" id="boton_url" title="Copiar URL" data-id="{{ $encuesta->id_encuesta }}" data-clipboard-text="{{ url('encuestas/get',$encuesta->token) }}" style="width: 86px"> <span class="fa fa-copy" aria-hidden="true"></span> Copiar</a>
                    <a href="{{ url('encuestas/get',$encuesta->token) }}" target="_blank"  rel="noopener noreferrer" class="btn  btn-success  add-tooltip btn_url" id="boton_abrir" title="Abrir URL" data-id="{{ $encuesta->id_encuesta }}" data-url="{{ url('encuestas/get',$encuesta->token) }}" style="width: 86px"> <i class="fad fa-external-link-square-alt"></i> Abrir</a>
                </div>
            </div>
            <div class="row mt-2">
                <div class="form-group col-md-12">
                    <label>Aplicar solo a perfiles</label><br>
                    <select name="perfiles[]" id="list_perfiles" class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple">
                        <option value=""></option>
                        @foreach ($perfiles as $p)
                            <option {{ in_array($p->cod_nivel,explode(",",$encuesta->list_perfiles)) ? 'selected' : ''}} value="{{$p->cod_nivel}}">{{$p->des_nivel_acceso}}</option>
                        @endforeach
                    </select>
                </div>
                
            </div>
            <div class="row">
                @include('resources.combos_filtro',[$hide=['cli'=>1,'est'=>1,'head'=>1,'btn'=>1,'usu'=>1,'est_inc'=>1,'tip_mark'=>1, 'tip_inc'=>1]])
            </div>
           
            <div class="row">
                <div class="form-group col-md-12 text-end mt-3">
                    <input class="btn btn-primary" type="submit" value="Guardar">
                </div>
            </div>
            
        </form>

    </div>
</div>
<div class="modal fade" id="modal_img">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">QR </h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div> 
            <div class="modal-body">
                <img style="width:100%" id="img_accion">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="btn_download"><i class="fad fa-download"></i> Descargar</button>
                <button type="button" data-dismiss="modal" class="btn btn-warning close"  onclick="cerrar_modal()">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ url('/plugins/clipboard-js/clipboard.js') }}"></script>
<script src="{{ url('/plugins/html5-editor/wysihtml5-0.3.0.js') }}"></script>
<script src="{{ url('/plugins/html5-editor/bootstrap-wysihtml5.js') }}"></script>
<script>

    $(".select2").select2({
        placeholder: "Seleccione",
        allowClear: true,
        width: "90%",
    });

    $('.btn_fechas').click(function(){
        rangepicker.show();
    })

    $('#btn_gen_qr').click(function(){
        $('#modal_img').modal('show');
    })

    //Ponemos los valores seleccionados que tengan los select2
    $(document).ready(function() {
       //console.log($("#multi-planta"));
        $('.scroll-top').click();
       s=setTimeout('end_update_filtros()',2000);
       
    });

    // function end_update_filtros(entidad){
    //     console.log('end update '+entidad)
    //     $('#multi-planta').select2().val(61,);
    //    //$('#multi-planta').trigger('change');
    //    console.log($('#multi-planta').select2().val());
    // }
    function end_update_filtros(entidad){
        //window.scrollTo(0,0);
        console.log('end_update');
        string="{{ $encuesta->list_edificios }}"
        var arr = string.split(',');
        $('#multi-edificio').select2().val(arr);

        string="{{ $encuesta->list_plantas }}"
        var arr = string.split(',');
        $('#multi-planta').select2().val(arr);

        string="{{ $encuesta->list_puestos }}"
        var arr = string.split(',');
        $('#multi-puesto').select2().val(arr);

        string="{{ $encuesta->list_tags }}"
        var arr = string.split(',');
        $('#multi-tag').select2().val(arr);

        string="{{ $encuesta->list_tipos }}"
        var arr = string.split(',');
        $('#multi-tipo').select2().val(arr);

        $('#multi-edificio').select2().val();
        $('#multi-planta').select2().val();
        $('#multi-puesto').select2().val();
        $('#multi-tag').select2().val();
        $('#multi-tipo').select2().val();

        
    }

    var clipboard = new ClipboardJS('#boton_url');

    $('#id_cliente').change(function(){
        $("#multi-cliente").val($("#id_cliente").val());
        $("#multi-cliente").change(function(){
        
        });
    })

    $('#btn_download').click(function(){
        var gh = $('#img_accion').attr('src');

        var a  = document.createElement('a');
        a.href = gh;
        a.download = 'QR.png';

        a.click();
    })

    var rangepicker = new Litepicker({
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
        setup: (rangepicker) => {
            rangepicker.on('selected', (date1, date2) => {
                //comprobar_puestos();
            });
        }
    });

    $('.form-ajax').submit(form_ajax_submit);
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
    $('.btn_tipo_check').click(function(){
        $('#id_tipo_encuesta').val($(this).data('tipo'));
        $('#img_tipo').attr('src',"{{ url('/img') }}/"+$(this).data('imagen'));
        $('#des_tipo').html($(this).data('desc'));
    })

    

    $('#val_icono').iconpicker({
        icon:'{{isset($encuesta) ? ($encuesta->val_icono) : ''}}'
    });
    $('.textarea_editor').wysihtml5();

    

    $('#btn_generar_token').click(function(event){
        //console.log('token');
        $.get( "/clientes/gen_key")
        .done(function( data, textStatus, jqXHR ) {
            $('#token_1uso').val(data);
            $('.btn_url').data('clipboard-text',"{{ url('encuestas/get') }}/"+data);
            $('.btn_url').data('url',"{{ url('encuestas/get') }}/"+data);
            $('#link_url').attr('href',"{{ url('encuestas/get') }}/"+data);
            $('#span_url').html("{{ url('encuestas/get') }}/"+data);
            $('#boton_abrir').attr('href',"{{ url('encuestas/get') }}/"+data);
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
                console.log(errorThrown);
        });	
    })

    $('#btn_gen_qr').click(function(){
        console.log($(this).data('url'));
        $.post('{{url('/gen_qr')}}', {_token:'{{csrf_token()}}',url: $(this).data('url')}, function(data, textStatus, xhr) {
            $('#img_accion').attr('src','data:image/png;base64, '+data);
		});
    })

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
</script>
@include('layouts.scripts_panel')

@yield('scripts2')