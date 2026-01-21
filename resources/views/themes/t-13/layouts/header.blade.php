{{-- Header - Düzenlenecek --}}
<header class="bg-white border-b">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-xl font-bold">
                {{ setting('site_title') ?: 'Site Adı' }}
            </a>
            <nav class="flex gap-4">
                <a href="{{ url('/') }}" class="hover:text-blue-600">Ana Sayfa</a>
            </nav>
        </div>
    </div>
</header>