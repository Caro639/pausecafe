<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Repository\ProductsRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/produits', name: 'admin_product_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index.admin')]
    public function index(ProductsRepository $productsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PRODUCT_ADMIN');

        $produits = $productsRepository->findAll();
        return $this->render('admin/products/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/ajout', name: 'add.admin')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        PictureService $pictureService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_PRODUCT_ADMIN');

        $product = new Products();
        $productForm = $this->createForm(ProductsFormType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {

            $images = $productForm->get('images')->getData();

            // dd($images);
            foreach ($images as $image) {
                $folder = 'products';

                $fichier = $pictureService->upload($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }

            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $prix = $product->getPrice();
            $product->setPrice($prix);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté !');
            return $this->redirectToRoute('admin_product_index.admin');
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
        SluggerInterface $slugger,
        PictureService $pictureService
    ): Response {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        $productForm = $this->createForm(ProductsFormType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {

            $images = $productForm->get('images')->getData();

            foreach ($images as $image) {
                $folder = 'products';

                $fichier = $pictureService->upload($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }

            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $prix = $product->getPrice();
            $product->setPrice($prix);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été modifié !');
            return $this->redirectToRoute('admin_product_index.admin');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView(),
            'product' => $product,
        ]);
    }
    #[Route('/suppression/{id}', name: 'delete.admin')]
    public function delete(Products $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'Le produit a bien été supprimé !');
        return $this->redirectToRoute('admin_product_index.admin');
        // return $this->render('admin/products/index.html.twig', [
        //     'controller_name' => 'UsersController',
        // ]);
    }
    #[Route('/suppression/image/{id}', name: 'delete.image', methods: ['DELETE'])]
    public function deleteImage(
        Images $image,
        Request $request,
        EntityManagerInterface $em,
        PictureService $pictureService
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $nom = $image->getName();

            if ($pictureService->delete($nom, 'products', 300, 300)) {
                $em->remove($image);
                $em->flush();

                return new JsonResponse(['success' => true], 200);
            }
            return new JsonResponse(['error' => 'erreur de suppression'], 400);
        }
        return new JsonResponse(['error' => 'erreur de token'], 400);
    }
}