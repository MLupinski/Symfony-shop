<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'admin_products')]
    public function index(Request $request, ProductRepository $productRepository, PaginatorInterface $paginator): Response
    {
        $sortParam = $request->query->get('sort', 'p.id_asc');
        [$sortField, $sortDirection] = explode('_', $sortParam);
        $queryBuilder = $productRepository->paginationQueryBuilder($sortField, $sortDirection);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('admin/product/list.html.twig', [
            'pagination' => $pagination,
            'sort' => $sortParam
        ]);
    }

    #[Route('admin/product/new', name: 'admin_product_new')]
    public function create(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nowDate = new \DateTimeImmutable();
            $product->setCreatedAt($nowDate);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product added successfully!');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('/admin/product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'admin_product_edit')]
    public function edit(Request $request, Product $product, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Product updated successfully!');

            return $this->redirectToRoute('admin_products');
        }

        return $this->render(
            'admin/product/edit.html.twig',
            [
                'product' => $product,
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/admin/product/{id}/delete', name: 'admin_product_delete')]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully!');
        }

        return $this->redirectToRoute('admin_products');
    }
}
