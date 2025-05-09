<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/produits', name: 'products_')]
final class ProductsController extends AbstractController
{
    #[Route('/', name: 'index.products')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {

        return $this->render('products/index.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['categoryOrder' => 'ASC']),
        ]);
    }


    #[Route('/{slug}', name: 'details')]
    public function details($slug, Products $product, ProductsRepository $repository, Request $request): Response
    {
        // dd($request->attributes);
        $product = $repository->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }
        // dd($product->getDescription());
        return $this->render('products/details.html.twig', [
            'product' => $product,
        ]);
    }
}
