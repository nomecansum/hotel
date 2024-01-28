@php
	Carbon\Carbon::setLocale(session('lang'));	
    setlocale(LC_TIME, 'Spanish');
@endphp
@php $idcl=0; @endphp
@foreach ($festives as $f)
    @if($idcl!=$f->id_cliente && sizeof(clientes())>1)
        <tr class="bg-light-extra">

            <td>
                @if( !empty($f->img_logo) )
                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" style="max-width:50px;" alt="">
                @else
                <img src="{{ url('/img/logo_def.png') }}" style="height: 40px">
                @endif
                <span class="font-20 font-bold"> {{ $f->nom_cliente }}</span>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @php $idcl=$f->id_cliente; @endphp
    @endif
    <tr class="hover-this @if($f->mca_fijo=='S') bg-light @endif" style="height: 40px">
        <td>{{$f->des_festivo}}</td>
        <td>{!!beauty_fecha($f->val_fecha,0)!!}</td>
        @admin @desktop<td>@if($f->mca_fijo!='S'){{$f->nom_cliente}}@else ---- @endif</td>@enddesktop @endadmin
        <td style="position: relative;">
            @if($f->mca_nacional=='N')
                {{--Provincial--}}
                @if(($f->cod_centro == "" || $f->cod_centro == null) && ($f->cod_provincia != "" || $f->cod_provincia != null))
                    @php
                        try{
                        $provincia = DB::select( "select group_concat(`provincias`.`nombre` ORDER BY `nombre` ASC SEPARATOR '#') as provincia from provincias where `provincias`.`id_prov` IN (".$f->cod_provincia.")");
                        $provincia=array_filter(explode('#',$provincia[0]->provincia));
                    } catch (\Exception $e) {
                        $provincia=[];
                    }
                    @endphp
                    <b>PROVINCIAL</b>
                    @foreach($provincia as $p)
                        <li>{{$p}}</li>
                    @endforeach
                {{--Regional--}}
                @elseif(($f->cod_centro == "" || $f->cod_centro == null) && ($f->cod_region != "" || $f->cod_region != null) && ($f->cod_provincia == "" || $f->cod_provincia == null))
                    @php
                        try{
                        $regional = DB::select("select group_concat(`regiones`.`nom_region` ORDER BY `nom_region` ASC SEPARATOR '#') as region from regiones where `regiones`.`cod_region` IN (".$f->cod_region.")");
                        $regional=array_filter(explode('#',$regional[0]->region));
                    } catch (\Exception $e) {
                        $regional=[];
                    }
                    @endphp
                    <b>REGIONAL</b>
                    @foreach($regional as $r)
                        <li>{{$r}}</li>
                    @endforeach
                {{--Local--}}
                @elseif(($f->cod_centro != "" || $f->cod_centro != null))
                    @php
                        try{
                        $centros = DB::select("select group_concat(`centros`.`des_centro` ORDER BY `des_centro` ASC SEPARATOR '#') as centro from centros where `centros`.`cod_centro` IN (".$f->cod_centro.")");
                        $centros=array_filter(explode('#',$centros[0]->centro));
                    } catch (\Exception $e) {
                        $centros=[];
                    }
                    @endphp
                    <b>LOCAL</b>
                    @foreach($centros as $c)
                        <li>{{$c}}</li>
                    @endforeach
                @endif
            @else
                @php $pais=DB::table('paises')->where('id_pais',$f->cod_pais)->first();@endphp
                @if(isset($f->cod_pais))<b><i class="flag-icon flag-icon-{{ strtolower($pais->cod_iso_pais) }}"></i> NACIONAL</b> [{{$pais->nom_pais }}]@endif
            @endif
            {{-- {{$f->des_centro}} --}}
            <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                <div class="btn-group btn-group pull-right ml-1" role="group">
                    @if(checkPermissions(['Festivos'],["W"]))<a href="#" onclick="editar_festivo({{ $f->cod_festivo }});" class="btn btn-xs btn-info"><span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                    @if(checkPermissions(['Festivos'],["D"]))<a href="#eliminar-festivo-{{$f->cod_festivo}}" onclick="del({{ $f->cod_festivo }});" data-toggle="modal" class="btn btn-xs btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
                </div>
            </div>
            @if(checkPermissions(['Festivos'],["D"]))
            <div class="modal fade" id="eliminar-festivo-{{$f->cod_festivo}}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Borrar festivo</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            ¿Borrar festivo {{ $f->des_festivo}}?
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-warning mr-auto" href="{{url('festives/delete',$f->cod_festivo)}}">{{trans('strings.yes')}}</a>
                            <button type="button" data-dismiss="modal" class="btn btn-info">{{trans('strings.cancel')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </td>
    </tr>
@endforeach
<tr></tr>