
    @php
        use App\Models\puestos_tipos;   
    @endphp
    <div class="card editor mb-5">

        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nuevo tipo de incidencia
                    @else
                        Editar tipo de incidencia @if(config('app.env')=='local') #{{$tipo->id_tipo_incidencia}} @endif
                    @endif
                </h5>
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

            <form method="POST" action="{{ url('/incidencias/tipos/save') }}" id="edit_tipos_incidencia_form" name="edit_tipos_incidencia_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="des_tipo_incidencia" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_tipo_incidencia" type="text" id="dedes_tipo_incidencias_edificio" value="{{ old('des_tipo_incidencia', optional($tipo)->des_tipo_incidencia) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_tipo_incidencia', '<p class="help-block">:message</p>') !!}
                        </div>

                        @if(checkPermissions(['Tipos de incidencia'],['D']) && ( $tipo->mca_fijo!='S' || ($tipo->mca_fijo=='S' && fullAccess())))
                        <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                            <label for="id_cliente" class="control-label">Cliente</label>
                            <select class="form-control" required id="id_cliente" name="id_cliente">
                                @foreach ($Clientes as $key => $Cliente)
                                    <option value="{{ $key }}" {{ (old('id_cliente', optional($tipo)->id_cliente) == $key)||$id==0 && $key==session('CL')['id_cliente'] ? 'selected' : '' }}>
                                        {{ $Cliente }}
                                    </option>
                                @endforeach
                            </select>
                                
                            {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                        </div>
                        @endif

                        
                </div>
                <div class="row mt-2">
                    
                    <div class="form-group col-md-2 {{ $errors->has('id_estado_inicial') ? 'has-error' : '' }}">
                        <label for="id_estado_inicial" class="control-label">Estado inicial</label>
                        <select class="form-control" required id="id_estado_inicial" name="id_estado_inicial">
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id_estado }}" {{ old('id_estado_inicial', optional($tipo)->id_estado_inicial) == $estado->id_estado ? 'selected' : '' }}>
                                    {{ $estado->des_estado }}
                                </option>
                            @endforeach
                        </select>
                            
                        {!! $errors->first('id_estado_inicial', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="val_responsable" class="control-label">Responsable</label>
                        <input class="form-control"  name="val_responsable" type="text" id="val_responsable" value="{{ old('val_responsable', optional($tipo)->val_responsable) }}" maxlength="200" placeholder="Enter val_responsable here...">
                        {!! $errors->first('val_responsable', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-1">
                        <label for="id_tipo_salas" class="control-label">ID en salas</label>
                        <input class="form-control"  name="id_tipo_salas" type="text" id="id_tipo_salas" value="{{ old('id_tipo_salas', optional($tipo)->id_tipo_salas) }}" maxlength="200" placeholder="Enter id_tipo_salas here...">
                        {!! $errors->first('id_tipo_salas', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="id_tipo_externo" class="control-label">ID externo</label>
                        <input class="form-control"  name="id_tipo_externo" type="text" id="id_tipo_externo" value="{{ old('id_tipo_externo', optional($tipo)->id_tipo_externo) }}" maxlength="200" placeholder="Enter id_tipo_externo here...">
                        {!! $errors->first('id_tipo_externo', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-1">
                        <label for="val_color">Color</label><br>
                        <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($tipo->val_color)?$tipo->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="form-group col-md-1" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker"  data-selectedClass="btn-warning"   data-unselectedClass="btn-primary"  name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-placement="bottom"  class="btn btn-light iconpicker" data-iconset-version="5.3.1_pro" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..." value="{{isset($tipo->val_icono)?$tipo->val_icono:''}}"></button>
                        </div>
                    </div>
                    <div class="form-group col-md-2 {{ $errors->has('mca_aplica') ? 'has-error' : '' }}">
                        <label for="mca_aplica" class="control-label">Aplica a</label>
                        <select class="form-control" required id="mca_aplica" name="mca_aplica">
                            <option value="I" {{ old('mca_aplica', optional($tipo)->mca_aplica) == 'I' ? 'selected' : '' }}>Incidencias</option>
                            <option value="S" {{ old('mca_aplica', optional($tipo)->mca_aplica) == 'S' ? 'selected' : '' }}>Solicitudes</option>
                            <option value="A" {{ old('mca_aplica', optional($tipo)->mca_aplica) == 'A' ? 'selected' : '' }}>Ambos</option>
                        </select>
                            
                        {!! $errors->first('mca_aplica', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="form-group  col-md-12" style="{{ (isset($hide['tip']) && $hide['tip']==1) ? 'display: none' : ''  }}">
                        <label>Tipo de puesto</label>
                        <div class="input-group select2-bootstrap-append">
                            <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="tipos_puesto[]" id="multi-tipo" >
                                @php
                                    $tipos_puesto=explode(",",$tipo->list_tipo_puesto);
                                @endphp
                                @foreach($tipos as $tp)
                                    <option value="{{ $tp->id_tipo_puesto }}" {{ in_array($tp->id_tipo_puesto,$tipos_puesto)?'selected':'' }}>{{ $tp->des_tipo_puesto }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-btn">
                                <button class="btn btn-primary select-all btn_todos" data-select="multi-estado"  type="button" style="margin-left:-10px; height: 45px"><i class="fad fa-check-double"></i> todos</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 text-end">
                        @if(checkPermissions(['Tipos de incidencia'],['D']) && ( $tipo->mca_fijo!='S' || ($tipo->mca_fijo=='S' && fullAccess())))<input class="btn btn-primary" type="button" id="boton_submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
                    </div>
                </div>
                <hr class="mt-5 mb-5">
                <div class="row bg-gray mt-4">
                    <div class="col-md-3 pt-3">
                        <h5>Postprocesado de la incidencia<h5>
                    </div>
                    <div class="col-md-3 ">

                        <div class="input-group mt-2">
                            <label class="pt-2 mr-2 font-bold">Momento</label>
                            <select class="form-control col-md-2 float-left" required id="val_momento" name="val_momento">
                                    <option value="C">Creacion</option>
                                    <option value="A">Accion</option>
                                    <option value="E">Estado</option>
                                    <option value="F">Cierre</option>
                                    <option value="R">Reapertura</option>
                            </select>
                            <a href="#nueva-incidencia" id="btn_nueva" class="btn btn-success text-white" data-toggle="modal" title="Nueva accion" style="padding-top: 0.25rem">
                                <i class="fa fa-plus-square pt-2"aria-hidden="true"></i>
                                <span>Nueva</span>
                            </a>
                        </div>                        
                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-4">
                        <div class="input-group mt-2">
                            <select class="form-control col-md-2 float-left" required id="id_incidencia_import" name="id_incidencia_import">
                                <option value=""></option>
                                @foreach($tipos_incidencia as $tp)
                                    <option value="{{ $tp->id_tipo_incidencia }}">{{ $tp->des_tipo_incidencia }}</option>
                                @endforeach
                            </select>
                            <a href="#nueva-incidencia" id="btn_importar" class="btn btn-secondary text-white" data-toggle="modal" title="Nueva accion" style="padding-top: 0.5rem">
                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                <span>Importar</span>
                            </a>
                        </div>  
                        
                    </div>
                    
                </div>
                

                <div class="row b-all rounded">
                    <div class="col-md-12" id="divacciones"></div>
                </div>
                

                
            </form>

        </div>
    </div>

    <div class="modal fade" id="importar-post-incidencia" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form method="POST" action="{{ url('/incidencias/tipos/postprocesado/copiar') }}" accept-charset="UTF-8" class="form-horizontal form-ajax" id="frm_import_tipo">	
            @csrf
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <input type="hidden" name="tipo_origen" id="tipo_origen">
                        <input type="hidden" name="tipo_destino" id="tipo_destino" value="{{ $tipo->id_tipo_incidencia }}">
                        <input type="hidden" name="momento" value="C" id="import_momento">
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                        <h1 class="modal-title text-nowrap">Importar postprocesado de incidencia</h1>
                        <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                            <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                        </button>
                    </div>
                    <div class="modal-body text-start">
                        <h4>¿Que configuracion de postprocesado quiere importar?</h4>
                        <div class="form-check mb-2">
                            <input type="radio" name="data_importar" id="tip_importar_1" value="1" checked><label id="lbl_import_1">
                            <label for="data_importar" class="form-check-label">
                                Solo creacion
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input type="radio" name="data_importar" id="tip_importar_todo" value="T"><label id="lbl_import_T">
                            <label for="tip_importar_todo" class="form-check-label">
                                Todo el postprocesado
                            </label>
                        </div>
                        <div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation"></i> Atencion! El postprocesado actual del tipo de incidencia se borrará</div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-info btn_importar_post_incidencia">Importar</button>
                        <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cancelar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function prettyPrint(el){ 
            
           try{
            var ugly = el.val();
            var obj = JSON.parse(ugly);
            var pretty = JSON.stringify(obj, undefined, 4);
            //console.log(pretty);
            el.val(pretty);
           } catch(e){
            
           }
        }


        $('.form-ajax').submit(form_ajax_submit);
        $('.opciones').hide();
        $('#tip_metodo').change(function(){
            $('.opciones').hide();
            $('.'+$(this).val()).show();
        })
        $('#tip_metodo').change();

        

    //$('#frm_contador').on('submit',form_ajax_submit);
    $('#frm_contador').submit(form_ajax_submit);

    $('#val_icono').iconpicker({
        icon:'{{isset($tipo->val_icono) ? ($tipo->val_icono) : ''}}'
    });

    $(function(){
        $('#divacciones').load("{{ url('/incidencias/tipos/postprocesado/'.$tipo->id_tipo_incidencia) }}/C");
    });

    $('#val_momento').change(function(){
        $('#divacciones').load("{{ url('/incidencias/tipos/postprocesado/'.$tipo->id_tipo_incidencia) }}/"+$('#val_momento').val());
        $('#import_momento').val($('#val_momento').val());
    });

    $('#boton_submit').on('click',function(){
        event.preventDefault();
        if($(".btn_save ").is(":visible")){
            toast_warning('Datos sin guardar','Tiene pasos de postprocesado pendientes de guardar. Utilice el boton azul al lado del paso correspondiente,  para guardar los pasos de postprocesado');
        } else {
            $(this).submit();
        }
    });

    $('.select-all').click(function(event) {
        $(this).parent().parent().find('select option').prop('selected', true)
        $(this).parent().parent().find('select').select2({
            placeholder: "Todos",
            allowClear: true,
            @desktop width: "90%", @elsedesktop width: "75%", @enddesktop 
        });
        $(this).parent().parent().find('select').change();
    });

    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        @desktop width: "90%", @elsedesktop width: "75%", @enddesktop 
    });

    $('#btn_importar').click(function(){
        if($('#id_incidencia_import').val()==''){
            toast_warning('No hay tipos de incidencia','Seleccione un tipo de incidencia para importar');
        } else {
            $('#tipo_origen').val($('#id_incidencia_import').val());
            $('#import_momento').val($('#val_momento').val());
            $('#importar-post-incidencia').modal('show');
        }
    });

    $('#btn_nueva').click(function(){
        $.get("{{ url('/incidencias/tipos/add_procesado',$id) }}/"+$('#val_momento').val(),function(){
            $('#divacciones').load("{{ url('/incidencias/tipos/postprocesado',$id) }}/"+$('#val_momento').val());
        });
    });  

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    
    </script>
    @include('layouts.scripts_panel')