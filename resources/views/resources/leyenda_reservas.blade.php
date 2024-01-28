<div class="modal fade" id="modal-leyenda" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <h1 class="modal-title text-nowrap">Leyenda de puestos </h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>    
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-1 text-end"><img src="{{ url('img/res_flecha1.png') }}"></div>
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-2 text-end mb-2"><img src="{{ url('img/res_disponible.png') }}"></div><div class="col-md-9 mb-1"  style="padding-top: 9px">Puesto disponible</div>
                                <div class="col-md-2 text-end mb-1"><img src="{{ url('img/res_miperfil.png') }}"></div><div class="col-md-9 mb-1"  style="padding-top: 9px">Puesto reservado para mi perfil</div>
                            </div>
                        </div>
                </div>
                
                <div class="row mt-2">
                    <div class="col-md-1 text-end"><img src="{{ url('img/res_flecha2.png') }}"></div>
                        <div class="col-md-11">
                            <div class="row pad-top">
                                <div class="col-md-2 text-end mb-2"><img src="{{ url('img/res_reservado.png') }}"></div><div class="col-md-9 mb-1" style="padding-top: 9px">Puesto reservado</div>
                                <div class="col-md-2 text-end mb-2"><img src="{{ url('img/res_bloqueado.png') }}"></div><div class="col-md-9 mb-1"  style="padding-top: 9px">Puesto bloqueado/inoperativo</div>
                                <div class="col-md-2 text-end mb-2"><img src="{{ url('img/res_incidencia.png') }}"></div><div class="col-md-9 mb-1"  style="padding-top: 9px">Puesto con incidencia</div>
                                <div class="col-md-2 text-end mb-2"><img src="{{ url('img/res_perfil.png') }}"></div><div class="col-md-9 mb-1"  style="padding-top: 9px">Puesto reservado para un perfil distinto del mío</div>
                                <div class="col-md-2 text-end mb-1"><img src="{{ url('img/res_persona.png') }}"></div><div class="col-md-9 mb-1"  style="padding-top: 9px">Puesto reservado para una persona concreta</div>
                            </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center mb-1"><img src="{{ url('img/partes_puesto.png') }}"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning" onclick="cerrar_modal()">Cerrar</button>
            </div>
        </div>
        <div class="modal-footer">
            <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
        </div>
    </div>
</div>