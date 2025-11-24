# Abonelik Durumu Sayfas1 - Plan

**Tarih:** 2025-11-24 19:53
**Durum:** Plan
**Öncelik:** Orta

---

## Hedef

Kullan1c1n1n mevcut aboneliini görebildii ve yönetebildii bir sayfa.

---

## Özellikler

### 1. Mevcut Plan Bilgisi

#### Gösterilecek Bilgiler
- **Plan Ad1**: Premium, Basic, Free
- **Durum**: Aktif, Trial, Süresi Dolmu_
- **Ba_lang1ç Tarihi**: "15 Kas1m 2025"
- **Biti_ Tarihi**: "15 Aral1k 2025" (+ kalan gün say1s1)
- **Ayl1k Fiyat**: "º99.00"
- **Yenileme**: Otomatik / Manuel
- **Özelliklerin Listesi**: Plan içerii

#### Görsel Tasar1m
- Hero card: Plan ad1, durum badge, kalan gün
- Özellikler listesi (checkmark ile)
- Action butonlar1

### 2. Plan Dei_tirme Seçenekleri

#### Özellikler
- "Plan Dei_tir" butonu ’ Pricing sayfas1na yönlendir
- Upgrade: Hemen aktif ol + prorated payment
- Downgrade: Dönem sonunda geçerli

#### Kurumsal Kullan1c1lar 0çin
- Kurum üyesiyse ’ "Aboneliiniz kurum taraf1ndan yönetiliyor" mesaj1
- Plan dei_tirme butonu disabled
- Kurum sahibi bilgisi göster

### 3. Ödeme Geçmi_i

#### Tablo
- Tarih
- Plan
- Tutar
- Durum (Ba_ar1l1 / Ba_ar1s1z / 0ptal)
- Fatura (PDF indir butonu)

### 4. 0ptal / Yenileme

#### 0ptal
- "Abonelii 0ptal Et" butonu
- Modal: "Emin misiniz?" + iptal sebebi
- 0ptal sonras1 ’ Dönem sonuna kadar kullanabilir

#### Yenileme
- Otomatik yenileme toggle
- Kredi kart1 bilgisi güncelleme linki
- Manuel yenileme butonu (otomatik kapal1ysa)

---

## Teknik Detaylar

### Veri Kayna1
```php
// Kullan1c1n1n efektif aboneliini al
$subscription = CorporateService::getEffectiveSubscription(auth()->user());

// Eer kurumsal üyeyse kurum sahibinin abonelii döner
// Deilse kendi abonelii
```

### Subscription Model 0li_kisi
```php
$subscription = auth()->user()->subscription;
// veya
$subscription = auth()->user()->subscription()->with('plan')->first();
```

### Kalan Gün Hesaplama
```php
$endsAt = Carbon::parse($subscription->ends_at);
$daysLeft = now()->diffInDays($endsAt);

if ($daysLeft < 0) {
    $status = 'expired';
} elseif ($daysLeft <= 7) {
    $status = 'expiring_soon';
} else {
    $status = 'active';
}
```

---

## Dosyalar

### Livewire Component
- `app/Http/Livewire/Profile/SubscriptionComponent.php`

### Blade View
- `resources/views/livewire/profile/subscription-component.blade.php`

### Servisler
- `Modules/Subscription/app/Services/SubscriptionService.php`
- `app/Services/Auth/CorporateService.php`

---

## Yakla_1m

### Ad1m 1: Livewire Component Olu_tur
```php
class SubscriptionComponent extends Component
{
    public $subscription;
    public $isCorporateMember = false;
    public $corporateAccount = null;

    public function mount()
    {
        // Kurumsal kontrol
        $this->isCorporateMember = CorporateService::isMember(auth()->user());

        if ($this->isCorporateMember) {
            $this->corporateAccount = CorporateService::getCorporateForUser(auth()->user()->id);
        }

        // Efektif abonelik
        $this->subscription = CorporateService::getEffectiveSubscription(auth()->user());
    }

    public function cancelSubscription()
    {
        // 0ptal et
    }

    public function toggleAutoRenewal()
    {
        // Otomatik yenileme toggle
    }

    public function render()
    {
        $payments = $this->subscription
            ? $this->subscription->payments()->latest()->paginate(10)
            : collect([]);

        return view('livewire.profile.subscription-component', compact('payments'));
    }
}
```

### Ad1m 2: Blade View Olu_tur
- Hero card (plan bilgisi)
- Özellikler listesi
- Action butonlar1
- Ödeme geçmi_i tablosu

### Ad1m 3: Kurumsal Durum Kontrolü
```blade
@if($isCorporateMember)
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Aboneliiniz <strong>{{ $corporateAccount->company_name }}</strong> taraf1ndan yönetiliyor.
    </div>
@endif
```

---

## Teknik Notlar

### Status Badge Renkleri
```blade
@switch($subscription->status)
    @case('active')
        <span class="badge bg-success">Aktif</span>
        @break
    @case('trial')
        <span class="badge bg-warning">Deneme</span>
        @break
    @case('expired')
        <span class="badge bg-danger">Süresi Dolmu_</span>
        @break
@endswitch
```

### Kalan Gün Uyar1s1
```blade
@if($daysLeft <= 7 && $daysLeft > 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        Aboneliiniz {{ $daysLeft }} gün içinde sona erecek!
        <a href="{{ route('pricing') }}">Yenile</a>
    </div>
@endif
```

### Plan Özellikleri
```blade
<ul class="feature-list">
    @foreach($subscription->plan->features as $feature)
        <li>
            <i class="fas fa-check text-success"></i>
            {{ $feature->name }}
        </li>
    @endforeach
</ul>
```

---

## Beklenen Sonuç

-  Mevcut plan bilgisi gösteriliyor
-  Kalan gün hesaplan1yor
-  Kurumsal üyeler için bilgilendirme
-  Plan dei_tirme linki çal1_1yor
-  Ödeme geçmi_i listeleniyor
-  0ptal/Yenileme i_lemleri çal1_1yor

---

## Test Senaryolar1

1. **Aktif Abonelik**: Tüm bilgiler görünüyor mu?
2. **Trial Abonelik**: Trial badge gösteriliyor mu?
3. **Süresi Dolmu_**: Yenileme uyar1s1 ç1k1yor mu?
4. **Kurumsal Üye**: Bilgilendirme mesaj1 görünüyor mu?
5. **Plan Dei_tir**: Pricing sayfas1na yönlendiriyor mu?
6. **0ptal Et**: Modal aç1l1yor mu?
7. **Ödeme Geçmi_i**: Doru listeleniyor mu?

---

**NOT:** Önce HTML taslak haz1rlanacak!
