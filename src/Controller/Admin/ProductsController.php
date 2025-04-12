<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index.admin')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }
    #[Route('/ajout', name: 'add.admin')]
    public function add(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/products/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }
    #[Route('/edition/{id}', name: 'edit.admin')]
    public function edit(Products $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        return $this->render('admin/products/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }
    #[Route('/suppression/{id}', name: 'delete.admin')]
    public function delete(Products $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }
}