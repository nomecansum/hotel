@extends($layout)

@section('content')
@php
    
    try{
        if (isset($r->tam_qr)){
            $tf=$r->tam_qr;
        } else {
            $tf=session('CL')['tam_qr'];
        }
        $tam_fuente=round(15*$tf/230);
    }   catch(\Exception $e){
            $tam_fuente=14;
    }
@endphp
@section('styles')
    <style type="text/css">
        .tarjeta {
            /* height: calc(1.7118 * 100vw); */
            background-image: url("{{ url("/img/crambo_coe.jpg") }}");
            background-repeat: no-repeat;
        }
    </style>
@endsection
@if($layout!='layout_simple')
<form method="POST" action="{{url('/ferias/marcas/print_qr')}}"  name="frm_qr" id="frm_qr">
    <div class="row b-all rounded mb-3  ml-3">
        
            {{csrf_field()}}
            <input type="hidden" name="formato" value="" id="formato">
            @if(isset($r->lista_id))
                @foreach($r->lista_id as $id)
                <input type="hidden" name="lista_id[]" value="{{ $id }}" >
                @endforeach
            @endif
            <div class="col-md-2">
                <div class="form-group">
                    <label for="">Tamaño QR</label>
                    <input type="number" class="form-control" min="50" max="500"  required name="tam_qr" id="tam_qr" value="{{ $r->tam_qr??230 }}">
                </div>
            </div>
            <div class="col-md-4">
               
            </div>

            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn  btn-primary add-tooltip mt-3 " id="btn_pdf"  title="Exportar en PDF" data-id="1" data-url=""> <i class="fad fa-file-pdf"></i> Exportar a PDF</a>
            </div>

            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn  btn-warning add-tooltip mt-3" id="btn_print" title="Imprimir" data-id="1" data-url=""> <i class="fad fa-print"></i> Impirmir</a>
            </div>

    </div>
    @else
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/fontawesome6/css/all.min.css') }}" rel="stylesheet">
@endif
<br><br>
<div style="background-color: #fff" id="printarea">
{{-- @foreach($datos as $dato)
    
        <div class="row pb-4 pr-4 mr-0 ml-1 mb-4 cont_qr" style="border: 1px solid #ccc;padding: 5px 5px 5px 5px">
            <div class="col-md-6" >
                {{$dato->des_marca}} <br>
                <img class="qr" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size($r->tam_qr)->generate(config('app.url_base_feria')."landing/marca/".$dato->token)) !!} ">
            </div>
            <div class="col-md-6" >
                @if(isset($dato->img_logo) && Storage::disk(config('app.img_disk'))->exists('img/ferias/marcas/'.$dato->img_logo))
                    <img src="{{Storage::disk(config('app.img_disk'))->url('img/ferias/marcas/'.$dato->img_logo)}}" style="width: {{ $r->tam_qr }}px" alt="">
                @else
                [LOGO]
                @endif
                
            </div>
            <div class="w-100 bg-white text-center font-bold mt-0 pb-2 texto_qr" style="background-color: #fff; font-size: {{ $tam_fuente }}px">
                @if(config('app.env')=="local")<a href="{{ config('app.url_base_feria')."landing/marca/".$dato->token }}">{{ config('app.url_base_feria')."landing/marca/".$dato->token }}</a>@endif
            </div>
        </div>
    
@endforeach --}}
<div style="background-color: #fff" id="printarea">

    @foreach($datos as $dato)        
        <div class="tarjeta mb-1" style="width: 342px; height: 498px; display: inline-block; border: 1px solid #ccc;">
            <img class="qr" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->margin(0)->generate(config('app.url_base_feria')."landing/marca/".$dato->token)) !!} " style="position: relative; top: 144px; left: 101px;">
            <div class="nombre text-center" style="position: relative; top: 150px; left: 5px; font-size: 18px; width: 320px; overflow: hide">{{$dato->observaciones}}</div>
            @if(isset($dato->img_logo) && Storage::disk(config('app.img_disk'))->exists('img/ferias/marcas/'.$dato->img_logo))
                <div class="imagen text-center" style="position: relative; top: 194px; height: 112px; overflow: hidden; backgroud-color: #ff0"><img src="{{Storage::disk(config('app.img_disk'))->url('img/ferias/marcas/'.$dato->img_logo)}}" style="width: {{ $r->tam_qr }}px;" alt=""></div>
            @endif
            {{-- @if(config('app.env')=="local")<a href="{{ config('app.url_base_feria')."landing/marca/".$dato->token }}" style="font-size: 10px" target="_blank">{{ config('app.url_base_feria')."landing/marca/".$dato->token }}</a>@endif --}}
        </div>

        {{-- <div class="w-100 bg-white text-center font-bold mt-0 pb-2 texto_qr col-md-12" style="background-color: #fff; font-size: {{ $tam_fuente }}px">
            {{$dato->nombre}} - {{ $dato->empresa }} - {{ $dato->email }}
        </div> --}}
    @endforeach
</div>
</div>
</form>
@endsection


@section('scripts')
<script>
    $('.ferias').addClass('active active-sub');
	$('.ferias_marcas').addClass('active');

    $('#btn_print').click(function(){
        $('#printarea:visible').printThis({
            importCSS: true,
            importStyle: true,
            footer: "<img src='{{ url('/imgcompo/Mosaic_brand_20.png') }}' class='float-right'>"
        });
    })

    $('#tam_qr').change(function(){
        event.preventDefault();
       $('.qr').css('width',$(this).val());
       $('.cont_qr').css('width',$(this).val());
       tam_fuente=Math.round(16*$(this).val()/230);
       $('.texto_qr').css("font-size", tam_fuente + "px");
    })

    $('#btn_pdf').click(function(){
        $('#formato').val('PDF');
        $('#frm_qr').submit();
    })


    $('#tam_qr').keydown(function(){
        console.log(event.keyCode);
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
    })

</script>
@endsection
