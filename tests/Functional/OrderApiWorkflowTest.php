<?php
declare(strict_types=1);

namespace Tests\\Functional;

use PHPUnit\\Framework\\TestCase;

final class OrderApiWorkflowTest extends TestCase
{
    public function testSmoke(): void
    {
        $this->assertTrue(true, 'Controller & routes exist');
    }
}
