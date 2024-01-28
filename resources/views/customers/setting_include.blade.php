
<div class="row">
    <div class="col-md-3 p-4 bg-light">

        <h5 class="fw-bold pb-3 mb-2">Menu</h5>
    
        {{-- <!-- OPTION : Sticky Navigation -->
        <h6 class="mb-2 pb-1">Navegacion</h6>
        <div class="d-flex align-items-center pt-1 mb-2">
            <label class="form-check-label flex-fill" for="_dm-stickyNavCheckbox">Scroll</label>
            <div class="form-check form-switch">
                <input id="_dm-stickyNavCheckbox_cus" class="form-check-input ms-0" type="checkbox" autocomplete="off" data-value="mn--sticky" data-control="_dm-stickyNavCheckbox">
            </div>
        </div> --}}
    
        <h6 class="mb-2 pb-1 pt-3">Apariencia</h6>
        <!-- OPTION : Mini navigation mode -->
        <div class="d-flex align-items-center pt-1 mb-2">
            <label class="form-check-label flex-fill" for="_dm-miniNavRadio">Minimizado</label>
            <div class="form-check form-switch">
                <input id="_dm-miniNavRadio_cus" class="form-check-input ms-0 chk_menu" type="radio" name="navigation-mode" autocomplete="off"  data-value="mn--min" data-control="_dm-miniNavRadio">
            </div>
        </div>
    
        <!-- OPTION : Maxi navigation mode -->
        <div class="d-flex align-items-center pt-1 mb-2">
            <label class="form-check-label flex-fill" for="_dm-maxiNavRadio">Maximizado</label>
            <div class="form-check form-switch">
                <input id="_dm-maxiNavRadio_cus" class="form-check-input ms-0 chk_menu" type="radio" name="navigation-mode" autocomplete="off"  data-value="mn--max" data-control="_dm-maxiNavRadio">
            </div>
        </div>
    
        <!-- OPTION : Push navigation mode -->
        <div class="d-flex align-items-center pt-1 mb-2">
            <label class="form-check-label flex-fill" for="_dm-pushNavRadio">Push Mode</label>
            <div class="form-check form-switch">
                <input id="_dm-pushNavRadio_cus" class="form-check-input ms-0 chk_menu" type="radio" name="navigation-mode" autocomplete="off" data-value="mn--push" data-control="_dm-pushNavRadio">
            </div>
        </div>
    
        <!-- OPTION : Slide on top navigation mode -->
        <div class="d-flex align-items-center pt-1 mb-2">
            <label class="form-check-label flex-fill" for="_dm-slideNavRadio">Superponer</label>
            <div class="form-check form-switch">
                <input id="_dm-slideNavRadio_cus" class="form-check-input ms-0 chk_menu" type="radio" name="navigation-mode" autocomplete="off" data-value="mn--slide" data-control="_dm-slideNavRadio">
            </div>
        </div>
    
        <!-- OPTION : Slide on top navigation mode -->
        <div class="d-flex align-items-center pt-1 mb-2">
            <label class="form-check-label flex-fill" for="_dm-revealNavRadio">Desplazar</label>
            <div class="form-check form-switch">
                <input id="_dm-revealNavRadio_cus" class="form-check-input ms-0 chk_menu" type="radio" name="navigation-mode" autocomplete="off" data-value="mn--reveal" data-control="_dm-revealNavRadio">
            </div>
        </div>
    
    </div>
    <div class="col-md-8 p-4">
        <h5 class="fw-bold pb-3 mb-2">Esquema de color</h5>
    
        <div class="row mb-3 pb-3">
            <div class="col-md-6">
    
                <div class="d-flex align-items-start position-relative">
                    <div class="flex-shrink-0 me-3">
                        <div class="_dm-color-box bg-light"></div>
                    </div>
                    <div class="flex-grow-1 div_light div_eesquema" >
                        <a href="#" data-dir="light" data-single="true" class="_dm-themeColors esquema schemes-btn h6 d-block mb-0 stretched-link text-decoration-none" data-esquema="">Claro</a>
                        <small class="text-muted">Tema completamente claro.</small>
                    </div>
                </div>
    
            </div>
            <div class="col-md-6">
    
                <div class="d-flex align-items-start position-relative">
                    <div class="flex-shrink-0 me-3">
                        <div class="_dm-color-box bg-dark"></div>
                    </div>
                    <div class="flex-grow-1 div_dark div_eesquema">
                        <a href="#" data-dir="dark" data-hd="expanded" class="_dm-themeColors esquema schemes-btn h6 d-block mb-0 stretched-link text-decoration-none" data-esquema="/color-schemes/dark">Oscuro</a>
                        <small class="text-muted">Tema completamente oscuro.</small>
                    </div>
                </div>
    
            </div>
        </div>
    
        <div class="row text-center my-3">
    
            <!-- Expanded Header -->
            <div class="col-md-4">
                <h6 class="m-0">Cabecera expandida</h6>
                <div class="_dm-colorShcemesMode">
    
                    <!-- Scheme Button -->
                    <button type="button" class="btn shadow-none">
                        <img src="./assets/img/color-schemes/expanded-header.png" alt="color scheme illusttration" loading="lazy">
                    </button>
    
                    <!-- Scheme Colors -->
                    <div class="_dm-colorSchemesMode__colors">
                        <div class="d-flex flex-wrap justify-content-center">
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-gray" type="button" data-dir="all-headers/gray" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-navy" type="button" data-dir="" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-ocean" type="button" data-dir="all-headers/ocean" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-lime" type="button" data-dir="all-headers/lime" data-hd="expanded"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-violet" type="button" data-dir="all-headers/violet" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-orange" type="button" data-dir="all-headers/orange" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-teal" type="button" data-dir="all-headers/teal" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-corn" type="button" data-dir="all-headers/corn" data-hd="expanded"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-cherry" type="button" data-dir="all-headers/cherry" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-coffee" type="button" data-dir="all-headers/coffee" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-pear" type="button" data-dir="all-headers/pear" data-hd="expanded"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-night" type="button" data-dir="all-headers/night" data-hd="expanded"></button>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Fair Header -->
            <div class="col-md-4">
                <h6 class="m-0">Cabecera minima</h6>
                <div class="_dm-colorShcemesMode">
    
                    <!-- Scheme Button -->
                    <button type="button" class="btn shadow-none">
                        <img src="./assets/img/color-schemes/fair-header.png" alt="color scheme illusttration" loading="lazy">
                    </button>
    
                    <!-- Scheme Colors -->
                    <div class="_dm-colorSchemesMode__colors">
                        <div class="d-flex flex-wrap justify-content-center">
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-gray" type="button" data-dir="all-headers/gray" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-navy" type="button" data-dir="" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-ocean" type="button" data-dir="all-headers/ocean" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-lime" type="button" data-dir="all-headers/lime" data-hd="fair"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-violet" type="button" data-dir="all-headers/violet" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-orange" type="button" data-dir="all-headers/orange" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-teal" type="button" data-dir="all-headers/teal" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-corn" type="button" data-dir="all-headers/corn" data-hd="fair"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-cherry" type="button" data-dir="all-headers/cherry" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-coffee" type="button" data-dir="all-headers/coffee" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-pear" type="button" data-dir="all-headers/pear" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-night" type="button" data-dir="all-headers/night" data-hd="fair"></button>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-4">
                <h6 class="m-0">Cabecera completa</h6>
    
                <div class="_dm-colorShcemesMode">
    
                    <!-- Scheme Button -->
                    <button type="button" class="btn shadow-none">
                        <img src="./assets/img/color-schemes/full-header.png" alt="color scheme illusttration" loading="lazy">
                    </button>
    
                    <!-- Scheme Colors -->
                    <div class="_dm-colorSchemesMode__colors">
                        <div class="d-flex flex-wrap justify-content-center">
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-gray" type="button" data-dir="all-headers/gray"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-navy" type="button" data-dir=""></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-ocean" type="button" data-dir="all-headers/ocean"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-lime" type="button" data-dir="all-headers/lime"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-violet" type="button" data-dir="all-headers/violet"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-orange" type="button" data-dir="all-headers/orange"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-teal" type="button" data-dir="all-headers/teal"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-corn" type="button" data-dir="all-headers/corn"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-cherry" type="button" data-dir="all-headers/cherry"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-coffee" type="button" data-dir="all-headers/coffee"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-pear" type="button" data-dir="all-headers/pear"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-night" type="button" data-dir="all-headers/night"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="row text-center mb-3">
            <div class="col-md-4">
                <h6 class="m-0">Navegacion primaria</h6>
    
                <div class="_dm-colorShcemesMode">
    
                    <!-- Scheme Button -->
                    <button type="button" class="btn shadow-none">
                        <img src="./assets/img/color-schemes/navigation.png" alt="color scheme illusttration" loading="lazy">
                    </button>
    
                    <!-- Scheme Colors -->
                    <div class="_dm-colorSchemesMode__colors">
                        <div class="d-flex flex-wrap justify-content-center">
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-gray" type="button" data-dir="primary-nav/gray" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-navy" type="button" data-dir="primary-nav/navy" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-ocean" type="button" data-dir="primary-nav/ocean" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-lime" type="button" data-dir="primary-nav/lime" data-hd="fair"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-violet" type="button" data-dir="primary-nav/violet" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-orange" type="button" data-dir="primary-nav/orange" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-teal" type="button" data-dir="primary-nav/teal" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-corn" type="button" data-dir="primary-nav/corn" data-hd="fair"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-cherry" type="button" data-dir="primary-nav/cherry" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-coffee" type="button" data-dir="primary-nav/coffee" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-pear" type="button" data-dir="primary-nav/pear" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-night" type="button" data-dir="primary-nav/night" data-hd="fair"></button>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-4">
                <h6 class="m-0">Marca</h6>
    
                <div class="_dm-colorShcemesMode">
    
                    <!-- Scheme Button -->
                    <button type="button" class="btn shadow-none">
                        <img src="./assets/img/color-schemes/brand.png" alt="color scheme illusttration" loading="lazy">
                    </button>
    
                    <!-- Scheme Colors -->
                    <div class="_dm-colorSchemesMode__colors">
                        <div class="d-flex flex-wrap justify-content-center">
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-gray" type="button" data-dir="brand/gray" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-navy" type="button" data-dir="brand/navy" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-ocean" type="button" data-dir="brand/ocean" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-lime" type="button" data-dir="brand/lime" data-hd="fair"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-violet" type="button" data-dir="brand/violet" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-orange" type="button" data-dir="brand/orange" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-teal" type="button" data-dir="brand/teal" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-corn" type="button" data-dir="brand/corn" data-hd="fair"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-cherry" type="button" data-dir="brand/cherry" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-coffee" type="button" data-dir="brand/coffee" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-pear" type="button" data-dir="brand/pear" data-hd="fair"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-night" type="button" data-dir="brand/night" data-hd="fair"></button>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-md-4">
                <h6 class="m-0">Cabecera grande</h6>
                <div class="_dm-colorShcemesMode">
    
                    <!-- Scheme Button -->
                    <button type="button" class="btn shadow-none">
                        <img src="./assets/img/color-schemes/tall-header.png" alt="color scheme illusttration" loading="lazy">
                    </button>
    
                    <!-- Scheme Colors -->
                    <div class="_dm-colorSchemesMode__colors">
                        <div class="d-flex flex-wrap justify-content-center">
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-gray" type="button" data-dir="all-headers/gray" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-navy" type="button" data-dir="" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-ocean" type="button" data-dir="all-headers/ocean" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-lime" type="button" data-dir="all-headers/lime" data-hd="fair,expanded,border"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-violet" type="button" data-dir="all-headers/violet" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-orange" type="button" data-dir="all-headers/orange" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-teal" type="button" data-dir="all-headers/teal" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-corn" type="button" data-dir="all-headers/corn" data-hd="fair,expanded,border"></button>
    
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-cherry" type="button" data-dir="all-headers/cherry" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-coffee" type="button" data-dir="all-headers/coffee" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-pear" type="button" data-dir="all-headers/pear" data-hd="fair,expanded,border"></button>
                            <button class="_dm-themeColors _dm-box-xs _dm-bg-night" type="button" data-dir="all-headers/night" data-hd="fair,expanded,border"></button>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>
    </div>
</div>

@php
    $colores=json_decode($config->theme_name??null);

@endphp


<script>
    $('.chk_menu').click(function() {
        if ($(this).is(':checked')) {
            $('#menu').val($(this).data('value'));
        } 
        //$('#'+$(this).data('control')).trigger('click');
    });
    $('.esquema').click(function() {
        $('#tema').val("/color-schemes/"+$(this).data('dir'));
        $('#esquema').val($(this).data('esquema'));
        $(this).toggle('active');
        console.log($('.div_'+$(this).data('dir')));
        $('.div_eesquema').not(this).removeClass('btn btn-outline-primary');
        $('.div_'+$(this).data('dir')).addClass('btn btn-outline-primary');
    });

    $('._dm-box-xs').click(function() {
        $('._dm-box-xs').removeClass('active');
        $('#tema').val("/color-schemes/"+$(this).data('dir'));
        $('#rootClass').val($(this).data('hd'));
        $(this).addClass('active');
    });

    $('[data-value="{{ $colores->menu??'' }}"]').prop('checked', true);
    $('[data-dir="{{ str_replace('/color-schemes/','',$colores->tema??'') }}"][data-hd="{{ $colores->rootClass??'' }}"]').addClass('active');
   
</script>
