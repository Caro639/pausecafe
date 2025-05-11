<?php

namespace App\Service;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private RequestStack $request;
    private SessionInterface $session;

    protected ProductsRepository $productsRepository;

    public function __construct(RequestStack $request, ProductsRepository $productsRepository)
    {
        $this->session = $request->getSession();
        $this->productsRepository = $productsRepository;
    }

    protected function saveCart(array $panier): void
    {
        // Save cart to session
        $this->session->set('panier', $panier);
    }

    public function clear(): void
    {
        // Empty the cart
        $this->session->remove('panier');
    }

    public function add(Products $product)
    {
        // Add product to cart
        $id = $product->getId();

        $panier = $this->session->get('panier', []);
        if (empty($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }
        $this->session->set('panier', $panier);
    }

    public function getCart()
    {
        // Get the cart
        $panier = $this->session->get('panier', []);

        $data = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $product = $this->productsRepository->find($id);

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

    public function remove(Products $product)
    {
        // Remove product from cart
        $id = $product->getId();

        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }
        $this->session->set('panier', $panier);
    }

    public function delete(Products $product)
    {
        // Delete product from cart
        $id = $product->getId();

        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function getTotalQuantity(): int
    {
        $panier = $this->session->get('panier', []);
        $totalQuantity = 0;

        foreach ($panier as $id => $quantity) {
            $product = $this->productsRepository->find($id);
            if ($product) {
                $totalQuantity += $quantity;
            }
        }
        return $totalQuantity;
    }
}
