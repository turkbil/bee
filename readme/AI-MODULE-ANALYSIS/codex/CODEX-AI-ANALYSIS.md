# Codex — AI Modülü Derinlemesine Analiz

Tarih: 2025-09-19
Kapsam: `Modules/AI` modülü (servisler, rotalar, konfig, entegrasyonlar)

## Mimari ve Dosya Yapısı
- Servis Katmanı: `Modules/AI/app/Services` altında geniş bir dağılım (Core, Translation, Content, V3, Templates, Logging, Universal vb.).
- Büyük ve Merkezî Sınıflar:
  - `AIService.php` (ana orkestrasyon; tek dosyada çok kapsam): prod ağacında `AIService.php.backup` ve `AIService.php.bak` duruyor.
  - `AIResponseRepository.php`, `SmartHtmlTranslationService.php`, `AdvancedSeoIntegrationService.php`, `Content/AIContentGeneratorService.php` boyut ve sorumluluk olarak geniş.
- Çeviri Yığını:
  - `Translation/AITranslationService.php`, `Translation/CentralizedTranslationService.php`, `HtmlTranslationService.php`, `FastHtmlTranslationService.php`, `SmartHtmlTranslationService.php` → yeteneklerin çakışma riski.

## Rotalar ve Güvenlik
- Admin/Debug Rotaları: `Modules/AI/routes/admin.php` altında `debug` grubu `role:root` ile sınırlandırılmış; monitoring ve profil API’leri `module.permission:ai,*` ile korunuyor.
- Web API’leri: `Modules/AI/routes/web.php`’de admin `auth:web` altında içerik üretimi/çeviri uçları; `throttle` ve `ai.tokens` alias’larının standart uygulanması önerilir.

## Konfigürasyon (config/ai.php)
- Provider’lar: `openai`, `deepseek`, `anthropic` tanımlı. Varsayılan `default_provider = openai`, `fallback_provider = deepseek`.
- Performans:
  - `use_queue_for_long_requests = false` (öneri: true)
  - `cache_responses = false` (öneri: hash’lenmiş input + TTL ile seçici cache)
- Güvenlik:
  - `enable_rate_limiting = true`, `rate_limit_key = ip|user|tenant` seçenekleri var; rota düzeyinde uygulama eksik.
  - `log_responses = false` varsayılanı yerinde (PII sızıntı riskini azaltır).

## Kalite ve Bakım
- Sorunlar:
  - Tek dosyada çok sorumluluk (God Class): AIService ve bazı yardımcı katmanlar.
  - Yedek/duplikasyon dosyaları prod kaynaklarında.
  - Çeviri yetenekleri birden fazla servisle tekrar ediyor; sınırlar belirsiz.
- Önerilen Refactor Adımları:
  1) AIService’i yetenek-trait’lerine ayır: `HandlesTranslation`, `HandlesChat`, `HandlesContentGeneration`, `ProviderRouting`, `CreditAccounting`.
  2) Çeviri hattını tek bir “pipeline”e indir: Preprocess → Prompt Build → Provider Call → Stream/Chunk → Postprocess → Persist.
  3) Provider Katmanı: Ortak arayüz + strateji deseni; OpenAI/DeepSeek/Anthropic sürücüleri sade.
  4) Uzun işlemler: Kuyruğa al (queue: `ai-content`), idempotent job anahtarı + progress cache.
  5) Monitoring: Token kullanım ölçümü, provider hata oranı, fallback ratio; Horizon/Pulse grafikleri.

## Güvenlik ve Kullanım Kontrolleri
- Kimlik Doğrulama: Admin altında; ancak özel AI uçlarına `throttle:ai` benzeri üretim profili ve `ai.tokens` eklenmeli.
- CORS: Global CORS `*`; AI uçlarına özel `allowed_origins` kısıtlaması (örn. reverse proxy header’a göre) önerilir.
- Rate Limit & Kota: `ai.security.max_credit_per_request`, `rate_limit_per_minute` var; Request pipeline’da enforcement middleware’i görünmüyor.

## Performans İyileştirme Olanakları
- Kuyruk Varsayılanı: Uzun istekler için kuyruk kullanımını aç → UI tarafında job status polling hazır.
- Cache: Aynı input için deterministik sonuç gereken senaryolarda SHA-256 hash ile response cache (isteğe bağlı TTL), PII hariç tut.
- N+1: Livewire listeleri ve içerik ilişkilerinde eager load + paginate.
- Asset’ler: Prod build’te `console.log` temizliği, `.backup` dosyalarının paket dışına itilmesi.

## Yol Haritası (Kısa)
- 0–7 gün: Duplike/backup temizliği, throttle + ai.tokens standardizasyonu, `use_queue_for_long_requests = true`.
- 8–30 gün: AIService parçalama, ortak provider arayüzleri, tekil çeviri pipeline’ı, monitoring metrikleri.
- 31–60 gün: Seçici response cache, maliyet/credit görünürlüğü, fallback optimizasyonu ve alarm setleri.

