<style type="text/css">
    div.warning {
    color: saddlebrown;
    font-size: 75%;
    height: 0;
    }
    .text-editor input {
    font-family: "Courier New", Courier, monospace;
    text-align: center;
    font-size: 250%;
    width: 100%;
    background-color: #333333;
    border: 1px solid #cccccc;
    border-radius: 0.6em;
    color: #ffffff;
    padding-top: 0.075rem;
    }
    .text-editor input.invalid {
    border: 1px solid darkred;
    }
    .text-editor input.warning {
    border: 1px solid saddlebrown;
    }
    .text-editor input:focus {
    outline: none;
    }
    .text-editor input::-ms-clear {
    width: 0;
    height: 0;
    }
    .text-editor input::-moz-selection {
    color: #ffff80;
    background-color: rgba(255, 255, 128, 0.2);
    }
    .text-editor input::selection {
    color: #ffff80;
    background-color: rgba(255, 255, 128, 0.2);
    }
    .clickable {
    text-decoration: underline;
    cursor: pointer;
    -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
            user-select: none;
    }
    .part-explanation {
    font-size: 75%;
    color: #a8a8a8;
    height: 24em;
    }
    .part-explanation div {
    display: inline-block;
    vertical-align: top;
    margin: 0 1em 0 0;
    }
    .part-explanation .active {
    color: #ffff80;
    }
    .part-explanation .invalid {
    background-color: darkred;
    }
    .part-explanation .warning {
    background-color: saddlebrown;
    }
    .part-explanation .clickable {
    border-radius: 1em;
    padding: 0.1em 0.36em;
    }
    .part-explanation .clickable:last-child {
    margin: 0;
    }
    .human-readable {
    font-size: 200%;
    font-family: Georgia, serif;
    min-height: 2.2em;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: end;
        justify-content: flex-end;
    -ms-flex-line-pack: end;
        align-content: flex-end;
    -ms-flex-direction: column;
        flex-direction: column;
    margin-bottom: 0.2em;
    margin-top: 0.9em;
    }
    .human-readable .active {
    color: #ffff80;
    }
    .next-date {
    font-size: 75%;
    margin-left: 0.5em;
    }
    .tips {
    font-size: 75%;
    text-align: left;
    display: inline-block;
    vertical-align: top;
    margin-bottom: 3em;
    }
    .tips .title {
    font-weight: bold;
    }
    .example {
    text-align: right;
    font-size: 75%;
    margin-top: -1em;
    margin-bottom: 7px;
    }
</style>

