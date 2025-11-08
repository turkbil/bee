-- ================================================
-- CENTRAL DB SEED DATA - AI Tenant Directives
-- ================================================
-- AMAÇ: Tenant-specific AI ayarları (central'de tenant_id ile)
-- DURUM: ONAYLANDI - FINAL KARAR (2025-11-08)
-- ================================================

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE IF NOT EXISTS `ai_tenant_directives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Directive ID - Benzersiz tanımlayıcı',
  `tenant_id` int unsigned NOT NULL COMMENT 'Hangi tenant (örn: 2=ixtif.com)',
  `directive_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ayar anahtarı - Kod içinde kullanılan isim (örn: "greeting_style", "max_products")',
  `directive_value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ayar değeri - String, sayı, JSON olabilir (örn: "friendly", "5", "true")',
  `directive_type` enum('string','integer','boolean','json','array') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT 'Değer tipi - Kod tarafında nasıl parse edileceğini belirler',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT 'Kategori - Ayarları gruplamak için (general, behavior, pricing, contact, display, lead)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Açıklama - Admin için bilgi, bu ayar ne işe yarar',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif mi? 1=kullanımda, 0=devre dışı (sadece aktif olanlar okunur)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tenant_key` (`tenant_id`,`directive_key`),
  KEY `ai_tenant_directives_tenant_id_category_index` (`tenant_id`,`category`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- ================================================
-- TENANT ID = 2 (ixtif.com) - İXTİF ÖZEL AYARLAR
-- ================================================

INSERT INTO `ai_tenant_directives` VALUES
(1,2,'welcome_message','Merhaba! Nasıl yardımcı olabilirim?','string','chat','Chat başlangıcında gösterilen karşılama mesajı',1,'2025-11-06 15:37:06','2025-11-06 15:39:27'),
(2,2,'max_tokens','500','integer','ai_config','AI yanıtlarının maksimum token sayısı',1,'2025-11-06 15:37:06','2025-11-06 15:37:06'),
(3,2,'temperature','0.7','string','ai_config','AI yaratıcılık seviyesi (0-1 arası)',1,'2025-11-06 15:37:06','2025-11-06 15:37:06'),
(7,2,'welcome_variations','["🎯 Hangi ürünümüz ilginizi çekti?", "💼 Size nasıl yardımcı olabilirim?", "🚚 Hangi ürünü arıyorsunuz?", "✨ Hoş geldiniz! Ne lazım?", "💡 Merhaba! Ürün mü arıyorsunuz?"]','json','chat','Karşılama mesajı çeşitleri',1,NULL,NULL),
(8,2,'product_found_responses','["🔥 İşte en uygun seçenekler:", "✅ Tam aradığınız ürünler:", "💡 Size özel fiyatlar:", "🎯 Bu ürünler tam size göre:", "⭐ En çok satanlar:"]','json','chat','Ürün bulundu yanıtları',1,NULL,NULL),
(9,2,'call_to_action','["📞 Detaylı bilgi: 0212 XXX XX XX", "💬 Hemen sipariş verin!", "🚚 Bugün sipariş, yarın kargoda!", "✅ Tıklayın, detaylı bilgi alın!", "💰 Özel fiyat için arayın!"]','json','chat','Harekete geçirici mesajlar',1,NULL,NULL),
(10,2,'system_prompt_override','Satış odaklı konuş. Ürün özellikleri ve fiyatları vurgula. Doğal dil kullan.','string','ai_config','AI sistem prompt override',1,NULL,NULL),
(11,2,'chatbot_system_prompt','Sen profesyonel bir e-ticaret satış asistanısın.\n\n**KRİTİK KURALLAR:**\n\n1. **ÜRÜN VARSA:**\n   - {product_context} içindeki ürünleri kullan\n   - ASLA ürün uydurma, sadece listedeki ürünleri göster\n   - Fiyatları göster (zaten formatlı)\n   - Stok durumunu belirt\n   - Link\'leri paylaş\n\n2. **ÜRÜN YOKSA:**\n   - \"Aradığınız ürün şu anda stoklarımızda bulunmuyor.\"\n   - \"Müşteri temsilcimiz size yardımcı olabilir.\"\n   - \"Lütfen iletişim bilgilerinizi paylaşır mısınız?\"\n   - ASLA ürün uydurma!\n\n3. **KONUŞMA:**\n   - Konuşma geçmişini kontrol et\n   - Kullanıcı adını hatırla\n   - Samimi ama profesyonel ol\n   - Emoji kullan ama abartma\n\n**YAPMA:**\n❌ Olmayan ürün uydurma\n❌ Fiyat uydurma\n❌ \"Model A, B, C\" gibi genel isimler\n❌ \"Stokta uygun ürün yok\" sonra ürün gösterme','string','chatbot','Ana chatbot system prompt',1,NULL,NULL),
(12,2,'chatbot_no_product_response','🔍 Aradığınız ürün şu anda stoklarımızda bulunmuyor.\n\n💬 **Müşteri temsilcimiz size yardımcı olabilir!**\n\nLütfen iletişim bilgilerinizi (telefon/email) paylaşır mısınız? En kısa sürede size dönüş yapacağız.','string','chatbot','Ürün bulunamadığında gösterilecek mesaj',1,NULL,NULL),
(13,2,'chatbot_hallucination_prevention','true','boolean','chatbot','AI hallucination\'ı engelle - sadece gerçek ürünleri göster',1,NULL,NULL),
(14,2,'chatbot_require_product_context','true','boolean','chatbot','product_context olmadan ürün önerme',1,NULL,NULL);

-- ================================================
-- TEMPLATE KOPYALAMA İÇİN ÖRNEK KULLANIM
-- ================================================
-- Yeni tenant oluşturulduğunda:
-- AITenantDirective::copyFromTenant(2, $newTenantId);
-- Bu komut tenant_id=2'nin tüm directive'lerini yeni tenant'a kopyalar
-- ================================================
