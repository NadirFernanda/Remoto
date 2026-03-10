<x-mail::message>
<div style="text-align:center; margin-bottom: 24px;">
    <img src="{{ asset('img/logo.png') }}" alt="Logo 24h" style="max-width:180px; height:auto; margin:auto;">
</div>
{{-- Greeting --}}

@if (! empty($greeting))
# {{ $greeting }}
@else
    @if ($level === 'error')
        # Erro!
    @else
        # Bem-vindo à 24 Horas Remoto!
    @endif
@endif


{{-- Intro Lines --}}
@if ($level !== 'error')
Olá! Obrigado por se registar na <strong>24 Horas Remoto</strong>.
<br><br>
Para activar a sua conta e começar a usar todos os recursos da nossa plataforma, confirme o seu e-mail clicando no botão abaixo:
<br><br>
@endif

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset


{{-- Outro Lines --}}
@if ($level !== 'error')
Se não realizou este registo, pode ignorar este e-mail.
<br><br>
Qualquer dúvida, estamos à disposição!
<br>
Equipa 24 Horas Remoto
@endif

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards,')<br>
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
