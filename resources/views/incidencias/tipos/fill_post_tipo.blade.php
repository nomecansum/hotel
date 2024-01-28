


@foreach($data as $index => $tipo)
    <div class="row rounded b-all mb-3" id="fila{{ $tipo->id_proceso }}">  
        @include('incidencias.tipos.fila_procesado_tipo', ['tipo' => $tipo, 'index'=>$index, 'momento'=>$momento])
        
    </div>
@endforeach
<div class="modal fade" id="modal-url" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">URL del servicio </h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>    
            <div class="modal-body">
                El formato de la URL deberá ser el estandar, sin parámetros ni interrogacion <b>?</b> que se añadiran en el siguiente campo<br><br>
                Ejemplo:<br>
                <pre><b>https://direccion_del_servidor/ruta1/ruta2  </b></pre><br><br>
                En la URL se pueden utilizar los siguientes comodines que serán sustituidos por el valor correspondiente en el momento de la petición:<br>
                <div class="col-md-12 rounded mb-3 bg-yellow" style="font-size: 12px" >
                    <h3>Campos variables</h3>
                    <div class="col-md-12">
                        <b>#id_cliente#:</b> Identificador unico del cliente<br>
                        <b>#id_incidencia#:</b> Identificador unico de la incidencia<br>
                        <b>#id_usuario_apertura#:</b> ID Usuario que ha abierto la incidencia<br>
                        <b>#id_usuario_externo#:</b> ID externo del usuario que ha abierto la incidencia<br>
                        <b>#id_usuario_cierre#:</b> ID  del usuario que ha cerrado la incidencia<br>
                        <b>#fec_apertura#:</b> Fecha de apertura de la incidencia<br>
                        <b>#fec_cierre#:</b> Fecha de cierre de la incidencia<br>
                        <b>#id_tipo_incidencia#:</b> Identificador de tipo de la incidencia<br>
                        <b>#id_puesto#:</b> Identificador del puesto<br>
                        <b>#id_estado#:</b> Identificador del estado de la incidencia<br>
                        <b>#edificio#:</b> Edificio en le que esta el puesto<br>
                        <b>#planta#:</b> Planta en la que esta el puesto<br>
                        <b>#id_cliente#:</b> Identificador de cliente<br>
                        <b>#id_incidencia_externo#:</b> Identificador de la incidencia en el sistema externo<br>
                        <b>#id_incidencia_salas#:</b> Identificador de la incidencia en spotlinker salas<br>
                        <b>#id_causa_cierre#:</b> Identificador de la causa de cierre<br>
                        <b>#url_base#:</b> URL base de la aplicacion<br>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">OK</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-param_url" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">Parametros URL </h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                El formato de los parámetros por URL será de pares nombre=valor separados por el caracter <b>&</b><br><br>
                Ejemplo:<br>
                <pre><b>param1=1&amp;param2=2&amp;param3=3  </b></pre><br><br>
                En los parámetros se pueden utilizar los siguientes comodines que serán sustituidos por el valor correspondiente en el momento de la petición:<br>
                <div class="col-md-12 rounded mb-3 bg-yellow" style="font-size: 12px" >
                    <h3>Campos variables</h3>
                    <div class="col-md-12">
                        <b>#id_cliente#:</b> Identificador unico del cliente<br>
                        <b>#id_incidencia#:</b> Identificador unico de la incidencia<br>
                        <b>#id_usuario_apertura#:</b> ID Usuario que ha abierto la incidencia<br>
                        <b>#id_usuario_externo#:</b> ID externo del usuario que ha abierto la incidencia<br>
                        <b>#id_usuario_cierre#:</b> ID  del usuario que ha cerrado la incidencia<br>
                        <b>#fec_apertura#:</b> Fecha de apertura de la incidencia<br>
                        <b>#fec_cierre#:</b> Fecha de cierre de la incidencia<br>
                        <b>#id_tipo_incidencia#:</b> Identificador de tipo de la incidencia<br>
                        <b>#id_puesto#:</b> Identificador del puesto<br>
                        <b>#id_estado#:</b> Identificador del estado de la incidencia<br>
                        <b>#edificio#:</b> Edificio en le que esta el puesto<br>
                        <b>#planta#:</b> Planta en la que esta el puesto<br>
                        <b>#id_cliente#:</b> Identificador de cliente<br>
                        <b>#id_incidencia_externo#:</b> Identificador de la incidencia en el sistema externo<br>
                        <b>#id_incidencia_salas#:</b> Identificador de la incidencia en spotlinker salas<br>
                        <b>#id_causa_cierre#:</b> Identificador de la causa de cierre<br>
                        <b>#url_base#:</b> URL base de la aplicacion<br>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">OK</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-param_header" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">Header </h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                El formato de la cabecera de la peticion deberá ser un unico objeto JSON con los valores requeridos<br><br>
                Ejemplo:<br>
