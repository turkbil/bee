@extends('layout')

@section('page-title', 'AI Kredi Paketleri')
@section('page-description', 'AI özelliklerini kullanmak için kredi satın alın')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Mevcut Bakiye -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Mevcut Kredi Bakiyeniz</h2>
                <p class="text-blue-100">AI özelliklerini kullanmak için kredi gereklidir</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold" id="currentBalance">
                    {{ format_credit(ai_get_credit_balance(), false) }}
                </div>
                <div class="text-blue-100">Kredi</div>
            </div>
        </div>
        
        <!-- Kullanım İstatistikleri -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-blue-400">
            <div class="text-center">
                <div class="text-lg font-semibold">Bu Ay Kullanılan</div>
                <div class="text-2xl font-bold">{{ format_credit(ai_get_monthly_credits_used(), false) }}</div>
            </div>
            <div class="text-center">
                <div class="text-lg font-semibold">Toplam Kullanılan</div>
                <div class="text-2xl font-bold">{{ format_credit(ai_get_total_credits_used(), false) }}</div>
            </div>
            <div class="text-center">
                <div class="text-lg font-semibold">Kalan Süre</div>
                <div class="text-2xl font-bold">Sınırsız</div>
            </div>
        </div>
    </div>
    
    <!-- Kredi Paketleri -->
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Kredi Paketleri</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($packages as $package)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden {{ $package->is_popular ? 'ring-2 ring-blue-500 relative' : '' }}">
                @if($package->is_popular)
                    <div class="absolute top-0 left-0 right-0 bg-blue-500 text-white text-center py-2 text-sm font-semibold">
                        🌟 En Popüler
                    </div>
                    <div class="pt-10">
                @else
                    <div>
                @endif
                
                    <div class="p-6">
                        <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $package->name }}</h4>
                        <p class="text-gray-600 text-sm mb-4">{{ $package->description }}</p>
                        
                        <!-- Kredi Miktarı -->
                        <div class="text-center mb-4">
                            <div class="text-3xl font-bold text-blue-600">
                                {{ format_credit($package->credits, false) }}
                            </div>
                            <div class="text-gray-500">Kredi</div>
                        </div>
                        
                        <!-- Fiyat -->
                        <div class="text-center mb-6">
                            @if($package->discount_percentage > 0)
                                <div class="text-lg text-gray-400 line-through">
                                    ${{ number_format($package->price_usd, 2) }}
                                </div>
                                <div class="text-2xl font-bold text-green-600">
                                    ${{ number_format($package->discounted_price, 2) }}
                                </div>
                                <div class="text-sm text-green-600 font-semibold">
                                    %{{ $package->discount_percentage }} İndirim
                                </div>
                            @else
                                <div class="text-2xl font-bold text-gray-800">
                                    ${{ number_format($package->price_usd, 2) }}
                                </div>
                            @endif
                            
                            @if($package->price_try)
                                <div class="text-sm text-gray-500 mt-1">
                                    (₺{{ number_format($package->price_try, 2) }})
                                </div>
                            @endif
                        </div>
                        
                        <!-- Özellikler -->
                        @if($package->features)
                            <ul class="text-sm text-gray-600 mb-6 space-y-2">
                                @foreach($package->features as $feature)
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        
                        <!-- Satın Al Butonu -->
                        <button 
                            onclick="purchasePackage({{ $package->id }})"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors {{ $package->is_popular ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700' : '' }}"
                        >
                            Satın Al
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Kredi Kullanım Rehberi -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">💡 Kredi Kullanım Rehberi</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Kredi Nasıl Kullanılır?</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• AI chat ve sohbet: ~0.001-0.01 kredi</li>
                    <li>• İçerik üretimi: ~0.01-0.05 kredi</li>
                    <li>• SEO analizi: ~0.02-0.08 kredi</li>
                    <li>• Çeviri işlemleri: ~0.005-0.02 kredi</li>
                    <li>• Kod üretimi: ~0.02-0.1 kredi</li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Avantajlar</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Gerçek zamanlı token bazlı fiyatlandırma</li>
                    <li>• Sadece kullandığınız kadar ödersiniz</li>
                    <li>• Birden fazla AI provider desteği</li>
                    <li>• Otomatik maliyet optimizasyonu</li>
                    <li>• Detaylı kullanım raporları</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Satın Alma Modal -->
<div id="purchaseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Kredi Paketi Satın Al</h3>
            <button onclick="closePurchaseModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="modalContent">
            <!-- Modal içeriği AJAX ile doldurulacak -->
        </div>
    </div>
</div>

<script>
// Mevcut bakiyeyi güncelle
function updateBalance() {
    fetch('/api/credits/balance')
        .then(response => response.json())
        .then(data => {
            document.getElementById('currentBalance').textContent = parseFloat(data.balance).toFixed(4);
        })
        .catch(error => {
            console.error('Balance update error:', error);
        });
}

// Paket satın alma
function purchasePackage(packageId) {
    // Modal içeriğini yükle
    fetch(`/credits/purchase/${packageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalContent').innerHTML = data.html;
                document.getElementById('purchaseModal').classList.remove('hidden');
                document.getElementById('purchaseModal').classList.add('flex');
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Purchase modal error:', error);
            alert('Bir hata oluştu. Lütfen tekrar deneyin.');
        });
}

// Modal kapat
function closePurchaseModal() {
    document.getElementById('purchaseModal').classList.add('hidden');
    document.getElementById('purchaseModal').classList.remove('flex');
}

// Ödeme işlemi
function processPurchase(packageId, paymentMethod) {
    const button = event.target;
    const originalText = button.textContent;
    
    button.disabled = true;
    button.textContent = 'İşleniyor...';
    
    fetch('/credits/purchase/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            package_id: packageId,
            payment_method: paymentMethod
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ödeme başarılı! Kredi bakiyeniz güncellendi.');
            closePurchaseModal();
            updateBalance();
            
            // Sayfayı yenile (isteğe bağlı)
            // window.location.reload();
        } else {
            alert('Ödeme hatası: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Payment error:', error);
        alert('Ödeme işleminde bir hata oluştu.');
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = originalText;
    });
}

// Sayfa yüklendiğinde bakiyeyi güncelle
document.addEventListener('DOMContentLoaded', function() {
    // Periyodik bakiye güncellemesi (isteğe bağlı)
    setInterval(updateBalance, 30000); // 30 saniyede bir
});
</script>
@endsection