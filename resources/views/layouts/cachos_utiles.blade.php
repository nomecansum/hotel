

{{-- HEader modal --}}
<div class="modal-header">
    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
    <h1 class="modal-title text-nowrap">PREGUNTA </h1>
    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
    </button>
</div>    
<div class="modal-body">
    CONTENIDO
</div>


{{-- Header card --}}
<div class="card-header toolbar">
    <div class="toolbar-start">
        <h5 class="m-0">Reserva de puesto</h5>
    </div>
    <div class="toolbar-end">
        <button type="button" class="btn-close btn-close-card">
            <span class="visually-hidden">Close the card</span>
        </button>
    </div>
</div>


{{-- Floating like floating-like-gmail --}}

<div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
    <div class="btn-group btn-group pull-right ml-1" role="group">

<td style="position: relative">
    <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
        <div class="btn-group btn-group pull-right ml-1" role="group">

{{-- Tablas --}}
data-buttons-class="secondary"
data-show-button-text="true"

{{-- Checkbox --}}

<div class="form-check pt-2">
    <input  class="form-check-input" type="checkbox">
    <label class="form-check-label" for="chktodos">Todos</label>
</div>


{{-- Dropdown --}}
<div class="btn-group">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item" href="#">Separated link</a></li>
    </ul>
</div>


<div class="tab-base tab-vertical">

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#_dm-verTabsHome" type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#_dm-verTabsProfile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#_dm-verTabsContact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content">
        <div id="_dm-verTabsHome" class="tab-pane fade active show" role="tabpanel" aria-labelledby="home-tab">
            <h5 class="card-title">Home tab</h5>
            <p>One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff sections.</p>
        </div>
        <div id="_dm-verTabsProfile" class="tab-pane fade" role="tabpanel" aria-labelledby="profile-tab">
            <h5 class="card-title">Profile tab</h5>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean. A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
        </div>
        <div id="_dm-verTabsContact" class="tab-pane fade" role="tabpanel" aria-labelledby="contact-tab">
            <h5 class="card-title">Contact tab</h5>
            <p>The quick, brown fox jumps over a lazy dog. DJs flock by when MTV ax quiz prog. Junk MTV quiz graced by fox whelps. Bawds jog, flick quartz, vex nymphs. Waltz, bad nymph, for quick jigs vex! Fox nymphs grab quick-jived waltz. Brick quiz whangs jumpy veldt fox.</p>
        </div>
    </div>

</div>


<script>

function del(id){
    $('#eliminar-planta-'+id).modal('show');
}

document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );


// {{-- Range Picker --}}
    var rangepicker = new Litepicker({
        element: document.getElementById( "fechas" ),
        singleMode: false,
        @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
        @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
        autoApply: true,
        format: 'DD/MM/YYYY',
        lang: "es-ES",
        tooltipText: {
            one: "day",
            other: "days"
        },
        tooltipNumber: (totalDays) => {
            return totalDays - 1;
        },
        setup: (rangepicker) => {
            rangepicker.on('selected', (date1, date2) => {
                //comprobar_puestos();
            });
        }
    });

    $('.btn_fechas').click(function(){
        rangepicker.show();
    })



// {{-- Single Picker --}}
    $('.btn_fecha').click(function(){
        picker.open('#fecha_ver');
    })

    const picker = MCDatepicker.create({
        el: "#fecha_ver",
        dateFormat: cal_formato_fecha,
        autoClose: true,
        closeOnBlur: true,
        firstWeekday: 1,
        disableWeekDays: cal_dias_deshabilitados,
        customMonths: cal_meses,
        customWeekDays: cal_diassemana
    });

</script>