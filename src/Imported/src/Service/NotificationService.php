<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

final class NotificationService
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly Environment $twig,
        private readonly LoggerInterface $logger,
    ) {}

    public function sendTemplatedEmail(string $to, string $subject, string $template, array $context = [], ?string $from = 'noreply@yourdomain.com'): void
    {
        $html = $this->twig->render($template, $context);
        try {
            $this->bus->dispatch(new SendEmailMessage($to, $subject, $html, $from));
        } catch (\Throwable $e) {
            $this->logger->error('Notification dispatch failed', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    // Заглушки для SMS/Push
    public function sendSms(string $phone, string $text): void
    {
        $this->logger->info('SMS dispatched', ['phone' => $phone, 'text' => $text]);
    }

    public function sendPush(string $deviceToken, string $title, string $body): void
    {
        $this->logger->info('Push dispatched', ['device' => $deviceToken, 'title' => $title]);
    }
}
