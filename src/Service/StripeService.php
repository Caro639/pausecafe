<?php

namespace App\Service;

use Stripe\StripeClient;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StripeService
{

    public function getPaymentIntent(
        SessionInterface $session,
        CartService $cartService,
        ProductsRepository $productsRepository
    ) {


        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);


        $total = $cartService->getCart($session, $productsRepository)['total'];


        return $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $total,
            'currency' => 'eur',
            // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

    }
}