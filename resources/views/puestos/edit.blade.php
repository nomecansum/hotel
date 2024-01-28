<style type="text/css">
#edit_tag .tt-dropdown-menu {
  max-height: 150px;
  overflow-y: auto;
}
 .fc .fc-toolbar-title{
    font-size: 0.8em !important;
  }

  .fc-list-event td{
    font-size: 0.8em !important;
  }
  .fc-list-day-cushion{
    font-size: 0.8em !important;
  }

</style>
<div class="card editor mb-5" id="editor">
    <div class="card">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Modificar puesto {{ $puesto->id_puesto }}</h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form  action="{{url('puestos/update')}}" method="POST" name="frm_contador" id="frm_contador" class="form-ajax"  enctype="multipart/form-data">
               
                <div class="row">
                    <input type="hidden" name="id_puesto" value="{{ $puesto->id_puesto }}">
                    <input type="hidden" name="tags" value="" id="tags">
                    <input type="hidden" name="id_cliente" value="{{ Auth::user()->id_cliente }}">
                    <input type="hidden" name="token" value="{{ $puesto->id_puesto!=0?$puesto->token:Illuminate\Support\Str::random(50) }}">
                    <input type="hidden" name="val_color" value="{{ $puesto->val_color }}" id="ed_val_color">

                    {{csrf_field()}}
                    <div class="form-group col-md-2">
                        <label for="cod_puesto">Identificador</label>
                        <input type="text" name="cod_puesto" id="cod_puesto" class="form-control" value="{{$puesto->cod_puesto}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="des_puesto">Nombre descriptivo</label>
                        <input required type="text" name="des_puesto" id="des_puesto" class="form-control" required value="{{$puesto->des_puesto}}">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="id_estado">Estado</label>
                        <select name="id_estado" id="id_estado"  class="form-control">
                            @foreach(DB::table('estados_puestos')->get() as $estado)
                            <option value="{{ $estado->id_estado}}" {{$puesto->id_estado==$estado->id_estado?'selected':''}}>{{ $estado->des_estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label for="val_color">Color</label><br>
                        <input type="color" autocomplete="off" name="sel_color" id="sel_color"  class="form-control" value="{{$puesto->id_puesto==0?App\Classes\RandomColor::one(['luminosity' => 'bright']):$puesto->val_color??''}}" />
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" required name="val_icono"  id="val_icono" data-iconset="fontawesome5"   data-iconset-version="5.3.1_pro"   class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    @if(session('CL')['mca_reserva_horas']=='S')
                    <div class="form-group col-md-2">
                        <label for="max_horas_reservar">Max reserva(horas)</label>
                        <input type="text" autocomplete="off" name="max_horas_reservar" id="max_horas_reservar"   class="form-control hourMask" value="{{isset($puesto->max_horas_reservar)?decimal_to_time($puesto->max_horas_reservar):'23:59'}}" />
                       {{--  <input type="number" min="1" max="999999" name="max_horas_reservar" id="max_horas_reservar" class="form-control" value="{{$puesto->max_horas_reservar}}">  --}}
                        
                    </div>
                    
                    @endif
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="id_edificio">Edificio</label>
                        <select name="id_edificio" id="id_edificio" class="form-control" data-planta="{{ $puesto->id_planta }}">
                            @foreach(DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $edificio)
                                <option value="{{ $edificio->id_edificio}}" {{ $puesto->id_edificio==$edificio->id_edificio?'selected':'' }}>{{ $edificio->des_edificio }}</option>
                            @endforeach
                        </select>
                    </div>
                    @php

                    @endphp
                    <div class="form-group col-md-3">
                        <label for="planta">Planta</label>
                        <select name="id_planta" id="id_planta" class="form-control">
                            @foreach(DB::table('plantas')->where('id_cliente',Auth::user()->id_cliente)->where('id_edificio',$puesto->id_edificio)->get() as $planta)
                                <option value="{{ $planta->id_planta}}" {{ $puesto->id_planta==$planta->id_planta?'selected':'' }}>{{ $planta->des_planta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 p-t-30">

                        <div class="form-check pt-2">
                            <input name="mca_acceso_anonimo"  id="mca_acceso_anonimo" value="S" {{ $puesto->mca_acceso_anonimo=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                            <label for="mca_acceso_anonimo" class="form-check-label">Permitir anonimo</label>
                        </div>
                    </div>
                    <div class="col-md-2  p-t-30">

                        <div class="form-check pt-2">
                            <input name="mca_reservar"  id="mca_reservar" value="S" {{ $puesto->mca_reservar=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                            <label for="mca_reservar" class="form-check-label">Permitir reserva</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="id_usuario">Tipo de puesto</label>
                        <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                            @foreach($tipos as $t)
                                <option value="{{ $t->id_tipo_puesto}}" {{ isset($puesto->id_tipo_puesto) && $puesto->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }}>{{ $t->des_tipo_puesto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="planta">Tags</label>
                        <input type="text" class="edit_tag typeahead" data-role="tagsinput" id="edit_tag" placeholder="Type to add a tag" size="17" value="{{ $tags }}">
                    </div>
                    {{-- <div class="form-group col-md-6">
                        <label for="id_usuario">Asignado permanentemente a usuario</label>
                        <select name="id_usuario" id="id_usuario" class="form-control select2">
                            <option value="0"></option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id}}" {{ isset($puesto->id_usuario) && $puesto->id_usuario==$u->id?'selected':'' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="id_perfil">Asignado permanentemente a perfil</label>
                        <select name="id_perfil" id="id_perfil" class="form-control select2">
                            <option value="0"></option>
                            @foreach($perfiles as $n)
                                <option value="{{ $n->cod_nivel}}" {{ isset($puesto->id_perfil) && $puesto->id_perfil==$n->cod_nivel?'selected':'' }}>{{ $n->des_nivel_acceso }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    
                </div>
                <div class="row mt-4 mb-4">
                    <div class="col-md-12   text-end ">
                        @if(checkPermissions(['Puestos'],["W"]))<button type="submit" class="btn btn-primary mr-2 btn_submit">GUARDAR</button>@endif  
                    </div>
                </div>
                
                <div class="row b-all rounded pad-all mb-3" id="divsalas" @if(!in_array($puesto->id_tipo_puesto,config('app.tipo_puesto_sala'))) style="display:none" @endif>
                    <div class="col-md-12">
                        <label class="font-bold">Sala de reunion</label>
                    </div>
                    
                    <div class="form-group col-md-2">
                        <label for="val_capacidad">Capacidad</label>
                        <input type="number" min="1" max="40" name="val_capacidad" id="val_capacidad" class="form-control" value="{{$puesto->val_capacidad??1}}">
                    </div>
                    <div class="form-group col-md-8">
                        <label for="val_capacidad">Observaciones</label>
                        <input type="text" name="obs_sala" id="obs_sala" class="form-control" value="{{$puesto->obs_sala??''}}">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="val_capacidad">ID Externo salas</label>
                        <input type="text" name="id_externo_salas" id="id_externo_salas" class="form-control" value="{{$puesto->id_externo_salas??''}}">
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-2 text-center">
                            <i class="fad fa-projector fa-2x"></i>
                            <div class="form-check pt-2">
                                <input name="mca_proyector"  id="mca_proyector" value="S" {{ isset($puesto->mca_proyector)&&$puesto->mca_proyector=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                <label for="mca_proyector" class="form-check-label">Proyector</label>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <i class="fad fa-tv-alt fa-2x"></i>
                            <div class="form-check pt-2">
                                <input name="mca_pantalla"  id="mca_pantalla" value="S" {{ isset($puesto->mca_pantalla)&&$puesto->mca_pantalla=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                <label for="mca_pantalla" class="form-check-label">Pantalla</label>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <i class="fad fa-webcam fa-2x"></i>
                            <div class="form-check pt-2">
                                <input name="mca_videoconferencia"  id="mca_videoconferencia" value="S" {{ isset($puesto->mca_videoconferencia)&&$puesto->mca_videoconferencia=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                <label for="mca_videoconferencia" class="form-check-label">Videoconferencia</label>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <i class="fad fa-volume-up fa-2x"></i>
                            <div class="form-check pt-2">
                                <input name="mca_manos_libres"  id="mca_manos_libres" value="S" {{ isset($puesto->mca_manos_libres)&&$puesto->mca_manos_libres=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                <label for="mca_manos_libres" class="form-check-label">Manos Libres</label>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <i class="fad fa-chalkboard fa-2x"></i>
                            <div class="form-check pt-2">
                                <input  name="mca_pizarra"  id="mca_pizarra" value="S" {{ isset($puesto->mca_pizarra)&&$puesto->mca_pizarra=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                <label for="mca_pizarra" class="form-check-label">Pizarra</label>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <i class="fad fa-chalkboard-teacher fa-2x"></i>
                            <div class="form-check pt-2">
                                <input  name="mca_pizarra_digital"  id="mca_pizarra_digital" value="S" {{ isset($puesto->mca_pizarra_digital)&&$puesto->mca_pizarra_digital=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                <label for="mca_pizarra_digital" class="form-check-label">Pizarra digital</label>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="row mb-0">
                    <div class="col-md-6 mb-0 p-0" style="padding-left: 18px">
                        <label>Imagen</label>
                    </div>
                    <div class="col-md-6 p-0 pl-2">
                        <label>Posicion en el plano</label>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 text-center b-all" style="padding-left: 18px">
                        <img src="{{ isset($puesto) ? Storage::disk(config('app.img_disk'))->url('img/puestos/'.$puesto->img_puesto) : ''}}" style="width: 90%;  margin-top: 50px" alt="" class="img-fluid ml-0">
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_puesto" id="img_puesto" lang="es">
                                <label class="custom-file-label" for="img_puesto"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6 text-center b-all" style="padding-left: 18px">
                        @include('puestos.posicion_plano')
                    </div>
                </div>
                {{-- <div class="row mt-4 ">
                    <div class="col-md-12   text-end ">
                        @if(checkPermissions(['Puestos'],["W"]))<button type="submit" class="btn btn-primary mr-2 btn_submit">GUARDAR</button>@endif  
                    </div>
                </div> --}}
                <div class="row mt-4 ">
                    <div class="col-md-1"></div>
                    <div class="fluid col-md-10">
                        <div class="card">
                            <div class="card-header">
                                Uso del puesto
                            </div>
                            <div class="card-body" id='demo-calendar'></div>
                        </div>
                        
                        {{-- <div id='demo-calendar'></div> --}}
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </form>
        </div>
    </div>
 </div>
 <script src="{{ asset('plugins/typeahead-js/main.js') }}"></script>
 

 <script>
     
    var tipos_puestos_sala=[{{ implode(',', config('app.tipo_puesto_sala')) }}];
    $('#id_tipo_puesto').change(function(){
        console.log(tipos_puestos_sala);
        console.log(parseInt($(this).val()));
        console.log($.inArray(parseInt($(this).val()),tipos_puestos_sala));
        if($.inArray(parseInt($(this).val()),tipos_puestos_sala)){
            $('#divsalas').hide();
        }  else {
            $('#divsalas').show();
        }
    });

     $('.btn_submit').click(function(){
        //$('#frm_contador').submit();
     })


    $('#frm_contador').submit(form_ajax_submit);

    
    

    //$('#frm_contador').on('submit',form_ajax_submit);
    

     document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    $('#val_icono').iconpicker({
        @if(isset($puesto->val_icono))
        icon:'{{$puesto->val_icono??''}}'
        @else
        icon: ''
        @endif
    });

    $('#id_edificio').change(function(){
        $('#id_planta').load("{{ url('/combos/plantas/') }}/"+$(this).val(), function(){
           
        });
    })

    $('.edit_tag').on("keypress", function(e) {
            /* ENTER PRESSED*/
            if (e.keyCode == 13) {
                /* FOCUS ELEMENT */
                e.preventDefault();
            }
        });


    var data = {!! js_array($tags_cliente) !!};
    var lista_tags = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: $.map(data, function (elem) {
            return {
                name: elem
            };
        })
    });
    lista_tags.initialize();


    $('.edit_tag').tagsinput({
        typeaheadjs: [{
            minLength: 1,
            highlight: true,
        },{
            minlength: 1,
            name: 'lista_tags',
            displayKey: 'name',
            valueKey: 'name',
            source: lista_tags.ttAdapter()
        }],
        freeInput: true,
        allowDuplicates: false,
    });

    $('.edit_tag').on('itemAdded', function(event) {
        $('#tags').val($(".edit_tag").tagsinput('items'));
    });

    $(function(){
        $('#id_planta').load("{{ url('/combos/plantas/') }}/"+$('#id_edificio').val(), function(){
            $('#id_planta').val({{ $puesto->id_planta }});
            $('#id_planta option[value={{ $puesto->id_planta }}]').attr('selected','selected');
        });
        
    })

    $(".select2").select2({
        placeholder: "Seleccione",
        allowClear: true,
        width: "90%",
    });

    $('#id_perfil').on('select2:select', function (e) {
        $('#id_usuario').val(null).trigger('change');
    });
    $('#id_usuario').on('select2:select', function (e) {
        $('#id_perfil').val(null).trigger('change');
    });

    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $(this).next('label').html(fileName);
        //$('.custom-file-label').html(fileName);
    });

    $('#sel_color').change(function(){
        $('#ed_val_color').val($(this).val());
    });



    // Initialize the calendar
    // -----------------------------------------------------------------
    var calendarEl = document.getElementById('demo-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            editable: false,
            initialView: 'listWeek',
            droppable: false, // this allows things to be dropped onto the calendar
            drop: function() {
                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    $(this).remove();
                }
            },
            eventLimit: true, // allow "more" link when too many events
            locale: 'es',
            firstDay: 1,
            themeSystem: 'bootstrap',
            moreLinkClick: "popover",
            events: {!! $eventos !!}
        });
        calendar.render();

    $('.fc-event-title').css('font-size','10px');
    $('.fc-event-title').css('font-weight','normal');
    $('.fc-toolbar-chunk').find(".btn").addClass("btn-xs");
    @mobile 
        $('.fc-today-button').hide(); 
    @endmobile

    Inputmask({regex:"^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$"}).mask('.hourMask');
    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

 </script>
