<link rel="stylesheet" href="{{ asset('/plugins/html5-editor/bootstrap-wysihtml5.css') }}" />
<link href="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet">
<style>
    .textnothide {
        display: block !important;
        position: absolute;
        z-index: -1;
    }
</style>
@php $padre=""; @endphp
<div class="row form_param">
    @foreach($parametros as $p)
        @if(isset($p->id_campo_padre) && $p->id_campo_padre!=$padre)
            @php $padre=$p->id_campo_padre; @endphp
            <div class="w-100"></div>
        @endif
        @php
            if(isset($p->num_nivel)){
                $margin=50*$p->num_nivel;
                $margin='style="margin-left: '.$margin.' px;"';
            } else {
                $margin="";
            }
            //sufijo por defecto para cuando la accion no es la if_then_else
            if(!isset($sufijo)){
                $sufijo="";
            }

            if(!isset($p->required)){
                $p->required=false;
            }
        @endphp

        {{-- Etiqueta --}}
        @if($p->tipo=="lbl")
            <div class="w-100"></div>
            <div class="col-md-{{ $p->width??3 }} {{ $p->margin??'' }} pt-3"  {{ $margin }}>
                <div class="form-group">
                    <h3 class="font-weight-bold">{{ ucfirst(str_replace("_"," ",$p->label)) }}</h3>
                </div>
            </div>

        @endif

        {{-- Tipo de campo oculto --}}
        @if($p->tipo=="hidden" || $p->tipo=="hid")
            <input type="hidden" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}"  value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}">
        @endif

        {{-- Boolean Checkbox --}}
        @if($p->tipo=="bool" || $p->tipo=="bol")
            <div class="col-md-{{ $p->width??3 }} pt-3 mb-4 mt-2 {{ $p->margin??'' }}">
                <div class="custom-control custom-switch" {{ $margin }}>
                    <input type="checkbox" name="{{ $p->name.$sufijo }}" class="custom-control-input form-check-input {{ $p->classname??'' }}" id="{{ $p->name.$sufijo }}" value="1" {{!isset($p->value)?(isset($p->def)&&$p->def===true?'checked':''):($p->value==1&&!is_array($p->value)?'checked':'') }}>
                    <label class="custom-control-label pt-1" for="{{ $p->name.$sufijo }}">{{ $p->label }}</label>
                </div>
            </div>
        @endif

        {{-- Campo de texto --}}
        @if($p->tipo=="txt")
            <div class="col-md-{{ $p->width??6 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                    <input type="text" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" class="form-control {{ $p->classname??'' }}" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

        {{-- Campo de file --}}
        @if($p->tipo=="img")
            <div class="col-md-{{ $p->width??6 }} {{ $p->margin??'' }} campo_file"  {{ $margin }}>
                <div class="form-group">
                    <label for="{{ $p->name.$sufijo }}">{{ $p->label }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input {{ $p->classname??'' }}" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" lang="es" value="{{ isset($p->value)&&!is_array($p->value)&& !is_object($p->value)? $p->value : ''}}" {{ $p->required==true?'required':'' }}>
                        <label class="custom-file-label" for="{{ $p->name.$sufijo }}">{{ isset($p->value) && !is_object($p->value)? $p->value : $p->label}}</label>
                      </div>
                </div>
            </div>
        @endif

         {{-- Campo de texto grander --}}
         @if($p->tipo=="textarea")
            <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3"><label for="{{ $p->name.$sufijo }}" class="control-label w-100 mt-2">{{ $p->label }} </label></div>
                    </div>
                    <textarea  class="json_format form-control {{ $p->classname??'' }}" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" rows="6" style="height: 200px" placeholder="Enter text ..." {{ $p->required==true?'required':'' }}>{!! isset($p->value)&&!is_array($p->value)? $p->value : '' !!}</textarea>
                </div>
            </div>
        @endif

        {{-- Campo de texto enriquecido --}}
        @if($p->tipo=="html5" || $p->tipo=="htm")
            <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                    <textarea  class="textarea_editor form-control {{ $p->classname??'' }}" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" rows="6" style="height: 200px;" placeholder="Enter text ..." {{ $p->required==true?'required':'' }}>{!! isset($p->value)&&!is_array($p->value)? $p->value : $p->def !!}</textarea>
                </div>
            </div>
        @endif


        {{-- Campo numerico --}}
        @if($p->tipo=="num")
            <div class="col-md-{{ $p->width??3 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                    <input type="number" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" class="form-control col-10 {{ $p->classname??'' }}" min="{{ $p->min??'' }}" max="{{ $p->max??'' }}" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

        {{-- Campo fecha --}}
        @if($p->tipo=="fec")
            <div class="col-md-{{ $p->width??5 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                    <input type="date" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" class="form-control col-10  {{ $p->classname??'' }}" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

        {{-- Campo lista de email --}}
        @if($p->tipo=="email")
            <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                    <input type="text" class="form-control edit_tag typeahead {{ $p->classname??'' }}" data-role="tagsinput" id="{{ $p->name.$sufijo }}" name="{{ $p->name.$sufijo }}" placeholder="{{ __('general.addadir_usuarios') }}" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

        {{-- Combo proveniente de consulta de BDD --}}
        @if($p->tipo=="list_db")
            @php
                $qr=DB::select($p->sql);
                $qr=collect($qr);
                try{
                    if (!fullAccess()){
                        $qr=$qr->orWhereIn('id_cliente',clientes());
                    }
                } catch(\Exception $e){

                }
            @endphp

            <div @if($p->multiple) class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}" @else class="col-md-{{ $p->width??4 }} {{ $p->margin??'' }}"  @endif  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                    <select @if($p->multiple) class="select2 mb-2 col-md-11 select2-multiple multi2{{ $sufijo }} form-control {{ $p->classname??'' }}" style="width: 100%" multiple="multiple" name="{{ $p->name.$sufijo }}[]"  @else class="form-control {{ isset($p->buscar)&&$p->buscar==true?'select2_bus':'' }} {{ $p->classname??'' }}"  name="{{ $p->name.$sufijo }}" style="height: 50px"  @endif id="list_db-{{ $p->name.$sufijo }}" {{ $p->required==true?'required':'' }}>
                        @foreach($qr as $item)

                            @if($p->multiple)
                                <option value="{{$item->id}}" {{ isset($p->value) && is_array ($p->value) && in_array($item->id,$p->value)===true?'selected':'' }}>{{$item->nombre}}</option>
                            @else
                                <option value="{{$item->id}}" {{ isset($p->value) && $item->id==$p->value?'selected':'' }}>{{$item->nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

        @endif

        @if($p->tipo=="tags")
            @php
                $qr=[];
            @endphp

            <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}" {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}
                        @include('resources.spin_puntitos',['id_spin'=>'spin_tag{{ $p->name.$sufijo }}','clase'=>'spin_tag'])
                    </label><br>
                    <select class="select2 mb-2 col-md-11 select2-multiple form-control multi2{{ $sufijo }} multitags {{ $p->classname??'' }}" style="width: 100%" multiple="multiple" name="{{ $p->name.$sufijo }}[]" id="tags-{{ $p->name.$sufijo }}" {{ $p->required==true?'required':'' }}>
                        @foreach($qr as $item)
                        <option value="{{$item->id_tag}}" {{ isset($p->value) && is_array ($p->value) && in_array($item->id_tag,$p->value)===true?'selected':'' }}>{{$item->nombre_tag}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        @endif

        @if($p->tipo=="disp")
            @php
                $qr=[];
            @endphp

            <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}" {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}
                        @include('resources.spin_puntitos',['id_spin'=>'spin_disp{{ $p->name.$sufijo }}','clase'=>'spin_disp'])
                    </label><br>
                    <select class="select2 mb-2 col-md-11 select2-multiple form-control multi2{{ $sufijo }} multidispositivos {{ $p->classname??'' }}" style="width: 100%" multiple="multiple" name="{{ $p->name.$sufijo }}[]" id="disp-{{ $p->name.$sufijo }}" {{ $p->required==true?'required':'' }}>
                        @foreach($qr as $item)
                        <option value="{{$item->id_dispositivo}}" {{ isset($p->value) && is_array ($p->value) && in_array($item->id_dispositivo,$p->value)===true?'selected':'' }}>{{$item->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        @endif

        {{-- Color picker --}}
        @if($p->tipo=="color" || $p->tipo=="col")
            <div class="col-md-{{ $p->width??2 }} mr-4 {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                    <input type="color" autocomplete="off"  name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" class="colorpicker form-control {{ $p->classname??'' }}" style="padding: 0 0 0 0"  value="{{ isset($p->value)? $p->value : (isset($p->def)?$p->def:(!isset($p->random) || (isset($p->random) && $p->random==true)?App\Classes\RandomColor::one(['luminosity' => 'bright']):'')) }}" {{ $p->required==true?'required':'' }} />
                </div>
            </div>
        @endif

         {{-- Icon picker --}}
         @if($p->tipo=="icon" || $p->tipo=="ico")
         <div class="col-md-{{ $p->width??2 }} mr-4 {{ $p->margin??'' }} campo_icon"  {{ $margin }}>
             <div class="form-group">
                 <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                 <button type="button" autocomplete="no"  role="iconpicker" required  name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}"  data-iconset="fontawesome5"  @if(isset($p->container))data-container="#{{ $p->container }}"@endif data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker {{ $p->classname??'' }}"  style="padding: 0 0 0 0; height: 40px"  data-search="true" data-rows="10" data-cols="22" data-search-text="Buscar..."></button>
             </div>
         </div>
     @endif

        {{-- Lista de elementos con pares lista/valor --}}
        @if($p->tipo=="list" || $p->tipo=="lis")
            <div @if($p->multiple) class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}" @else class="col-md-{{ $p->width??4 }} {{ $p->margin??'' }}" @endif  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name.$sufijo }}">{{ $p->label }}</label><br>
                    <select @if($p->multiple) class="mb-2 col-md-11 select2-multiple form-control {{ $p->classname??'' }}" multiple="multiple" style="width: 100%" name="{{ $p->name.$sufijo }}[]"  @else class="form-control {{ isset($p->buscar)&&$p->buscar==true?'select2_bus':'' }} {{ $p->classname??'' }}"  name="{{ $p->name.$sufijo }}"  @endif id="@if($p->multiple)multi-@endif{{ $p->name.$sufijo }}" {{ $p->required==true?'required':'' }}>
                        @php
                            $lista=explode("|",$p->list);
                            $valores=explode("|",$p->values);
                            $index=0;
                        @endphp
                        @foreach($lista as $item)                               {{-- Si es un valor sencillo --}}                                           {{-- Si es un valor multiple --}}
                            <option value="{{ $valores[$index]}}" {{ (isset($p->value) && $valores[$index]==$p->value) || (isset($p->value) && is_array ($p->value) && in_array($valores[$index],$p->value))===true?'selected':'' }}>{{ $item}} {{ isset($p->value) && $valores[$index]==$p->value?'selected':'' }}</option>
                            @php $index++; @endphp
                        @endforeach
                    </select>

                </div>
            </div>
        @endif

        {{-- Esto es solo para hacer el campo de cliente mandatory si se ha puesto el parametro correspondiente --}}
        @if($p->tipo=="cli")
            @if($p->required==true)
                <script>
                    $('#multi-clientes').attr("required", "true");
                    $('#multi-clientes').attr("data-placeholder", "Debe seleccionar al menos un cliente");
                    //$('#multi-clientes').select2({placeholder:"Debe seleccionar al menos un cliente"});
                </script>
            @endif
        @endif

        @if($p->tipo=="hidecli")
            <script>
                $('.group_cliente').hide();
            </script>
        @endif

        {{-- Cuando queremos añadir un salto de linea en el form ponermos un atributo en el campo de antes br: true --}}
        @if((isset($p->br) && $p->br==true) || ($p->tipo=="br"))
            <div class="row"><br></div>
        @endif

        {{-- Campo http url --}}
        @if($p->tipo=="http_url")
            <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3"><label for="{{ $p->name.$sufijo }}" class="control-label w-100 mt-2">{{ $p->label }} </label></div>
                        <div class="col-md-9 text-end mt-2"><a href="#modal-url" data-bs-toggle="modal" data-target="#modal-url" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda URL"></i></a></div>
                    </div>
                    <div class="input-group mb-3 ">
                        <input type="text" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" class="form-control campo_url {{ $p->classname??'' }}" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : '' }}" {{ $p->required==true?'required':'' }}>
                        <div class="input-group-append">
                            <a class="input-group-text btn btn-warning btn_preview" target="_blank"  rel="noopener noreferrer" @if(isset($p->value)&&!is_array($p->value)) href="{{ $p->value }}" @endif><i class="fa-solid fa-file-magnifying-glass"></i> Preview</a>
                        </div>
                    </div>
                    
                </div>
            </div>
        @endif

        {{-- Campo http header --}}
        @if($p->tipo=="http_header")
            <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3"><label for="{{ $p->name.$sufijo }}" class="control-label w-100 mt-2">{{ $p->label }} (JSON)</label></div>
                        <div class="col-md-9 text-end mt-2"><a href="#modal-param_header" data-bs-toggle="modal" data-target="#modal-param_header" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda Header"></i></a></div>
                    </div>
                    <textarea  class="json_format form-control {{ $p->classname??'' }}" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" rows="6" style="height: 200px" placeholder="Enter text ..." {{ $p->required==true?'required':'' }}>{!! isset($p->value)&&!is_array($p->value)? $p->value : '' !!}</textarea>
                </div>
            </div>
        @endif

        {{-- Campo http header --}}
        @if($p->tipo=="http_body")
            <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}"  {{ $margin }}>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3"><label for="{{ $p->name.$sufijo }}" class="control-label w-100 mt-2">{{ $p->label }} (JSON)</label></div>
                        <div class="col-md-9 text-end mt-2"><a href="#modal-param_body" data-bs-toggle="modal" data-target="#modal-param_body" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda Body"></i></a></div>
                    </div>
                    <textarea  class="json_format form-control {{ $p->classname??'' }}" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" rows="6" style="height: 200px" placeholder="Enter text ..." {{ $p->required==true?'required':'' }}>{!! isset($p->value)&&!is_array($p->value)? $p->value : '' !!}</textarea>
                </div>
            </div>
        @endif

         {{-- Campo http header --}}
         @if($p->tipo=="http_response")
         <div class="col-md-{{ $p->width??12 }} {{ $p->margin??'' }}"  {{ $margin }}>
             <div class="form-group">
                 <div class="row">
                     <div class="col-md-3"><label for="{{ $p->name.$sufijo }}" class="control-label w-100 mt-2">{{ $p->label }} (JSON)</label></div>
                     <div class="col-md-9 text-end mt-2"><a href="#modal-param_respuesta" data-bs-toggle="modal" data-target="#modal-param_respuesta" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda response"></i></a></div>
                 </div>
                 <textarea  class="json_format form-control {{ $p->classname??'' }}" name="{{ $p->name.$sufijo }}" id="{{ $p->name.$sufijo }}" rows="6" style="height: 200px" placeholder="Enter text ..." {{ $p->required==true?'required':'' }}>{!! isset($p->value)&&!is_array($p->value)? $p->value : '' !!}</textarea>
             </div>
         </div>
     @endif

    @endforeach