<div class="card mb-2">
    <div id="crontabs" class="card-body">
        <p class="card-title-desc">Generar una expresion CRON que describirá la periodicidad del trabajo </p>
        <ul id="crongenerator" class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-2" role="tab">
                    <span class="d-block d-sm-none">Minutos</span>
                    <span class="d-none d-sm-block">Minutos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-3" role="tab" aria-selected="true">
                    <span class="d-block d-sm-none">Horas</span>
                    <span class="d-none d-sm-block">Horas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-4" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Dia</span>
                    <span class="d-none d-sm-block">Dia</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-5" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Mes</span>
                    <span class="d-none d-sm-block">Mes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-7" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Personalizado</span>
                    <span class="d-none d-sm-block">Personalizado</span>
                </a>
            </li>
        </ul>
        <div class="tab-content p-3 text-muted">
            <div class="tab-pane" id="tabs-2" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryMinute" name="cronMinute">
                        <label class="form-check-label" for="cronEveryMinute">Cada minuto</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMinuteIncrement" name="cronMinute">
                        <label class="form-check-label" for="cronMinuteIncrement">
                            Cada
                            <select id="cronMinuteIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 60; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                               
                            </select> minuto(s) empezando en el minuto
                            <select id="cronMinuteIncrementStart" style="width:50px;  display: inline-block;" class="form-control">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronMinuteSpecific" checked="checked" name="cronMinute">
                        <label for="cronMinuteSpecific">Minutos específicos (uno o varios)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 0; $i <= 59; $i++)
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                    <span style="width:10%">
                                        <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute{{ $i }}" value="{{ $i }}" >
                                        <label class="form-check-label" for="cronMinute{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                @endfor
                            </div>
                                
                        </div>
                    </div>
                    <div class="form-check mb-3 mt-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMinuteRange" name="cronMinute">
                        <label class="form-check-label" for="cronMinuteRange">
                            Cada minuto entre el minuto
                            <select id="cronMinuteRangeStart"  style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                                
                            </select>
                            y el minuto
                            <select id="cronMinuteRangeEnd"  style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-3" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryHour" name="cronHour">
                        <label class="form-check-label" for="cronEveryHour">Cada hora</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronHourIncrement" name="cronHour">
                        <label class="form-check-label" for="cronHourIncrement">
                            Cada
                            <select id="cronHourIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                                
                            </select> hora(s) empezando en la hora
                            <select id="cronHourIncrementStart" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 23; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronHourSpecific" checked="checked" name="cronHour">
                        <label for="cronHourSpecific">Hora específica (una o varias)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 0; $i <= 23; $i++)
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                    <span style="width:10%">
                                        <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour{{ $i }}" value="{{ $i }}" >
                                        <label class="form-check-label" for="cronHour{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3 mt-3">
                        <input class="form-check-input mt-2" type="radio" id="cronHourRange" name="cronHour">
                        <label class="form-check-label" for="cronHourRange">
                            Cada hora entre la hora
                            <select id="cronHourRangeStart" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 23; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            y la hora
                            <select id="cronHourRangeEnd" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 23; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-4" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryDay" name="cronDay" checked="">
                        <label class="form-check-label" for="cronEveryDay">Cada día</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDowIncrement" name="cronDay">
                        <label class="form-check-label" for="cronDowIncrement">
                            Cada
                            <select id="cronDowIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
                            </select> dia(s) empezando en el
                            <select id="cronDowIncrementStart" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="MON">Lunes</option>
                                <option value="TUE">Martes</option>
                                <option value="WED">Miercoles</option>
                                <option value="THU">Jueves</option>
                                <option value="FRI">Viernes</option>
                                <option value="SAT">Sabado</option>
                                <option value="SUN">Domingo</option>
                            </select>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDomIncrement" name="cronDay">
                        <label class="form-check-label" for="cronDomIncrement">
                            Cada
                            <select id="cronDomIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select> dia(s) empezando en el dia
                            <select id="cronDomIncrementStart" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            del mes
                        </label>
                    </div>
                    <div class="mb-3">
                        <input class="form-check-input" type="radio" id="cronDowSpecific" name="cronDay">
                        <label for="cronDowSpecific">Día específico de la semana (uno o varios)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowMon" value="MON">
                                    <label class="form-check-label" for="cronDowMon">Lunes</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowTue" value="TUE">
                                    <label class="form-check-label" for="cronDowTue">Martes</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowWed" value="WED">
                                    <label class="form-check-label" for="cronDowWed">Miercoles</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowThu" value="THU">
                                    <label class="form-check-label" for="cronDowThu">Jueves</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowFri" value="FRI">
                                    <label class="form-check-label" for="cronDowFri">Viernes</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowSat" value="SAT">
                                    <label class="form-check-label" for="cronDowSat">Sabado</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowSun" value="SUN" >
                                    <label class="form-check-label" for="cronDowSun">Domingo</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input  class="form-check-input"  type="radio" id="cronDomSpecific" name="cronDay">
                        <label for="cronDomSpecific">Días específico del mes (uno o varios)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 1; $i <= 31; $i++)
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                    <span style="width:10%">
                                        <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom{{ $i }}" value="{{ $i }}" >
                                        <label class="form-check-label" for="cronDom{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastDayOfMonth" name="cronDay">
                        <label class="form-check-label" for="cronLastDayOfMonth">El ultimo día del mes</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastWeekdayOfMonth" name="cronDay">
                        <label class="form-check-label" for="cronLastWeekdayOfMonth">El último dia entre semana del mes</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronLastSpecificDom" name="cronDay">
                        <label class="form-check-label" for="cronLastSpecificDom">
                            El ultimo
                            <select id="cronLastSpecificDomDay" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="MON">Lunes</option>
                                <option value="TUE">Martes</option>
                                <option value="WED">Miercoles</option>
                                <option value="THU">Jueves</option>
                                <option value="FRI">Viernes</option>
                                <option value="SAT">Sabado</option>
                                <option value="SUN">Domingo</option>
                            </select>
                            del mes
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDaysBeforeEom" name="cronDay">
                        <label class="form-check-label" for="cronDaysBeforeEom">
                            En los ultimos
                            <select id="cronDaysBeforeEomMinus" style="width:50px; display: inline-block;"  class="form-control">
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            dia(s) antes del fin de mes
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDaysNearestWeekdayEom" name="cronDay">
                        <label class="form-check-label" for="cronDaysNearestWeekdayEom">
                            El días entre semana (Lunes to Viernes) mas cercano al
                            <select id="cronDaysNearestWeekday" style="width:50px; display: inline-block;"  class="form-control">
                                @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ lz($i,2) }}</option>
                            @endfor
                            </select>
                            del mes
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronNthDay" name="cronDay">
                        <label class="form-check-label" for="cronNthDay">
                            En el
                            <select id="cronNthDayNth" style="width:100px; display: inline-block; "  class="form-control">
                                <option value="1">primer</option>
                                <option value="2">segundo</option>
                                <option value="3">tercer</option>
                                <option value="4">cuarto</option>
                                <option value="5">quinto</option>
                            </select>
                            <select id="cronNthDayDay" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="MON">Lunes</option>
                                <option value="TUE">Martes</option>
                                <option value="WED">Miercoles</option>
                                <option value="THU">Jueves</option>
                                <option value="FRI">Viernes</option>
                                <option value="SAT">Sabado</option>
                                <option value="SUN">Domingo</option>
                            </select>
                            del mes
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-5" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryMonth" name="cronMonth" checked="">
                        <label class="form-check-label" for="cronEveryMonth">Cada mes</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMonthIncrement" name="cronMonth">
                        <label class="form-check-label" for="cronMonthIncrement">
                            Cada
                            <select id="cronMonthIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select> mes(es) empezando en
                            <select id="cronMonthIncrementStart" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronMonthSpecific" name="cronMonth">
                        <label for="cronMonthSpecific">Mes específico (uno o varios)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center mb-3">
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth1" value="JAN" selected="">
                                    <label class="form-check-label" for="cronMonth1">ENE</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth2" value="FEB">
                                    <label class="form-check-label" for="cronMonth2">FEB</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth3" value="MAR">
                                    <label class="form-check-label" for="cronMonth3">MAR</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth4" value="APR">
                                    <label class="form-check-label" for="cronMonth4">ABR</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth5" value="MAY">
                                    <label class="form-check-label" for="cronMonth5">MAY</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth6" value="JUN">
                                    <label class="form-check-label" for="cronMonth6">JUN</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth7" value="JUL">
                                    <label class="form-check-label" for="cronMonth7">JUL</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth8" value="AUG">
                                    <label class="form-check-label" for="cronMonth8">AGO</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth9" value="SEP">
                                    <label class="form-check-label" for="cronMonth9">SEP</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth10" value="OCT">
                                    <label class="form-check-label" for="cronMonth10">OCT</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth11" value="NOV">
                                    <label class="form-check-label" for="cronMonth11">NOV</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth12" value="DEC">
                                    <label class="form-check-label" for="cronMonth12">DIC</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMonthRange" name="cronMonth">
                        <label class="form-check-label" for="cronMonthRange">
                            Cada mes entre
                            <select id="cronMonthRangeStart" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1" selected>Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                            y
                            <select id="cronMonthRangeEnd" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1" selected>Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-7" role="tabpanel">
                <div>
                    <div class="example">
                      <span class="clickable" id="borrame">borrar</span>
                    </div>
                    <div class="text-editor">
                        <input id="cron_input" type="text" class="" value="{{ $val_periodo }}">
                    </div>
                    <div class="warning"></div>
                    <div class="part-explanation">
                      <div class="cron-parts w-100 text-center">
                        <div style="left: 200px">
                            <div class="clickable" data-div="min">min</div>
                            <div class="clickable" data-div="hor">hora</div>
                            <div class="clickable" data-div="dia">dia<br>(mes)</div>
                            <div class="clickable" data-div="mes">mes</div>
                            <div class="clickable" data-div="sem">dia<br>(sem)</div>
                           
                        </div>
                    </div>
                    <div class="row w-100">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <table>
                                <tbody>
                                  <tr><td>*</td><td>cualquier valor</td></tr>
                                  <tr><td>,</td><td>separador de lista de valores</td></tr>
                                  <tr><td>-</td><td>rango de valores</td></tr>
                                  <tr><td>/</td><td>valores de paso</td></tr>
                                </tbody>
                                <tbody style="display: none" class="min">
                                  <tr><td nowrap><b>0-59</b></td><td>valores permitidos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="hor">
                                  <tr><td nowrap><b>0-23</td><td>valores permitidos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="dia">
                                  <tr><td nowrap><b>1-31</td><td>valores permitidos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="mes">
                                  <tr><td nowrap><b>1-12</td><td>valores permitidos</td></tr>
                                  <tr><td nowrap><b>JAN-DEC</td><td>Valores alternativos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="sem">
                                  <tr><td nowrap><b>0-6</td><td>valores permitidos</td></tr>
                                  <tr><td nowrap><b>SUN-SAT</td><td>Valores alternativos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="ano">
                                    <tr><td nowrap><b>2022-2040</td><td>valores permitidos</td></tr>
                                  </tbody>
                            </table>
                        </div>
                    </div>
                      
                    </div>
                    <div style="margin-bottom: 10px"></div>
                  </div>
            </div>
        </div>

        <div>
            <h3 class="mb-2" style="color:#1b6ca8;text-align: center;">- Cron Expression -</h3>
            <h2 class="cronResult mb-2" style="text-align: center;background: aliceblue;padding: 10px;">{{ $val_periodo }}</h2>
            <h4 class="cronHuman mb-2" style="text-align: center;background: #baf2e7;padding: 10px;"></h4>
            <table class="table" style="text-align:center;">
                <thead>
                    <tr>
                        {{-- <th>Segundos</th> --}}
                        <th>Minutos</th>
                        <th>Horas</th>
                        <th>Dia mes</th>
                        <th>Mes</th>
                        <th>Dia semana</th>
                    </tr>
                </thead>
                <tbody style="font-weight: 400;font-size: large; overflow-wrap: anywhere;">
                    <tr>
                        {{-- <td><span id="cronResultSecond">0</span></td> --}}
                        <td><span id="cronResultMinute">*</span></td>
                        <td><span id="cronResultHour">*</span></td>
                        <td><span id="cronResultDom">?</span></td>
                        <td><span id="cronResultMonth">*</span></td>
                        <td><span id="cronResultDow">*</span></td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div class="row" id="next_cron"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- end card body -->
