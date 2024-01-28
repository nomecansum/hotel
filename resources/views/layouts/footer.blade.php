<div class="content__wrap py-3 py-md-1 border-top d-flex flex-column flex-md-row align-items-md-center">
    <div class="text-nowrap mb-4 mb-md-0">Copyright &copy; 2022 <a href="{{ config('app.url') }}" class="ms-1 btn-link fw-bold">{{ config('app.name') }}</a> {{ config('app.env') }}</div>
    
    @desktop
    <nav class="nav flex-column gap-1 flex-md-row gap-md-3 ms-md-auto me-5" style="row-gap: 0 !important;">
        <a class="nav-link px-0 _dm-offcanvasBtn" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" id="boton_politica" value="offcanvas-bottom">Privacidad</a>
        <a class="nav-link px-0 _dm-offcanvasBtn" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" id="boton_cookies" value="offcanvas-bottom">Cookies</a>
        <a class="nav-link px-0 _dm-offcanvasBtn" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" id="boton_terminos" value="offcanvas-bottom">Aviso legal</a>
        <a class="nav-link px-0 _dm-offcanvasBtn" href="{{ config('app.link_contacto') }}" target="_blank"  rel="noopener noreferrer">Contacto</a>
    </nav>
    @elsedesktop
    <div class="d-inline-flex p-2">
        <a class="nav-link px-0 _dm-offcanvasBtn mr-3" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" id="boton_politica" value="offcanvas-bottom">Privacidad</a>
        <a class="nav-link px-0 _dm-offcanvasBtn mr-3" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" id="boton_cookies" value="offcanvas-bottom">Cookies</a>
        <a class="nav-link px-0 _dm-offcanvasBtn mr-3" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" id="boton_terminos" value="offcanvas-bottom">Aviso legal</a>
        <a class="nav-link px-0 _dm-offcanvasBtn mr-3" href="{{ config('app.link_contacto') }}" target="_blank"  rel="noopener noreferrer">Contacto</a>
    </div>
    @enddesktop
</div>

