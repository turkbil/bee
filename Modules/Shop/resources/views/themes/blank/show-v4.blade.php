@extends('themes.blank.layouts.app')

{{-- V4: LANDING PAGE STYLE - Conversion-focused with big hero and strong CTAs --}}

@section('module_content')
    <div class="min-h-screen bg-white dark:bg-gray-900">
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale);
            $shortDescription = $item->getTranslated('short_description', $currentLocale);
            $longDescription = $item->getTranslated('long_description', $currentLocale);
            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            $indexSlug = $moduleSlugService->getMultiLangSlug('Shop', 'index', $currentLocale);
            $defaultLocale = get_tenant_default_locale();
            $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
            $shopIndexUrl = $localePrefix . '/' . $indexSlug;
            $featuredImage = $item->getFirstMedia('featured_image');
            $galleryImages = $item->getMedia('gallery');
            $resolveLocalized = static function ($data) use ($currentLocale, $defaultLocale) {
                if (!is_array($data)) return $data;
                $hasLocaleKeys = isset($data[$currentLocale]) || isset($data[$defaultLocale]) || isset($data['en']);
                if (!$hasLocaleKeys && array_is_list($data)) return $data;
                return $data[$currentLocale] ?? $data[$defaultLocale] ?? $data['en'] ?? reset($data);
            };
            $formatSpecValue = static function ($value) use (&$formatSpecValue) {
                if (is_bool($value)) return $value ? 'Var' : 'Yok';
                if (is_array($value)) {
                    if (isset($value['value']) && isset($value['unit'])) {
                        $note = $value['note'] ?? '';
                        $formatted = $value['value'] . ' ' . $value['unit'];
                        return $note ? $formatted . ' (' . $note . ')' : $formatted;
                    }
                    if (isset($value['unit'])) {
                        $unit = $value['unit'];
                        $parts = [];
                        foreach ($value as $key => $val) {
                            if ($key === 'unit') continue;
                            $formattedVal = is_array($val) ? $formatSpecValue($val) : $val;
                            $parts[] = ucfirst($key) . ': ' . $formattedVal . ' ' . $unit;
                        }
                        return implode(' | ', $parts);
                    }
                    if (isset($value['min']) || isset($value['max'])) {
                        $parts = [];
                        if (isset($value['min'])) $parts[] = 'Min: ' . $value['min'];
                        if (isset($value['max'])) $parts[] = 'Max: ' . $value['max'];
                        $unit = $value['unit'] ?? '';
                        return implode(' | ', $parts) . ($unit ? ' ' . $unit : '');
                    }
                    if (count($value) > 1) {
                        $unit = $value['unit'] ?? '';
                        $parts = [];
                        foreach ($value as $key => $val) {
                            if ($key === 'unit') continue;
                            $formattedVal = is_array($val) ? $formatSpecValue($val) : $val;
                            $parts[] = ucfirst($key) . ': ' . $formattedVal;
                        }
                        return implode(' × ', $parts) . ($unit ? ' ' . $unit : '');
                    }
                    return json_encode($value);
                }
                return (string) $value;
            };
            $primarySpecs = is_array($item->primary_specs) ? array_values(array_filter($item->primary_specs, fn($spec) => is_array($spec) && ($spec['label'] ?? false) && ($spec['value'] ?? false))) : [];
            $highlightedFeatures = collect($item->highlighted_features ?? [])->map(fn($feature) => is_array($feature) ? ['icon' => $feature['icon'] ?? 'bolt', 'title' => is_array($feature['title'] ?? null) ? $resolveLocalized($feature['title']) : ($feature['title'] ?? null), 'description' => is_array($feature['description'] ?? null) ? $resolveLocalized($feature['description']) : ($feature['description'] ?? null)] : null)->filter(fn($feature) => $feature && ($feature['title'] || $feature['description']));
            $useCases = [];
            if (is_array($item->use_cases)) {
                $resolvedUseCases = $resolveLocalized($item->use_cases);
                if (is_array($resolvedUseCases)) {
                    $useCases = collect($resolvedUseCases)->map(fn($case) => is_array($case) && isset($case['text']) ? $case : ['icon' => 'check', 'text' => $case])->filter(fn($case) => !empty($case['text']))->values()->all();
                }
            }
            $faqEntries = collect($item->faq_data ?? [])->map(fn($faq) => is_array($faq) ? ['question' => $resolveLocalized($faq['question'] ?? null), 'answer' => $resolveLocalized($faq['answer'] ?? null), 'sort_order' => $faq['sort_order'] ?? 999] : null)->filter(fn($faq) => $faq && $faq['question'] && $faq['answer'])->sortBy('sort_order')->values();
            $technicalSpecs = is_array($item->technical_specs) ? $item->technical_specs : [];
            $brandingInfo = null;
            $featuresList = [];
            if (is_array($item->features)) {
                $resolvedFeatures = $resolveLocalized($item->features) ?? [];
                if (is_array($resolvedFeatures)) {
                    if (array_is_list($resolvedFeatures)) {
                        $featuresList = collect($resolvedFeatures)->map(fn($f) => is_array($f) && isset($f['text']) ? $f : ['icon' => 'check-circle', 'text' => $f])->filter(fn($f) => !empty($f['text']))->values()->all();
                    } else {
                        $featuresList = collect($resolvedFeatures['list'] ?? [])->map(fn($f) => is_array($f) && isset($f['text']) ? $f : ['icon' => 'check-circle', 'text' => $f])->filter(fn($f) => !empty($f['text']))->values()->all();
                        $brandingInfo = $resolvedFeatures['branding'] ?? null;
                    }
                }
            }
            $competitiveAdvantages = [];
            if (is_array($item->competitive_advantages)) {
                $resolvedAdvantages = $resolveLocalized($item->competitive_advantages);
                if (is_array($resolvedAdvantages)) {
                    $competitiveAdvantages = collect($resolvedAdvantages)->map(fn($adv) => is_array($adv) && isset($adv['text']) ? $adv : ['icon' => 'star', 'text' => $adv])->filter(fn($adv) => !empty($adv['text']))->values()->all();
                }
            }
            $targetIndustries = [];
            if (is_array($item->target_industries)) {
                $resolvedIndustries = $resolveLocalized($item->target_industries);
                if (is_array($resolvedIndustries)) {
                    $targetIndustries = collect($resolvedIndustries)->map(fn($ind) => is_array($ind) && isset($ind['text']) ? $ind : ['icon' => 'briefcase', 'text' => $ind])->filter(fn($ind) => !empty($ind['text']))->values()->all();
                }
            }
            $warrantyInfo = null;
            if (is_array($item->warranty_info)) {
                $warrantyInfo = $resolveLocalized($item->warranty_info);
            }
            $accessories = [];
            if (is_array($item->accessories)) {
                $accessories = collect($item->accessories)->filter(fn($acc) => is_array($acc) && !empty($acc['name']))->values()->all();
            }
            $certifications = [];
            if (is_array($item->certifications)) {
                $certifications = collect($item->certifications)->filter(fn($cert) => is_array($cert) && !empty($cert['name']))->values()->all();
            }
            $siblingVariants = $siblingVariants ?? collect();
        @endphp

        {{-- FULLSCREEN HERO --}}
        <section class="relative bg-gradient-to-br from-blue-900 via-blue-700 to-blue-600 text-white min-h-screen flex items-center">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative z-10 container mx-auto px-4 py-24 text-center">
                <span class="inline-block px-6 py-2 bg-green-500 text-white text-sm font-bold rounded-full mb-6 animate-pulse">✓ STOKTA MEVCUT</span>
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">{{ $title }}</h1>
                @if($shortDescription)
                    <p class="text-xl md:text-2xl mb-12 max-w-3xl mx-auto text-blue-100">{{ $shortDescription }}</p>
                @endif
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center mb-16">
                    <a href="#contact" class="inline-flex items-center justify-center gap-3 bg-white dark:bg-blue-600 text-blue-900 dark:text-white px-12 py-5 rounded-full text-xl font-bold hover:bg-blue-50 dark:hover:bg-blue-700 transition-all transform hover:scale-105 shadow-2xl">
                        <i class="fa-solid fa-envelope"></i>
                        <span>ÜCRETSİZ TEKLİF AL</span>
                    </a>
                    <a href="tel:02167553555" class="inline-flex items-center justify-center gap-3 border-4 border-white text-white px-12 py-5 rounded-full text-xl font-bold hover:bg-white hover:text-blue-900 dark:hover:bg-white/20 dark:hover:text-white transition-all">
                        <i class="fa-solid fa-phone"></i>
                        <span>0216 755 3 555</span>
                    </a>
                </div>

                @if(!empty($primarySpecs))
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
                        @foreach(array_slice($primarySpecs, 0, 4) as $spec)
                            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                                <div class="text-4xl font-bold mb-2">{{ $spec['value'] }}</div>
                                <div class="text-sm text-blue-100 uppercase">{{ $spec['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
                <i class="fa-solid fa-chevron-down text-white text-3xl"></i>
            </div>
        </section>

        {{-- TRUST BADGES --}}
        <section class="py-16 bg-gray-900 text-white">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-8">
                    <div class="flex items-center gap-4"><i class="fa-solid fa-shield-halved text-5xl text-green-500"></i><div><div class="font-bold text-lg">GARANTİLİ</div><div class="text-sm text-gray-400">Orijinal Ürün</div></div></div>
                    <div class="flex items-center gap-4"><i class="fa-solid fa-truck-fast text-5xl text-blue-500"></i><div><div class="font-bold text-lg">HIZLI TESLİMAT</div><div class="text-sm text-gray-400">Türkiye Geneli</div></div></div>
                    <div class="flex items-center gap-4"><i class="fa-solid fa-headset text-5xl text-purple-500"></i><div><div class="font-bold text-lg">7/24 DESTEK</div><div class="text-sm text-gray-400">Kesintisiz Hizmet</div></div></div>
                    <div class="flex items-center gap-4"><i class="fa-solid fa-award text-5xl text-yellow-500"></i><div><div class="font-bold text-lg">SERTİFİKALI</div><div class="text-sm text-gray-400">Uluslararası</div></div></div>
                </div>
            </div>
        </section>

        {{-- FEATURES --}}
        @if($highlightedFeatures->isNotEmpty())
            <section class="py-24 bg-white dark:bg-gray-900">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-16">
                        <h2 class="text-5xl font-bold mb-4">Güçlü Özellikler</h2>
                        <p class="text-xl text-gray-600 dark:text-gray-400">Size özel çözümler sunuyoruz</p>
                    </div>
                    <div class="grid md:grid-cols-3 gap-12">
                        @foreach($highlightedFeatures as $feature)
                            <div class="text-center group hover:scale-105 transition-transform">
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:rotate-12 transition-transform">
                                    <i class="fa-solid fa-{{ $feature['icon'] }} text-white text-4xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold mb-4">{{ $feature['title'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-lg">{{ $feature['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- CTA BANNER 1 --}}
        <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center">
            <div class="container mx-auto px-4">
                <h2 class="text-4xl font-bold mb-6">Fiyat Öğrenmek İster Misiniz?</h2>
                <p class="text-xl mb-8">Size özel kampanyalı fiyatlarımızı hemen öğrenin!</p>
                <a href="#contact" class="inline-flex items-center gap-3 bg-white dark:bg-blue-700 text-blue-600 dark:text-white px-12 py-5 rounded-full text-xl font-bold hover:bg-gray-100 dark:hover:bg-blue-800 transition-all transform hover:scale-105">
                    <i class="fa-solid fa-gift"></i>
                    <span>KAMPANYALI FİYAT AL</span>
                </a>
            </div>
        </section>

        {{-- COMPETITIVE ADVANTAGES --}}
        @if(!empty($competitiveAdvantages))
            <section class="py-24 bg-gray-50 dark:bg-gray-800">
                <div class="container mx-auto px-4">
                    <h2 class="text-5xl font-bold text-center mb-16">Neden Bizi Tercih Etmelisiniz?</h2>
                    <div class="grid md:grid-cols-2 gap-8">
                        @foreach($competitiveAdvantages as $adv)
                            @php
                                $advIcon = is_array($adv) ? ($adv['icon'] ?? 'star') : 'star';
                                $advText = is_array($adv) ? ($adv['text'] ?? $adv) : $adv;
                            @endphp
                            <div class="flex gap-6 p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-lg hover:shadow-2xl transition-shadow">
                                <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-{{ $advIcon }} text-white text-2xl"></i>
                                </div>
                                <p class="text-lg text-gray-700 dark:text-gray-300 pt-3">{{ $advText }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- GALLERY --}}
        @if($galleryImages->count() > 0)
            <section class="py-24 bg-white dark:bg-gray-900">
                <div class="container mx-auto px-4">
                    <h2 class="text-5xl font-bold text-center mb-16">Ürün Görselleri</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($galleryImages as $image)
                            <a href="{{ $image->getUrl() }}" class="glightbox block rounded-xl overflow-hidden group" data-gallery="prod">
                                <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}" alt="" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- SPECS, USE CASES, etc. (simplified for landing page) --}}
        @if(!empty($technicalSpecs) || !empty($useCases) || !empty($featuresList))
            <section class="py-24 bg-gray-900 text-white">
                <div class="container mx-auto px-4">
                    <h2 class="text-5xl font-bold text-center mb-16">Detaylı Bilgiler</h2>
                    
                    @if(!empty($featuresList))
                        <div class="grid md:grid-cols-3 gap-6 mb-16">
                            @foreach($featuresList as $feature)
                                @php
                                    $featureIcon = is_array($feature) ? ($feature['icon'] ?? 'check-circle') : 'check-circle';
                                    $featureText = is_array($feature) ? ($feature['text'] ?? $feature) : $feature;
                                @endphp
                                <div class="flex gap-3 p-4 bg-white/10 rounded-xl">
                                    <i class="fa-solid fa-{{ $featureIcon }} text-green-400 text-xl mt-1"></i>
                                    <span>{{ $featureText }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($useCases))
                        <h3 class="text-3xl font-bold mb-8">Kullanım Alanları</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            @foreach($useCases as $case)
                                @php
                                    $caseIcon = is_array($case) ? ($case['icon'] ?? 'check') : 'check';
                                    $caseText = is_array($case) ? ($case['text'] ?? $case) : $case;
                                @endphp
                                <div class="flex gap-4 p-6 bg-blue-600/20 rounded-xl">
                                    <i class="fa-solid fa-{{ $caseIcon }} text-blue-400 text-xl mt-1"></i>
                                    <span>{{ $caseText }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- VARIANTS --}}
        @if($siblingVariants->count() > 0)
            <section class="py-24 bg-white dark:bg-gray-900">
                <div class="container mx-auto px-4">
                    <h2 class="text-5xl font-bold text-center mb-16">Diğer Modeller</h2>
                    <div class="grid md:grid-cols-4 gap-6">
                        @foreach($siblingVariants as $variant)
                            @php
                                $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);
                                $variantImage = $variant->getFirstMedia('featured_image');
                            @endphp
                            <a href="{{ $variantUrl }}" class="group block border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 rounded-xl p-4 transition-all hover:shadow-xl">
                                @if($variantImage)
                                    <img src="{{ $variantImage->hasGeneratedConversion('thumb') ? $variantImage->getUrl('thumb') : $variantImage->getUrl() }}" alt="{{ $variantTitle }}" class="w-full h-32 object-cover rounded mb-3">
                                @endif
                                <h3 class="font-bold group-hover:text-blue-600">{{ $variantTitle }}</h3>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- FINAL CTA SECTION --}}
        <section id="contact" class="py-24 bg-gradient-to-br from-blue-900 via-purple-800 to-blue-900 text-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center mb-12">
                    <h2 class="text-5xl font-bold mb-6">Hemen Teklif Alın!</h2>
                    <p class="text-2xl mb-8">Size özel fiyat teklifimizi kaçırmayın!</p>
                </div>

                <form action="{{ route('shop.quote.submit') }}" method="POST" class="max-w-2xl mx-auto space-y-6 bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="product_title" value="{{ $title }}">

                    <div class="grid md:grid-cols-2 gap-6">
                        <input type="text" name="name" required placeholder="Ad Soyad *" class="px-6 py-4 bg-white/20 border-0 rounded-xl text-white placeholder-white/70 focus:ring-2 focus:ring-white">
                        <input type="email" name="email" required placeholder="E-posta *" class="px-6 py-4 bg-white/20 border-0 rounded-xl text-white placeholder-white/70 focus:ring-2 focus:ring-white">
                    </div>
                    <input type="tel" name="phone" required placeholder="Telefon *" class="w-full px-6 py-4 bg-white/20 border-0 rounded-xl text-white placeholder-white/70 focus:ring-2 focus:ring-white">
                    <textarea name="message" rows="4" placeholder="Mesajınız" class="w-full px-6 py-4 bg-white/20 border-0 rounded-xl text-white placeholder-white/70 focus:ring-2 focus:ring-white"></textarea>
                    <button type="submit" class="w-full bg-white dark:bg-blue-600 text-blue-900 dark:text-white font-bold py-5 rounded-xl text-xl hover:bg-gray-100 dark:hover:bg-blue-700 transition-all transform hover:scale-105">
                        <i class="fa-solid fa-paper-plane mr-2"></i>TEKLİF İSTE
                    </button>
                </form>
            </div>
        </section>

        {{-- STICKY BOTTOM CTA --}}
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 shadow-2xl md:hidden">
            <div class="container mx-auto px-4 flex gap-4">
                <a href="tel:02167553555" class="flex-1 bg-white dark:bg-blue-700 text-blue-600 dark:text-white text-center font-bold py-3 rounded-lg">ARA</a>
                <a href="#contact" class="flex-1 bg-yellow-400 dark:bg-yellow-500 text-gray-900 dark:text-gray-900 text-center font-bold py-3 rounded-lg">TEKLİF AL</a>
            </div>
        </div>
    </div>
@endsection
