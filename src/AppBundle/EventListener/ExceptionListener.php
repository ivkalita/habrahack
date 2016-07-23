<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 13:54.
 */
namespace AppBundle\EventListener;

use AppBundle\Classes\Payload;
use AppBundle\Exceptions\BaseException;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * ExceptionListener constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $isBase = is_a($exception, 'AppBundle\Exceptions\BaseException');
        $contentType = $event->getRequest()->headers->get('Content-Type');
        $accept = $event->getRequest()->headers->get('Accept');
        $isAPI = (null !== $contentType && strpos($contentType, 'application/json') !== false)
              || (null !== $accept && strpos($accept, 'application/json') !== false);
        $isNotFound = strpos($exception->getMessage(), 'No route found for') === 0;

        $payload = null;
        if ($isAPI && $isBase) {
            /**
             * @var BaseException $exception
             */
            $payload = new Payload(null, $exception->getName(), $exception->getHttpCode(), $exception->getErrors());
        } elseif ($isAPI && $isNotFound) {
            $payload = new Payload(null, 'NotFound', 404);
        } elseif ($isAPI) {
            $payload = new Payload(null, 'GeneralInternalError', 500);
        } else {
            return;
        }
        $event->stopPropagation();

        $event->setResponse(new JsonResponse($payload->getForResponse(), $payload->getHttpCode()));
        $this->logException(
            $exception,
            sprintf(
                'Exception thrown when handling an exception (%s: %s at %s line %s)',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            )
        );
    }

    /**
     * @param \Exception $exception
     * @param $message
     */
    protected function logException(\Exception $exception, $message)
    {
        if (null !== $this->logger) {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($message, ['exception' => $exception]);
            } else {
                $this->logger->error($message, ['exception' => $exception]);
            }
        }
    }
}
