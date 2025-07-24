<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use HasFactory, SoftDeletes, Sluggable, SluggableScopeHelpers;

    /**
     * Varsayılan $fillable Alanları
     */
    protected $fillable = ['title', 'slug', 'is_active'];

    /**
     * Varsayılan Değerler
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Sluggable Ayarları
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'   => 'title',
                'onUpdate' => true, // Slug güncellemeyi destekler
                'method'   => 'customSlugMethod', // Custom Türkçe slug metodu
                'separator' => '-',
                'unique' => true,
                'includeTrashed' => false,
            ],
        ];
    }

    /**
     * Custom Türkçe Slug Üretimi
     */
    public function customSlugMethod($string, $separator = '-')
    {
        // Türkçe karakter dönüşüm tablosu
        $turkishChars = [
            'Ç' => 'C', 'ç' => 'c',
            'Ğ' => 'G', 'ğ' => 'g', 
            'I' => 'I', 'ı' => 'i',
            'İ' => 'I', 'i' => 'i',
            'Ö' => 'O', 'ö' => 'o',
            'Ş' => 'S', 'ş' => 's',
            'Ü' => 'U', 'ü' => 'u'
        ];
        
        // Türkçe karakterleri çevir
        $string = strtr($string, $turkishChars);
        
        // Küçük harfe çevir
        $string = strtolower($string);
        
        // Özel karakterleri temizle (sadece harf, rakam ve boşluk kalsın)
        $string = preg_replace('/[^a-z0-9\s\-]/', '', $string);
        
        // Birden fazla boşluk/tire varsa tek tire yap
        $string = preg_replace('/[\s\-]+/', $separator, $string);
        
        // Başındaki ve sonundaki tireleri kaldır
        $string = trim($string, $separator);
        
        return $string;
    }

    /**
     * Boot Method for Custom Slugging Logic
     */
    protected static function boot()
    {
        parent::boot();

        // Kaydedilmeden önce benzersizlik kontrolü
        static::saving(function ($model) {
            $model->ensureUniqueSlug();
        });
    }

    /**
     * Benzersiz Slug Üretimi
     */
    protected function ensureUniqueSlug()
    {
        // HasTranslations trait'i varsa JSON slug kontrolü
        if (method_exists($this, 'isTranslatable') && $this->isTranslatable('slug')) {
            // JSON slug sistemi için özel kontrol (şimdilik skip)
            return;
        }
        
        // Normal string slug kontrolü
        if (!empty($this->slug) && is_string($this->slug)) {
            $originalSlug = $this->slug;
            $suffix = 1;

            while ($this->slugExists($this->slug)) {
                $this->slug = "{$originalSlug}-{$suffix}";
                $suffix++;
            }
        }
    }

    /**
     * Slug'un Benzersiz Olup Olmadığını Kontrol Eder
     *
     * @param string $slug
     * @return bool
     */
    protected function slugExists(string $slug): bool
    {
        return self::where('slug', $slug)
            ->where($this->getKeyName(), '!=', $this->getKey())
            ->exists();
    }
}
