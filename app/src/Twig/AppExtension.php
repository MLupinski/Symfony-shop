<?php

namespace App\Twig;

use App\Service\CartService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{

    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    /**
     * @return array
     */
    public function getGlobals(): array
    {
        return [
            'cartItems' => $this->cartService->getCartItems()
        ];
    }
}