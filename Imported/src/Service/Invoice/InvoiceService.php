<?php
declare(strict_types=1);

namespace App\Service\Invoice;

use App\Entity\Order\Order;
use App\Service\Address\AddressFormatStrategyFactory;

final class InvoiceService
{
    public function __construct(private AddressFormatStrategyFactory $factory) {}

    public function generateInvoice(Order $order, float $subtotal): string
    {
        $billing = $order->billingAddress();
        if ($billing === null) {
            throw new \RuntimeException('Billing address required for invoice');
        }

        $formatter = $this->factory->forCountry($billing->country()->value());
        $formattedAddress = $formatter->format($billing);

        // Демонстрационная логика генерации счета
        $invoice = "=== INVOICE ===\n";
        $invoice .= "Bill To:\n" . $formattedAddress . "\n";
        $invoice .= "Subtotal: $" . number_format($subtotal, 2) . "\n";

        return $invoice;
    }
}
