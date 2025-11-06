# 🎯 PROMPT DÜZELTMELERİ - KRİTİK İŞ ODAĞI DEĞİŞİKLİĞİ

**Tarih:** 2025-11-06
**Flow ID:** 6 (İxtif AI Assistant)
**Değişiklik Türü:** 🚨 KRİTİK - Ana iş tanımı düzeltmesi

---

## ❌ SORUN: YANLIŞ İŞ ODAĞI

### Test Sonuçları (Hatalı Davranış):

**Senaryo 1: Samimi konuşma**
```
Kullanıcı: nasılsın
AI: Bu konuda yardımcı olamam. ❌
```

**Senaryo 2: Transpalet talebi**
```
Kullanıcı: transpalet istiyorum
AI: Tam transpalet satmıyoruz. Hangi yedek parçaya ihtiyacınız var? ❌
```

**Senaryo 3: Karşılama**
```
Kullanıcı: merhaba
AI: Merhaba! Size nasıl yardımcı olabilirim? Forklift veya transpaletiniz için hangi yedek parçaya ihtiyacınız var? ❌
```

### Kullanıcı Geri Bildirimi (KRİTİK):

> "bu nasıl yanıtlar?
>
> şunu net anlaşalım artık:
>
> yedek parça bizim en kücük işimiz. bunu neden öne cıkarıyorsun? yedek parça hairç ve alt kategoriler hariç diğer ana kategorilerimiz hakkında bilgi verecek yapay zeka. yedek parça en sonuncusu yani önemsiz. amacımız tam endüstriyel ürünlerin satısını ve tanıtımını yapıoyruz. forklift transpalet vs vs vs .
>
> ayrıca nasılsın diyıruz yanıt versin yaw"

---

## 🔍 KÖK NEDEN ANALİZİ

### Eski Prompt (YANLIŞ):

```
Sen İxtif.com satış danışmanısın. Forklift, transpalet ve istif makineleri için YEDEK PARÇA satıyorsun.

🎯 ANA KONULAR (Bunlarda konuş):
✅ Forklift, transpalet, istif makinesi
✅ Tekerlek, zincir, pompa, plaka, fren, direksiyon
✅ Marka, model, kapasite
✅ Yedek parça, aksesuar
```

**Sorunlar:**
1. ❌ "YEDEK PARÇA satıyorsun" → Yanlış ana iş tanımı
2. ❌ Yedek parça parçaları listelenmiş → Yanlış önceliklendirme
3. ❌ TAM ÜRÜN vurgusu YOK
4. ❌ Samimi konuşma kuralları YOK

---

## ✅ ÇÖZÜM: YENİ PROMPT YAPISI

### 1. ANA İŞ TANIMI (YENİ EKLENEN BÖLÜM)

```
🎯 ANA İŞİMİZ (EN ÖNEMLİ!):
✅ TAM ÜRÜN SATIŞI (Forklift, Transpalet, İstif Makinesi)
✅ Endüstriyel ekipman tanıtımı ve satışı
✅ YEDEK PARÇA: En düşük öncelik (sadece müşteri isterse)
```

**Değişiklik:**
- İlk cümle: "YEDEK PARÇA satıyorsun" → Kaldırıldı
- Yeni ilk cümle: "Forklift, transpalet ve istif makineleri satıyorsun."
- Yeni bölüm: "ANA İŞİMİZ" eklendi
- Yedek parça: "En düşük öncelik" olarak tanımlandı

### 2. SAMİMİ KONUŞMA KURALLARI (YENİ EKLENEN BÖLÜM)

```
🗣️ SAMİMİ KONUŞMA:
- "Nasılsın?" → "İyiyim teşekkürler! 😊 Size nasıl yardımcı olabilirim?"
- "Merhaba" → "Merhaba! 🎉 Size yardımcı olmaktan mutluluk duyarım!"
- "Nasıl" → Bağlama göre yanıt ver (ürün mü soru mu?)
- ROBOT GİBİ KONUŞMA! Samimi ve arkadaşça ol!
```

**Değişiklik:**
- Yeni bölüm: "SAMİMİ KONUŞMA" eklendi
- "Nasılsın?" için direkt cevap şablonu
- "ROBOT GİBİ KONUŞMA" → Çıkarıldı, "Samimi ve arkadaşça ol!" eklendi
- Konu dışı reddetme → Kaldırıldı

### 3. KATEGORİ ÖNCELİKLERİ (DÜZENLEME)

**Eski:**
```
KATEGORLER:
1. Forklift
2. Transpalet
3. İstif Makinesi
4. Yedek Parça ✅ (Eşit öncelik)
```

**Yeni:**
```
KATEGORLER:
1. TRANSPALET ✅
2. FORKLIFT ✅
3. İSTİF MAKİNESİ ✅
4. REACH TRUCK ✅
5. PLATFORM ✅
6. TOW TRACTOR ✅
7. YEDEK PARÇA (EN DÜŞÜK ÖNCELİK!) ⚠️
```

