<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrdersDetailsRepository;
use App\Repository\OrdersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrdersListController extends AbstractController
{
    #[Route('/orders/list', name: 'app_orders_list')]
    public function index(
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
        $user->getOrders();

        $orders = $ordersRepository->findBy(['user' => $user]);

        $ordersDetails = $ordersDetailsRepository->findBy(['orders' => $orders]);

        // dd($orders, $ordersDetails);

        // dd($user);
        return $this->render('orders_list/index.html.twig', [
            'orders' => $orders,
            'ordersDetails' => $ordersDetails,
        ]);
    }
}
