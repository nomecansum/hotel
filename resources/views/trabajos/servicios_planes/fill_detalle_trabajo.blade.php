<div class="tab-base">

    <!-- Nav tabs -->
    <ul class="nav nav-callout justify-content-end" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#_dm-recursos" type="button" role="tab" aria-controls="recursos" aria-selected="false">Detalle</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#_dm-programacion" type="button" role="tab" aria-controls="programacion" aria-selected="false">Historial ({{ $historial->count() }})</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content">
        <div id="_dm-recursos" class="tab-pane fade active show" role="tabpanel" aria-labelledby="recursos-tab">
            @php
                $celda=App\Http\Controllers\TrabajosController::celda_plan_trabajos($datos,$datos,Carbon\Carbon::now(),$fecha);
                try{
                    $tiempo_empleado=Carbon\Carbon::parse($datos->fec_inicio)->diffforHumans(Carbon\Carbon::parse($datos->fec_fin));
                } catch (\Throwable $th) {
                    $tiempo_empleado='-';
                }
                
              //dump($datos);
            @endphp
            <div class="row">
                <div class="col-md-4">
                    <label class="font-bold w-100">Trabajo</label>
                    {{ $datos->des_trabajo }}
                </div>
                <div class="col-md-4">
                    <label class="font-bold w-100">Grupo</label>
                    {{ $datos->des_grupo }}
                </div>
                <div class="col-md-4">
                    <label class="font-bold w-100">Plan</label>
                    {{ $datos->des_plan }}
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 text-info">
                    <label class="font-bold w-100">Fecha Inicio</label>
                    {!! beauty_fecha($datos->fec_inicio) !!}<br>
                    {{ $datos->nom_operario_ini }}
                </div>
                <div class="col-md-4 text-success">
                    <label class="font-bold w-100">Fecha Fin</label>
                    {!! beauty_fecha($datos->fec_fin) !!}<br>
                    {{ $datos->nom_operario_fin}}
                </div>
                <div class="col-md-4">
                    <label class="font-bold w-100">Estado</label>
                   <div class=" p-2 rounded text-white {{ $celda['color'] }}"><i class="{{ $celda['icono'] }}"></i> {{ $celda['title'] }}</div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="font-bold w-100">Operarios</label>
                    <div class="row">
                        <div class="col-md-4">
                            Previstos: <i class="fa-solid fa-person-simple"></i> {{ $datos->operarios_previstos }}
                        </div>
                        <div class="col-md-4">
                            Asignados: <i class="fa-solid fa-person-simple"></i> {{ $datos->num_operarios+count(explode(",",$datos->list_operarios)) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="font-bold w-100">Tiempo</label>
                    <div class="row">
                        <div class="col-md-4">
                            Previsto: <i class="fa-regular fa-stopwatch"></i> {{ $datos->val_tiempo }}''
                        </div>
                        <div class="col-md-8">
                            Empleado: <i class="fa-regular fa-stopwatch"></i> {{ $tiempo_empleado }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="font-bold w-100">Comentarios</label>
                    {!! $datos->observaciones !!}
                </div>
            </div>
        </div>
        <div id="_dm-programacion" class="tab-pane fade" role="tabpanel" aria-labelledby="programacion-tab">
           <div class="row">
                <label class="font-bold w-100">Periodicidad</label>
                <div class="col-md-4">
                    <h5>{{ $datos->val_periodo }}</h5>
                </div>
                <div class="col-md-8" id="txt_periodicidad">
                    
                </div>
           </div>
           <div class="row mt-4">
            <label class="font-bold w-100">Ultimas ejecuciones (+/- 10 dias)</label>
            <div class="table-responsive">
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Programado</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historial as $h)
                        @php
                            $celda=App\Http\Controllers\TrabajosController::celda_plan_trabajos($h,$h,Carbon\Carbon::now(),$fecha);
                        @endphp
                            <tr>
                                <td>
                                    {!! beauty_fecha($h->fec_programada) !!}<br>
                                    {{ $h->nom_operario_ini }}
                                </td>
                                <td>
                                    {!! beauty_fecha($h->fec_inicio) !!}<br>
                                    {{ $h->nom_operario_ini }}
                                </td>
                                <td>
                                    {!! beauty_fecha($h->fec_fin) !!}<br>
                                    {{ $h->nom_operario_fin }}
                                </td>
                                <td>
                                    <div class="w-100 text-center rounded {{ $celda['color'] }}" title="{{ $celda['title'] }}">
                                        <i class="{{ $celda['icono']==''?"fa-solid fa-circle":$celda['icono'] }}"></i>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('/js/cron/cronstrue.min.js')}}" defer></script>
<script src="{{ asset('/js/cron/cronstrue-i18n.min.js')}}" defer></script>
<script>
    $(function(){
        $('#txt_periodicidad').html(cronstrue.toString("{{ $datos->val_periodo }}",{ use24HourTimeFormat: true,locale: "es" }));
    })
    
</script>
