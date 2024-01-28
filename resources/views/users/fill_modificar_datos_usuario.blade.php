<style>
    .controls {
        display: flex;
    }

    .radio {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .form-horizontal .checkbox, .form-horizontal .checkbox-inline, .form-horizontal .radio, .form-horizontal .radio-inline{
        padding-top: 0px;
    }
</style>

<form method="POST" action="{{ url('users/modificar_usuarios') }}" id="modif_users_form" name="modif_users_form" accept-charset="UTF-8" class="form-horizontal form-ajax p-10" >
    {{ csrf_field() }}
    <input type="hidden" name="id_usuario" value={{ implode(",",$r->id_usuario) }}>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Cliente</label><br>
                <select required name="id_cliente" id="id_cliente" class="select2_modal" style="width: 100%">
                    <option value=""></option>
                    @foreach ($clientes as $c)
                        <option  value="{{$c->id_cliente}}">{{$c->nom_cliente}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('id_perfil') ? 'has-error' : '' }}">
                <label for="id_perfil">Perfil</label>
                <select class="select2_modal notsearch"  id="cod_nivel" name="cod_nivel">
                    <option value=""></option>
                    @foreach ($Perfiles as $Perfile)
                        <option value="{{ $Perfile->cod_nivel }}">
                            {{ $Perfile->des_nivel_acceso }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('cod_nivel', '<p class="help-block">:message</p>') !!}
            </div>
        </div>

        {{-- //Vamos a añadir un combo con las zonas horarias --}}
        @php
            $regions = array(
                'Africa' => DateTimeZone::AFRICA,
                'America' => DateTimeZone::AMERICA,
                'Antarctica' => DateTimeZone::ANTARCTICA,
                'Asia' => DateTimeZone::ASIA,
                'Atlantic' => DateTimeZone::ATLANTIC,
                'Europe' => DateTimeZone::EUROPE,
                'Indian' => DateTimeZone::INDIAN,
                'Pacific' => DateTimeZone::PACIFIC
            );
            function tz_list() {
                $zones_array = array();
                $timestamp = time();
                $dummy_datetime_object = new DateTime();
                foreach(timezone_identifiers_list() as $key => $zone) {
                    date_default_timezone_set($zone);
                    $zones_array[$key]['zone'] = $zone;
                    $zones_array[$key]['diff_from_GMT'] = 'GMT' . date('P', $timestamp);
                    $tz = new DateTimeZone($zone);
                    $zones_array[$key]['offset'] = $tz->getOffset($dummy_datetime_object);
                }
                return $zones_array;
                }

        @endphp
        <div class="col-md-4">
            <div class="form-group">
                <label>Zona horaria</label>
                <select name="val_timezone" class="select2_modal" style="width: 100%; margin-top: 25px; height: 38px">
                    <option value="" selected></option>
                    @foreach($regions as $region => $list)
                    <optgroup label="{{ $region }}">
                        @foreach(tz_list() as $tz)
                        @if (stripos($tz['zone'],$region)===0)
                                <option  value="{{ $tz['zone'] }}">{{ $tz['zone'] }} {{ $tz['diff_from_GMT'] }}</option>
                            @endif
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Edificio de referencia</label><br>
                <select name="id_edificio" id="id_edificio" class="select2_modal notsearch">
                    <option value=""></option>
                    @foreach ($edificios as $t)
                        <option  value="{{$t->id_edificio}}">{{$t->des_edificio}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label>Departamento</label>
                    <select   name="id_departamento" class="select2_modal tab_general" style="width: 100%" id="id_departamento">
                        <option value=""> </option>
                        @php $departamentos=lista_departamentos("cliente",Auth::user()->id_cliente); @endphp
                        @isset($departamentos)
                            @foreach($departamentos as $departamento)
                                <option style="padding-left: 20px"  value="{{ $departamento->cod_departamento}}"  >
                                    @for($i = 1; $i <= $departamento->num_nivel; $i++) &nbsp;&nbsp;&nbsp; @endfor{{ $departamento->nom_departamento}}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                <br>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Asignacion automatica de puestos (Copiar de)</label>
                    <select   name="id_usuario_asig_auto" class="select2_modal tab_general" style="width: 100%" id="id_usuario_asig_auto">
                        <option value=""> </option>
                            @foreach($usuarios as $usuario)
                                <option style="padding-left: 20px"  value="{{ $usuario->id}}"  >
                                {{ $usuario->name}}
                                </option>
                            @endforeach
                    </select>
                <br>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="form-group">
                <label>Supervisor</label>
                    <select   name="id_usuario_supervisor" class="select2_modal tab_general" style="width: 100%" id="id_usuario_supervisor">
                        <option value=""> </option>
                        @foreach($usuarios as $usuario)
                            <option style="padding-left: 20px"  value="{{ $usuario->id}}"  >
                            {{ $usuario->name}}
                            </option>
                        @endforeach
                    </select>
                <br>
            </div>
        </div>
        <div class="col-md-12 b-all rounded mb-2 pb-2 mt-3" >
            <div class="form-group"  style="overflow: hidden">
                <label class="text-nowrap ml-2">Colectivos
                    <div class="controls ">
                        <div >
                            <input id="demo-form-radio-c1" class="magic-radio" type="radio" name="colectivo_accion" value="add" checked="">
                            <label class="radio" for="demo-form-radio-c1">Añadir</label>
                        </div>
                        <div >
                            <input id="demo-form-radio-c2" class="magic-radio" type="radio"  name="colectivo_accion" value="del">
                            <label for="demo-form-radio-c2" class="radio">Quitar</label>
                        </div>
                        <div >
                            <input id="demo-form-radio-c3" class="magic-radio" type="radio"  name="colectivo_accion" value="set">
                            <label for="demo-form-radio-c3" class="radio">Establecer</label>
                        </div>
                    </div>
                </label><br>
                <select  name="val_colectivo[]" multiple="" class="form-control  select2_modal" style="width: 100%" id="val_colectivo">
                    @foreach ($colectivos_cliente as $col)
                        <option  value="{{$col->cod_colectivo}}">{{$col->des_colectivo}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12 b-all rounded mr-4 mb-2">
            <label>Turno de asistencia
                <div class="controls">
                    <div >
                        <input id="demo-form-radio-3" class="magic-radio" type="radio" name="turno_accion" value="add" checked="">
                        <label class="radio" for="demo-form-radio-3">Añadir</label>
                    </div>
                    <div >
                        <input id="demo-form-radio-4" class="magic-radio" type="radio"  name="turno_accion" value="del">
                        <label for="demo-form-radio-4" class="radio">Quitar</label>
                    </div>
                    <div >
                        <input id="demo-form-radio-4e" class="magic-radio" type="radio"  name="turno_accion" value="set">
                        <label for="demo-form-radio-4e" class="radio">Establecer</label>
                    </div>
                </div>
            </label><br>
            <div class="row">
                @foreach($turnos as $t)
                <div class="form-group col-md-3">
                    <div class="form-check pt-2">
                        <input   name="turno[]" id="turno{{$t->id_turno}}" value="{{$t->id_turno}}" class="form-check-input chkdia" type="checkbox">
                        <label class="form-check-label" for="turno{{$t->id_turno}}"><b>{{$t->des_turno}} <i class="fa-solid fa-square" style="color: {{ $t->val_color }}"></i></b></label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-12 b-all rounded mr-4 mb-2">
            <label>Tipos de puesto que puede reservar
                <div class="controls ">
                    <div >
                        <input id="demo-form-radio-5" class="magic-radio" type="radio" name="tipo_puesto_accion" value="add" checked="">
                        <label class="radio" for="demo-form-radio-5">Añadir</label>
                    </div>
                    <div >
                        <input id="demo-form-radio-6" class="magic-radio" type="radio"  name="tipo_puesto_accion" value="del">
                        <label for="demo-form-radio-6" class="radio">Quitar</label>
                    </div>
                    <div >
                        <input id="demo-form-radio-6e" class="magic-radio" type="radio"  name="tipo_puesto_accion" value="set">
                        <label for="demo-form-radio-6e" class="radio">Establecer</label>
                    </div>
                </div>
            </label><br>
            <div class="row">
                @foreach($tipos_puestos as $t)
                    <div class="form-group col-md-3">
                        <div class="form-check pt-2">
                            <input  name="tipos_puesto_admitidos[]" id="tipo_puesto{{$t->id_tipo_puesto}}" value="{{$t->id_tipo_puesto}}" class="form-check-input chkdia" type="checkbox">
                            <label class="form-check-label" for="tipo_puesto{{$t->id_tipo_puesto}}" title="{{ $t->des_tipo_puesto }}"> {{$t->abreviatura!=''?$t->abreviatura:$t->des_tipo_puesto}} </label>
                        </div>
                
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-12 b-all rounded mb-2 pb-2" >
            <div class="form-group"  style="overflow: hidden">
                <label class="text-nowrap ml-2">Plantas en las que puede reservar
                    <div class="controls">
                        <div >
                            <input id="demo-form-radio-7" class="magic-radio" type="radio" name="planta_accion" value="add" checked="">
                            <label class="radio" for="demo-form-radio-7">Añadir</label>
                        </div>
                        <div >
                            <input id="demo-form-radio-8" class="magic-radio" type="radio"  name="planta_accion" value="del">
                            <label for="demo-form-radio-8" class="radio">Quitar</label>
                        </div>
                        <div >
                            <input id="demo-form-radio-8e" class="magic-radio" type="radio"  name="planta_accion" value="set">
                            <label for="demo-form-radio-8e" class="radio">Establecer</label>
                        </div>
                    </div>
                </label><br>
                <select  name="plantas[]" multiple="" class="form-control  select2_modal" style="width: 100%" id="plantas">
                    @foreach ($plantas as $pl)
                        <option  value="{{$pl->id_planta}}">{{$pl->des_planta}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12 b-all rounded mb-2 pb-2"  >
            <div class="form-group"  style="overflow: hidden">
                <label class="text-nowrap ml-2">Zonas en las que puede reservar
                    <div class="controls">
                        <div >
                            <input id="demo-form-radio-7" class="magic-radio" type="radio" name="zona_accion" value="add" checked="">
                            <label class="radio" for="demo-form-radio-7">Añadir</label>
                        </div>
                        <div >
                            <input id="demo-form-radio-8" class="magic-radio" type="radio"  name="zona_accion" value="del">
                            <label for="demo-form-radio-8" class="radio">Quitar</label>
                        </div>
                        <div >
                            <input id="demo-form-radio-8e" class="magic-radio" type="radio"  name="zona_accion" value="set">
                            <label for="demo-form-radio-8e" class="radio">Establecer</label>
                        </div>
                    </div>
                </label><br>
                <select  name="zonas[]" multiple="" class="form-control  select2_modal" style="width: 100%" id="zonas">
                    @foreach($plantas as $pl)
                        @php
                            $zonas_planta=$zonas->where('id_planta',$pl->id_planta);
                        @endphp
                        <optgroup label="{{ $pl->des_planta }}">
                                @foreach($zonas_planta as $z)
                                    <option  value="{{$z->num_zona}}">[{{ $pl->des_planta }}] {{$z->des_zona}}</option>
                                @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
</form>
<script>
    $(".select2_modal").select2({
        placeholder: "No modificar",
        allowClear: true,
        width: "99.2%",
        dropdownParent: $('#modificar-usuario .modal-content')
    });
    $('.form-ajax').submit(form_ajax_submit);
</script>