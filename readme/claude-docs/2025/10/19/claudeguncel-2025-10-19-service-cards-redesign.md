# Servis Kartları Tasarım Yenileme

**Tarih**: 2025-10-19
**ID**: service-cards-redesign

## 🎯 AMAÇ
5 adet servis kartını (Satın Alma, Kiralama, İkinci El, Yedek Parça, Teknik Servis) sıfırdan yeniden tasarlamak.

## 📋 TASARIM GEREKSİNİMLERİ

### Yapı Düzeni:
```
margin → kutu (div) → padding → içerik
margin → gradient çizgi (separator)
margin → kutu (div) → padding → içerik
margin → gradient çizgi (separator)
margin → kutu (div) → padding → içerik
margin → gradient çizgi (separator)
margin → kutu (div) → padding → içerik
margin → gradient çizgi (separator)
margin → kutu (div) → padding → içerik
```

### Teknik Standartlar:
- ✅ **Framework**: Tailwind CSS
- ✅ **Renk Paleti**: Tailwind framework renkleri (custom yok)
- ✅ **Responsive**: Mobile-first yaklaşım
- ✅ **Icons**: FontAwesome (mevcut)
- ✅ **Hover Effects**: Smooth transitions

## 📐 TASARIM DETAYLARı

### Kartlar:
1. **Satın Alma** - Blue/Cyan gradient - fa-shopping-cart
2. **Kiralama** - Yellow/Orange gradient - fa-calendar-days
3. **İkinci El** - Green/Emerald gradient - fa-recycle
4. **Yedek Parça** - Orange/Red gradient - fa-gears
5. **Teknik Servis** - Purple/Pink gradient - fa-wrench

### Gradient Çizgiler:
- Dikey ayraç çizgileri
- Renk: Her kartın gradient rengine uyumlu
- Efekt: from-transparent via-[color] to-transparent

## ✅ YAPILACAKLAR

- [x] Tasarım analizi ve planlama
- [ ] HTML/Tailwind yapısını kodla
- [ ] Responsive optimizasyon
- [ ] Test ve gözden geçirme

## 🎨 YENİ KOD YAPISI

### Container Layout:
- Grid sistem (2 columns mobile, 5 columns desktop)
- Gap değerleri optimize edilecek
- Margin ve padding dengesi sağlanacak

### Card Komponenti:
- Link wrapper
- Hover efektleri
- Icon container (gradient background)
- Title

### Separator Çizgi:
- Absolute position
- Gradient background
- Responsive görünürlük

---

## 📝 NOTLAR
- Gradient çizgiler sadece kartlar arasında olacak (son kartta yok)
- Hover efektleri smooth ve profesyonel
- Mobile'da tek sütun, tablet'te 2, desktop'ta 5 sütun
