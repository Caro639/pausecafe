<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Service\StripeService;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/order/payment', name: 'order_payment_')]
final class OrderPaymentController extends AbstractController
{
    #[Route('/{id}', name: 'form.order')]
    /**
     * Affiche le formulaire de paiement.
     * @param \App\Entity\Orders $order
     * @param \App\Service\CartService $cartService
     * @param \App\Service\StripeService $stripeService
     * @return Response
     */
    public function showCardForm(
        Orders $order,
        CartService $cartService,
        StripeService $stripeService,
    ): Response {

        $total = $cartService->getCart()['total'];

        $data = $cartService->getCart()['data'];

        $paymentIntent = $stripeService->getPaymentIntent(
            $cartService,
            $order,
        );
        // dd($paymentIntent);

        return $this->render('order_payment/index.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'metadata' => $order->getId(),
            'order' => $order,
            'amount' => $total,
        ]);
    }
}