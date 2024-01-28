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
            <h2 class="section-heading text-uppercase">Registro</h2>
            <h3 class="section-subheading text-muted">Bienvenido a este evento, déjenos sus datos para poder informarle sobre los productos de su interes.</h3>
        </div>
        <!-- * * * * * * * * * * * * * * *-->
        <!-- * * SB Forms Contact Form * *-->
        <!-- * * * * * * * * * * * * * * *-->
        <!-- This form is pre-integrated with SB Forms.-->
        <!-- To make this form functional, sign up at-->
        <!-- https://startbootstrap.com/solution/contact-forms-->
        <!-- to get an API token!-->
        <form id="contactForm" data-sb-form-api-token="API_TOKEN" method="post" action="/landing/save">
            @csrf
            <div class="row align-items-stretch mb-5 quitar">
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- Name input-->
                        <input class="form-control" id="name" name="name" type="text" placeholder="Nombre *" required />
                        <div class="invalid-feedback" data-sb-feedback="name:required">Por favor, indique su nombre.</div>
                    </div>
                    <div class="form-group">
                        <!-- Email address input-->
                        <input class="form-control" id="email" name="email" type="email" placeholder="e-mail *" required />
                        <div class="invalid-feedback" data-sb-feedback="email:required">El e-mail es obligatorio para el registro.</div>
                        <div class="invalid-feedback" data-sb-feedback="email:email">E-mail no válido.</div>
                    </div>
                    <div class="form-group mb-md-0">
                        <!-- Phone number input-->
                        <input class="form-control" name="empresa" id="phone" type="text" placeholder="Empresa *" required />
                        <div class="invalid-feedback" data-sb-feedback="phone:required">Por favor, indique su empresa.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group form-group-textarea mb-md-0">
                        <!-- Message input-->
                        <textarea class="form-control" id="message" name="message" placeholder="Mensaje " ></textarea>
                        <div class="invalid-feedback" data-sb-feedback="message:required">A message is required.</div>
                    </div>
                </div>
            </div>
            <div class="input-group flex-nowrap quitar mb-3">
                <div class="input-group-prepend">
                    <input type="checkbox" class="big-checkbox mr-2" name="chk_acepto" id="chk_acepto" value="S"  required>
                </div>
                <label class="custom-control-label text-white ml-2" for="chk_acepto">&nbsp;&nbsp; Acepto las <a href="#" class="link_condiciones" data-toggle="modal" data-target="#condiciones_generales">condiciones generales para reservas</a> y la <a href="#" class="link_privacidad" data-toggle="modal" data-target="#condiciones_generales">política de privacidad y tratamiento de datos</a>.</label>
                <div class="invalid-feedback" data-sb-feedback="chk_acepto:required">Por favor, indique su empresa.</div>
            </div>
            <div class="input-group flex-nowrap quitar mt-2 mb-5">
                <div class="input-group-prepend">
                    <input type="checkbox" class="big-checkbox mr-2"  name="chk_mandar" id="chk_mandar"  value="S">
                </div>
                <label class="custom-control-label text-white" for="chk_mandar">&nbsp;&nbsp; Mandadme de vez en cuando información de los eventos y promociones que tenéis.</label>
            </div>
            <!-- Submit success message-->
            <!---->
            <!-- This is what your users will see when the form-->
            <!-- has successfully submitted-->
            <div id="submitSuccessMessage" style="display: none">
                <div class="text-center text-white mb-3">
                    <div class="fw-bolder">Registro completado!</div>
                    Muchas gracias
                    <br />
                    
                </div>
            </div>
            <!-- Submit error message-->
            <!---->
            <!-- This is what your users will see when there is-->
            <!-- an error submitting the form-->
            <div class="d-none" id="submitErrorMessage"><div class="text-center text-danger mb-3">Error guardando registro!</div></div>
            <!-- Submit Button-->
            <div class="text-center"><button class="btn btn-primary btn-xl text-uppercase quitar" id="submitButton" type="button">REGISTRARME</button></div>
        </form>
    </div>
</section>

<div class="portfolio-modal modal fade" id="condiciones_generales" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="close-modal" data-bs-dismiss="modal"><img src="/landing/assets/img/close-icon.svg" alt="Close modal" /></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="modal-body" id="body_condiciones">
                           
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts1')
    <script>
        $('.link_condiciones').click(function(){
            $('#condiciones_generales').modal('show');
            $("#body_condiciones").load("/landing/condiciones_reservas.html");
        });

        $('.link_privacidad').click(function(){
            $('#condiciones_generales').modal('show');
            $("#body_condiciones").load("/landing/privacidad.html");
        });

        $('#submitButton').click(function(){
            console.log($("#contactForm").serializeArray());
            $.post("/landing/save", $("#contactForm").serializeArray(), function(data, textStatus, xhr) {
                
                console.log(data);
                $('.quitar').hide();
                $('#submitSuccessMessage').show();
            })
            .fail(function(err) {
                let error = JSON.parse(err.responseText);
                //toast_error("ERROR",error.message);
            })
            .always(function() {
                //fin_espere();
                console.log("complete");
                //$(this).find('[type="submit"]').attr('disabled',false);
            });
        });
    </script>
    
@endsection