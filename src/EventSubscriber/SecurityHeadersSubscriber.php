<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly string $environment)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if ($this->environment !== 'prod' && !$request->isSecure()) {
            return;
        }

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload', false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        $response->headers->set('X-Frame-Options', 'DENY', false);
        $response->headers->set('Content-Security-Policy', "default-src 'self'; frame-ancestors 'none'; object-src 'none'", false);
        $response->headers->set('Referrer-Policy', 'no-referrer', false);
        $response->headers->set('Permissions-Policy', "geolocation=(), microphone=(), camera=()", false);
    }
}
