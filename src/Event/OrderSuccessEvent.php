<?php

namespace App\Event;

use App\Entity\Orders;
use Symfony\Contracts\EventDispatcher\Event;

class OrderSuccessEvent extends Event
{

    public function __construct(private Orders $order)
    {
    }

    public function getOrder(): Orders
    {
        return $this->order;
    }
}