**Değişiklik:**
- Yedek parça: 4. sıradan → 7. sıraya
- Uyarı: "(EN DÜŞÜK ÖNCELİK!)" eklendi
- Tam ürün kategorileri: 3'ten 6'ya çıktı

### 4. ÜRÜN ÖNCELİKLENDİRME (YENİ EKLENEN BÖLÜM)

```
🎯 ÜRÜN ÖNCELİKLENDİRME:
1. ✅ TAM ÜRÜN kategorilerini ÖNE! (Transpalet, Forklift, İstif)
2. ❌ YEDEK PARÇA kategorisini EN SONA!
3. ✅ Ana kategorilere odaklan (Endüstriyel ekipman)
```

**Değişiklik:**
- Yeni bölüm: "ÜRÜN ÖNCELİKLENDİRME" eklendi
- Açık talimat: TAM ÜRÜN öne, yedek parça sona
- Ana kategorilere odaklanma vurgusu

---

## 🧪 BEKLENEN SONUÇLAR

### Senaryo 1: "nasılsın?"

**Önceki Yanıt:**
```
Bu konuda yardımcı olamam. ❌
```

**Yeni Beklenti:**
```
İyiyim teşekkürler! 😊 Size nasıl yardımcı olabilirim? ✅
```

### Senaryo 2: "transpalet istiyorum"

**Önceki Yanıt:**
```
Tam transpalet satmıyoruz. Hangi yedek parçaya ihtiyacınız var? ❌
```

**Yeni Beklenti:**
```
Harika! 🎉 Size en popüler transpalet modellerimizi göstereyim:

⭐ **BT LWE 160** [LINK:shop:bt-lwe-160]
- 1.6 ton taşıma kapasitesi
- Elektrikli sistem
- Ergonomik tasarım
Fiyat: 45.000 ₺

⭐ **LINDE T20** [LINK:shop:linde-t20]
- 2 ton kapasite
- Manuel sistem
- Dayanıklı yapı
Fiyat: 32.000 ₺

Hangi kapasite arıyorsunuz? 😊 ✅
```

### Senaryo 3: "merhaba"

**Önceki Yanıt:**
```
Merhaba! Size nasıl yardımcı olabilirim? Forklift veya transpaletiniz için hangi yedek parçaya ihtiyacınız var? ❌
```

**Yeni Beklenti:**
```
Merhaba! 🎉 Size yardımcı olmaktan mutluluk duyarım! Forklift, transpalet veya istif makinesi mi arıyorsunuz? ✅
```

---

## 📊 PROMPT KARŞILAŞTIRMA

### Giriş Cümlesi

| **Eski** | **Yeni** |
|----------|----------|
| Sen İxtif.com satış danışmanısın. Forklift, transpalet ve istif makineleri için **YEDEK PARÇA** satıyorsun. | Sen İxtif.com satış danışmanısın. Forklift, transpalet ve istif makineleri **satıyorsun**. |

### Ana İş Tanımı

| **Eski** | **Yeni** |
|----------|----------|
| ❌ YOK | 🎯 **ANA İŞİMİZ (EN ÖNEMLİ!):**<br>✅ TAM ÜRÜN SATIŞI (Forklift, Transpalet, İstif Makinesi)<br>✅ Endüstriyel ekipman tanıtımı ve satışı<br>✅ YEDEK PARÇA: En düşük öncelik (sadece müşteri isterse) |

### Samimi Konuşma

| **Eski** | **Yeni** |
|----------|----------|
| ❌ YOK<br>Konu dışı sorulara:<br>"Bu konuda yardımcı olamam" | 🗣️ **SAMİMİ KONUŞMA:**<br>- "Nasılsın?" → "İyiyim teşekkürler! 😊"<br>- "Merhaba" → "Merhaba! 🎉"<br>- ROBOT GİBİ KONUŞMA! Samimi ve arkadaşça ol! |

### Kategori Öncelikleri

| **Eski** | **Yeni** |
|----------|----------|
| 1-4 arası, yedek parça eşit öncelik | 1-6 TAM ÜRÜN<br>7. YEDEK PARÇA (EN DÜŞÜK ÖNCELİK!) |

---

## 🛠️ UYGULAMA DETAYLARI

### Database Güncelleme:

**Tablo:** `tenant_ixtif.tenant_conversation_flows`
**Kayıt ID:** 6
**Güncellenen Alan:** `flow_data->nodes[9]->config->system_prompt`

**SQL Komutu:**
```sql
UPDATE tenant_conversation_flows
SET flow_data = JSON_SET(
    flow_data,
    '$.nodes[9].config.system_prompt',
    '[4176 karakter yeni prompt]'
)
WHERE id = 6;
```

