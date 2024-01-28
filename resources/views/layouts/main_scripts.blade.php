<script>
    ///Variables globales de la aplicacion ////
    let modal_open = false;   //Indica si hay ventanas modales abiertas

    //Locale de Moment
    moment.locale('es'); 
    
    //Para cambiar el tema de la plantilla
    var themeBtn = $('.demo-theme'),
    changeTheme = function (themeName, type) {
        var themeCSS = $('#theme'),
            fileext = '.min.css',
            filename = '/css/themes/type-' + type + '/' + themeName + fileext;

        if (themeCSS.length) {
            themeCSS.prop('href', filename);
        } else {
            themeCSS = '<link id="theme" href="' + filename + '" rel="stylesheet">';
            $('head').append(themeCSS);
        }
        localStorage.theme=themeName;
        localStorage.themetype=type;
    };

    @if(isset(session('CL')['theme_type']) && isset(session('CL')['theme_name']))
        localStorage.theme="{{ session('CL')['theme_name'] }}";
        localStorage.themetype="{{ session('CL')['theme_type'] }}";
    @endif

    //Ocultar los alert
    $('div.alert').not('.alert-important,.alert-danger,.not-dismissable').delay(5000).fadeOut(350);

    //Mostrar / Ocultar spinner
    function spshow(spinner){
        $('#'+spinner).show();
    }

    function sphide(spinner){
        $('#'+spinner).hide();
    }

    //Funciones para mostrar los mensajes Toast
    function toast_ok(titulo,mensaje){
        if (titulo==undefined){
            titulo="{{ env('APP_NAME') }}";
        }
        let toast = {
            title: titulo,
            message: mensaje,
            status: TOAST_STATUS.SUCCESS,
            timeout: 2000
        }
        Toast.setTheme(TOAST_THEME.LIGHT);
        Toast.setPlacement(TOAST_PLACEMENT.TOP_CENTER);
        Toast.create(toast);
    }
    function toast_error(titulo,mensaje){
        if (titulo==undefined){
            titulo="ERROR";
        }
        let toast = {
            title: titulo,
            message: mensaje,
            status: TOAST_STATUS.DANGER,
            timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.LIGHT);
        Toast.setPlacement(TOAST_PLACEMENT.TOP_CENTER);
        Toast.create(toast);
    }
    function toast_warning(titulo,mensaje){
        if (titulo==undefined){
            titulo="{{ env('APP_NAME') }}";
        }
        let toast = {
            title: titulo,
            message: mensaje,
            status: TOAST_STATUS.WARNING,
            timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.LIGHT);
        Toast.setPlacement(TOAST_PLACEMENT.TOP_CENTER);
        Toast.create(toast);
    }
    function toast_info(titulo,mensaje){
        if (titulo==undefined){
            titulo="{{ env('APP_NAME') }}";
        }
        let toast = {
            title: titulo,
            message: mensaje,
            status: TOAST_STATUS.INFO,
            timeout: 5000
        }
        Toast.setTheme(TOAST_THEME.LIGHT);
        Toast.setPlacement(TOAST_PLACEMENT.TOP_CENTER);
        Toast.create(toast);
    }

    function mensaje_error_respuesta(err){
        let error = JSON.parse(err.responseText);
        let html =error.message;
        console.log(error);
        $.each(error.errors, function(index, val) {
                html += "- "+$(this)[0]+"<br>";
        });
        toast_error("Error:",html);
    }

    function mensaje_error_controlado(data){
        toast_error(data.title,data.error);
    }

    function mensaje_warning_controlado(data){
        toast_warning(data.title,data.alert);
    }

    //Mostrara un sweet alert indicando que hay algo leyendo en la pagina. Para quitarlo se llama a fin_espere()
    function block_espere(mensaje="{{ __('general.cargando_espere') }}"){
        //Mostrara un sweet alert indicando que hay algo leyendo en la pagina. Para quitarlo se llama a fin_espere()
        sw=Swal.fire({
            title: mensaje,
            footer: 'Spotlinker <img src="{{ asset('/img/Mosaic_brand_20.png') }}" height="20" class="float-right">',
            allowEscapeKey: true,
            //allowOutsideClick: false,
            timer: 50000,
            showConfirmButton: false,
            //backdrop: false,
            toast: true,
            width: '15em'
            });
        Swal.showLoading();
    }

    function fin_espere(){
        Swal.close();
    }

    //Para poner animaciones en caulquier elennto
    function animateCSS(element, animationName, callback) {
        const node = document.querySelector(element)
        node.classList.add('animate__animated', 'animate__'+animationName)

        function handleAnimationEnd() {
            node.classList.remove('animate__animated', 'animate__'+animationName)
            node.removeEventListener('animationend', handleAnimationEnd)

            if (typeof callback === 'function') callback()
        }

        node.addEventListener('animationend', handleAnimationEnd)
    }

    //PAra ocultar automaticamente las notificaciones flash que no sean errores
    $('div.alert').not('.alert-important,.alert-danger,.not-dismissable').delay(5000).fadeOut(350);

    //Inicilizacion de los controles select2
    $('.select2').select2({
        width: '100%',
    }      
    );


    //Esta clase te permite poner un link en cualquier elemento
    $('.hover-this').click(function(event) {
        if (!modal_open) {
            if ($(this).data('href')) {
                window.open($(this).data('href'),'_self');
            }
        }
    });

    //Para mostrar los password
    $('.toggle-password').click(function(event) {
        if ($(this).parent().prev().attr('type') == "password") {
            $(this).parent().prev().attr('type',"text")
        }else{
            $(this).parent().prev().attr('type',"password")
        }
    });


    $('[data-toggle="modal"]').click(function(event) {
        event.stopPropagation();
        $($(this).data('target')).modal('show');
        $($(this).attr('href')).modal('show');
    });

    $('.select-all').click(function(event) {
        //element=$('#'+$(this).data('select'));
        //element.find('option').prop('selected', 'selected').end().select2();
        //element.find('option').prop('selected', '').end().select2();
        //element.val(null).trigger('change');
    });

    // when any modal is opening
    $('.modal').on('shown.bs.modal', function (e) {
      // disable your handler
      modal_open = true;
    })

    // when any modal is closing
    $('.modal').on('hidden.bs.modal', function (e) {
      // enable your handler
      modal_open = false;
    })

    //Muetra el fichero seleccionado en los custom file input
    $('.custom-file-input').on('change',function(){
        var fileName = $(this).val();
        $(this).next('.custom-file-label').html(fileName);
    })

   
        //////////////////////////////LOGICA DE GESTION DE FORMS CON AJAX/////////////////////////////
    $('.form-ajax').submit(form_ajax_submit);

    //Form enviado por AJAX con notificaciones mediabte TOAST
    function form_ajax_submit(event){
        event.preventDefault();

        //$(this).block({ message: "<br><img src='{{url('ajax-loader.gif')}}' style='width:50px'><br><br>" });
        @if(config('app.env')!='local') block_espere(); @endif

        let form = $(this);

        let data = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            contentType: false,
            processData: false,
            data: data,
        })
        .done(function(data) {
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.alert){
                toast_warning(data.title,data.alert);
            } else{
                toast_ok(data.title,data.message);
            }
            $('.modal').modal('hide');
           

            try{//Si tenemos defiinda la funcion de despues
                animateCSS('.editor','fadeOut',$('.editor').hide());
                post_form_ajax(data);
            } catch(e){}

            setTimeout(()=>{
                if(data.url=="reload()"){
                    top.location.reload();
                }else if(data.url=="reload_acciones()"){
                    $('#acciones_regla').load("{{ url('/events/acciones/') }}/"+data.id);
                } else {
                    window.open(data.url,'_self');
                }
            },1500)
        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            let html =error.message;
            console.log(error);
            $.each(error.errors, function(index, val) {
                 html += "- "+$(this)[0]+"<br>";
            });
            toast_error("Error:",html);
        })
        .always(function() {
            fin_espere();
            console.log("FORM complete");
            form.find('[type="submit"]').attr('disabled',false);
        });
    }


    function form_pdf_submit(event){
        https://qrclean.ddns.net:444/puestos/print_qrblock_espere("Generando ...");

        $.post($(this).attr('action'), $(this).serializeArray(), function(data, textStatus, xhr) {
            //block_espere("Generando PDF...");
            fin_espere();
            //toast_ok('Generacion de PDF','PDF Generado');
        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            console.log(error);

            toast_error("ERROR",error.message);
        })
        .always(function() {
            fin_espere();
            //console.log("complete");
            $(this).find('[type="submit"]').attr('disabled',false);
        });
    }

    function get_ajax(url,spin){
        console.log(url+" "+spin);
        if(spin!=null){
            $('#'+spin).show();
        }
        $.ajax({
            url: url
        })
        .done(function(data) {
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.warning){
                toast_warning(data.title,data.alert);
            } else{
                toast_ok(data.title,data.message);
            }
            $('.modal').modal('hide');
            setTimeout(()=>{
                if(data.url)
                    window.open(data.url,'_self');
                if(data.reload)
                    window.location.reload();
            },3000)
        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            let html = "";
            console.log(error);
            $.each(error.errors, function(index, val) {
                html += "- "+$(this)[0]+"<br>";
            });
            toast_error("Error",html);
        })
        .always(function() {
            fin_espere();
            console.log("complete");
            if(spin!=null){
                $('#'+spin).hide();
            }
        });
    }


    function ajax_filter(event) {
        block_espere("Obteniendo datos...");
        let form = $(this);
        let tipo = form.find('[name="document"]').val();

        if (tipo == 'pantalla') {
            event.preventDefault();
        }
        $('#action_orig').val($(this).attr('action'));

        $.post($(this).attr('action'), $(this).serializeArray(), function(data, textStatus, xhr) {
            block_espere("Renderizando datos...");

            if (tipo == 'pantalla') {
                //$('body').scrollTo('#myFilter');
                //$('#divfiltro').toggle();
                $('#myFilter').html(data);
            }
            fin_espere();

        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            console.log(error);

            toast_error("ERROR",error.message);
        })
        .always(function() {
            fin_espere();
            //console.log("complete");
            form.find('[type="submit"]').attr('disabled',false);
        });



    }

    //formularios de informes
    $('.ajax-filter').submit(function(event) {
        block_espere("Obteniendo datos...");
        let form = $(this);
        let tipo = form.find('[name="output"]').val();

        if (tipo == 'pantalla') {
            event.preventDefault();
        }
        $('#action_orig').val($(this).attr('action'));

        $.post($(this).attr('action'), $(this).serializeArray(), function(data, textStatus, xhr) {
            block_espere("Renderizando datos...");
            @if (checkPermissions(['Informes programados'],["W"]))
                if($('#div_programar_informe').length)
                {
                    $('#request_orig').val(request_orig);
                    $('#div_programar_informe').show();
                    animateCSS('#div_programar_informe','bounceInRight');
                }
            @endif
            if($('.btn_print').length)
            {
                $('.btn_print').show();
                animateCSS('.btn_print','zoomIn');
            }

            if (tipo == 'pantalla') {
                console.log(data);
                $('#myFilter').html(data);
                //$('#divfiltro').toggle();
            }
            fin_espere();

        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            console.log(error);

            toast_error("{{trans('strings.error')}}",error.message);
        })
        .always(function() {
            fin_espere();
            //console.log("complete");
            form.find('[type="submit"]').attr('disabled',false);
        });
    });

    $('.btn_print').click(function(){
        $('#myFilter').printThis({
            importCSS: true,
            importStyle: true,
            footer: "<img src='{{ url('/img/Mosaic_brand_20.png') }}' class='float-right'>"
        });
    })

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        console.log(document.cookie);
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) {
                console.log(name+': '+c.substring(nameEQ.length,c.length));
                return c.substring(nameEQ.length,c.length);
            }
        }
        return null;
    }

    function eraseCookie(name) {   
        document.cookie = name+'=; Max-Age=-99999999;';  
    }

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    function cargar_combo(combo,ruta,selected=0,fire=0){
        $.ajax({
                url: ruta
            })
            .done(function(data) {
                $('#'+combo).empty();
                $('#'+combo).html(data);
                if(selected!=0){
                    $('#'+combo).val(selected);
                    $('#'+combo).select2().trigger('change');
                }
                if(fire!=0){
                    $('#'+combo).select2().trigger('change');
                }
            })
            .fail(function(err) {
                console.log(err);
            });
    }

    function color_estado(estado){
        if(estado==1)
            return 'success';
        if(estado==2)
            return 'danger';
        if(estado==3)
            return 'info';
        if(estado==4)
            return 'gray';
        if(estado==5)
            return 'pink';
    }

    $(".select2").on("select2:select", function (evt) {
		var element = evt.params.data.element;
		var $element = $(element);
		$element.detach();
		$(this).append($element);
		$(this).trigger("change");
	});


    //Comprobacion de notificaciones
    notif=setInterval(() => {
        fetch('{{url('/check')}}')
        .then(response => response.json())
        .then(function(response){
            $('.cuenta_notificaciones').html(response.length);
        })
    }, 30000);


    //Conversion de colores
    function RGB2Color(r,g,b)
    {
        return '#' + this.byte2Hex(r) + this.byte2Hex(g) + this.byte2Hex(b);
    }
    function byte2Hex (n)
    {
        var nybHexString = "0123456789ABCDEF";
        return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);
    }

    function hexToRgb(h)
    {
        var r = parseInt((cutHex(h)).substring(0,2),16), g = ((cutHex(h)).substring(2,4),16), b = parseInt((cutHex(h)).substring(4,6),16)
        return r+''+b+''+b;
    }
    function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}


    //Boton de notificaicones en el header
    $('#btn_notif').click(function(){
        $('#div_notif').toggle();
        $('#lista_notif').load("{{ url('/notif/list') }}");
    });
     //Pseudo-random string generator para passwords
    function randomString(len, an) {
        an = an && an.toLowerCase();
        var str = "",
            i = 0,
            min = an == "a" ? 10 : 0,
            max = an == "n" ? 10 : 62;
        for (; i++ < len;) {
            var r = Math.random() * (max - min) + min << 0;
            str += String.fromCharCode(r += r > 9 ? r < 36 ? 55 : 61 : 48);
            }
        return str;
    }

    function recolocar_puestos(posiciones){
        $('.container').each(function(){
            plano=$(this);
            //console.log('plano: '+plano.height()+' '+plano.width());
            $.each(plano.data('posiciones'), function(i, item) {
                puesto=$('#puesto'+item.id);
                puesto.css('top',plano.height()*item.offsettop/100);
                puesto.css('left',(plano.width()*item.offsetleft/100));
                //las dimensiones del cuadradito
                if(puesto.data('width')!=0) {
                    puesto.css('width',plano.width()*(puesto.data('width')/100)+'px');
                    //console.log('w');
                } else {
                    puesto.css('width',plano.width()*(puesto.data('factorw')/100)+'px');
                }
                if(puesto.data('height')!=0) {
                    puesto.css('height',plano.height()*(puesto.data('height')/100)+'px');
                    //console.log('h');
                } else {
                    puesto.css('height',plano.height()*(puesto.data('factorh')/100)+'px');
                }
                // if(i==0){
                //     console.log(item);
                //     console.log(puesto.css('width'));
                //     console.log(puesto.css('height'));
                //     console.log(puesto.css('top'));
                //     console.log(puesto.css('left'));
                // }
                // puesto.css('width',plano.width()*(puesto.data('factorw')/100)+'px');
                // puesto.css('height',plano.height()*(puesto.data('factorh')/100)+'px');
                @mobile()
                    puesto.css('border-radius',puesto.data('factorr')*0.2+'px');
                @endmobile()
                //$('.viewport').html('w:'+plano.width()+' h:'+plano.height())+' vw:'+Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0)+' vh:'+Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
            });

        })
    }

    $('#boton_politica').click(function() {
        $('.offcanvas-title').html('Politica de privacidad');
        $('.body_politica').load("{{ url('/politica') }}");
    });

    $('#boton_terminos').click(function() {
        $('.offcanvas-title').html('Términos y condiciones');
        $('.body_politica').load("{{ url('/terminos') }}");
    });

    $('#boton_cookies').click(function() {
        $('.offcanvas-title').html('Política de cookies');
        $('.body_politica').load("{{ url('/cookies') }}");
    });

    function cerrar_modal(){
        $('.modal').modal('hide');
    }


    function scrollToTop() {
        window.scrollTo(0, 0);
    }

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    //Si tenemos el menu pequeño, para poner pequeño el icono de usuario
    if(root.classList.contains( "mn--min" )){
        document.getElementById( "main_user_image" ).classList.remove('img-md');
        document.getElementById( "main_user_image" ).classList.add('img-xs');
    }


    /* Initialize the Bootstrap's Popovers
    /* ---------------------------------------------- */
    const popoverTriggerList = [...document.querySelectorAll( '[data-bs-toggle="popover"]' )];
    const popoverList = popoverTriggerList.map( popoverTriggerEl => new bootstrap.Popover( popoverTriggerEl ));

    tooltipTriggerList = [...document.querySelectorAll( '.add-tooltip' )];
    tooltipList = tooltipTriggerList.map( tooltipTriggerEl => new bootstrap.Tooltip( tooltipTriggerEl ));

    const cal_formato_fecha="{{trans("general.date_format")}}";
    const cal_meses=["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"];
    const cal_diassemana=["{{trans('general.domingo')}}","{{trans('general.lunes')}}","{{trans('general.martes')}}","{{trans('general.miercoles')}}","{{trans('general.jueves')}}","{{trans('general.viernes')}}","{{trans('general.sabado')}}"];
    @if(session('perfil')!==null)
    const cal_dias_deshabilitados=[{{ session('perfil')->mca_reservar_sabados=='N'?'':'6,' }} {{ session('perfil')->mca_reservar_domingos=='N'?'':'0,' }}];
    @endif
</script>
