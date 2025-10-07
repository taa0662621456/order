<?php
declare(strict_types=1);

namespace App\Service\Payment\Gateway;
use App\Entity\Payment\Payment;

use Symfony\Component\HttpFoundation\Request;

interface WebhookProcessor
{
    public function process(Request $request): bool;
}
