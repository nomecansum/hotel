<style>
    .rounded_cam{
        border-radius: 6px;
    }

</style>

<div id="demo-panel-network" class="card pb-3">
    <div class="card-header">
        <div class="card-control">
            <button id="demo-panel-network-refresh" class="btn btn-default btn-active-primary" data-toggle="panel-overlay" data-target="#mosaico"><i class="demo-psi-repeat-2"></i></button>
            <div class="dropdown">
                <button class="dropdown-toggle btn btn-default btn-active-primary" data-toggle="dropdown" aria-expanded="false"><i class="demo-psi-dot-vertical"></i></button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                </ul>
            </div>
        </div>
        <h3 class="card-title">Camaras(<span id="paginade"></span>/<span id="paginatotal"></span>)</h3>
    </div>

    <div class="row" id="mosaico">
        @for ($i = 0; $i < 6; $i++)
        <div class="col-md-4">
            <div id="feedimg{{$i}}" class="mar-lft mar-rgt mar-top rounded_cam" style="border: thin solid gray; with:300px; height: 300px">
                <img id="imgcam{{$i}}" style="width:100%; height:100%" data-id="0" data-url="" class="rounded_cam imgcam">
                <div class="p-2 mb-4 bg-mid-gray"><span class="badge badge-header badge-success float-left" id="dot{{$i}}"></span><span id="label{{$i}}" class="float-left"></span></div>
            </div>

        </div>
        @endfor
    </div>

</div>
@section('scripts')
    <script>
        let paginas;
        let datos;
        let pagina=0;
        let refresco=20000;
        let enlace=0;
        let int_proceso;

        function proceso(){
            bloque=datos[pagina];
            //console.log(bloque);
            indice=0;
            $.each(bloque,function(j,item){
                setTimeout(() => {
                    $('#label'+indice).html(item.etiqueta);
                    $('#paginade').html(pagina+1);
                    $('#paginatotal').html(paginas);
                    $('#imgcam'+indice).data("id",item.id);
                    $('#imgcam'+indice).data("url",item.url);
                    if(enlace==0)
                        $('#imgcam'+indice).attr("src",item.url);
                    if(item.status==1){
                        $('#dot'+indice).removeClass('badge-danger');
                        $('#dot'+indice).addClass('badge-success');
                    } else {
                        $('#dot'+indice).addClass('badge-danger');
                        $('#dot'+indice).removeClass('badge-success');
                    }
                    //$.get("{{ url('camaras/status') }}/"+item.id+"/1");
                    indice++;
                }, 500);
            })
            $.each(bloque,function(j,item){
                setTimeout(() => {
                    $.get("{{ url('/camaras/savesnapshot') }}/"+item.id);
                }, 500);
            })
            pagina++;
            if(pagina>=paginas){
                pagina=0;
            }
        }

        $('#demo-panel-network-refresh').click(function(){
            pagina=0;
            clearInterval(int_proceso);
            cargar_camaras();
        });

        function cargar_camaras(){
            $.get("{{ url('/6camaras/') }}/",function(data){
                data=JSON.parse(data);
                paginas=data.length;
                datos=data;
                proceso();
                int_proceso=setInterval(proceso, refresco);
            });
        }

        $(function(){
            //cargar_camaras();
        });

        $('.imgcam').on('error', function(){

            setTimeout(() => {
                console.log('Error CAM '+$(this).attr("src")+' Releyendo');
                src=$(this).attr("src");
                $(this).attr("src",'');
                $(this).attr("src",src);
            }, 500);
            console.log($(this).data("id"));
            $.get("{{ url('camaras/status') }}/"+$(this).data("id")+"/0");
            $('#dot'+indice).removeClass('badge-success');
            $('#dot'+indice).addClass('badge-danger');
        });

        $('.imgcam').click(function(){
            if ($(this).data('id')!=0) {
                $(location).attr("href", "{{ url('/camaras/ver_camara/') }}/"+$(this).data('id'));
            }
        })

        $('a').click(function(){
            console.log('link');
            enlace=1;
            clearInterval(int_proceso);

        });

    </script>
@endsection
