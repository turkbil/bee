<div>
    @if($submitted)
        {{-- Success Message --}}
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6 text-center">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-3xl text-green-600 dark:text-green-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-green-800 dark:text-green-300 mb-2">Mesajınız Gönderildi!</h3>
            <p class="text-green-700 dark:text-green-400">En kısa sürede size dönüş yapacağız.</p>
            <button wire:click="$set('submitted', false)" class="mt-4 text-sm text-green-600 dark:text-green-400 hover:underline">
                Yeni mesaj gönder
            </button>
        </div>
    @else
        {{-- Error Message --}}
        @if($errorMessage)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                    <p class="text-red-700 dark:text-red-400">{{ $errorMessage }}</p>
                </div>
            </div>
        @endif

        {{-- Contact Form --}}
        <form wire:submit="submit" class="space-y-6">
            <div class="grid sm:grid-cols-2 gap-6">
                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Ad Soyad *</label>
                    <input
                        type="text"
                        wire:model="name"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 transition-all @error('name') border-red-500 @enderror"
                        placeholder="Adınız Soyadınız"
                    >
                    @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Telefon</label>
                    <input
                        type="tel"
                        wire:model="phone"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 transition-all"
                        placeholder="+90 (___) ___ __ __"
                    >
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">E-posta *</label>
                <input
                    type="email"
                    wire:model="email"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 transition-all @error('email') border-red-500 @enderror"
                    placeholder="email@example.com"
                >
                @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Subject (Optional) --}}
            @if($showSubject)
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konu</label>
                <select
                    wire:model="subject"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white transition-all"
                >
                    <option value="">Konu Seçiniz</option>
                    <option value="Genel Bilgi">Genel Bilgi</option>
                    <option value="Teklif Talebi">Teklif Talebi</option>
                    <option value="Destek">Destek</option>
                    <option value="Diğer">Diğer</option>
                </select>
            </div>
            @endif

            {{-- Message --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mesajınız *</label>
                <textarea
                    rows="4"
                    wire:model="message"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 resize-none transition-all @error('message') border-red-500 @enderror"
                    placeholder="Mesajınızı yazınız..."
                ></textarea>
                @error('message') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Submit Button --}}
            <button
                type="submit"
                class="w-full px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="submit">
                    <i class="fas fa-paper-plane mr-2"></i>
                    {{ $buttonText }}
                </span>
                <span wire:loading wire:target="submit">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Gönderiliyor...
                </span>
            </button>

            <p class="text-xs text-slate-500 dark:text-slate-400 text-center">* Zorunlu alanlar. Bilgileriniz gizli tutulacaktır.</p>
        </form>
    @endif
</div>
