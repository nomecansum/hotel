@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Importrar datos</h1>
@endsection

@section('styles')
<link href="{{url('/plugins/wizard/steps.css')}}" rel="stylesheet">
<link href="{{url('/plugins/dropzone/dropzone.css')}}" rel="stylesheet">

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item">Utilidades</li>
        <li class="breadcrumb-item"><a href="{{url('/import')}}">importar datos</a></li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Importar datos</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="alert" style="display: none"  id="msg_result">
                        <button type="button" class="close" onclick="$('#msg_result').hide();" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        <h3 id="h_titulo" class=""><i id="icono_msg" class=""></i> <span id="tit_msg"></span></h3> <span id="msg"></span>
                    </div>
                    <div class="card-body wizard-content">
                        <form name="form_fichero" id="form_fichero"  enctype="multipart/form-data"  action="{{ url('import/process_import') }}" class="tab-wizard wizard-circle form-horizontal" method="POST">
                            <input type="hidden" name="fichero" id="fic">
                            <input type="hidden" name="cod_cliente" id="cod_cliente" value="{{ Auth::user()->id_cliente }}">
                            {{csrf_field()}}
                            <!-- Step 1 -->
                            <h6>{{trans('strings.download_template')}}</h6>
                            <section>
                                
                                <h4>Descarge la plantilla EXCEL para rellenar con los datos especificos de su empresa haciendo click el enlace.</h4>
                                
                                
                                <br><br><br>
                                <div class="text-center">
                                    <a class="link_excel hover-this" href="{{ url('plantilla_importacion.xlsx') }}" id="link_descarga">  
                                        <img src="{{ url('img/logo_excel.png') }}">
                                        <span><h2 id="nombre_fichero" style="color: #007233">
                                            descarga_plantilla_importacion.xlsx
                                        </h2></span>
                                    </a>
                                </div>
                                <br>
                            </section>
                            <!-- Step 2 -->
                            <h6>{{trans('strings.fill_template')}}</h6>
                            <section>
                                    <h4>Rellene la plantilla EXCEL con los datos de sus empleados.</h4>
    
                                    <h4>Hay ciertos campos que son obligatorios, debe tener en cuenta de que si alguno de dichos campos no estan rellenos, se rechazará la plantilla durante el procesado.</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="font-bold">Campos obligatorios en puestos</h4>
                                            <ul>
                                                <li>COD_PUESTO: Identificador del puesto (Ej: P1-F22)</li>
                                                <li>PLANTA</li>
                                                <li>EDIFICIO</li>
                                            </ul>
                                            <span class="text-info"><i class="fad fa-info-circle"></i> Importante: Si esta añadiendo puestos sobre plantas o edificios existentes, el dato en esta columna debe coincidir EXACTAMENTE con el ya existente, de lo contrario se crearán nuevas</span>
                                            <br>
                                            
                                            <h4 class="font-bold">Campos obligatorios en usuarios</h4>
                                            <ul>
                                                <li>NOMBRE</li>
                                                <li>EMAIL</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <img src="{{ url('img/ejemplo_excel.png') }}">
                                        </div>
                                    </div>
                                    <h4>Para estos campos, si no indica valor, no se asignará nada</h4>
                                    <ul>
                                        <li>DESCRIPCION DEL PUESTO</li>
                                        <li>FOTO DE USUARIO</li>
                                        <li>EMAIL USUARIO ASIGNADO</li>
                                    </ul>
                                    <h4>Para estos campos, si no indica valor, se asume que es NO y si indica cualquier valor se asume que es SI</h4>
                                    <ul>
                                        <li>ANONIMO</li>
                                        <li>RESERVA</li>
                                        <li>ACTUALIZACION</li>
                                    </ul>
                                    <h4>Una vez rellenada la plantilla pulse "Siguiente"</h4>
                            </section>
                            <!-- Step 3 -->
                            <h6>{{trans('strings.process_template')}}</h6>
                            <section>
                                <h4>Ahora arrastre los ficheros que desea subir: imagenes de usuarios y la plantilla de excel rellenada.<br><br></h4>
                                <i class="fa fas fa-info-circle"></i>NOTA: Las fotos de usuarios deben tener el mismo nombre que haya puesto en la plantilla, si no, se descartarán.<br><br>
                                <h4>Es importante que <b>suba la plantilla en ultimo lugar</b>, pues al subir la plantilla se iniciará el procesado de la informacion y una vez subida ya no se podrán subir más ficheros con fotos.</h4>
                                <br><br>
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-primary">
                                        <div class="custom-control mr-sm-2">
                                            <div class="form-check pt-2">
                                                <input  name="enviar_email"  id="enviar_email" class="form-check-input" type="checkbox">
                                                <label class="form-check-label" for="enviar_email">Marque si desea que se envie un email de invitacion a los usuarios creados</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div id="dZUpload" class="dropzone">
                                    <div class="dz-default dz-message">
                                        <h2><i class="mdi mdi-cloud-upload"></i> Arrastre archivos <span class="text-blue">para subirlos</span></h2>&nbsp&nbsp<h6 class="display-inline text-muted"> (o Click aqui)</h6>
                                    </div>
                                </div>
                            </section>
                        </form>
                    </div>             
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script type="text/javascript"  src="{{url('/plugins/wizard/jquery.steps.min.js')}}"></script>
<script type="text/javascript"  src="{{url('/plugins/wizard/steps.js')}}"></script>
<script type="text/javascript"  src="{{url('/plugins/dropzone/dropzone.js')}}"></script>
<script>
$(function(){
    var nom_fichero="plantilla_cucoweb_";
    var fecha=moment().format('YYYYMMDD');
    $("#cod_cliente").change(function()
    {
        if($( "#cod_cliente option:selected" ).text()=='')
        {
            $('#link_descarga').hide();
        }
        else
        {
            var fichero = nom_fichero+$( "#cod_cliente option:selected" ).text()+'_'+fecha+'.xlsx';
            $('#nombre_fichero').html(fichero);
            $('#fic').val(fichero);
            $('#link_descarga').show();
            Dropzone.forElement("#dZUpload").removeAllFiles(true);
        }
    });


});
window.Laravel = {!! json_encode([
    'csrfToken' => csrf_token(),
]) !!};

