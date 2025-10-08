<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Billing;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\Billing\{OrderPaymentIntent, OrderTransaction};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class WebhookHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private IdempotencyGuard       $guard,
    ) {}

    /**
     * @throws \Exception
     */
    public function handlePayment(Request $request): array
    {
        $provider = $request->headers->get('X-Provider', 'mock');
        $eventId = $request->headers->get('X-Event-Id', bin2hex(random_bytes(6)));
        $payload = $request->getContent() ?: '{}';

        if (!$this->guard->checkAndPersist($provider, $eventId, $payload)) {
            return ['status' => 'ignored', 'reason' => 'duplicate'];
        }

        $data = json_decode($payload, true);
        if (!is_array($data) || empty($data['intent']) || empty($data['status'])) {
            throw new BadRequestHttpException('Invalid payload');
        }

        $intentRepo = $this->em->getRepository(OrderPaymentIntent::class);
        $intent = $intentRepo->findOneBy(['intentId' => $data['intent']]);
        if (!$intent) {
            throw new BadRequestHttpException('Unknown payment intent');
        }

        // Update intent and create transaction
        if ($data['status'] === 'succeeded') {
            $intent->markConfirmed();
            $txn = new OrderTransaction($intent->getOrder(), 'tx_' . bin2hex(random_bytes(8)), $intent->getAmount(), $data['currency'] ?? 'USD');
            $txn->confirm();
            $this->em->persist($txn);
        } else {
            $intent->markFailed();
        }
        $this->em->flush();

        return ['status' => 'ok'];
    }

    /**
     * @throws \Exception
     */
    public function handleRefund(Request $request): array
    {
        // For brevity: re-use handlePayment shape
        return $this->handlePayment($request);
    }
}
