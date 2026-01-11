<?php

namespace Modules\Muzibu\App\Observers;

/**
 * Müzik Verisi Observer
 *
 * Song, Album, Artist, Playlist, Radio, Genre, Sector modellerine eklenecek
 * Her kayıt saving event'inde otomatik UTF-8 temizleme yapar
 *
 * ✅ 30 bin şarkı eklense bile sorun çıkarmaz!
 */
class MuzikDataObserver
{
    /**
     * Saving event - kaydetmeden önce UTF-8 temizle
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function saving($model)
    {
        // JSON field'ları temizle (title, slug, description)
        $jsonFields = ['title', 'slug', 'description'];

        foreach ($jsonFields as $field) {
            if (isset($model->$field) && !empty($model->$field)) {
                $model->$field = $this->cleanJsonField($model->$field);
            }
        }
    }

    /**
     * JSON field temizleme
     *
     * @param string|array $jsonData
     * @return string
     */
    protected function cleanJsonField($jsonData)
    {
        // Eğer zaten array ise
        if (is_array($jsonData)) {
            $data = $jsonData;
        } else {
            // String ise JSON decode et
            $data = json_decode($jsonData, true);

            if (!is_array($data)) {
                // JSON decode başarısız, string olarak temizle
                return $this->cleanUtf8($jsonData);
            }
        }

        // Array içindeki her string'i temizle
        array_walk_recursive($data, function(&$value) {
            if (is_string($value)) {
                $value = $this->cleanUtf8($value);
            }
        });

        // JSON encode et
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * UTF-8 temizleme
     *
     * @param string $string
     * @return string
     */
    protected function cleanUtf8($string)
    {
        if (empty($string)) {
            return $string;
        }

        // Remove NULL bytes
        $string = str_replace("\0", '', $string);

        // Use iconv for aggressive cleaning
        $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $string);

        if ($cleaned === false) {
            $cleaned = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        }

        // Remove control characters
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F\xAD]/u', '', $cleaned);

        // Final check
        if (!mb_check_encoding($cleaned, 'UTF-8')) {
            $cleaned = mb_convert_encoding($cleaned, 'UTF-8', 'UTF-8');
        }

        return $cleaned;
    }
}
