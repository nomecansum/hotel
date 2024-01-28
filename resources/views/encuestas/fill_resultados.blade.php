<div class="card">
    <div class="card-header">
        <div class="form-group col-md-5" >
            <label>Fechas </label>
            <div class="input-group mb-3">
                <input type="text" class="form-control pull-left rangepicker" id="fechas" name="fechas" value="{{  Carbon\Carbon::parse($encuesta->fec_inicio)->format('d/m/Y').' - '.Carbon\Carbon::parse($encuesta->fec_fin)->format('d/m/Y') }}">
                <span class="btn input-group-text btn-secondary btn_fechas" ><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
            </div>
        </div>
    </div>


<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <h4>Resultados de la encuesta: {{ $encuesta->titulo }}</h4>
        </div>
    </div>
    
    <div id="chartdiv_puestos" style="width:100%; height:300px;  ml-0"></div>
    <div class="row">
        <div class="col-md-12">
            @switch($encuesta->id_tipo_encuesta)
                @case(1)
                    <i class="fas fa-tired  fa-2x text-danger valor" data-value="1"></i> 1
                    <i class="fas fa-frown  fa-2x text-warning valor" data-value="2"></i> 2
                    <i class="fas fa-meh-rolling-eyes  fa-2x text-primary valor" data-value="3"></i> 3
                    <i class="fas fa-smile  fa-2x text-secondary valor" data-value="4"></i> 4
                    <i class="fas fa-grin-alt fa-2x text-success valor" data-value="5"></i> 5
                    @break
                @case(2)
                    <i class="fas fa-frown  fa-2x text-danger valor" data-value="1"></i> 1
                    <i class="fas fa-meh  fa-2x text-warning valor" data-value="2"></i> 2
                    <i class="fas fa-smile fa-2x text-success valor" data-value="3"></i> 3
                    @break
                @case(3)
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est1" data-value="1"></i>
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est2" data-value="2"></i>
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est3" data-value="3"></i>
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est4" data-value="4"></i>
                    <i class="fal fa-star  valor" style="color: #ffd700; font-size: 2vw" id="est5" data-value="5"></i>
                    @break
            @endswitch
        </div>
    </div>
    <label class="mt-5">Votos por fecha</label>
    <div id="chartdiv" style="width:100%; height:300px;  ml-0"></div>

</div>
</div>


{{--  AMCharts  --}}
<script src="{{url('plugins')}}/amcharts4/core.js"></script>
<script src="{{url('plugins')}}/amcharts4/charts.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/material.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/animated.js"></script>
<script src="{{url('plugins')}}/amcharts4/themes/kelly.js"></script>
<script src="{{url('plugins')}}/amcharts4/lang/es_ES.js"></script>



<script>

    
    var rangepicker = new Litepicker({
        element: document.getElementById( "fechas" ),
        singleMode: false,
        @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
        @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
        autoApply: true,
        format: 'DD/MM/YYYY',
        lang: "es-ES",
        tooltipText: {
            one: "day",
            other: "days"
        },
        tooltipNumber: (totalDays) => {
            return totalDays - 1;
        },
        setup: (rangepicker) => {
            rangepicker.on('selected', (date1, date2) => {
                console.log('Refresh '+$('#fechas').val());
                $.post('{{url('/encuestas/resultados')}}', {_token:'{{csrf_token()}}',id_encuesta: {{ $encuesta->id_encuesta }},fechas: $('#fechas_resul').val()}, function(data, textStatus, xhr) {
                    $('#body_resultados').html(data);
                });
            });
        }
    });

    $('.btn_fechas').click(function(){
        rangepicker.show();
    })

    am4core.ready(function() {
        // Themes begin
        am4core.useTheme(am4themes_kelly);
        am4core.useTheme(am4themes_animated);

        // Themes end
        // Create chart instance
        var chartpuestos = am4core.create("chartdiv_puestos", am4charts.PieChart);

        chartpuestos.legend = new am4charts.Legend();
        
        // Add data
        chartpuestos.data = {!! json_encode($resultado_valor) !!};
        
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


    // Create chart instance
    var chart = am4core.create("chartdiv", am4charts.XYChart);
    chart.addClassNames= true;

    var scrollbarX = new am4core.Scrollbar();
    chart.scrollbarX = scrollbarX;

    chart.language.locale = am4lang_es_ES;

    // Add data
    chart.data = [
        @foreach($resultado_fecha as $g)
            {
            "Fecha": "{{ Carbon\Carbon::parse($g->fecha)->format('Y-m-d') }}",
            "Cuenta": "{{ $g->cuenta }}"
            },
        @endforeach
        ];

    // Create axes
    var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
    dateAxis.renderer.grid.template.location = 0;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

    // Create series
    function createSeries(field, name) {
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = field;
        series.dataFields.dateX = "Fecha";
        series.name = name;
        series.tooltipText = "{dateX}: {name} -> [b]{valueY}[/]";
        series.strokeWidth = 4;
        series.tensionX = 0.85;

        var bullet = series.bullets.push(new am4charts.CircleBullet());
        bullet.circle.stroke = am4core.color("#fff");
        bullet.circle.strokeWidth = 2;

        return series;
    }

    var series1 = createSeries("Cuenta", "Votos");

    chart.legend = new am4charts.Legend();
    chart.cursor = new am4charts.XYCursor();

</script>