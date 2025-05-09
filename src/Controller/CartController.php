<?php

namespace App\Controller;

use App\Entity\Products;
use App\Service\CartService;
use App\Form\OrderComfirmType;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
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
     * 
     * @param ProductsRepository $productsRepository
     * @param CartService $cartService
     * @return Response
     */
    #[Route('/', name: 'index.cart', methods: ['GET', 'POST'])]
    public function index(
        ProductsRepository $productsRepository,
        CartService $cartService
    ): Response {

        $data = $cartService->getCart($productsRepository)['data'];
        $total = $cartService->getCart($productsRepository)['total'];

        $formOrder = $this->createForm(OrderComfirmType::class, null);

        return $this->render('cart/index.html.twig', [
            'data' => $data,
            'total' => $total,
            'formOrder' => $formOrder->createView(),
        ]);
    }

    #[Route('/add/{id}', name: 'add.cart', condition: "params['id']")]
    /**
     * ajoute un produit au panier
     * @param \App\Entity\Products $product
     * @param \App\Service\CartService $cartService
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function add(
        Products $product,
        CartService $cartService,
        Request $request
    ): Response {
        $id = $product->getId();

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id introuvable !");
        }

        $cartService->add($product);

        $this->addFlash('success', 'Le produit a bien été ajouté au panier !');
        // return to the current page
        // return $this->redirectToRoute('products_details', ['slug' => $product->getSlug()]);
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('cart_index.cart'));
    }

    /**
     * retire un produit du panier
     */
    #[Route('/remove/{id}', name: 'remove.cart')]
    /**
     * Summary of remove
     * @param \App\Entity\Products $product
     * @param \App\Service\CartService $cartService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(
        Products $product,
        CartService $cartService
    ): Response {

        $product->getId();

        $cartService->remove($product);
        $this->addFlash('success', 'Le produit a bien été retiré du panier !');

        return $this->redirectToRoute('cart_index.cart');
    }

    /**
     * supprime un produit du panier
     */
    #[Route('/delete/{id}', name: 'delete.cart')]
    /**
     * Summary of delete
     * @param \App\Entity\Products $product
     * @param \App\Service\CartService $cartService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(
        Products $product,
        CartService $cartService
    ): Response {

        $product->getId();

        $cartService->delete($product);
        $this->addFlash('success', 'Le produit a bien été supprimé du panier !');

        return $this->redirectToRoute('cart_index.cart');
    }

    #[Route('/empty', name: 'empty.cart')]
    /**
     * Summary of empty
     * @param \App\Service\CartService $cartService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function empty(CartService $cartService): Response
    {
        $cartService->clear();

        return $this->redirectToRoute('cart_index.cart');
    }
}
