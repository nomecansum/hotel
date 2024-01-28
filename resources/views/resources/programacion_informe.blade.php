{{-- Programacion del informe --}}
{{-- style="display: none" --}}

<link href="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet">
@if(empty($r->reportFromEmail) && checkPermissions(['Informes programados'],["R"]))
    <br>
    <div class="row m-2" id="div_programar_informe" style="display: none">
        @if(!isset($edit))<span class="ml-4 text-info font-20"><i class="fa-solid fa-lightbulb text-warning"></i> {{ __('reports.quiere_recibir_periodicamente_este_informe') }}</span>@endif
        <div class="col-md-12">
            <form action="{{url('programar_informe')}}" method="POST" class="prog-informe" id="frm_programar_informe">
                {{csrf_field()}}
                <input type="hidden" name="controller" id="controller" value="@isset($controller){{$controller}}@endisset">
                <input type="hidden" name="request_orig" id="request_orig" value="@if(isset($r)){!! json_encode($r->all()) !!} @endif">
                <input type="hidden" name="action_orig" id="action_orig">
                <input type="hidden" name="url_orig" id="url_orig" value="{{url($_SERVER['REQUEST_URI'])}}">
                <input type="hidden" name="cod_informe_programado" id="cod_informe_programado"  value=0>
                {{-- <input type="hidden" value="{{isset($inf->cod_cliente)?$inf->cod_cliente:Auth::user()->cod_cliente}}" name="cod_cliente"> --}}
                <div class="card border-primary border">
                    @if(!isset($edit))
                        <div class="card-header bg-transparent text-primary ml-3" style="font-size: 20px">
                            <i class="mdi mdi-calendar-clock"></i> {{ __('reports.programar_este_informe') }}</h2>
                            <button type="button" class="btn btn-outline-primary mt-0  p-1 pl-2 pr-2" onclick="showbody();">
                                <i class="fa-solid fa-circle-caret-down"></i>
                            </button>
                        </div>
                    @endif
                    <div class="card-body mt-0" style="display:none" id="body_programar" id="btn_det_programar">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('general.descripcion') }}</label>
                                    <input required type="text" name="des_informe_programado" class="form-control" value="{{ $inf->des_informe_programado??'' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('reports.periodo') }}</label>
                                    @php

                                    @endphp
                                    @if(isset($periodo_programado) && $periodo_programado=='FW')
                                        <select name="fechas_prog" class="form-control fechas_prog" id="fechas_prog" onchange="prog_personalizada();">
                                            <option value="1" {{ (isset($inf->val_periodo) && $inf->val_periodo==1)?'selected':'' }}>{{ __('reports.manana') }} </option>
                                            <option value="2" {{ (isset($inf->val_periodo) && $inf->val_periodo==2)?'selected':'' }}>{{ __('reports.proxima_semana') }}</option>
                                            <option value="3" {{ isset($inf->val_periodo) && $inf->val_periodo==3?'selected':'' }}>{{ __('reports.lv_prox_semana') }}</option>
                                            <option value="4" {{ isset($inf->val_periodo) && $inf->val_periodo==4?'selected':'' }}>{{ __('reports.prox_10_dias') }}</option>
                                            <option value="5" {{ isset($inf->val_periodo) && $inf->val_periodo==5?'selected':'' }}>{{ __('reports.proxima_quincena') }}</option>
                                            <option value="6" {{ isset($inf->val_periodo) && $inf->val_periodo==6?'selected':'' }}>{{ __('reports.proximo_mes') }}</option>
                                            <option value="7" {{ isset($inf->val_periodo) && $inf->val_periodo==7?'selected':'' }}>{{ __('reports.proximo_trimestre') }}</option>
                                            <option value="8" {{ isset($inf->val_periodo) && $inf->val_periodo==8?'selected':'' }}>{{ __('reports.proximo_semestre') }}</option>
                                            <option value="9" {{ isset($inf->val_periodo) && $inf->val_periodo==9?'selected':'' }}>{{ __('reports.proximo_ano') }}</option>
                                            <option value="P" {{ isset($inf->val_periodo) && $inf->val_periodo==10?'selected':'' }}{{ __('reports.personalizar_periodo') }}></option>
                                        </select>
                                    @else
                                        <select name="fechas_prog" class="form-control fechas_prog" id="fechas_prog" onchange="prog_personalizada();">
                                            <option value="0" {{ (isset($inf->val_periodo) && $inf->val_periodo==1)?'selected':'' }}>{{ __('reports.hoy') }}</option>
                                            <option value="-1" {{ (isset($inf->val_periodo) && $inf->val_periodo==1)?'selected':'' }}>{{ __('reports.ayer') }}</option>
                                            <option value="-2" {{ (isset($inf->val_periodo) && $inf->val_periodo==2)?'selected':'' }}>{{ __('reports.semana_pasada') }}</option>
                                            <option value="-3" {{ isset($inf->val_periodo) && $inf->val_periodo==3?'selected':'' }}>{{ __('reports.lv_semana_pasada') }}</option>
                                            <option value="-4" {{ isset($inf->val_periodo) && $inf->val_periodo==4?'selected':'' }}>{{ __('reports.ultimos_10_dias') }}</option>
                                            <option value="-5" {{ isset($inf->val_periodo) && $inf->val_periodo==5?'selected':'' }}>{{ __('reports.ultima_quincena') }}</option>
                                            <option value="-6" {{ isset($inf->val_periodo) && $inf->val_periodo==6?'selected':'' }}>{{ __('reports.ultimo_mes') }}</option>
                                            <option value="-7" {{ isset($inf->val_periodo) && $inf->val_periodo==7?'selected':'' }}>{{ __('reports.ultimo_trimestre') }}</option>
                                            <option value="-8" {{ isset($inf->val_periodo) && $inf->val_periodo==8?'selected':'' }}>{{ __('reports.ultimo_semestre') }}</option>
                                            <option value="-9" {{ isset($inf->val_periodo) && $inf->val_periodo==9?'selected':'' }}>{{ __('reports.ultimo_ano') }}</option>
                                            <option value="P" {{ isset($inf->val_periodo) && $inf->val_periodo==10?'selected':'' }}>{{ __('reports.personalizar_periodo') }}</option>
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-1 personalizado" style="display:none">
                                <div class="form-group">
                                    <label>{{ __('general.desde') }}</label>
                                    <input type="number" name="dias_desde" class="form-control" min="1" max="365" value="{{ $inf->dia_desde??'' }}">
                                </div>
                            </div>
                            <div class="col-md-1 personalizado" style="display:none">
                                <div class="form-group">
                                    <label>{{ __('general.hasta') }}</label>
                                    <input type="number" name="dias_hasta" class="form-control" min="1" max="365"  value="{{ $inf->dia_hasta??'' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Intervalo</label>
                                    <select class="form-control" name="val_intervalo" id="val_intervalo">
                                        <option  value="1" {{ (isset($inf->val_intervalo) && $inf->val_intervalo==1)?'selected':'' }}>{{ __('reports.diario') }}</option>
                                        <option  value="7" {{ (isset($inf->val_intervalo) && $inf->val_intervalo==7)?'selected':'' }}>{{ __('reports.semanal') }}</option>
                                        <option  value="14" {{ (isset($inf->val_intervalo) && $inf->val_intervalo==14)?'selected':'' }}>{{ __('reports.quincenal') }}</option>
                                        <option  value="1M" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='1M') || (!isset($inf->val_intervalo))?'selected':'' }}>{{ __('reports.mensual') }}</option>
                                        <option  value="2M" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='2M')?'selected':'' }}>{{ __('reports.bimensual') }}</option>
                                        <option  value="3M" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='3M')?'selected':'' }}>{{ __('reports.trimestral') }}</option>
                                        <option  value="6M" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='6M')?'selected':'' }}>{{ __('reports.semestral') }}</option>
                                        <option  value="1Y" {{ (isset($inf->val_intervalo) && $inf->val_intervalo=='1Y')?'selected':'' }}>{{ __('reports.anual') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">{{ __('reports.comenzando_en') }}</label>
                                <div class="input-group float-right" id="div_fechas">
                                    <input required type="text" name="val_fecha" id="val_fecha" class="form-control singledate" style="width: 100px" autocomplete="off"  value="{{ isset($inf->fec_inicio)?Carbon\Carbon::parse($inf->fec_inicio)->format('d/m/Y'):'' }}">
                                    <span class="btn input-group-text btn-secondary" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                                </div>
                            </div>

                            
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    
                                    <label>{{ __('reports.destinatarios') }}</label>
                                    <input type="text" class="edit_tag typeahead" data-role="tagsinput" id="list_usuarios" name="list_usuarios" placeholder="{{ __('reports.addadir_usuarios') }}" size="17" value="{{ $inf->list_usuarios??'' }}">
                                    {{-- <select required class="form-control select2 select2-filtro select2-multiple" multiple="multiple" name="list_usuarios[]" id="multi-usuarios">
                                       
                                    </select> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button id="btn_programar" class="btn btn-primary float-right mt-2"><i class="fa-solid fa-calendar-clock"></i> {{ __('reports.programar_informe') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>
    @section('scripts3')
    <script src="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('plugins/typeahead-js/main.js') }}"></script>
    <script>
        $.fn.serializeObject = function() {
            var o = {};
            var a = this.serializeArray();
            $.each(a, function() {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        };

        const validateEmail = (email) => {
        return String(email)
            .toLowerCase()
            .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
        };

        $('.btn_fecha').click(function(){
            simplepicker.open('#val_fecha');
        })

        const simplepicker = MCDatepicker.create({
            el: "#val_fecha",
            dateFormat: cal_formato_fecha,
            autoClose: true,
            closeOnBlur: true,
            firstWeekday: 1,
            customMonths: cal_meses,
            customWeekDays: cal_diassemana
        });

        $('#frm_programar_informe').submit(form_ajax_submit);

        function prog_personalizada(){
            if($('#fechas_prog').val()=='P'){
                $('.personalizado').show();
            } else {
                $('.personalizado').hide();
            }
        }

        function showbody(){
            $('#body_programar').toggle();
            animateCSS('#body_programar','fadeIn');
        }

        $('#btn_programar').click(function(){
            json=$('.ajax-filter').serializeObject();
            request_original=JSON.stringify($('.ajax-filter').serializeObject());
            request_original=request_original.replaceAll('[]','');
            jobj=JSON.parse(request_original);
            if(!Array.isArray(jobj.clientes)){
                jobj.cliente=jobj.cliente.split(',');
            }
            if(jobj.edificio && !Array.isArray(jobj.edificio)){
                jobj.edificio=jobj.edificio.split(',');
            }
            if(jobj.planta && !Array.isArray(jobj.planta)){
                jobj.planta=jobj.planta.split(',');
            }
            if(jobj.tags && !Array.isArray(jobj.tags)){
                jobj.tags=jobj.tags.split(',');
            }
            if(jobj.tipo && !Array.isArray(jobj.tipo)){
                jobj.tipo=jobj.tipo.split(',');
            }
            if(jobj.estado && !Array.isArray(jobj.estado)){
                jobj.estado=jobj.estado.split(',');
            }
            if(jobj.estado_inc && !Array.isArray(jobj.estado_inc)){
                jobj.estado_inc=jobj.estado_inc.split(',');
            }
            if(jobj.tipoinc && !Array.isArray(jobj.tipoinc)){
                jobj.tipoinc=jobj.tipoinc.split(',');
            }
            if(jobj.user && !Array.isArray(jobj.user)){
                jobj.user=jobj.user.split(',');
            }
            if(jobj.tipomark && !Array.isArray(jobj.tipomark)){
                jobj.tipomark=jobj.tipomark.split(',');
            }
            console.log(jobj);
            request_original=JSON.stringify(jobj);
            $('#request_orig').val(request_original);
        });

        $('.edit_tag').on("keypress", function(e) {
            /* ENTER PRESSED*/
            if (e.keyCode == 13) {
                /* FOCUS ELEMENT */
                e.preventDefault();
            }
        });


        //{{-- var data = {!! js_array($tags_cliente) !!};--}}
        var data="";
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
            tagClass: 'label label-primary p-3 rounded'
        });

        $('.edit_tag').on('beforeItemAdd', function(event) {
            if(validateEmail(event.item)==null){
            event.item=null;
            event.cancel=true;
            event.preventDefault=true;
            } 
        });
        $('.edit_tag').on('itemAdded', function(event) {
            $('#tags').val($(".edit_tag").tagsinput('items'));
        });

    </script>
    @endsection
    {{-- Fin programacion informe --}}
@endif
