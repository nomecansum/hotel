
<link href="{{ asset('/plugins/fullcalendar/lib/main.css') }}" rel="stylesheet">
<style>
    .floating-like-gmail {
        position: absolute;
        bottom: 8px;
        right: 10px;
        max-width: 100vw;
        width: 600px;
        right: 10px;
        text-align: right;
        opacity: 0;
        transition: 500ms;
    }
    .modal {
        text-align: center !important;
    }
    .hover-this:hover .floating-like-gmail,.floating-like-gmail:hover {
        opacity: 1;
    }
    .preview {
        width: 100%;
        height: 200px;
        background-position: center;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 6px;
        overflow: hidden;
    }
    .img-preview {
        height: 100px;
        /*width: 100px;*/
        background-position: center;
        background-size: cover;
    }
    #img-inputs input {
        display: none;
        height: 0;
    }
    .sidebar-footer a {
        width: 50%;
    }
    .user-profile .profile-img::before {
        left: 0;
        right: 0;
    }

    .ui-sortable-handle{
        padding: 10px 10px 10px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        cursor: move;
        min-height: 40px;
        min-width: 250px;
        font-size: 18px;
        list-style-type: none;
        width: 400px;
    }

    .ui-draggable-handle{
        padding: 10px 10px 10px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        cursor: move;
        min-height: 40px;
        min-width: 120px;
        list-style-type: none;
        width: 400px;
    }

    .fc-event-title{
        font-size: 10px;
        font-weight: normal;
    }

    .bottom-right {
        position: relative;
        bottom: 0;
        right: 0;
        cursor: pointer;
    }

    .detalle{
        font-weight: 600;
    }

    .ui-sortable{
        width: 414px;
    }

    .chk_naranja{
        background-color:#fd7e14;
        border-color:#fd7e14;
    }
    .chk_gris{
        background-color:#aaa;
        border-color:#aaa;
    }

    
