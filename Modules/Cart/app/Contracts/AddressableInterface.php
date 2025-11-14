<?php

declare(strict_types=1);

namespace Modules\Cart\App\Contracts;

/**
 * Addressable Interface
 *
 * Universal interface for managing customer addresses
 * Any module can implement this to provide address functionality
 */
interface AddressableInterface
{
    /**
     * Get billing address
     *
     * @return array|null
     */
    public function getBillingAddress(): ?array;

    /**
     * Get shipping address
     *
     * @return array|null
     */
    public function getShippingAddress(): ?array;

    /**
     * Set billing address
     *
     * @param array $address
     * @return void
     */
    public function setBillingAddress(array $address): void;

    /**
     * Set shipping address
     *
     * @param array $address
     * @return void
     */
    public function setShippingAddress(array $address): void;

    /**
     * Check if has valid billing address
     *
     * @return bool
     */
    public function hasBillingAddress(): bool;

    /**
     * Check if has valid shipping address
     *
     * @return bool
     */
    public function hasShippingAddress(): bool;
}
