@php
    $datos_quesito=DB::table('estados_puestos')
        ->join('puestos','puestos.id_estado','estados_puestos.id_estado')
        ->selectraw('des_estado, count(cod_puesto) as cuenta')
        ->groupby('des_estado')
        ->get();
   
@endphp


<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Estado {!! beauty_fecha(Carbon\Carbon::now()->Settimezone(Auth::user()->val_timezone)) !!}</h3>
    </div>
    <div class="card-body">
        <div id="chartdiv_puestos" style="width:100%; height:300px;  ml-0"></div>

    </div>
</div>

@section('scripts2')
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
        var chartpuestos = am4core.create("chartdiv_puestos", am4charts.PieChart);

        chartpuestos.legend = new am4charts.Legend();
        
        // Add data
        chartpuestos.data = {!! json_encode($datos_quesito) !!};
        
        // Add and configure Series
        var pieSeries = chartpuestos.series.push(new am4charts.PieSeries());
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