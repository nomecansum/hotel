@extends('layouts.landing')
@section('estilos')
<style type="text/css">
.big-checkbox {width: 25px; height: 25px;}
input[type="checkbox"] {
    vertical-align: middle;
}
</style>
@endsection
@section('cuerpo')
 <!-- Contact-->
 <section class="page-section" id="contact">
    <div class="container">
        <div class="text-center quitar">
            <h2 class="section-heading text-uppercase">Muchas gracias</h2>
            <h3 class="section-subheading text-muted">Su solicitud de mas informacion para {{ $detalles->des_marca }} ha quedado registrada.<br><br>En breve nos pondremos en contacto con usted<br>
                @if(isset($detalles->url))
                    +informacion:<br> <a href="{{ $detalles->url }}">{{ $detalles->url }}</a>
                @endif
            </h3>
            
        </div>
        
        @if(isset($cp->key_id))
        <h4 class="text-white text-center" id="result_comment"></h4>
        <div class="row frm_comentario">
            <div class="col-md-3">

            </div>
            <div class="col-md-6">
                <div class="form-group form-group-textarea mb-md-0">
                    <!-- Message input-->
                    <label class="text-white">Si quiere añadir un comentario puede hacerlo aqui:</label>
                    <textarea class="form-control w-100" id="message" name="message" placeholder="Mensaje" style="height: 67px" ></textarea>
                    <div class="invalid-feedback" data-sb-feedback="message:required">A message is required.</div>
                </div>
            </div>
            <div class="col-md-3 pt-4">
               <button class="btn btn-primary btn-xl text-uppercase btn_comment" id="submitButton" type="button">Enviar</button>
            </div>
            
        </div>
        @endif
       
    </div>
</section>

@endsection


@section('scripts1')
    @if(isset($cp->key_id))
        <script>
            $('.btn_comment').click(function(){
                $.post('{{url('/landing/comentario')}}', {_token:'{{csrf_token()}}',id_accion: {{ $cp->key_id }},txt: $('#message').val()}, function(data, textStatus, xhr) {
                $('#result_comment').html(data);
                $('.frm_comentario').hide();
                });
            })
        </script>
    @endif
    
@endsection