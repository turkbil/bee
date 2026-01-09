-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 08 Oca 2026, 17:20:55
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
-- Veritabanı: `muzibu_mayis25`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_albums`
--

CREATE TABLE `muzibu_albums` (
  `id` int(11) NOT NULL,
  `title_tr` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `description_tr` text DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `meta_title` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_artists`
--

CREATE TABLE `muzibu_artists` (
  `id` int(11) NOT NULL,
  `title_tr` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `bio_tr` text DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `meta_title` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_favorites`
--

CREATE TABLE `muzibu_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_genres`
--

CREATE TABLE `muzibu_genres` (
  `id` int(11) NOT NULL,
  `title_tr` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description_tr` text NOT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `meta_title` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_moods`
--

CREATE TABLE `muzibu_moods` (
  `id` int(11) NOT NULL,
  `title_tr` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `meta_title` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_playlists`
--

CREATE TABLE `muzibu_playlists` (
  `id` int(11) NOT NULL,
  `title_tr` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `system` tinyint(1) NOT NULL DEFAULT 0,
  `description_tr` text DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `meta_title` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `radio` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_playlist_favorites`
--

CREATE TABLE `muzibu_playlist_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_playlist_radio`
--

CREATE TABLE `muzibu_playlist_radio` (
  `playlist_id` int(11) NOT NULL,
  `radio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_playlist_sector`
--

CREATE TABLE `muzibu_playlist_sector` (
  `playlist_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_playlist_song`
--

CREATE TABLE `muzibu_playlist_song` (
  `playlist_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_radios`
--

CREATE TABLE `muzibu_radios` (
  `id` int(11) NOT NULL,
  `title_tr` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `meta_title` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_radio_sector`
--

CREATE TABLE `muzibu_radio_sector` (
  `radio_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_songs`
--

CREATE TABLE `muzibu_songs` (
  `id` int(11) NOT NULL,
  `title_tr` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `album_id` int(11) DEFAULT NULL,
  `genre_id` int(11) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in seconds',
  `file_path` varchar(255) DEFAULT NULL,
  `lyrics_tr` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `play_count` int(11) DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `meta_title` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `meta_description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_song_mood`
--

CREATE TABLE `muzibu_song_mood` (
  `song_id` int(11) NOT NULL,
  `mood_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzibu_song_plays`
--

CREATE TABLE `muzibu_song_plays` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `song_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `muzibu_albums`
--
ALTER TABLE `muzibu_albums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Tablo için indeksler `muzibu_artists`
--
ALTER TABLE `muzibu_artists`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `muzibu_favorites`
--
ALTER TABLE `muzibu_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_song` (`user_id`,`song_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Tablo için indeksler `muzibu_genres`
--
ALTER TABLE `muzibu_genres`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `muzibu_moods`
--
ALTER TABLE `muzibu_moods`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `muzibu_playlists`
--
ALTER TABLE `muzibu_playlists`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `muzibu_playlist_favorites`
--
ALTER TABLE `muzibu_playlist_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_playlist` (`user_id`,`playlist_id`) USING BTREE,
  ADD KEY `playlist_id` (`playlist_id`) USING BTREE;

--
-- Tablo için indeksler `muzibu_playlist_radio`
--
ALTER TABLE `muzibu_playlist_radio`
  ADD PRIMARY KEY (`playlist_id`,`radio_id`),
  ADD KEY `sector_id` (`radio_id`);

--
-- Tablo için indeksler `muzibu_playlist_sector`
--
ALTER TABLE `muzibu_playlist_sector`
  ADD PRIMARY KEY (`playlist_id`,`sector_id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Tablo için indeksler `muzibu_playlist_song`
--
ALTER TABLE `muzibu_playlist_song`
  ADD PRIMARY KEY (`playlist_id`,`song_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Tablo için indeksler `muzibu_radios`
--
ALTER TABLE `muzibu_radios`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `muzibu_radio_sector`
--
ALTER TABLE `muzibu_radio_sector`
  ADD PRIMARY KEY (`radio_id`,`sector_id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Tablo için indeksler `muzibu_sectors`
--
ALTER TABLE `muzibu_sectors`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `muzibu_songs`
--
ALTER TABLE `muzibu_songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Tablo için indeksler `muzibu_song_mood`
--
ALTER TABLE `muzibu_song_mood`
  ADD PRIMARY KEY (`song_id`,`mood_id`) USING BTREE,
  ADD KEY `sector_id` (`mood_id`);

--
-- Tablo için indeksler `muzibu_song_plays`
--
ALTER TABLE `muzibu_song_plays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `song_id` (`song_id`),
  ADD KEY `idx_song_plays_songid_created` (`song_id`,`created`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_albums`
--
ALTER TABLE `muzibu_albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_artists`
--
ALTER TABLE `muzibu_artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_favorites`
--
ALTER TABLE `muzibu_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_genres`
--
ALTER TABLE `muzibu_genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_moods`
--
ALTER TABLE `muzibu_moods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_playlists`
--
ALTER TABLE `muzibu_playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_playlist_favorites`
--
ALTER TABLE `muzibu_playlist_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_radios`
--
ALTER TABLE `muzibu_radios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_sectors`
--
ALTER TABLE `muzibu_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_songs`
--
ALTER TABLE `muzibu_songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `muzibu_song_plays`
--
ALTER TABLE `muzibu_song_plays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `muzibu_albums`
--
ALTER TABLE `muzibu_albums`
  ADD CONSTRAINT `muzibu_albums_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `muzibu_artists` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `muzibu_favorites`
--
ALTER TABLE `muzibu_favorites`
  ADD CONSTRAINT `muzibu_favorites_ibfk_1` FOREIGN KEY (`song_id`) REFERENCES `muzibu_songs` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `muzibu_playlist_sector`
--
ALTER TABLE `muzibu_playlist_sector`
  ADD CONSTRAINT `muzibu_playlist_sector_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `muzibu_playlists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `muzibu_playlist_sector_ibfk_2` FOREIGN KEY (`sector_id`) REFERENCES `muzibu_sectors` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `muzibu_playlist_song`
--
ALTER TABLE `muzibu_playlist_song`
  ADD CONSTRAINT `muzibu_playlist_song_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `muzibu_playlists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `muzibu_playlist_song_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `muzibu_songs` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `muzibu_songs`
--
ALTER TABLE `muzibu_songs`
  ADD CONSTRAINT `muzibu_songs_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `muzibu_albums` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `muzibu_songs_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `muzibu_genres` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `muzibu_song_plays`
--
ALTER TABLE `muzibu_song_plays`
  ADD CONSTRAINT `muzibu_song_plays_ibfk_1` FOREIGN KEY (`song_id`) REFERENCES `muzibu_songs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
