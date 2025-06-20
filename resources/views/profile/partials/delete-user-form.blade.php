<section>
    <!-- Warning Box -->
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mt-1 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">
                    Hesabı Kalıcı Olarak Sil
                </h3>
                <p class="text-red-700 dark:text-red-300 mb-4">
                    Hesabınız silindiğinde, tüm kaynaklarınız ve verileriniz kalıcı olarak silinecektir. 
                    Bu işlem <strong>geri alınamaz</strong> ve hesabınızla ilişkili tüm bilgiler tamamen kaldırılacaktır.
                </p>
                
                <!-- What will be deleted -->
                <div class="bg-white dark:bg-red-950/30 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Silinecek Veriler:</h4>
                    <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Profil bilgileri ve kişisel veriler
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Tüm hesap ayarları ve tercihler
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Giriş geçmişi ve aktivite logları
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Hesabınızla ilişkili tüm veriler
                        </li>
                    </ul>
                </div>
                
                <p class="text-sm text-red-600 dark:text-red-400 font-medium">
                    ⚠️ Hesabınızı silmeden önce, saklamak istediğiniz tüm verileri indirdiğinizden emin olun.
                </p>
            </div>
        </div>
    </div>

    <!-- Delete Button -->
    <div class="flex justify-center">
        <button type="button" 
                onclick="openDeleteModal()"
                class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hesabı Kalıcı Olarak Sil
        </button>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteAccountModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirmDeletion()">
                    @csrf
                    @method('delete')
                    
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Hesabı Sil
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    Bu işlem geri alınamaz. Hesabınızı silmek istediğinizden emin misiniz?
                                </p>
                                
                                <!-- Password confirmation -->
                                <div class="mb-4">
                                    <label for="delete_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Şifrenizi onaylayın
                                    </label>
                                    <input type="password" 
                                           id="delete_password"
                                           name="password" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-gray-100 @error('password', 'userDeletion') border-red-500 @enderror" 
                                           placeholder="Mevcut şifrenizi girin" 
                                           required>
                                    @error('password', 'userDeletion')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Confirmation checkbox -->
                                <div class="flex items-start mb-4">
                                    <input type="checkbox" 
                                           id="confirm_deletion" 
                                           class="mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500" 
                                           required>
                                    <label for="confirm_deletion" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                        Hesabımın kalıcı olarak silineceğini ve bu işlemin geri alınamayacağını anlıyorum.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse space-y-2 sm:space-y-0 sm:space-x-2 sm:space-x-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hesabı Sil
                        </button>
                        <button type="button" 
                                onclick="closeDeleteModal()"
                                class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm transition-colors">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            document.getElementById('deleteAccountModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteAccountModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Reset form
            document.getElementById('delete_password').value = '';
            document.getElementById('confirm_deletion').checked = false;
        }

        function confirmDeletion() {
            const password = document.getElementById('delete_password').value;
            const confirmed = document.getElementById('confirm_deletion').checked;
            
            if (!password) {
                alert('Lütfen şifrenizi girin.');
                return false;
            }
            
            if (!confirmed) {
                alert('Lütfen silme işlemini onaylayın.');
                return false;
            }
            
            return confirm('Bu işlem geri alınamaz. Hesabınızı silmek istediğinizden emin misiniz?');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</section>