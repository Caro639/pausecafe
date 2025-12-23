<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categories', name: 'categories_')]
final class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    /**
     * Summary of list
     * @param string $slug
     * @param \App\Repository\CategoriesRepository $repository
     * @param \App\Repository\ProductsRepository $productsRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
     */
    public function list(
        string $slug,
        CategoriesRepository $repository,
        ProductsRepository $productsRepository,
        Request $request
    ): Response {
        $category = $repository->findOneBy(['slug' => $slug]);

        if (!$category instanceof \App\Entity\Categories) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        $page = $request->query->getInt('page', 1);

        // $products = $category->getProducts();

        $products = $productsRepository->findProductsPaginated($page, $category->getSlug(), 1);

        return $this->render('categories/list.html.twig', ['category' => $category, 'products' => $products]);
    }
}
