@component('mail::message')
# Abonelik Yenileme Hatırlatması

Merhaba {{ $user->name }},

Aboneliğiniz **{{ $renewalDate }}** tarihinde otomatik olarak yenilenecektir.

@component('mail::panel')
**Plan:** {{ $subscription->plan->title ?? 'Standart' }}<br>
**Tutar:** {{ $amount }}<br>
**Yenileme Tarihi:** {{ $renewalDate }}
@endcomponent

Otomatik yenilemeyi iptal etmek isterseniz, hesap ayarlarınızdan bu işlemi gerçekleştirebilirsiniz.

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
@endcomponent
