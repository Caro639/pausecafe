<?php

namespace App\Service;

use App\Entity\Orders;
use Stripe\StripeClient;
use App\Service\CartService;

class StripeService
{

    public function getPaymentIntent(
        CartService $cartService,
        Orders $order,
    ) {

        if (!isset($_ENV['STRIPE_SECRET_KEY'])) {
            throw new \RuntimeException('La clé Stripe n\'est pas définie dans les variables d\'environnement.');
        }


        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);

        $order->getId();
        if (!$order) {
            throw new \RuntimeException('La commande n\'existe pas.');
        }

        $total = $cartService->getCart()['total'];
        if (!isset($total)) {
            throw new \RuntimeException('Le total du panier est introuvable.');
        }

        try {
            return $stripe->paymentIntents->create([
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
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \RuntimeException('Erreur lors de la création du PaymentIntent : ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
