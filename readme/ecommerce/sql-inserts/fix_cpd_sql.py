#!/usr/bin/env python3
"""
CPD15-18-20TVL SQL dosyasını gerçek tablo yapısına uyarla
TÜM coşkulu içeriği koru, sadece kolon isimlerini düzelt
"""

import re

# SQL dosyasını oku
with open('/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/sql-inserts/CPD15-18-20TVL-insert-BACKUP.sql', 'r', encoding='utf-8') as f:
    sql = f.read()

print("🔧 SQL düzeltiliyor...")

# ======================
# BRANDS TABLOSU DÜZELTMELERİ
# ======================
# Olmayan kolonları kaldır: parent_brand_id, short_description (VALUES kısmında), metadata (VALUES kısmında)
# Var olan kolonları ekle: headquarters, sort_order

# 1. parent_brand_id kolonunu kaldır
sql = re.sub(
    r'INSERT INTO shop_brands \(\s*brand_id,\s*parent_brand_id,\s*title',
    'INSERT INTO shop_brands (\n    brand_id,\n    title',
    sql
)
sql = re.sub(r'\s*1, -- brand_id\s*NULL, -- parent_brand_id', '    1, -- brand_id', sql)

# 2. short_description (brands için) kaldır - kolondan
sql = re.sub(
    r'description,\s*short_description,\s*logo_url',
    'description,\n    logo_url',
    sql
)

# 3. short_description VALUES kısmından kaldır
sql = re.sub(
    r"JSON_OBJECT\(\s*'tr',[^)]+İstif Pazarı[^)]+\), -- description\s*JSON_OBJECT\(\s*'tr',[^)]+forklift çözümleri[^)]+\), -- short_description",
    lambda m: m.group(0).split('-- short_description')[0].rstrip().rstrip(',') + ', -- description',
    sql,
    flags=re.DOTALL
)

# 4. metadata (brands için) kaldır - kolondan
sql = re.sub(
    r'certifications,\s*metadata,\s*created_at',
    'certifications,\n    created_at',
    sql
)

# 5. metadata VALUES kısmından kaldır
sql = re.sub(
    r"JSON_ARRAY\([^)]+\), -- certifications\s*JSON_OBJECT\([^)]+services[^)]+\), -- metadata",
    lambda m: m.group(0).split('-- metadata')[0].rstrip().rstrip(',') + ', -- certifications',
    sql,
    flags=re.DOTALL
)

# 6. headquarters ekle (kolonlara)
sql = re.sub(
    r'(country_code,)\s*(founded_year,)',
    r'\1\n    \2\n    headquarters,',
    sql
)

# 7. headquarters ekle (VALUES'a)
sql = re.sub(
    r"('TR', -- country_code)\s*(1995, -- founded_year)",
    r"\1\n    \2\n    'İstanbul, Türkiye', -- headquarters",
    sql
)

# 8. sort_order ekle (kolonlara)
sql = re.sub(
    r'(is_active,)\s*(is_featured,)',
    r'\1\n    \2\n    sort_order,',
    sql
)

# 9. sort_order ekle (VALUES'a)
sql = re.sub(
    r'(1, -- is_active)\s*(1, -- is_featured)',
    r'\1\n    \2\n    1, -- sort_order',
    sql
)

# ======================
# PRODUCTS TABLOSU DÜZELTMELERİ
# ======================
# Olmayan kolonlar: parent_product_id, series_name, compare_price, availability,
#                   warranty_months, sort_order, rating_avg, rating_count, is_new_arrival
#                   use_cases, competitive_advantages, target_industries, faq_data,
#                   related_products, cross_sell_products, up_sell_products, metadata
#
# Var olan kolonlar: barcode, max_installments, low_stock_threshold, allow_backorder,
#                    video_url, manual_pdf_url, sales_count, shipping_info
#
# compare_price -> compare_at_price
# stock_quantity -> current_stock
# warranty_months -> warranty_info (JSON)

# 1. parent_product_id kaldır
sql = re.sub(
    r'product_id,\s*category_id,\s*brand_id,\s*parent_product_id,\s*sku',
    'product_id,\n    category_id,\n    brand_id,\n    sku',
    sql
)
sql = re.sub(
    r'1001, -- product_id\s*163, -- category_id[^)]*\s*1, -- brand_id[^)]*\s*NULL, -- parent_product_id',
    '    1001, -- product_id\n    163, -- category_id (FORKLİFTLER)\n    1, -- brand_id (İXTİF)',
    sql
)