</div>

<script>
    function update_human(){
        $('#val_periodo').val($('.cronResult').html());
        $('.cronHuman').html((cronstrue.toString($('.cronResult').html(),{ use24HourTimeFormat: true,locale: "es",dayOfWeekStartIndexZero: false })));
        
    }

    function siguientes_cron(){
        var cron = $('.cronResult').html();
        var cronSched = later.parse.cron(cron);
        later.date.localTime();
        moment.localeData("{{ config('app.lang') }}");
        siguientes=later.schedule(cronSched).next(12);
        $('#next_cron').empty();
        i=1;
        siguientes.forEach(function(element) {
            $('#next_cron').append('<div class="col-md-4"><span class="text-info">['+i+']  </span>'+moment(element).locale("{{ config('app.lang') }}").format('DD/MM/YYYY H:mm')+'</div>');
            i++;
        });
    }
    $(function () {
        $('#crontabs input, #crontabs select').not( "#cron_input" ).change(_FF.cron);
        //_FF.cron();
        cronstrue = window.cronstrue;
        update_human();
        siguientes_cron();
    });
    $('.clickable').hover(function(){
        $('.'+$(this).data('div')).toggle();
    })

    $('#cron_input').keyup(function(){
        console.log($(this).val());
        $('.cronResult').html($(this).val());
        partes =$(this).val().split(" ");
        $('#cronResultMinute').html(partes[0]);
        $('#cronResultHour').html(partes[1]);
        $('#cronResultDom').html(partes[2]);
        $('#cronResultMonth').html(partes[3]);
        $('#cronResultDow').html(partes[4]);
        update_human();
        siguientes_cron();
    })

    $('#borrame').click(function(){
        $('.cronResult').html("");
        $('.cronHuman').html("");
        $('#val_periodo').val("");
        $('#cron_input').val("");
    })

    $('#cron_input').change(function(){
        $('.cronHuman').html("");
        update_human();
        siguientes_cron();
    })

</script>