{{-- Sipariş Detay - Muzibu Dark Theme --}}
<div class="px-4 py-6 sm:px-6 sm:py-8 space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-gray-400 text-sm mb-2">
                <a href="/my-subscriptions" data-spa class="hover:text-white transition-colors">Aboneliklerim</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-white">{{ $order->order_number }}</span>
            </div>
            <h1 class="text-2xl font-bold text-white">
                Sipariş #{{ $order->order_number }}
            </h1>
            <p class="text-gray-400 mt-1">
                {{ $order->created_at->format('d.m.Y H:i') }}
            </p>
        </div>

        {{-- Status Badge --}}
        @php
            // Abonelik siparişi mi kontrol et
            $isSubscriptionOrder = $order->items->contains(fn($item) => str_contains($item->orderable_type ?? '', 'SubscriptionPlan'));

            $statusColors = [
                'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                'processing' => 'bg-green-500/20 text-green-400 border-green-500/30', // Abonelik için yeşil
                'shipped' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                'delivered' => 'bg-green-500/20 text-green-400 border-green-500/30',
                'completed' => 'bg-green-500/20 text-green-400 border-green-500/30',
                'cancelled' => 'bg-red-500/20 text-red-400 border-red-500/30',
                'payment_failed' => 'bg-red-500/20 text-red-400 border-red-500/30',
            ];
            $statusLabels = [
                'pending' => 'Beklemede',
                'processing' => $isSubscriptionOrder ? 'Aktif' : 'Hazırlanıyor',
                'shipped' => 'Kargoya Verildi',
                'delivered' => 'Teslim Edildi',
                'completed' => 'Tamamlandı',
                'cancelled' => 'İptal Edildi',
                'payment_failed' => 'Ödeme Başarısız',
            ];
            $statusColor = $statusColors[$order->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
            $statusLabel = $statusLabels[$order->status] ?? $order->status;
        @endphp
        <span class="px-4 py-2 rounded-full text-sm font-medium border {{ $statusColor }}">
            {{ $statusLabel }}
        </span>
    </div>

    {{-- Info Cards --}}
    <div class="grid sm:grid-cols-2 gap-4">
        {{-- Ödeme Bilgileri --}}
        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
            <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-credit-card text-green-400"></i>
                Ödeme Bilgileri
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Toplam</span>
                    <span class="text-xl font-bold text-green-400">
                        {{ number_format($order->total_amount, 0, ',', '.') }} TL
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Ödeme Durumu</span>
                    @php
                        $paymentColors = [
                            'pending' => 'text-yellow-400',
                            'paid' => 'text-green-400',
                            'failed' => 'text-red-400',
                            'refunded' => 'text-purple-400',
                        ];
                        $paymentLabels = [
                            'pending' => 'Bekliyor',
                            'paid' => 'Ödendi',
                            'failed' => 'Başarısız',
                            'refunded' => 'İade Edildi',
                        ];
                    @endphp
                    <span class="font-semibold {{ $paymentColors[$order->payment_status] ?? 'text-gray-400' }}">
                        {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                    </span>
                </div>
                @if($payment && $payment->gateway)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Ödeme Yöntemi</span>
                    <span class="text-white">
                        @if($payment->gateway === 'paytr')
                            <i class="fas fa-credit-card mr-1"></i> Kredi Kartı
                        @else
                            {{ $payment->gateway }}
                        @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Fatura Bilgileri --}}
        @php
            $billing = $order->billing_address ?? [];
            $hasCompanyInfo = !empty($order->customer_company) || !empty($billing['company_name']) ||
                              !empty($order->customer_tax_number) || !empty($billing['tax_number']);
            $hasAddressInfo = !empty($billing['address_line_1']) || !empty($billing['city']);
            $billingName = $billing['full_name'] ?? $order->customer_name ?? '';
            $billingPhone = $billing['phone'] ?? $order->customer_phone ?? '';
            $billingEmail = $billing['email'] ?? $order->customer_email ?? '';
        @endphp
        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
            <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-file-invoice text-orange-400"></i>
                Fatura Bilgileri
            </h2>
            <div class="space-y-3">
                @if($billingName)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Ad Soyad</span>
                    <span class="text-white">{{ $billingName }}</span>
                </div>
                @endif
                @if($billingPhone)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Telefon</span>
                    <span class="text-white">{{ $billingPhone }}</span>
                </div>
                @endif
                @if($billingEmail)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">E-posta</span>
                    <span class="text-white text-sm">{{ $billingEmail }}</span>
                </div>
                @endif
                @if($order->customer_company || !empty($billing['company_name']))
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Şirket</span>
                    <span class="text-white">{{ $order->customer_company ?: $billing['company_name'] }}</span>
                </div>
                @endif
                @if($order->customer_tax_office || !empty($billing['tax_office']))
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Vergi Dairesi</span>
                    <span class="text-white">{{ $order->customer_tax_office ?: $billing['tax_office'] }}</span>
                </div>
                @endif
                @if($order->customer_tax_number || !empty($billing['tax_number']))
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Vergi No</span>
                    <span class="text-white font-mono">{{ $order->customer_tax_number ?: $billing['tax_number'] }}</span>
                </div>
                @endif
                @if($hasAddressInfo)
                <div class="pt-2 border-t border-white/10">
                    <span class="text-gray-400 text-sm block mb-1">Adres</span>
                    <span class="text-white text-sm">
                        {{ $billing['address_line_1'] ?? '' }}
                        @if(!empty($billing['neighborhood'])) {{ $billing['neighborhood'] }} @endif
                        @if(!empty($billing['district'])) {{ $billing['district'] }} @endif
                        @if(!empty($billing['city'])) / {{ $billing['city'] }} @endif
                    </span>
                </div>
                @else
                <div class="pt-2 border-t border-white/10">
                    <span class="text-gray-400 text-sm block mb-1">Adres</span>
                    <span class="text-gray-500 italic text-sm">Girilmemiş</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sipariş İçeriği --}}
    <div class="bg-white/5 border border-white/10 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-white/10">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-box text-purple-400"></i>
                Sipariş İçeriği
            </h2>
        </div>
        <div class="divide-y divide-white/5">
            @foreach($order->items as $item)
            @php
                $isSubscription = str_contains($item->orderable_type ?? '', 'SubscriptionPlan');
                $metadata = $item->metadata ?? [];
                $isCorporate = ($metadata['type'] ?? null) === 'corporate_bulk';
                $targetUserCount = count($metadata['target_user_ids'] ?? []);
                $cycleLabel = $metadata['cycle_label'][app()->getLocale()] ?? $metadata['cycle_label']['tr'] ?? null;
                $durationDays = $metadata['duration_days'] ?? null;
            @endphp
            <div class="p-4">
                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 {{ $isSubscription ? ($isCorporate ? 'bg-orange-500/20' : 'bg-yellow-500/20') : 'bg-purple-500/20' }}">
                        <i class="fas {{ $isSubscription ? ($isCorporate ? 'fa-building text-orange-400' : 'fa-crown text-yellow-400') : 'fa-box text-purple-400' }} text-lg"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                {{-- Başlık --}}
                                <div class="text-white font-medium">{{ $item->item_title ?? 'Ürün' }}</div>

                                {{-- Detaylar --}}
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    {{-- Tür Badge --}}
                                    @if($isSubscription)
                                        @if($isCorporate)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-500/20 text-orange-400 text-xs rounded-full">
                                                <i class="fas fa-building text-[10px]"></i>
                                                Kurumsal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-yellow-500/20 text-yellow-400 text-xs rounded-full">
                                                <i class="fas fa-crown text-[10px]"></i>
                                                Premium
                                            </span>
                                        @endif
                                    @endif

                                    {{-- Süre Bilgisi --}}
                                    @if($cycleLabel)
                                        <span class="text-gray-400 text-sm">{{ $cycleLabel }}</span>
                                    @elseif($durationDays)
                                        <span class="text-gray-400 text-sm">{{ $durationDays }} gün</span>
                                    @endif
                                </div>

                                {{-- Kurumsal: Hangi kullanıcılar için --}}
                                @if($isCorporate && $targetUserCount > 0)
                                    @php
                                        $targetUserIds = $metadata['target_user_ids'] ?? [];
                                        $targetUsers = \App\Models\User::whereIn('id', $targetUserIds)->pluck('name', 'id');
                                    @endphp
                                    <div class="mt-3 p-3 bg-white/5 rounded-lg">
                                        <div class="text-sm text-gray-400 mb-2">
                                            <i class="fas fa-users mr-1"></i>
                                            {{ $targetUserCount }} kullanıcı için satın alındı:
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($targetUserIds as $userId)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-500/10 border border-orange-500/20 text-orange-300 text-sm rounded-lg">
                                                    <i class="fas fa-user text-[10px]"></i>
                                                    {{ $targetUsers[$userId] ?? 'Kullanıcı #'.$userId }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Adet (eğer >1 ve kurumsal değilse) --}}
                                @if($item->quantity > 1 && !$isCorporate)
                                    <div class="mt-1 text-sm text-gray-500">
                                        Adet: {{ $item->quantity }}
                                    </div>
                                @endif
                            </div>

                            {{-- Fiyat --}}
                            <div class="text-right">
                                <div class="text-white font-semibold">{{ number_format($item->total, 0, ',', '.') }} TL</div>
                                @if($item->quantity > 1)
                                    <div class="text-gray-500 text-xs">
                                        {{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }} TL
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Özet --}}
        <div class="p-4 border-t border-white/10 bg-white/5">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Ara Toplam</span>
                    <span class="text-white">{{ number_format($order->subtotal, 0, ',', '.') }} TL</span>
                </div>
                @if($order->tax_amount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">KDV</span>
                    <span class="text-white">{{ number_format($order->tax_amount, 0, ',', '.') }} TL</span>
                </div>
                @endif
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">İndirim</span>
                    <span class="text-green-400">-{{ number_format($order->discount_amount, 0, ',', '.') }} TL</span>
                </div>
                @endif
                <div class="flex justify-between pt-2 border-t border-white/10">
                    <span class="text-white font-bold">Toplam</span>
                    <span class="text-green-400 font-bold text-lg">{{ number_format($order->total_amount, 0, ',', '.') }} TL</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Geri Butonu --}}
    <div class="flex justify-center">
        <a href="/my-subscriptions" data-spa
           class="inline-flex items-center gap-2 px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
            Aboneliklerime Dön
        </a>
    </div>
</div>
