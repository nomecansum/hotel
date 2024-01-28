@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Configurador de URL Señaletica</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item">Utilidades</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">señaletica</a></li>
        <li class="breadcrumb-item active">configurador de url</li>
    </ol>
@endsection

@section('content')
@php
    //dd($edificios);
@endphp
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Configurar URL</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-4">
                <label for="id_usuario">Utilizar cuenta de usuario</label>
                <select name="id_usuario" id="id_usuario" class="form-control">
                    <option value="0"></option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->token_acceso}}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="planta">Planta</label>
                <select name="id_planta" id="id_planta" class="form-control">
                    @foreach($edificios as $ed)
                        @php
                            $misplantas=$plantas->where('id_edificio',$ed->id_edificio)->sortby('des_planta');
                        @endphp
                        <optgroup label="{{ $ed->des_edificio }}">
                            @foreach($misplantas as $planta)
                                <option value="{{ $planta->id_planta}}">{{ $planta->des_planta }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2">
                <label for="max_horas_reservar">Intervalo (min)</label>
                <input type="number" min="1" max="999999" name="intervalo" id="intervalo" class="form-control" value="1">
            </div>
            
            <div class="form-group col-md-2" style="margin-top: 22px">
                <button type="button" class="btn btn-primary mr-2" id="btn_add">Añadir</button>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-10">
                <label>Lista de reproduccion</label>
                <ul class="b-all" id="lista_rep">

                </ul>
            </div>
            <div class="form-group col-md-2">
                
                <button type="button" class="btn btn-success mr-2 text-center btn-lg mt-4" id="btn_gen">GENERAR</button>
            </div>
        </div>
    </div>
</div>
<div class="card" id="descarga" style="display: none">
    <div class="card-header">
        <h3 class="card-title">Descarga de aplicacion de señaletica</h3>
    </div>
    <div class="card-body">

        <div class="row">
            <div class="col-md-12">
                Aqui esta el fichero de configuracion para la aplicacion de señaletica que deberá montar en los equipos para que muestren esta lista de reproduccion
            </div>
            <div class="col-md-12">
                <a href="#" id="link_config" class="text-warning text-bold font-20"><i class="fad fa-file fa-2x"></i> playerweb.json</a>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                Recuerde que debe colocar el fichero de configuracion, tal y como lo descargue en la raiz de la carpeta del reproductor de señaletica. <br>
                El reproductor de señaletica y las instrucciones para su configuracion puede descargarlos aqui
            </div>
            <div class="col-md-12">
                <a href="https://spotdesking.s3-eu-west-1.amazonaws.com/spotdesking/PlayerWEB.zip" id="zip_player" class="text-secondary text-bold font-20"><i class="fad fa-file fa-2x"></i> playerweb.zip</a>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.configuracion').addClass('active active-sub');
        $('.menu_utilidades').addClass('active active-sub');
        $('.menu_parametrizacion').addClass('active active-sub');
        $('.mkd').addClass('active');
        var lista=[];
        var num=1;

        function pintar_lista(){
            $('#lista_rep').empty();
            for (var i = 0; i < lista.length; i++) {
                //Do something
                $('#lista_rep').append('<li id="elem'+lista[i].id+'">'+lista[i].url+' <i class="fa fa-arrow-right"></i> '+lista[i].time+' min <i class="fa fa-trash text-danger btn_trash ml-4" onclick="borrar('+lista[i].id+')"></i></li>');
            }
        }

        $('#btn_add').click(function(){
            obj={};
            obj.url="{{ url('/MKD/plano') }}"+"/"+$('#id_planta').val()+"/"+$('#id_usuario').val();
            obj.id=num;
            obj.time=$('#intervalo').val();
            num++;
            lista.push(obj);     
            console.log(lista);
            pintar_lista();
        })

        $('#btn_gen').click(function(){
            if (lista.length<1){
                toast_error('Generar fichero de señaletica','Debe añadir al menos un elemento a la lista de reproduccion');
                exit();
            }
            $('#descarga').show();
            animateCSS('#descarga','bounceInRight');
        })

        $('#link_config').click(function(){
            $.post("{{url('MKD/gen_config')}}", {_token:'{{csrf_token()}}',intervalo:$('#intervalo').val(), lista: lista }, function(data, textStatus, xhr) {
                var blob=new Blob([data]);
                var link=document.createElement('a');
                link.href=window.URL.createObjectURL(blob);
                link.download="playerweb.json";
                link.click();
            });
        })

        function borrar(id_borrar){
            console.log(lista);
            lista = lista.filter(function( obj ) {
                return obj.id !== id_borrar;
            });
            pintar_lista();
        }
    </script>
@endsection
