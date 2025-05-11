<?php
namespace App\EventDispatcher;

use App\Service\CartService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class CartSubscriber implements EventSubscriberInterface
{
    private CartService $cartService;
    private Environment $twig;

    public function __construct(CartService $cartService, Environment $twig)
    {
        $this->cartService = $cartService;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $totalQuantity = $this->cartService->getTotalQuantity();
        $this->twig->addGlobal('cartTotalQuantity', $totalQuantity);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}