@component('mail::message')
# Kurumsal Hesap Daveti

Merhaba,

**{{ $corporateUser->name }}** sizi {{ config('app.name') }} kurumsal hesabına davet ediyor.

Bu daveti kabul ederek, kurumsal hesap altında tüm özelliklere erişim sağlayabilirsiniz.

@component('mail::panel')
**Kurumsal Kod:** {{ $corporateCode }}
@endcomponent

@component('mail::button', ['url' => $registerUrl, 'color' => 'primary'])
Daveti Kabul Et
@endcomponent

Bu daveti beklemiyorsanız, bu e-postayı görmezden gelebilirsiniz.

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
@endcomponent
