<?php

namespace App\Service;

use App\Entity\Orders;
use App\Entity\Products;
use App\Service\CartService;
use App\Entity\OrdersDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderPersister
{
    protected $em;

    protected $cartService;

    public function __construct(
        private Security $security,
        EntityManagerInterface $em,
        CartService $cartService
    ) {
        $this->em = $em;
        $this->cartService = $cartService;
    }

    public function persistOrder(
        Orders $order,
        SessionInterface $session,
        EntityManagerInterface $em,
        CartService $cartService,
        OrdersDetails $ordersDetails,
        Products $product,
    ) {

        $panier = $session->get('panier', []);

        $this->security->getUser();

        $total = $cartService->getCart()['total'];
        $product = $cartService->getCart()['data'];

        foreach ($panier as $item => $quantity) {
            $ordersDetails = new OrdersDetails();

            $product = $this->em->getRepository(Products::class)->find($item);

            $price = $product->getPrice();
            $name = $product->getName();
            $total = $cartService->getCart()['total'];
            $data = $cartService->getCart()['data'];

            $ordersDetails->setProducts($product);
            $ordersDetails->setPrice($price);
            $ordersDetails->setQuantity($quantity);
            $ordersDetails->setName($name);
            $ordersDetails->setTotal($total);

            $order->addOrdersDetail($ordersDetails);

            $em->persist($ordersDetails);
        }

        // dd($ordersDetails, $order);
        // dd($order);
        $em->persist($order);
        $em->flush();

        return $order;
    }
}