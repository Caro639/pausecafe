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
    public function __construct(
        private UserRepository $userRepository
    ) {
        // Inject dependencies if needed
    }

    #[Route('/{id}', name: 'index.profile')]
    /**
     * page profil
     * @param \App\Repository\UserRepository $userRepository
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(string $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        // $user = $userRepository->findOneBy(['id' => $user->getId()]);
        $userId = $this->userRepository->findOneBy(['id' => $id]);
        if ($user === null) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('profile/index.html.twig', [
            // 'user' => $userRepository->findOneBy(['id' => $user->getId()]),
            'user' => $userId,
        ]);
    }
}