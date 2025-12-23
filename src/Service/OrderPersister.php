<?php

namespace App\Service;

use App\Entity\Orders;
use App\Entity\Products;
use App\Service\CartService;
use App\Entity\OrdersDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderPersister
{

    public function __construct(
        protected EntityManagerInterface $em,
        protected CartService $cartService,
    ) {
    }

    public function persistOrder(
        Orders $order,
        SessionInterface $session,
        OrdersDetails $ordersDetails,
    ) {

        $panier = $session->get('panier', []);

        $total = $this->cartService->getCart()['total'];
        $this->cartService->getCart();

        foreach ($panier as $item => $quantity) {
            $ordersDetails = new OrdersDetails();

            $product = $this->em->getRepository(Products::class)->find($item);

            $price = $product->getPrice();
            $name = $product->getName();
            $total = $this->cartService->getCart()['total'];
            $this->cartService->getCart();

            $ordersDetails->setProducts($product);
            $ordersDetails->setPrice($price);
            $ordersDetails->setQuantity($quantity);
            $ordersDetails->setName($name);
            $ordersDetails->setTotal($total);

            $order->addOrdersDetail($ordersDetails);

            $this->em->persist($ordersDetails);
        }
        // dd($ordersDetails, $order);
        // dd($order);
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }
}