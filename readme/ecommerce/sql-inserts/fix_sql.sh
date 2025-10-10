#!/bin/bash
# İlk SQL'i al ve kolon isimlerini düzelt

sed -e 's/parent_brand_id,/-- parent_brand_id (YOK),/g' \
    -e 's/short_description,/-- short_description (brands için YOK),/g' \
    -e 's/metadata,/-- metadata (brands için YOK),/g' \
    -e 's/NULL, -- parent_brand_id/-- NULL, -- parent_brand_id (YOK)/g' \
    -e 's/JSON_OBJECT.*short_description/-- JSON_OBJECT short_description (YOK)/g' \
    -e 's/JSON_OBJECT.*metadata/-- JSON_OBJECT metadata (YOK)/g' \
    "/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/sql-inserts/CPD15-18-20TVL-insert-BACKUP.sql"
