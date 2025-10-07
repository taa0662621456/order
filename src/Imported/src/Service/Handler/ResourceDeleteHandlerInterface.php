<?php
declare(strict_types=1);

namespace App\Service\Handler;

interface ResourceDeleteHandlerInterface
{
    public function handle(object $resource): void;
}
