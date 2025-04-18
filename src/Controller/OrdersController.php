<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/orders', name: 'orders_')]
final class OrdersController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(
        SessionInterface $session,
        ProductsRepository $productsRepository,
        EntityManagerInterface $em,
        CartService $cartService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        if ($panier === []) {
            $this->addFlash('error', 'Votre panier est vide !');
            return $this->redirectToRoute('app_home');
        }

        $order = new Orders();

        $order->setUser($this->getUser());
        $reference = $createdAt = new \DateTimeImmutable();
        $reference = $createdAt->format('dmY') . '-' . uniqid();
        $order->setReference(uniqid($reference));
        $order->setCreatedAt($createdAt);

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

        // $session->remove('panier');

        $this->addFlash('success', 'Vous pouvez passer au paiement !');

        return $this->render('orders/index.html.twig', [
            'order' => $order,
            'data' => $data,
            'total' => $total,
            'user' => $this->getUser(),
        ]);
    }
}
