@component('mail::message')
# Deneme Süreniz Bitiyor

Merhaba {{ $user->name }},

Ücretsiz deneme süreniz **{{ $daysLeft }} gün** içinde sona erecek.

Hizmetlerimizden kesintisiz faydalanmaya devam etmek için şimdi abone olun.

@component('mail::button', ['url' => $subscribeUrl, 'color' => 'primary'])
Şimdi Abone Ol
@endcomponent

Sorularınız mı var? Destek ekibimiz size yardımcı olmaktan mutluluk duyacaktır.

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
@endcomponent
