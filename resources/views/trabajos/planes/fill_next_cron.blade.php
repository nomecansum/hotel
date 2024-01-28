<div class="row">
@foreach($cron as $c)
    <div class="col-md-4 text-start"><span class="text-info">[{{ $loop->index+1 }}]</span> {!! beauty_fecha($c) !!}</div>
@endforeach
</div>