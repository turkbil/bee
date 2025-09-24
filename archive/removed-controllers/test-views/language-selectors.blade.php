@extends('admin.layout')

@section('title', 'Dil Seçici Alternatifleri Test')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Test</div>
                    <h2 class="page-title">Dil Seçici Alternatifleri</h2>
                    <p class="text-muted">Gerçek Page modülü tasarımında kullanılabilecek alternatifler</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container">

            <!-- Page modülü benzeri Card yapısı -->
            @php
                $alternatives = [
                    [
                        'id' => 1,
                        'title' => 'Kompakt Button Group',
                        'description' => 'Mevcut kullanılan tasarım - minimal ve temiz',
                        'html' => '<div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-primary">TR</button>
                                    <button class="btn btn-sm btn-outline-primary">EN</button>
                                    <button class="btn btn-sm btn-outline-primary">AR</button>
                                </div>'
                    ],
                    [
                        'id' => 2, 
                        'title' => 'Pills Style',
                        'description' => 'Nav pills stili - daha yumuşak görünüm',
                        'html' => '<ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link nav-link-sm active" href="#">TR</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link nav-link-sm" href="#">EN</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link nav-link-sm" href="#">AR</a>
                                    </li>
                                </ul>'
                    ],
                    [
                        'id' => 3,
                        'title' => 'Badge Style', 
                        'description' => 'Badge/etiket stili - küçük ve kompakt',
                        'html' => '<div class="d-flex gap-1">
                                    <span class="badge bg-primary" style="cursor: pointer; padding: 8px 12px;">TR</span>
                                    <span class="badge bg-light text-dark" style="cursor: pointer; padding: 8px 12px;">EN</span>
                                    <span class="badge bg-light text-dark" style="cursor: pointer; padding: 8px 12px;">AR</span>
                                </div>'
                    ],
                    [
                        'id' => 4,
                        'title' => 'Underline Style',
                        'description' => 'Alt çizgi ile vurgulama - modern görünüm',
                        'html' => '<div class="d-flex gap-3">
                                    <button class="btn btn-link p-2 text-primary border-0" style="border-bottom: 2px solid var(--tblr-primary) !important;">TR</button>
                                    <button class="btn btn-link p-2 text-muted border-0">EN</button>
                                    <button class="btn btn-link p-2 text-muted border-0">AR</button>
                                </div>'
                    ],
                    [
                        'id' => 5,
                        'title' => 'Rounded Pills',
                        'description' => 'Yuvarlak köşeli butonlar - soft görünüm',
                        'html' => '<div class="d-flex gap-2">
                                    <button class="btn btn-primary rounded-pill px-3">TR</button>
                                    <button class="btn btn-outline-primary rounded-pill px-3">EN</button>
                                    <button class="btn btn-outline-primary rounded-pill px-3">AR</button>
                                </div>'
                    ],
                    [
                        'id' => 6,
                        'title' => 'Minimal Text',
                        'description' => 'Sadece metin - ultra minimal',
                        'html' => '<div class="d-flex gap-3">
                                    <span class="fw-bold text-primary" style="cursor: pointer;">TR</span>
                                    <span class="text-muted" style="cursor: pointer;">EN</span>
                                    <span class="text-muted" style="cursor: pointer;">AR</span>
                                </div>'
                    ],
                    [
                        'id' => 7,
                        'title' => 'Square Buttons',
                        'description' => 'Kare butonlar - belirgin görünüm',
                        'html' => '<div class="d-flex gap-1">
                                    <button class="btn btn-light border active" style="width: 40px; height: 40px;">TR</button>
                                    <button class="btn btn-light border" style="width: 40px; height: 40px;">EN</button>
                                    <button class="btn btn-light border" style="width: 40px; height: 40px;">AR</button>
                                </div>'
                    ],
                    [
                        'id' => 8,
                        'title' => 'Chip Style',
                        'description' => 'Chip stili - oval formda',
                        'html' => '<div class="d-flex gap-2">
                                    <span class="badge bg-primary rounded-pill fs-6 py-2 px-4" style="cursor: pointer;">TR</span>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill fs-6 py-2 px-4" style="cursor: pointer;">EN</span>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill fs-6 py-2 px-4" style="cursor: pointer;">AR</span>
                                </div>'
                    ]
                ];
            @endphp

            @foreach($alternatives as $alt)
            <!-- {{ $alt['title'] }} -->
            <div class="card mb-4" style="border-radius: 0px;">
                <div class="card-header" style="border-radius: 0px;">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-{{ $alt['id'] }}-1" class="nav-link active" data-bs-toggle="tab" style="border-radius: 0px;">
                                <i class="fas fa-info-circle me-2"></i>{{ $alt['id'] }}. {{ $alt['title'] }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-{{ $alt['id'] }}-2" class="nav-link" data-bs-toggle="tab" style="border-radius: 0px;">
                                <i class="fas fa-search me-2"></i>SEO
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tabs-{{ $alt['id'] }}-3" class="nav-link" data-bs-toggle="tab" style="border-radius: 0px;">
                                <i class="fas fa-code me-2"></i>Kod Alanı
                            </a>
                        </li>
                        <li class="nav-item ms-auto" role="presentation">
                            <a href="#" class="nav-link px-3 py-2 bg-primary text-white" style="border-radius: 0px;">
                                <i class="fas fa-wand-magic-sparkles me-2"></i>Studio
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            {!! $alt['html'] !!}
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="tabs-{{ $alt['id'] }}-1" role="tabpanel">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" value="Anasayfa" style="border-radius: 0px;">
                                <label>Başlık (Türkçe) *</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" style="border-radius: 0px; height: 120px;">{{ $alt['description'] }}</textarea>
                                <label>İçerik (Türkçe)</label>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabs-{{ $alt['id'] }}-2" role="tabpanel">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" value="anasayfa" style="border-radius: 0px;">
                                <label>Slug (Türkçe)</label>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabs-{{ $alt['id'] }}-3" role="tabpanel">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" style="border-radius: 0px; height: 100px;"></textarea>
                                <label>CSS</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="border-radius: 0px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="#" class="btn btn-link text-decoration-none" style="border-radius: 0px;">İptal</a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn" style="border-radius: 0px;">
                                <i class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et
                            </button>
                            <button type="button" class="btn btn-primary" style="border-radius: 0px;">
                                <i class="fa-thin fa-floppy-disk me-2"></i> Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>

<style>
/* Interactive styles for language selectors */
.btn-group .btn {
    border-right: 1px solid rgba(var(--tblr-primary-rgb), 0.2) !important;
}

.btn-group .btn:last-child {
    border-right: none !important;
}

.nav-pills .nav-link {
    border-radius: 0.5rem !important;
    padding: 0.5rem 1rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1rem;
}

/* Make all language selectors interactive */
[style*="cursor: pointer"] {
    transition: all 0.15s ease;
}

[style*="cursor: pointer"]:hover {
    opacity: 0.8;
}
</style>

<script>
// Make all language selectors interactive
document.addEventListener('DOMContentLoaded', function() {
    // Button groups
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from siblings
            this.parentElement.querySelectorAll('.btn').forEach(sibling => {
                sibling.classList.remove('btn-primary');
                sibling.classList.add('btn-outline-primary');
            });
            // Add active to this button
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
        });
    });

    // Nav pills
    document.querySelectorAll('.nav-pills .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            // Remove active from siblings
            this.parentElement.parentElement.querySelectorAll('.nav-link').forEach(sibling => {
                sibling.classList.remove('active');
            });
            // Add active to this link
            this.classList.add('active');
        });
    });

    // Badges
    document.querySelectorAll('.badge[style*="cursor"]').forEach(badge => {
        badge.addEventListener('click', function() {
            // Remove active from siblings
            this.parentElement.querySelectorAll('.badge').forEach(sibling => {
                sibling.classList.remove('bg-primary');
                sibling.classList.add('bg-light', 'text-dark');
            });
            // Add active to this badge
            this.classList.remove('bg-light', 'text-dark');
            this.classList.add('bg-primary');
        });
    });

    // Underline buttons
    document.querySelectorAll('.btn-link').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Remove active from siblings
            this.parentElement.querySelectorAll('.btn-link').forEach(sibling => {
                sibling.classList.remove('text-primary');
                sibling.classList.add('text-muted');
                sibling.style.borderBottom = 'none';
            });
            // Add active to this button
            this.classList.remove('text-muted');
            this.classList.add('text-primary');
            this.style.borderBottom = '2px solid var(--tblr-primary)';
        });
    });

    // Rounded pills
    document.querySelectorAll('.rounded-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            // Remove active from siblings
            this.parentElement.querySelectorAll('.rounded-pill').forEach(sibling => {
                sibling.classList.remove('btn-primary');
                sibling.classList.add('btn-outline-primary');
            });
            // Add active to this pill
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
        });
    });

    // Minimal text spans
    document.querySelectorAll('span[style*="cursor"]').forEach(span => {
        if (!span.classList.contains('badge')) {
            span.addEventListener('click', function() {
                // Remove active from siblings
                this.parentElement.querySelectorAll('span').forEach(sibling => {
                    if (!sibling.classList.contains('badge')) {
                        sibling.classList.remove('fw-bold', 'text-primary');
                        sibling.classList.add('text-muted');
                    }
                });
                // Add active to this span
                this.classList.remove('text-muted');
                this.classList.add('fw-bold', 'text-primary');
            });
        }
    });

    // Square buttons
    document.querySelectorAll('button[style*="width: 40px"]').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from siblings
            this.parentElement.querySelectorAll('button').forEach(sibling => {
                sibling.classList.remove('active');
            });
            // Add active to this button
            this.classList.add('active');
        });
    });
});
</script>
@endsection