@extends('layout')

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

@section('breadcrumb')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
	<li class="breadcrumb-item">ferias</li>
	<li class="breadcrumb-item"><a href="{{url('/ferias/asitentes')}}" class="link-light">asistentes </a></li>
	 <li class="breadcrumb-item active">Imprimir QR asistentes</li> 
</ol>
@endsection

@section('content')

@if($layout!='layout_simple')
<form method="POST" action="{{url('/ferias_asistentes/preview_qr')}}"  name="frm_qr" id="frm_qr" enctype="multipart/form-data">
    <div class=" card row b-all rounded mb-3  ml-3 bs-comp-active-bg">
            {{csrf_field()}}
            <div class="card-header">
                <h4>Configuracion de la impresion</h4>
            </div>
            <div class="card-body">
                <input type="hidden" name="formato" value="" id="formato">
                <input type="hidden" name="lista_id" value="{{ implode(",",$r->lista_id) }}" id="formato">
                <input type="hidden" name="id_cliente" value="{{ Auth::user()->id_cliente }}">
                <input type="hidden" name="header" id="header" value="{{$r->header??''}}">
                <input type="hidden" name="footer" id="footer" value="{{$r->footer??''}}">
                
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Alto ficha<i class="fa-solid fa-arrow-down-small-big"></i></label>
                            <input type="number" class="form-control resize val_alto_ficha bind_value" min="50" max="1000" step="1" data-clase="val_alto_ficha" required name="tam_h_ficha" id="tam_h_ficha" value="{{ $r->tam_h_ficha??230 }}">
                        </div>
                        <input type="range" class="form-range val_alto_ficha bind_value resize" min="50" max="1000" step="1" data-clase="val_alto_ficha"  id="range_tam_h_ficha" value="{{ $r->tam_h_ficha??230 }}">
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Ancho ficha<i class="fa-solid fa-arrow-down-small-big"></i></label>
                            <input type="number" class="form-control resize val_ancho_ficha bind_value" min="50" max="1000" step="1" data-clase="val_ancho_ficha" required name="tam_w_ficha" id="tam_w_ficha" value="{{ $r->tam_w_ficha??230 }}">
                        </div>
                        <input type="range" class="form-range val_ancho_ficha bind_value resize" min="50" max="1000" step="1" data-clase="val_ancho_ficha"  id="range_tam_w_ficha" value="{{ $r->tam_w_ficha??230 }}">
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Tamaño QR<i class="fa-solid fa-arrow-down-small-big"></i></label>
                            <input type="number" class="form-control resize val_qr bind_value" min="100" max="1000" step="1" data-clase="val_qr" required name="tam_qr" id="tam_qr" value="{{ $r->tam_qr??230 }}">
                        </div>
                        <input type="range" class="form-range val_qr bind_value resize" min="100" max="1000" step="1" data-clase="val_qr"  id="range_tam_qr"  value="{{ $r->tam_qr??230 }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="">Borde <i class="fa-solid fa-arrow-left-to-line"></i></label>
                            <input type="number" class="form-control resize val_border bind_value" min="0" max="15" data-clase="val_border"  required name="border" id="border" value="{{ $r->border??1 }}">
                        </div>
                        <input type="range" class="form-range val_border bind_value resize" min="0" max="15" step="1" data-clase="val_border"  id="val_border"  value="{{ $r->border??1 }}">
                    </div>
                    <div class="col-md-1">
                        <label for="val_color">Color QR <i class="fa-solid fa-palette"></i></label><br>
                        <input type="color" autocomplete="off" name="sel_color" id="sel_color"  class="form-control refrescar_form" value="{{$r->sel_color??'#000'}}" />
                    </div>
                    <div class="col-md-1">
                        <label for="color_texto">Color txt <i class="fa-solid fa-text"></i></label><br>
                        <input type="color" autocomplete="off" name="sel_color_txt" id="sel_color_txt"  class="form-control refrescar_form" value="{{$r->sel_color_txt??'#000'}}" />
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="">Nombre <i class="fa-solid fa-text-size"></i></label>
                            <input type="number" class="form-control resize val_font_size bind_value" min="0" max="40" data-clase="val_font_size"  required name="font_size" id="font_size" value="{{ $r->font_size??14 }}" value="{{ $r->font_size??14 }}">
                        </div>
                        <input type="range" class="form-range val_font_size bind_value resize" min="0" max="40" step="1" data-clase="val_font_size"  id="range_font_size"  value="{{ $r->font_size??14 }}">
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="">Resto <i class="fa-solid fa-text-size"></i></label>
                            <input type="number" class="form-control resize val_font_size_resto bind_value" min="0" max="40" data-clase="val_font_size_resto"  required name="font_size_resto" id="font_size_resto" value="{{ $r->font_size_resto??14 }}" value="{{ $r->font_size_resto??14 }}">
                        </div>
                        <input type="range" class="form-range val_font_size_resto bind_value resize" min="0" max="40" step="1" data-clase="val_font_size_resto"  id="range_font_size_resto" value="{{ $r->font_size_resto??14 }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for=""><i class="fa-solid fa-picture"></i> Header</label>
                            <input type="file"  accept=".jpg,.png,.gif,.webp.jiff" class="form-control val_header ficheros refrescar_form" min="0" max="200" step="1" data-clase="fic_header"  id="fic_header"  value="{{ $r->header??'' }}">
                        </div>
                        
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for=""><i class="fa-solid fa-picture"></i> Footer</label>
                            <input type="file"  accept=".jpg,.png,.gif,.webp.jiff" class="form-control val_footer ficheros refrescar_form" min="0" max="200" step="1" data-clase="fic_footer"  id="fic_footer"  value="{{ $r->footer??'' }}">
                        </div>
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
                            <label for="">Salto p. <i class="fa-solid fa-file-dashed-line"></i></label>
                            <input type="number" class="form-control resize val_page_break bind_value" min="0" max="500" data-clase="val_page_break"  required name="page_break" id="page_break" value="{{ $r->page_break??40 }}" value="{{ $r->page_break??40 }}">
                        </div>
                        <input type="range" class="form-range val_page_break bind_value resize" min="0" max="500" step="1" data-clase="val_page_break"  id="range_page_break" value="{{ $r->page_break??40 }}">
                    </div>
                    <div class="col-md-1 text-right pt-2">
                        @include('resources.spin_gear',['id_spin'=>'spinner','clase'=>'spinner'])

                    </div>
                    <div class="col-md-1 text-right">
                        <a href="javascript:void(0)" class="btn  btn-info add-tooltip mt-3 text-nowrap" id="btn_pdf"  title="Exportar en PDF" data-id="1" data-url="" style="width: 80px"> <i class="fad fa-file-pdf"></i> PDF</a>
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
        @include('ferias.asistentes.fill_printarea')
    </page>
