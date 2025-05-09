<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Repository\OrdersDetailsRepository;
use App\Repository\ProductsRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderPaymentSuccessController extends AbstractController
{
    #[Route('/stripe/webhook', name: 'webhook', methods: ['POST'])]
    /**
     * gestion payment stripeWebhook
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Orders $order
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @return Response
     */
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
            $orderId = $paymentIntent->metadata->order_id; //envoyer l'ID de la commande dans les métadonnées


            if ($orderId) {
                $order = $em->getRepository(Orders::class)->find($orderId);
                $order->setStatus(Orders::STATUS_PAID);

                $em->persist($order);
                $em->flush();
            }
            return new Response('Webhook handled', 200);

        } elseif ($event->type === 'payment_intent.payment_failed') {
            $paymentIntent = $event->data->object; // Contient les données du PaymentIntent
            $orderId = $paymentIntent->metadata->order_id; // envoyer l'ID de la commande dans les métadonnées

            if ($orderId) {
                $order = $em->getRepository(Orders::class)->find($orderId);
                $order->setStatus(Orders::STATUS_FAILED);

                $em->persist($order);
                $em->flush();
            }
        }
        return new Response('Webhook handled', 200);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/order/payment/success/{id}', name: 'order_payment_success')]
    /**
     * page of success payment
     * @param \App\Entity\Orders $order
     * @param \App\Repository\OrdersDetailsRepository $ordersDetailsRepository
     * @param \App\Service\CartService $cartService
     * @param \App\Repository\ProductsRepository $productsRepository
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function success(
        Orders $order,
        OrdersDetailsRepository $ordersDetailsRepository,
        CartService $cartService,
        ProductsRepository $productsRepository,
    ): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $order->getId();

        if (!$order) {
            $this->addFlash('error', 'Aucune commande trouvée !');
            return $this->redirectToRoute('app_home');
        }

        $data = $cartService->getCart($productsRepository)['data'];
        if ($data) {
            $cartService->clear();
        }

        $ordersDetails = $ordersDetailsRepository->findBy(['orders' => $order]);

        $this->addFlash('success', 'Votre paiement a été validée avec succès !');

        // dd($order, $ordersDetails);

        return $this->render('order_payment_success/index.html.twig', [
            'id' => $order->getId(),
            'order' => $order,
            'ordersDetails' => $ordersDetails,
            'cartService' => $cartService->getCart($productsRepository)['total'],
            'data' => $cartService->clear(),
        ]);
    }
}