# 2. series_name kaldır
sql = re.sub(
    r'sku,\s*model_number,\s*series_name,\s*title',
    'sku,\n    model_number,\n    title',
    sql
)
sql = re.sub(
    r"'CPD15TVL', -- sku\s*'CPD15TVL', -- model_number\s*'CPD TVL Series', -- series_name",
    "'CPD15TVL', -- sku\n    'CPD15TVL', -- model_number",
    sql
)

# 3. compare_price -> compare_at_price
sql = sql.replace('compare_price,', 'compare_at_price,')
sql = sql.replace('-- compare_price', '-- compare_at_price')

# 4. warranty_months ve availability kaldır, stock_quantity -> current_stock
sql = re.sub(
    r'lead_time_days,\s*warranty_months,\s*condition,\s*availability,\s*product_type',
    'lead_time_days,\n    condition,\n    product_type',
    sql
)
sql = re.sub(
    r'stock_quantity,',
    'current_stock,',
    sql
)

# 5. is_new_arrival, sort_order, rating_avg, rating_count kaldır
sql = re.sub(
    r'is_bestseller,\s*is_new_arrival,\s*sort_order,\s*view_count,\s*rating_avg,\s*rating_count,\s*tags',
    'is_bestseller,\n    view_count,\n    sales_count,\n    published_at,\n    warranty_info,\n    tags',
    sql
)

# 6. use_cases, competitive_advantages,target_industries, faq_data, related_products, cross_sell_products, up_sell_products, metadata kaldır
sql = re.sub(
    r'tags,\s*use_cases,\s*competitive_advantages,\s*target_industries,\s*faq_data,\s*media_gallery,\s*related_products,\s*cross_sell_products,\s*up_sell_products,\s*metadata,\s*published_at',
    'tags,\n    media_gallery',
    sql
)

# 7. barcode, max_installments, low_stock_threshold, allow_backorder, video_url, manual_pdf_url, shipping_info ekle
sql = re.sub(
    r'(model_number,)\s*(title)',
    r'\1\n    barcode,\n    \2',
    sql
)
sql = re.sub(
    r"('CPD15TVL', -- model_number)\s*(JSON_OBJECT)",
    r"\1\n    NULL, -- barcode\n    \2",
    sql
)

sql = re.sub(
    r'(installment_available,)\s*(deposit_required)',
    r'\1\n    max_installments,\n    \2',
    sql
)
sql = re.sub(
    r'(1, -- installment_available)\s*(1, -- deposit_required)',
    r'\1\n    12, -- max_installments\n    \2',
    sql
)

sql = re.sub(
    r'(current_stock,)\s*(lead_time_days)',
    r'\1\n    low_stock_threshold,\n    allow_backorder,\n    \2',
    sql
)
sql = re.sub(
    r'(0, -- current_stock)\s*(45, -- lead_time_days)',
    r'\1\n    5, -- low_stock_threshold\n    0, -- allow_backorder\n    \2',
    sql
)

sql = re.sub(
    r'(media_gallery,)\s*(is_active)',
    r'\1\n    video_url,\n    manual_pdf_url,\n    \2',
    sql
)
sql = re.sub(
    r"(\), -- media_gallery)\s*(1, -- is_active)",
    r"\1\n    NULL, -- video_url\n    NULL, -- manual_pdf_url\n    \2",
    sql
)

# 8. WARRANTY_INFO ekle - warranty_months yerine JSON
# İlk olarak VALUES kısmındaki warranty_months'ları bul ve warranty_info JSON'a çevir
sql = re.sub(
    r'45, -- lead_time_days\s*24, -- warranty_months\s*\'new\', -- condition\s*\'on_order\', -- availability',
    "45, -- lead_time_days\n    'new', -- condition",
    sql
)

# VALUES kısmına warranty_info ekle (published_at'tan önce)
# Bunu manuel yapacağız çünkü çok karmaşık

# 9. shipping_info ekle (tags'den sonra)
sql = re.sub(
    r"(JSON_ARRAY\([^]]+\), -- tags)\s*(JSON_ARRAY\([^]]+\), -- media_gallery)",
    r"\1\n    NULL, -- shipping_info\n    \2",
    sql
)

# 10. Fazla VALUES satırlarını temizle (use_cases, competitive_advantages, vb)
# Bunları regex ile kaldırmak çok zor, manuel düzeltme gerekebilir

# Çıktıyı kaydet
with open('/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/sql-inserts/CPD15-18-20TVL-FIXED.sql', 'w', encoding='utf-8') as f:
    f.write(sql)

print("✅ SQL düzeltildi ve kaydedildi: CPD15-18-20TVL-FIXED.sql")
print(f"📊 Toplam satır sayısı: {len(sql.splitlines())}")
