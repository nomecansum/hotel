@php
    //$hide=['cli'=>0,'cen'=>0,'dis'=>0,'col'=>0,'dep'=>0,'emp'=>0,'fec'=>0];
    use App\Models\estados;
    use App\Models\tags;
    use App\Models\puestos_tipos;
    use App\Models\users;
    use App\Models\estados_incidencias;
    use App\Models\incidencias_tipos;
    use App\Models\marcas;
    use App\Models\ferias;
@endphp

<style>
     .select-all{
            max-height: 50px !important;
        }
</style>


@if(!(isset($hide['head']) || (isset($hide['head']) && ($hide['head']!==1))))
<div class="card" >
    <div class="card-header cursor-pointer headfiltro" style="padding-top: 2px" id="" >
        {{--  <span class="mt-3 ml-2 font-18"></span>  --}}
        <div id="expand_campos" data-div="divfiltro"  class="expandir ml-2  font-18 p-t-10"><i class="fad fa-filter"></i> Filtro <a href=javascript:void(0); class="expand"><i disabled class="fas fa-caret-right text-secondary "></i></a></div>
        <span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
        {{-- <div id="loadfilter" class="load8"><div class="loader"></div></div> --}}
    </div>
    <div class="card-body" id="divfiltro" style="display: none" >
@endif
        
