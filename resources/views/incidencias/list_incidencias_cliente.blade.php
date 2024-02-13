{{-- Aqui cargaremos los detalles --}}
<div id="editorCAM" class="mt-2 mb-5">

</div>


{{-- Listaode de incidencias del cliente --}}

@foreach ($incidencias as $inc)
    @php
        $descripcion="";
        if(isset($inc->txt_incidencia) && $inc->txt_incidencia!=''){
            $descripcion=substr($inc->txt_incidencia,0,50);
        }
        if(isset($inc->des_incidencia) && $inc->des_incidencia!=''){
            $descripcion=substr($inc->des_incidencia,0,50);
        }
    @endphp
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header toolbar">
                    <div class="toolbar-start mb-0">
                        <span class="m-0 text-muted font-bold"> ID: {{ $inc->id_incidencia }}</span>
                        <span class="float-end">{!! beauty_fecha($inc->fec_apertura)!!} 
                            @if(isset($inc->fec_cierre)) <span class="badge bg-success text-xs text-white text-center rounded-pill" style="padding: 5px; width: 100px" id="cell{{$inc->id_incidencia}}" >Cerrada</span> @else  <span class=" badge bg-blue  text-xs text-white text-center rounded-pill"  style="padding: 5px;width: 100px" id="cell{{$inc->id_incidencia}}">Abierta </span>@endif
                        </span>
                    </div>
                    <span class="text-muted" style="font-size:12px">Ult actualizacion: {!! beauty_fecha($inc->fec_audit)!!} </span>
                </div>
                <div class="card-body pt-1">
                    <div class="row">
                        <span class="rounded ml-3"  style="padding: 3px; width:100%: height: 100%; color: {{ $inc->val_color  }};">
                            <i class="{{ $inc->val_icono }}" style="color: {{ $inc->val_color  }};"></i>
                            {{$inc->des_tipo_incidencia}}
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="card-text">{{ $descripcion }}</p>
                        </div>
                    </div>
                    <div class="row">
                       
                        <div class="pull-right mt-3" style="width: 400px;">
                            <div class="btn-group btn-group pull-right float-end   ml-1" role="group">
                                <a href="#" title="Ver incidencia " data-id="{{ $inc->id_incidencia }}" class="btn btn-xs btn-primary add-tooltip btn_edit"><span class="fa fa-eye pt-1" aria-hidden="true"></span> Ver</a>
                                {{-- @if (!isset($inc->fec_cierre))<a href="#accion-incidencia" title="Acciones incidencia" data-toggle="modal" class="btn btn-xs btn-warning add-tooltip btn-accion" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-accion{{ $inc->id_incidencia }}" onclick="accion_incidencia({{ $inc->id_incidencia}})"><span class="fad fa-plus pt-1" aria-hidden="true"></span> Accion</a>@endif
                                @if (isset($inc->fec_cierre) )<a href="#reabrir-incidencia" title="Reabrir incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-reabrir" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-reabrir{{ $inc->id_incidencia }}" onclick="reabrir_incidencia({{ $inc->id_incidencia}})"><i class="fad fa-external-link-square-alt"></i> Reabrir</a>@endif
                                @if (!isset($inc->fec_cierre))<a href="#cerrar-incidencia" title="Cerrar incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-cierre" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-cierre{{ $inc->id_incidencia }}" onclick="cierre_incidencia({{ $inc->id_incidencia}})"><span class="fad fa-thumbs-up pt-1" aria-hidden="true"></span> Cerrar</a>@endif --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
$('#accion-incidencia').on('shown.bs.modal', function (e) {
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
    
    //Dropzone para adjuntos de acciones
    lista_ficheros=[];
    $('#adjuntos').val('');
    var myDropzone = new Dropzone("#dZUpload" , {
        url: '{{ url('/incidencias/upload_imagen/') }}',
        autoProcessQueue: true,
        uploadMultiple: true,
        parallelUploads: 1,
        maxFiles: {{ $config->num_imagenes_incidencias??2 }},
        addRemoveLinks: true,
        maxFilesize: 15,
        autoProcessQueue: true,
        acceptedFiles: 'image/*,video/*',
        dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Arrastre archivos <span class="font-xs">para subirlos</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (O haga Click)</h4></span>',
        dictResponseError: 'Error subiendo fichero!',
        dictDefaultMessage :
            '<span class="bigger-150 bolder"><i class=" fa fa-caret-right red"></i> Drop files</span> to upload \
            <span class="smaller-80 grey">(or click)</span> <br /> \
            <i class="upload-icon fa fa-cloud-upload blue fa-3x"></i>'
        ,
        dictResponseError: 'Error while uploading file!',
        headers: {
            'X-CSRF-TOKEN': Laravel.csrfToken
        },
        init: function() {
            dzClosure = this; // Makes sure that 'this' is understood inside the functions below.
            this.on("sending", function(file, xhr, formData) {
                formData.append("id_cliente", {{$puesto->id_cliente }});
                // formData.append("enviar_email", $("#enviar_email").is(':checked'));
                console.log(formData)
            });
            
            //send all the form data along with the files:
            this.on("sendingmultiple", function(data, xhr, formData) {
                console.log("multiple")
            });

            this.on("drop", function(event) {
                
            });

            this.on("removedfile", function(event) {
                console.log(event);
                value=event.name;
                lista_ficheros = lista_ficheros.filter(item => item.orig !== value);
                console.log(lista_ficheros);     
                ficheros_final=lista_ficheros.map(function(item,index,array){
                    return item.nuevo;
                });
                $('#adjuntos').val(ficheros_final);
            });


            this.on("maxfilesexceeded", function(event) {
                toast_warning('Incidencias','El numero maximo de adjuntos es {{ $config->num_imagenes_incidencias??2 }}')   
            });

            this.on("success", function(file, responseText) {
                //Dropzone.forElement("#dZUpload").removeAllFiles(true);
                fic=new Object();
                fic.orig=responseText.filename;
                fic.nuevo=responseText.newfilename;
                lista_ficheros.push(fic);
                ficheros_final=lista_ficheros.map(function(item,index,array){
                    return item.nuevo;
                });
                $('#adjuntos').val(ficheros_final);
                console.log(lista_ficheros);
            });
        }
    });

});

$('.btn_edit').click(function(){
   $('#editorCAM').load("{{ url('/incidencia/det/'.$puesto->token.'/'.$mireserva->pin) }}/"+$(this).data("id"), function(){
        animateCSS('#editorCAM','bounceInRight');
    });
})




</script>