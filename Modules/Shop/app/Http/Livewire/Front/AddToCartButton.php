<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Front;

use Livewire\Component;
use Modules\Shop\App\Services\ShopCartService;

class AddToCartButton extends Component
{
    public int $productId;
    public ?int $variantId = null;
    public int $quantity = 1;
    public string $buttonText = 'Sepete Ekle';
    public string $buttonClass = 'btn btn-primary';
    public bool $showQuantity = false;
    public bool $isAdding = false;

    protected $rules = [
        'quantity' => 'required|integer|min:1',
    ];

    public function mount(
        int $productId,
        ?int $variantId = null,
        int $quantity = 1,
        string $buttonText = 'Sepete Ekle',
        string $buttonClass = 'btn btn-primary',
        bool $showQuantity = false
    ) {
        $this->productId = $productId;
        $this->variantId = $variantId;
        $this->quantity = $quantity;
        $this->buttonText = $buttonText;
        $this->buttonClass = $buttonClass;
        $this->showQuantity = $showQuantity;
    }

    public function addToCart()
    {
        $this->validate();

        $this->isAdding = true;

        try {
            $cartService = app(ShopCartService::class);

            $cartService->addItem(
                $this->productId,
                $this->quantity,
                $this->variantId
            );

            $this->dispatch('cartUpdated');

            $this->dispatch('product-added-to-cart', [
                'message' => 'Ürün sepete eklendi!',
                'productId' => $this->productId,
            ]);

            // Quantity'yi reset et
            if (!$this->showQuantity) {
                $this->quantity = 1;
            }
        } catch (\Exception $e) {
            $this->dispatch('cart-error', [
                'message' => 'Hata: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isAdding = false;
        }
    }

    public function increaseQuantity()
    {
        $this->quantity++;
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        return view('shop::livewire.front.add-to-cart-button');
    }
}
