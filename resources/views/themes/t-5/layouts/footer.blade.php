{{-- Ecrin Turizm - Footer (Dinamik) --}}
@php
    $siteName = setting('site_title', 'Ecrin Turizm');
    $siteSlogan = setting('site_slogan', 'Olçun Travel');
    $phone = setting('contact_phone_1', '0546 810 17 17');
    $phoneClean = preg_replace('/[^0-9]/', '', $phone);
    $email = setting('contact_email_1', 'info@ecrinturizm.org');
    $address = setting('contact_address_line_1', 'Güngören / İstanbul');
    $city = setting('contact_city', 'İstanbul');
    $workingHours = setting('contact_working_hours', '7/24 Hizmet');

    // Hizmetleri veritabanından çek
    $services = \Modules\Service\App\Models\Service::where('is_active', true)
        ->whereNull('deleted_at')
        ->orderBy('created_at')
        ->take(5)
        ->get();
@endphp

<!-- FOOTER -->
<footer class="bg-slate-900 dark:bg-black text-white py-16">
    <div class="container mx-auto ">
        <!-- Footer Content -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-8 mb-12">
            <!-- Brand -->
            <div class="lg:col-span-1">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fat fa-plane-departure text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="font-heading font-bold text-xl text-white">{{ $siteName }}</span>
                        <span class="block text-xs text-sky-400 font-medium">{{ $siteSlogan }}</span>
                    </div>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">
                    2008'den beri güvenle hizmet veren A Grubu Seyahat Acentası. Profesyonel taşımacılık çözümleri.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-heading font-semibold text-white mb-6">Hızlı Linkler</h4>
                <ul class="space-y-3">
                    <li><a href="{{ url('/') }}" class="text-slate-400 hover:text-sky-400 transition-colors">Ana Sayfa</a></li>
                    <li><a href="{{ module_locale_url('service', 'index') }}" class="text-slate-400 hover:text-sky-400 transition-colors">Hizmetlerimiz</a></li>
                    <li><a href="{{ module_locale_url('page', 'show', ['hakkimizda']) }}" class="text-slate-400 hover:text-sky-400 transition-colors">Hakkımızda</a></li>
                    <li><a href="{{ module_locale_url('page', 'show', ['iletisim']) }}" class="text-slate-400 hover:text-sky-400 transition-colors">İletişim</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h4 class="font-heading font-semibold text-white mb-6">Hizmetlerimiz</h4>
                <ul class="space-y-3">
                    @forelse($services as $service)
                        <li>
                            <a href="{{ $service->getUrl() }}" class="text-slate-400 hover:text-sky-400 transition-colors">
                                {{ $service->getTranslation('title', app()->getLocale()) }}
                            </a>
                        </li>
                    @empty
                        <li class="text-slate-400">Henüz hizmet eklenmemiş</li>
                    @endforelse
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-heading font-semibold text-white mb-6">İletişim</h4>
                <ul class="space-y-3">
                    <li class="flex items-center space-x-3 text-slate-400">
                        <i class="fat fa-phone text-sky-400"></i>
                        <a href="tel:+90{{ $phoneClean }}" class="hover:text-sky-400 transition-colors">{{ $phone }}</a>
                    </li>
                    <li class="flex items-center space-x-3 text-slate-400">
                        <i class="fat fa-envelope text-sky-400"></i>
                        <a href="mailto:{{ $email }}" class="hover:text-sky-400 transition-colors">{{ $email }}</a>
                    </li>
                    <li class="flex items-center space-x-3 text-slate-400">
                        <i class="fat fa-location-dot text-sky-400"></i>
                        <span>{{ $address }}</span>
                    </li>
                    <li class="flex items-center space-x-3 text-slate-400">
                        <i class="fat fa-clock text-sky-400"></i>
                        <span>{{ $workingHours }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="pt-8 border-t border-slate-800 text-center">
            <p class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} {{ $siteName }}. Tüm hakları saklıdır. | A Grubu Seyahat Acentası İşletme Belgesi No: 9817
            </p>
        </div>
    </div>
</footer>

<!-- AOS Init -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Header scroll effect
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        });
    }

    // Icon hover effect (thin to solid)
    document.querySelectorAll('.card-hover').forEach(card => {
        const icons = card.querySelectorAll('.icon-hover');

        card.addEventListener('mouseenter', () => {
            icons.forEach(icon => {
                if (icon.classList.contains('fat')) {
                    icon.classList.remove('fat');
                    icon.classList.add('fas');
                }
            });
        });

        card.addEventListener('mouseleave', () => {
            icons.forEach(icon => {
                if (icon.classList.contains('fas') && !icon.classList.contains('fab')) {
                    icon.classList.remove('fas');
                    icon.classList.add('fat');
                }
            });
        });
    });

    // Dark mode persistence
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.parentElement.classList.add('dark');
    }

    // Watch for dark mode changes
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                const isDark = document.body.parentElement.classList.contains('dark');
                localStorage.setItem('darkMode', isDark);
            }
        });
    });

    observer.observe(document.body.parentElement, { attributes: true });
</script>
