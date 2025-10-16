<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İXTİF - İletişim Bilgileri</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white min-h-screen flex items-center justify-center p-8">

    <div class="max-w-5xl w-full space-y-12">

        <!-- Header -->
        <div class="text-center space-y-4">
            <img src="{{ $logoUrl }}" alt="İXTİF Logo" class="h-24 mx-auto">
            <p class="text-2xl text-blue-300 font-semibold">Türkiye'nin İstif Pazarı</p>
            <div class="h-1 w-48 bg-gradient-to-r from-transparent via-blue-400 to-transparent mx-auto"></div>
        </div>

        <!-- Contact Grid -->
        <div class="grid md:grid-cols-2 gap-8">

            <!-- İletişim Bilgileri -->
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 space-y-6">
                <h2 class="text-3xl font-bold mb-6 flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                    </svg>
                    İletişim
                </h2>

                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-blue-400 mt-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                        </svg>
                        <div>
                            <div class="text-blue-200 text-sm">Telefon</div>
                            <div class="text-xl font-bold">0216 755 3 555</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-green-400 mt-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                            <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"></path>
                        </svg>
                        <div>
                            <div class="text-blue-200 text-sm">WhatsApp</div>
                            <div class="text-xl font-bold">0501 005 67 58</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-purple-400 mt-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <div>
                            <div class="text-blue-200 text-sm">E-posta</div>
                            <div class="text-xl font-bold">info@ixtif.com</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-yellow-400 mt-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <div class="text-blue-200 text-sm">Web</div>
                            <div class="text-xl font-bold">ixtif.com</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hizmetler -->
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                <h2 class="text-3xl font-bold mb-6 flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
                    </svg>
                    Hizmetlerimiz
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/5 rounded-lg p-4 text-center">
                        <div class="text-3xl mb-2">📦</div>
                        <div class="font-semibold">Sıfır Ürünler</div>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4 text-center">
                        <div class="text-3xl mb-2">♻️</div>
                        <div class="font-semibold">İkinci El</div>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4 text-center">
                        <div class="text-3xl mb-2">🔑</div>
                        <div class="font-semibold">Kiralama</div>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4 text-center">
                        <div class="text-3xl mb-2">🔧</div>
                        <div class="font-semibold">Teknik Servis</div>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4 text-center">
                        <div class="text-3xl mb-2">⚙️</div>
                        <div class="font-semibold">Yedek Parça</div>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4 text-center">
                        <div class="text-3xl mb-2">🛡️</div>
                        <div class="font-semibold">Bakım</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="text-center space-y-4 border-t border-white/10 pt-8">
            <p class="text-blue-200">
                Bu katalog {{ $catalogDate }} tarihinde oluşturulmuştur.
            </p>
            <p class="text-sm text-blue-300">
                Güncel fiyatlar ve detaylı bilgi için lütfen bizimle iletişime geçiniz.
            </p>
            <p class="text-xs text-slate-400">
                © 2025 İXTİF - Türkiye'nin İstif Pazarı | Tüm hakları saklıdır.
            </p>
        </div>

    </div>

</body>
</html>