</style>





    <div class="card editor mb-5">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</h5>
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
            <form method="POST" action="{{ url('users/update',$users->id) }}" id="edit_users_form" name="edit_users_form" accept-charset="UTF-8" class="form-horizontal mt-4 form-ajax" enctype="multipart/form-data">
            {{ csrf_field() }}
                <div class="tab-base">		
                    <!--Nav tabs-->
                    <ul class="nav nav-tabs"  role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-1" type="button" role="tab" aria-controls="generales" aria-selected="true">Datos generales</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-2"  type="button" role="tab" aria-controls="config" aria-selected="false">Configuracion</button>
                        </li>
                        @if (isset($users))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"  data-bs-target="#demo-stk-lft-tab-6" type="button" role="tab" aria-controls="seguridad" aria-selected="false">Seguridad</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-4"  type="button" role="tab" aria-controls="puestos" aria-selected="false">Espacios</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"  data-bs-target="#demo-stk-lft-tab-5"  id="actividad" type="button" role="tab" aria-controls="actividad" aria-selected="false">Actividad</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"  data-bs-target="#demo-stk-lft-tab-7"  id="permisos" type="button" role="tab" aria-controls="permisos" aria-selected="false">Permisos</button>
                            </li>
                        @endif
                    </ul>
                
                    <!--Tabs Content-->
                    <div class="tab-content">
                        <div id="demo-stk-lft-tab-1" class="tab-pane fade active show" role="tabpanel" aria-labelledby="generales-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="form-group col-md-12 {{ $errors->has('name') ? 'has-error' : '' }}">
                                                    <label for="name" class="control-label">Nombre</label>
                                                    <input class="form-control" name="name" type="text" id="name" value="{{ old('name', optional($users)->name) }}" minlength="1" maxlength="255" required="true" placeholder="Enter name here...">
                                                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="form-group col-md-6 {{ $errors->has('email') ? 'has-error' : '' }}">
                                                    <label for="email" class="control-label" autocomplete="off">e-mail</label>
                                                    <input class="form-control" name="email" type="text" id="email" value="{{ old('email', optional($users)->email) }}" minlength="1" maxlength="255" required="true" placeholder="Enter email here...">
                                                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                                                </div>
                                                <div class="form-group col-md-6 {{ $errors->has('password') ? 'has-error' : '' }}">
                                                    <label for="password" class="control-label">Password</label>
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" name="password" type="password" id="password" autocomplete="off"  minlength="4" maxlength="255" placeholder="Enter password here...">
                                                        {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                                                        <button class="btn btn-secondary" type="button"  id="btn_generar_password">Generar</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-n2">
                                                <div class="col-md-10">
                                                    <label class="control-label">Token acceso</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" name="token_acceso" readonly=""  id="token_1uso"  class="form-control" value="{{isset($users) ? $users->token_acceso : ''}}">
                                                        <div class="input-group-btn">
                                                            <button class="btn btn-secondary" type="button"  id="btn_generar_token">Generar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label">Expira (dias)</label>
                                                        <input class="form-control" name="token_expires" type="number" id="token_expires" value="{{ old('token_expires', optional($users)->token_expires) }}" min="1" max="1000000" >
                                                        {!! $errors->first('token_expires', '<p class="help-block">:message</p>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center b-all rounded">
                                            <div class="row font-bold" style="padding-left: 15px">
                                                Imagen<br>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group  {{ $errors->has('img_usuario') ? 'has-error' : '' }}">
                                                    <label for="img_usuario" class="preview preview1">
                                                        <img src="{{ isset($users) ? Storage::disk(config('app.img_disk'))->url('img/users/'.$users->img_usuario) : ''}}" style="margin: auto; display: block; width: 156px; heigth:180px" alt="" id="img_preview" class="img-fluid">
                                                    </label>
                                                    <div class="custom-file">
                                                        <input type="file" accept=".jpg,.png,.gif,.webp.jiff" class="form-control  custom-file-input" name="img_usuario" id="img_usuario" lang="es" value="{{ isset($users) ? $users->img_usuario : ''}}">
                                                        <label class="custom-file-label" for="img_usuario"></label>
                                                    </div>
                                                </div>
                                                    {!! $errors->first('img_usuario', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div id="demo-stk-lft-tab-2"  class="tab-pane fade" role="tabpanel" aria-labelledby="config-tab">
                            <div class="card">
                                <div class="card-header">
                                    <p class="text-main text-semibold">Configuracion</p>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Cliente</label><br>
                                                <select required name="id_cliente" id="id_cliente" class="select2" style="width: 100%">
                                                    <option value=""></option>
                                                    @foreach (\DB::table('clientes')->where(function($q){
                                                        if (!fullAccess()){
                                                                $q->WhereIn('id_cliente',clientes());
                                                            }
                                                        })->wherenull('fec_borrado')->get() as $c)
                                                        <option {{isset($users) && $users->id_cliente == $c->id_cliente ? 'selected' : ''}} value="{{$c->id_cliente}}">{{$c->nom_cliente}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group {{ $errors->has('id_perfil') ? 'has-error' : '' }}">
                                                <label for="id_perfil">Perfil</label>
                                                <select class="select2 notsearch"  id="cod_nivel" name="cod_nivel">
                                                    @foreach ($Perfiles as $Perfile)
                                                        <option value="{{ $Perfile->cod_nivel }}" {{ old('cod_nivel', optional($users)->cod_nivel) == $Perfile->cod_nivel ? 'selected' : '' }}>
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
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Zona horaria</label>
                                                <select name="val_timezone" class="select2" style="width: 100%; margin-top: 25px; height: 38px">
                                                    <option value="" selected></option>
                                                    @foreach($regions as $region => $list)
                                                    <optgroup label="{{ $region }}">
                                                        @foreach(tz_list() as $tz)
                                                        @if (stripos($tz['zone'],$region)===0)
                                                                <option {{ isset($users) && $tz['zone'] == $users->val_timezone  ? 'selected' : ''}} value="{{ $tz['zone'] }}">{{ $tz['zone'] }} {{ $tz['diff_from_GMT'] }}</option>
                                                            @endif
                                                        @endforeach
                                                    </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Edificio de referencia</label><br>
                                                <select name="id_edificio" id="id_edificio" class="select2 notsearch">
                                                    <option value=""></option>
                                                    @foreach ($edificios as $t)
                                                        <option {{isset($users) && $users->id_edificio == $t->id_edificio ? 'selected' : ''}} value="{{$t->id_edificio}}">{{$t->des_edificio}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
											<div class="form-group">
												<label>Departamento</label>
													<select   name="id_departamento" class="select2 tab_general" style="width: 100%" id="id_departamento">
														<option value="0"> </option>
														@php $departamentos=lista_departamentos("cliente",$users->id_cliente); @endphp
														@isset($departamentos)
															@foreach($departamentos as $departamento)
                                                                <option style="padding-left: 20px"  value="{{ $departamento->cod_departamento}}"  {{ old('id_departamento', optional($users)->id_departamento) == $departamento->cod_departamento ? 'selected' : '' }}>
                                                                    @for($i = 1; $i <= $departamento->num_nivel; $i++) &nbsp;&nbsp;&nbsp; @endfor{{ $departamento->nom_departamento}}
                                                                </option>
															@endforeach
														@endisset
													</select>
												<br>
											</div>
										</div>
                                        <div class="col-md-3">
											<div class="form-group">
												<label for="name" class="control-label">ID Externo</label>
                                                <input class="form-control" name="id_usuario_externo" type="text" id="id_usuario_externo" value="{{ old('id_usuario_externo', optional($users)->id_usuario_externo) }}" autocomplete="off" minlength="1" maxlength="255"  placeholder="Enter id_usuario_externo here...">
                                                {!! $errors->first('id_usuario_externo', '<p class="help-block">:message</p>') !!}
											</div>
										</div>
                                    </div>
                                    <div class="row mt-2">
                                        <label class="text-nowrap">Notificacion</label><br>
                                        <div class="col-md-3  mt-1">
                                            <div class="form-check pt-3">
                                                <input  name="mca_notif_push"  id="mca_notif_push" value="S" {{ isset($users->mca_notif_push)&&$users->mca_notif_push=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                                <label class="form-check-label" for="mca_notif_push">Web PUSH</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3  mt-1">
                                            <div class="form-check pt-3">
                                                <input  name="mca_notif_email"  id="mca_notif_email" value="S" {{ isset($users->mca_notif_email)&&$users->mca_notif_email=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                                <label class="form-check-label" for="mca_notif_email">e-mail</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3  mt-1">
                                            <div class="form-check pt-3">
                                                <input  name="mca_reserva_multiple"  id="mca_reserva_multiple" value="S" {{ isset($users->mca_reserva_multiple)&&$users->mca_reserva_multiple=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                                <label class="form-check-label" for="mca_reserva_multiple">reserva multiple</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 {{ $errors->has('id_onesignal') ? 'has-error' : '' }}">
                                            <label for="email" class="control-label">PlayerID</label>
                                            <input class="form-control" name="id_onesignal" disabled type="text" id="id_onesignal" value="{{ old('id_onesignal', optional($users)->id_onesignal) }}" minlength="1" maxlength="255" placeholder="Enter PlayerID here...">
                                            {!! $errors->first('id_onesignal', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12" >
                                            <div class="form-group"  style="overflow: hidden">
                                                <label class="text-nowrap">{{trans('strings.collectives')}}</label><br>
                                                <select  name="val_colectivo[]" multiple="" class="form-control  select2" style="width: 100%" id="val_colectivo">
                                                    @foreach ($colectivos_cliente as $col)
                                                        <option {{ in_array($col->cod_colectivo,$colectivos_user)===true ? 'selected' : ''}} value="{{$col->cod_colectivo}}">{{$col->des_colectivo}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row rounded b-all mt-4 pb-3">
                                        <div class="col-md-4" >
                                            <div class="form-group ">
                                                <label><b>Supervisor</b></label><br>
                                                <select name="id_usuario_supervisor" id="id_usuario_supervisor" class="select2 form-control" style="width:350px">
                                                    <option value=""></option>
                                                    @foreach ($supervisores as $c)
                                                        <option {{isset($users) && $users->id_usuario_supervisor == $c->id ? 'selected' : ''}} value="{{$c->id}}">{{$c->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @if(isset($users) && isSupervisor($users->id))
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label>Usuarios a los que supervisa</label><br>
                                                    <select name="lista_id[]" id="lista_id" class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple">
                                                        <option value=""></option>
                                                        @foreach ($usuarios_supervisables as $c)
                                                            <option {{ in_array($c->id,$usuarios_supervisados) ? 'selected' : ''}} value="{{$c->id}}">{{$c->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row rounded b-all mt-4 pb-3">
                                        <div class="col-md-2" >
                                            <div class="form-group ">
                                                <label><b>Contrata</b></label><br>
                                                <select name="id_contrata" id="id_contrata" class="select2 form-control" style="width:350px">
                                                    <option value=""></option>
                                                    @foreach ($contratas as $c)
                                                        <option {{isset($users) && $users->id_contrata == $c->id_contrata ? 'selected' : ''}} value="{{$c->id_contrata}}">{{$c->des_contrata}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2  mt-2 d_contrata"  style="display:none">
                                            <div class="form-check pt-3">
                                                <input  name="mca_compartido"  id="mca_compartido" value="S" {{ isset($users->mca_compartido)&&$users->mca_compartido=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                                <label class="form-check-label" for="mca_compartido">Usuario compartido</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d_contrata cmb_gen" style="{{ isset($users->mca_compartido)&&$users->mca_compartido=='S'?'':'display:none' }}" >
                                            <div class="form-group">
                                                <label>Prefijo de operario GEN</label><br>
                                                <select name="val_prefijo_compartido" id="val_prefijo_compartido " class="select2 form-control" style="width:350px;"  >
                                                    <option value=""></option>
                                                    @foreach ($operarios_gen->unique() as $c)
                                                        <option {{isset($users) && $users->val_prefijo_compartido == $c->nom_operario ? 'selected' : ''}} value="{{$c->nom_operario}}">{{$c->nom_operario}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5 d_contrata cmb_op" style="{{ isset($users->mca_compartido)&&$users->mca_compartido=='N'?'':'display:none' }}" >
                                            <div class="form-group">
                                                <label>Operario</label><br>
                                                <select name="id_operario" id="id_operario " class="select2 form-control"  >
                                                    <option value=""></option>
                                                    @foreach ($operarios_ind as $c)
                                                        <option {{isset($users) && $users->id_operario == $c->id_operario ? 'selected' : ''}} value="{{$c->id_operario}}">[{{ $c->des_contrata }}] {{$c->nom_operario}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="demo-stk-lft-tab-6"  class="tab-pane fade " role="tabpanel" aria-labelledby="seguridad-tab">
                            <div class="card">
                                <div class="card-header">
                                    <span class="text-main text-semibold float-left mr-5">@if ($users->two_factor_secret!=null) <i class="fa-solid fa-circle-check text-success check_2fa"></i>@endif  Autenticacion de doble factor</span>
                                    @if ($users->two_factor_secret!=null)
                                    <a href="#" class="btn btn-danger btn_desactivar_2fa">
                                        Desactivar
                                    </a>
                                    @else
                                    <a href="#" class="badge bg-warning">
                                        No activada
                                    </a>
                                    @endif
                                </div>
                                <div class="card-body" id="content_2fa">
                                    @if ($users->two_factor_secret!=null)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>QR de activacion</label><br>
                                            {!! App\user::find($users->id)->twoFactorQrCodeSvg()!!}<br>
                                           {{ decrypt(App\user::find($users->id)->two_factor_secret)}}
                                        </div>
                                        <div class="col-md-6">
                                            <label>Codigos de emergencia</label>
                                            <ul class="list-group mb-2" style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">
                                            @foreach (json_decode(decrypt(App\user::find($users->id)->two_factor_recovery_codes)) as $code)
                                                <li class="list-group-item">{{ $code }}</li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-3  mt-1">
                                            <div class="form-check pt-3">
                                                <input  name="sso_override"  id="sso_override" value="S" {{ isset($users->sso_override)&&$users->sso_override=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                                <label class="form-check-label" for="sso_override">Bypass SSO</label>
                                            </div>
                                        </div>
                                        @if(isset($users) && $users->deleted_at!=null)
                                        <div class="col-md-5">
                                            <label class="control-label">Borrado logico</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="deleted_at" readonly=""  id="deleted_at"  class="form-control" value="{{isset($users) ? $users->deleted_at : ''}}">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-success" type="button"  id="btn_restablecer">Restablecer</button>
                                                    <button class="btn btn-danger" type="button"  id="btn_definitivo">Eliminar</button>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div id="demo-stk-lft-tab-4"  class="tab-pane fade" role="tabpanel" aria-labelledby="puestos-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Tipos de espacio que puede reservar</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row rounded b-all mb-2 bg-gray-light">
                                        <div class="col-md-12">
                                            <label><input  id="chk_todos_tipo_puesto"  class="form-check-input chk_tipo_puesto mr-1" type="checkbox"> <b>Tipos de espacio que puede reservar</b></label><br>
                                            @foreach($tipos_puestos as $t)
                                                <div class="form-group col-md-4">
                                                    <div class="form-check pt-2">
                                                        <input name="tipos_puesto_admitidos[]" id="tipo_puesto{{$t->id_tipo_puesto}}" value="{{$t->id_tipo_puesto}}" {{ in_array($t->id_tipo_puesto,$tipos_puesto_usuario)?'checked':'' }} class="form-check-input chk_tipo_puesto" type="checkbox">
                                                        <label class="form-check-label" for="tipo_puesto{{$t->id_tipo_puesto}}"> {{$t->des_tipo_puesto}}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Plantas en las que puede reservar</h3>
                                </div>
                                <div class="card-body" id="plantas_usuario">
                        
                                </div>
                            </div>
                            @if(isSupervisor($users->id))
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Puestos que puede gestionar como supervisor</h3>
                                </div>
                                <div class="card-body" id="puestos_usuario">
                        
                                </div>
                            </div>
                            @endif
                        </div>
                        <div id="demo-stk-lft-tab-5"  class="tab-pane fade" role="tabpanel" aria-labelledby="actividad-tab">
                            
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bitácora de {{ $users->name }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tablapuestos"  data-toggle="table" data-mobile-responsive="true"
                                            data-locale="es-ES"
                                            data-search="true"
                                            data-show-columns="true"
                                            data-show-columns-toggle-all="true"
                                            data-page-list="[5, 10, 20, 30, 40, 50]"
                                            data-page-size="10"
                                            data-pagination="true"
                                            data-show-button-text="true"
                                            >
                                            <thead>
                                                <tr>
                                                    <th>Modulo</th>
                                                    <th>Seccion</th>
                                                    <th style="width:30%">Accion</th>
                                                    <th>Status</th>
                                                    <th style="width: 140px">Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($bitacoras as $bitacora)
                                                <tr @if($bitacora->status=="error" || strpos($bitacora->accion,"ERROR:")!==false)  @endif>
                                                    <td>{{ $bitacora->id_modulo }}</td>
                                                    <td>{{ $bitacora->id_seccion }}</td>
                                                    <td style="word-break: break-all;">{{ $bitacora->accion }}</td>
                                                    <td class="text-center" ><span @if(strtoupper($bitacora->status)=="OK") class="badge p-2 bg-success" @else class="badge p-2 bg-danger" @endif style="padding: 0 5px 0 5px">{{ $bitacora->status }}</span></td>
                                                    <td>{!! beauty_fecha($bitacora->fecha) !!}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Reservas de {{ $users->name }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row mt-2 ">
                                        <div class="fluid col-md-12">
                                            <div id='demo-calendar'></div>
                                            <div id="events-popover-head" class="hide">Events</div>
                                            <div id="events-popover-content" class="hide">Test</div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="demo-stk-lft-tab-7"  class="tab-pane fade" role="tabpanel" aria-labelledby="permisos-tab">
                            @php
                               

                                function checkChecked($n,$s,$t,$permisos,$campo)
                                {
                                    $type = "";
                                    if ($t == "R") {$type = "mca_read";}
                                    if ($t == "W") {$type = "mca_write";}
                                    if ($t == "C") {$type = "mca_create";}
                                    if ($t == "D") {$type = "mca_delete";}
                                    foreach ($permisos as $permiso)
                                    {
                                        if ($n==$permiso->$campo && $s==$permiso->id_seccion){
                                            return $permiso->$type;
                                            break;
                                        }
                                    }
                            
                                }
                            
                            
                                $permisos=DB::table('secciones')
                                        ->select('secciones_perfiles.id_seccion',
                                                'secciones_perfiles.id_perfil',
                                                'secciones_perfiles.mca_read',
                                                'secciones_perfiles.mca_write',
                                                'secciones_perfiles.mca_create',
                                                'secciones_perfiles.mca_delete')
                                        ->join('secciones_perfiles', 'secciones.cod_seccion', '=', 'secciones_perfiles.id_seccion')
                                        ->where('secciones_perfiles.id_perfil',$users->cod_nivel)
                                        ->where(function($q){
                                            if (!fullAccess()) {
                                                $q->wherein('des_seccion',array_column(session('P'), 'des_seccion'));
                                            }
                                        })
                                        ->get();
                                $permisos_usu=DB::table('secciones')
                                        ->select('permisos_usuarios.id_seccion',
                                                'permisos_usuarios.cod_usuario',
                                                'permisos_usuarios.mca_read',
                                                'permisos_usuarios.mca_write',
                                                'permisos_usuarios.mca_create',
                                                'permisos_usuarios.mca_delete')
                                        ->join('permisos_usuarios', 'secciones.cod_seccion', '=', 'permisos_usuarios.id_seccion')
                                        ->where('permisos_usuarios.cod_usuario',$users->id)
                                        ->where(function($q){
                                            if (!fullAccess()) {
                                                $q->wherein('des_seccion',array_column(session('P'), 'des_seccion'));
                                            }
                                        })
                                        ->get();
                            
                                $secciones=DB::table('secciones')
                                          ->orderby('des_grupo')
                                          ->orderby('des_seccion')
                                          ->get();
                            
                                $niveles=DB::table('niveles_acceso')
                                        ->where('cod_nivel',$users->cod_nivel)
                                        ->get();
                            
                                $grupos=DB::table('secciones')
                                    ->selectraw('distinct(des_grupo) as des_grupo, icono')
                                    ->orderby('des_grupo')
                                    ->get();
                            
                                $primero=true;
                            
                            @endphp
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="tab-base tab-vertical">
                                                <!-- Nav tabs -->
                                                <ul class="nav flex-column  nav-pills me-2" role="tablist">

                                                    @php $primero=true;	@endphp
                                                    @foreach($grupos as $grupo)
                                                        @php
                                                            $cuenta_secciones=$secciones->where('des_grupo',$grupo->des_grupo)->count();
                                                        @endphp
                                                        <li class="nav-item {{ ($primero===true) ? "active" :"" }}"  role="presentation">
                                                            <a class="nav-link {{ ($primero===true) ? "active" :"" }}" data-bs-toggle="tab" data-bs-target="#{{  str_replace(" ","_",$grupo->des_grupo) }}" type="button" role="tab" aria-controls="{{ $grupo->des_grupo }}" aria-selected="{{ ($primero===true) ? "true" :"false" }}"><i class="{{ $grupo->icono }}"></i></span>&nbsp;&nbsp;{{ $grupo->des_grupo }} <span class="badge bg-primary ml-2 float-md-end">{{ $cuenta_secciones }}</span></a>
                                                        </li>
                                                        @php $primero=false;	@endphp
                                                    @endforeach
                                                </ul>
                                                <!-- Tab panes -->
                                                <div class="tab-content">
                                                    @php $primero=true;	@endphp
                                                    @foreach($grupos as $grupo)
                                                        @if($grupo->des_grupo != "")
                                                            <div id="{{ $grupo->des_grupo }}" class="tab-pane fade {{ ($primero===true) ? "active show" :"" }}" role="tabpanel" aria-labelledby="{{ $grupo->des_grupo }}-tab">
                                                                @php $primero = false; @endphp
                                                                {{ $grupo->des_grupo }}
                                                                <table class="table table-responsive table-hover" style="font-size:12px">
                                                                    <thead>
                                                                        <tr>
                                                                            @foreach ($niveles as $n)
                                                                                <th class="text-center celda_{{ $n->cod_nivel }}" style="font-size: 14px; font-weight: bold">
                                                                                    <span style="color: #fd7e14" class="tooltipster" data-toggle="tooltip"  title="Los permisos en naranja corresponden a permisos especificos para el usuario">{{ $users->nom_usuario }}</span>
                                                                                    <span class="tooltipster" data-toggle="tooltip"  title="Los permisos en gris corresponden a permisos heredados del perfil {{ $n->des_nivel_acceso }}" style="color: #aaa">({{$n->des_nivel_acceso}})</span>
                                                                                </th>
                                                                            @endforeach
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($secciones as $sec)
                                                                            @if($sec->des_grupo == $grupo->des_grupo)
                                                                                <tr>
                                                                                    <td style="font-size: 14px">{{$sec->des_seccion}}</td>
                                                                                    @foreach ($niveles as $n)
                                                                                        <td style="vertical-align: middle" class="celda_{{ $n->cod_nivel }}" nowrap>
                                                                                            <div class="custom-control custom-checkbox "  style="display: inline-block; @if($sec->val_tipo!='Seccion')  margin-left: 60px @else  margin-right: 6px  @endif">
                                                                                                <input @if((!checkPermissions(['Permisos'],["C"]) || !checkPermissions([$sec->des_seccion],["R"]))) disabled @endif {{checkChecked($users->id,$sec->cod_seccion,'R',$permisos_usu,'cod_usuario') ? 'checked' : ''}} type="checkbox" data-type="R" data-section="{{$sec->cod_seccion}}" data-level="{{ $users->id}}" class="check-permission form-check-input" id="read{{$sec->cod_seccion}}">
                                                                                                <label class="form-check-label" for="read{{$sec->cod_seccion}}" style="padding-top: 4px">@if($sec->val_tipo=='Seccion')R @endif</label>
                                                                                            </div>
                                                                                            @if($sec->val_tipo == 'Seccion')
                                                                                            <div class="custom-control custom-checkbox " style="display: inline-block; margin-right: 6px">
                                                                                                <input @if((!checkPermissions(['Permisos'],["C"]) || !checkPermissions([$sec->des_seccion],["W"]))) disabled @endif {{checkChecked($users->id,$sec->cod_seccion,'W',$permisos_usu,'cod_usuario') ? 'checked' : ''}} type="checkbox" data-type="W" data-section="{{$sec->cod_seccion}}" data-level="{{ $users->id}}" class="check-permission form-check-input " id="write{{$sec->cod_seccion}}">
                                                                                                <label class="form-check-label" for="write{{$sec->cod_seccion}}" style="padding-top: 4px">W</label>
                                                                                            </div>
                            
                                                                                            <div class="custom-control custom-checkbox " style="display: inline-block; margin-right: 6px">
                                                                                                <input @if((!checkPermissions(['Permisos'],["C"]) || !checkPermissions([$sec->des_seccion],["C"]))) disabled @endif {{checkChecked($users->id,$sec->cod_seccion,'C',$permisos_usu,'cod_usuario') ? 'checked' : ''}} type="checkbox" data-type="C" data-section="{{$sec->cod_seccion}}" data-level="{{ $users->id}}" class="check-permission form-check-input " id="create{{$sec->cod_seccion}}">
                                                                                                <label class="form-check-label" for="create{{$sec->cod_seccion}}" style="padding-top: 4px">C</label>
                                                                                            </div>
                            
                                                                                            <div class="custom-control custom-checkbox " style="display: inline-block; margin-right: 6px">
                                                                                                <input @if((!checkPermissions(['Permisos'],["C"]) || !checkPermissions([$sec->des_seccion],["D"]))) disabled @endif {{checkChecked($users->id,$sec->cod_seccion,'D',$permisos_usu,'cod_usuario') ? 'checked' : ''}} type="checkbox" data-type="D" data-section="{{$sec->cod_seccion}}" data-level="{{ $users->id}}" class="check-permission form-check-input " id="delete{{$sec->cod_seccion}}">
                                                                                                <label class="form-check-label" for="delete{{$sec->cod_seccion}}" style="padding-top: 4px">D</label>
                                                                                            </div>
                                                                                            @endif
                                                                                        </td>
                                                                                    @endforeach
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    @if(checkPermissions(['Usuarios'],['W']))
                    <div class="col-md-10 text-end">
                        @if (checkPermissions(['ReLogin'],["R"]) && $users->nivel_acceso<=Auth::user()->nivel_acceso)<a href="{{url('relogin',$users->id)}}" class="btn btn-lg btn-warning"><i class="fa fa-user" ></i> Suplantar</a>@endif
                    </div>
                    <div class="col-md-2 text-end">
                        <input class="btn btn-primary btn-lg btn_guardar" type="submit" value="Guardar">
                    </div>
                    @endif
                </div>
            </form>

        </div>
    </div>


<script src="{{ asset('plugins/fullcalendar/lib/main.min.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/lib/locales/es.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/tooltip.min.js') }}"></script>
<script>
    $(function(){
        $('#plantas_usuario').load("{{ url('users/plantas/'.$users->id) }}/1");
        $('#id_contrata').trigger('change');
        $('#mca_compartido').trigger('change');
    });

    $('.configuracion').addClass('active active-sub');
    $('.usuarios').addClass('active');
    @if(isSupervisor($users->id))
        $(function(){
            $('#puestos_usuario').load("{{ url('users/puestos_supervisor/'.$users->id) }}")
        });
    @endif

    $('#tablapuestos').bootstrapTable();
    
    $('#chk_todos_tipo_puesto').click(function(){
        if($(this).is(':checked')){
            $('.chk_tipo_puesto').prop('checked',true);
        }else{
            $('.chk_tipo_puesto').prop('checked',false);
        }
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(".select2").select2({
        width: "100%",
    });

    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        @desktop width: "90%", @elsedesktop width: "75%", @enddesktop
        
    });

    $('.select-all').click(function(event) {
        $(this).parent().parent().find('select option').prop('selected', true)
        $(this).parent().parent().find('select').select2({
            placeholder: "Todos",
            allowClear: true,
            width: "99.2%",
        });
        $(this).parent().parent().find('select').change();
    });

    $("#img_usuario").change(function(){
        readURL(this);
    });

    $('#btn_generar_token').click(function(event){
        //console.log('token');
        $.get( "/users/gen_token/{{ $users->id??'' }}")
        .done(function( data, textStatus, jqXHR ) {
            $('#token_1uso').val(data.access_token);
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
                console.log(errorThrown);
        });
    })
    
    $('#btn_generar_password').click(function(event){
        //console.log('token');
        $.get( "/users/gen_password/{{ $users->id??'' }}")
        .done(function( data, textStatus, jqXHR ) {
            $('#password').val(data.pwd);
            $('#password').attr('type', 'text')
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
                console.log(errorThrown);
        });
    })

    $('.notsearch').select2({
        minimumResultsForSearch: -1,
        width: "100%"
    });

    $('.chkdia').click(function(){
        $.get("{{ url('users/turno') }}/{{ $users->id??0 }}/"+$(this).val()+"/"+$(this).is(':checked'));
    })

    $('#actividad').click(function(){
        setTimeout(function(){
            calendar.render();
        },200)
    })

    $( function() {
        
        $( "#sortable" ).sortable({
            revert: true,
            dropOnEmpty: true,
            connectWith : ".draggable",
            stop: function( event, ui ) {
                console.log(ui.item.find('.puesto_pref'));
                ui.item.parent().find('.select2').css('display','none');
                ui.item.find('.detalle').css('display','block');
                ui.item.find('.papelera').css('display','block');
                ui.item.attr('id',ui.item.find('.puesto_pref').val());
                ui.item.attr('type',ui.item.attr('type'));
                $('.btn_borrar').click(function(){
                    $(this).parents(':eq(1)').remove();
                });
                if(ui.item.find('.detalle').data('id')==0){
                    console.log("El elemento no tiene ID");
                        
                    $( "#sortable" ).sortable( "cancel" );
                    toast_error('Error', 'Debe seleccionar una opcion en el elemento');
                    ui.item.remove();
                }
            }
        });

        $( ".draggable" ).draggable({
            connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid",
            opacity: 0.7,
        });
        $( "ul, li" ).disableSelection();
    });
        
    $('.puesto_pref').on('change', function (e) {
        $(this).parent().find('.detalle').attr('data-id', $(this).val());
        $(this).parent().find('.detalle').html($(this).find('option:selected').text());
    })

    $('.btn_guardar').click(function(){

        if($('#demo-stk-lft-tab-3').hasClass('active')){
            event.preventDefault();
            resultado=[];
            $('#sortable').children().each(function(item){
                elem = new Object();
                elem.id=$(this).find('.detalle').data('id');
                elem.tipo=$(this).find('.detalle').data('tipo');
                elem.text=$(this).find('.detalle').html();
                elem.color=$(this).css( "background-color" );
                elem.icono=$(this).find('h4').html();
                resultado.push(elem);
            });
            console.log(resultado);
            $('#list_puestos_preferidos').val(JSON.stringify(resultado));
            $('#edit_users_form').submit();
        }
    });

    $('.btn_borrar').click(function(){
        console.log("papelera");
        $(this).parents(':eq(1)').remove();
    });

    $('.btn_desactivar_2fa').click(function(){
        $.get("{{ url('users/activar_2fa/'.$users->id) }}/D")
        .done(function( data, textStatus, jqXHR ) {
            $('.btn_activar_2fa').html('Activar 2FA');
            $('.btn_desactivar_2fa').hide();
            $('.btn_activar_2fa').show();
            $('.check_2fa').hide();
            $('#content_2fa').empty();
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
                console.log(errorThrown);
        });
    })
    
    // Initialize the calendar
    // -----------------------------------------------------------------
    var calendarEl = document.getElementById('demo-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        eventDidMount: function(info) {
            //console.log(info);
            var tooltip = new Tooltip(info.el, {
            title: info.event.extendedProps.description,
            placement: 'top',
            trigger: 'hover',
            container: 'body'
            });
        },
        eventLimit: 4,

        eventLimitClick: function (cellInfo, jsEvent) {
    
            $(cellInfo.dayEl).popover({
                html: true,
                placement: 'bottom',
                container: 'body',
                title: function () {
                    return $("#events-popover-head").html();
                },
                content: function () {
                    return $("#events-popover-content").html();
                }
            });

            $(cellInfo.dayEl).popover('show');
        },
        dayClick: function (cellInfo, jsEvent) {
            $(this).popover({
                html: true,
                placement: 'bottom',
                container: 'body',
                title: function () {
                    return $("#events-popover-head").html();
                },
                content: function () {
                    return $("#events-popover-content").html();
                }
            });

            $(this).popover('show');
        },
        editable: false,
        droppable: false, // this allows things to be dropped onto the calendar
        eventLimit: true, // allow "more" link when too many events
        locale: 'es',
        firstDay: 1,
        themeSystem: 'bootstrap',
        moreLinkClick: "popover",
        dayMaxEventRows: 4,
        initialView: 'listWeek',
        events: {!! $eventos !!}
    });
    calendar.render();

    $('#btn_restablecer').click(function(){
        console.log("{{ url('users/restablecer/') }}/{{ $users->id??0 }}");
        window.open("{{ url('users/restablecer/') }}/{{ $users->id??0 }}",'_self');
    })

    $('#btn_definitivo').click(function(){
        console.log("{{ url('users/destroy/') }}/{{ $users->id??0 }}");
        window.open("{{ url('users/destroy/') }}/{{ $users->id??0 }}",'_self');
    })


    $('.form-ajax').submit(form_ajax_submit);

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );


    //PErmisos del usuario
    $('.check-permission').click(function(event) {
        $(this).data('_token','{{csrf_token()}}');
        if ($(this).is(':checked')) {
            $.post('{{url('addPermissions_user')}}', $(this).data(), function(data, textStatus, xhr) {
            });
        }else{
            $.post('{{url('removePermissions_user')}}', $(this).data(), function(data, textStatus, xhr) {
            });
        }
    });

    $('#niveles').on('select2:select', function (e) {
        var data = e.params.data;
        $(".celda_"+data.id).fadeToggle();
        //console.log(data.id);
    });
    $('#niveles').on('select2:unselect', function (e) {
        var data = e.params.data;
        $(".celda_"+data.id).fadeToggle();
        //console.log(data.id);
    });
    @foreach ($secciones as $s)

        @if(checkChecked($users->cod_nivel,$s->cod_seccion,'R',$permisos,'id_perfil'))
            $('#read{{$s->cod_seccion}}').prop("disabled", true);
            $('#read{{$s->cod_seccion}}').prop("checked", 'checked');
        @endif
        @if(checkChecked($users->cod_nivel,$s->cod_seccion,'W',$permisos,'id_perfil'))
            $('#write{{$s->cod_seccion}}').prop("disabled", true);
            $('#write{{$s->cod_seccion}}').prop("checked", 'checked');
        @endif
        @if(checkChecked($users->cod_nivel,$s->cod_seccion,'C',$permisos,'id_perfil'))
            $('#create{{$s->cod_seccion}}').prop("disabled", true);
            $('#create{{$s->cod_seccion}}').prop("checked", 'checked');
        @endif
        @if(checkChecked($users->cod_nivel,$s->cod_seccion,'D',$permisos,'id_perfil'))
            $('#delete{{$s->cod_seccion}}').prop("disabled", true);
            $('#delete{{$s->cod_seccion}}').prop("checked", 'checked');
        @endif
    @endforeach

    $('#mca_compartido').change(function(event) {
        if ($(this).is(':checked')) {
            $('.cmb_gen').show();
            $('.cmb_op').hide();
        }else{
            $('.cmb_gen').hide();
            $('.cmb_op').show();
        }
    });

    $('#cnt_permisos').html("{{$permisos_usu->count()}}")
    $('#id_contrata').change(function(){
        if ($(this).val()!='') {
            $('.d_contrata').show();
        }else{
            $('.d_contrata').hide();
        }
    })
</script>

