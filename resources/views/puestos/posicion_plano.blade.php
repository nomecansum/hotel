@php

@endphp
<style>
      @keyframes glowing {
        0% {
          color: #2ba805;
        }
        25% {
          color: #f0be1a;
        }
        50% {
          color: #ffee02;
        }
        75% {
          color: #ff3b38;
        }
      }
      .glow {
        animation: glowing 1300ms infinite;
      }
</style>
@if(isset($puesto->img_plano))


    <div class="row container"  data-posiciones="" data-id="{{ $puesto->id_planta }}" style="width: 100%; padding: 0px 0px 0px 0px">
        <div class="col-md-12">
            <img id="plano{{ $puesto->id_planta }}" src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$puesto->img_plano) }}" style="width: 100%" id="img_fondo{{ $puesto->id_planta }}">
            @php
                $left=0;
                $top=0;
    
            @endphp
    
                @php
                    $title= nombrepuesto($puesto);
                    $borde="";
    
                    $color=$puesto->val_color?$puesto->val_color:"#dff9d2";
                    $font_color="#fff";
                @endphp
                <div id="puesto{{ $puesto->id_puesto}}" style="top: {{ $top }}px; left: {{ $left }}px; position: absolute">
                    <i class="fa-solid fa-location-dot fa-3x text-danger glow"></i>
                </div>
                
        </div>
    </div>
    <script>
        
        function posicionar(){
            //console.log("plano: "+h_plano+"x"+w_plano");
            puesto=$('#puesto{{ $puesto->id_puesto }}');
            h_plano=$('#plano{{ $puesto->id_planta }}').height();
            w_plano=$('#plano{{ $puesto->id_planta }}').width();
            console.log("plano: "+h_plano+"x"+w_plano);

            try{ //Pra el caso de antiguos que no tienen los offset puestos
                puesto.css('top',(h_plano*{{ $puesto->offset_top }}/100)-24);
                puesto.css('left',(w_plano*{{ $puesto->offset_left }}/100)+10);
                console.log("puesto: "+(h_plano*{{ $puesto->offset_top }}/100)+"x"+(w_plano*{{ $puesto->offset_left }})/100)
            } catch (e) {
                console.log(e);
            }
            

            // $('#puesto{{ $puesto->id_puesto }}').css({top: t_puesto, left: l_puesto, position: 'absolute'}); 
        }
           
        $(function(){
            setTimeout(posicionar,100);
        })         

    </script>
@else
    <div id="plano{{ $puesto->id_planta }}" data-posiciones="" data-id="{{ $puesto->id_planta }}">
        No hay plano de la planta
    </div>
@endif