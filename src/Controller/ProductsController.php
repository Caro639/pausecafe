<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/produits', name: 'products_')]
final class ProductsController extends AbstractController
{
    #[Route('/', name: 'index.products')]
    /**
     * affiche les produits
     * @param \App\Repository\CategoriesRepository $categoriesRepository
     * @return Response
     */
    public function index(CategoriesRepository $categoriesRepository): Response
    {

        return $this->render('products/index.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['categoryOrder' => 'ASC']),
        ]);
    }


    #[Route('/{slug}', name: 'details')]
    /**
     * product details
     * @param mixed $slug
     * @param \App\Repository\ProductsRepository $repository
     * @return Response
     */
    public function details($slug, ProductsRepository $repository): Response
    {
        // dd($request->attributes);
        $product = $repository->findOneBy(['slug' => $slug]);

        if (!$product instanceof \App\Entity\Products) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }
        // dd($product->getDescription());
        return $this->render('products/details.html.twig', [
            'product' => $product,
        ]);
    }
}
