@component('mail::message')
# Hoş Geldiniz, {{ $user->name }}!

{{ config('app.name') }} ailesine katıldığınız için teşekkür ederiz.

@if($trialDays > 0)
**{{ $trialDays }} gün ücretsiz deneme** süreniz başladı. Bu süre içinde tüm özellikleri keşfedebilirsiniz.
@endif

Hesabınızla ilgili herhangi bir sorunuz olursa destek ekibimizle iletişime geçmekten çekinmeyin.

@component('mail::button', ['url' => $loginUrl])
Giriş Yap
@endcomponent

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
@endcomponent
