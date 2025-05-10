<?php

namespace App\Event;

use App\Entity\Orders;
use Symfony\Contracts\EventDispatcher\Event;

class OrderSuccessEvent extends Event
{

    private Orders $order;

    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

    public function getOrder(): Orders
    {
        return $this->order;
    }
}