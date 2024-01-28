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

    .caja_elol {
    border: 2px solid red;
    }
</style>

<div class="card mb-2">
    <div id="crontabs" class="card-body">
        <p class="card-title-desc">{{ __('tareas.descripcion_cron') }} </p>
        <nav id="crongenerator" class="nav nav-tabs nav-justified" role="tablist">
            <button class="nav-link" data-bs-toggle="tab" href="#tabs-2" role="tab" aria-controls="tabs-2"  data-bs-toggle="tab"  aria-selected="false">
                <span class="d-block d-sm-none">{{ __('general.minuto') }}</span>
                <span class="d-none d-sm-block">{{ __('general.minuto') }}</span>
            </button>
            <button class="nav-link active" data-bs-toggle="tab" href="#tabs-3" role="tab" aria-controls="tabs-3"   data-bs-toggle="tab"  aria-selected="true">
                <span class="d-block d-sm-none">{{ __('general.hora') }}</span>
                <span class="d-none d-sm-block">{{ __('general.hora') }}</span>
            </button>
            <button class="nav-link" data-bs-toggle="tab" href="#tabs-4" role="tab"  aria-controls="tabs-4"   data-bs-toggle="tab"  aria-selected="false">
                <span class="d-block d-sm-none">{{ __('general.dia') }}</span>
                <span class="d-none d-sm-block">{{ __('general.dia') }}</span>
            </button>
            <button class="nav-link" data-bs-toggle="tab" href="#tabs-5" role="tab"  aria-controls="tabs-5"   data-bs-toggle="tab"  aria-selected="false">
                <span class="d-block d-sm-none">{{ __('general.mes') }}</span>
                <span class="d-none d-sm-block">{{ __('general.mes') }}</span>
            </button>
            <button class="nav-link" data-bs-toggle="tab" href="#tabs-7" role="tab"  aria-controls="tabs-7"   data-bs-toggle="tab"  aria-selected="false">
                <span class="d-block d-sm-none">{{ __('tareas.custom') }}</span>
                <span class="d-none d-sm-block">{{ __('tareas.custom') }}</span>
            </button>
        </nav>
        <div class="tab-content p-3 text-muted">
            <div class="tab-pane" id="tabs-2" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryMinute" name="cronMinute" checked="checked" >
                        <label class="form-check-label" for="cronEveryMinute">{{  __('tareas.cada_minuto')  }}</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMinuteIncrement" name="cronMinute">
                        <label class="form-check-label" for="cronMinuteIncrement">
                            {{ __('tareas.cada') }}
                            <select id="cronMinuteIncrementIncrement" style="width:80px; display: inline-block; "  data-target="cronMinuteIncrement" class="form-control changeme">
                                @for ($i = 1; $i <= 60; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                               
                            </select> {{ __('tareas.cada_minuto_empezando_en_el_minuto') }}
                            <select id="cronMinuteIncrementStart" style="width:80px;  display: inline-block;" data-target="cronMinuteIncrement" class="form-control changeme">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}"  >{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronMinuteSpecific" name="cronMinute">
                        <label for="cronMinuteSpecific">{{ __('general.minutos') }} {{ __('tareas.especificos_uno_o_varios') }}</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 0; $i <= 59; $i++)
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                    <span style="width:10%">
                                        <input class="form-check-input changeme" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute{{ $i }}" value="{{ $i }}"  data-target="cronMinuteSpecific">
                                        <label class="form-check-label" for="cronMinute{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                @endfor
                            </div>
                                
                        </div>
                    </div>
                    <div class="form-check mb-3 mt-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMinuteRange" name="cronMinute">
                        <label class="form-check-label" for="cronMinuteRange">
                            {{ __('tareas.cada_minuto_entre_el_minuto') }}
                            <select id="cronMinuteRangeStart"  style="width:80px; display: inline-block; "   data-target="cronMinuteRange" class="form-control changeme">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                                
                            </select>
                            {{ __('tareas.y_el_minuto') }}
                            <select id="cronMinuteRangeEnd"  style="width:80px; display: inline-block; "   data-target="cronMinuteRange" class="form-control changeme">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane active" id="tabs-3" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryHour" name="cronHour" checked="checked">
                        <label class="form-check-label" for="cronEveryHour">{{ __('tareas.cada_hora') }}</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronHourIncrement" name="cronHour">
                        <label class="form-check-label" for="cronHourIncrement">
                            {{ __('tareas.cada') }}
                            <select id="cronHourIncrementIncrement" style="width:80px; display: inline-block; "  class="form-control changeme"  data-target="cronHourIncrement" >
                                @for ($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                                
                            </select> {{ __('tareas.hora_empezando_en_la_hora') }}
                            <select id="cronHourIncrementStart" style="width:80px; display: inline-block; "  class="form-control changeme"  data-target="cronHourIncrement" >
                                @for ($i = 0; $i <= 23; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronHourSpecific" name="cronHour">
                        <label for="cronHourSpecific">{{ __('tareas.hora_especifica_una_o_varias') }}</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 0; $i <= 23; $i++)
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                    <span style="width:10%">
                                        <input class="form-check-input changeme" name="cronHourSpecificSpecific" type="checkbox" id="cronHour{{ $i }}" value="{{ $i }}" data-target="cronHourSpecific"  >
                                        <label class="form-check-label" for="cronHour{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3 mt-3">
                        <input class="form-check-input mt-2" type="radio" id="cronHourRange" name="cronHour">
                        <label class="form-check-label" for="cronHourRange">
                            {{ __('tareas.cada_hora_entre_la_hora') }}
                            <select id="cronHourRangeStart" style="width:80px; display: inline-block; "  class="form-control changeme"  data-target="cronHourRange" >
                                @for ($i = 0; $i <= 23; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            {{ __('tareas.y_la_hora') }}
                            <select id="cronHourRangeEnd" style="width:80px; display: inline-block; "  class="form-control changeme"  data-target="cronHourRange" >
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
                        <input class="form-check-input" type="radio" id="cronEveryDay" name="cronDay" checked="checked">
                        <label class="form-check-label" for="cronEveryDay">{{ __('tareas.cada_dia') }}</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDowIncrement" name="cronDay">
                        <label class="form-check-label" for="cronDowIncrement">
                            {{ __('tareas.cada') }}
                            <select id="cronDowIncrementIncrement" style="width:80px; display: inline-block; "  class="form-control changeme" data-target="cronDowIncrement" >
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
                            </select> {{ __('general.dia') }} {{ __('tareas.s_empezando_el') }}{{ __('tareas.s_empezando_el') }}
                            <select id="cronDowIncrementStart" style="width:125px; display: inline-block; "  class="form-control changeme"  data-target="cronDowIncrement" >
                                <option value="MON">{{__('general.lunes')}}</option>
                                <option value="TUE">{{__('general.martes')}}</option>
                                <option value="WED">{{__('general.miercoles')}}</option>
                                <option value="THU">{{__('general.jueves')}}</option>
                                <option value="FRI">{{__('general.viernes')}}</option>
                                <option value="SAT">{{__('general.sabado')}}</option>
                                <option value="SUN">{{__('general.domingo')}}</option>
                            </select>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDomIncrement" name="cronDay">
                        <label class="form-check-label" for="cronDomIncrement">
                            {{ __('tareas.cada') }}
                            <select id="cronDomIncrementIncrement" style="width:80px; display: inline-block; "  class="form-control changeme"  data-target="cronDomIncrement" >
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select> {{ __('general.dia') }}{{ __('tareas.s_empezando_el') }}{{ __('general.dia') }}
                            <select id="cronDomIncrementStart" style="width:80px; display: inline-block; "  class="form-control changeme"  data-target="cronDomIncrement" >
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            {{ __('general.del') }} {{ __('general.mes') }}
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronDowSpecific" name="cronDay">
                        <label for="cronDowSpecific">{{ __('tareas.dia_especficico_de_la_semana') }}</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronDowSpecificSpecific" type="checkbox" id="cronDowMon" value="MON"  data-target="cronDowSpecific" >
                                    <label class="form-check-label" for="cronDowMon">{{__('general.lunes2')}}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronDowSpecificSpecific" type="checkbox" id="cronDowTue" value="TUE"  data-target="cronDowSpecific" >
                                    <label class="form-check-label" for="cronDowTue">{{__('general.martes2')}}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronDowSpecificSpecific" type="checkbox" id="cronDowWed" value="WED"  data-target="cronDowSpecific" >
                                    <label class="form-check-label" for="cronDowWed">{{__('general.miercoles2')}}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronDowSpecificSpecific" type="checkbox" id="cronDowThu" value="THU"  data-target="cronDowSpecific" >
                                    <label class="form-check-label" for="cronDowThu">{{__('general.jueves2')}}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronDowSpecificSpecific" type="checkbox" id="cronDowFri" value="FRI"  data-target="cronDowSpecific" >
                                    <label class="form-check-label" for="cronDowFri">{{__('general.viernes2')}}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronDowSpecificSpecific" type="checkbox" id="cronDowSat" value="SAT"  data-target="cronDowSpecific" >
                                    <label class="form-check-label" for="cronDowSat">{{__('general.sabado2')}}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronDowSpecificSpecific" type="checkbox" id="cronDowSun" value="SUN"  data-target="cronDowSpecific" >
                                    <label class="form-check-label" for="cronDowSun">{{__('general.domingo2')}}</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input  class="form-check-input"  type="radio" id="cronDomSpecific" name="cronDay">
                        <label for="cronDomSpecific">{{ __('tareas.dias_especificos_del') }} ({{ __('tareas.uno_o_varios') }})</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 1; $i <= 31; $i++)
                                    
                                    <span style="width:9%">
                                        <input class="form-check-input changeme" name="cronDomSpecificSpecific" type="checkbox" id="cronDom{{ $i }}" value="{{ $i }}"  data-target="cronDomSpecific" >
                                        <label class="form-check-label" for="cronDom{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastDayOfMonth" name="cronDay">
                        <label class="form-check-label" for="cronLastDayOfMonth">{{ __('tareas.ultimo_dia_del_mes') }}</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastWeekdayOfMonth" name="cronDay">
                        <label class="form-check-label" for="cronLastWeekdayOfMonth">{{ __('tareas.el_ultimo_dia_entre_semana_del_mes') }}</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronLastSpecificDom" name="cronDay">
                        <label class="form-check-label" for="cronLastSpecificDom">
                            {{ __('tareas.el_ultimo') }}
                            <select id="cronLastSpecificDomDay" style="width:125px; display: inline-block; "  class="form-control changeme"  data-target="cronLastSpecificDom" >
                                <option value="MON">{{__('general.lunes')}}</option>
                                <option value="TUE">{{__('general.martes')}}</option>
                                <option value="WED">{{__('general.miercoles')}}</option>
                                <option value="THU">{{__('general.jueves')}}</option>
                                <option value="FRI">{{__('general.viernes')}}</option>
                                <option value="SAT">{{__('general.sabado')}}</option>
                                <option value="SUN">{{__('general.domingo')}}</option>
                            </select>
                            {{ __('general.del') }} {{ __('general.mes') }}
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDaysBeforeEom" name="cronDay">
                        <label class="form-check-label" for="cronDaysBeforeEom">
                            {{ __('tareas.en_los_ultimos') }}
                            <select id="cronDaysBeforeEomMinus" style="width:80px; display: inline-block;"  class="form-control changeme"  data-target="cronDaysBeforeEom" >
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            {{ __('tareas.dias_antes_del_fin_del_mes') }}
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDaysNearestWeekdayEom" name="cronDay">
                        <label class="form-check-label" for="cronDaysNearestWeekdayEom">
                           {{__('tareas.el_dia_entre_semana') }} ({{__('general.lunes')}} - {{__('general.viernes')}}) {{ __('tareas.mas_cercano_al') }}
                            <select id="cronDaysNearestWeekday" style="width:80px; display: inline-block;"  class="form-control changeme"  data-target="cronDaysNearestWeekdayEom" >
                                @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ lz($i,2) }}</option>
                            @endfor
                            </select>
                            {{ __('general.del') }} {{ __('general.mes') }}
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronNthDay" name="cronDay">
                        <label class="form-check-label" for="cronNthDay">
                            {{ __('general.en_el') }}
                            <select id="cronNthDayNth" style="width:100px; display: inline-block; "  class="form-control changeme"  data-target="cronNthDay" >
                                <option value="1">{{ __('tareas.primer') }}</option>
                                <option value="2">{{ __('tareas.segundo') }}</option>
                                <option value="3">{{ __('tareas.tercer') }}</option>
                                <option value="4">{{ __('tareas.cuarto') }}</option>
                                <option value="5">{{ __('tareas.quinto') }}</option>
                            </select>
                            <select id="cronNthDayDay" style="width:125px; display: inline-block; "  class="form-control changeme"  data-target="cronNthDay" >
                                <option value="MON">{{__('general.lunes')}}</option>
                                <option value="TUE">{{__('general.martes')}}</option>
                                <option value="WED">{{__('general.miercoles')}}</option>
                                <option value="THU">{{__('general.jueves')}}</option>
                                <option value="FRI">{{__('general.viernes')}}</option>
                                <option value="SAT">{{__('general.sabado')}}</option>
                                <option value="SUN">{{__('general.domingo')}}</option>
                            </select>
                            {{ __('general.del') }}  {{ __('general.mes') }}
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-5" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryMonth" name="cronMonth" checked="checked">
                        <label class="form-check-label" for="cronEveryMonth">{{ __('tareas.cada') }} {{ __('general.mes') }}</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMonthIncrement" name="cronMonth">
                        <label class="form-check-label" for="cronMonthIncrement">
                            {{ __('tareas.cada') }}
                            <select id="cronMonthIncrementIncrement" style="width:80px; display: inline-block; "  class="form-control changeme"   data-target="cronMonthIncrement">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select> {{ __('tareas.meses_empezando') }}
                            <select id="cronMonthIncrementStart" style="width:125px; display: inline-block; "  class="form-control changeme"   data-target="cronMonthIncrement">
                                <option value="1">{{ __('general.enero') }}</option>
                                <option value="2">{{ __('general.febrero') }}</option>
                                <option value="3">{{ __('general.marzo') }}</option>
                                <option value="4">{{ __('general.abril') }}</option>
                                <option value="5">{{ __('general.mayo') }}</option>
                                <option value="6">{{ __('general.junio') }}</option>
                                <option value="7">{{ __('general.julio') }}</option>
                                <option value="8">{{ __('general.agosto') }}</option>
                                <option value="9">{{ __('general.septiembre') }}</option>
                                <option value="10">{{ __('general.octubre') }}</option>
                                <option value="11">{{ __('general.noviembre') }}</option>
                                <option value="12">{{ __('general.diciembre') }}</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronMonthSpecific" name="cronMonth">
                        <label for="cronMonthSpecific">{{ __('tareas.mes_especifico_uno_o_varios') }} </label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center mb-3">
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth1" value="JAN" selected=""  data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth1">{{ __('general.enero') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth2" value="FEB"  data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth2">{{ __('general.febrero') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth3" value="MAR" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth3">{{ __('general.marzo') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth4" value="APR" data-target="cronMonthSpecific"
                                    <label class="form-check-label" for="cronMonth4">{{ __('general.abril') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth5" value="MAY" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth5">{{ __('general.mayo') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth6" value="JUN" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth6">{{ __('general.junio') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth7" value="JUL" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth7">{{ __('general.julio') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth8" value="AUG" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth8">{{ __('general.agosto') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth9" value="SEP" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth9">{{ __('general.septiembre') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth10" value="OCT" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth10">{{ __('general.octubre') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth11" value="NOV" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth11">{{ __('general.noviembre') }}</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input changeme" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth12" value="DEC" data-target="cronMonthSpecific">
                                    <label class="form-check-label" for="cronMonth12">{{ __('general.diciembre') }}</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMonthRange" name="cronMonth">
                        <label class="form-check-label" for="cronMonthRange">
                            {{ __('tareas.cada_mes_entre') }}
                            <select id="cronMonthRangeStart" style="width:125px; display: inline-block; "  class="form-control changeme" data-target="cronMonthRange">
                                <option value="1" selected>{{ __('general.enero') }}</option>
                                <option value="2">{{ __('general.febrero') }}</option>
                                <option value="3">{{ __('general.marzo') }}</option>
                                <option value="4">{{ __('general.abril') }}</option>
                                <option value="5">{{ __('general.mayo') }}</option>
                                <option value="6">{{ __('general.junio') }}</option>
                                <option value="7">{{ __('general.julio') }}</option>
                                <option value="8">{{ __('general.agosto') }}</option>
                                <option value="9">{{ __('general.septiembre') }}</option>
                                <option value="10">{{ __('general.octubre') }}</option>
                                <option value="11">{{ __('general.noviembre') }}</option>
                                <option value="12">{{ __('general.diciembre') }}</option>
                            </select>
                            {{ __('general.y') }}
                            <select id="cronMonthRangeEnd" style="width:125px; display: inline-block; "  class="form-control changeme" data-target="cronMonthRange">
                                <option value="1" selected>{{ __('general.enero') }}</option>
                                <option value="2">{{ __('general.febrero') }}</option>
                                <option value="3">{{ __('general.marzo') }}</option>
                                <option value="4">{{ __('general.abril') }}</option>
                                <option value="5">{{ __('general.mayo') }}</option>
                                <option value="6">{{ __('general.junio') }}</option>
                                <option value="7">{{ __('general.julio') }}</option>
                                <option value="8">{{ __('general.agosto') }}</option>
                                <option value="9">{{ __('general.septiembre') }}</option>
                                <option value="10">{{ __('general.octubre') }}</option>
                                <option value="11">{{ __('general.noviembre') }}</option>
                                <option value="12">{{ __('general.diciembre') }}</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-7" role="tabpanel">
                <div>
                    <div class="example">
                      <span class="clickable" id="borrame">{{ __('general.borrar') }}</span>
                    </div>
                    <div class="text-editor">
                        <input id="cron_input" type="text" class="cron_edit" value="">
                    </div>
                    <div class="warning"></div>
                    <div class="part-explanation">
                      <div class="cron-parts w-100 text-center">
                        <div style="left: 200px">
                            <div class="clickable" data-div="min">{{ __('general.min') }}</div>
                            <div class="clickable" data-div="hor">{{ __('general.hora') }}</div>
                            <div class="clickable" data-div="{{ __('general.dia') }}">{{ __('general.dia') }}<br>({{ __('general.mes') }})</div>
                            <div class="clickable" data-div="{{ __('general.mes') }}">{{ __('general.mes') }}</div>
                            <div class="clickable" data-div="sem">{{ __('general.dia') }}<br>({{ __('general.sem') }})</div>
                           
                        </div>
                    </div>
                    <div class="row w-100">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <table>
                                <tbody>
                                  <tr><td>*</td><td>{{ __('tareas.cualquier_valor') }}</td></tr>
                                  <tr><td>,</td><td>{{ __('tareas.separador_de_lista_de_valores') }}</td></tr>
                                  <tr><td>-</td><td>{{ __('tareas.rango_de_valores') }}</td></tr>
                                  <tr><td>/</td><td>{{ __('tareas.valores_de_paso') }}</td></tr>
                                </tbody>
                                <tbody style="display: none" class="min">
                                  <tr><td nowrap><b>0-59</b></td><td>{{ __('tareas.valores_permitidos') }}</td></tr>
                                </tbody>
                                <tbody style="display: none" class="hor">
                                  <tr><td nowrap><b>0-23</td><td>{{ __('tareas.valores_permitidos') }}</td></tr>
                                </tbody>
                                <tbody style="display: none" class="{{ __('general.dia') }}">
                                  <tr><td nowrap><b>1-31</td><td>{{ __('tareas.valores_permitidos') }}</td></tr>
                                </tbody>
                                <tbody style="display: none" class="{{ __('general.mes') }}">
                                  <tr><td nowrap><b>1-12</td><td>{{ __('tareas.valores_permitidos') }}</td></tr>
                                  <tr><td nowrap><b>JAN-DEC</td><td>{{ __('tareas.valores_alternativos') }}</td></tr>
                                </tbody>
                                <tbody style="display: none" class="sem">
                                  <tr><td nowrap><b>0-6</td><td>{{ __('tareas.valores_permitidos') }}</td></tr>
                                  <tr><td nowrap><b>SUN-SAT</td><td>{{ __('tareas.valores_alternativos') }}</td></tr>
                                </tbody>
                                <tbody style="display: none" class="ano">
                                    <tr><td nowrap><b>2022-2040</td><td>{{ __('tareas.valores_permitidos') }}</td></tr>
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
            <h2 class="cronResult mb-2 cron_edit" style="text-align: center;background: aliceblue;padding: 10px;"></h2>
            <h4 class="cronHuman mb-2" style="text-align: center;background: #baf2e7;padding: 10px;"></h4>
            <table class="table" style="text-align:center;">
                <thead>
                    <tr>
                        {{-- <th>Segundos</th> --}}
                        <th>{{ __('general.minuto') }}</th>
                        <th>{{ __('general.hora') }}</th>
                        <th>{{ __('general.dia_del_mes') }}</th>
                        <th>{{ __('general.mes') }}</th>
                        <th>{{ __('general.dia_de_la_semana') }}</th>
                    </tr>
                </thead>
                <tbody style="font-weight: 400;font-size: large; overflow-wrap: anywhere;">
                    <tr>
                        {{-- <td><span id="cronResultSecond">0</span></td> --}}
                        <td><span id="cronResultMinute" class="cronResultDigit">*</span></td>
                        <td><span id="cronResultHour" class="cronResultDigit">*</span></td>
                        <td><span id="cronResultDom" class="cronResultDigit">?</span></td>
                        <td><span id="cronResultMonth" class="cronResultDigit">*</span></td>
                        <td><span id="cronResultDow" class="cronResultDigit">*</span></td>
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
        try{
            humano=cronstrue.toString($('.cronResult').html(),{ use24HourTimeFormat: true,locale: "{{ config('app.lang') }}",dayOfWeekStartIndexZero: false });
            $('.cronHuman').removeClass('text-danger');
            $('.cronHuman').removeClass('caja_elol');
            $('.cronHuman').html((humano));
        } catch(e){
            $('.cronHuman').addClass('text-danger');
            $('.cronHuman').addClass('caja_elol');
            $('.cronHuman').html("{{ __('tareas.expresion_cron_incorrecta') }}");
        }
        
        
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
    $('.changeme').change(function(){
        $('#' + $(this).data('target')).prop("checked", true);
    })

</script>