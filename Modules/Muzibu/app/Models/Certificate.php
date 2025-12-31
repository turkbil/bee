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
     * User relationship (Central DB)
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
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
        return url('/muzibu/certificate/' . $this->qr_hash);
    }

    /**
     * Apply Turkish title case spelling correction
     * İlk harf büyük, devamı küçük
     */
    public static function correctSpelling(string $text): string
    {
        // Türkçe karakterleri destekleyen mb_convert_case kullan
        return mb_convert_case(mb_strtolower($text, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
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