</form>
@endsection


@section('scripts')
<script>
    function save_config_print(){
        var data = $('#frm_qr').serialize();
        $.ajax({
            url: "{{ url('/ferias/save_config_print') }}",
            type: "POST",
            data: data,
            dataType: "json",
            success: function (data) {
                console.log("saved");
            },
            error: function (data) {
                console.log("Error: "+data.responsetext);
            }
        });
    }
    
    function setsize(){
        console.log('setsize');
        $('.ficha').css('width',$('#tam_ficha').val());
        $('.qr').css('width',$('#tam_qr').val());
        $('.cont_qr').css('width',$('#tam_qr').val());
        tam_fuente=Math.round(16*$('#tam_qr').val()/230);
        $('.texto_qr').css("font-size", tam_fuente + "px");
        $('.contenedor').css('margin-top',$('#margen_top').val()+'px');
        $('.contenedor').css('margin-left',$('#margen_left').val()+'px');
        $('.cont_ficha').css('margin',$('#espacio_h').val()+'px '+$('#espacio_v').val()+'px '+$('#espacio_h').val()+'px '+$('#espacio_v').val()+'px');
        $('.cont_ficha').css('width',parseInt($('#tam_w_ficha').val())+(2*parseInt($('#padding_qr').val()))+(2*parseInt($('#padding_cont').val()))+2);
        $('.cont_ficha').css('height',parseInt($('#tam_h_ficha').val())+(2*parseInt($('#padding_qr').val()))+(2*parseInt($('#padding_cont').val()))+2);
        $('.cont_ficha').css('border-width',$('#border').val()+'px');
        $('.img_qr').css('margin',$('#padding_qr').val()+'px');
        $('.cont_ficha').css('padding',$('#padding_cont').val()+'px');
        $('.nombre').css('font-size',$('#font_size').val()+'px');
        $('.resto').css('font-size',$('#font_size_resto').val()+'px');
        $('.page_breaker').css('height',$('#page_break').val()+'px');
        save_config_print();
    }

    function refresca_form(){
        $('.spinner').show();
        $('#formato').val('preview');
        save_config_print();
        let form = $('#frm_qr');
        let data = new FormData(form[0]);
        $.ajax({
            url: '{{url("/ferias/asistentes/print_qr")}}',
            type: 'POST',
            contentType: false,
            processData: false,
            data: data,
        })
        .done(function(data) {
            $('#printarea').html(data);
            $('.spinner').hide();
            
        });
    }

    $('#btn_print').click(function(){
        $('#printarea:visible').printThis({
            importCSS: true,
            importStyle: true,
            footer: "<img src='{{ url('/imgcompo/Mosaic_brand_20.png') }}' class='float-right'>"
        });
    })

    $('.resize').on("keyup change", function(){
           setsize();
    });
    

    $('.refrescar_form').change(function(){
        refresca_form();
    });


    $('#btn_pdf').click(function(){
        $('#formato').val('PDF');
        $('#frm_qr').submit();
        $('#frm_qr').attr('action','{{url("/ferias/asistentes/print_qr")}}');
    })


    $('#tam_qr').keydown(function(){
        console.log(event.keyCode);
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
    })

    $('.bind_value').on("keyup change", function(){
        //console.log('bind');
        $('.'+$(this).data('clase')).val($(this).val());
    
    })

    $('.ficheros').change(function(){
        form=$('#frm_qr');
        let formData = new FormData()
        var header = $('#fic_header')[0].files[0]
        var footer = $('#fic_footer')[0].files[0]

        formData.append('header', header);
        formData.append('footer', footer);

        $.ajax({
            url: "{{ url('/ferias/subir_imagen') }}",
            data: formData,
            cache: false,
            enctype: 'multipart/form-data',
            contentType: false,
            processData: false,
            method: 'POST',
            headers: 
            {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (data) {
                $('#header').val(data.header);
                $('#footer').val(data.footer);
                save_config_print();
                refresca_form();
            },
            error: function (data) {
                console.log("Error: "+data.responsetext);
            }
        });
    })
    
    $(function(){
        setsize();
    })

</script>
@endsection
