<div class="table-responsive rounded">
    <table class="table" style="width:50%">
        @if($jobs->count()>0 || $failed->count()>0)
            <thead class="bg-secondary text-white  font-14">
                <th class=" pt-1 pb-1">id</th>
                <th  class=" pt-1 pb-1">payload</th>
                <th  class=" pt-1 pb-1 text-center">intentos</th>
                <th  class=" pt-1 pb-1 text-center">creado</th>
                <th  class=" pt-1 pb-1 text-center">ejeutando</th>
            </thead>
        @endif
        @foreach($jobs as $j)
            @php
                $payload=json_decode($j->payload);
                $id_informe=unserialize($payload->data->command)->id_informe;
                $command=$payload->data->commandName;
            @endphp
            <tr style="font-size: 12px">
                <td class="text-center" style="width: 30px">{{$j->id}}</td>
                <td>{{ $command }} [{{ $id_informe }}] <b class="text-primary font-weight-bold">{{ strpos($command,"GeneraInforme")&&isset($id_informe)?DB::table('cug_informes_programados')->where('cod_informe_programado',$id_informe)->first()->des_informe_programado :'' }}</b> </td>
                <td class="text-center" style="width: 30px">{{$j->attempts}}</td>
                <td class="text-center"  style="width: 100px">{!! beauty_fecha(Carbon\Carbon::createFromTimestamp($j->created_at)) !!}</td>
                <td class="text-center"  style="width: 100px">{!! isset($j->reserved_at) ? beauty_fecha(Carbon\Carbon::createFromTimestamp($j->reserved_at)) : '' !!}</td>
            </tr>
        @endforeach
        @foreach($failed as $j)
            @php
                $payload=json_decode($j->payload);
                $id_informe=unserialize($payload->data->command)->id_informe;
                $command=$payload->data->commandName;
            @endphp
            <tr style="font-size: 12px" class="bg-warning">
                <td class="text-center" style="width: 30px">{{$j->id}}</td>
                <td>{{ $command }} [{{ $id_informe }}] <b class="text-primary font-weight-bold">{{ strpos($command,"GeneraInforme")?DB::table('cug_informes_programados')->where('cod_informe_programado',$id_informe)->first()->des_informe_programado :'' }}</b> </td>
                <td class="text-center" style="width: 30px"></td>
                <td class="text-center"  style="width: 100px"></td>
                <td class="text-center"  style="width: 100px">{!! beauty_fecha(Carbon\Carbon::parse($j->failed_at)) !!}</td>
            </tr>
        @endforeach
    </table>
</div>