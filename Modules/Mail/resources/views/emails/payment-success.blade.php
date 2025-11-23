@component('mail::message')
# Ödemeniz Başarıyla Alındı

Merhaba {{ $user->name }},

Ödemeniz başarıyla işlendi ve aboneliğiniz aktifleştirildi.

@component('mail::panel')
**Ödenen Tutar:** {{ $amount }}<br>
**Abonelik No:** {{ $subscription->subscription_number }}<br>
**Sonraki Yenileme:** {{ $nextRenewal }}
@endcomponent

Hizmetlerimizi kullanmaya devam edebilirsiniz.

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
@endcomponent
