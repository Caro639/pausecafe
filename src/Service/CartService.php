<?php

namespace App\Service;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected function saveCart(array $panier, SessionInterface $session): void
    {
        // Save cart to session
        $session->set('panier', $panier);
    }

    public function clear(SessionInterface $session): void
    {
        // Empty the cart
        $session->remove('panier');
    }

    public function add(Products $product, SessionInterface $session)
    {
        // Add product to cart
        $id = $product->getId();

        $panier = $session->get('panier', []);
        if (empty($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }
        $session->set('panier', $panier);
    }

    public function getCart(SessionInterface $session, ProductsRepository $productsRepository)
    {
        // Get the cart
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
        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    public function remove(Products $product, SessionInterface $session)
    {
        // Remove product from cart
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
    }

    public function delete(Products $product, SessionInterface $session)
    {
        // Delete product from cart
        $id = $product->getId();

        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
    }
}
