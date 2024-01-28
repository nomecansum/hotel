@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Crear nueva incidencia</h1>
@endsection

@section('styles')

<link href="{{url('/plugins/dropzone/dropzone.css')}}" rel="stylesheet">

@endsection

@section('breadcrumb')

@endsection

@section('content')

<div class="row" id="div_respuesta">
    <div class="col-md-3"></div>
    <div class="col-md-6 text-3x text-center rounded">
        
    </div>
    <div class="col-md-3"></div>
</div>
<script type="text/javascript"  src="{{url('/plugins/dropzone/dropzone.js')}}"></script>
@include('incidencias.fill_frm_incidencia')


@endsection


@section('scripts2')
    
    <script>

        $('input[type="file"]').change(function(e){
			var fileName = e.target.files[0].name;
			$(this).next('label').html(fileName);
			//$('.custom-file-label').html(fileName);
		});

        lista_ficheros=new Array(0);


        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};

        $('#btn_guardar').click(function(){
            $('#spinner').show();
            @if(config('app.env')!="local") $('#btn_guardar').hide(); @endif
            $('#incidencia_form').submit();
        });

        Dropzone.options.dZUpload= {
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
            headers: {
                'X-CSRF-TOKEN': Laravel.csrfToken
            },
            init: function() {
                dzClosure = this; // Makes sure that 'this' is understood inside the functions below.
                this.on("sending", function(file, xhr, formData) {
                    formData.append("id_cliente", {{ Auth::user()->id_cliente }});
                    // formData.append("enviar_email", $("#enviar_email").is(':checked'));
                    console.log(file)
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
                    console.log("onremoved");
                });


                this.on("maxfilesexceeded", function(event) {
                    toast_warning('Incidencias','El numero maximo de adjuntos es {{ $config->num_imagenes_incidencias??2 }}')   
                });

                this.on("success", function(file, responseText) {
                    //Dropzone.forElement("#dZUpload").removeAllFiles(true);
                    console.log(responseText);
                    fic=new Object();
                    fic.orig=responseText.filename;
                    fic.nuevo=responseText.newfilename;
                    lista_ficheros.push(fic);
                    ficheros_final=lista_ficheros.map(function(item,index,array){
                        return item.nuevo;
                    });
                    $('#adjuntos').val(ficheros_final);
                    console.log(ficheros_final);
                });
            }
        }
    </script>
@endsection
