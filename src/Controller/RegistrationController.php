<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    /**
     * inscription de l'utilisateur plus envoie d'un mail de vérification
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @param SendMailService $mail
     * @param JWTService $jwt
     * @return Response
     */
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        JWTService $jwt
    ): Response {
        $user = new User();

        $user->setRoles(['ROLE_USER']);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            // dd($user);


            $entityManager->flush();

            // do anything else you need here, like send an email
            // create JWt user
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];

            $payload = [
                'user_id' => $user->getId(),
            ];

            $token = $jwt->generate(
                $header,
                $payload,
                $this->getParameter('app.jwtsecret')
            );
            // dd($token);

            $mail->send(
                'no-reply@pausecafe.fr',
                $user->getEmail(),
                'Activation de votre compte',
                'register',
                [
                    'user' => $user,
                    'token' => $token,
                    // 'token' => $user->getToken(),
                ]

            );

            return $security->login($user, UserAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * vérification de l'utilisateur
     */
    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser(
        $token,
        JWTService $jwt,
        UserRepository $userRepository,
        EntityManagerInterface $manager,
    ): Response {

        // dd($jwt->check($token, $this->getParameter('app.jwtsecret')));
        if (
            $jwt->isValid($token) && !$jwt->isExpired($token) &&
            $jwt->check($token, $this->getParameter('app.jwtsecret'))
        ) {
            $payload = $jwt->getPayload($token);

            $user = $userRepository->find($payload['user_id']);

            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $manager->flush();

                // Rechargez l'utilisateur dans la session
                // $security->login($user);

                $this->addFlash(
                    'success',
                    'Votre compte a été activé avec succès.'
                );
                return $this->redirectToRoute('profile_index.profile', [
                    'id' => $user->getId(),
                ]);
            }

        }
        $this->addFlash(
            'danger',
            'Le lien de vérification est invalide ou a expiré.'
        );
        return $this->redirectToRoute('app_login');
    }

    /**
     * renvoie de l'email de vérification à la demande de l'utilisateur
     *
     * @param JWTService $jwt
     * @param SendMailService $mail
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(
        JWTService $jwt,
        SendMailService $mail,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash(
                'danger',
                'Vous devez être connecté pour accéder à cette page.'
            );
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($user->getIsVerified()) {
            $this->addFlash(
                'warning',
                'Votre compte est déjà activé.'
            );
            return $this->redirectToRoute('profile_index.profile', [
                'id' => $user->getId(),
            ]);
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $payload = [
            'user_id' => $user->getId(),
        ];

        $token = $jwt->generate(
            $header,
            $payload,
            $this->getParameter('app.jwtsecret')
        );
        // dd($token);

        $mail->send(
            'no-reply@pausecafe.fr',
            $user->getEmail(),
            'Activation de votre compte',
            'register',
            [
                'user' => $user,
                'token' => $token,
            ]
        );
        $this->addFlash(
            'success',
            'Un email de vérification a été envoyé.'
        );
        return $this->redirectToRoute('profile_index.profile', [
            'id' => $user->getId(),
        ]);
    }
}