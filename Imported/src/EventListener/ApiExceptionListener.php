<?php

namespace App\EventListener;

use App\Model\ErrorResponseModel;
use App\Request\AttributeRequest;
use App\Service\System\ExceptionMapping;
use App\Service\System\ExceptionMappingResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListener
{
    private ExceptionMappingResolver $exceptionMappingResolver;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;

    public function __construct(
        ExceptionMappingResolver $exceptionMappingResolver,
        LoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        $this->exceptionMappingResolver = $exceptionMappingResolver;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $isApiZone = $request->attributes->get(AttributeRequest::API_ZONE, true);

        if (!$isApiZone) {
            return;
        }

        $throwable = $event->getThrowable();

        // Получаем маппинг исключения
        $mapping = $this->exceptionMappingResolver->resolve(\get_class($throwable));
        if (null === $mapping) {
            $mapping = new ExceptionMapping(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Логируем, если ошибка серьёзная
        if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable()) {
            $this->logger->error($throwable->getMessage(), [
                'exception' => $throwable,
                'trace' => $throwable->getTraceAsString(),
                'previous' => $throwable->getPrevious()?->getMessage(),
            ]);
        }

        // Формируем сообщение для ответа
        $message = $mapping->isHidden()
            ? Response::$statusTexts[$mapping->getCode()] ?? 'Error'
            : $throwable->getMessage();

        $errorModel = new ErrorResponseModel(
            $message,
            $mapping->getCode(),
            !$mapping->isHidden() ? $throwable->getTraceAsString() : null
        );

        $data = $this->serializer->serialize($errorModel, JsonEncoder::FORMAT);

        $response = new JsonResponse($data, $mapping->getCode(), [], true);
        $event->setResponse($response);
    }
}
