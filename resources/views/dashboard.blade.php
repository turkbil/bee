<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('admin.user_panel') }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ setting('site_name', config('app.name')) }} {{ __('admin.welcome_to_panel') }}</p>
            </div>
            <div>
                <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/50 px-3 py-1 text-sm font-medium text-blue-700 dark:text-blue-300">
                    {{ setting('site_title', config('app.name')) }}
                </span>
            </div>
        </div>
    </x-slot>

    <x-profile-layout-styles />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
            @livewire('profile-sidebar')
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Welcome Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-8 sm:p-10 sm:pb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <h2 class="text-2xl font-bold text-white sm:text-3xl">
                                {{ __('admin.welcome') }}, {{ auth()->user()->name }}!
                            </h2>
                            <p class="mt-2 text-blue-100">
                                {{ setting('site_title', config('app.name')) }} {{ __('admin.dashboard_subtitle') }}
                            </p>
                            <div class="mt-4 flex items-center gap-4">
                                <span class="inline-flex items-center rounded-full bg-blue-800 dark:bg-blue-900 px-3 py-1 text-sm font-medium text-blue-100">
                                    {{ __('admin.profile_completed') }} 85%
                                </span>
                                <span class="inline-flex items-center rounded-full bg-green-500 dark:bg-green-600 px-3 py-1 text-sm font-medium text-white">
                                    {{ __('admin.active') }}
                                </span>
                            </div>
                        </div>
                        <div class="hidden lg:block">
                            <div class="h-2 w-64 overflow-hidden rounded-full bg-blue-800 dark:bg-blue-900">
                                <div class="h-2 bg-white" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SATIR 1: Stats - col-12 mobil, col-4 col-4 col-4 desktop -->
            <div class="grid grid-cols-12 gap-4">
                <!-- Account Status - col-4 -->
                <div class="col-span-12 md:col-span-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 h-full">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-indigo-500 dark:bg-indigo-600 flex items-center justify-center">
                                @if(auth()->user()->email_verified_at)
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.account_status') }}</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ auth()->user()->email_verified_at ? __('admin.verified') : __('admin.pending') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership Duration - col-4 -->
                <div class="col-span-12 md:col-span-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 h-full">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.membership_duration') }}</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ (int) auth()->user()->created_at->diffInDays(now()) }} {{ __('admin.days') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Role - col-4 -->
                <div class="col-span-12 md:col-span-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 h-full">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-purple-500 dark:bg-purple-600 flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.user_role') }}</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    @forelse(auth()->user()->roles as $role)
                                        {{ ucfirst($role->name) }}
                                    @empty
                                        {{ __('admin.users') }}
                                    @endforelse
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SATIR 2: Hızlı İşlemler col-8 + Hesap Özeti col-4 -->
            <div class="grid grid-cols-12 gap-4">
                <!-- Hızlı İşlemler - col-8 -->
                <div class="col-span-12 lg:col-span-8">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 h-full">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('admin.quick_actions') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ route('profile.edit') }}" class="block p-5 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center mb-3">
                                    <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ __('admin.edit_profile') }}</h4>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.personal_info') }}</p>
                            </a>

                            <a href="{{ route('profile.password') }}" class="block p-5 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-yellow-300 dark:hover:border-yellow-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
                                <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center mb-3">
                                    <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </div>
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ __('admin.change_password') }}</h4>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.increase_security') }}</p>
                            </a>

                            @if(\Nwidart\Modules\Facades\Module::has('Cart') && \Nwidart\Modules\Facades\Module::isEnabled('Cart') && \Nwidart\Modules\Facades\Module::has('Payment') && \Nwidart\Modules\Facades\Module::isEnabled('Payment'))
                            <a href="{{ route('shop.orders.index') }}" class="block p-5 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-green-300 dark:hover:border-green-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
                                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/50 flex items-center justify-center mb-3">
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ __('cart::front.my_orders') }}</h4>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('cart::front.view_order_history') }}</p>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Hesap Özeti - col-4 -->
                <div class="col-span-12 lg:col-span-4">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 h-full">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('admin.account_summary') }}</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.email') }}</dt>
                                <dd class="text-gray-900 dark:text-gray-100 truncate ml-2 max-w-[150px]">{{ auth()->user()->email }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.phone') }}</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ auth()->user()->phone ?? __('admin.not_specified') }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.registration_date') }}</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ auth()->user()->created_at->format('d.m.Y') }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.last_update') }}</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ auth()->user()->updated_at->format('d.m.Y') }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.ip_address') }}</dt>
                                <dd class="text-gray-900 dark:text-gray-100 font-mono text-xs">{{ request()->ip() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
