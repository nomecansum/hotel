@if($mostrar_graficos==1)
@notmobile
<tr>
    <td colspan="8">
        <div id="chartdiv" style="width:100%; height:300px;  ml-0"></div>
    </td>
</tr>
@endnotmobile
@endif

@foreach ($incidencias as $inc)
    @php
        $descripcion="";
        if(isset($inc->txt_incidencia) && $inc->txt_incidencia!=''){
            $descripcion=substr($inc->txt_incidencia,0,50);
        }
        if(isset($inc->des_incidencia) && $inc->des_incidencia!=''){
            $descripcion=substr($inc->des_incidencia,0,50);
        }
    @endphp
    <tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
        <td>{{$inc->id_incidencia}}</td>
        <td class="text-center d-flex"><i class="{{ $inc->val_icono }} fa-2x" style="color:{{ $inc->val_color }}"></i>
            <span class="rounded ml-3"  style="padding: 3px; width:100%: height: 100%; background-color: {{ $inc->val_color  }}; {{ txt_blanco($inc->val_color=='text-white')?'color: #fff':'color:#222' }}">
                {{$inc->des_tipo_incidencia}}
            </span>
        </td>
        <td>{{ nombrepuesto($inc) }}</td>
        <td>{{ $inc->des_edificio}}</td>
        <td>{{ $inc->des_planta}}</td>
        <td>{!! beauty_fecha($inc->fec_apertura)!!}</td>
        <td>@if(isset($inc->fec_cierre)) <div class="bg-success text-xs text-white text-center rounded b-all" style="padding: 5px" id="cell{{$inc->id_incidencia}}">Cerrada</div> @else  <div class="bg-pink  text-xs text-white text-center rounded b-all"  style="padding: 5px" id="cell{{$inc->id_incidencia}}">Abierta </div>@endif</td>  
        
        <td style="position: relative; vertical-align: middle" class="pt-2">
            {{ $descripcion}}
            <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                <div class="btn-group btn-group pull-right ml-1" role="group">
                    @if (checkPermissions(['Incidencias'],["W"]))<a href="#" title="Ver incidencia " data-id="{{ $inc->id_incidencia }}" class="btn btn-xs btn-info add-tooltip btn_edit" onclick="edit({{ $inc->id_incidencia }})"><span class="fa fa-eye pt-1" aria-hidden="true"></span> Ver</a>@endif
                    @if (!isset($inc->fec_cierre) && checkPermissions(['Incidencias > Accion'],["W"]))<a href="#accion-incidencia" title="Acciones incidencia" data-toggle="modal" class="btn btn-xs btn-warning add-tooltip btn-accion" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-accion{{ $inc->id_incidencia }}" onclick="accion_incidencia({{ $inc->id_incidencia}})"><span class="fad fa-plus pt-1" aria-hidden="true"></span> Accion</a>@endif
                    @if (!isset($inc->fec_cierre) && checkPermissions(['Incidencias > Cerrar'],["W"]))<a href="#cerrar-incidencia" title="Cerrar incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-cierre" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-cierre{{ $inc->id_incidencia }}" onclick="cierre_incidencia({{ $inc->id_incidencia}})"><span class="fad fa-thumbs-up pt-1" aria-hidden="true"></span> Cerrar</a>@endif
                    @if (isset($inc->fec_cierre) && checkPermissions(['Incidencias > Reabrir'],["W"]))<a href="#reabrir-incidencia" title="Reabrir incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-reabrir" data-desc="{{ $inc->des_incidencia}}" data-id="{{ $inc->id_incidencia}}" id="boton-reabrir{{ $inc->id_incidencia }}" onclick="reabrir_incidencia({{ $inc->id_incidencia}})"><i class="fad fa-external-link-square-alt"></i> Reabrir</a>@endif
                    @if (checkPermissions(['Incidencias'],["D"]))<a href="#eliminar-incidencia-{{$inc->id_incidencia}}" title="Borrar incidencia" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip " onclick="$('#eliminar-incidencia-{{$inc->id_incidencia}}').modal('show')"><span class="fa fa-trash pt-1" aria-hidden="true"></span> Del</a>@endif
                    {{--  @if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-Cliente-{{$inc->id_incidencia}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif  --}}
                </div>
            </div>
            @if (checkPermissions(['Incidencias'],["D"]))
                <div class="modal fade" id="eliminar-incidencia-{{$inc->id_incidencia}}">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                            <div class="modal-header"><i class="fa-solid fa-circle-question text-warning fa-3x"></i>
                                ¿Borrar incidencia {{ $descripcion}}?
                            </div>
                            
                            <div class="modal-footer">
                                <a class="btn btn-info" href="{{url('/incidencias/delete',$inc->id_incidencia)}}">Si</a>
                                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()" onclick="$('.modal').modal('hide')">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </td>
    </tr>
    
@endforeach

@if($mostrar_graficos==1)
@notmobile
<tr>
    <td colspan="8">
        @php
        $datos=$incidencias->groupBy('des_tipo_incidencia')->map->count();
        $datos_quesito = [];
        foreach ($datos as $key => $value) {
            $obj=new stdClass();
            $obj->des_estado = $key;
            $obj->cuenta = $value;
            $datos_quesito[]=$obj;
        }   
        //dd($incidencias); 
    @endphp
    <div class="card">
        <div class="card-header">
            <h3 class="card-title font-bold"><span class="font-bold fs-2">{{ $incidencias->count() }}</span> Incidencias</h3>
        </div>
        <div class="card-body">
            Tipo de incidencia
            <div id="chartdiv_incidencias" style="width:100%; height:400px;  ml-0"></div>
    
        </div>
    </div>
   
    

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


    </td>
</tr>

<tr>
    <td colspan="8">
        @php
            $datos=$incidencias->groupBy('des_estado')->map->count();
            $datos_quesito = [];
            foreach ($datos as $key => $value) {
                $obj=new stdClass();
                if($key!=''){
                    $obj->des_estado = $key;
                } else{
                    $obj->des_estado = "Sin estado";
                }
                
                $obj->cuenta = $value;
                $datos_quesito[]=$obj;
            }    

            //dd($datos_quesito);
        @endphp
        <div class="card">
            <div class="card-body">
                Estado de incidencia
                <div id="chartdiv_incidencias2" style="width:100%; height:300px;  ml-0"></div>
        
            </div>
        </div>

        <script>
            am4core.ready(function() {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            am4core.useTheme(am4themes_kelly);
        
            // Themes end
            // Create chart instance
            var chart_inci2 = am4core.create("chartdiv_incidencias2", am4charts.PieChart);
        
            chart_inci2.legend = new am4charts.Legend();
            
            // Add data
            chart_inci2.data = {!! json_encode($datos_quesito) !!};
            
            // Add and configure Series
            var pieSeries2 = chart_inci2.series.push(new am4charts.PieSeries());
            pieSeries2.dataFields.value = "cuenta";
            pieSeries2.dataFields.category = "des_estado";
            pieSeries2.slices.template.stroke = am4core.color("#fff");
            pieSeries2.slices.template.strokeWidth = 2;
            pieSeries2.slices.template.strokeOpacity = 1;
            
            // This creates initial animation
            pieSeries2.hiddenState.properties.opacity = 1;
            pieSeries2.hiddenState.properties.endAngle = -90;
            pieSeries2.hiddenState.properties.startAngle = -90;

            }); // end am4core.ready()
        </script>
    </td>
</tr>
<tr>
    <td colspan="8">
        @php
            $datos=$incidencias->groupBy('des_causa')->map->count();
            $datos_quesito = [];
            foreach ($datos as $key => $value) {
                $obj=new stdClass();
                if($key!=''){
                    $obj->des_estado = $key;
                } else{
                    $obj->des_estado = "Sin causa de cierre";
                }
                
                $obj->cuenta = $value;
                $datos_quesito[]=$obj;
            }    

            //dd($datos_quesito);
        @endphp
        <div class="card">
            <div class="card-body">
                Causa de cierre
                <div id="chartdiv_incidencias3" style="width:100%; height:300px;  ml-0"></div>
        
            </div>
        </div>

        <script>
            am4core.ready(function() {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            am4core.useTheme(am4themes_kelly);
        
            // Themes end
            // Create chart instance
            var chart_inci3 = am4core.create("chartdiv_incidencias3", am4charts.PieChart);
        
            chart_inci3.legend = new am4charts.Legend();
            
            // chart_inci3 data
            chart_inci3.data = {!! json_encode($datos_quesito) !!};
            
            // Add and configure Series
            var pieSeries3 = chart_inci3.series.push(new am4charts.PieSeries());
            pieSeries3.dataFields.value = "cuenta";
            pieSeries3.dataFields.category = "des_estado";
            pieSeries3.slices.template.stroke = am4core.color("#fff");
            pieSeries3.slices.template.strokeWidth = 2;
            pieSeries3.slices.template.strokeOpacity = 1;
            
            // This creates initial animation
            pieSeries3.hiddenState.properties.opacity = 1;
            pieSeries3.hiddenState.properties.endAngle = -90;
            pieSeries3.hiddenState.properties.startAngle = -90;

            }); // end am4core.ready()
        </script>
    </td>
</tr>
@php
    $datos=$incidencias->groupBy('fecha_corta')->map->count();
    $resultado_fecha = [];
    foreach ($datos as $key => $value) {
        $obj=new stdClass();
        $obj->fecha = $key;
        $obj->cuenta = $value;
        $resultado_fecha[]=$obj;
    }    

    //dd($datos_quesito);
@endphp

<script>
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
        bullet.circle.strokeWidth = 1;

        return series;
    }

    var series1 = createSeries("Cuenta", "Incidencias");

    chart.legend = new am4charts.Legend();
    chart.cursor = new am4charts.XYCursor();
</script>
@endnotmobile 
@endif