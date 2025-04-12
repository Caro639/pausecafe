<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function add(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Products();
        $productForm = $this->createForm(ProductsFormType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {

            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $prix = $product->getPrice();
            $product->setPrice($prix);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté !');
            return $this->redirectToRoute('admin_products_index.admin');
        }

        return $this->render('admin/products/add.html.twig', [
            'productForm' => $productForm->createView(),
        ]);
    }
    #[Route('/edition/{id}', name: 'edit.admin')]
    public function edit(
        Products $product,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        $prix = $product->getPrice() / 100;
        $product->setPrice($prix);

        $productForm = $this->createForm(ProductsFormType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {

            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été modifié !');
            return $this->redirectToRoute('admin_products_index.admin');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView(),
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