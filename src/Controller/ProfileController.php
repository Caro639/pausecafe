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

    // }
}
