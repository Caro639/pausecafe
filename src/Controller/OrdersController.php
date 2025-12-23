<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Service\CartService;
use App\Entity\OrdersDetails;
use App\Form\OrderComfirmType;
use App\Service\OrderPersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders', name: 'orders_')]
final class OrdersController extends AbstractController
{

    public function __construct(protected OrderPersister $persister)
    {
    }

    #[Route('/', name: 'add')]
    /**
     * Envoie le panier de l'utilisateur connecté vers la page de validation avant paiement.
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \App\Service\CartService $cartService
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Products $product
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function add(
        SessionInterface $session,
        EntityManagerInterface $em,
        CartService $cartService,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $panier = $session->get('panier', []);

        if ($panier === []) {
            $this->addFlash('warning', 'Votre panier est vide !');
            return $this->redirectToRoute('app_home');
        }

        $formOrder = $this->createForm(OrderComfirmType::class, $order = new Orders());

        $formOrder->handleRequest($request);

        if (!$formOrder->isSubmitted()) {
            $this->addFlash('warning', 'Le formulaire n\'est pas soumis !');
            return $this->redirectToRoute('cart_index.cart');
        }

        $user = $this->getUser();
        // si form rempli et valid enregistr
        if ($formOrder->isValid()) {

            $formOrder->getData();
            $lastname = $formOrder->get('lastname')->getData();
            $address = $formOrder->get('address')->getData();
            $zipCode = $formOrder->get('zipcode')->getData();
            $city = $formOrder->get('city')->getData();
            // $promo = $form->get('promo')->getData();
            // dd($formOrder->getData());
            // enregistre bdd
            $order = new Orders();

            $cartService->getCart();

            $ordertotal = $cartService->getCart()['total'];




            // $order->setPromo($promo);
            $order->setUser($user);
            $reference = $createdAt->format('dmY') . '-' . uniqid();
            $order->setReference(uniqid($reference));
            $order->setCreatedAt($createdAt);

            $order->setLastname($lastname);
            $order->setAddress($address);
            $order->setZipcode($zipCode);
            $order->setCity($city);
            $order->setStatus(Orders::STATUS_PENDING);
            $order->setOrdertotal($ordertotal);

            // // dd($panier, $formOrder->getData(), $order);

            $em->persist($order);


            $this->persister->persistOrder(
                $order,
                $session,
                $ordersDetails = new OrdersDetails(),
            );
        }
        ;

        return $this->redirectToRoute('orders_show', [
            'id' => $order->getId(),
            'order' => $order,
            'total' => $cartService->getCart()['total'],
            'data' => $cartService->getCart()['data'],
        ]);
    }

    #[Route('/{id}', name: 'show')]
    /**
     * affiche la commande à payer
     * @param \App\Entity\Orders $order
     * @param \App\Service\CartService $cartService
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function show(
        Orders $order,
        CartService $cartService,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $order->getId();
        if (!$order) {
            $this->addFlash('warning', 'La commande n\'existe pas !');
            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('success', 'Vous pouvez passer au paiement !');


        return $this->render('orders/index.html.twig', [
            'id' => $order->getId(),
            'order' => $order,
            'total' => $cartService->getCart()['total'],
            'data' => $cartService->getCart()['data'],
        ]);
    }
}
