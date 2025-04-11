<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProductsRepository $repository, Products $product): Response
    {

        return $this->render(
            'home/index.html.twig',
            [
                'products' => $repository->findProduct(8),
                'product' => $product,
                'slug' => $product->getSlug(),
            ]
        );
    }
}
