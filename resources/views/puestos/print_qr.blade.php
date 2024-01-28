@extends($layout)

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
    .printarea {
    background: rgb(204,204,204); 
    }
    page[size="A4"] {
    background: white;
    width: 21cm;
    height: 59.4cm;
    display: block;
    margin: 0 auto;
    margin-bottom: 0.5cm;
    box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
    }
    @media print {
    body, page[size="A4"] {
        margin: 0;
        box-shadow: 0;
    }
    .page-break { display: block; page-break-before: always; }
    }
    @media print {
    .noprint {display:none;}
    .enable-print { display: block; }
    }

    .break-line{
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        height: 3px;
        background-color: yellowgreen;
    }

    hr {
        border: none;
        border-top: 3px dashed black;
    }
</style>
@endsection
@section('content')

@if($layout!='layout_simple')
<form method="POST" action="{{url('/puestos/preview_qr')}}"  name="frm_qr" id="frm_qr">
    <div class="row b-all rounded mb-3  ml-3 text-white">
        
            {{csrf_field()}}
            <input type="hidden" name="formato" value="" id="formato">
            <input type="hidden" name="lista_id" value="{{ implode(",",$r->lista_id) }}" id="formato">
            <input type="hidden" name="id_cliente" value="{{ Auth::user()->id_cliente }}">
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Cols <i class="fa-solid fa-columns-3"></i></label>  
                        <input type="number" class="form-control refrescar_form" min="0" max="12"  required name="col" id="col" value="{{ $r->col??0 }}">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Filas <i class="fa-solid fa-table-rows"></i></label>  
                        <input type="number" class="form-control refrescar_form" min="0" max="12"  required name="row" id="row" value="{{ $r->row??0 }}">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Tamaño <i class="fa-solid fa-arrow-down-small-big"></i></label>
                        <input type="number" class="form-control resize val_qr bind_value" min="50" max="1000" step="1" data-clase="val_qr" required name="tam_qr" id="tam_qr" value="{{ $r->tam_qr??230 }}">
                    </div>
                    <input type="range" class="form-range val_qr bind_value resize" min="50" max="1000" step="1" data-clase="val_qr"  id="range_tam_qr">
                </div>
                <div class="col-md-2">
                    <label for="val_color">Color QR <i class="fa-solid fa-palette"></i></label><br>
                    <input type="color" autocomplete="off" name="sel_color" id="sel_color"  class="form-control refrescar_form" value="{{$r->sel_color??'#000'}}" />
                </div>
                <div class="col-md-2">
                    <label for="color_texto">Color texto <i class="fa-solid fa-text"></i></label><br>
                    <select name="color_texto" id="color_texto" class="form-control refrescar_form">
                        <option {{ $r->sel_color??1==1?'selected':'' }} value="1">Negro</option>
                        <option {{ $r->sel_color??1==2?'selected':'' }} value="2">Color del tipo</option>
                        <option {{ $r->sel_color??1==3?'selected':'' }} value="3">Color del QR</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label for="color_texto">Icono <i class="fa-solid fa-icons"></i></label><br>
                    <select name="mca_icono" id="mca_icono" class="form-control refrescar_form">
                        <option value="1"  {{ $r->mca_icono??1==1?'selected':'' }} >No</option>
                        <option value="2"  {{ $r->mca_icono??1==2?'selected':'' }} >Si</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="val_color">Header <i class="fa-regular fa-diagram-successor"></i></label><br>
                    <select name="header" id="header" class="form-control refrescar_form">
                            <option value="0" {{ $r->header==0?'selected':'' }}>Ninguno</option>
                            <option value="1" {{ $r->header==1 || !isset($r->header)?'selected':'' }}>Identificador</option>
                            <option value="2" {{ $r->header==2?'selected':'' }}>descripcion</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="val_color">Footer <i class="fa-regular fa-diagram-successor"></i></label><br>
                    <select name="footer" id="footer" class="form-control refrescar_form">
                            <option value="1" {{ $r->footer==1?'selected':'' }}>Ninguno</option>
                            <option value="2" {{ $r->footer==2?'selected':'' }}>Logo</option>
                            <option value="3" {{ $r->footer==3?'selected':'' }}>Logo y nombre</option>
                            <option value="4" {{ $r->footer==4?'selected':'' }}>Logo junto a puesto</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Margen <i class="fa-solid fa-arrow-left-to-line"></i></label>
                        <input type="number" class="form-control resize val_margen_left bind_value" min="0" max="200" data-clase="val_margen_left"  required name="margen_left" id="margen_left" value="{{ $r->margen_left??4 }}">
                    </div>
                    <input type="range" class="form-range val_margen_left bind_value resize" min="0" max="200" step="1" data-clase="val_margen_left"  id="range_margen_left"  value="{{ $r->margen_left??4 }}">
                </div>
    
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Margen <i class="fa-solid fa-up-to-line"></i></label>
                        <input type="number" class="form-control resize val_margen_top bind_value" min="0" max="200" data-clase="val_margen_top"  required name="margen_top" id="margen_top" value="{{ $r->margen_top??4 }}">
                    </div>
                    <input type="range" class="form-range val_margen_top bind_value resize" min="0" max="200" step="1" data-clase="val_margen_top"  id="range_margen_top" value="{{ $r->margen_top??4 }}">
                </div>
    
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Espacio <i class="fa-solid fa-arrows-up-down"></i></label>
                        <input type="number" class="form-control resize bind_value" min="0" max="40" data-clase="val_espacio_h"  required name="espacio_h" id="espacio_h" value="{{ $r->espacio_h??4 }}">
                    </div>
                    <input type="range" class="form-range val_espacio_h val_espacio_h bind_value resize" min="0" data-clase="val_espacio_h"  max="40" step="1" id="range_espacio_h" value="{{ $r->espacio_h??4 }}">
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Espacio <i class="fa-solid fa-arrows-left-right"></i></label>
                        <input type="number" class="form-control resize val_espacio_v bind_value" min="0" max="40" data-clase="val_espacio_v"  required name="espacio_v" id="espacio_v" value="{{ $r->espacio_v??4 }}">
                    </div>
                    <input type="range" class="form-range val_espacio_v bind_value resize" min="0" max="40" step="1"  data-clase="val_espacio_v"  id="range_espacio_v" value="{{ $r->espacio_v??4 }}">
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Margin <i class="fa-solid fa-qrcode"></i></label>
                        <input type="number" class="form-control resize val_padding_qr bind_value" min="0" max="40" data-clase="val_padding_qr"   required name="padding_qr" id="padding_qr" value="{{ $r->padding_qr??2 }}">
                    </div>
                    <input type="range" class="form-range val_padding_qr bind_value resize" min="0" max="40" step="1" data-clase="val_padding_qr"  id="range_padding_qr" value="{{ $r->padding_qr??2 }}">
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Padding <i class="fa-thin fa-square"></i></label>
                        <input type="number" class="form-control resize val_padding_cont bind_value" min="0" max="40" data-clase="val_padding_cont"  required name="padding_cont" id="padding_cont" value="{{ $r->padding_cont??2 }}">
                    </div>
                    <input type="range" class="form-range val_padding_cont bind_value resize" min="0" max="40" step="1" data-clase="val_padding_cont"  id="range_padding_cont" value="{{ $r->padding_cont??2 }}">
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Fuente <i class="fa-solid fa-text-size"></i></label>
                        <input type="number" class="form-control resize val_font_size bind_value" min="0" max="40" data-clase="val_font_size"  required name="font_size" id="font_size" value="{{ $r->font_size??14 }}" value="{{ $r->font_size??14 }}">
                    </div>
                    <input type="range" class="form-range val_font_size bind_value resize" min="0" max="40" step="1" data-clase="val_font_size"  id="range_font_size">
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="">Salto p. <i class="fa-solid fa-file-dashed-line"></i></label>
                        <input type="number" class="form-control resize val_page_break bind_value" min="0" max="500" data-clase="val_page_break"  required name="page_break" id="page_break" value="{{ $r->page_break??40 }}" value="{{ $r->page_break??40 }}">
                    </div>
                    <input type="range" class="form-range val_page_break bind_value resize" min="0" max="500" step="1" data-clase="val_page_break"  id="range_page_break">
                </div>
                <div class="col-md-1 text-right pt-2">
                    @include('resources.loading',['id_spin'=>'spinner','clase'=>'spinner'])
                </div>
                <div class="col-md-1 text-right">
                    <a href="javascript:void(0)" class="btn  btn-info add-tooltip mt-3 " id="btn_pdf"  title="Exportar en PDF" data-id="1" data-url="" style="height: 53.81px; width: 67.23px"> <i class="fad fa-file-pdf"></i><br> PDF</a>
                </div>
                
                <div class="col-md-1 text-right">
                    <a href="javascript:void(0)" class="btn  btn-warning add-tooltip mt-3 text-nowrap" id="btn_print" title="Imprimir" data-id="1" data-url="" > <i class="fad fa-print"></i> PRINT</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">

                </div>
                
            </div>
    </div>
    
    @else
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/fontawesome6/css/all.min.css') }}" rel="stylesheet">
@endif
@php
$columna=1;
@endphp

    <page size="A4" style="background-color: #fff; border: 1px solid #666; width:" id="printarea">
        @include('puestos.fill_printarea')
    </page>
