<?php

namespace App\EventDispatcher;

use App\Event\OrderSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'order.success' => 'sendSuccessEmail',
        ];
    }

    public function sendSuccessEmail(OrderSuccessEvent $orderSuccessEvent)
    {
        // dd($orderSuccessEvent);
        $this->logger->info("Email envoyé pour la commande n° " .
            $orderSuccessEvent->getOrder()->getId());
    }
}

