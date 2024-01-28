{{--  <script>
    var panelControl = {
            closeBtn : $('[data-dismiss="panel"], [data-panel="dismiss"]'),
            minMaxBtn : $('[data-panel="minmax"]'),
            fullScreen: $('[data-panel="fullscreen"]')
        }
    if (panelControl.closeBtn.length) {
        panelControl.closeBtn.one('click', function(e){
            e.preventDefault();
            var el = $(this).parents('.panel');

            el.addClass('remove').on('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function(e){
                if (e.originalEvent.propertyName == "opacity") {
                    el.remove();
                    $('body').removeClass('panel-fullscreen');
                }
            });
        });
    }
</script>  --}}