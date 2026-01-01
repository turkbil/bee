<?php

namespace Modules\Muzibu\App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Certificate extends Model
{
    protected $table = 'certificates';

    /**
     * Tenant connection
     */
    public function getConnectionName()
    {
        return 'tenant';
    }

    protected $fillable = [
        'user_id',
        'certificate_code',
        'qr_hash',
        'member_name',
        'tax_office',
        'tax_number',
        'address',
        'membership_start',
        'view_count',
        'issued_at',
        'is_valid',
    ];

    protected $casts = [
        'membership_start' => 'date',
        'issued_at' => 'datetime',
        'is_valid' => 'boolean',
        'view_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User relationship (Tenant DB)
     * Note: User::on('tenant') kullanılmalı, relationship tenant context'te çalışır
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Get user from tenant DB explicitly
     */
    public function getTenantUser()
    {
        return User::on('tenant')->find($this->user_id);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Generate certificate code
     */
    public static function generateCode(): string
    {
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        return "MZB-{$year}-{$random}";
    }

    /**
     * Generate QR hash
     */
    public static function generateHash(): string
    {
        return hash('sha256', uniqid('cert_', true) . time() . random_bytes(16));
    }

    /**
     * Get verification URL
     */
    public function getVerificationUrl(): string
    {
        // Tenant'ın birincil domain'ini kullan
        $domain = tenant()->domains->first()?->domain ?? 'muzibu.com.tr';
        return 'https://' . $domain . '/muzibu/certificate/' . $this->qr_hash;
    }

    /**
     * Apply Turkish title case spelling correction
     * İlk harf büyük, devamı küçük
     * Nokta (.) ve iki nokta (:) ve slash (/) sonrası büyük harf
     * Slash etrafındaki boşluklar temizlenir
     */
    public static function correctSpelling(string $text): string
    {
        // Slash etrafindaki bosluklari temizle (  /  -> /)
        $text = preg_replace('/\s*\/\s*/', '/', $text);

        // Title case uygula - karakter karakter isle
        $result = '';
        $capitalizeNext = true;

        for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');

            // Bosluk, yeni satir, nokta, iki nokta, slash sonrasi buyuk harf
            if ($char === ' ' || $char === "\n" || $char === "\r") {
                $result .= $char;
                $capitalizeNext = true;
            } elseif ($char === '.' || $char === ':' || $char === '/') {
                $result .= $char;
                $capitalizeNext = true;
            } elseif ($capitalizeNext) {
                $result .= self::toUpperTR($char);
                $capitalizeNext = false;
            } else {
                $result .= self::toLowerTR($char);
            }
        }

        return $result;
    }

    private static function toUpperTR(string $char): string
    {
        if ($char === 'i') return 'İ';
        if ($char === 'ı') return 'I';
        return mb_strtoupper($char, 'UTF-8');
    }

    private static function toLowerTR(string $char): string
    {
        if ($char === 'I') return 'ı';
        if ($char === 'İ') return 'i';
        return mb_strtolower($char, 'UTF-8');
    }

    /**
     * Scope: Only valid certificates
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope: By user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
