@php
    $edificio_ahora=0;
    $planta_ahora=0;
    use App\Models\plantas;
    $id_puesto_edit=App\Models\reservas::find($r->id_reserva??0)->id_puesto??null;
@endphp

<!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
{{-- <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet"> --}}
<link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
<style type="text/css">
    .container {
        border: 1px solid #DDDDDD;
        width: 100%;
        position: relative;
        padding: 0px 0px 0px 0px !important;
        margin: 0px 0px 0px 0px !important;
    }
    .flpuesto {
        float: left;
        position: absolute;
        z-index: 1000;
        color: #aaa;
        font-weight: bold;
        font-size: 9px;
        width: 40px;
        height: 40px;
        overflow: hidden;
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        cursor: pointer;
    }
    
    .glow {
        background-color: #1c87c9;
        border: 6px dashed yellow;
        color: #eeeeee;
        cursor: pointer;
        display: inline-block;
        font-family: sans-serif;
        font-size: 20px;
        padding: 10px 10px;
        text-align: center;
        text-decoration: none;
        opacity: 1 !important;
      }
      @keyframes glowing {
        0% {
          background-color: #2ba805;
          box-shadow: 0 0 5px #2ba805;
        }
        50% {
          background-color: #f0be1a;
          box-shadow: 0 0 20px #49e819;
        }
        100% {
          background-color: #ffee02;
          box-shadow: 0 0 5px #2ba805;
        }
      }
      .glow {
        animation: glowing 1300ms infinite;
      }

      .card_plano{
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        padding: 0px 0px 0px 0px;
        margin: 0px 0px 0px 0px;
        overflow: auto !important;
    }
</style>

<div class="row botones_accion">
    <div class="col-md-8">
        <span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="col-md-2 text-end">
        <a href="#modal-leyenda " class="link-primary" data-bs-toggle="modal" data-bs-target="#modal-leyenda"><img src="{{ url("img/img_leyenda.png") }}"> LEYENDA</a>
    </div>
    <div class="col-md-2 text-end">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off">
            <label class="btn btn-outline-primary btn-xs boton_modo" data-href="comprobar" for="btnradio1"><i class="fad fa-th"></i> Mosaico</label>
            
            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off"  checked="">
            <label class="btn btn-outline-primary btn-xs boton_modo" data-href="comprobar_plano" for="btnradio2"><i class="fad fa-map-marked-alt"></i> Plano</label>
        </div>
    </div>
</div>
@if($edificios->isempty())
    <div class="row">
        <div class="col-md-12  alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> El usuario no tiene asignada ninguna planta en la que pueda reservar, debe asignarle plantas en los detalles de usuario o utilizando la acción de "Asignar planta"
        </div>
    </div>
@endif
@foreach ($edificios as $e)
<div class="card mt-2">
    <div class="card-header bg-gray-dark">
        <div class="row">
            <div class="col-md-5">
                <span class="fs-3 ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}</span>
            </div>
            <div class="col-md-5"></div>
            <div class="col-md-2 text-end">
                <h4 class=" text-white">
                    <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                    <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $e->puestos }}</span>
                </h4>
            </div>
        </div>
    </div>
    <div class="card-body">
        @php
            $plantas=plantas::where('id_edificio',$e->id_edificio)
            ->where(function($q) use($plantas_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('id_planta',$plantas_usuario??[]);
                    $q->orwhere('plantas.mca_publica','S');
                }
                
            })
            ->where(function($q) use($id_planta){
                if($id_planta && $id_planta!=0){
                    $q->where('plantas.id_planta',$id_planta);
                }
            })
            ->get();
        @endphp
        @foreach($plantas as $pl)
            <a id="planta{{ $pl->id_planta }}">
                <div class="card border-dark mb-3">
                    <div class="card-header bg-gray">
                        <h3 >{{ $pl->des_planta }}</h3>
                    </div>
                    <div class="card-body  card_plano">
                        @include('reservas.fill-plano')
                    </div>
                </div>
                
                
            </a>
        @endforeach
    </div>
</div>
@endforeach
@include('resources.leyenda_reservas')

<script>

    

    $('.sitio').click(function(){
        $('#des_puesto').html('');
        $('#des_puesto_form').html('');
        $('#id_puesto').val(null);
        $('.disponible').removeClass('bg-info');
        $('.disponible').each(function(){
            $(this).css('background-color',$(this).data('bgcolor'));
        });
        
    });

    $('.disponible').click(function(){
        $('#des_puesto').html($(this).data('desc'));
        $('#des_puesto_form').val($(this).data('desc'));
        $('#id_puesto').val($(this).data('id'));
        $('.disponible').removeClass('bg-info');
        $('.disponible').each(function(){
            $(this).css('background-color',$(this).data('bgcolor'));
        });
        $(this).css('background-color','');
        $(this).addClass('bg-info');
        //animateCSS('#des_puesto','zoomIn');
       
        $('#frm_contador').submit();
    })

    $('.boton_modo').click(boton_modo_click);
    
    

   $('#tipo_vista').val('comprobar_plano');

   
   @if(isset($r->id_reserva)&&$r->id_reserva!=0)
    $(function(){
        $('#puesto{{ $id_puesto_edit }}').addClass('glow');
    })
   @endif
    
</script>