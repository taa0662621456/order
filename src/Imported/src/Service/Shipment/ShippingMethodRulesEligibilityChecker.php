<?php
declare(strict_types=1);

namespace App\Service\Shipment;
use App\Entity\Shipment\Shipment;

/**
 * Проверяет, применим ли метод доставки к субъекту доставки.
 * Использует мягкие проверки через method_exists, чтобы не зависеть от конкретных интерфейсов Sylius.
 */
final class ShippingMethodRulesEligibilityChecker
{
    public function isEligible(object $shippingSubject, object $shippingMethod): bool
    {
        // Если метод вообще выключен
        if (\method_exists($shippingMethod, 'isEnabled') && !$shippingMethod->isEnabled()) {
            return false;
        }

        // Минимальная сумма
        if (\method_exists($shippingMethod, 'getMinTotal') && \method_exists($shippingSubject, 'getTotal')) {
            if ($shippingSubject->getTotal() < $shippingMethod->getMinTotal()) {
                return false;
            }
        }

        // Ограничения по странам
        if (\method_exists($shippingMethod, 'getAllowedCountries') && \method_exists($shippingSubject, 'getShippingCountry')) {
            $allowed = (array) $shippingMethod->getAllowedCountries();
            $country = (string) $shippingSubject->getShippingCountry();
            if ($allowed && !\in_array($country, $allowed, true)) {
                return false;
            }
        }

        return true;
    }
}
