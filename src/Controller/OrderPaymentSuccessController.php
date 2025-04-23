<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderPaymentSuccessController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/order/payment/success/{id}', name: 'order_payment_success')]
    public function success(
        Orders $order,
        EntityManagerInterface $em,
        CartService $cartService,
        SessionInterface $session
    ): Response {

        $session->get('panier', []);

        $order->getId();

        if (!$order) {
            return $this->redirectToRoute('cart_index.cart');
        }
        // if (
        //     !$order ||
        //     ($order && $order->getUser() !== $this->getUser()) ||
        //     ($order && $order->getStatus() === Orders::STATUS_PAID)
        // ) {
        //     $this->addFlash('warning', 'Cette commande n\'existe pas ou a déjà été payée !');
        //     return $this->redirectToRoute('app_orders_list', [
        //         'id' => $this->getUser()->getId(),
        //     ]);
        // }

        $order->setStatus(Orders::STATUS_PAID);

        $em->persist($order);
        $em->flush();


        // On vide le panier
        // Clear the cart using CartService
        $cartService->clear($session->remove('panier'));

        dd($order, $cartService);

        $this->addFlash('success', 'Votre paiement a été validée avec succès !');

        return $this->redirectToRoute('app_orders_detail', [
            'id' => $order->getId(),
            'order' => $order,
        ]);
    }
}