</div>

{{-- @include('resources.modales_form_parametros_http') --}}
<script src="{{ asset('/plugins/html5-editor/wysihtml5-0.3.0.js') }}"></script>
<script src="{{ asset('/plugins/html5-editor/bootstrap-wysihtml5.js') }}"></script>
<script src="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('/plugins/typeahead-js/typeahead.bundle.js') }}"></script>
<script>
    $(".select2-multiple").select2({
            allowClear: true
        });

    @foreach($parametros as $p)    
        @if($p->tipo=="icon" || $p->tipo=="ico")
            $('#{{ $p->name }}').iconpicker(
                @if(valor_parametro($parametros,$p->name)!="")
                    { icon:'{{$p->value}}' }
                @endif
            );  
        @endif
    @endforeach
    $("#comando").select2({
        allowClear: false,
    });

    $(".select2_bus").select2({
        allowClear: false,
    });

    $('.campo_url').keyup(function() {
        var url = $(this).val();
        $('.btn_preview').attr('href', url);
        console.log("change url");
    });



    function prettyPrint(el){
        try{
            var ugly = el.val();
            var obj = JSON.parse(ugly);
            var pretty = JSON.stringify(obj, undefined, 4);
            //console.log(pretty);
            el.val(pretty);
        } catch(e){ }
    }

    $('textarea').on('change keyup paste', function() {
        prettyPrint($(this));
    });

    $('.textarea_editor').wysihtml5({
        events: {
            load: function () {
                $('.textarea_editor').addClass('textnothide');
            }
        }
    });

    $('.custom-file-input').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
    
    $('.edit_tag').tagsinput({
        confirmKeys: [13, 44],
        freeInput: true,
        allowDuplicates: false
    });


    $('.edit_tag').on('beforeItemAdd', function(event) {
        //console.log(event.item);
        if(validateEmail(event.item)==null){
            event.item=null;
            event.cancel=true;
            event.preventDefault=true;
        }
    });

</script>
