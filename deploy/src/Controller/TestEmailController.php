<?php
declare(strict_types=1);
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

final class TestEmailController
{
    #[Route('/api/test-email', name: 'api_test_email', methods: ['POST'])]
    public function __invoke(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent() ?: "{}", true) ?: [];
        $to = $data['to'] ?? 'default@example.com';
        $subject = $data['subject'] ?? 'Test message';
        $message = $data['message'] ?? 'Hello from Order!';

        $email = (new Email())
            ->from('no-reply@order.local')
            ->to($to)
            ->subject($subject)
            ->text($message);
        $mailer->send($email);

        return new JsonResponse(['status' => 'ok', 'sent_to' => $to, 'subject' => $subject]);
    }
}