<div class="form-group col-md-12 mt-3" style="{{ ((!fullAccess() && count(clientes())==1) || (isset($hide['cli']) && $hide['cli']===1)) ? 'display: none' : ''}}">
    <label>Cliente</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="cliente[]" id="multi-cliente">
            @foreach (lista_clientes() as $c)
                <option value="{{$c->id_cliente}}">{{$c->nom_cliente}}</option>
            @endforeach
        </select>
        <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3  text-nowrap" style="{{ (isset($hide['edi']) && $hide['edi']==1) ? 'display: none' : ''  }}">
    <label>Edificio</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="edificio[]" id="multi-edificio"></select>
        <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['pla']) && $hide['pla']==1) ? 'display: none' : ''  }}">
    <label>Planta</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="planta[]" id="multi-planta" all="0" ></select>
        <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['tag']) && $hide['tag']==1) ? 'display: none' : ''  }}">
    <label>Tag
        <input id="demo-sw-checkstate" name="andor" type="checkbox">
        <span id="demo-sw-checkstate-field" class="label label-info">OR</span>
    </label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="tags[]" id="multi-tag" ></select>
        <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['pue']) && $hide['pue']==1) ? 'display: none' : ''  }}">
    <label>Puesto</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="puesto[]" id="multi-puesto" ></select>
        <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['tip']) && $hide['tip']==1) ? 'display: none' : ''  }}">
    <label>Tipo de puesto</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="tipo[]" id="multi-tipo" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-estado"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['est']) && $hide['est']==1) ? 'display: none' : ''  }}">
    <label>Estado puesto</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="estado[]" id="multi-estado" >
            @foreach(estados::all() as $estado)
                <option {{ $estado->id_estado==0?'selected':'' }} value="{{ $estado->id_estado }}">{{ $estado->des_estado }}</option>
            @endforeach
                <option value="A">Anonimo</option>
                <option value="R">Reserva</option>
                <option value="P">Asignado a perfil</option>
                <option value="U">Asignado a usuario</option>
        </select>
        <button class="btn btn-primary select-all" data-select="multi-estado"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['est_inc']) && $hide['est_inc']==1) ? 'display: none' : ''  }}">
    <label>Estado {{ $singular??'incidencia' }}</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="estado_inc[]" id="multi-estado_inc" >
            @foreach(estados_incidencias::where(function($q) {
                $q->where('id_cliente',Auth::user()->id_cliente);
                $q->orwhere('mca_fijo','S');
                })
                ->where('id_estado','>',0)
                ->orderby('des_estado')
                ->get() as $estado)
                <option value="{{ $estado->id_estado }}">{{ $estado->des_estado }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary select-all" data-select="multi-estado"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['tip_inc']) && $hide['tip_inc']==1) ? 'display: none' : ''  }}">
    <label>Tipo {{ $singular??'incidencia' }}</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="tipoinc[]" id="multi-tipoinc" >
            @foreach(incidencias_tipos::where(function($q) {
                $q->where('id_cliente',Auth::user()->id_cliente);
                $q->orwhere('mca_fijo','S');
                })
                ->orderby('des_tipo_incidencia')
                ->get() as $tipo)
                <option value="{{ $tipo->id_tipo_incidencia }}">{{ $tipo->des_tipo_incidencia }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary select-all" data-select="multi-estado"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['proc']) && $show['proc']==1) ? '' : 'display: none'  }}">
    <label>Procedencia</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="procedencia[]" id="multi-procedencia" >
            <option value="web">WEB</option>
            <option value="scan">SCAN</option>
            <option value="api">API</option>
            <option value="salas">SALAS</option>
        </select>
        <button class="btn btn-primary select-all" data-select="multi-tipomark"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['usu']) && $hide['usu']==1) ? 'display: none' : ''  }}">
    <label>Usuario</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="user[]" id="multi-user" >

        </select>
        <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['sup']) && $show['sup']==1) ? '' : 'display: none' }}">
    <label>Supervisor</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="supervisor[]" id="multi-supervisor" >

        </select>
        <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($hide['tip_mark']) && $hide['tip_mark']==1) ? 'display: none' : ''  }}">
    <label>Marca</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="tipomark[]" id="multi-tipomark" >
            @foreach(marcas::where(function($q){
                    if (!isAdmin()) {
                        $q->where('id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('ferias_marcas.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->orderby('des_marca')
                ->get() as $tipo)
                <option value="{{ $tipo->id_marca }}">{{ $tipo->des_marca }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary select-all" data-select="multi-tipomark"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>
<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['tip_feria']) && $show['tip_feria']==1) ?'' : 'display: none'  }}">
    <label>Feria</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="tipoferia[]" id="multi-tipoferia" >
            @foreach(ferias::where(function($q){
                    if (!isAdmin()) {
                        $q->where('id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('ferias.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->orderby('des_feria')
                ->get() as $tipo)
                <option value="{{ $tipo->id_feria }}">{{ $tipo->des_feria }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary select-all" data-select="multi-tipoferia"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['perfil']) && $show['perfil']==1) ? '' : 'display: none'  }}">
    <label>Perfil</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="cod_nivel[]" id="multi-perfiles" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-perfiles"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['dep']) && $show['dep']==1) ? '' : 'display: none'  }}">
    <label>Departamento</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="id_departamento[]" id="multi-departamentos" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-departamentos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['col']) && $show['col']==1) ? '' : 'display: none'  }}">
    <label>Colectivo</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="cod_colectivo[]" id="multi-colectivos" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-colectivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['tur']) && $show['tur']==1) ? '' : 'display: none'  }}">
    <label>Turno</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="id_turno[]" id="multi-turnos" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['zon']) && $show['zon']==1) ? '' : 'display: none'  }}">
    <label>Zona</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="id_zona[]" id="multi-zonas" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['gru']) && $show['gru']==1) ? '' : 'display: none'  }}">
    <label>Grupos de trabajos</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="id_grupo[]" id="multi-grupos" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['con']) && $show['con']==1) ? '' : 'display: none'  }}">
    <label>Contratas</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="id_contrata[]" id="multi-contratas" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['pln']) && $show['pln']==1) ? '' : 'display: none'  }}">
    <label>Planes de trabajo</label>
    <div class="input-group select2-bootstrap-append">
        <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="id_plan[]" id="multi-planes" >
            
        </select>
        <button class="btn btn-primary select-all" data-select="multi-user"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['u_l']) && $show['u_l']==1) ? '' : 'display: none'  }}">
    <label>Lista de email usuarios (separada por ,)</label>
    <div class="form-control">
        <textarea name="user_list" style="width: 98%" cols="80" rows="3"></textarea>
    </div>
