@extends('themes::' . config('theme.active_theme') . '.layouts.app')

@section('title', __('payment::front.payment_success'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            {{-- Başarı İkonu --}}
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white dark:bg-gray-100 rounded-full mb-4">
                    <i class="fa-solid fa-check text-5xl text-green-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    {{ __('payment::front.payment_successful') }}
                </h1>
                <p class="text-green-100">
                    {{ __('payment::front.payment_completed_message') }}
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

                    {{-- Ödeme Yöntemi --}}
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            <i class="fa-solid fa-credit-card mr-2"></i>
                            {{ __('payment::front.payment_method') }}
                        </span>
                        <span class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $payment->paymentMethod->getTranslated('title', app()->getLocale()) }}
                        </span>
                    </div>

                    {{-- Taksit Bilgisi --}}
                    @if($payment->installment_count > 1)
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            <i class="fa-solid fa-calendar-days mr-2"></i>
                            {{ __('payment::front.installment') }}
                        </span>
                        <span class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $payment->installment_count }}x {{ __('payment::front.installment') }}
                        </span>
                    </div>
                    @endif

                    {{-- Ödeme Tarihi --}}
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            <i class="fa-solid fa-calendar-check mr-2"></i>
                            {{ __('payment::front.payment_date') }}
                        </span>
                        <span class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $payment->paid_at->format('d.m.Y H:i') }}
                        </span>
                    </div>
                </div>

                {{-- Bilgilendirme --}}
                <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 mt-1 mr-3"></i>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <p class="font-medium mb-2">{{ __('payment::front.payment_confirmation_sent') }}</p>
                            <p>{{ __('payment::front.payment_confirmation_email_message') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Aksiyonlar --}}
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    @if($order)
                        <a href="{{ route('shop.orders.show', $order->order_id) }}"
                           class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fa-solid fa-box mr-2"></i>
                            {{ __('payment::front.view_order') }}
                        </a>
                    @endif

                    <a href="{{ route('shop.index') }}"
                       class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-home mr-2"></i>
                        {{ __('payment::front.continue_shopping') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
