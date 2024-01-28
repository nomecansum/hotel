@php
use Doctrine\SqlFormatter\HtmlHighlighter;
try{
    $cuenta=count($resultado->data);
} catch(\Throwable $e){
    $cuenta=0;
}
@endphp
@if(isset($resultado))
    <div class="row b-all mb-3">
        <div class="col-md-2 {{ $resultado->respuesta=='ok'?'bg-success':'bg-danger' }} p-1 rounded text-center">
            {{ __('general.resultado') }}: {{ $resultado->respuesta }}
        </div>
        <div class="col-md-5 p-1 rounded">
            {{ __('tareas.comando') }}: {{ $resultado->comando }}
        </div>
        <div class="col-md-4 p-1 rounded">
            {{ $cuenta }} {{ $resultado->tipo_id }}
        </div>
        <div class="col-md-1 text-end">
            <a href="#" id="btn-reset" class="btn btn-sm btn-danger ">
                <i class="fa-solid fa-empty-set"></i> Reset
            </a>
        </div>
    </div>
    @if($cuenta>0)
        <div class="table table-responsive table-condensed table-striped">
            <table>
                <thead>
                    <tr>
                        @foreach(array_keys((array)$resultado->data[0]) as $key)
                            <th class="text-center">{{ $key }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultado->data as $item)
                        <tr>
                            @foreach(array_keys((array)$resultado->data[0]) as $key)
                                <td class="text-nowrap text-center">{{ $item->$key }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        @php
            dump($resultado->data??[]);
        @endphp
       
    @endif
    @if(config("app.debug"))
        <div class="row mt-4">
            <h4>SQL</h4>
        </div>
        @sql{!! $resultado->query !!}@endsql
    @endif
@endif
<script>
    $('#btn-reset').click(function(){
        $.post('{{url(config('app.carpeta_asset')."/reset_estado")}}', {_token: '{{csrf_token()}}', cod_regla: {{ $cod_regla }}}, function(data, textStatus, xhr) {
        })
        .done(function(data){
            toast_ok(data.title,data.message);
        });
    })
</script>