</div>

<div class="form-group  col-md-12 mt-3" style="{{ (isset($show['u_id']) && $show['u_id']==1) ? '' : 'display: none'  }}">
    <label>Lista de ID de usuarios (separada por ,)</label>
    <div class="form-control">
        <textarea name="user_id_list" style="width: 98%" cols="80" rows="3"></textarea>
    </div>
</div>


<div class="row mt-5" style="{{ (isset($hide['btn']) && $hide['btn']==1) ? 'display: none' : ''  }}">
    <div class="col-md-12 text-end mb-3">
        <button id="btn_submit" class="btn btn-primary btn-lg float-right"><i class="fa fa-search"></i> {{ $etiqueta_boton??'Ver' }}</button>
    </div>
</div>
@if(!(isset($hide['head']) || (isset($hide['head']) && ($hide['head']!==1))))
    </div>
</div>
@endif

<br>


@section('scripts2')

<script>

    var changeCheckbox = document.getElementById('demo-sw-checkstate'), changeField = document.getElementById('demo-sw-checkstate-field');
    new Switchery(changeCheckbox,{ size: 'small',color:'#489eed' })
    changeCheckbox.onchange = function() {
        if(changeCheckbox.checked){
            changeField.innerHTML='AND'
        } else {
            changeField.innerHTML='OR'
        }
    };


    $(function(){
        $('#multi-cliente').change();
    }) 

    $('.select-all').click(function(event) {
        $(this).parent().parent().find('select option').prop('selected', true)
        $(this).parent().parent().find('select').select2({
            placeholder: "Todos",
            allowClear: true,
            @desktop width: "90%", @elsedesktop width: "75%", @enddesktop 
        });
        $(this).parent().parent().find('select').change();
    });



    $('.headfiltro').click(function(){
        $('#divfiltro').toggle();
    })  

    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        @desktop width: "90%", @elsedesktop width: "65%", @enddesktop 
    });

    $('#multi-cliente').change(function(event) {
        $('#loadfilter').show();
       
        console.log('cambio de cliente');
        $.post('{{url('/filters/loadedificios')}}', {_token:'{{csrf_token()}}',cliente:$(this).val()}, function(data, textStatus, xhr) {
            console.log('cliente');
            console.log(data);
            cliente="";
            $('#multi-edificio').empty();
            $.each(data.edificios, function(index, val) {
                if(cliente!=val.id_cliente){
                    $('#multi-edificio').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente=val.id_cliente;
                }
                $('#multi-edificio').append('<option value="'+val.id_edificio+'">'+val.des_edificio+'</option>');
            });

            cliente_c="";
            edificio_c="";
            $('#multi-planta').empty();
            $.each(data.plantas, function(index, val) {
                if(cliente_c!=val.id_cliente){
                    $('#multi-planta').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_c=val.id_cliente;
                }
                if(edificio_c!=val.id_edificio){
                    $('#multi-planta').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_c=val.id_edificio;
                }
                $('#multi-planta').append('<option value="'+val.id_planta+'">'+val.des_planta+'</option>');
            });

            cliente_c="";
            edificio_c="";
            planta_c="";
            $('#multi-puesto').empty();
            $.each(data.puestos, function(index, val) {
                if(cliente_c!=val.id_cliente){
                    $('#multi-puesto').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_c=val.id_cliente;
                }
                if(edificio_c!=val.id_edificio){
                    $('#multi-puesto').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_c=val.id_edificio;
                }
                if(planta_c!=val.id_planta){
                    $('#multi-puesto').append('<optgroup label="'+val.des_planta+'"></optgroup>');
                    planta_c=val.id_planta;
                }
                $('#multi-puesto').append('<option value="'+val.id_puesto+'">'+val.cod_puesto+'</option>');
            });
            $('#loadfilter').hide();

            tag_c="";
            $('#multi-tag').empty();
            $.each(data.tags, function(index, val) {
                if(tag_c!=val.id_cliente){
                    $('#multi-tag').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    tag_c=val.id_cliente;
                }
                $('#multi-tag').append('<option value="'+val.id_tag+'">'+val.nom_tag+'</option>');
            });

            dep_c="";
            $('#multi-departamentos').empty();
            $.each(data.departamentos, function(index, val) {
                if(dep_c!=val.id_cliente){
                    $('#multi-departamentos').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    dep_c=val.id_cliente;
                }
                $('#multi-departamentos').append('<option value="'+val.cod_departamento+'">'+val.nom_departamento+'</option>');
            });
            $('#loadfilter').hide();

            perf_c="";
            $('#multi-perfiles').empty();
            $.each(data.perfiles, function(index, val) {
                if(perf_c!=val.id_cliente){
                    $('#multi-perfiles').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    perf_c=val.id_cliente;
                }
                $('#multi-perfiles').append('<option value="'+val.id_perfil+'">'+val.des_perfil+'</option>');
            });
            $('#loadfilter').hide();

            turno_c="";
            $('#multi-turnos').empty();
            $.each(data.turnos, function(index, val) {
                if(turno_c!=val.id_cliente){
                    $('#multi-turnos').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    turno_c=val.id_cliente;
                }
                $('#multi-turnos').append('<option value="'+val.id_turno+'">'+val.des_turno+'</option>');
            });
            $('#loadfilter').hide();

            tipo_c="";
            $('#multi-tipo').empty();
            $.each(data.tipos, function(index, val) {
                if(tipo_c!=val.id_cliente){
                    $('#multi-tipo').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    tipo_c=val.id_cliente;
                }
                $('#multi-tipo').append('<option value="'+val.id_tipo+'">'+val.des_tipo+'</option>');
            });
            $('#loadfilter').hide();

            tipo_u="";
            $('#multi-user').empty();
            $.each(data.users, function(index, val) {
                if(tipo_u!=val.id_cliente){
                    $('#multi-user').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    tipo_u=val.id_cliente;
                }
                $('#multi-user').append('<option value="'+val.id+'">'+val.name+'</option>');
            });
            $('#loadfilter').hide();

            tipo_s="";
            $('#multi-supervisor').empty();
            $.each(data.supervisores, function(index, val) {
                if(tipo_s!=val.id_cliente){
                    $('#multi-supervisor').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    tipo_s=val.id_cliente;
                }
                $('#multi-supervisor').append('<option value="'+val.id+'">'+val.name+'</option>');
            });
            $('#loadfilter').hide();

            colectivo_c="";
            $('#multi-colectivos').empty();
            $.each(data.colectivos, function(index, val) {
                if(colectivo_c!=val.id_cliente){
                    $('#multi-colectivos').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    colectivo_c=val.id_cliente;
                }
                $('#multi-colectivos').append('<option value="'+val.cod_colectivo+'">'+val.des_colectivo+'</option>');
            });
            $('#loadfilter').hide();

            zona_c="";
            $('#multi-zonas').empty();
            $.each(data.zonas, function(index, val) {
                if(zona_c!=val.id_cliente){
                    $('#multi-zonas').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    zona_c=val.id_cliente;
                }
                $('#multi-zonas').append('<option value="'+val.id_zona+'">'+val.des_zona+'</option>');
            });
            $('#loadfilter').hide();

            grupo_c="";
            $('#multi-grupos').empty();
            $.each(data.grupos, function(index, val) {
                if(grupo_c!=val.id_cliente){
                    $('#multi-grupos').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    grupo_c=val.id_cliente;
                }
                $('#multi-grupos').append('<option value="'+val.id_grupo+'">'+val.des_grupo+'</option>');
            });
            $('#loadfilter').hide();

            contrata_c="";
            $('#multi-contratas').empty();
            $.each(data.contratas, function(index, val) {
                if(contrata_c!=val.id_cliente){
                    $('#multi-contratas').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    contrata_c=val.id_cliente;
                }
                $('#multi-contratas').append('<option value="'+val.id_contrata+'">'+val.des_contrata+'</option>');
            });
            $('#loadfilter').hide();

            plan_c="";
            $('#multi-planes').empty();
            $.each(data.planes, function(index, val) {
                if(plan_c!=val.id_cliente){
                    $('#multi-planes').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    plan_c=val.id_cliente;
                }
                $('#multi-planes').append('<option value="'+val.id_plan+'">'+val.des_plan+'</option>');
            });
            $('#loadfilter').hide();
            //try{ end_update_filtros('cliente') } catch(excp){ } //Funcion para actualizar cosas despues ed que se hayan cargado
        });
        
        
    });

    $('#multi-edificio').change(function(event) {
        $('#loadfilter').show();
        
       
       $.post('{{url('/filters/loadplantas')}}', {_token:'{{csrf_token()}}',centros:$(this).val(),cliente:$('#multi-cliente').val(),edificio:$('#multi-edificio').val()}, function(data, textStatus, xhr) {
            cliente_e="";
            edificio_e="";
            $('#multi-planta').empty();
            $.each(data.plantas, function(index, val) {
                if(cliente_e!=val.id_cliente){
                    $('#multi-planta').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_e=val.id_cliente;
                }
                if(edificio_e!=val.id_edificio){
                    $('#multi-planta').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_e=val.id_edificio;
                }
                $('#multi-planta').append('<option value="'+val.id_planta+'">'+val.des_planta+'</option>');
            });

            cliente_e="";
            edificio_e="";
            planta_e="";
            $('#multi-puesto').empty();
            $.each(data.puestos, function(index, val) {
                if(cliente_e!=val.id_cliente){
                    $('#multi-puesto').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_e=val.id_cliente;
                }
                if(edificio_e!=val.id_edificio){
                    $('#multi-puesto').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_e=val.id_edificio;
                }
                if(planta_e!=val.id_planta){
                    $('#multi-puesto').append('<optgroup label="'+val.des_planta+'"></optgroup>');
                    planta_e=val.id_planta;
                }
                $('#multi-puesto').append('<option value="'+val.id_puesto+'">'+val.cod_puesto+'</option>');
            });
            $('#loadfilter').hide();
            
            $('#multi-zonas').empty();
            $.each(data.zonas, function(index, val) {
                if(cliente_z!=val.id_cliente){
                    $('#multi-zonas').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_z=val.id_cliente;
                }
                if(edificio_z!=val.id_edificio){
                    $('#multi-zonas').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_z=val.id_edificio;
                }
                if(planta_z!=val.id_planta){
                    $('#multi-zonas').append('<optgroup label="'+val.des_planta+'"></optgroup>');
                    planta_z=val.id_planta;
                }
                $('#multi-zonas').append('<option value="'+val.id_zona+'">'+val.des_zona+'</option>');
            });
            $('#loadfilter').hide();
        });
        
    });

    $('#multi-planta').change(function(event) {
        $('#loadfilter').show();
        $.post('{{url('/filters/loadpuestos')}}', {_token:'{{csrf_token()}}',centros:$(this).val(),cliente:$('#multi-cliente').val(),edificio:$('#multi-edificio').val(),planta:$('#multi-planta').val()}, function(data, textStatus, xhr) {
            cliente_p="";
            edificio_p="";
            planta_p="";
            $('#multi-puesto').empty();
            $.each(data.puestos, function(index, val) {
                if(cliente_p!=val.id_cliente){
                    $('#multi-puesto').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_p=val.id_cliente;
                }
                if(edificio_p!=val.id_edificio){
                    $('#multi-puesto').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_p=val.id_edificio;
                }
                if(planta_p!=val.id_planta){
                    $('#multi-puesto').append('<optgroup label="'+val.des_planta+'"></optgroup>');
                    planta_p=val.id_planta;
                }
                $('#multi-puesto').append('<option value="'+val.id_puesto+'">'+val.cod_puesto+'</option>');
            });
            $('#loadfilter').hide();

            cliente_z="";
            edificio_z="";
            planta_z="";
            $('#multi-zonas').empty();
            $.each(data.zonas, function(index, val) {
                if(cliente_z!=val.id_cliente){
                    $('#multi-zonas').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_z=val.id_cliente;
                }
                if(edificio_z!=val.id_edificio){
                    $('#multi-zonas').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_z=val.id_edificio;
                }
                if(planta_z!=val.id_planta){
                    $('#multi-zonas').append('<optgroup label="'+val.des_planta+'"></optgroup>');
                    planta_z=val.id_planta;
                }
                $('#multi-zonas').append('<option value="'+val.id_zona+'">'+val.des_zona+'</option>');
            });
            $('#loadfilter').hide();
            //try{ end_update_filtros('planta') } catch(excp){ } //Funcion para actualizar cosas despues ed que se hayan cargado
        });
        
    });

    $('#save-favorite-button').click(function(event) {
		let name = $('#des_filtro').val();
		if (name.trim() == "") {
			Swal.fire({title: "{{ __('general.espere') }}",
						text: "{{ __('general.indique_nombre_filtro') }}",
						type: 'warning',
						footer: '<div><img src="/images/spotdyna.png"></div>',
                        timer: 3000});
            return false;
		}
		let data = $('.ajax-filter').serialize();
        data+='&name='+name;
        data+='&_token={{csrf_token()}}';

		$.post('{{url('getForm')}}', data, function(data, textStatus, xhr) {
			$('#save-favorite').modal('hide');
			$.toast({
                heading: '{{trans('strings.save_favorite')}}',
                text: name+': '+'{{trans('strings.favorite_success')}}',
                position: 'top-left',
                showHideTransition: 'slide',
                loaderBg: '#ff8000',
                icon: 'success',
                hideAfter: 6000,
                stack: 6,
				bgColor : '#d4edda',
				textColor : '#155724',
            });
            $('#des_filtro').val('');
        });


	});

	$('#load-favorites').click(function(event) {
        $.post('{{url('getFavorites')}}', {_token:'{{csrf_token()}}'}, function(data, textStatus, xhr) {
			$('#all-favorites').html(data);
			//$('#tablafavoritos').dataTable();
            $('#favorites-table').modal('show');
		});
	});

	function loadFilter(json)
	{
        $('#spin_filtros').show();
        let filter = JSON.parse(json);
		$('#multi-rango').val(filter['rango']);
		$('#multi-cliente').val(filter['clientes']).select2({placeholder: "todos"});
        $('.multi2').empty();

        $.post('{{url('filtros/loadtags')}}', {_token:'{{csrf_token()}}',clientes:$('#multi-cliente').val()}, function(data, textStatus, xhr) {

            $.each(data.edificios, function(index, val) {
                $('#multi-edificio').append('<option value="'+val.id_edificio+'">'+val.des_edificio+'</option>');
            });

            $.each(data.plantas, function(index, val) {
                $('#multi-planta').append('<option value="'+val.id_planta+'">'+val.des_planta+'</option>');
            });

            $.each(data.puestos, function(index, val) {
                $('#multi-puesto').append('<option value="'+val.id_puesto+'">'+val.des_puesto+'</option>');
            });

        });
        $('#favorites-table').modal('hide');
        $('#spin_filtros').hide();
	}

    $('#multi-cliente').on('select2:clearing', function(e){
        // $('#multi-cliente').off('change');
        e.preventDefault();
        $('#multi-cliente').empty();
        $('#multi-cliente').val(null).trigger('change');
        $('#multi-cliente').load("{{ url('/combos/clientes') }}", function(){
            $('#multi_clientes').trigger("change");
        });

        console.log('clearing');
    });

    $('.select-all').css('height',46);
        //$(':checkbox').on('change', handleCheckboxClick);

    $('.expand, .expandir').click(function(){
        $(this).find('i').toggleClass('fas fa-caret-right fas fa-caret-down');
        //$('#div_filtro').toggleClass('col-md-8 col-xs-12');
    });

    $('#btn_submit').click(function(){
        $('.expand').find('i').toggleClass('fas fa-caret-right fas fa-caret-down');
    })
</script>
@stop
