<?php

if (!function_exists('turkish_title_case')) {
    /**
     * Türkçe imla kurallarına uygun başlık formatı
     * Her kelimenin ilk harfi büyük, geri kalanı küçük (Türkçe karakter desteği ile)
     *
     * @param string $text
     * @return string
     */
    function turkish_title_case(string $text): string
    {
        // Alt tire ve tire karakterlerini boşluğa çevir
        $text = str_replace(['_', '-'], ' ', $text);

        // Birden fazla boşluğu tek boşluğa indir
        $text = preg_replace('/\s+/', ' ', $text);

        // Trim
        $text = trim($text);

        // Türkçe karakterler için mapping
        $lowerMap = [
            'İ' => 'i', 'I' => 'ı', 'Ş' => 'ş', 'Ğ' => 'ğ',
            'Ü' => 'ü', 'Ö' => 'ö', 'Ç' => 'ç'
        ];

        $upperMap = [
            'i' => 'İ', 'ı' => 'I', 'ş' => 'Ş', 'ğ' => 'Ğ',
            'ü' => 'Ü', 'ö' => 'Ö', 'ç' => 'Ç'
        ];

        // Türkçe karakterleri önce map ile küçült (mb_strtolower sorunlarını önlemek için)
        $text = strtr($text, $lowerMap);

        // Sonra tüm metni küçük harfe çevir (Türkçe destekli)
        $text = mb_strtolower($text, 'UTF-8');

        // Her kelimenin ilk harfini büyüt
        $words = explode(' ', $text);
        $result = [];

        foreach ($words as $word) {
            if (empty($word)) {
                continue;
            }

            // İlk karakteri al
            $firstChar = mb_substr($word, 0, 1, 'UTF-8');
            $rest = mb_substr($word, 1, null, 'UTF-8');

            // İlk karakteri büyüt (Türkçe destekli)
            if (isset($upperMap[$firstChar])) {
                $firstChar = $upperMap[$firstChar];
            } else {
                $firstChar = mb_strtoupper($firstChar, 'UTF-8');
            }

            $result[] = $firstChar . $rest;
        }

        return implode(' ', $result);
    }
}

if (!function_exists('is_favorited')) {
    /**
     * 🔥 P1 FIX: Check if item is favorited using pre-loaded data
     * Uses $userFavoritedIds from SidebarComposer (bulk loaded)
     * Reduces N+1 problem (174 queries → 0 queries during render)
     *
     * @param string $type - 'song', 'album', 'playlist', 'genre', 'sector', 'radio', 'artist'
     * @param int $id - Entity ID
     * @return bool
     */
    function is_favorited(string $type, int $id): bool
    {
        // Get pre-loaded favorites from view
        $userFavoritedIds = view()->shared('userFavoritedIds', []);

        // If not available in shared data, return false (guest user or not loaded yet)
        if (empty($userFavoritedIds)) {
            return false;
        }

        // Check if ID exists in the type array
        return isset($userFavoritedIds[$type]) && in_array($id, $userFavoritedIds[$type], true);
    }
}

if (!function_exists('clean_filename_for_title')) {
    /**
     * Dosya adından başlık oluştur (Türkçe imla kurallarına uygun)
     * Uzantıyı kaldırır, alt tire/tire yerine boşluk ekler, Türkçe başlık formatına çevirir
     *
     * @param string $filename
     * @return string
     */
    function clean_filename_for_title(string $filename): string
    {
        // Uzantıyı kaldır
        $title = pathinfo($filename, PATHINFO_FILENAME);

        // Türkçe başlık formatına çevir
        return turkish_title_case($title);
    }
}