Dropzone.options.dZUpload= {
    url: '{{ url('import/process_import/') }}',
    autoProcessQueue: true,
    uploadMultiple: true,
    parallelUploads: 5,
    //maxFiles: 5,
    addRemoveLinks: true,
    maxFilesize: 5,
    autoProcessQueue: true,
    acceptedFiles: 'image/*,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel',
    dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Arrastre archivos <span class="font-xs">para subirlos</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (O haga Click)</h4></span>',
    dictResponseError: 'Error subiendo fichero!',
    headers: {
        'X-CSRF-TOKEN': Laravel.csrfToken
    },
    init: function() {
        dzClosure = this; // Makes sure that 'this' is understood inside the functions below.
        this.on("sending", function(file, xhr, formData) {
            formData.append("cod_cliente", $("#cod_cliente").val());
            formData.append("enviar_email", $("#enviar_email").is(':checked'));
            console.log(formData)
        });
        
        //send all the form data along with the files:
        this.on("sendingmultiple", function(data, xhr, formData) {
            //formData.append("cod_cliente", $("#cod_cliente").val());
            //formData.append("enviar_email", $("#enviar_email").is(':checked'));
            console.log("multiple")
        });

        this.on("drop", function(event) {
            if($("#cod_cliente").val()==""){
                Swal.fire("Debe indicar un cliente para poder subir ficheros (paso 1)<br>Los ficheros subidos se descartarán");        
            }
        });

        this.on("success", function(file, responseText) {
        	Dropzone.forElement("#dZUpload").removeAllFiles(true);
            if (responseText.tipo == 'ok')
            {
                $("#msg_result").removeClass('alert-danger');
                $("#h_titulo").removeClass('text-danger');
                $("#icono_msg").removeClass('fa fa-exclamation-triangle');
                $('#msg_result').hide();
                $('#msg_result').show();
                $('#msg_result').addClass('animated bounceInRight');
                $('#msg_result').addClass('alert-success');
                $('#h_titulo').addClass('text-success');
                $('#icono_msg').addClass('fa fa-check-circle');
                $('#tit_msg').html(responseText.title);
                $('#msg').html(responseText.message);
                window.scrollTo(0, 0); 
                $("#form_fichero").hide();
            } 
            else if (responseText.tipo == 'error')
            {
                $("#msg_result").removeClass('alert-success');
                $("#h_titulo").removeClass('text-success');
                $("#icono_msg").removeClass('fa fa-check-circle');
                $('#msg_result').hide();
                $('#msg_result').show();
                $('#msg_result').addClass('animated bounceInRight');
                $('#msg_result').addClass('alert-danger');
                $('#h_titulo').addClass('text-danger');
                $('#icono_msg').addClass('fa fa-exclamation-triangle');
                $('#tit_msg').html(responseText.title);
                $('#msg').html(responseText.message);
                window.scrollTo(0, 0);
            }
        });
    }
}

    $('.configuracion').addClass('active active-sub');
    $('.menu_utilidades').addClass('active active-sub');
    $('.importar').addClass('active');
</script>
@endsection