**Prompt Boyutu:**
- Eski: ~2800 karakter
- Yeni: 4176 karakter
- Fark: +1376 karakter (Yeni bölümler eklendi)

### Cache Temizleme:

```bash
# OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php

# Doğrulama
mysql -e "SELECT JSON_EXTRACT(flow_data, '$.nodes[9].config.system_prompt') FROM tenant_ixtif.tenant_conversation_flows WHERE id = 6" | grep "TAM ÜRÜN"
```

---

## 📋 DEĞİŞİKLİK ÖZETİ

### Eklemeler (+):
- ✅ "ANA İŞİMİZ" bölümü
- ✅ "SAMİMİ KONUŞMA" bölümü
- ✅ "ÜRÜN ÖNCELİKLENDİRME" bölümü
- ✅ Reach Truck, Platform, Tow Tractor kategorileri

### Çıkarmalar (-):
- ❌ "YEDEK PARÇA satıyorsun" cümlesi
- ❌ "Bu konuda yardımcı olamam" red cümlesi
- ❌ Yedek parça parçaları listesi (tekerlek, zincir, pompa...)

### Değişiklikler (~):
- 🔄 Yedek parça: Eşit öncelik → En düşük öncelik (7. sıra)
- 🔄 Giriş cümlesi: "YEDEK PARÇA satıyorsun" → "satıyorsun" (genel)
- 🔄 Karşılama tonu: Soğuk → Samimi ve arkadaşça

---

## ✅ DOĞRULAMA

### Test Öncesi Kontrol:
```bash
# 1. Prompt'un güncel olduğunu doğrula
mysql -e "SELECT JSON_EXTRACT(flow_data, '$.nodes[9].config.system_prompt') FROM tenant_ixtif.tenant_conversation_flows WHERE id = 6" | head -20

# 2. "TAM ÜRÜN" kelimesinin var olduğunu kontrol et
mysql -e "SELECT JSON_EXTRACT(flow_data, '$.nodes[9].config.system_prompt') FROM tenant_ixtif.tenant_conversation_flows WHERE id = 6" | grep -i "TAM ÜRÜN"

# 3. OPcache reset yapıldığını doğrula
curl -s -k https://ixtif.com/opcache-reset.php
```

### Frontend Test Senaryoları:
1. ✅ "nasılsın?" → Samimi yanıt
2. ✅ "merhaba" → TAM ÜRÜN odaklı karşılama
3. ✅ "transpalet istiyorum" → Direkt ürün linkleri
4. ✅ "forklift" → Forklift modelleri (yedek parça DEĞİL)
5. ✅ "yedek parça" → Sadece açıkça istenirse

---

## 🎯 BAŞARI KRİTERLERİ

### ✅ Başarı (Beklenen):
- "Nasılsın?" → Samimi cevap (robot cevabı değil)
- "Transpalet" → TAM ÜRÜN linkleri (yedek parça değil)
- "Merhaba" → TAM ÜRÜN odaklı karşılama
- Samimi ve arkadaşça ton
- Yedek parça: Sadece açıkça istenirse

### ❌ Hata (Kabul Edilemez):
- "Nasılsın?" → "Bu konuda yardımcı olamam"
- "Transpalet" → "Tam transpalet satmıyoruz"
- "Merhaba" → Yedek parça vurgusu
- Robot gibi soğuk ton
- Yedek parça öncelikli davranış

---

## 📌 NOTLAR

1. **Kritik İş Değişikliği:** Bu sadece prompt güncellemesi değil, şirketin ANA İŞ TANIMININ düzeltilmesidir.
2. **Kullanıcı Geri Bildirimi:** Kullanıcının açık ifadesi: "yedek parça bizim en kücük işimiz"
3. **Prompt Boyutu:** 4176 karakter (uzun ama gerekli - tüm senaryoları kapsar)
4. **Backward Compatibility:** Yedek parça özelliği kaldırılmadı, sadece öncelik düşürüldü
5. **Frontend Test Gerekli:** Değişiklikler database'de yapıldı, frontend'den test edilmeli

---

## 🔗 İLGİLİ DOSYALAR

- **Flow JSON:** `tenant_ixtif.tenant_conversation_flows` (ID: 6)
- **Prompt Kaynak:** `/tmp/correct_ixtif_prompt.txt`
- **SQL Script:** `/tmp/update_prompt.sql`
- **Test Sonuçları:** Bu dosyanın "BEKLENEN SONUÇLAR" bölümü
- **Audit Raporu:** `/tmp/documentation_audit.md`

---

**Güncelleme:** 2025-11-06 03:30
**Durum:** ✅ Database'de uygulandı, OPcache reset yapıldı
**Sonraki Adım:** Frontend'den test et, sonuçları doğrula
