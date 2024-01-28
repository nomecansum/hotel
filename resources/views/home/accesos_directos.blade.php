<div class="row mt-3">
    @if(checkPermissions(['Scan acceso'],['R']))
    <div class="col-md-6 text-center mb-2">
        <a class="btn btn-lg btn-primary fs-2 rounded" href="{{ url('/scan_usuario/') }} "><i class="fad fa-qrcode "></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Scan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
    </div>
    @endif
    @if(checkPermissions(['Reservas'],['R']))
    <div class="col-md-6 text-center mb-2 ">
        <a class="btn btn-lg btn-info fs-2 rounded" href="{{ url('/reservas/') }} "><i class="fad fa-calendar-alt "></i> Reservas</a>
    </div>
    @endif
</div>