#!/usr/bin/env python3
"""
JSON Dosyalarını Phase 1 Formatına Dönüştürme Script
- name → title
- seo_data kaldırma
- string slug → JSON slug
- Tüm metin field'larına "vs." ekleme
"""

import json
import os
from pathlib import Path
from datetime import datetime

# Dosyalar
FILES = [
    "CPD15TVL-product-NEW.json",
    "CPD18TVL-product-NEW.json",
    "CPD20TVL-product-NEW.json",
    "EST122-product-NEW.json",
    "F4-product-NEW.json"
]

def add_vs_to_json_field(obj):
    """JSON object'e 'vs.' ekle"""
    if isinstance(obj, dict):
        # Eğer tr ve en varsa ama vs. yoksa
        if "tr" in obj and "en" in obj and "vs." not in obj:
            obj["vs."] = "..."
    return obj

def convert_slug_to_json(slug_value):
    """String slug'ı JSON'a çevir"""
    if isinstance(slug_value, str):
        # Örnek: "cpd15tvl-elektrikli-forklift" → {"tr": "cpd15tvl-elektrikli-forklift", "en": "...", "vs.": "..."}
        return {
            "tr": slug_value,
            "en": slug_value.replace("elektrikli", "electric").replace("forklift", "forklift")
                          .replace("istif", "stacker").replace("makinesi", "")
                          .replace("transpalet", "pallet-truck").replace("devrim-nitelikli", "revolutionary")
                          .replace("akulu", "electric").strip(),
            "vs.": "..."
        }
    return slug_value

def process_variants(variants):
    """Variants array'ini işle"""
    if not variants:
        return variants

    for variant in variants:
        # name → title
        if "name" in variant:
            variant["title"] = variant.pop("name")

        # title'a vs. ekle
        if "title" in variant:
            variant["title"] = add_vs_to_json_field(variant["title"])

    return variants

def process_options(options):
    """Options array'ini işle"""
    if not options:
        return options

    for option in options:
        # name → title
        if "name" in option:
            option["title"] = option.pop("name")

        # title'a vs. ekle
        if "title" in option:
            option["title"] = add_vs_to_json_field(option["title"])

        # values içindeki label'lara vs. ekle
        if "values" in option:
            for value in option["values"]:
                if "label" in value:
                    value["label"] = add_vs_to_json_field(value["label"])

    return options

def process_highlighted_features(features):
    """Highlighted features array'ini işle"""
    if not features:
        return features

    for feature in features:
        # title ve description'a vs. ekle
        if "title" in feature:
            feature["title"] = add_vs_to_json_field(feature["title"])
        if "description" in feature:
            feature["description"] = add_vs_to_json_field(feature["description"])

    return features

def process_media_gallery(media):
    """Media gallery array'ini işle"""
    if not media:
        return media

    for item in media:
        # alt ve title'a vs. ekle
        if "alt" in item:
            item["alt"] = add_vs_to_json_field(item["alt"])
        if "title" in item:
            item["title"] = add_vs_to_json_field(item["title"])

    return media

def process_faq_data(faq):
    """FAQ data array'ini işle"""
    if not faq:
        return faq

    for item in faq:
        # question ve answer'a vs. ekle
        if "question" in item:
            item["question"] = add_vs_to_json_field(item["question"])
        if "answer" in item:
            item["answer"] = add_vs_to_json_field(item["answer"])

    return faq

def process_features(features):
    """Features object'ini işle"""
    if not features:
        return features

    # primary ve secondary array'lerini işle
    for key in ["primary", "secondary"]:
        if key in features and isinstance(features[key], list):
            for item in features[key]:
                if isinstance(item, dict):
                    for lang in item:
                        # Her dil için "vs." ekle kontrolü
                        pass  # Zaten dict, "vs." manuel eklenecek

    return features

