<style type="text-css">
	.center_input {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}
</style>

<div class="card editor mb-5" id="editor">
	@php
		//dd($c);
	@endphp
    <div class="card">
		<div class="card-header toolbar">
			<div class="toolbar-start">
				<h5 class="m-0">
					@if($c->id_cliente!=0)
					<h3 class="card-title">Modificar cliente</h3>
					@else
					<h3 class="card-title">Nuevo cliente</h3>
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
			@if($c->id_cliente!=0)
				<form action="{{url('/clientes/update')}}" method="POST" class="form-ajax" id="frm_cliente" enctype='multipart/form-data'>
				<input type="hidden" name="id" value="{{$c->id_cliente}}">
			@else
				<form action="{{url('/clientes/save')}}" method="POST" class="form-ajax"  id="frm_cliente" enctype='multipart/form-data'>
			@endif
			{{csrf_field()}}
			<div class="tab-base">
					
				<!--Nav tabs-->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item active" role="presentation">
						<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-1" type="button" role="tab" aria-controls="general" aria-selected="true">Datos generales</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-2" type="button" role="tab" aria-controls="config" aria-selected="true">Configuracion</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-5" type="button" role="tab" aria-controls="seguridad" aria-selected="true">Seguridad</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-3" type="button" role="tab" aria-controls="logos" aria-selected="true">Logos</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-4" type="button" role="tab" aria-controls="tema" aria-selected="true">Tema</button>
					</li>
				</ul>
	
				<!--Tabs Content-->
				<div class="tab-content">
					<div id="demo-stk-lft-tab-1" class="tab-pane fade  active show"  role="tabpanel" aria-labelledby="general-tab">
						<div class="row">
							<div class="col-12">
								<div class="row">
									<div class="col-sm-12">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Nombre</label>
													<input type="text" name="nom_cliente" class="form-control" required value="{{isset($c) ? $c->nom_cliente : ''}}">
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="">Distribuidor</label>
													<select name="id_distribuidor" id="id_distribuidor" class="form-control" style="width: 100%">
														<option value=""></option>
														@foreach (\DB::table('distribuidores')->get() as $d)
															<option {{isset($c) && $c->id_distribuidor == $d->id_distribuidor ? 'selected' : ''}} value="{{$d->id_distribuidor}}">{{$d->nom_distribuidor}}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="">ID externo</label>
													<input type="text" name="id_cliente_externo" class="form-control" value="{{isset($c) ? $c->id_cliente_externo : ''}}">
												</div>
											</div>
										</div>
										
										<div class="row mt-2">
											<div class="col-md-12">
												<div class="form-group">
													<label for="">Informacion de contacto</label>
													<input type="text" name="nom_contacto" class="form-control" value="{{isset($c) ? $c->nom_contacto : ''}}">
												</div>
											</div>
										</div>
										<div class="row mt-2">
											<div class="col-md-12">
												<label class="control-label">Token registro</label>
												<div class="input-group mb-3">
													<input type="text" name="token_1uso" readonly=""  id="token_1uso"  class="form-control" value="{{isset($c) ? $c->token_1uso : ''}}">
													<div class="input-group-btn">
														@if(checkPermissions(['Clientes'],["W"]))<button class="btn btn-secondary" type="button"  id="btn_generar_token">Generar</button>@endif
													</div>
												</div>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
						
						
					</div>
					<div id="demo-stk-lft-tab-2" class="tab-pane fade"  role="tabpanel" aria-labelledby="config-tab">
						<div class="row">
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_restringir_usuarios_planta" title="Indica que a los usuarios solo se les mostrara para reservar las plantas que tengan asigndadas en su configuracion"  id="mca_restringir_usuarios_planta" value="S" {{ isset($config->mca_restringir_usuarios_planta)&&$config->mca_restringir_usuarios_planta=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label" for="mca_restringir_usuarios_planta">Restringir plantas usuarios</label>
								</div>
							</div>
							<div class="col-md-3  mt-1">
								<div class="form-check pt-2">
									<input  name="mca_limpieza"  id="mca_limpieza" value="S" title="indica que un pueto despues de usado pasa a estado limpieza si esta activda o a estado disponible si no lo esta" {{ isset($config->mca_limpieza)&&$config->mca_limpieza=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label" for="mca_limpieza">Funcion de limpieza</label>
								</div>
							</div>
							<div class="col-md-3  mt-1">
								<div class="form-check pt-2">
									<input  name="mca_salas"  id="mca_salas" value="S" title="Habilita el uso de salas de reunion y su gestor de reservas" {{ isset($config->mca_salas)&&$config->mca_salas=='S'?'checked':'' }} class="form-check-input add-tooltip"  type="checkbox">
									<label class="form-check-label"  for="mca_salas">Gestion de salas</label>
								</div>
							</div>
							<div class="col-md-3  mt-1">
								<div class="form-check pt-2">
									<input  name="mca_checkin"  id="mca_checkin" value="S" title="Habilita el uso de la funcion check-in/check-out al escanear los puestos" {{ isset($config->mca_checkin)&&$config->mca_checkin=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label" for="mca_checkin">Funcion de check-in</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input  name="mca_permitir_anonimo"  id="mca_permitir_anonimo" title="Permite el checkin de los puestos marcados como anonimos a usuarios que no tengan cuenta en la herramienta" value="S" {{ isset($config->mca_permitir_anonimo)&&$config->mca_permitir_anonimo=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label" for="mca_permitir_anonimo">Permitir escaneo anónimo</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input  name="mca_reserva_horas"  id="mca_reserva_horas" value="S" title="Indica que las reservas se pueden realizar por unas horas determinadas o durante todo el dia" {{ isset($config->mca_reserva_horas)&&$config->mca_reserva_horas=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label" ffor="mca_reserva_horas">Reservas por horas</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_mostrar_nombre_usando"  id="mca_mostrar_nombre_usando" value="S" title="Mostrar en las vistas de puesto el nombre del usuario que tiene dicho puesto" {{ isset($config->mca_mostrar_nombre_usando)&&$config->mca_mostrar_nombre_usando=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label" for="mca_mostrar_nombre_usando">Mostrar nombre que usa puesto</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input  name="mca_liberar_puestos_auto"  id="mca_liberar_puestos_auto" title="Habilita la funcion de que a una determinada hora, de forma automatica, todos los puestos utilizados de la empresa se marcarán nueva mente como disponibles" value="S" {{ isset($config->mca_liberar_puestos_auto)&&$config->mca_liberar_puestos_auto=='S'?'checked':'' }} class="form-check-input  add-tooltip" type="checkbox">
									<label class="form-check-label" for="mca_liberar_puestos_auto">Liberar puestos auto</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_mostrar_datos_fijos"  id="mca_mostrar_datos_fijos" title="Da la opcion de utilizar en el cliente, tipos de puesto, tipos de incidencia y otra serie de datos por defecto configurados por un administrador de la herramienta" value="S" {{ isset($config->mca_mostrar_datos_fijos)&&$config->mca_mostrar_datos_fijos=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label"  for="mca_mostrar_datos_fijos">Mostrar datos fijos</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_incidencia_reserva"  id="mca_incidencia_reserva" title="Permite la reserva de puestos que tengan incidencia abierta" value="S" {{ isset($config->mca_incidencia_reserva)&&$config->mca_incidencia_reserva=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label"  for="mca_incidencia_reserva">Reserva con incidencia</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_incidencia_scan"  id="mca_incidencia_scan" title="Cuando se escanea un puesto para utilizarlo, nos muestra si dicho puesto tiene incidencia abierta" value="S" {{ isset($config->mca_incidencia_scan)&&$config->mca_incidencia_scan=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label"  for="mca_incidencia_scan">Mostrar incidencia al escanear</label>
								</div>
							</div>
							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_mail_apertura_incidencia"  id="mca_mail_apertura_incidencia" title="Determina si para este cliente se quiere que se mande el mail de cortesia que acredita la apertura de una incidencia al usuario abriente" value="S" {{ isset($config->mca_mail_apertura_incidencia)&&$config->mca_mail_apertura_incidencia=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label"  for="mca_mail_apertura_incidencia">Mail de apertura de incidencia</label>
								</div>
							</div>

							<div class="col-md-3 mt-1">
								<div class="form-check pt-2">
									<input name="mca_solicitudes"  id="mca_solicitudes" title="Determina si para este cliente se quiere que se mande el mail de cortesia que acredita la apertura de una incidencia al usuario abriente" value="S" {{ isset($config->mca_solicitudes)&&$config->mca_solicitudes=='S'?'checked':'' }} class="form-check-input add-tooltip" type="checkbox">
									<label class="form-check-label"  for="mca_mail_apertura_incidencia">Gestion de solicitudes</label>
								</div>
							</div>
							
						</div>

						<div class="row mt-4">
							<div class="col-md-2">
								<div class="form-group">
									<label for="" class="add-tooltip" title="Determina si se mandan notificaciones a usuarios o no de forma general, independientemente de las preferencias de cada usuario">Notificar a usuarios</label>
									<select name="val_metodo_notificacion" id="val_metodo_notificacion"  class="form-control">
										<option value="0"  {{isset($config->val_metodo_notificacion) && $config->val_metodo_notificacion == 0 ? 'selected' : ''}}>No</option>
										<option value="1"  {{isset($config->val_metodo_notificacion) && $config->val_metodo_notificacion == 1 ? 'selected' : ''}}>e-mail</option>
										{{--  <option value="2"  {{isset($c) && $c->val_metodo_notificacion == 0 ? 'selected' : ''}}>Notificacion APP</option>
										<option value="3"  {{isset($c) && $c->val_metodo_notificacion == 0 ? 'selected' : ''}}>Ambas</option>  --}}
									</select>
								</div>
							</div>
							{{-- <div class="col-md-2">
								<div class="form-group">
									<label for="">Tamaño QR</label>
									<input type="number" class="form-control" min="50" max="500"  required name="tam_qr" value="{{ $config->tam_qr??230 }}">
								</div>
							</div> --}}
							<div class="col-md-3">
								<div class="form-group">
									<label for="" class="add-tooltip" title="Determina Los campos descriptivos que se ofrecerán al usuario al abrir una incidencia">Layout incidencias</label>
									<select name="val_layout incidencias" id="val_layout incidencias" class="form-control ">
										<option value="A"  {{isset($config->val_layout_incidencias) && $config->val_layout_incidencias == 'A' ? 'selected' : ''}}>Titulo y descripcion</option>
										<option value="T"  {{isset($config->val_layout_incidencias) && $config->val_layout_incidencias == 'T' ? 'selected' : ''}}>Solo titulo</option>
										<option value="D"  {{isset($config->val_layout_incidencias) && $config->val_layout_incidencias == 'D' ? 'selected' : ''}}>Solo descripcion</option>
										{{--  <option value="2"  {{isset($c) && $c->val_metodo_notificacion == 0 ? 'selected' : ''}}>Notificacion APP</option>
										<option value="3"  {{isset($c) && $c->val_metodo_notificacion == 0 ? 'selected' : ''}}>Ambas</option>  --}}
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for=""  class="add-tooltip" title="Determina cuantas imagenes adjuntas se permiten en incidencias">Imagenes incidencias</label>
									<input type="number" class="form-control" min="0" max="2"  required name="num_imagenes_incidencias" value="{{ $config->num_imagenes_incidencias??1 }}">
								</div>
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-md-3">
								<label for="modo_visualizacion_reservas"  class="add-tooltip" title="Vista por defecto de las pantallas del gestor de reservas">Vista por defecto en reservas</label>
								<select name="modo_visualizacion_reservas" id="modo_visualizacion_reservas" class="form-control" style="width: 100%">
									<option value="M" {{ isset($config->modo_visualizacion_reservas)&&$config->modo_visualizacion_reservas=='M'?'selected':'' }}>Mosaico (Puestos)</option>
									<option value="P" {{ isset($config->modo_visualizacion_reservas)&&$config->modo_visualizacion_reservas=='P'?'selected':'' }}>Plano</option>
								</select>
							</div>
							<div class="col-md-2">
								<label for="modo_visualizacion_puestos"   class="add-tooltip" title="Aparienda de la representacion de puestos. Puede ser un cuadro o el icono que se haya seleccionado en el tipo de puesto o en el propio puesto">Vista de puesto</label>
								<select name="modo_visualizacion_puestos" id="modo_visualizacion_puestos" class="form-control" style="width: 100%">
									<option value="C" {{ isset($config->modo_visualizacion_puestos)&&$config->modo_visualizacion_puestos=='C'?'selected':'' }}>Cuadro</option>
									<option value="I" {{ isset($config->modo_visualizacion_puestos)&&$config->modo_visualizacion_puestos=='I'?'selected':'' }}>Icono</option>
								</select>
							</div>
							<div class="col-md-3">
								<label for="val_campo_puesto_mostrar"   class="add-tooltip" title="Determina cual de los campos descriptivos que tiene el puesto se muestra para representarlo ">Mostrar como nombre de puesto</label><br>
								<select name="val_campo_puesto_mostrar" id="val_campo_puesto_mostrar" class="form-control" style="width: 100%">
									<option value="D" {{ isset($config->val_campo_puesto_mostrar)&&$config->val_campo_puesto_mostrar=='D'?'selected':'' }}>Descripcion</option>
									<option value="I" {{ isset($config->val_campo_puesto_mostrar)&&$config->val_campo_puesto_mostrar=='I'?'selected':'' }}>Identificador</option>
									<option value="A" {{ isset($config->val_campo_puesto_mostrar)&&$config->val_campo_puesto_mostrar=='A'?'selected':'' }}>[Identif] Descripcion</option>
								</select>
							</div>
							{{-- <div class="form-group col-md-2" style="{{ $config->mca_liberar_puestos_auto=='N'?'display:none':'' }}" id="grupo_liberar">
								<label for="hora_liberar_puestos">Hora def. de liberar</label><br>
								<input type="time" autocomplete="off" name="hora_liberar_puestos" id="hora_liberar_puestos"   class="form-control" value="{{isset($config->hora_liberar_puestos)?$config->hora_liberar_puestos:'23:59'}}" />
							</div> --}}
							<div class="form-group col-md-2"  id="grupo_liberar">
								<label for="mca_mostrar_puestos_reservas"  class="add-tooltip" title="Indica si se quiere ver en el gestor de reservas todos los puestos, viendo cuales estan reservados y cuales no, o solo los disponibles segun los criterios dados">Mostrar en reserva</label><br>
								<select name="mca_mostrar_puestos_reservas" id="mca_mostrar_puestos_reservas" class="form-control" style="width: 100%">
									<option value="D" {{ isset($config->mca_mostrar_puestos_reservas)&&$config->mca_mostrar_puestos_reservas=='D'?'selected':'' }}>Solo disponibles</option>
									<option value="T" {{ isset($config->mca_mostrar_puestos_reservas)&&$config->mca_mostrar_puestos_reservas=='T'?'selected':'' }}>Todos</option>
								</select>
							</div>
							
							<div class="form-group col-md-2">
								<label for="max_dias_reserva"  class="add-tooltip" title="Determina numero de dias maximo por defecto que los usuarios pueden reservar con antelacion. La antelacion establecida en el tipo de puesto tiene prioridad sobre esta">Max de dias reserva</label><br>
								<select name="max_dias_reserva" id="max_dias_reserva"  class="form-control ">
									@for ($n=1;$n<=100;$n++)
										<option value={{$n}}  {{  isset($config->max_dias_reserva)&&$config->max_dias_reserva==$n?'selected':''  }}>{{ $n }}</option>
									@endfor
									
								</select>
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-md-12 text-muted add-tooltip"  title="Rango de horas, dentro de cada dia, en el que se pueden realizar reservas para este cliente">
								Rango de horas de reserva
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="max_horas_reservar">Min</label>
									<input type="text" autocomplete="off" name="min_hora_reservas" id="min_hora_reservas"   class="form-control hourMask" value="{{isset($config->min_hora_reservas)?decimal_to_time($config->min_hora_reservas/60):'00:00'}}" />						
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="max_horas_reservar">Max</label>
									<input type="text" autocomplete="off" name="max_hora_reservas" id="max_hora_reservas"   class="form-control hourMask" value="{{isset($config->max_hora_reservas)?decimal_to_time($config->max_hora_reservas/60):'23:59'}}" />
								</div>
							</div>
							<div class="form-group col-md-2" id="tipo_antelacion">
								<label for="mca_tipo_antelacion"   class="add-tooltip" title="Determina si la antelacion de este cliente se tiene en cuenta desde las 0 horas del dia en cuestion o aplicando la hora minima del rango de horas de reserva">Mostrar antelacion</label><br>
								<select name="mca_tipo_antelacion" id="mca_tipo_antelacion" class="form-control" style="width: 100%">
									<option value="0" {{ isset($config->mca_tipo_antelacion)&&$config->mca_tipo_antelacion=='0'?'selected':'' }}>Desde las 0 horas</option>
									<option value="H" {{ isset($config->mca_tipo_antelacion)&&$config->mca_tipo_antelacion=='H'?'selected':'' }}>Desde la hora min</option>
								</select>
							</div>

						</div>

					</div>
					<div id="demo-stk-lft-tab-5" class="tab-pane fade"  role="tabpanel" aria-labelledby="seguridad-tab">
						<div class="row">
							<div class="col-md-4 mt-1">
								<div class="form-check pt-2">
									<input name="mca_requerir_2fa"  id="mca_requerir_2fa" value="S" {{ isset($config->mca_requerir_2fa)&&$config->mca_requerir_2fa=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" for="mca_requerir_2fa">Requerir autentificacion de doble factor</label>
								</div>
							</div>
							<div class="col-md-4  mt-1">
								<div class="form-check pt-2">
									<input  name="mca_permitir_google"  id="mca_permitir_google" value="S" {{ isset($config->mca_permitir_google)&&$config->mca_permitir_google=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" for="mca_permitir_google">Permitir autentificacion con Google</label>
								</div>
							</div>
							<div class="col-md-4  mt-1">
								<div class="form-check pt-2">
									<input  name="mca_permitir_microsoft"  id="mca_permitir_microsoft" value="S" {{ isset($config->mca_permitir_microsoft)&&$config->mca_permitir_microsoft=='S'?'checked':'' }} class="form-check-input" type="checkbox">
									<label class="form-check-label" for="mca_permitir_microsoft">Permitir autentificacion con Microsoft</label>
								</div>
							</div>
						</div>	
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-3  mt-1">
									<div class="form-check pt-2">
										<input  name="mca_saml2"  id="mca_saml2" value="S" {{ isset($config->mca_saml2)&&$config->mca_saml2=='S'?'checked':'' }} class="form-check-input" type="checkbox">
										<label class="form-check-label"  for="mca_saml2">sso SAML2</label>
									</div>
								</div>
							</div>
						</div>					
						<div class="row b-all rounded" id="row_saml2" style="{{ isset($config->mca_saml2)&&$config->mca_saml2=='S'?'':'display:none'  }}">
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Enttity ID</label>
									<input type="text" name="saml2_idp_entityid" class="form-control" value="{{isset($config) ? $config->saml2_idp_entityid : ''}}">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label for="">Login URL</label>
									<input type="text" name="saml2_idp_sso_target_url" class="form-control" value="{{isset($config_saml2) ? $config_saml2->idp_login_url : ''}}">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label for="">Logout URL</label>
									<input type="text" name="saml2_idp_slo_target_url" class="form-control" value="{{isset($config_saml2) ? $config_saml2->idp_logout_url : ''}}">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label for="">X509 Certificate</label>
									<textarea type="text" name="saml2_idp_x509_cert" class="form-control" rows=26>{{isset($config_saml2) ? $config_saml2->idp_x509_cert : ''}}</textarea>
								</div>
							</div>
						</div>	
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-3  mt-1">
									<div class="form-check pt-2">
										<input  name="mca_spotlinker_salas"  id="mca_spotlinker_salas" value="S" {{ isset($config->mca_spotlinker_salas)&&$config->mca_spotlinker_salas=='S'?'checked':'' }} class="form-check-input" type="checkbox">
										<label class="form-check-label"  for="mca_saml2">Integracion con Spotlinker salas</label>
									</div>
								</div>
							</div>
						</div>					
						<div class="row b-all rounded" id="row_salas" style="{{ isset($config->mca_spotlinker_salas)&&$config->mca_spotlinker_salas=='S'?'':'display:none'  }}">
							<div class="col-md-4">
								<div class="form-group">
									<label for="">ID cliente salas</label>
									<input type="text" name="id_cliente_salas" class="form-control" value="{{isset($c) ? $c->id_cliente_salas : ''}}">
								</div>
							</div>
							<div class="col-md-12 mb-3">
								<div class="form-group">
									<label for="">Token en salas</label>
									<input type="text" name="token_acceso_salas" class="form-control" value="{{isset($c) ? $c->token_acceso_salas : ''}}">
								</div>
							</div>
						</div>
					</div>
					<div id="demo-stk-lft-tab-3" class="tab-pane fade"  role="tabpanel" aria-labelledby="logos-tab">
						<div class="row mb-0">
							<div class="col-md-6 text-center bg-gray-light pad-all">
								<img src="img/img_logo_grande.png" style="width: 50px"> Logo grande (Home, informes) <span style="font-size:8px"> 500px</span></label>
							</div>
							<div class="col-md-6 text-center mb-0 bg-gray-light pad-all">
								<img src="img/img_logo_menu.png"  style="width: 50px"> Logo pequeño (Menu) <span style="font-size:8px">55X55px</span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 text-center b-all">
								<img src="{{ isset($c) ? Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$c->img_logo) : ''}}" style="width: 300px; margin-top: 50px" alt="" class="img-fluid ml-0">
								<div class="form-group">
	
									<div class="custom-file center_input ">
										<input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_logo" id="img_logo" lang="es">
										<label class="custom-file-label" for="img_logo"></label>
									</div>
								</div>
							</div>
							<div class="col-md-6 text-center b-all" >
								<img src="{{ isset($c) ? Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$c->img_logo_menu) : ''}}" style="idth: 300px;  margin-top: 50px" alt="" class="img-fluid ml-0">
								<div class="form-group">
									<div class="custom-file center_input ">
										<input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_logo_menu" id="img_logo_menu" lang="es">
										<label class="custom-file-label" for="img_logo_menu"></label>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					<div id="demo-stk-lft-tab-4" class="tab-pane fade"  role="tabpanel" aria-labelledby="tema-tab">
						@php
							try{
								$tema=json_decode($config->theme_name);
							} catch(\Throwable $e){
								$tema=null;
							}
							//dump($tema);
						@endphp
						<p class="text-main text-semibold">Tema que tendrán por defecto los usuarios que no lo hayan personalizado</p>
						<input type="hidden" name="tema" id="tema" value="{{ isset($tema)?$tema->tema:'' }}"> 
						<input type="hidden" name="rootClass" id="rootClass"  value="{{ isset($tema->rootClass)?$tema->rootClass:'' }}"> 
						<input type="hidden" name="esquema" id="esquema"  value="{{ isset($tema->esquema)?$ctema->esquema:'' }}"> 
						<input type="hidden" name="menu" id="menu"  value="{{ isset($tema->menu)?$tema->menu:'mn--max' }}"> 
						@include('customers.setting_include')
					</div>
				</div>
			</div>
			
			<div class="row mt-2">
				<div class="col-md-12 text-end">
					@if(checkPermissions(['Clientes'],["C"]) || checkPermissions(['Clientes'],["W"]))<button type="submit" class="btn btn-primary btn_guardar">Guardar</button>@endif
				</div>
			</div>
			
			

		</div>
	</div>
</form>
	<script src="{{ asset('/plugins/inputmask/dist/inputmask.js') }}"></script>
	<script src="{{ asset('/plugins/inputmask/dist/jquery.inputmask.js') }}"></script>
	<script src="{{ asset('/plugins/inputmask/dist/bindings/inputmask.binding.js') }}"></script>
	<script>
		$('.form-ajax').submit(form_ajax_submit);

		$('.select2').select2();

		document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

		@if(checkPermissions(['Clientes'],["W"]))
			$('#btn_generar_token').click(function(event){
				$.get( "/clientes/gen_key")
				.done(function( data, textStatus, jqXHR ) {
					$('#token_1uso').val(data);
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
						console.log(errorThrown);
				});	
			})
			if($("#token_1uso").val() == "")
				$('#btn_generar_token').click();
		@endif
		
		$('#mca_saml2').click(function(){
			$('#row_saml2').toggle();		
		})

		$('#mca_spotlinker_salas').click(function(){
			$('#row_salas').toggle();		
		})


		$('input[type="file"]').change(function(e){
			var fileName = e.target.files[0].name;
			$(this).next('label').html(fileName);
			//$('.custom-file-label').html(fileName);
		});

		// $('#mca_liberar_puestos_auto').click(function(){
		// 	if($(this).is(':checked')){
		// 		$('#grupo_liberar').show();
		// 	} else{
		// 		$('#grupo_liberar').hide();
		// 	}
			
		// })

		Inputmask({regex:"^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$"}).mask('.hourMask');


		tooltipTriggerList = [...document.querySelectorAll( '.add-tooltip' )];
     	tooltipList = tooltipTriggerList.map( tooltipTriggerEl => new bootstrap.Tooltip( tooltipTriggerEl ));
	
	</script>
@include('layouts.scripts_panel')