<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Entity\Categories;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categories', name: 'categories_')]
final class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(string $slug, CategoriesRepository $repository): Response
    {
        $category = $repository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        $products = $category->getProducts();

        // dd($products);
        return $this->render('categories/list.html.twig', compact('category', 'products'));
    }
}
