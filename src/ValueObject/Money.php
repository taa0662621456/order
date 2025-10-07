<?php

namespace App\ValueObject;

use JsonSerializable;
use InvalidArgumentException;

final class Money implements JsonSerializable
{
    private string $currency; // ISO 4217
    private int $amount; // minor units (cents)

    private function __construct(int $amount, string $currency)
    {
        if ($currency === '' || strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency must be 3-letter ISO code.');
        }

        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative.');
        }

        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public static function zero(string $currency = 'USD'): self
    {
        return new self(0, $currency);
    }

    public static function fromMinor(int $amount, string $currency = 'USD'): self
    {
        return new self($amount, $currency);
    }

    public static function fromDecimal(string|float $decimal, string $currency = 'USD', int $scale = 2): self
    {
        $normalized = is_string($decimal) ? $decimal : number_format($decimal, $scale, '.', '');

        // Дополнительная проверка на валидность десятичного значения
        if (!preg_match('/^-?\d+(?:\.\d{1,' . $scale . '})?$/', $normalized)) {
            throw new InvalidArgumentException('Invalid decimal money: ' . $normalized);
        }

        $minor = (int) round(((float) $normalized) * (10 ** $scale));
        return new self($minor, $currency);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    // Метод для работы с положительными значениями
    public function plus(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    // Метод для вычитания значений
    public function minus(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    // Умножение на коэффициент
    public function multiply(float $factor): self
    {
        return new self((int) round($this->amount * $factor), $this->currency);
    }

    // Проверка, является ли сумма отрицательной
    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    // Преобразование в десятичную строку
    public function asDecimal(int $scale = 2): string
    {
        return number_format($this->amount / (10 ** $scale), $scale, '.', '');
    }

    // Проверка равенства с другим объектом Money
    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && $this->amount === $other->amount;
    }

    // Сравнение на больше или равно
    public function greaterThanOrEqual(self $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount >= $other->amount;
    }

    // Сериализация в JSON
    public function jsonSerialize(): array
    {
        return [
            'currency' => $this->currency,
            'amount_minor' => $this->amount,
            'amount' => $this->asDecimal() // Добавим вывод в десятичном формате
        ];
    }

    // Приватный метод для проверки одинаковости валют
    private function assertSameCurrency(self $other): void
    {
        if ($other->currency !== $this->currency) {
            throw new InvalidArgumentException('Money currency mismatch.');
        }
    }

    // Преобразование из строки/целого числа в Money объект
    public static function fromString(string|int $value, string $currency = 'USD', int $scale = 2): self
    {
        if (is_int($value)) {
            return new self($value, $currency);
        }

        return self::fromDecimal($value, $currency, $scale);
    }

    public function addAmount(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtractAmount(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

}
