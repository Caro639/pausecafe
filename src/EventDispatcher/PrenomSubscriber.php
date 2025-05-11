<?php

namespace App\EventDispatcher;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomSubscriber
{
    public function addPrenomToAttributes(RequestEvent $requestEvent)
    {
        // dd($requestEvent);

    }
}