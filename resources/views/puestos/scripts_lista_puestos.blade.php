<script>
    id_fila=0;
    token_fila=0;

function editar(id){
    $('#editorCAM').load("{{ url('/puestos/edit/') }}"+"/"+id, function(){
        animateCSS('#editorCAM','bounceInRight');
        $('#toolbutton').hide();
    });
}

function scan(id){
    window.open("{{ url('/puesto') }}"+"/"+id, 'scan');
}

function estado(est,id){
    $.get("{{ url('/puesto/estado/') }}/"+id+"/"+est, function(data){
        toast_ok('Cambio de estado',data.mensaje);
        //console.log('#estado_'+$(this).data('id'));
        $('#estado_'+data.id).removeClass();
        $('#estado_'+data.id).addClass('bg-'+data.color);
        $('#estado_'+data.id).html(data.label);
        $('#toolbutton').hide();
        animateCSS('#estado_'+data.id,'rubberBand');
    }) 
    .fail(function(err){
        console.log(err);
        toast_error('Error',err);
    });
}

function cancelar(id){
    $.get("{{ url('/reservas/cancelar_puesto/') }}/"+id, function(data){
        toast_ok('Cancelar reserva',data.mensaje);
        $('#nreserva_'+data.id).html();
        $('#freserva_'+data.id).html();
        //console.log('#estado_'+$(this).data('id'));    
    }) 
    .fail(function(err){
        console.log(err);
        toast_error('Error',err);
    });
}

function tabla_click(){
    $('#toolbutton').hide();
    //console.log('tabla_click');
   }

</script>