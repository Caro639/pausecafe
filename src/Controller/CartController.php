<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart', name: 'cart_')]
final class CartController extends AbstractController
{
    #[Route('/', name: 'index.cart')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository): Response
    {
        $panier = $session->get('panier', []);

        $data = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $product = $productsRepository->find($id);

            $data[] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
            $total += $product->getPrice() * $quantity;
        }
        return $this->render('cart/index.html.twig', [
            'data' => $data,
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'add.cart')]
    public function add(Products $product, SessionInterface $session): Response
    {
        $id = $product->getId();

        $panier = $session->get('panier', []);
        if (empty($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index.cart');
    }

    #[Route('/remove/{id}', name: 'remove.cart')]
    public function remove(Products $product, SessionInterface $session): Response
    {
        $id = $product->getId();

        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index.cart');
    }

    #[Route('/delete/{id}', name: 'delete.cart')]
    public function delete(Products $product, SessionInterface $session): Response
    {
        $id = $product->getId();

        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index.cart');
    }

    #[Route('/empty', name: 'empty.cart')]
    public function empty(SessionInterface $session): Response
    {
        $session->remove('panier');

        return $this->redirectToRoute('cart_index.cart');
    }
}
