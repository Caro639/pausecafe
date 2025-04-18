<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart', name: 'cart_')]
final class CartController extends AbstractController
{
    /**
     * affiche le panier
     *
     * @param SessionInterface $session
     * @param ProductsRepository $productsRepository
     * @param CartService $cartService
     * @return Response
     */
    #[Route('/', name: 'index.cart')]
    public function index(
        SessionInterface $session,
        ProductsRepository $productsRepository,
        CartService $cartService
    ): Response {

        $data = $cartService->getCart($session, $productsRepository)['data'];
        $total = $cartService->getCart($session, $productsRepository)['total'];


        return $this->render('cart/index.html.twig', [
            'data' => $data,
            'total' => $total,
        ]);
    }

    /**
     * ajoute un produit au panier
     */
    #[Route('/add/{id}', name: 'add.cart', condition: "params['id']")]
    public function add(
        Products $product,
        CartService $cartService,
        SessionInterface $session,
    ): Response {
        $id = $product->getId();

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id introuvable !");
        }

        $cartService->add($product, $session);

        $this->addFlash('success', 'Le produit a bien été ajouté au panier !');

        return $this->redirectToRoute('products_details', ['slug' => $product->getSlug()]);
    }

    /**
     * retire un produit du panier
     */
    #[Route('/remove/{id}', name: 'remove.cart')]
    public function remove(
        Products $product,
        SessionInterface $session,
        CartService $cartService
    ): Response {

        $product->getId();

        $cartService->remove($product, $session);
        $this->addFlash('success', 'Le produit a bien été retiré du panier !');

        return $this->redirectToRoute('cart_index.cart');
    }

    /**
     * supprime un produit du panier
     */
    #[Route('/delete/{id}', name: 'delete.cart')]
    public function delete(
        Products $product,
        SessionInterface $session,
        CartService $cartService
    ): Response {

        $product->getId();

        $cartService->delete($product, $session);
        $this->addFlash('success', 'Le produit a bien été supprimé du panier !');

        return $this->redirectToRoute('cart_index.cart');
    }

    /**
     * vide le panier
     */
    #[Route('/empty', name: 'empty.cart')]
    public function empty(SessionInterface $session): Response
    {
        $session->remove('panier');

        return $this->redirectToRoute('cart_index.cart');
    }
}
