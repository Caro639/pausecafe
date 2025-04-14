<?php

namespace App\Controller\Admin;

use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/modif/categories', name: 'admin_modif_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index.category')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'ASC']);

        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
