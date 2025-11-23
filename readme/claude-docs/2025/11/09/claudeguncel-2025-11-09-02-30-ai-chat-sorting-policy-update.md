# AI Chat Sıralama ve Fiyat/Stok Politikası Güncellemesi

**Tarih:** 2025-11-09 02:30
**Konu:** AI Chat ürün sıralaması + Fiyatsız/stoksuz ürün gösterme politikası

## 📋 Yapılan Değişiklikler

### 1️⃣ Sıralama Önceliği Güncellendi

**Yeni Sıralama (Kullanıcı Talebi):**
1. 🥇 **Homepage Products** (show_on_homepage = 1)
2. 🥈 **Stok Durumu** (current_stock > 0)
3. 🥉 **Category Sort Order** (sort_order ASC)
4. 💰 **Fiyat** (base_price ASC - en ucuz önce)

**Düzenlenen Dosya:**
- `Modules/AI/app/Services/Workflow/Nodes/StockSorterNode.php` (lines 42-85)

### 2️⃣ Fiyat/Stok Politikası Değişikliği

**ESKİ POLİTİKA:**
- Fiyatsız ürünler (base_price = 0) → FİLTRELENİYORDU
- Stoksuz ürünler (current_stock = 0) → FİLTRELENİYORDU

**YENİ POLİTİKA:**
- ✅ **Tüm ürünler gösterilir** (fiyatsız/stoksuz dahil)
- ❌ "Stokta yok" asla denmez
- ✅ Fiyatsız ürünler için: "Fiyat bilgisi için müşteri temsilcilerimizle iletişime geçin"
- ✅ Stoksuz ürünler için: "Tedarik süresi için numaranızı bırakın"

**Düzenlenen Dosyalar:**
1. `app/Services/AI/HybridSearchService.php` - Meilisearch filtresi kaldırıldı
2. `Modules/AI/app/Services/Tenant/IxtifPromptService.php` - Fiyat/stok politikası prompt eklendi
3. `Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php` - base_price filtresi kaldırıldı

### 3️⃣ Raw Meilisearch Client Kullanımı

**Sorun:** Laravel Scout `->where('base_price', '>', 0)` gibi comparison operatörlerini desteklemiyor.

**Çözüm:** HybridSearchService'de raw Meilisearch client kullanıldı.

**Kod:**
```php
$client = new MeiliClient(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
$indexName = tenancy()->initialized
    ? 'shop_products_tenant_' . tenant('id')
    : 'shop_products';

$filterParts = ['is_active = true'];  // Sadece aktif ürünler

if ($categoryId) {
    $filterParts[] = "category_id = {$categoryId}";
}

$filterString = implode(' AND ', $filterParts);

$searchResults = $client->index($indexName)->search($query, [
    'filter' => $filterString,
    'limit' => 50
]);
```

### 4️⃣ Hard-coded Ürün Örnekleri Temizlendi

**Değişiklik:** `IxtifPromptService.php` içindeki hard-coded ürün adları (İXTİF F4, EPL185, EPT20) kaldırıldı.

**Yeni Yaklaşım:** "BAĞLAM BİLGİLERİ'ndeki GERÇEK model adlarını kullan"

## 🔧 Teknik Detaylar

### Sıralama Algoritması:

```php
$products = $products->sort(function($a, $b) {
    // 1. Homepage products önce
    $aHomepage = $a->homepage ?? 0;
    $bHomepage = $b->homepage ?? 0;
    if ($aHomepage !== $bHomepage) {
        return $bHomepage <=> $aHomepage;
    }

    // 2. Stokta olanlar önce
    $aInStock = ($a->current_stock ?? 0) > 0;
    $bInStock = ($b->current_stock ?? 0) > 0;
    if ($aInStock !== $bInStock) {
        return $bInStock <=> $aInStock;
    }

    // 3. Category sort order
    $aSortOrder = $a->sort_order ?? 9999;
    $bSortOrder = $b->sort_order ?? 9999;
    if ($aSortOrder !== $bSortOrder) {
        return $aSortOrder <=> $bSortOrder;
    }

    // 4. Fiyat (en ucuz önce)
    $aPrice = $a->base_price ?? 0;
    $bPrice = $b->base_price ?? 0;

    if ($aPrice == 0 && $bPrice > 0) return 1;  // Fiyatsız en sona
    if ($aPrice > 0 && $bPrice == 0) return -1;
    if ($aPrice == 0 && $bPrice == 0) return 0;

    return $aPrice <=> $bPrice;
});
```

### AI Prompt Kuralları:

```markdown
**💰 FİYAT VE STOK DURUMU POLİTİKASI:**

**1️⃣ FİYATSIZ ÜRÜNLER:**
- ✅ Ürünü MUTLAKA göster!
- ❌ ASLA 'Bu ürünün fiyatı yok', '0 TL' YAZMA!
- ✅ Fiyat yerine: "Müşteri temsilcilerimizle iletişime geçerek detaylı fiyat teklifi alabilirsiniz."

**2️⃣ STOKTA OLMAYAN ÜRÜNLER:**
- ✅ Ürünü MUTLAKA göster!
- ❌ ASLA 'Stokta yok', 'Tükendi' YAZMA!
- ✅ Mesaj: "Sipariş ve teslimat süresi için numaranızı bırakabilirsiniz."

**3️⃣ HER İKİSİ DE YOKSA:**
- ✅ "Fiyat ve tedarik bilgisi için müşteri temsilcilerimizle iletişime geçebilirsiniz."
```

## 📊 Test Sonuçları

### Meilisearch Test:
```bash
curl -X POST 'http://127.0.0.1:7700/indexes/shop_products_tenant_2/search' \
  -H 'Authorization: Bearer vu1zM39HMijhnBm6XwaJapovdd6L2dEA' \
  -d '{"q": "transpalet", "filter": "is_active = true AND category_id = 2", "sort": ["base_price:asc"]}'

# Sonuç: 69 transpalet bulundu
# Sıralama: Fiyata göre artan
```

## ⚠️ Önemli Notlar

1. **OPcache Reset Gerekli:** Değişikliklerden sonra mutlaka OPcache reset yapılmalı
2. **Permission:** Dosyalar `tuufi.com_:psaserv` owner olmalı
3. **Tenant Context:** HybridSearchService tenant-aware çalışır
4. **Category Boundary:** Kategori tespit edilirse sadece o kategori ürünleri gösterilir

## 🔄 Deployment Checklist

- [x] StockSorterNode.php güncellendi
- [x] HybridSearchService.php güncellendi
- [x] IxtifPromptService.php fiyat/stok politikası eklendi
- [x] MeilisearchSettingsNode.php base_price filtresi kaldırıldı
- [x] Hard-coded ürün örnekleri temizlendi
- [x] File permissions düzeltildi (644)
- [x] OPcache reset yapıldı

## 📝 Kullanıcı Talepleri

1. ✅ "Önce show on homepage, sonra stok, sonra kategori sorting, en son fiyat"
2. ✅ "Fiyatsız ve stoksuz ürünleri müşteri isterse göster, temsilci yönlendir"
3. ✅ "Hiç bir ürüne 'stokta yok' deme, numarasını bırakmasını iste"
4. ✅ "Hard-code ürün örnekleri verme"

