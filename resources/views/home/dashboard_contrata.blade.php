@php
    $tipo_ronda='L';   
@endphp

@if(Auth::user()->id_contrata!=null)
    @php
        $datos_contrata=DB::table('contratas')->where('id_contrata',Auth::user()->id_contrata)->first();
        $operarios_contrata=DB::table('contratas_operarios')->where('id_contrata',Auth::user()->id_contrata)->wherenull('id_usuario')->whereraw("nom_operario like '%".Auth::user()->val_prefijo_compartido."%'")->get();
        $datos_operario=DB::table('contratas_operarios')->where('id_operario',session('id_operario'))->first();
        if(session('id_operario')!=null){
            $datos=DB::table('trabajos_programacion')
                ->select('trabajos.des_trabajo','trabajos.val_icono as icono_trabajo','trabajos.val_color as color_trabajo',
                        'trabajos_programacion.*',
                        'trabajos_planes.des_plan','trabajos_planes.val_icono as icono_plan','trabajos_planes.val_color as color_plan','trabajos_planes.id_edificio',
                        'edificios.des_edificio',
                        'plantas.des_planta',
                        'plantas_zonas.des_zona',
                        'trabajos_planes_detalle.id_planta','trabajos_planes_detalle.id_zona','trabajos_planes_detalle.val_tiempo','trabajos_planes_detalle.num_operarios','trabajos_planes_detalle.list_operarios','trabajos_planes_detalle.txt_observaciones',
                        'grupos_trabajos.id_grupo','grupos_trabajos.des_grupo','grupos_trabajos.val_icono as icono_grupo','grupos_trabajos.val_color as color_grupo',
                        'operarios_ini.nom_operario as nom_operario_ini',
                        'operarios_fin.nom_operario as nom_operario_fin',)
                ->join('trabajos_planes_detalle','trabajos_programacion.id_trabajo_plan','trabajos_planes_detalle.key_id')
                ->join('trabajos_planes','trabajos_planes_detalle.id_plan','trabajos_planes.id_plan')
                ->join('edificios','trabajos_planes.id_edificio','edificios.id_edificio')
                ->leftjoin('contratas_operarios as operarios_ini','trabajos_programacion.id_operario_inicio','operarios_ini.id_operario')
                ->leftjoin('contratas_operarios  as operarios_fin','trabajos_programacion.id_operario_fin','operarios_fin.id_operario')
                ->leftjoin('plantas','trabajos_planes_detalle.id_planta','plantas.id_planta')
                ->leftjoin('plantas_zonas','trabajos_planes_detalle.id_zona','plantas_zonas.key_id')
                ->join('trabajos','trabajos_planes_detalle.id_trabajo','trabajos.id_trabajo')
                ->join('grupos_trabajos','trabajos_planes_detalle.id_grupo_trabajo','grupos_trabajos.id_grupo')
                ->where('trabajos_planes.id_cliente',Auth::user()->id_cliente)
                ->where(function($q){
                    if(session('id_operario')!=null){
                        $q->whereraw("find_in_set(".session('id_operario').",trabajos_planes_detalle.list_operarios)");
                    }
                    $q->orwherenull('trabajos_planes_detalle.list_operarios');
                })
                ->wheredate('trabajos_programacion.fec_programada',Carbon\Carbon::now())
                ->get();

                $plantas=$datos->pluck('id_planta')->unique()->count();
                $zonas=$datos->pluck('id_zona')->unique()->count();
                $edificios=$datos->pluck('id_edificio')->unique()->count();
        }
    @endphp
    <div class="row">
        <div class="col-md-12 text-center">
            <img src="{{ isset($datos_contrata->img_logo) ? Storage::disk(config('app.img_disk'))->url('img/contratas/'.$datos_contrata->img_logo) : ''}}" title="{{ $datos_contrata->des_contrata??'' }}" style="margin: auto; display: block; width: 100px; heigth:20px" alt=""  class="img-fluid">
        </div>
    </div>
    @if(Auth::user()->mca_compartido=='S' && session('id_operario')==null)
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Por favor, seleccione su identificador de operario dentro de {{ $datos_contrata->des_contrata }}</label><br>
                    <select name="id_operario" id="id_operario " class="select2 form-control" style="width:350px;"  >
                        <option value=""></option>
                        @foreach ($operarios_contrata->unique() as $c)
                            <option value="{{$c->id_operario}}">{{$c->nom_operario}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @section('scripts4')
        <script>
            $('#mainnav-container').hide();
            $('.header-searchbox').hide();


            $('.select2').change(function(){
                console.log('cambio');
                var id_operario=$(this).val();
                $.ajax({
                    url: "{{ route('home.set_operario') }}",
                    type: "POST",
                    data: {
                        id_operario: id_operario,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (data) {
                        location.reload();
                        //console.log(data);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            });
            
        </script>
        @endsection
    @endif

    <div class="row">
        <div class="col-md-12 text-center">
            <h2>{{ $datos_operario->nom_operario??'' }}</h2>
        </div>
    </div>
    @if(isset($datos))
        <div class="card">
            <div class="card-body text-center">
                <div class="d-flex align-items-center">
                    <a class="flex-shrink-0 p-3 btn btn-outline-light" href="{{ url('/trabajos/mistrabajos') }}">
                        <div class="h3 display-3">{{ $datos->count() }}</div>
                        <span class="h6">Tareas para hoy</span>
                    </a>
                    <div class="flex-grow-1 text-center ms-3 ">
                        <!-- Social media statistics -->
                        
                            @desktop <div class="mt-4 pt-3 d-flex justify-content-around border-top"> @elsedesktop <div class="row text-center ms-3"> @enddesktop
                            <div class="card bg-cyan text-white mb-3 mb-xl-3">
                                <div class="card-body py-3 d-flex align-items-stretch">
                                    <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                                        <i class="fa-solid fa-building fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="h2 mb-0">{{ $edificios }}</h5>
                                        <p class="mb-0"> Edificios</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card bg-purple text-white mb-3 mb-xl-3">
                                <div class="card-body py-3 d-flex align-items-stretch">
                                    <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                                        <i class="fa-solid fa-layer-group fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="h2 mb-0">{{ $plantas }}</h5>
                                        <p class="mb-0">Plantas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card bg-orange text-white mb-3 mb-xl-3">
                                <div class="card-body py-3 d-flex align-items-stretch">
                                    <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                                        <i class="fa-solid fa-draw-square fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="h2 mb-0">{{ $zonas }}</h5>
                                        <p class="mb-0">Zonas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END : Social media statistics -->
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif