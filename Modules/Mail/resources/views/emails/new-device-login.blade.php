@component('mail::message')
# Yeni Cihazdan Giriş Yapıldı

Merhaba {{ $user->name }},

Hesabınıza yeni bir cihazdan giriş yapıldı.

@component('mail::panel')
**Tarih:** {{ $time }}<br>
**IP Adresi:** {{ $ip }}<br>
**Cihaz:** {{ $userAgent }}<br>
@if($location)
**Konum:** {{ $location }}
@endif
@endcomponent

Bu giriş size ait değilse, lütfen hemen şifrenizi değiştirin ve aktif oturumlarınızı sonlandırın.

@component('mail::button', ['url' => $devicesUrl])
Cihazları Yönet
@endcomponent

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
@endcomponent
