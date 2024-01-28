@component('mail::message')
{{-- Greeting --}}
{{--  @if (! empty($greeting))
    # {{ $greeting }}
@else
    @if ($level === 'error')
    # @lang('Whoops!')
    @else
    # @lang('Hello!')
    @endif
@endif  --}}
<h3>Hola</h3>
{{-- Intro Lines --}}
{{--  @foreach ($introLines as $line)
{{ $line }}

@endforeach  --}}
Hemos recibido una solicitud de restablecimiento de contraseña de su cuenta de {{ config('app.name') }}

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}

@endcomponent
@endisset

{{-- Outro Lines --}}
{{--  @foreach ($outroLines as $line)
{{ $line }}

@endforeach  --}}
Este link caducará en 60 minutos<br>
Si usted no ha solicitado un restablecimiento de contraseña, ignore este mensaje y no haga nada


{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Gracias,<br>
<div class="mar-ver pad-btm w-100 text-center" style="margil-left: 150px">
    <img src="{{ config('app.url_asset_mail').'/img/Mosaic_brand_300.png' }}">
</div>
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
    "Si tiene problemas con el botón \":actionText\" copie la URL a continuacion y peguela en un navegador\n".
    ':',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset
@endcomponent
