<section x-data="{
    showDeleteForm: false,
    password: '',
    confirmed: false,
    isSubmitting: false,
    get canSubmit() {
        return this.password.length >= 1 && this.confirmed && !this.isSubmitting;
    }
}">
    {{-- Warning Box --}}
    <div class="bg-red-50 dark:bg-gray-800 border border-red-200 dark:border-red-500/30 rounded-lg p-6 mb-6">
        <div class="flex items-start">
            <i class="fa-solid fa-circle-exclamation text-xl text-red-500 dark:text-red-400 mt-1 mr-4 flex-shrink-0"></i>
            <div>
                <h3 class="text-lg font-semibold text-red-800 dark:text-red-400 mb-2">
                    Hesabı Kalıcı Olarak Sil
                </h3>
                <p class="text-red-700 dark:text-gray-300 mb-4">
                    Hesabınız silindiğinde, tüm kaynaklarınız ve verileriniz kalıcı olarak silinecektir.
                    Bu işlem <strong class="text-red-800 dark:text-red-400">geri alınamaz</strong> ve hesabınızla ilişkili tüm bilgiler tamamen kaldırılacaktır.
                </p>

                {{-- What will be deleted --}}
                <div class="bg-white dark:bg-gray-900 rounded-lg p-4 mb-4 border border-red-100 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">Silinecek Veriler:</h4>
                    <ul class="text-sm text-red-700 dark:text-gray-400 space-y-1">
                        <li class="flex items-center">
                            <i class="fa-solid fa-check text-sm mr-2 text-red-500 dark:text-red-500"></i>
                            Profil bilgileri ve kişisel veriler
                        </li>
                        <li class="flex items-center">
                            <i class="fa-solid fa-check text-sm mr-2 text-red-500 dark:text-red-500"></i>
                            Tüm hesap ayarları ve tercihler
                        </li>
                        <li class="flex items-center">
                            <i class="fa-solid fa-check text-sm mr-2 text-red-500 dark:text-red-500"></i>
                            Giriş geçmişi ve aktivite logları
                        </li>
                        <li class="flex items-center">
                            <i class="fa-solid fa-check text-sm mr-2 text-red-500 dark:text-red-500"></i>
                            Hesabınızla ilişkili tüm veriler
                        </li>
                    </ul>
                </div>

                <p class="text-sm text-red-600 dark:text-yellow-500 font-medium">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    Hesabınızı silmeden önce, saklamak istediğiniz tüm verileri indirdiğinizden emin olun.
                </p>
            </div>
        </div>
    </div>

    {{-- Delete Button (Toggle Form) --}}
    <div class="flex justify-center" x-show="!showDeleteForm">
        <button type="button"
                @click="showDeleteForm = true"
                class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
            <i class="fa-solid fa-trash-can mr-2"></i>
            Hesabı Kalıcı Olarak Sil
        </button>
    </div>

    {{-- Inline Delete Confirmation Form --}}
    <div x-show="showDeleteForm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mt-6 bg-red-50 dark:bg-gray-800 border-2 border-red-200 dark:border-red-500/50 rounded-xl p-6">

        {{-- Warning Header --}}
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-red-200 dark:border-red-500/30">
            <div class="w-12 h-12 bg-red-600 dark:bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-skull-crossbones text-white text-xl"></i>
            </div>
            <div>
                <h4 class="text-lg font-bold text-red-800 dark:text-white">Son Adım: Hesap Silme Onayı</h4>
                <p class="text-sm text-red-600 dark:text-red-400">Bu işlem geri alınamaz!</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.destroy') }}" @submit="isSubmitting = true">
            @csrf
            @method('delete')

            {{-- Password Field --}}
            <div class="mb-5">
                <label for="delete_password" class="block text-sm font-semibold text-red-800 dark:text-gray-200 mb-2">
                    <i class="fa-solid fa-lock mr-1 text-red-600 dark:text-red-400"></i>
                    Şifrenizi Girin
                </label>
                <input type="password"
                       id="delete_password"
                       name="password"
                       x-model="password"
                       class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-2 border-red-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:focus:border-red-500 transition-colors"
                       placeholder="Mevcut şifrenizi girin"
                       required
                       autocomplete="current-password">
                @error('password', 'userDeletion')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Confirmation Checkbox --}}
            <div class="mb-6">
                <label class="flex items-start gap-3 cursor-pointer group">
                    <div class="relative mt-1 flex-shrink-0">
                        <input type="checkbox"
                               id="confirm_deletion"
                               x-model="confirmed"
                               class="peer sr-only"
                               required>
                        <div class="w-5 h-5 border-2 border-red-300 dark:border-gray-500 rounded bg-white dark:bg-gray-900 peer-checked:bg-red-600 peer-checked:border-red-600 peer-focus:ring-2 peer-focus:ring-red-500 peer-focus:ring-offset-2 dark:peer-focus:ring-offset-gray-800 transition-colors"></div>
                        <i class="fa-solid fa-check absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></i>
                    </div>
                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                        <strong class="text-red-700 dark:text-red-400">Hesabımın kalıcı olarak silineceğini</strong> ve bu işlemin <strong class="text-red-700 dark:text-red-400">geri alınamayacağını</strong> anlıyorum ve onaylıyorum.
                    </span>
                </label>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                        :disabled="!canSubmit"
                        :class="canSubmit ? 'bg-red-600 hover:bg-red-700 cursor-pointer' : 'bg-red-300 dark:bg-red-900 cursor-not-allowed'"
                        class="flex-1 inline-flex items-center justify-center px-6 py-3 text-white font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <template x-if="isSubmitting">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Siliniyor...
                        </span>
                    </template>
                    <template x-if="!isSubmitting">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-trash-can"></i>
                            Hesabımı Kalıcı Olarak Sil
                        </span>
                    </template>
                </button>

                <button type="button"
                        @click="showDeleteForm = false; password = ''; confirmed = false;"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <i class="fa-solid fa-xmark mr-2"></i>
                    Vazgeç
                </button>
            </div>
        </form>
    </div>
</section>
