<div class="card editor mb-5">
    <div class="card-header toolbar">
        <div class="toolbar-start">
            <h5 class="m-0">Modificar festivo</h5>
        </div>
        <div class="toolbar-end">
            <button type="button" class="btn-close btn-close-card">
                <span class="visually-hidden">Close the card</span>
            </button>
        </div>
    </div>
    <div class="card-body collapse show">
        <form  @if($fes->cod_festivo!=0) action="{{url('festives/update',$fes->cod_festivo)}}" @else action="{{url('festives/save')}}"  @endif method="POST" class="form-ajax formfestivo">
            <div class="row">
                <input type="hidden" name="id" value="{{$fes->cod_festivo}}">

                {{csrf_field()}}

                <div class="form-group @if(isAdmin())col-md-5 @else col-md-6 @endif">
                    <label for="">{{trans('strings._employees.festives.name')}}</label>
                    <input required type="text" name="des_festivo" class="form-control" required value="{{isset($fes) ? $fes->des_festivo : ''}}">
                </div>
                @if(isAdmin())
                <div class="form-group col-md-1 p-t-20 mt-1">
                    <div class="form-check pt-2">
                        <input name="mca_fijo"  id="mca_fijo" value="S" {{isset($fes) ? ($fes->mca_fijo == 'S' ? 'checked' : '') : ''}} class="form-check-input" type="checkbox">
                        <label class="form-check-label"  for="mca_fijo">Fijo</label>
                    </div>
                </div>
                @endif
                <div class="form-group col-md-2">
                    <label for="">{{trans('strings._employees.festives.date')}}</label>
                    <div class="input-group float-right" id="div_fechas">
                        <input type="text" class="form-control pull-left singledate" id="val_fecha" name="val_fecha"  required value="{{isset($fes) ? \Carbon\Carbon::parse($fes->val_fecha)->format('d/m/Y') : ''}}">
                        <span class="btn input-group-text btn-secondary btn_fecha"   style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="">{{trans('strings._centers.business')}}</label>
                    <select required name="cod_cliente" class="form-control" id="cod_cliente">
                        {{-- <option value="" selected></option> --}}
                        @foreach ($clientes as $cl)
                            <option  {{ (isset($fes) && $fes->id_cliente == $cl->id_cliente) || ($fes->cod_festivo==0 && $cl->id_cliente==session('CL')['id_cliente']) ? 'selected' : ''}}  value="{{$cl->id_cliente}}">{{$cl->nom_cliente}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 p-t-30 mt-1" >
                    <div class="form-check pt-2">
                        <input  name="mca_nacional"  id="mca_nacional" value="S" {{isset($fes) ? ($fes->mca_nacional == 'S' ? 'checked' : '') : ''}} class="form-check-input" type="checkbox">
                        <label class="form-check-label"  for="mca_nacional">Festivo nacional</label>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-4" id="div_pais" style="{{ isset($fes)&&$fes->mca_nacional=='S'?'':'display:none' }};margin-right: 20px;">
                    <div class="form-group" >
                        <label for="">{{trans('strings.pais')}}</label><br>
                        <select name="cod_pais" id="cod_pais" class="select2 form-control" style="width:100%" >
                            <option value=""></option>
                            @foreach ($paises as $p)
                                <option {{  (isset($fes)&&$fes->cod_pais == $p->id_pais ? 'selected' : '') }} {{ !isset($fes)&&$p->id_pais==$fes->id_pais?'selected':'' }} value="{{$p->id_pais}}">{{$p->nom_pais}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-12" id="div_region" style="{{ isset($fes)&&$fes->mca_nacional=='N'?'':'display:none' }}">
                    <label for="">Region</label>
                    <select name="region[]" id="cod_region" style="width: 100%" class="form-control select2 select2-multiple" multiple="multiple">
                        @foreach ($regiones as $rg)
                            <option  {{ (in_array($rg->cod_region,$regiones_festivo) ? 'selected' : '') }}  value="{{$rg->cod_region}}">{{$rg->nom_region}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-12" id="div_provincia" style="{{ isset($fes)&&$fes->mca_nacional=='N'?'':'display:none' }}">
                    <label for="">Provincia</label>
                    <select name="provincia[]" id="cod_provincia" style="width: 100%" class="form-control select2 select2-multiple" multiple="multiple">
                        @foreach ($provincias as $prov)
                            <option  {{ (in_array($prov->id_prov,$prov_festivo) ? 'selected' : '') }}  value="{{$prov->id_prov}}">{{$prov->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12" id="div_centro" style="{{ isset($fes)&&$fes->mca_nacional=='N'?'':'display:none' }}">
                    <label for="">{{trans('strings._employees.festives.center')}}</label><br>
                    <select name="cod_centro[]" class="form-control select2 select2-multiple" id="cod_centro" multiple="multiple" style="width: 100%">
                        {{-- <option value="0">TODOS LOS CENTROS (Nacional)</option> --}}
                        @foreach ($centros as $c)
                            <option {{ (in_array($c->id_edificio,$centros_festivo) ? 'selected' : '') }} value="{{$c->id_edificio}}">{{$c->des_edificio}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="md-12 text-end" style="margin-top:32px">
                    @if(checkPermissions(['Festivos'],["C"]))<button type="submit" class="btn btn-primary btnguardar">{{trans('strings.submit')}}</button>@endif
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('.btnguardar').click(function(event){
        if(!$('#mca_nacional').is(':checked') && $('#cod_centro').val()=='' && $('#cod_region').val()=='' && $('#cod_provincia').val()==''){
            toast_error('Error','Debe seleccionar alguna de las opciones: Provincia, Region, Centro');
            event.preventDefault();
            return;
        }
       if($('#mca_nacional').is(':checked') &&  $('#cod_pais').val()==''){
            toast_error('Error','Debe seleccionar un país');
            event.preventDefault();
            return;
       }
    });

    $('#mca_nacional').change(function(){
        if($('#mca_nacional').is(":checked")){
            $('#div_pais').show();
            $('#div_centro').hide();
            $('#div_provincia').hide();
            $('#div_region').hide();
        } else {
            $('#div_pais').hide();
            $('#div_centro').show();
            $('#div_provincia').show();
            $('#div_region').show();
        }
    })

    $('#cod_cliente').change(function(){
        $('#cod_centro').load('/combos/edificios/'+$(this).val(), function(data){
            $('#cod_centro').html();
            $('#cod_centro').html(data);
        });

        $('#cod_provincia').load('/combos/provincias/'+$(this).val(), function(data){
            $('#cod_provincia').html();
            $('#cod_provincia').html(data);
        });

        $('#cod_region').load('/combos/regiones/'+$(this).val(), function(data){
            $('#cod_region').html();
            $('#cod_region').html(data);
        });

        $('#cod_pais').load('/combos/paises/'+$(this).val(), function(data){
            $('#cod_pais').html();
            $('#cod_pais').html(data);
        });

    })

    $('.form-ajax').submit(form_ajax_submit);

    $('.btn_fecha').click(function(){
        picker.open('#val_fecha');
    })

    picker = MCDatepicker.create({
        el: "#val_fecha",
        dateFormat: cal_formato_fecha,
        autoClose: true,
        closeOnBlur: true,
        firstWeekday: 1,
        disableWeekDays: cal_dias_deshabilitados,
        customMonths: cal_meses,
        customWeekDays: cal_diassemana
    });

    $(".select2").select2();

    $(function(){
        $('#cod_cliente').trigger('change', function(){
            $('#cod_region').val({!!  js_array($regiones_festivo)  !!}).trigger('change');
            $('#cod_provincia').val({!! js_array($prov_festivo) !!}).trigger('change');
            $('#cod_centro').val({!! js_array($centros_festivo) !!}).trigger('change');
        });
        
    })

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
</script>
