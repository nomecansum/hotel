<div class="row b-all rounded mb-2">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Periodo</th>
                        <th>Tipo</th>
                        <th>Sala</th>
                        <th>Puesto</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($misreservas as $res)
                    <tr>
                        <td>{{ $res->id_reserva }}</td>
                        <td>{!! beauty_fecha ($res->fec_reserva) !!} @if($res->fec_fin_reserva!=null)<i class="fas fa-arrow-right"></i> {{ Carbon\Carbon::parse($res->fec_fin_reserva)->format('H:i') }}@endif</td>
                        <td style="color: {{ $res->val_color }}"><i class="{{ $res->val_icono }}"></i> {{ $res->des_tipo_puesto }}</td>
                        <td>{{ nombrepuesto($res) }}</td>
                        <td>{{ $res->cod_puesto }}</td>
                        <td><a href="javascript:void(0)" class="btn_del text-danger add-tooltip" title="Cancelar reserva" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-des_puesto="{{ $res->cod_puesto }}"><i class="fad fa-trash-alt"></i></a>
                            {{--  <a href="#planta{{ $res->id_planta }}" class="btn_ver text-info add-tooltip" title="Ver puesto en plano/mapa" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-puesto="{{ $res->id_puesto }}"><i class="fad fa-search-location"></i></a>  --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 

<script>
    if (window.jQuery) {  
        $('.btn_del').click(borrar);
    }


</script>