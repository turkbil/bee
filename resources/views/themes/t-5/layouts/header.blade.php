{{-- Ecrin Turizm - Header (Dinamik) --}}
@php
    $siteName = setting('site_title', 'Ecrin Turizm');
    $siteSlogan = setting('site_slogan', 'Olçun Travel');
    $phone = setting('contact_phone_1', '0546 810 17 17');
    $phoneClean = preg_replace('/[^0-9]/', '', $phone);
    $phoneFormatted = $phone;
@endphp

<!-- HEADER -->
<header id="header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-transparent">
    <div class="container mx-auto ">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fat fa-plane-departure text-white text-xl"></i>
                </div>
                <div>
                    <span class="font-heading font-bold text-xl text-slate-900 dark:text-white">{{ $siteName }}</span>
                    <span class="block text-xs text-sky-600 dark:text-sky-400 font-medium">{{ $siteSlogan }}</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden xl:flex items-center space-x-8">
                <a href="{{ url('/') }}" class="font-medium text-slate-700 dark:text-slate-300 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Ana Sayfa</a>
                <a href="{{ module_locale_url('service', 'index') }}" class="font-medium text-slate-700 dark:text-slate-300 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Hizmetlerimiz</a>
                <a href="{{ module_locale_url('page', 'show', ['hakkimizda']) }}" class="font-medium text-slate-700 dark:text-slate-300 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Hakkımızda</a>
                <a href="{{ module_locale_url('page', 'show', ['iletisim']) }}" class="font-medium text-slate-700 dark:text-slate-300 hover:text-sky-600 dark:hover:text-sky-400 transition-colors">İletişim</a>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fat fa-sun text-xl" x-show="darkMode"></i>
                    <i class="fat fa-moon text-xl" x-show="!darkMode"></i>
                </button>

                <!-- CTA Button -->
                <a href="tel:+90{{ $phoneClean }}" class="hidden md:flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-medium rounded-lg hover:from-sky-600 hover:to-blue-700 transition-all shadow-lg shadow-sky-500/25">
                    <i class="fat fa-phone"></i>
                    <span>{{ $phoneFormatted }}</span>
                </a>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu" class="xl:hidden p-2 rounded-lg text-slate-600 dark:text-slate-400">
                    <i class="fat fa-bars text-2xl" x-show="!mobileMenu"></i>
                    <i class="fat fa-xmark text-2xl" x-show="mobileMenu"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu fixed inset-y-0 right-0 w-80 bg-white dark:bg-slate-900 shadow-2xl xl:hidden" :class="{ 'open': mobileMenu }">
        <div class="p-6">
            <div class="flex items-center justify-between mb-8">
                <span class="font-heading font-bold text-xl text-slate-900 dark:text-white">Menü</span>
                <button @click="mobileMenu = false" class="p-2 rounded-lg text-slate-600 dark:text-slate-400">
                    <i class="fat fa-xmark text-2xl"></i>
                </button>
            </div>
            <nav class="space-y-4">
                <a href="{{ url('/') }}" @click="mobileMenu = false" class="block py-3 px-4 rounded-lg font-medium text-slate-700 dark:text-slate-300 hover:bg-sky-50 dark:hover:bg-slate-800 hover:text-sky-600 transition-colors">Ana Sayfa</a>
                <a href="{{ module_locale_url('service', 'index') }}" @click="mobileMenu = false" class="block py-3 px-4 rounded-lg font-medium text-slate-700 dark:text-slate-300 hover:bg-sky-50 dark:hover:bg-slate-800 hover:text-sky-600 transition-colors">Hizmetlerimiz</a>
                <a href="{{ module_locale_url('page', 'show', ['hakkimizda']) }}" @click="mobileMenu = false" class="block py-3 px-4 rounded-lg font-medium text-slate-700 dark:text-slate-300 hover:bg-sky-50 dark:hover:bg-slate-800 hover:text-sky-600 transition-colors">Hakkımızda</a>
                <a href="{{ module_locale_url('page', 'show', ['iletisim']) }}" @click="mobileMenu = false" class="block py-3 px-4 rounded-lg font-medium text-slate-700 dark:text-slate-300 hover:bg-sky-50 dark:hover:bg-slate-800 hover:text-sky-600 transition-colors">İletişim</a>
            </nav>
            <div class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-700">
                <a href="tel:+90{{ $phoneClean }}" class="flex items-center justify-center space-x-2 w-full py-3 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-medium rounded-lg">
                    <i class="fat fa-phone"></i>
                    <span>{{ $phoneFormatted }}</span>
                </a>
            </div>
        </div>
    </div>
</header>
