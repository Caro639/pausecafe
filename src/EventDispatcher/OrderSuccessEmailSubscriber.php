<?php

namespace App\EventDispatcher;

use App\Event\OrderSuccessEvent;
use App\Service\SendMailService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bundle\SecurityBundle\Security;

class OrderSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    private Security $security;
    private SendMailService $mail;

    public function __construct(LoggerInterface $logger, Security $security, SendMailService $mail)
    {
        $this->logger = $logger;
        $this->security = $security;
        $this->mail = $mail;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'order.success' => 'sendSuccessEmail',
        ];
    }

    /**
     * Summary of sendSuccessEmail
     * @param \App\Event\OrderSuccessEvent $orderSuccessEvent
     * @throws \RuntimeException
     * @return void
     */
    public function sendSuccessEmail(OrderSuccessEvent $orderSuccessEvent)
    {
        // dd($orderSuccessEvent);
        $user = $this->security->getUser();
        if (!$user) {
            throw new \RuntimeException('Utilisateur non authentifié.');
        }

        $order = $orderSuccessEvent->getOrder();
        $orderId = $order->getId();

        $this->mail->send(
            'no-reply@pausecafe.fr',
            $user->getEmail(),
            "Votre commande n° {$orderId} a bien été validée",
            'order_success',
            [
                'user' => $user,
                'order' => $order,
            ]

        );

        $this->logger->info("Email envoyé pour la commande n° " .
            $orderSuccessEvent->getOrder()->getId());
    }
}

