@extends('themes::' . config('theme.active_theme') . '.layouts.app')

@section('title', __('payment::front.payment_failed'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            {{-- Hata İkonu --}}
            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white dark:bg-gray-100 rounded-full mb-4">
                    <i class="fa-solid fa-times text-5xl text-red-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    {{ __('payment::front.payment_failed') }}
                </h1>
                <p class="text-red-100">
                    {{ __('payment::front.payment_failed_message') }}
                </p>
            </div>

            {{-- Ödeme Detayları --}}
            <div class="p-8">
                <div class="space-y-6">
                    {{-- Sipariş Numarası --}}
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            <i class="fa-solid fa-hashtag mr-2"></i>
                            {{ __('payment::front.transaction_id') }}
                        </span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $payment->transaction_id }}
                        </span>
                    </div>

                    {{-- Ödeme Tutarı --}}
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            <i class="fa-solid fa-money-bill-wave mr-2"></i>
                            {{ __('payment::front.payment_amount') }}
                        </span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($payment->amount, 2, ',', '.') }} {{ $payment->currency }}
                        </span>
                    </div>

                    {{-- Hata Nedeni --}}
                    @if($payment->gateway_response && isset($payment->gateway_response['failed_reason_msg']))
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fa-solid fa-exclamation-triangle text-red-600 dark:text-red-400 mt-1 mr-3"></i>
                            <div class="text-sm text-red-800 dark:text-red-300">
                                <p class="font-medium mb-1">{{ __('payment::front.failure_reason') }}:</p>
                                <p>{{ $payment->gateway_response['failed_reason_msg'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Bilgilendirme --}}
                <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fa-solid fa-info-circle text-yellow-600 dark:text-yellow-400 mt-1 mr-3"></i>
                        <div class="text-sm text-yellow-800 dark:text-yellow-300">
                            <p class="font-medium mb-2">{{ __('payment::front.what_happened') }}</p>
                            <ul class="list-disc list-inside space-y-1 ml-2">
                                <li>{{ __('payment::front.payment_not_processed') }}</li>
                                <li>{{ __('payment::front.no_charge_made') }}</li>
                                <li>{{ __('payment::front.can_try_again') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Aksiyonlar --}}
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    @if($order)
                        <a href="{{ route('cart.checkout') }}"
                           class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fa-solid fa-rotate-right mr-2"></i>
                            {{ __('payment::front.try_again') }}
                        </a>
                    @endif

                    <a href="{{ url('/') }}"
                       class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-home mr-2"></i>
                        {{ __('payment::front.back_to_home') }}
                    </a>
                </div>

                {{-- Destek --}}
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('payment::front.need_help') }}
                        <a href="{{ route('contact') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            {{ __('payment::front.contact_support') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
