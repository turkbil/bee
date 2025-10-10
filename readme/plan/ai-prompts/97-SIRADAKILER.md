# 📋 SIRADAKI GÖREVLER - ACİL YAPILACAKLAR LİSTESİ

## 🎯 GENEL DURUM
- **Core AI Sistem**: ✅ Tamamlanmış (detectLength, enforceStructure, buildPrompt)
- **Feature/Prompt Seeding**: 🚫 İptal edildi (Nurullah kararı)
- **Odak**: Core sistem optimizasyonu ve test

---

## 🔥 ACİL GÖREVLER (ŞU AN)

### **1. End-to-End AI Pipeline Testi** 🚨 ÖNCELİKLİ
📅 **Süre**: 1-2 saat  
🎯 **Hedef**: "uzun yazı yaz" → gerçekten 1000+ kelime gelsin

**YAPILACAKLAR:**
- [ ] Prowess sayfasında manual test yap
- [ ] "bilişim hakkında uzun yazı yaz" komutu ver
- [ ] Response 1000+ kelime mi kontrol et
- [ ] Paragraf sayısı 4+ mi kontrol et
- [ ] HTML yok mu kontrol et

**BAŞARI KRİTERİ:**
```
Input: "bilişim hakkında uzun yazı yaz"
Expected Output: 
- Min 1000 kelime ✅
- Min 4 paragraf ✅  
- HTML yok ✅
- Düz metin format ✅
```

---

### **2. Chat vs Feature Mode Test** 🚨 ÖNCELİKLİ  
📅 **Süre**: 30 dakika
🎯 **Hedef**: Mode ayrımının çalışması

**TEST SENARYOLARI:**
- [ ] **Chat Test**: "Merhaba" → kısa samimi yanıt
- [ ] **Feature Test**: "uzun blog yazısı yaz" → profesyonel detaylı content
- [ ] **Context Test**: TechCorp şirket bilgisi tanıma

**BAŞARI KRİTERİ:**
```
Chat: Samimi, kısa (100-300 kelime)
Feature: Profesyonel, uzun (1000+ kelime)  
Context: Şirket bilgisi dahil
```

---

### **3. Response Quality Kontrol** 🚨 ÖNCELİKLİ
📅 **Süre**: 1 saat
🎯 **Hedef**: AI provider response kalitesi artırma

**KONTROL LİSTESİ:**
- [ ] HTML tag'leri çıkıyor mu?
- [ ] Tek paragraf mı geliyor?
- [ ] "Yardımcı olamam" diyor mu?
- [ ] Kelime sayısı yetersiz mi?

**DÜZELTME STRATEJİSİ:**
```php
// Prompt güçlendirme gerekiyorsa
1. buildFullSystemPrompt() daha sert kurallar
2. enforceStructure() post-processing güçlendirme  
3. Provider-specific prompt optimization
```

---

## ⚡ ORTA VADELİ GÖREVLER (1-3 GÜN)

### **4. Context Engine Test ve Geliştirme**
📅 **Süre**: 2-3 saat
🎯 **Hedef**: User/Tenant/Page context sistemi test

**YAPILACAKLAR:**
- [ ] ContextEngine servisi test et
- [ ] User context (Merhaba Nurullah!) test
- [ ] Tenant context (TechCorp için...) test  
- [ ] Page context (mevcut sayfa bilgisi) test

### **5. Error Handling Güçlendirme**
📅 **Süre**: 2 saat  
🎯 **Hedef**: AI hatalarını daha iyi yönetme

**YAPILACAKLAR:**
- [ ] API timeout handling
- [ ] Credit yetersizliği handling
- [ ] Response format validation
- [ ] Fallback response sistemi

### **6. Performance Optimization**
📅 **Süre**: 2-3 saat
🎯 **Hedef**: Response süreleri optimize etme

**YAPILACAKLAR:**
- [ ] Cache stratejileri test
- [ ] Provider response time ölçümü  
- [ ] Memory usage optimization
- [ ] Concurrent request handling

---

## 🔄 UZUN VADELİ GÖREVLER (1 HAFTA+)

### **7. Advanced Context Intelligence**
- [ ] Conversation memory sistemi
- [ ] User preference learning
- [ ] Content quality scoring
- [ ] Adaptive response generation

### **8. Multi-Language Support**  
- [ ] Language detection
- [ ] Localized responses  
- [ ] Cultural context adaptation
- [ ] Multi-language prompt optimization

### **9. Learning & Analytics**
- [ ] Usage pattern analysis
- [ ] Response quality metrics
- [ ] User satisfaction tracking  
- [ ] Auto-optimization engine

---

## 🎯 BU HAFTAKI HEDEFLER

### **PAZARTESI-SALI (ŞU AN):**
- [x] Core sistem kontrolü ✅ BİTTİ
- [ ] End-to-end test 🔥 ACİL
- [ ] Mode ayrımı test 🔥 ACİL  
- [ ] Response quality kontrol 🔥 ACİL

### **ÇARŞAMBA-PERŞEMBE:**
- [ ] Context Engine test
- [ ] Error handling güçlendirme
- [ ] Performance baseline ölçümü

### **CUMA:**
- [ ] Haftalık test ve değerlendirme
- [ ] Bug fixing  
- [ ] Gelecek hafta planning

---

## 🚨 KRİTİK HATIRLATMALAR

### **ASLA UNUTMA:**
- ⚠️ "uzun yaz" = MİNİMUM 1000 kelime
- ⚠️ Minimum 4 paragraf zorunlu
- ⚠️ HTML yok, sadece düz metin
- ⚠️ "Yardımcı olamam" yasak

### **TEST KOMUTLARI:**
```
1. "bilişim hakkında uzun yazı yaz"
2. "Merhaba" (chat test)  
3. "blog yazısı oluştur" (feature test)
4. "kısa özet yaz" (uzunluk test)
```

### **BAŞARI KRİTERLERİ:**
- ✅ Her "uzun" talebi 1000+ kelime
- ✅ Chat mode ≠ Feature mode
- ✅ Context tanıma çalışıyor  
- ✅ HTML yok, düz metin var
- ✅ Paragraf yapısı zorunlu

---

## 📊 PROGRESS TRACKING

```
🔥 ACİL GÖREVLER:     ████░░░░░░ 40% (2/5)
⚡ ORTA VADELİ:       ░░░░░░░░░░  0% (0/3)  
🔄 UZUN VADELİ:       ░░░░░░░░░░  0% (0/3)

GENEL İLERLEME:      ████░░░░░░ 40%
```

**GÜNCEL DURUM**: End-to-end test bekleniyor  
**SONRAKI MILESTONE**: Response quality kontrol  
**HAFTALIK HEDEF**: Core sistem %100 çalışır durumda

---

**SON GÜNCELLEME**: 08.08.2025 04:30  
**GÜNCELLEYEN**: AI Assistant  
**SONRAKI KONTROL**: Her test tamamlandıktan sonra