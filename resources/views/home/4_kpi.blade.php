@php
 //Datos de KPI
    use App\Models\puestos;
    use App\Models\edificios;
    use App\Models\plantas;
    $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('id_cliente',session('CL')['id_cliente']);
            }
        })
        ->get();

    $puestos_si=puestos::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        } else {
            $q->where('id_cliente',session('CL')['id_cliente']);
        }
    })
    ->where('id_estado',1);
    
    $edificios=edificios::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        } else {
            $q->where('id_cliente',session('CL')['id_cliente']);
        }
    });
    
    $plantas=plantas::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        } else {
            $q->where('id_cliente',session('CL')['id_cliente']);
        }
    });

    

    try{
        $pct_completado=(100*$puestos_si->count()/$puestos->count());
    } catch(\Throwable $e){
        $pct_completado=0;
    }

    //Datos de donut chart   

@endphp
<div class="row mt-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card bg-purple text-white mb-3 mb-xl-3">
            <div class="card-body py-3 d-flex align-items-stretch">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                    <i class="fad fa-building fa-2x"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="h2 mb-0">{{ $edificios->count() }}</h5>
                    <p class="mb-0">Edificios</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card bg-cyan text-white mb-3 mb-xl-3">
            <div class="card-body py-3 d-flex align-items-stretch">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                    <i class="fad fa-layer-group fa-2x"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="h2 mb-0">{{ $plantas->count() }}</h5>
                    <p class="mb-0">Plantas</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card bg-pink text-white mb-3 mb-xl-3">
            <div class="card-body py-3 d-flex align-items-stretch">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                    <i class="fad fa-desktop-alt fa-2x"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="h2 mb-0">{{ $puestos->count() }}</h5>
                    <p class="mb-0">Puestos</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card bg-{{ color_porcentaje($pct_completado) }} text-white mb-3 mb-xl-3">
            <div class="card-body py-3 d-flex align-items-stretch">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                    <i class="fad fa-check fa-2x"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="h2 mb-0">{{ round($pct_completado) }}%</h5>
                    <p class="mb-0">Operativos</p>
                </div>
            </div>
        </div>
    </div>
</div>