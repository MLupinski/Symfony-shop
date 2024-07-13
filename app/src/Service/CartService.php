<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * @param Product $product
     * @param int     $quantity
     *
     * @return void
     */
    public function addProduct(Product $product, int $quantity): void
    {
        $cart = $this->getCart();
        $productId = $product->getId();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    /**
     * @return array
     */
    public function getCart(): array
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    /**
     * @return void
     */
    public function clearCart(): void
    {
        $this->requestStack->getSession()->remove('cart');
    }

    /**
     * @return int
     */
    public function getCartItems(): int
    {
        return count($this->getCart($this->requestStack->getSession()));
    }
}