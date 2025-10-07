<?php

namespace App\Service\Order;

use App\Entity\Order\OrderStorage;
use App\Entity\Order\OrderItem;
use App\Entity\Address\Address;
use App\ValueObject\Money;

class OrderMapper
{
    public function mapOrderItem(array $itemData): OrderItem
    {
        // Создаем объект OrderItem на основе данных
        $orderItem = new OrderItem();
        $orderItem->setUnitPrice(Money::fromMinor($itemData['unit_price'], $itemData['currency']));
        // Дальше маппинг остальных данных
        return $orderItem;
    }

    public function mapAddress(array $addressData): Address
    {
        // Маппинг данных для адреса
        $address = new Address();
        $address->setAddressStreet($addressData['street']);
        $address->setAddressCity($addressData['city']);
        // Дальше маппинг других полей
        return $address;
    }

    public function mapOrderStorage(array $orderData): OrderStorage
    {
        // Маппинг данных для OrderStorage
        $order = new OrderStorage();
        $order->setOrderItem($this->mapOrderItem($orderData['items']));
        $order->setOrderBillingAddress($this->mapAddress($orderData['billing']));
        $order->setOrderShipmentAddress($this->mapAddress($orderData['shipping']));
        // Дальше можно маппировать другие поля
        return $order;
    }
}

