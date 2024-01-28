@php
 $incidencias=DB::table('incidencias')
    ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
    ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
    ->join('edificios','puestos.id_edificio','edificios.id_edificio')
    ->join('plantas','puestos.id_planta','plantas.id_planta')
    ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
    ->join('clientes','puestos.id_cliente','clientes.id_cliente')
    ->where(function($q){
        if (!isAdmin()) {
            $q->where('puestos.id_cliente',Auth::user()->id_cliente);
        } else {
            $q->where('puestos.id_cliente',session('CL')['id_cliente']);
        }
    })
    ->wherenull('fec_cierre')
    ->get();

    $datos_quesito=DB::table('incidencias')
        ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
        ->selectraw('des_tipo_incidencia as des_estado, count(id_incidencia) as cuenta')
        ->groupby('des_tipo_incidencia')
        ->get();
        
@endphp
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><span class="font-bold fs-2">{{ $incidencias->count() }}</span> Incidencias abiertas a {!! beauty_fecha(Carbon\Carbon::now()->Settimezone(Auth::user()->val_timezone)) !!}</h3>
    </div>
    <div class="card-body">
        <div id="chartdiv_incidencias" style="width:100%; height:400px;  ml-0"></div>

    </div>
</div>


@section('scripts3')
    {{--  AMCharts  --}}
    <script src="{{url('plugins')}}/amcharts4/core.js"></script>
    <script src="{{url('plugins')}}/amcharts4/charts.js"></script>
    <script src="{{url('plugins')}}/amcharts4/themes/material.js"></script>
    <script src="{{url('plugins')}}/amcharts4/themes/animated.js"></script>
    <script src="{{url('plugins')}}/amcharts4/themes/kelly.js"></script>
    <script src="{{url('plugins')}}/amcharts4/lang/es_ES.js"></script>

    <script src="/js/demo/nifty-demo.min.js"></script>

<script>
    am4core.ready(function() {
    // Themes begin
    am4core.useTheme(am4themes_animated);
    am4core.useTheme(am4themes_kelly);

    // Themes end
    // Create chart instance
    var chart_inci = am4core.create("chartdiv_incidencias", am4charts.PieChart);

    chart_inci.legend = new am4charts.Legend();
    chart_inci.legend.maxHeight = 150;
    chart_inci.legend.scrollable = true;
    
    // Add data
    chart_inci.data = {!! json_encode($datos_quesito) !!};
    
    // Add and configure Series
    var pieSeries = chart_inci.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "cuenta";
    pieSeries.dataFields.category = "des_estado";
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.slices.template.strokeWidth = 2;
    pieSeries.slices.template.strokeOpacity = 1;
    
    // This creates initial animation
    pieSeries.hiddenState.properties.opacity = 1;
    pieSeries.hiddenState.properties.endAngle = -90;
    pieSeries.hiddenState.properties.startAngle = -90;
    
    }); // end am4core.ready()
</script>
@endsection