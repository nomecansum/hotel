<style type="text-css">
    .noUi-pips-horizonta{
        padding: 0px;
        margin-top: 0px;
        line-height: 0px
    }

    .select2-choices {
        min-height: 150px;
        max-height: 150px;
        overflow-y: auto;
    }
    
    

</style>

<div class="card mb-5" id="editor">
    <div class="card">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Editar reserva</h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form  action="{{url('reservas/save')}}" method="POST" name="frm_contador" id="frm_contador" class="form-ajax">
                <div class="row">
                    <input type="hidden" name="id_reserva" value="{{ $reserva->id_reserva }}">
                    <input type="hidden" name="id_cliente" value="{{ $reserva->id_cliente }}">
                    <input type="hidden" name="salas" value="1">
                    <input type="hidden" id="id_puesto" name="id_puesto" value="{{ $sala!=0?$sala:'' }}">
                    <input type="hidden" id="des_puesto_form" name="des_puesto" value="">
                    <input type="hidden" name="tipo_vista" id="tipo_vista" value="{{ session('CL')['modo_visualizacion_reservas']=='P'?'comprobar_plano':'comprobar' }}">
                    <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ Carbon\Carbon::now()->format('H:i') }}">
                    <input type="hidden" name="hora_fin" id="hora_fin" value="{{ Carbon\Carbon::now()->addHours()->format('H:i') }}">
                    {{csrf_field()}}
                    <div class="form-group col-md-4">
                        <label for="fechas">Fecha</label>
                        {{--  <div class="input-group">
                            <input type="text" class="form-control pull-left singledate" id="fechas" name="fechas" style="width: 180px" value="{{ $f1->format('d/m/Y')}}">
                            <span class="btn input-group-text btn-secondary datepickerbutton" disabled  style="height: 33px"><i class="fas fa-calendar mt-1"></i></span>
                        </div>  --}}
                        <div class="input-group">
                            <input type="text" class="form-control pull-left" id="rango_fechas" name="fechas"  value="{{ $f1->format('d/m/Y').' - '.$f1->format('d/m/Y') }}">
                            <span class="btn input-group-text btn-secondary" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                        </div>

                    </div>
                    @if($sala==0)
                        <div class="form-group col-md-3">
                            <label for="id_edificio"><i class="fad fa-building"></i> Edificio</label>
                            <select name="id_edificio" id="id_edificio" class="form-control">
                                @foreach($edificios as $edificio)
                                    <option value="{{ $edificio->id_edificio}}" {{ isset($reserva) && $reserva->id_edificio==$edificio->id_edificio?'selected':'' }}>{{ $edificio->des_edificio }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="planta"><i class="fad fa-layer-group"></i> Planta</label>
                            <select name="id_planta" id="id_planta" class="form-control">
                                <option value="0">Cualquiera</option>
                                @foreach($plantas_usuario as $p)
                                    <option value="{{ $p->id_planta}}" {{ isset($reserva->id_tipo_puesto) && $reserva->id_planta==$p->id_planta?'selected':'' }}>{{ $p->des_planta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="id_usuario">Tipo de sala</label>
                            <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id_tipo_puesto}}" {{ isset($reserva->id_tipo_puesto) && $reserva->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }}>{{ $t->des_tipo_puesto }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="col-md-7"></div>
                        <div class="col-md-1">
                            <button class="btn btn-primary btn-lg {{ $link??'' }} btn_reservar" data-id="{{ $sala }}" data-desc=""> Reservar </button> 
                        </div>
                    @endif
                    
                    
                   
                    
                </div>
                <div class="row mt-2">
                    
                    <div class="form-group col-md-7">
                        <label for="hora-range-drg"><i class="fad fa-clock"></i> Horas</label>
                        <div id="hora-range-drg" style="margin-top: 40px"></div><span id="hora-range-val" style="display: none"></span>
                    </div>
                    
                    <div class="col-md-4 text-center mt-5">
                        <div class="d-flex">
                            <div class="form-check form-switch">
                                <input id="_dm-dbInvisibleMode" class="form-check-input" type="checkbox"  name="mca_ical"  id="mca_ical" value="S">
                            </div>
                            <label class="form-check-label h6 mt-1" for="_dm-dbInvisibleMode">Añadir a mi calendario</label>
                        </div>
                    </div>
                </div>                
            </form>
        </div>
    </div>
 </div>


 <script>



    //$('#frm_contador').on('submit',form_ajax_submit);
    comprobar_puestos();
    
    $('#id_edificio').change(function(){
        $('#id_planta').load("{{ url('/combos/plantas_salas/') }}/"+$(this).val(), function(){
            $('#id_planta').prepend("<option value='0'>Cualquiera</option>")
        });
    })
    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        width: "99.2%",
        height: "20px"
    });

    $(function(){
        $('#id_planta').load("{{ url('/combos/plantas_salas/') }}/"+$('#id_edificio').val(), function(){
            $('#id_planta').val({{ $reserva->id_planta??0 }});
            $('#id_planta option[value={{ $reserva->id_planta??0 }}]').attr('selected','selected');
            $('#id_planta').prepend("<option value='0'>Cualquiera</option>")
        });
        
    })



    $('#frm_contador').submit(function(event){
        event.preventDefault();
        let form = $(this);
        let data = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            contentType: false,
            processData: false,
            data: data,
        })
        .done(function(data) {
            console.log(data);
            if(data.error){
                mensaje_error_controlado(data);
            } else if(data.alert){
                mensaje_warning_controlado(data);
            } else{
                toast_ok(data.title,data.mensaje);
                animateCSS('#editor','fadeOut',$('#editor').html(''));

                $('#detalles_reserva').load("/salas/dia/"+data.fec_ver);
                $('#misreservas').load("/salas/mis_reservas");
                $('#div_fechas').show();
            }
            
        })
        .fail(function(err) {
            mensaje_error_respuesta(err);
        })
        .always(function() {
            fin_espere();
            console.log("Reserva complete");
            form.find('[type="submit"]').attr('disabled',false);
        });
    });

    function prueba(){
        console.log('prueba');
    }

    

    function comprobar_puestos(){
        console.log($('#fechas').val());
        $.post('{{url('/salas/comprobar')}}', {_token: '{{csrf_token()}}',fecha: $('#rango_fechas').val(),edificio:$('#id_edificio').val(),tipo: $('#tipo_vista').val(), hora_inicio: $('#hora_inicio').val(),hora_fin: $('#hora_fin').val(), tipo_puesto: $('#id_tipo_puesto').val(),id_planta:$('#id_planta').val(),tags:$('#multi-tag').val(),sala:{{ $sala }}}, function(data, textStatus, xhr) {
            $('#detalles_reserva').html(data);
        });
    }

    $('#id_edificio, #id_tipo_puesto, #id_planta, #multi-tag').change(function(){
      comprobar_puestos();
    })

    var rangepicker = new Litepicker({
        element: document.getElementById( "rango_fechas" ),
        singleMode: false,
        @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
        @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
        autoApply: true,
        format: 'DD/MM/YYYY',
        lang: "es-ES",
        maxDays: {{ config_cliente('max_dias_reserva',Auth::user()->id_cliente) }},
        tooltipText: {
            one: "day",
            other: "days"
        },
        tooltipNumber: (totalDays) => {
            return totalDays - 1;
        },
        lockDaysFilter: (day) => {
            const d = day.getDay();
            return [-1{{ session('perfil')->mca_reservar_sabados=='N'?'':',6' }}{{ session('perfil')->mca_reservar_domingos=='N'?'':',0' }}].includes(d);
        },
        setup: (rangepicker) => {
            rangepicker.on('selected', (date1, date2) => {
                comprobar_puestos();
            });
        }
    });


    $('.btn_guardar').click(function(){
        if($('#id_puesto').val()==null || $('#id_puesto').val()==""){
            toast_error('Error', 'Seleccione un puesto para reservar');
            event.preventDefault();
        }
    })

   

    var mmt = moment();
    var mmtMidnight = mmt.clone().startOf('day');
    var diffMinutes = mmt.diff(mmtMidnight, 'minutes'); //Hora actual en minutos
    diffEnd=Math.min(diffMinutes+60,1440);//Maximo de reserva por defecto 1 hora

    var aproximateHour = function (mins)
    {
    //http://greweb.me/2013/01/be-careful-with-js-numbers/
    var minutes = Math.round(mins % 60);
    if (minutes == 60 || minutes == 0)
    {
        return mins / 60;
    }
    return Math.trunc (mins / 60) + minutes / 100;
    }


    function filter_hour(value, type) {
    return (value % 60 == 0) ? 1 : 0;
    }


    var r_def = document.getElementById('hora-range-drg');
    var r_def_value = document.getElementById('hora-range-val');


    noUiSlider.create(r_def,{
        start : [diffMinutes, diffEnd],
        connect: true, 
        behaviour: 'tap-drag', 
        step: 15,
        tooltips: true,
        range : {'min': 0, 'max': 1439},
        format:  wNumb({
                decimals: 2,
            mark: ":",
                encoder: function(a){
            return aproximateHour(a);
            }
            }),
    });
    r_def.noUiSlider.on('change', function( values, handle ) {
        console.log(values);
        $('#hora_inicio').val(values[0]);
        $('#hora_fin').val(values[1]);
        comprobar_puestos();
    });

        
        values=r_def.noUiSlider.get();
        $('#hora_inicio').val(values[0]);
        $('#hora_fin').val(values[1]);

    

    $('.noUi-tooltip').css('padding','0px');
    $('.noUi-tooltip').css('font-size','11px');

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
    
 </script>
