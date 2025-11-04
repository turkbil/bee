-- =====================================================
-- TÜM ÜRÜNLER - TOPLU SQL INSERT
-- Products: CPD15TVL, CPD18TVL, CPD20TVL, EST122, F4
-- Date: 2025-10-09
-- =====================================================

SET FOREIGN_KEY_CHECKS=0;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

-- =====================================================
-- BRAND: EP Equipment
-- =====================================================
INSERT INTO shop_brands (id, name, slug, description, logo_url, website_url, country_code, founded_year, is_active, certifications, created_at, updated_at)
VALUES (1, JSON_OBJECT('tr','EP Equipment','en','EP Equipment'), 'ep-equipment', 
JSON_OBJECT('tr','Dünya çapında lider elektrikli malzeme taşıma ekipmanları üreticisi','en','World-leading manufacturer of electric material handling equipment'),
'brands/ep-equipment.png', 'https://www.ep-equipment.com', 'CN', 1997, 1,
JSON_ARRAY(JSON_OBJECT('name','CE','year',2005), JSON_OBJECT('name','ISO 9001','year',2000)), NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), updated_at=NOW();

-- =====================================================
-- CATEGORIES
-- =====================================================
-- Forklift (Parent)
INSERT INTO shop_categories (id, parent_id, name, slug, level, path, is_active, sort_order, created_at, updated_at)
VALUES (1, NULL, JSON_OBJECT('tr','Forklift','en','Forklift'), 'forklift', 1, '1', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), updated_at=NOW();

-- Akülü Forklift
INSERT INTO shop_categories (id, parent_id, name, slug, level, path, is_active, sort_order, created_at, updated_at)
VALUES (11, 1, JSON_OBJECT('tr','Akülü Forklift','en','Electric Forklift'), 'akulu-forklift', 2, '1.11', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), updated_at=NOW();

-- İstif Makineleri (Parent)
INSERT INTO shop_categories (id, parent_id, name, slug, level, path, is_active, sort_order, created_at, updated_at)
VALUES (3, NULL, JSON_OBJECT('tr','İstif Makineleri','en','Stackers'), 'istif-makineleri', 1, '3', 1, 3, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), updated_at=NOW();

-- Yürüyen Operatörlü İstif
INSERT INTO shop_categories (id, parent_id, name, slug, level, path, is_active, sort_order, created_at, updated_at)
VALUES (22, 3, JSON_OBJECT('tr','Yürüyen Operatörlü İstif','en','Pedestrian Stacker'), 'yuruyen-operatorlu-istif', 2, '3.22', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), updated_at=NOW();

-- Transpalet (Parent)
INSERT INTO shop_categories (id, parent_id, name, slug, level, path, is_active, sort_order, created_at, updated_at)
VALUES (4, NULL, JSON_OBJECT('tr','Transpalet','en','Pallet Truck'), 'transpalet', 1, '4', 1, 4, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), updated_at=NOW();

-- Elektrikli Transpalet
INSERT INTO shop_categories (id, parent_id, name, slug, level, path, is_active, sort_order, created_at, updated_at)
VALUES (31, 4, JSON_OBJECT('tr','Elektrikli Transpalet','en','Electric Pallet Truck'), 'elektrikli-transpalet', 2, '4.31', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), updated_at=NOW();

-- =====================================================
-- NOT: Ürün INSERT'leri için ayrı dosyalara bakınız:
-- - CPD15TVL-insert.sql
-- - CPD18TVL-insert.sql  
-- - CPD20TVL-insert.sql
-- - EST122-insert.sql
-- - F4-insert.sql
-- =====================================================

SET FOREIGN_KEY_CHECKS=1;
SET SQL_MODE=@OLD_SQL_MODE;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
