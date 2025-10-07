<?php
declare(strict_types=1);

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;
use Tests\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';

$kernel = new Kernel('test', true);
$kernel->boot();

$container = $kernel->getContainer();
$em = $container->get(EntityManagerInterface::class);
$meta = $em->getMetadataFactory()->getAllMetadata();
if ($meta) {
    $tool = new SchemaTool($em);
    $tool->dropDatabase();
    $tool->createSchema($meta);
}
