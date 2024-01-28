@php
    $dias=['LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'];
    try{
        $dias_semana=json_decode($dato->dias_semana);
    } catch(\Throwable $e){
        $dias_semana=null;
    }
    
@endphp
<div class="card editor mb-5">
    <div class="card-header toolbar">
        <div class="toolbar-start">
            <h5 class="m-0">Modificar turno</h5>
        </div>
        <div class="toolbar-end">
            <button type="button" class="btn-close btn-close-card">
                <span class="visually-hidden">Close the card</span>
            </button>
        </div>
    </div>
    <div class="card-body collapse show">

        <form  action="{{url('turnos/save')}}" method="POST" class="form-ajax formturno">
            <div class="row">
                <input type="hidden" name="id" value="{{$dato->id_turno??0}}">
                {{csrf_field()}}
                <div class="form-group col-md-4">
                    <label for="">Nombre</label>
                    <input required type="text" name="des_turno" class="form-control" required value="{{isset($dato) ? $dato->des_turno : ''}}">
                </div>
                <div class="form-group col-md-1">
                    <label for="val_color">Color</label><br>
                    <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($tipo->val_color)?$tipo->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                </div>
                <div class="form-group col-md-4">
                    <label for="">Fecha de efectividad (sin año)</label>
                    <div class="input-group">
                        <input type="text" class="form-control pull-left" id="fechas" name="fechas" value="{{ Carbon\Carbon::parse($dato->fec_inicio)->format('d/m/Y').' - '.Carbon\Carbon::parse($dato->fec_fin)->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-secondary btn_fechas"  style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </div>

                <div class="form-group col-md-3">
                    <label for="">Cliente</label>
                    <select required name="cod_cliente" class="form-control" id="cod_cliente">
                        {{-- <option value="" selected></option> --}}
                        @foreach ($clientes as $cl)
                            <option  {{ (isset($dato) && $dato->id_cliente == $cl->id_cliente) ||($id==0 && $key==session('CL')['id_cliente']) ? 'selected' : ''}}  value="{{$cl->id_cliente}}">{{$cl->nom_cliente}}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group col-md-2 p-t-30 mt-1" >
                    <input type="checkbox" class="form-control  magic-checkbox" name="mca_nacional"  id="mca_nacional" value="S" {{isset($fes) ? ($fes->mca_nacional == 'S' ? 'checked' : '') : ''}}> 
                    <label class="custom-control-label"   for="mca_nacional">Festivo nacional</label>
                </div> --}}

            </div>
            <div class="row d-flex flex-wrap mt-4">
                @for ($i = 1; $i < 8; $i++)
                    <div class="form-group rounded b-all text-center p-10 " style="width: 14%; margin-right: 2px" >
                        <div class="form-check pt-2">
                            <input  name="dia[]" id="dia{{$i}}" value="{{$i}}" {{ isset($dias_semana)&&in_array($i,$dias_semana->dia)?'checked':'' }} class="form-check-input chkdia" type="checkbox">
                            <label class="form-check-label"  for="dia{{$i}}"><b>{{$dias[$i-1]}}</b></label><br>
                        </div>
                        <div class="form-group">
                            <label for="">Inicio</label>
                            <input type="time" name="hora_inicio[]" id="hora_inicio{{ $i }}" {{ isset($dias_semana)&&in_array($i,$dias_semana->dia)?'':'disabled' }} class="form-control control{{ $i }}" value="{{ isset($dias_semana)&&in_array($i,$dias_semana->dia)?$dias_semana->hora_inicio[array_search($i,$dias_semana->dia)]:'' }}">
                        </div>
                        <div class="form-group">
                            <label for="">Fin</label>
                            <input type="time" name="hora_fin[]" id="hora_fin{{ $i }}" {{ isset($dias_semana)&&in_array($i,$dias_semana->dia)?'':'disabled' }} class="form-control control{{ $i }}" value="{{ isset($dias_semana)&&in_array($i,$dias_semana->dia)?$dias_semana->hora_fin[array_search($i,$dias_semana->dia)]:'' }}">
                        </div>
                        <div class="form-group">
                            <label for="">Semanas</label>
                            <select name="mod_semana[]" id="mod_semana" id="mod_semana{{ $i }}" {{isset($dias_semana)&& in_array($i,$dias_semana->dia)?'':'disabled' }} class="form-control control{{ $i }}">
                                <option value="-1" {{ isset($dias_semana)&&in_array($i,$dias_semana->dia)&&$dias_semana->mod_semana[array_search($i,$dias_semana->dia)]==-1?'selected':'' }}>Todas</option>
                                <option value="0" {{ isset($dias_semana)&&in_array($i,$dias_semana->dia)&&$dias_semana->mod_semana[array_search($i,$dias_semana->dia)]==0?'selected':'' }}>Pares</option>
                                <option value="1" {{ isset($dias_semana)&&in_array($i,$dias_semana->dia)&&$dias_semana->mod_semana[array_search($i,$dias_semana->dia)]==1?'selected':'' }}>Impares</option>
                            </select>

                        </div>
                    </div>
                    
                @endfor
            </div>
            
            <div class="row">
                <div class="col-md-12 text-end" style="margin-top:32px">
                    @if(checkPermissions(['Festivos'],["C"]))<button type="submit" class="btn btn-primary btnguardar">{{trans('strings.submit')}}</button>@endif
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    
    $('.chkdia').click(function(){
        if($(this).is(':checked')){
            $('.control'+$(this).val()).prop( "disabled", false);
            $('.control'+$(this).val()).attr('required', true);
            if($('#hora_inicio'+$(this).val()).val()==""){
                $('#hora_inicio'+$(this).val()).val('00:00');
            }
            if($('#hora_fin'+$(this).val()).val()==""){
                $('#hora_fin'+$(this).val()).val('23:59');
            }
        } else {
            $('.control'+$(this).val()).prop( "disabled", true );
            $('.control'+$(this).val()).attr('required', false);
        }

    })

    $('.form-ajax').submit(form_ajax_submit);

    //Date range picker
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

    $('.btn_fechas').click(function(){
        rangepicker.show();
    })
    
    $(".select2").select2();

    

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

</script>
