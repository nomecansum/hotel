@php
    use App\Models\clientes;
    use Carbon\Carbon;
    if(!isset($cliente)){
        if (!isAdmin()) {
            $cliente=Auth::user()->id_cliente;
        } else {
            $cliente=session('CL')['id_cliente'];
        }

    }
    $cl = clientes::find($cliente);
@endphp
@if($r->output=="excel")
            {{ $cl->nom_cliente }}
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|      
            @isset($nombre_informe)
            {{$nombre_informe}}
            @endisset
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|      
            {{ Carbon::now()->locale('es_ES')->isoFormat('lll')  }}


@elseif($r->output=="pdf")
    <table style="width:100%">
        <tr>
            <td class="text-center">
                @if($r->output!=="excel")<img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$cl->img_logo) }}" class="rounded" style="width: 180px; margin-top: 20px" alt="" onerror="this.src='{{ url('/img/logo.png') }}';">@endif<br>
                {{ $cl->nom_cliente }}
            </td>
            <td class="text-center">
                @isset($nombre_informe)
                <span style="font-size: 40px">{{$nombre_informe}}</span>
                @endisset
            </td>
            <!--td style="text-align: right">
                {{ Carbon::now()->locale('es_ES')->isoFormat('lll')  }}
            </td-->
        </tr>
    </table>
@else
    <div class="row" style="color: #333">
        <div class="col-md-3 text-center">
            <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$cl->img_logo) }}" class="rounded"  style="width: 120px; margin-top: 20px" alt="" onerror="this.src='{{ url('/img/logo.png') }}';"><br>
            {{ $cl->nom_cliente }}
        </div>
        <div class="col-md-6 text-center vertical-middle">
            @isset($nombre_informe)
            <h1>{{$nombre_informe}}</h1>
            @endisset
        </div>
        <div class="col-md-3 mt-4  text-end vertical-middle">
            {{ Carbon::now()->timezone(Auth::user() ? Auth::user()->val_timezone : "Europe/Madrid")->isoFormat('lll')  }}
        </div>
    </div>
@endif