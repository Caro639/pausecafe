<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted(attribute: 'ROLE_USER')]
#[Route('/profil', name: 'profile_')]
final class ProfileController extends AbstractController
{
    #[Route('/{id}', name: 'index.profile')]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        $user = $userRepository->findOneBy(['id' => $user->getId()]);
        if ($user === null) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $userRepository->findOneBy(['id' => $user->getId()]),
        ]);
    }

    // #[Route('/commandes', name: 'orders')]
    // public function orders(
    //     UserRepository $userRepository,
    //     OrdersRepository $ordersRepository,
    //     OrdersDetailsRepository $ordersDetailsRepository
    // ): Response {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    //     $user = $this->getUser();
    //     if ($user === null) {
    //         return $this->redirectToRoute('app_login');
    //     }
    //     $user = $userRepository->findOneBy(['id' => $user->getId()]);
    //     if ($user === null) {
    //         throw $this->createNotFoundException('Utilisateur non trouvé.');
    //     }
    //     $orders = $ordersRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    //     if ($orders === null) {
    //         throw $this->createNotFoundException('Aucune commande trouvée.');
    //     }
    //     $ordersDetails = $ordersDetailsRepository->findBy(['orders' => $orders], ['createdAt' => 'DESC']);
    //     if ($ordersDetails === null) {
    //         throw $this->createNotFoundException('Aucun détail de commande trouvé.');
    //     }
    //     $user->getOrders();
    //     $orders = $ordersDetailsRepository->getOrdersDetails();
    //     dd($orders);

    //     return $this->render('profile/order.html.twig', [
    //         'user' => $user,
    //         'orders' => $orders,
    //         'ordersDetails' => $ordersDetails,
    //     ]);
    // }
}
