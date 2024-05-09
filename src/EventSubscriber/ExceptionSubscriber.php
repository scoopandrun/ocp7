<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();

            $message = $exception->getMessage();

            $data = [
                'status' => $statusCode,
                'message' => $message,
            ];

            if ($statusCode === 400) {
                $data["message"] = "Validation error";
                $data["errors"] = json_decode($message, true);
            }

            if ($statusCode === 404) {
                $request = $event->getRequest();
                $resourceType = explode(".", $request->attributes->get('_route'))[0];
                $data["message"] = 'This ' . ($resourceType ?: 'endpoint') . ' does not exist';
            }



            $response = new JsonResponse($data, $exception->getStatusCode());
        } else {
            $response = new JsonResponse([
                'status' => 500,
                'message' => $exception->getMessage(),
            ], 500);
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
