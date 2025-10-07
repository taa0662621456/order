<?php
declare(strict_types=1);

namespace App\Service\Security;
use App\Service\Generator;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

final class PasswordGenerator
{
    private ComputerPasswordGenerator $generator;

    public function __construct(
        bool $upperCase = true,
        bool $lowerCase = true,
        bool $numbers = true,
        bool $symbols = true,
        int $length = 12
    ) {
        $this->generator = new ComputerPasswordGenerator();
        $this->generator
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, $upperCase)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, $lowerCase)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, $numbers)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, $symbols)
            ->setLength($length);
    }

    public function generatePassword(): string
    {
        return $this->generator->generatePassword();
    }
}
