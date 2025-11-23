@component('mail::message')
# Doğrulama Kodunuz

Merhaba {{ $user->name }},

İki faktörlü doğrulama kodunuz:

@component('mail::panel')
<div style="text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px;">
{{ $code }}
</div>
@endcomponent

Bu kod **{{ $expiryMinutes }} dakika** içinde geçerliliğini yitirecektir.

Bu kodu kimseyle paylaşmayın. {{ config('app.name') }} ekibi sizden asla doğrulama kodu istemez.

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
@endcomponent
