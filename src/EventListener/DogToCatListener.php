<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

// Appelé a chaque reponse
final class DogToCatListener
{
    #[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onKernelResponse(ResponseEvent $event): void
    {
        // Example : Change sur toutes les page le string "Fromage" par "Cat"
        $event->getResponse()->setContent(
            str_replace('Fromage', 'Cat', $event->getResponse()->getContent())
        );
    }
}
