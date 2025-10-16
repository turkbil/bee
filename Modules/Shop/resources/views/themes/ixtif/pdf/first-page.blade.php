<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $productTitle }} - İXTİF Ürün Kataloğu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen flex">

    <!-- Left Side - Orange -->
    <div class="w-1/3 bg-gradient-to-br from-orange-500 to-orange-700 text-white p-12 flex flex-col justify-between">
        <div>
            <img src="{{ $logoUrl }}" alt="İXTİF Logo" class="h-24 mb-12">

            <div class="space-y-6">
                <h2 class="text-3xl font-bold">
                    Türkiye'nin<br>İstif Pazarı
                </h2>
                <div class="h-1 w-20 bg-white/50"></div>
                <p class="text-xl font-mono text-orange-100">
                    ixtif.com
                </p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center gap-3 text-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                </svg>
                <span>0216 755 3 555</span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                </svg>
                <span>info@ixtif.com</span>
            </div>
            <div class="text-xs text-orange-200 mt-6">
                Katalog Tarihi: {{ $catalogDate }}
            </div>
        </div>
    </div>

    <!-- Right Side - White -->
    <div class="flex-1 p-16 flex flex-col justify-center">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-orange-100 text-orange-800 px-6 py-3 rounded-full text-sm font-bold mb-8">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                </svg>
                <span class="uppercase tracking-wider">Ürün Kataloğu</span>
            </div>

            <h1 class="text-7xl font-black text-slate-900 leading-tight mb-8">
                {{ $productTitle }}
            </h1>

            <div class="h-2 w-64 bg-gradient-to-r from-orange-500 to-transparent rounded-full mb-12"></div>

            <p class="text-xl text-slate-600 mb-12">
                Detaylı teknik özellikler ve ürün bilgileri
            </p>

            <!-- Product Image -->
            @if($productImage)
            <div class="border-4 border-orange-200 rounded-2xl overflow-hidden">
                <div class="aspect-[4/3] bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
                    <img src="{{ $productImage }}" alt="{{ $productTitle }}" class="w-full h-full object-contain p-8">
                </div>
            </div>
            @else
            <div class="border-4 border-orange-200 rounded-2xl overflow-hidden">
                <div class="aspect-[4/3] bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
                    <svg class="w-32 h-32 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            @endif
        </div>
    </div>

</body>
</html>