</form>
@endsection


@section('scripts')
<script>
    function save_config_print(){
        var data = $('#frm_qr').serialize();
        $.ajax({
            url: "{{ url('/puestos/save_config_print') }}",
            type: "POST",
            data: data,
            dataType: "json",
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log(data);
            }
        });
    }
    
    function setsize(){
        $('.img_qr').css('width',$('#tam_qr').val());
        $('.img_qr').css('height',$('#tam_qr').val());
        $('.cont_qr').css('width',$('#tam_qr').val());
        tam_fuente=Math.round(16*$('#tam_qr').val()/230);
        $('.texto_qr').css("font-size", tam_fuente + "px");
        $('.contenedor').css('margin-top',$('#margen_top').val()+'px');
        $('.contenedor').css('margin-left',$('#margen_left').val()+'px');
        $('.cont_qr').css('margin',$('#espacio_h').val()+'px '+$('#espacio_v').val()+'px '+$('#espacio_h').val()+'px '+$('#espacio_v').val()+'px');
        $('.cont_qr').css('width',parseInt($('#tam_qr').val())+(2*parseInt($('#padding_qr').val()))+(2*parseInt($('#padding_cont').val()))+2);
        $('.img_qr').css('margin',$('#padding_qr').val()+'px');
        $('.cont_qr').css('padding',$('#padding_cont').val()+'px');
        $('.texto_qr').css('font-size',$('#font_size').val()+'px');
        $('.page_breaker').css('height',$('#page_break').val()+'px');
        save_config_print();
    }

    $('#btn_print').click(function(){
        $('#printarea:visible').printThis({
            importCSS: true,
            importStyle: true,
            footer: "<img src='{{ url('/imgcompo/Mosaic_brand_20.png') }}' class='float-right'>"
        });
    })

    $('.resize').change(function(){
           setsize();
    });

    $('.refrescar_form').change(function(){
        $('.spinner').show();
        $('#formato').val('preview');
        save_config_print();
        let form = $('#frm_qr');
        let data = new FormData(form[0]);
        $.ajax({
            url: '{{url("/puestos/print_qr")}}',
            type: 'POST',
            contentType: false,
            processData: false,
            data: data,
        })
        .done(function(data) {
            $('#printarea').html(data);
            $('.spinner').hide();
            
        });
    });


    $('#btn_pdf').click(function(){
        $('#formato').val('PDF');
        $('#frm_qr').submit();
        $('#frm_qr').attr('action','{{url("/puestos/print_qr")}}');
    })


    $('#tam_qr').keydown(function(){
        console.log(event.keyCode);
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
    })

    $('.bind_value').change(function(){
        //console.log('bind');
        $('.'+$(this).data('clase')).val($(this).val());
    
    })
    
    $(function(){
        setsize();
    })

</script>
@endsection
