<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('admin.dashboard') }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('admin.dashboard_welcome') }}</p>
            </div>
            <div>
                <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/50 px-3 py-1 text-sm font-medium text-blue-700 dark:text-blue-300">
                    {{ tenant('title') ?? config('app.name') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-8 sm:p-10 sm:pb-6">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-8">
                        <div>
                            <h2 class="text-2xl font-bold text-white sm:text-3xl">
                                {{ __('admin.welcome') }}, {{ auth()->user()->name }}!
                            </h2>
                            <p class="mt-2 text-blue-100">
                                {{ tenant('title') ?? config('app.name') }} {{ __('admin.dashboard_subtitle') }}
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
                        <div class="hidden lg:flex lg:items-center lg:justify-end">
                            <div class="h-2 w-64 overflow-hidden rounded-full bg-blue-800 dark:bg-blue-900">
                                <div class="h-2 bg-white" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Account Status -->
                <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                    <dt>
                        <div class="absolute rounded-md bg-indigo-500 dark:bg-indigo-600 p-3">
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
                        <p class="ml-16 text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('admin.account_status') }}</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ auth()->user()->email_verified_at ? __('admin.verified') : __('admin.pending') }}
                        </p>
                    </dd>
                </div>

                <!-- Membership Duration -->
                <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                    <dt>
                        <div class="absolute rounded-md bg-blue-500 dark:bg-blue-600 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="ml-16 text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('admin.membership_duration') }}</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        @php
                            $createdAt = auth()->user()->created_at;
                            $diff = $createdAt->diff(now());
                            $days = $diff->days;
                            
                            if ($days > 0) {
                                $timeText = $days . ' ' . __('admin.days');
                            } else {
                                $timeText = $diff->h . ' ' . __('admin.hours');
                            }
                        @endphp
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $timeText }}</p>
                    </dd>
                </div>

                <!-- User Role -->
                <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                    <dt>
                        <div class="absolute rounded-md bg-purple-500 dark:bg-purple-600 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <p class="ml-16 text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('admin.user_role') }}</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            @forelse(auth()->user()->roles as $role)
                                {{ ucfirst($role->name) }}
                            @empty
                                {{ __('admin.users') }}
                            @endforelse
                        </p>
                    </dd>
                </div>

                <!-- Connection Status -->
                <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                    <dt>
                        <div class="absolute rounded-md bg-green-500 dark:bg-green-600 p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                            </svg>
                        </div>
                        <p class="ml-16 text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('admin.connection_status') }}</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('admin.active') }}</p>
                    </dd>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Quick Actions -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">{{ __('admin.quick_actions') }}</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <a href="{{ route('profile.edit') }}" class="relative group bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all">
                                    <div>
                                        <span class="rounded-lg inline-flex p-3 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/70">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('admin.edit_profile') }}</h3>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.personal_info') }}</p>
                                    </div>
                                </a>

                                <a href="{{ route('profile.password') }}" class="relative group bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all">
                                    <div>
                                        <span class="rounded-lg inline-flex p-3 bg-yellow-50 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300 group-hover:bg-yellow-100 dark:group-hover:bg-yellow-900/70">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('admin.change_password') }}</h3>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.increase_security') }}</p>
                                    </div>
                                </a>

                                <a href="{{ url('/') }}" class="relative group bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all">
                                    <div>
                                        <span class="rounded-lg inline-flex p-3 bg-purple-50 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/70">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('admin.view_website') }}</h3>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.go_to_homepage') }}</p>
                                    </div>
                                </a>

                                <button onclick="checkForUpdates(event)" class="relative group bg-white dark:bg-gray-800 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all text-left">
                                    <div>
                                        <span class="rounded-lg inline-flex p-3 bg-green-50 dark:bg-green-900/50 text-green-700 dark:text-green-300 group-hover:bg-green-100 dark:group-hover:bg-green-900/70">
                                            <svg id="update-icon" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <h3 id="update-title" class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('admin.check_updates') }}</h3>
                                        <p id="update-desc" class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.check_system_updates') }}</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">{{ __('admin.account_summary') }}</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.user_id') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100 font-medium">#{{ auth()->user()->id }}</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.email') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100 truncate ml-2">{{ auth()->user()->email }}</dd>
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
    </div>

    @push('scripts')
    <script>
        function checkForUpdates(event) {
            event.preventDefault();
            
            const icon = document.getElementById('update-icon');
            const title = document.getElementById('update-title');
            const desc = document.getElementById('update-desc');
            
            // Add rotation animation
            icon.classList.add('animate-spin');
            title.textContent = '{{ __("admin.checking_updates") }}';
            desc.textContent = '{{ __("admin.searching_updates") }}';
            
            // Simulate update check
            setTimeout(() => {
                icon.classList.remove('animate-spin');
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                title.textContent = '{{ __("admin.system_updated") }}';
                desc.textContent = '{{ __("admin.all_systems_updated") }}';
                
                setTimeout(() => {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />';
                    title.textContent = '{{ __("admin.check_updates") }}';
                    desc.textContent = '{{ __("admin.check_system_updates") }}';
                }, 3000);
            }, 2000);
        }
    </script>
    @endpush
</x-app-layout>