<pre>{
"Accept":"application/json",
"Content-Type":"application/json",
"Authorization":"Ponga su contraseña"
}</pre>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-param_respuesta" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">Respuesta </h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>    
            <div class="modal-body">
                El formato de las reglas de evaluacion de la respuesta de la peticion deberá ser un unico objeto JSON con los valores requeridos<br>
                En la etiqueta se deberá poner el nombre del campo que se actualizará en la BDD en formato <b>"tabla.campo"</b> Las tablas que se pueden actualizar son <b>puestos</b> e <b>incidencias</b><br><br>
                El el valor se podrá poner directamente el valor deseado o poner una expresion  con el formato <b>@R:nombre_de_campo</b> de tal forma que se cogerá el campo correspondiente de la respuesta del sistema remoto. En el caso de necesitar recoger el dato de distintos niveles, debe seaprarlos por el caracter | (EJ: raiz|nivel1|nivel2|nivel3|dato)<br><br>
                Ejemplo:<br>
<pre>{
"incidencias.id_incidencia_externo": "@R:id_incidencia",
"incidencias.url_detalle_incidencia": "@R:url_detalle",
"incidencias.mca_sincronizada": "S"
}</pre>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-param_body" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">Body </h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                El formato del cuerpo de la peticion deberá ser un texto, el formato dependerá del lo que precise el sistema de destino, con los valores que se quieran enviar al sistema remoto<br><br>
                Ejemplo:<br>
<pre>{
"id_usuario_apertura": "14",
"des_incidencia": "Silla rota de nuevo",
"txt_incidencia": "TXT pruebaAAAAAAAAAAAAAAAAAAAAAAA",
"id_tipo_incidencia": 591,
"id_puesto": 17111,
"id_estado": 171
}</pre><br><br>
En las peticiones se pueden utilizar los siguientes comodines que serán sustituidos por el valor correspondiente en el momento de la petición:<br>
            <div class="col-md-12 rounded mb-3 bg-yellow" style="font-size: 12px" >
                <h3>Campos variables</h3>
                <div class="col-md-12">
                    <b>#id_cliente#:</b> Identificador unico del cliente<br>
                    <b>#id_incidencia#:</b> Identificador unico de la incidencia<br>
                    <b>#des_incidencia#:</b> Descripcion corta de la incidencia<br>
                    <b>#txt_incidencia#:</b> Descripcion larga de la incidencia<br>
                    <b>#name#:</b> Usuario que ha abierto la incidencia<br>
                    <b>#id_usuario_apertura#:</b> ID Usuario que ha abierto la incidencia<br>
                    <b>#id_usuario_externo#:</b> ID externo del usuario que ha abierto la incidencia<br>
                    <b>#id_usuario_cierre#:</b> ID  del usuario que ha cerrado la incidencia<br>
                    <b>#ema_usuario#:</b> e-mail del usaurio que ha abierto la incidencia<br>
                    <b>#fec_apertura#:</b> Fecha de apertura de la incidencia<br>
                    <b>#fec_cierre#:</b> Fecha de cierre de la incidencia<br>
                    <b>#url_detalle_incidencia#:</b> URL para el acceso directo a la incidencia<br>
                    <b>#vaL_procedencia#:</b> Procedencia de la apertura la incidencia<br>
                    <b>#id_tipo_incidencia#:</b> Identificador de tipo de la incidencia<br>
                    <b>#des_tipo_incidencia#:</b> Tipo de la incidencia<br>
                    <b>#id_puesto#:</b> Identificador del puesto<br>
                    <b>#cod_puesto#:</b> Codigo del puesto en el cliente<br>
                    <b>#id_estado#:</b> Identificador del estado de la incidencia<br>
                    <b>#edificio#:</b> Edificio en le que esta el puesto<br>
                    <b>#planta#:</b> Planta en la que esta el puesto<br>
                    <b>#id_cliente#:</b> Identificador de cliente<br>
                    <b>#img1#:</b> Imagen adjunta 1<br>
                    <b>#img2#:</b> Imagen adjunta 2<br>
                    <b>#id_incidencia_externo#:</b> Identificador de la incidencia en el sistema externo<br>
                    <b>#id_incidencia_salas#:</b> Identificador de la incidencia en spotlinker salas<br>
                    <b>#id_causa_cierre#:</b> Identificador de la causa de cierre<br>
                    <b>#comentario_cierre#:</b> Comentario de cierre de la incidencia<br>
                    <b>#des_accion#:</b> Texto de la ultima accion añadida<br>
                    <b>#url_base#:</b> URL base de la aplicacion<br>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.btn_borrar_accion').click(function(){
        $.get("{{ url('/incidencias/tipos/fila_postprocesado/delete') }}/"+$(this).data('id'),function(data){
            $('#fila'+data.id).remove();
        });
    });
</script>