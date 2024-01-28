@foreach($notif as $n)
<div class="list-group-item list-group-item-action d-flex align-items-start mb-3">
    <div class="flex-shrink-0 me-3">
        <i class="{{ $n->img_notificacion }} fs-2" style="color:#{{ $n->val_color }}"></i>
    </div>
    <div class="flex-grow-1 ">
        <div class="d-flex justify-content-between align-items-start">
            <a href="{{ $n->url_notificacion }}" class="h6 mb-0 stretched-link text-decoration-none">{!! beauty_fecha($n->fec_notificacion) !!} - {{ $n->des_tipo_notificacion }}</a>
            @if($n->mca_leida=='N')<span class="badge bg-info rounded ms-auto">NEW</span>@endif
        </div>
        <small class="text-muted">{!! $n->txt_notificacion !!}</small>
    </div>
</div>
<script>
    $.get('{{ url('/notif/leida/') }}',function(data){
        $('.cuenta_notificaciones').html('');
        $('#badge_notificaciones').hide();
    });
</script>
@endforeach

<!-- List item -->
