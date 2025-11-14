<?php

declare(strict_types=1);

namespace Modules\Cart\App\Traits;

/**
 * Has Addresses Trait
 *
 * Provides basic address management for Cart model
 * Stores addresses as JSON in cart table (simple approach)
 */
trait HasAddresses
{
    /**
     * Get billing address
     *
     * @return array|null
     */
    public function getBillingAddress(): ?array
    {
        return $this->billing_address ?? null;
    }

    /**
     * Get shipping address
     *
     * @return array|null
     */
    public function getShippingAddress(): ?array
    {
        return $this->shipping_address ?? null;
    }

    /**
     * Set billing address
     *
     * @param array $address
     * @return void
     */
    public function setBillingAddress(array $address): void
    {
        $this->billing_address = $address;
        $this->save();
    }

    /**
     * Set shipping address
     *
     * @param array $address
     * @return void
     */
    public function setShippingAddress(array $address): void
    {
        $this->shipping_address = $address;
        $this->save();
    }

    /**
     * Check if has valid billing address
     *
     * @return bool
     */
    public function hasBillingAddress(): bool
    {
        $address = $this->billing_address;

        if (!$address || !is_array($address)) {
            return false;
        }

        // Minimum required fields
        $requiredFields = ['address_line_1', 'city', 'postal_code'];

        foreach ($requiredFields as $field) {
            if (empty($address[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if has valid shipping address
     *
     * @return bool
     */
    public function hasShippingAddress(): bool
    {
        $address = $this->shipping_address;

        if (!$address || !is_array($address)) {
            return false;
        }

        // Minimum required fields
        $requiredFields = ['address_line_1', 'city', 'postal_code'];

        foreach ($requiredFields as $field) {
            if (empty($address[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Copy shipping address to billing
     *
     * @return void
     */
    public function copyShippingToBilling(): void
    {
        if ($this->hasShippingAddress()) {
            $this->setBillingAddress($this->getShippingAddress());
        }
    }

    /**
     * Copy billing address to shipping
     *
     * @return void
     */
    public function copyBillingToShipping(): void
    {
        if ($this->hasBillingAddress()) {
            $this->setShippingAddress($this->getBillingAddress());
        }
    }
}
