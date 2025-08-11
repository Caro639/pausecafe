<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\User;
use App\Repository\OrdersDetailsRepository;
use App\Repository\OrdersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrdersListController extends AbstractController
{
    /**
     * Affiche la liste des commandes de l'utilisateur connecté.
     *
     * @param User $user
     * @param OrdersRepository $ordersRepository
     * @param OrdersDetailsRepository $ordersDetailsRepository
     * @return Response
     */
    #[Route('/orders/list/{id}', name: 'app_orders_list')]
    public function index(
        Orders $orders,
        User $user,
        OrdersRepository $ordersRepository,
        OrdersDetailsRepository $ordersDetailsRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Aucune commande trouvée !');
            return $this->redirectToRoute('app_home');
        }
        // $user->getOrders();

        $orders = $ordersRepository->findBy(['user' => $user], ['created_at' => 'DESC']);

        $ordersDetails = $ordersDetailsRepository->findBy(['orders' => $orders]);

        // dd($orders, $ordersDetails);

        return $this->render('orders_list/index.html.twig', [
            'orders' => $orders,
            'ordersDetails' => $ordersDetails,
        ]);
    }


    #[Route('/orders/list/detail/{id}', name: 'app_orders_detail')]
    /**
     * order of show
     * @param \App\Entity\Orders $order
     * @param \App\Repository\OrdersDetailsRepository $ordersDetailsRepository
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function show(
        Orders $order,
        OrdersDetailsRepository $ordersDetailsRepository,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $order->getId();

        if (!$order) {
            $this->addFlash('error', 'Aucune commande trouvée !');
            return $this->redirectToRoute('app_home');
        }

        $ordersDetails = $ordersDetailsRepository->findBy(['orders' => $order]);

        // dd($order, $ordersDetails);

        return $this->render('orders_list/detail.html.twig', [
            'id' => $order->getId(),
            'order' => $order,
            'ordersDetails' => $ordersDetails,
        ]);
    }
}