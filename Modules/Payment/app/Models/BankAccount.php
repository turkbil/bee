<?php

namespace Modules\Payment\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bank_accounts';
    protected $primaryKey = 'bank_account_id';

    protected $fillable = [
        'bank_name',
        'branch_name',
        'branch_code',
        'account_holder_name',
        'account_number',
        'iban',
        'swift_code',
        'currency',
        'is_active',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scopes
     */

    // Sadece aktif hesapları getir
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Para birimine göre filtrele
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    // Sıralı getir
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('bank_name');
    }

    /**
     * Accessors
     */

    // Formatlanmış IBAN (TR12 3456 7890 1234 5678 9012 34)
    public function getFormattedIbanAttribute(): string
    {
        $iban = strtoupper($this->iban);
        return wordwrap($iban, 4, ' ', true);
    }

    // Formatlanmış hesap numarası (1234-5678-9012-3456)
    public function getFormattedAccountNumberAttribute(): string
    {
        if (!$this->account_number) {
            return '';
        }

        $number = preg_replace('/\D/', '', $this->account_number);
        return wordwrap($number, 4, '-', true);
    }

    // Para birimi sembolü
    public function getCurrencySymbolAttribute(): string
    {
        $symbols = [
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'RUB' => '₽',
        ];

        return $symbols[$this->currency] ?? $this->currency;
    }

    // Tam banka bilgisi (Ziraat Bankası - Kadıköy Şubesi)
    public function getFullBankNameAttribute(): string
    {
        $parts = [$this->bank_name];

        if ($this->branch_name) {
            $parts[] = $this->branch_name;
        }

        if ($this->branch_code) {
            $parts[] = "({$this->branch_code})";
        }

        return implode(' - ', $parts);
    }

    /**
     * Helper Methods
     */

    // Kopyala/Yapıştır için düz text bilgi
    public function getPlainTextInfo(): string
    {
        $info = [];
        $info[] = "Banka: {$this->bank_name}";

        if ($this->branch_name) {
            $info[] = "Şube: {$this->branch_name}";
        }

        $info[] = "Hesap Sahibi: {$this->account_holder_name}";
        $info[] = "IBAN: {$this->iban}";

        if ($this->account_number) {
            $info[] = "Hesap No: {$this->account_number}";
        }

        if ($this->swift_code) {
            $info[] = "SWIFT: {$this->swift_code}";
        }

        $info[] = "Para Birimi: {$this->currency}";

        return implode("\n", $info);
    }

    // IBAN geçerlilik kontrolü
    public static function validateIban(string $iban): bool
    {
        // Boşlukları temizle
        $iban = strtoupper(preg_replace('/\s+/', '', $iban));

        // Uzunluk kontrolü (TR için 26, max 34)
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        // TR IBAN kontrolü
        if (str_starts_with($iban, 'TR')) {
            return strlen($iban) === 26;
        }

        // Genel IBAN algoritması (mod 97)
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        $numericIban = '';

        for ($i = 0; $i < strlen($rearranged); $i++) {
            $char = $rearranged[$i];
            if (ctype_alpha($char)) {
                $numericIban .= (ord($char) - 55);
            } else {
                $numericIban .= $char;
            }
        }

        return bcmod($numericIban, '97') === '1';
    }
}
