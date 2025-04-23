<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Orders;
use App\Repository\OrdersRepository;
use App\Service\CartService;
use App\Entity\OrdersDetails;
use App\Form\OrderComfirmType;
use App\Service\OrderPersister;
use App\Repository\UserRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders', name: 'orders_')]
final class OrdersController extends AbstractController
{
    protected OrderPersister $persister;

    public function __construct(OrderPersister $persister)
    {
        $this->persister = $persister;
    }

    // todo status PAID & set en BDD + panier vider
    /**
     * Envoie le panier de l'utilisateur connecté vers la page de validation avant paiement.
     *
     * @param SessionInterface $session
     * @param ProductsRepository $productsRepository
     * @param EntityManagerInterface $em
     * @param CartService $cartService
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/', name: 'add')]
    public function add(
        SessionInterface $session,
        ProductsRepository $productsRepository,
        EntityManagerInterface $em,
        CartService $cartService,
        Request $request,
        UserRepository $userRepository,
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
        if ($formOrder->isSubmitted() && $formOrder->isValid()) {

            $formOrder->getData();
            $lastname = $formOrder->get('lastname')->getData();
            $address = $formOrder->get('address')->getData();
            $zipCode = $formOrder->get('zipcode')->getData();
            $city = $formOrder->get('city')->getData();
            // $promo = $form->get('promo')->getData();
            // dd($formOrder->getData());
            // enregistre bdd
            $order = new Orders();

            $data = $cartService->getCart($session, $productsRepository)['data'];

            $ordertotal = $cartService->getCart($session, $productsRepository)['total'];


            // $order->setPromo($promo);
            $order->setUser($user);

            $reference = $createdAt = new \DateTimeImmutable();
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
                $productsRepository,
                $em,
                $cartService,
                $userRepository,
                $request,
                $ordersDetails = new OrdersDetails(),
            );

            if (!$order) {
                throw new \Exception('La commande n\'a pas pu être validée.');
            }


            // foreach ($panier as $item => $quantity) {
            //     $ordersDetails = new OrdersDetails();

            //     $product = $productsRepository->find($item);
            //     if (!$product) {
            //         $this->addFlash('error', 'Produit introuvable !');
            //         return $this->redirectToRoute('app_home');
            //     }

            //     $price = $product->getPrice();
            //     $name = $product->getName();
            //     $total = $cartService->getCart($session, $productsRepository)['total'];
            //     $data = $cartService->getCart($session, $productsRepository)['data'];

            //     $ordersDetails->setProducts($product);
            //     $ordersDetails->setPrice($price);
            //     $ordersDetails->setQuantity($quantity);
            //     $ordersDetails->setName($name);
            //     $ordersDetails->setTotal($total);

            //     $order->addOrdersDetail($ordersDetails);

            //     $em->persist($ordersDetails);
            // }

            // dd($order);
            // $em->flush();

            // dd($order);
        }
        ;

        return $this->redirectToRoute('orders_show', [
            'id' => $order->getId(),
            'order' => $order,
            'total' => $total = $cartService->getCart($session, $productsRepository)['total'],
            'data' => $data = $cartService->getCart($session, $productsRepository)['data'],

        ]);
    }

    /**
     * Affiche la commande de l'utilisateur connecté.
     *
     * @param Orders $order
     * @param ProductsRepository $productsRepository
     * @param SessionInterface $session
     * @param CartService $cartService
     * @return Response
     */
    #[Route('/{id}', name: 'show')]
    public function show(
        Orders $order,
        ProductsRepository $productsRepository,
        SessionInterface $session,
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
            'total' => $total = $cartService->getCart($session, $productsRepository)['total'],
            'data' => $data = $cartService->getCart($session, $productsRepository)['data'],

        ]);
    }
}
