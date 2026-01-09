-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 08 Oca 2026, 20:09:53
-- Sunucu sürümü: 10.11.15-MariaDB
-- PHP Sürümü: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `muzibu_com_tr_yedek`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_sectors`
--

CREATE TABLE `muzibu_sectors` (
  `id` int(11) NOT NULL,
  `title_tr` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `meta_title` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `muzibu_sectors`
--

INSERT INTO `muzibu_sectors` (`id`, `title_tr`, `slug`, `thumb`, `created`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
(5, 'Cafe', 'cafe', 'SECTOR_QUZznO-4EEVub-zZ8h6s-dOrD60-mwR8TJ-2SmHiz.jpg', '2025-05-07 22:59:46', 'Cafe | Telifsiz Cafe Müzikleri', 'kafe müzikleri, telifsiz cafe playlist, işletme müzik yayını, yasal müzik yayını, chill kafe müzikleri, sakin müzik işletmeler, kahve müziği telifsiz, cafe ambiyans playlist, cafe background music, royalty-free cafe music, acoustic playlist for cafe, lo-fi kafe müzikleri, coffee time music', 'Kafenizin atmosferini telifsiz ve yasal müziklerle tamamlayın. Muzibu&#039;nun özel playlistleri ile müşterilerinize keyifli, huzurlu ve unutulmaz bir kafe deneyimi sunun.'),
(6, 'Restaurant', 'restaurant', 'SECTOR_v6XcqM-Km23EA-EwG0ke-OkMyok-8AAxHM-w8TzIZ.jpg', '2025-05-07 23:01:48', 'Restoranlar | Telifsiz Müzik', 'restoran müzikleri, telifsiz restoran playlist, işletme müzik yayını, akşam yemeği müziği, fine dining müzikleri, yasal müzik yayını, restoran ambiyans müziği, restaurant background music, royalty-free restaurant music, chill dining playlist, relaxing restaurant music, restaurant lounge music, yemek müziği telifsiz', 'Restoranınıza özel telifsiz müzik yayını ile yemek deneyimini zenginleştirin. Muzibu&#039;nun yasal ve profesyonel playlistleriyle atmosferinizi tamamlayın, misafir memnuniyetini artırın.'),
(7, 'Bar / Pub', 'bar-pub', 'SECTOR_xTl0yn-gtwz7E-JsRDjL-bWDUyL-rwd8Ci-qaonE7.jpeg', '2025-05-07 23:02:40', 'Telifsiz Müzik ve Playlistler |Bar', 'bar müzikleri, pub müzikleri, telifsiz müzik, gece mekanları müzikleri, pub chill müzik, kokteyl bar müzikleri, lounge bar playlist, telifsiz deep house, bar atmosfer şarkıları, gece kulübü telifsiz müzik, bar işletmeleri müzik çözümleri, pub için müzik önerileri, hafif tempolu bar müzikleri, kokteyl mekanları playlistleri, gece bar ambiyans müziği, alkollü mekanlar için müzik, bar ortamı müzikleri, işletmeler için müzik, ücretsiz mekan müziği', 'Bar ve pub atmosferinizi güçlendiren Muzibu&#039;nun telifsiz ve yasal müzik playlistleri ile müşterilerinize eşsiz bir eğlence deneyimi sunun. Mekanınız için en iyi ambiyansı yaratın!'),
(8, 'Coffee Shop', 'coffeeshop', 'SECTOR_dTj8EP-JlHy1C-lXJwDD-en9AQI-d9m2iz-JEgOGd.jpeg', '2025-05-07 23:06:51', 'Coffee Shop | Telifsiz Müzik', 'coffee shop müzikleri, kahve dükkanı playlist, telifsiz cafe müziği, işletme müzik yayını, chill kahve müzikleri, yasal müzik yayını, kahve molası müzikleri, lo-fi cafe playlist, acoustic coffee shop music, royalty-free coffee music, cafe ambience music, coffee shop background music, relaxing cafe playlist', 'Coffee Shop atmosferinizi Muzibu&#039;nun telifsiz ve yasal müzikleriyle zenginleştirin. Kahve molalarına eşlik eden özgün playlistlerle müşterilerinize huzurlu ve samimi bir ortam sunun!'),
(9, 'Mağaza', 'magaza', 'SECTOR_aum0HH-tEj9h9-t1WN9a-5it5T0-Ez8IsC-Q4MKxh.jpg', '2025-05-07 23:11:14', 'Mağaza | Telifsiz Müzik', 'mağaza müzikleri, butik müzik yayını, telifsiz mağaza playlist, işletme müzikleri, alışveriş müziği telifsiz, mağaza ambiyans müziği, butik atmosfer playlist, telifsiz müzik yayını, vitrin müzikleri, giyim mağazası müzikleri, mağaza içi müzik yayını', 'Mağaza ve butiklerde alışveriş keyfini artıran telifsiz müzik yayını ile müşterilerinize hoş bir atmosfer sunun. Muzibu&#039;nun yasal playlistleriyle fark yaratın.'),
(10, 'Butik Otel', 'otel', 'SECTOR_8S0Qnh-rbWsto-QIgQoQ-GaAeeP-MhHBT6-zoK6t7.jpg', '2025-05-07 23:14:22', 'Otel | Telifsiz Müzik', 'otel müzikleri, lobi müzik yayını, telifsiz otel playlist, spa müziği telifsiz, restoran müzik yayını, işletme müzik çözümleri, otel ambiyans müziği, hotel background music, royalty-free hotel music, hotel lounge playlist, relaxing music for hotels, otel resepsiyon müziği, sakin otel müzikleri', 'Otelinizin lobi, restoran, spa ve ortak alanlarında telifsiz ve yasal müziklerle profesyonel bir atmosfer oluşturun. Muzibu&#039;nun özel playlistleriyle misafir memnuniyetini artırın.'),
(11, 'Spor Salonu', 'spor-salonu', 'SECTOR_hzEWyY-QxKOrk-lDF2Jm-qEoiyR-SseFka-35STZv.jpg', '2025-05-07 23:15:52', 'Spor Salonları | Telifsiz Müzik', 'spor salonu müzikleri, fitness playlist telifsiz, telifsiz spor müziği, yasal müzik yayını, antrenman müzikleri, egzersiz playlist, gym müzik yayını, işletme müzik çözümleri, workout music for business, royalty-free gym music, high energy music playlist, gym background music, fitness club müzikleri', 'Spor salonunuzda motivasyonu artıran telifsiz müziklerle üyelerinize enerjik bir ortam sunun. Muzibu&#039;nun yasal playlistleriyle antrenmanlara ritim katın!'),
(12, 'Market', 'market', 'SECTOR_x3GJbC-awCTXn-J0aZH2-wZuQ6Q-Oa58u1-Tfbrsk.jpeg', '2025-05-07 23:24:18', 'Market ve Süpermarketler | Telifsiz Müzik', 'market müzik yayını, süpermarket playlist, telifsiz market müzikleri, işletme müzikleri, alışveriş müzik yayını, market arka plan müziği, yasal müzik yayını, telifsiz müzik market, market ortam müzikleri, market için playlist', 'Market ve süpermarketlerinizde alışverişi keyifli hale getirin. Muzibu&#039;nun telifsiz ve yasal müzik yayınları ile müşterilerinize enerjik ve huzurlu bir alışveriş ortamı sunun.'),
(13, 'Güzellik Merkezi / Kuaför', 'guzellik-merkezi-kuafor', 'SECTOR_caf4kg-4OVVDS-KV3tQU-vw9DO8-9FKp6p-otW5Th.jpeg', '2025-05-07 23:25:07', 'Güzellik Merkezleri ve Kuaförler | Telifsiz Müzik', 'kuaför müzikleri, güzellik salonu müzikleri, telifsiz spa müzikleri, işletme playlistleri, yasal müzik yayını, sakin kuaför müzikleri, güzellik merkezi playlist, relaxing music işletmeler, salon müzik çözümleri, bakım salonu müzikleri, wellness müzik telifsiz, kuaför ambiyans müziği', 'Güzellik merkeziniz veya kuaför salonunuzda huzur dolu bir atmosfer yaratın. Muzibu&#039;nun telifsiz müzik playlistleriyle müşterilerinize keyifli ve yasal bir deneyim sunun.'),
(14, 'Beach Club', 'beach-club', 'SECTOR_RzOuvt-9Z3Mf0-1XKglj-Qv70cK-LdRfJk-4ZwvAb.jpeg', '2025-05-07 23:43:26', 'Beach Club  | Telifsiz Müzik', 'beach club müzikleri, plaj müzikleri, telifsiz müzik, sahil mekanları müzikleri, telifsiz beach house, işletme playlistleri, sahil atmosfer müzikleri, summer vibes müzikleri, chillout beach müzikleri, happy hour telifsiz müzik, plaj ambiyans playlistleri, yazlık mekan müziği, deniz kenarı telifsiz müzik, beach club işletme müzikleri, chill house telifsiz', 'Beach Club atmosferinizi Muzibu&#039;nun telifsiz ve yasal playlistleriyle unutulmaz kılın. Plaj partilerinizden gün batımı keyfine kadar tüm anlarınız için özenle seçilmiş müziklerle mekanınızı öne çıkarın!'),
(15, 'Ofis &amp; Ortak Çalışma Alanı', 'ofis-ortak-calisma-alani', 'SECTOR_QyXsZa-Mie3Cc-LLH4Mv-VPsUFT-RCOQbH-kUbIDb.jpeg', '2025-05-07 23:52:24', 'Ofis ve Ortak Çalışma Alanları | Telifsiz Müzik', 'ofis müzikleri, ortak çalışma alanı müziği, telifsiz çalışma playlisti, odaklanma müziği, işletme müzik yayını, coworking space playlist, yasal müzik yayını, verimlilik müzikleri, telifsiz lo-fi müzik, ofis arka plan müziği, chill çalışma müzikleri', 'Ofisinizde ya da ortak çalışma alanınızda verimliliği artıran telifsiz müziklerle odaklanmayı kolaylaştırın. Muzibu&#039;nun yasal playlistleriyle sessizliğe ritim, çalışmaya motivasyon katın.'),
(16, 'Benzin İstasyonu', 'benzin-istasyonu', 'SECTOR_VFD07n-PH3qYA-Pysfr5-jOml8X-rVxnDN-qqnoEv.jpeg', '2025-05-08 00:01:25', 'Benzin İstasyonları  | Telifsiz Müzik', 'benzin istasyonu müzikleri, telifsiz müzik, istasyon playlist, market müzik yayını, akaryakıt istasyonu müzik, işletme müzikleri, istasyon için playlist, bekleme alanı müzikleri, market ambiyans müziği, yasal müzik yayını, dış mekan müzikleri, benzinlik müzik yayını', 'Benzin istasyonunuzda müşteri deneyimini güçlendirin! Muzibu&#039;nun telifsiz müzik hizmeti ile market, bekleme alanı ve dış alanlarda yasal ve etkili müzik yayını yapın.'),
(17, 'Eczane / Tıp Merkezi', 'ozel-tip-merkezi', 'SECTOR_j76qSc-TojnOG-RCQk5V-sayPVU-SzQdN2-ZmwZNu.jpeg', '2025-05-08 00:12:44', 'Tıp Merkezleri ve Klinikler İçin Telifsiz Müzik | Muzibu', 'tıp merkezi müzikleri, klinik müzik yayını, poliklinik playlist telifsiz, bekleme salonu müzikleri, sağlık merkezi müzik, yasal müzik yayını, işletme müzik çözümleri, telifsiz rahatlatıcı müzik, hastane arka plan müziği, klinik atmosfer müziği, sağlık sektörü playlist', 'Tıp merkezi, klinik ve poliklinik ortamlarınızda sakin ve huzur veren telifsiz müziklerle hastalarınıza güvenli bir atmosfer sunun. Muzibu&#039;nun yasal playlistleri ile bekleme süreci rahat geçsin.'),
(18, 'Fırın / Pastane', 'firin-pastane', 'SECTOR_DwdUmc-eI6YMU-XCCY5A-f3CdPz-EBVMMY-ZgS0RW.jpeg', '2025-05-08 00:16:14', 'Fırın ve Pastaneler İçin Telifsiz Müzik ve Playlistler | Muzibu', 'fırın müzikleri, pastane müzikleri, telifsiz müzik, işletme playlistleri, yasal müzik yayını, sakin fırın müzikleri, pastane ambiyans müziği, telifsiz kahvaltı müziği, tatlıcı müzikleri, unlu mamul işletmesi playlist, fırın cafe müzikleri, günlük işletme müziği, arka plan müzik yayını', 'Fırın ve pastanelerinizin samimi atmosferini Muzibu&#039;nun telifsiz müzikleriyle tamamlayın. Yasal ve huzurlu playlistlerle müşterilerinize keyifli bir alışveriş deneyimi sunun.'),
(20, 'Kahvaltı Mekanı', 'kahvalti-mekani', 'SECTOR_26yYaG-F1MoJS-rF3v0n-1eyP2j-dGVIi2-8smdwo.jpeg', '2025-05-31 15:40:24', 'Kahvaltı Mekanları  | Telifsiz Müzik', 'kahvaltı müzikleri, sabah müziği telifsiz, kahvaltı playlist, işletme müzikleri, kahvaltı mekanı ambiyans, sakin sabah müzikleri, telifsiz cafe müziği, brunch müzik yayını, kahvaltı salonu playlist, arka plan sabah müzikleri, huzurlu sabah müzikleri', 'Kahvaltı mekanınızda huzurlu bir atmosfer yaratın. Muzibu&#039;nun telifsiz ve yasal playlistleri ile sabah keyfini müzikle taçlandırın, müşterilerinize unutulmaz bir kahvaltı deneyimi yaşatın.'),
(21, 'Mantı Restoranı', 'manti-restorani', 'SECTOR_jMIO88-HsZBhi-tBZfBQ-2X4wHE-ATEGrs-xgWO1a.jpeg', '2025-06-01 13:56:20', 'Mantı Restoranları İçin Telifsiz Müzik ve Playlistler | Muzibu', 'mantı restoranı müzikleri, telifsiz restoran müzikleri, geleneksel restoran playlist, işletme müzik yayını, yasal müzik yayını, türk mutfağı müzikleri, mantıcı müzikleri, lokanta playlist telifsiz, arka plan müzikleri, telifsiz müzik işletmeler', 'Mantı restoranınızda huzurlu ve sıcak bir ortam sunun. Muzibu&#039;nun geleneksel yapıya uygun telifsiz müzikleriyle müşterilerinize keyifli bir yemek deneyimi yaşatın.'),
(23, 'Dönerci / Kebapçı / Çorbacı', 'doner-kebap-dukkani', 'SECTOR_CYCSAC-BAn2j6-AAJplj-rCpDfW-c6piz7-qdb9Si.jpg', '2025-06-11 23:58:10', 'Dönerci, Kebapçı ve Çorbacılar İçin Telifsiz Müzik | Muzibu', 'dönerci müzikleri, kebapçı müzik yayını, çorbacı playlist, telifsiz müzik, geleneksel restoran müzikleri, işletme playlistleri, yasal müzik yayını, türk müziği telifsiz, esnaf lokantası müzikleri, çorbacı atmosfer müziği, telifsiz türkü playlist, işletme müzik çözümleri', 'Dönerci, kebapçı ve çorbacı gibi geleneksel işletmeler için özel hazırlanmış telifsiz müzik playlistleriyle müşteri memnuniyetini artırın. Yasal müzik yayınıyla huzurlu bir ortam sunun.'),
(26, 'Pizza Restoranı', 'pizza-restorani', 'SECTOR_29xd76-MO7IwP-WKeQNO-dmWfJL-xJP5gr-JuQKrV.jpeg', '2025-06-23 18:54:15', 'Pizza Restoranları | Telifsiz Müzik', 'pizza restoranı müzikleri, telifsiz restoran playlist, işletme müzik yayını, yasal müzik yayını, pizza cafe müzikleri, modern restoran müzikleri, telifsiz italyan müzikleri, pizza salonu playlist, arka plan müziği telifsiz, işletmeler için müzik, restaurant background music, royalty-free pizza music', 'Pizza restoranınızın enerjisini Muzibu&#039;nun telifsiz ve yasal müzik playlistleriyle artırın. Samimi, modern ve keyifli bir atmosfer için ideal müzik çözümleriyle fark yaratın.'),
(27, 'Sushi Restoranı', 'sushi-restorani', 'SECTOR_GDGkND-Io2eTY-3fb6ZT-ny2CHL-GLi40K-qHTsqg.jpeg', '2025-06-24 16:16:04', 'Sushi Restoranları | Telifsiz Müzik | Muzibu', 'sushi restoranı müzikleri, telifsiz restoran playlist, japon restoran müziği, yasal müzik yayını, sakin yemek müzikleri, sushi lounge playlist, asian fusion müzikleri, işletme müzik çözümleri, japanese restaurant background music, royalty-free sushi music, chill dining playlist, sushi bar ambience, minimalist restaurant music', 'Sushi restoranınızın estetik ve sakin atmosferini Muzibu&#039;nun telifsiz müzik playlistleriyle tamamlayın. Yasal müzik yayınıyla misafirlerinize zarif bir deneyim sunun.'),
(28, 'Balık Restoranı', 'balik-restorani', 'SECTOR_4SyiOv-Mp4DQM-o4kggV-VlElDq-JmsXUa-BR7Rhc.jpeg', '2025-06-27 00:57:25', 'Balık Restoranları | Telifsiz Müzik', 'balık restoranı, deniz ürünleri restoranı, telifsiz müzik, restoran müziği telifsiz, işletme playlistleri, ambiyans müziği, deniz temalı müzikler, telifsiz playlist, balık restoranı müzikleri, restoran atmosfer müzikleri, Telifsiz müzikler', 'Balık restoranınızın atmosferini Muzibu&#039;nun telifsiz ve yasal müzik playlistleri ile güçlendirin. Deniz temalı ambiyans müzikleriyle misafirlerinize unutulmaz bir deneyim yaşatın.'),
(30, 'Rock Bar', 'rock-bar', 'SECTOR_Oi1kj0-IAHsZa-OHbIRW-PPyZob-BeM9ZG-Bnq5Iu.jpeg', '2025-07-31 18:56:38', 'Rock Barlar | Telifsiz Müzik', 'rock bar müzikleri, telifsiz rock playlist, bar müzik yayını, yasal müzik yayını, rock atmosfer müziği, işletme müzik çözümleri, bar için rock müzik, indie rock playlist, işletmeler için müzik, rock bar arka plan müziği, live bar music, royalty-free rock music, alternative bar playlist', 'Rock bar atmosferinizi Muzibu&#039;nun telifsiz ve yasal rock playlistleriyle güçlendirin. Müşterilerinize özgür, enerjik ve kaliteli bir müzik deneyimi sunun!'),
(31, 'Spa', 'spa', 'SECTOR_sOfAje-XzNWgr-tPLHnh-o8tVNX-Pi5xva-8fjlv4.jpg', '2025-10-03 14:57:29', 'Spa Music', 'telifsiz spa müzikleri, telifsiz müzik, işletme müzikleri', 'Spalar için telifsiz müzikler, playlistler ve radyolar'),
(32, 'Osmanlı Mutfağı', 'osmanli-mutfagi', 'SECTOR_2XFHXL-hV4Ogn-uyK1bh-kyvWwY-YCPVy3-SXniAP.jpg', '2025-10-26 12:12:18', '', '', '');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `muzibu_sectors`
--
ALTER TABLE `muzibu_sectors`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_sectors`
--
ALTER TABLE `muzibu_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
