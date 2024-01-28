
@php
    // use App\Classes\RandomColor;
    // dd(implode("','",RandomColor::many(40, array('hue'=>'green'))));
    $colores=['#FFFFFF','#7af26d','#59ea65','#8aeab5','#15eabc','#92efac','#9bf2e0','#c3ff84','#f1ff5e','#bed153','#b1ef45','#b3f99d','#bbdb48','#96f704','#47efd3','#1ae02e','#67e23b','#68e284','#dbf99a','#a1f4db','#adffeb','#4bb522','#64d83a','#a4fcb5','#3ee8cb','#54dd2e','#53ef45','#68f288','#34f941','#9cd662','#73ddbf','#72ff68','#ddef51','#67dba1','#ccf9a4','#00c181','#5fd8a2','#54a508','#63e209','#6de88b','#adea44'];
    $tz_offset=Carbon\Carbon::now()->setTimezone(Auth::user()->val_timezone)->getOffset()/3600;
@endphp

<div class="row">
    <div class="col-md-7">
        <table border="1">
            @foreach($fechas as $f)
                <tr>
                    <td class="text-nowrap p-1">{!! beauty_fecha($f) !!}</td>
                    @for($n=0;$n<24;$n++)
                        @php
                            
                            if($tz_offset>0){
                                $hora_ajustada=Carbon\Carbon::parse($f.' '.$n.':00:00')->subHours($tz_offset)->format('H');
                            } else {
                                $hora_ajustada=Carbon\Carbon::parse($f.' '.$n.':00:00')->addHours($tz_offset)->format('H');
                            }
                            $horas=$patron->where('fecha',$f)->where('hora',$hora_ajustada)->first();
                            if(isset($horas->cuenta)){
                                $index_color=round($horas->cuenta);
                                if($index_color>20){
                                    $index_color=20;
                                }
                            } else{
                                $index_color=0;
                            }
                            
                        @endphp
                        <td class="text-center td-hora p-1" data-id="{{$id}}" data-fecha="{{ $f }}" data-hora="{{ $hora_ajustada }}" style="font-size: 12px; background-color: {{ $colores[$index_color]   }}">{{  isset($horas->cuenta) ? $horas->cuenta : ''  }}</td>
                    @endfor

                </tr>
            @endforeach
                <tr style="font-size: 10px; background-color:bisque;">
                    <td></td>

            @for($n=0;$n<24;$n++)
                    <td class="text-center p-1" style="width:16px">{{$n<10 ? '0'.$n : $n}}</td>
            @endfor
                </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;">
            <div class="card" id="cardlog{{$id}}"  style="display: none;">
            <div class="spinner-border text-primay" id="spinlog{{$id}}" role="status" style="display: none;">
                    <span class="sr-only">Loading...</span>
                  </div>
                <div class="card-body" id="log{{$id}}" style="overflow: hidden">

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.td-hora').click(function(){
        animateCSS('#cardlog'+$(this).data('id'),'bounceInUp');
        $('#spinlog'+$(this).data('id')).show();
        $('#cardlog'+$(this).data('id')).show();

        $('#log'+$(this).data('id')).load("{{ url('/tasks/log_tarea/'.$id)  }}/"+$(this).data('fecha')+"/"+$(this).data('hora'), function(){
            $('#spinlog{{$id}}').hide();
        });

    });
    $('.slimScrollDiv').slimScroll({
		height: '460px',
        alwaysVisible: true,
        distance: '20px',
        railVisible: false,
        railColor: '#0287bb',
        railOpacity: 0.3,
        wheelStep: 10,
        allowPageScroll: false,
        disableFadeOut: false
	});
</script>
