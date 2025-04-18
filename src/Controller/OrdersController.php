<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use App\Repository\UserRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders', name: 'orders_')]
final class OrdersController extends AbstractController
{
    // todo verifier si le paiement est valide avec status PAID avant de set en BDD
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
    #[Route('/add', name: 'add')]
    public function add(
        SessionInterface $session,
        ProductsRepository $productsRepository,
        EntityManagerInterface $em,
        CartService $cartService,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $panier = $session->get('panier', []);

        if ($panier === []) {
            $this->addFlash('error', 'Votre panier est vide !');
            return $this->redirectToRoute('app_home');
        }

        $order = new Orders();

        $ordertotal = $cartService->getCart($session, $productsRepository)['total'];
        $user = $userRepository->findOneBy(['id' => $user->getId()]);
        $lastname = $user->getLastName();
        $address = $user->getAddress();
        $zipCode = $user->getZipCode();
        $city = $user->getCity();


        // $order->setPromo($user->getPromo());

        $order->setOrdertotal($ordertotal);
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

        // dd($order);

        foreach ($panier as $item => $quantity) {
            $ordersDetails = new OrdersDetails();

            $product = $productsRepository->find($item);
            if (!$product) {
                $this->addFlash('error', 'Produit introuvable !');
                return $this->redirectToRoute('app_home');
            }

            $price = $product->getPrice();
            $name = $product->getName();
            $total = $cartService->getCart($session, $productsRepository)['total'];
            $data = $cartService->getCart($session, $productsRepository)['data'];

            $ordersDetails->setProducts($product);
            $ordersDetails->setPrice($price);
            $ordersDetails->setQuantity($quantity);
            $ordersDetails->setName($name);
            $ordersDetails->setTotal($total);

            $order->addOrdersDetail($ordersDetails);
        }

        // dd($order);
        $em->persist($order);
        $em->flush();

        // todo remove panier apres paiement
        // $session->remove('panier');

        $this->addFlash('success', 'Vous pouvez passer au paiement !');

        return $this->render('orders/index.html.twig', [
            'order' => $order,
            'data' => $data,
            'total' => $total,
            'user' => $user,
        ]);
    }
}