def convert_json_file(filepath):
    """Tek bir JSON dosyasını dönüştür"""
    print(f"\n{'='*60}")
    print(f"İşleniyor: {filepath.name}")
    print(f"{'='*60}")

    # Backup oluştur
    backup_path = filepath.with_suffix('.json.backup')
    if not backup_path.exists():
        with open(filepath, 'r', encoding='utf-8') as f:
            data = f.read()
        with open(backup_path, 'w', encoding='utf-8') as f:
            f.write(data)
        print(f"✓ Backup oluşturuldu: {backup_path.name}")

    # JSON'u yükle
    with open(filepath, 'r', encoding='utf-8') as f:
        data = json.load(f)

    changes = []

    # 1. basic_data.name → title
    if "basic_data" in data:
        if "name" in data["basic_data"]:
            data["basic_data"]["title"] = data["basic_data"].pop("name")
            changes.append("✓ basic_data.name → title")

        # title'a vs. ekle
        if "title" in data["basic_data"]:
            data["basic_data"]["title"] = add_vs_to_json_field(data["basic_data"]["title"])
            changes.append("✓ basic_data.title'a 'vs.' eklendi")

        # slug'ı JSON'a çevir
        if "slug" in data["basic_data"]:
            if isinstance(data["basic_data"]["slug"], str):
                data["basic_data"]["slug"] = convert_slug_to_json(data["basic_data"]["slug"])
                changes.append("✓ slug string → JSON")
            # Slug JSON ise vs. ekle
            data["basic_data"]["slug"] = add_vs_to_json_field(data["basic_data"]["slug"])
            changes.append("✓ slug'a 'vs.' eklendi")
        else:
            # Slug yoksa oluştur
            if "title" in data["basic_data"] and isinstance(data["basic_data"]["title"], dict):
                if "tr" in data["basic_data"]["title"]:
                    title_tr = data["basic_data"]["title"]["tr"].lower()
                    slug_tr = title_tr.replace(" ", "-").replace("ı", "i")
                    data["basic_data"]["slug"] = {
                        "tr": slug_tr,
                        "en": data["basic_data"]["title"].get("en", "").lower().replace(" ", "-"),
                        "vs.": "..."
                    }
                    changes.append("✓ slug oluşturuldu")

        # short_description ve body'a vs. ekle
        for field in ["short_description", "body"]:
            if field in data["basic_data"]:
                data["basic_data"][field] = add_vs_to_json_field(data["basic_data"][field])
                changes.append(f"✓ basic_data.{field}'a 'vs.' eklendi")

    # 2. seo_data'yı kaldır
    if "seo_data" in data:
        del data["seo_data"]
        changes.append("✓ seo_data kaldırıldı")

    # 3. variants array
    if "variants" in data:
        data["variants"] = process_variants(data["variants"])
        changes.append(f"✓ {len(data['variants'])} variant işlendi")

    # 4. options array
    if "options" in data:
        data["options"] = process_options(data["options"])
        changes.append(f"✓ {len(data['options'])} option işlendi")

    # 5. highlighted_features array
    if "highlighted_features" in data:
        data["highlighted_features"] = process_highlighted_features(data["highlighted_features"])
        changes.append(f"✓ {len(data['highlighted_features'])} highlighted feature işlendi")

    # 6. media_gallery array
    if "media_gallery" in data:
        data["media_gallery"] = process_media_gallery(data["media_gallery"])
        changes.append(f"✓ {len(data['media_gallery'])} media item işlendi")

    # 7. faq_data array
    if "faq_data" in data:
        data["faq_data"] = process_faq_data(data["faq_data"])
        changes.append(f"✓ {len(data['faq_data'])} FAQ işlendi")

    # 8. pricing.price_display_text
    if "pricing" in data and "price_display_text" in data["pricing"]:
        data["pricing"]["price_display_text"] = add_vs_to_json_field(data["pricing"]["price_display_text"])
        changes.append("✓ pricing.price_display_text'e 'vs.' eklendi")

    # 9. features (use_cases, competitive_advantages, target_industries)
    for field in ["features", "use_cases", "competitive_advantages", "target_industries"]:
        if field in data:
            if isinstance(data[field], dict):
                for lang in data[field]:
                    if isinstance(data[field][lang], list):
                        # List items, "vs." eklenemez
                        pass

    # 10. categories ve brand name
    if "categories" in data:
        for cat in data["categories"]:
            if "name" in cat:
                cat["name"] = add_vs_to_json_field(cat["name"])
        changes.append("✓ categories.name'e 'vs.' eklendi")

    if "brand" in data and "name" in data["brand"]:
        data["brand"]["name"] = add_vs_to_json_field(data["brand"]["name"])
        changes.append("✓ brand.name'e 'vs.' eklendi")

    # JSON'u kaydet (pretty print)
    with open(filepath, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

    # Sonuçları göster
    print("\nYapılan Değişiklikler:")
    for change in changes:
        print(f"  {change}")

    print(f"\n✓ Dosya güncellendi: {filepath.name}")

    return len(changes)

def main():
    """Ana fonksiyon"""
    print("\n" + "="*60)
    print("JSON Dosyalarını Phase 1 Formatına Dönüştürme")
    print("="*60)

    script_dir = Path(__file__).parent
    total_changes = 0
    processed_files = 0

    for filename in FILES:
        filepath = script_dir / filename

        if not filepath.exists():
            print(f"\n❌ HATA: Dosya bulunamadı: {filename}")
            continue

        try:
            changes = convert_json_file(filepath)
            total_changes += changes
            processed_files += 1
        except Exception as e:
            print(f"\n❌ HATA: {filename} işlenirken hata: {e}")
            continue

    # Özet
    print("\n" + "="*60)
    print("ÖZET")
    print("="*60)
    print(f"İşlenen dosya sayısı: {processed_files}/{len(FILES)}")
    print(f"Toplam değişiklik: {total_changes}")
    print("\nTamamlandı! ✓")
    print(f"Tarih: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("="*60 + "\n")

if __name__ == "__main__":
    main()
