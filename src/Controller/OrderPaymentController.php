<?php

namespace App\Controller;

use App\Entity\Orders;
use Stripe\StripeClient;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCardForm(
        Orders $order,
        CartService $cartService,
        SessionInterface $session
    ): Response {

        $order->getId();
        if (!$order) {
            return $this->redirectToRoute('cart_index.cart');
        }

        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);

        $total = $cartService->getCart()['total'];

        $data = $cartService->getCart()['data'];

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $total,
            'currency' => 'eur',
            'metadata' => [
                'order_id' => $order->getId(),
            ],
            // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        // dd($paymentIntent);


        return $this->render('order_payment/index.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'metadata' => $order->getId(),
            'order' => $order,
            'amount' => $total,
        ]);
    }
}