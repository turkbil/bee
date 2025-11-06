# AI Shop Assistant - Rules Refactor
**Tarih:** 2025-11-05
**Durum:** In Progress
**Amaç:** V1 kurallarını analiz edip Global + İxtif özel olarak ayır, V2 flow sistemine entegre et

## 🎯 Hedef
V1 sistemindeki OptimizedPromptService + IxtifPromptService kurallarını:
1. Global Rules (tüm tenant'lar)
2. İxtif Özel Rules (tenant 2,3)

olarak ayırıp, her tenant için farklı flow seçimi yapılabilsin.

## 📁 Dosya Yapısı
```
2025-11-05-rules-refactor/
├── README.md (bu dosya)
├── 01-ai-rules-complete.md (Komple kural seti - 556 satır)
├── 02-v1-full-plan.md (V1 global kurallar özet)
├── 03-ixtif-rules-summary.md (İxtif özel kurallar özet)
├── 04-v1-critical-rules.md (V1'den kritik konuşma kuralları)
└── next-steps.md (Sonraki adımlar)
```

## ✅ Tamamlanan
- [x] V1 OptimizedPromptService analizi
- [x] V1 IxtifPromptService analizi
- [x] Global + İxtif kurallarını ayırma
- [x] 556 satırlık komple dokümantasyon

## ⏳ Devam Eden
- [ ] Global flow oluşturma (database)
- [ ] İxtif özel flow oluşturma (database)
- [ ] Admin panel - Flow seçici
- [ ] Test

## 📊 İstatistikler
- **Global Rules:** 12 ana bölüm
- **İxtif Özel:** 14 ana bölüm
- **Toplam:** 26 kritik kural kategorisi
- **Kaynak Kod:** OptimizedPromptService (1382 satır) + IxtifPromptService (515 satır)
