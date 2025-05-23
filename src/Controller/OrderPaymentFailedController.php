<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Service\CartService;
use App\Repository\OrdersDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderPaymentFailedController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/order/payment/failed/{id}', name: 'order_payment_failed')]
    /**
     * payment failed
     * @param \App\Entity\Orders $order
     * @param \App\Repository\OrdersDetailsRepository $ordersDetailsRepository
     * @param \App\Service\CartService $cartService
     * @param \Doctrine\ORM\EntityManagerInterface $manager
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function failed(
        Orders $order,
        OrdersDetailsRepository $ordersDetailsRepository,
        CartService $cartService,
        EntityManagerInterface $manager
    ): Response {

        $order->getId();

        if (!$order) {
            $this->addFlash('error', 'Aucune commande trouvée !');
            return $this->redirectToRoute('app_home');
        }

        $ordersDetails = $ordersDetailsRepository->findBy(['orders' => $order]);

        $this->addFlash('error', 'Votre paiement a échoué !');

        $manager->remove($order);
        $manager->flush();


        // return $this->redirectToRoute('app_home');
        return $this->render('order_payment_failed/failed.html.twig', [
            'id' => $order->getId(),
            'order' => $order,
            'ordersDetails' => $ordersDetails,
            'cartService' => $cartService->getCart()['total'],
        ]);
    }
}
