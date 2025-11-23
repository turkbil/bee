@component('mail::message')
# Ödeme Başarısız

Merhaba {{ $user->name }},

Abonelik ödemenizi işlerken bir sorun oluştu.

@if($reason)
**Hata:** {{ $reason }}
@endif

Aboneliğinizin kesintiye uğramaması için lütfen ödeme bilgilerinizi güncelleyin ve tekrar deneyin.

@component('mail::button', ['url' => $retryUrl, 'color' => 'primary'])
Ödemeyi Tekrar Dene
@endcomponent

Yardıma ihtiyacınız varsa destek ekibimizle iletişime geçin.

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
@endcomponent
