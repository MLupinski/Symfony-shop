<?php

namespace App\Controller;

use App\Form\AddToCartType;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Knp\Menu\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    #[Route('/product', name: 'product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $date = new \DateTimeImmutable();
        $products =  $productRepository->findBetweenDates($date->modify('-14 days'), $date);

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{slug}', name: 'product_show')]
    public function show(Request $request, ProductRepository $productRepository, FactoryInterface $factory): Response
    {
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = $form->get('quantity')->getData();
            $product = $productRepository->findOneBy(['id' => $request->get('productId')]);
            $session = new Session();
            $this->cartService->addProduct($product, $quantity, $session);
            $this->addFlash('success', 'Product added to cart');

            return $this->redirectToRoute('product_show', ['slug' => $product->getSlug()]);
        }

        $slug = $request->get('slug');
        $product = $productRepository->findOneBy(['slug' => $slug]);

        $breadcrumbs = $factory->createItem('root');
        $breadcrumbs->addChild('Home', ['route' => 'product_index']);
        $breadcrumbs->addChild('Products', ['route' => 'product_index']);
        $breadcrumbs->addChild($product->getName())->setCurrent(true);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'breadcrumbs' => $breadcrumbs,
            'addToCartForm' => $form->createView(),
        ]);
    }
}
