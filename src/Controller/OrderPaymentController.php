<?php

namespace App\Controller;

use App\Entity\Orders;
use Stripe\StripeClient;
use App\Service\CartService;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/order/payment', name: 'order_payment_')]
final class OrderPaymentController extends AbstractController
{

    #[Route('/stripe/webhook', name: 'webhook', methods: ['POST'])]
    public function stripeWebhook(
        Request $request,
        Orders $order,
        EntityManagerInterface $em,
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $endpointSecret = ($_ENV['WEBHOOK_SIGNING_SECRET']);

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Payload invalide
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Signature invalide
            return new Response('Invalid signature', 400);
        }

        $order->getId();

        // Gérez l'événement
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object; // Contient les données du PaymentIntent
            $order = $paymentIntent->metadata->order_id; // Assurez-vous d'envoyer l'ID de la commande dans les métadonnées

            $order = $em->getRepository(Orders::class)->find($order); // Récupérez l'entité Orders depuis la base de données

            if ($order) {
                $order->setStatus(Orders::STATUS_PAID);
                // Mettez à jour le statut de la commande
                $em->persist($order);
                $em->flush();
            }

            $request->getSession()->get('panier', []);
            $request->getSession()->remove('panier'); // Supprimez le panier de la session


            return new Response('Webhook handled', 200);
        }
    }

    /**
     * Affiche le formulaire de paiement.
     *
     * @param Orders $order
     * @param CartService $cartService
     * @param SessionInterface $session
     * @param ProductsRepository $productsRepository
     * @return Response
     */
    #[Route('/{id}', name: 'form.order')]
    public function showCardForm(
        Orders $order,
        CartService $cartService,
        SessionInterface $session,
        ProductsRepository $productsRepository
    ): Response {

        $order->getId();
        if (!$order) {
            return $this->redirectToRoute('cart_index.cart');
        }

        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);

        $total = $cartService->getCart($session, $productsRepository)['total'];

        $data = $cartService->getCart($session, $productsRepository)['data'];

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $total,
            'currency' => 'eur',
            'metadata' => [
                'order_id' => $order->getId(), // Ajoutez l'ID de la commande ici
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