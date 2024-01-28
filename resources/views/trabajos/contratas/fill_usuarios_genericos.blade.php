<div class="row mb-4">
    <div class="form-group col-md-2">
        <label for="name" class="control-label">Crear usuarios</label>
        <input class="form-control" name="num_usuarios" type="number" id="num_usuarios" value="1"  min="1" max="1000" >
    </div>
    <div class="form-group col-md-1">
        <label for="name" class="control-label">Inicio</label>
        <input class="form-control" name="num_inicio" type="number" id="num_inicio" value="1"  min="1" max="1000" >
    </div>
    <div class="form-group col-md-4">
        <label for="des_prefijo" class="control-label">Prefijo</label>
        <input class="form-control" name="des_prefijo" type="text" id="des_prefijo"  maxlength="200" placeholder="Ej: OPERARIO_">
    </div>
    <div class="form-group col-md-1" >
        <label for="val_color">Color</label><br>
        <input type="color" autocomplete="off" name="val_color_gen" id="val_color_gen"  class="form-control" value="{{isset($dato->val_color)?$dato->val_color:''}}" />
    </div>
    <div class="form-group col-md-1">
        <div class="form-group">
            <label>Icono</label><br>
            <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
        </div>
    </div>
    <div class="col-md-1 text-end">
       <input class="btn btn-primary mt-3 btn_crear" type="button" value="Crear">
    </div>
    <div class="col-md-2 text-end">
        <button class="btn btn-warning mt-3 btn_borrar" type="button" value="Borrar"><i class="fa-solid fa-trash"></i> Borrar</button>
     </div>
</div>

@foreach($usuarios as $u)
    <div class="col-md-4 mb-2 sp_operario" style="color: {{ $u->val_color }}; font-weight: 400" id="operario_{{ $u->id_operario }}" data-color="{{ $u->val_color }}" data-id="{{ $u->id_operario }}">
        <input type="checkbox" class="chk_generico" name="usuint_{{ $u->id_operario }}" data-id="{{ $u->id_operario }}" style="display: none" checked> <i class="{{ $u->val_icono??'fa-solid fa-person-simple' }}"></i> <span class="sp_nombre" >{{ $u->nom_operario }}</span>
    </div>
@endforeach

<div class="modal fade" id="modal-operarios" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h3 class="modal-title text-nowrap" id="tit_modal_comentarios">Datos del usuario generico </h3>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>    
            <div class="modal-body">
                <div id="body_comentario">

                </div>
                <div id="form_comentario">
                    <form id="form_dato_operario" method="post" class="form-ajax" action="{{ url('/contratas/save_operario_generico') }}">
                        @csrf
                        <input type="hidden" name="id_operario" id="id_operario">
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="des_prefijo" class="control-label">Nombre</label>
                                <input class="form-control" name="nom_operario" type="text" id="nom_operario"  maxlength="200" placeholder="Ej: OPERARIO_">
                            </div>
                            <div class="form-group col-md-2" >
                                <label for="val_color">Color</label><br>
                                <input type="color" autocomplete="off" name="val_color_operario" id="val_color_operario"  class="form-control" value="{{isset($dato->val_color)?$dato->val_color:''}}" />
                            </div>
                            <div class="form-group col-md-2">
                                <div class="form-group">
                                    <label>Icono</label><br>
                                    <button type="button"  role="iconpicker" name="val_icono_operario"  id="val_icono_operario" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                                </div>
                            </div>
                        </div>
                        
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" id="btn_borrar_operario"> <i class="fa-solid fa-trash"></i> Borrar</a>
                <a class="btn btn-info" id="btn_guardar_operario">Guardar</a>
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.btn_crear').click(function(){
        if($('#des_prefijo').val()==''){
            toast_warning('Crear usuarios','Debe ingresar un prefijo');
            return;
        }
        $.post('{{url('/trabajos/contratas/crear_usuarios')}}', {_token: '{{csrf_token()}}',num_usuarios:$('#num_usuarios').val(),num_inicio:$('#num_inicio').val(),des_prefijo:$('#des_prefijo').val(),val_color:$('#val_color_gen').val(),id_contrata:{{ $id }}}, function(data, textStatus, xhr) {
                console.log(data);
                if(data.error){
                    toast_error(data.title,data.error);
                } else if(data.alert){
                    toast_warning(data.title,data.alert);
                } else{
                    toast_ok(data.title,data.message);
                    $('#lista_genericos').load('{{url('/trabajos/contratas/usuarios_genericos',$id)}}');
                }
            })
            .fail(function(err){
                toast_error('Error',err.responseText);
            });
    })
    $('.btn_borrar').click(function(){
        $('.chk_generico').show();
    })

    $('.chk_generico').click(function(){
        $.get('{{url("/trabajos/contratas/set_usuarios_contrata/D",$id)}}/'+$(this).data('id'), function(data, textStatus, xhr) {
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.alert){
                toast_warning(data.title,data.alert);
            } else{
                $('#operario_'+data.id).remove();
                toast_ok(data.title,data.message);
            }
        })
    })

    $('#val_icono').iconpicker({
            icon:'{{isset($dato) ? ($dato->val_icono) : ''}}'
        });

    $('.sp_operario').click(function(){
        $('#id_operario').val($(this).data('id'));
        $('#nom_operario').val($(this).find('.sp_nombre').text());
        $('#val_color_operario').val($(this).data('color'));;
        $('#val_icono_operario').iconpicker({icon: $(this).find('i').attr('class')});
        $('#modal-operarios').modal('show');
    })

    $('#btn_guardar_operario').click(function(){
        event.preventDefault();
        $.post('{{url('/trabajos/contratas/save_operario_generico')}}', {_token: '{{csrf_token()}}',id_operario:$('#id_operario').val(),nom_operario:$('#nom_operario').val(),val_color:$('#val_color_operario').val(),val_icono:$('#val_icono_operario').find('i').attr('class')}, function(data, textStatus, xhr) {
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.alert){
                toast_warning(data.title,data.alert);
            } else{
                toast_ok(data.title,data.message);
                $('#lista_genericos').load('{{url('/trabajos/contratas/usuarios_genericos',$id)}}');
                $('#modal-operarios').modal('hide');
                $('#operario_'+$('#id_operario').val()).css('color',$('#val_color_operario').val());
                $('#operario_'+$('#id_operario').val()).find('i').attr('class',$('#val_icono_operario').find('i').attr('class'));
                $('#operario_'+$('#id_operario').val()).find('i').css('color',$('#val_color_operario').val());
                $('#operario_'+$('#id_operario').val()).find('.sp_nombre').html($('#nom_operario').val());
            }
        })
        .fail(function(err){
            toast_error('Error',err.responseText);
        });
    })

    $('#btn_borrar_operario').click(function(){
        event.preventDefault();
        $.post('{{url('/trabajos/contratas/del_operario_generico')}}', {_token: '{{csrf_token()}}',id_operario:$('#id_operario').val()}, function(data, textStatus, xhr) {
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.alert){
                toast_warning(data.title,data.alert);
            } else{
                toast_ok(data.title,data.message);
                $('#lista_genericos').load('{{url('/trabajos/contratas/usuarios_genericos',$id)}}');
                $('#modal-operarios').modal('hide');
            }
        })
        .fail(function(err){
            toast_error('Error',err.responseText);
        });
    })
</script>