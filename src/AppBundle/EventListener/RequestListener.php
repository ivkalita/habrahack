<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 14.06.16
 * Time: 11:38.
 */
namespace AppBundle\EventListener;

use Monolog\Logger;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * RequestListener constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $content = json_encode($request->getContent());
        $uri = $request->getUri();
        $method = $request->getMethod();
        $authHeader = $request->headers->get('Authorization');
        $this->logger->info("$method $uri Auth: $authHeader $content");
    }
}
