<footer class="ml-64 bg-spotify-dark text-white border-t border-white/10 pb-32">
    <div class="max-w-7xl mx-auto px-8 py-12">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Sol Taraf - İçerik -->
            <div>
                <div class="mb-8">
                    <div class="mb-4">
                        {!! app(\App\Services\LogoService::class)->renderFooterLogo(['class' => 'h-10 w-auto']) !!}
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        İşletmenize yasal ve telifsiz müzik. Cafe, restoran, mağaza ve ofisleriniz için profesyonel müzik çözümü.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <h5 class="font-bold mb-4 text-sm uppercase tracking-wider text-gray-500">ŞİRKET</h5>
                        <ul class="space-y-2">
                            <li><a href="/hakkimizda" class="text-gray-400 hover:text-white transition-colors">Hakkımızda</a></li>
                            <li><a href="/iletisim" class="text-gray-400 hover:text-white transition-colors">İletişim</a></li>
                            <li><a href="/sss" class="text-gray-400 hover:text-white transition-colors">Sık Sorulan Sorular</a></li>
                            <li><a href="/blog" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="font-bold mb-4 text-sm uppercase tracking-wider text-gray-500">YASAL</h5>
                        <ul class="space-y-2">
                            <li><a href="/kullanim-sartlari" class="text-gray-400 hover:text-white transition-colors">Kullanım Şartları</a></li>
                            <li><a href="/gizlilik-politikasi" class="text-gray-400 hover:text-white transition-colors">Gizlilik Politikası</a></li>
                            <li><a href="/cerez-politikasi" class="text-gray-400 hover:text-white transition-colors">Çerez Politikası</a></li>
                            <li><a href="/iptal-iade" class="text-gray-400 hover:text-white transition-colors">İptal ve İade</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/10 pt-6 mb-6">
                    <h5 class="font-bold mb-4 text-sm">Bizi Takip Edin</h5>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-spotify-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-spotify-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-spotify-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-spotify-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-youtube text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-spotify-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                    </div>
                </div>

                <div class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} Muzibu. Tüm hakları saklıdır.
                </div>
            </div>

            <!-- Sağ Taraf - İletişim -->
            <div class="bg-spotify-gray rounded-2xl p-8">
                <h4 class="text-2xl font-bold mb-6">Destek</h4>

                <div class="space-y-4">
                    <a href="https://wa.me/908501234567" target="_blank" class="flex items-center gap-4 p-4 bg-white/5 hover:bg-white/10 rounded-xl transition-all group">
                        <div class="w-12 h-12 bg-[#25D366] rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fab fa-whatsapp text-white text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400 mb-1">WhatsApp</div>
                            <div class="font-semibold text-white group-hover:text-spotify-green transition-colors">Hemen Mesaj Gönder</div>
                        </div>
                    </a>

                    <a href="mailto:destek@muzibu.com" class="flex items-center gap-4 p-4 bg-white/5 hover:bg-white/10 rounded-xl transition-all group">
                        <div class="w-12 h-12 bg-spotify-green rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400 mb-1">E-posta</div>
                            <div class="font-semibold text-white group-hover:text-spotify-green transition-colors">destek@muzibu.com</div>
                        </div>
                    </a>

                    <button class="w-full flex items-center gap-4 p-4 bg-spotify-green hover:bg-spotify-green-light rounded-xl transition-all group">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-comment-dots text-white text-xl"></i>
                        </div>
                        <div class="text-left">
                            <div class="text-sm text-white/80 mb-1">Canlı Destek</div>
                            <div class="font-bold text-white">Hemen Sohbet Başlat</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>
