@php
    $clientes=\DB::table('clientes')
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.id_cliente',clientes());
            }
        })
        ->orderby('nom_cliente')
        ->get();
@endphp

<div class="btn-group">
    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"  title="Cliente" style="padding: 1px 5px 1px 5px">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'. session('CL')['img_logo']) }}" style="height: 25px" alt="" class="Nifty logo rounded">  <span id="des_tipo" class="ml-3">{{ session('CL')['nom_cliente'] }}</span> <i class="dropdown-caret"></i>
    </button>
    <ul class="dropdown-menu" id="dropdown-acciones">
        @foreach($clientes as $c)
            <li class="dropdown-item combo_cliente" data-id="{{ $c->id_cliente }}" ><a href="#"  data-id="{{ $c->id_cliente }}" class="btn_tipo_check text-primary combo_cliente"> <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$c->img_logo) }}" style="height: 25px" alt="" class="Nifty logo rounded"> {{ $c->nom_cliente }} </a></li>
        @endforeach
    </ul>
</div>

<script>
       document.addEventListener("DOMContentLoaded",function(){
            //Combo para cambiar el clietne en caliente en algunas vistas
            $('.combo_cliente').click(function(){
                var id=$(this).data('id');
                $.ajax({
                    url: "{{ route('cliente.menu') }}",
                    type: "POST",
                    data: {id:id,_token: "{{ csrf_token() }}"},
                    success: function(data){
                        window.location.reload();
                    }
                });
            });
        });
       
       
</script>