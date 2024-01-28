<div class="row">
    <div class="col-sm-3">
        <div class="card mb-4 mb-xl-3">
            <div class="d-flex align-items-stretch">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-pink px-4 text-white rounded-start">
                    <i class="fa-solid fa-flag fs-1"></i>
                </div>
                <div class="flex-grow-1 py-3 ms-3">
                    <h5 class="h2 mb-0 text-pink">{{ $countFestNac }}</h5>
                    <p class="mb-0">Fiestas nacionales</p>
                    @php
                        try{
                            $pct_nacionales=ceil((100*$countFestNac/$countFest)/5)*5;
                        }catch(Throwable  $e){
                            $pct_nacionales=0;
                        }
                    @endphp
                    <div class="progress"><div class="progress-bar bg-pink" role="progressbar" style="width: {{ $pct_nacionales }}%" aria-valuenow="{{ $pct_nacionales }}" aria-valuemin="0" aria-valuemax="100">{{ $pct_nacionales }}%</div></div>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card mb-4 mb-xl-3">
            <div class="d-flex align-items-stretch">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-green px-4 text-white rounded-start">
                    <i class="fa-solid fa-landmark fs-1"></i>
                </div>
                <div class="flex-grow-1 py-3 ms-3">
                    <h5 class="h2 mb-0 text-green">{{ $countFestReg }}</h5>
                    <p class="mb-0">Fiestas regionales</p>
                    @php
                        try{
                            $pct_regionales= ceil((100*$countFestReg/$countFest)/5)*5;
                        }catch(Throwable  $e){
                            $pct_regionales=0;
                        }
                    @endphp
                    <div class="progress"><div class="progress-bar bg-green" role="progressbar" style="width: {{ $pct_regionales }}%" aria-valuenow="{{ $pct_regionales }}" aria-valuemin="0" aria-valuemax="100">{{ $pct_regionales }}%</div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card mb-4 mb-xl-3">
            <div class="d-flex align-items-stretch">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-cyan px-4 text-white rounded-start">
                    <i class="fa-solid fa-city fs-1"></i>
                </div>
                <div class="flex-grow-1 py-3 ms-3">
                    <h5 class="h2 mb-0 text-cyan">{{ $countFestProv }}</h5>
                    <p class="mb-0">Fiestas provinciales</p>
                    @php
                        try{
                            $pct_provinciales=ceil((100*$countFestProv/$countFest)/5)*5;
                        }catch(Throwable  $e){
                            $pct_provinciales=0;
                        }
                    @endphp
                    <div class="progress"><div class="progress-bar bg-cyan" role="progressbar" style="width: {{ $pct_provinciales }}%" aria-valuenow="{{ $pct_provinciales }}" aria-valuemin="0" aria-valuemax="100">{{ $pct_provinciales }}%</div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="d-flex align-items-stretch">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-orange px-4 text-white rounded-start border-success">
                <i class="fa-solid fa-house fs-1"></i>
            </div>
            <div class="flex-grow-1 py-3 ms-3">
                <h5 class="h2 mb-0 text-orange">{{ $countFestLoc }}</h5>
                <p class="mb-0">Fiestas locales</p>
                @php
                    try{
                        $pct_locales=ceil((100*$countFestLoc/$countFest)/5)*5;
                    }catch(Throwable  $e){
                        $pct_locales=0;
                    }
                @endphp
                <div class="progress"><div class="progress-bar bg-orange" role="progressbar" style="width: {{ $pct_locales }}%" aria-valuenow="{{ $pct_locales }}" aria-valuemin="0" aria-valuemax="100">{{ $pct_locales }}%</div></div>
            </div>
        </div>
    </div>
</div>

