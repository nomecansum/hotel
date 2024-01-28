<div class="card">
    <div class="card-header">
        <h3 class="card-title" id="titulo">
            Crear {{ isset($puesto->id_puesto)&&$puesto->id_puesto!=0?'incidencia para el puesto':'' }} 
            @isset($puesto->val_icono)
                <i class="{{ $puesto->val_icono }} fa-2x" style="color:{{ $puesto->val_color }}"></i>
            @endisset
           <span class="font-bold" style="color:{{ $puesto->val_color }}; font-size: 20px">{{ $puesto->cod_puesto }}</span>

        </h3>
        <span class="float-right" id="spinner" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/incidencias/save') }}" id="incidencia_form" name="incidencia_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
        {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_puesto" value="{{ $puesto->id_puesto }}">
                <input type="hidden" name="referer" id="referer" value="{{ $referer }}">
                <input type="hidden" name="adjuntos[]" id="adjuntos" value="">
                <input type="hidden" name="procedencia" value="web"></input>
                <input type="hidden" name="tipo" value="{{ $tipo }}"></input>
                @if(isset($config->val_layout_incidencias) && ($config->val_layout_incidencias=='T' || $config->val_layout_incidencias=='A'))
                    <div class="form-group col-md-8 {{ $errors->has('des_incidencia') ? 'has-error' : '' }}">
                        <label for="des_incidencia" class="control-label">Titulo</label>
                        <input class="form-control"  name="des_incidencia" type="text" id="des_incidencia"  maxlength="200" >
                        {!! $errors->first('des_incidencia', '<p class="help-block">:message</p>') !!}
                    </div>
                @endif
                
                <div class="form-group col-md-4 {{ $errors->has('id_tipo_incidencia') ? 'has-error' : '' }}">
                    <label for="id_tipo_incidencia" class="control-label">Tipo</label>
                    <select class="form-control" required id="id_tipo_incidencia" name="id_tipo_incidencia">
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id_tipo_incidencia }}">{{ $tipo->des_tipo_incidencia }}</option>
                        @endforeach
                    </select>
                    
                </div>   
                {{-- Si es una solicitud, pondremos el campo de proyecto y el de presupuesto --}}
                @if($puesto->id_puesto==0)
                <div class="form-group col-md-2 {{ $errors->has('id_tipo_incidencia') ? 'has-error' : '' }}">
                    <label for="val_presupuesto" class="control-label">Presupuesto</label>
                    <input class="form-control"  name="val_presupuesto" type="text" id="val_presupuesto"  maxlength="200" >
                </div>
                <div class="form-group col-md-2 {{ $errors->has('id_tipo_incidencia') ? 'has-error' : '' }}">
                    <label for="val_proyecto" class="control-label">Proyecto</label>
                    <input class="form-control"  name="val_proyecto" type="text" id="val_proyecto"  maxlength="200" >
                </div>
                @endif
                
            </div>
            @if((isset($config->val_layout_incidencias) && ($config->val_layout_incidencias=='D' || $config->val_layout_incidencias=='A')) || (!isset($config->val_layout_incidencias)))
            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('txt_incidencia') ? 'has-error' : '' }}">
                    <label for="txt_incidencia" class="control-label">Descripcion</label>
                    <textarea class="form-control" name="txt_incidencia" type="text" id="txt_incidencia" value="" rows="4"></textarea>
                    {!! $errors->first('txt_incidencia', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            @endif
            <div id="dZUpload" class="dropzone mt-3">
                <div class="dz-default dz-message">
                    <h2><i class="demo-psi-upload-to-cloud display-2 text-muted"></i> Arrastre archivos <span class="text-blue">para subirlos</span></h2>&nbsp&nbsp<h6 class="display-inline text-muted"> (o Click aqui)</h6>
                </div>
            </div>

            <div class="form-group mt-3">
                <div class="col-md-12 text-center">
                    <input class="btn btn-lg btn-primary" id="btn_guardar" type="button" value="Guardar">
                </div>
            </div>
        </form>

    </div>
</div>
<script>
    function iformat(icon) {
        var originalOption = icon.element;
        return $('<span><i class="mdi ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
    }

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
			
    $('#btn_guardar').click(function(){
        $('#spinner').show();
        @if(config('app.env')!="local") $('#btn_guardar').hide(); @endif
        $('#incidencia_form').submit();
    });
    $('.form-ajax').submit(form_ajax_submit);
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
                formData.append("id_cliente", {{ Auth::user()->id_cliente }});
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

</script>
