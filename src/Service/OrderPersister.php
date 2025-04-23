<?php

namespace App\Service;

use App\Entity\Orders;
use App\Repository\UserRepository;
use App\Service\CartService;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\SecurityBundle\Security;

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
        ProductsRepository $productsRepository,
        EntityManagerInterface $em,
        CartService $cartService,
        UserRepository $user,
        Request $request,
        OrdersDetails $ordersDetails,
    ) {

        $panier = $session->get('panier', []);

        $user = $this->security->getUser();

        $total = $cartService->getCart($session, $productsRepository)['total'];
        $data = $cartService->getCart($session, $productsRepository)['data'];

        foreach ($panier as $item => $quantity) {
            $ordersDetails = new OrdersDetails();

            $product = $productsRepository->find($item);

            $price = $product->getPrice();
            $name = $product->getName();
            $total = $cartService->getCart($session, $productsRepository)['total'];
            $data = $cartService->getCart($session, $productsRepository)['data'];

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
        $em->flush();

        return $order;
    }
}