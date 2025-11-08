# Database Migration Değişiklikleri Raporu

## 🗑️ Silinen/Birleştirilen Migration'lar

### AI Sistemi (Tenant → Central Taşıma)
**Commit:** f40cfdc5f - AI Mimari Düzeltme

**Tenant'tan Silinen (Central'a taşındı):**
- `tenant/2024_11_04_120001_create_ai_tenant_directives_table.php` 
  → Artık: `ai_directives` (Central DB)
- `tenant/2024_11_04_120002_create_ai_conversations_table.php`
  → Artık: `ai_conversations` (Central DB - zaten vardı)

### SEO Settings Temizliği
**Commit:** bf0c8f87d - Migration temizliği

**Silinen Duplicate/Redundant Migration'lar:**
- `2025_09_26_131240_remove_redundant_ai_columns_from_seo_settings_table.php`
- `2025_10_06_214500_add_missing_columns_to_seo_settings_central.php`
- `tenant/2025_10_06_214500_add_missing_columns_to_seo_settings.php`

### Click Tracking Sistemi Kaldırma
**Commit:** 0284d25d4 - Click tracking sistemi tamamen kaldırıldı

**Silinen:**
- `2025_10_18_210510_create_search_clicks_table.php` (Central)
- `tenant/2025_10_18_210510_create_search_clicks_table.php` (Tenant)
  → Tablo: `search_clicks` artık kullanılmıyor

---

## 📋 Central Database'deki BİRLEŞTİRİLEN Tablolar

### 1. **ai_directives** (YENİ - Tenant'tan Central'a taşındı)
- **Önceki Yer:** Her tenant'ta `ai_tenant_directives`
- **Yeni Yer:** Central DB'de `ai_directives`
- **Sebep:** Tüm tenant'lar için ortak direktifler
- **Tenant Filter:** `tenant_id` kolonu ile

### 2. **ai_conversations** (Zaten Central'daydı)
- **Durum:** Değişiklik yok
- **Tenant Filter:** `tenant_id` kolonu ile

### 3. **ai_messages** (Zaten Central'daydı)
- **Durum:** Değişiklik yok
- **Conversation ilişkisi:** `conversation_id` üzerinden

---

## 📊 Export Edilmesi Gereken Tablolar

**Central DB'deki bu tablolar export edilecek:**

1. ✅ `ai_directives` - Tüm tenant direktifleri
2. ✅ `ai_conversations` - Tüm konuşmalar
3. ✅ `ai_messages` - Tüm mesajlar
4. ✅ `ai_providers` - AI sağlayıcıları
5. ✅ `ai_features` - AI özellikleri

