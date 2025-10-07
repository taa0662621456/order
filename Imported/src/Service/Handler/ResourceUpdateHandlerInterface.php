<?php
declare(strict_types=1);

namespace App\Service\Handler;

use Doctrine\Persistence\ObjectManager;

interface ResourceUpdateHandlerInterface
{
    public function handle(object $resource, object $requestConfiguration, ObjectManager $manager): void;
}
