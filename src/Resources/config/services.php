<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Controller\{ReadinessController};
use OrderComponent\Command\{DlqRequeueCommand, OutboxReplayCommand};

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();
    $s->set(ReadinessController::class)
      ->public()
      ->arg('$asyncTransport', new Reference('messenger.transport.async'));

    $s->set(DlqRequeueCommand::class)
      ->tag('console.command')
      ->arg('$failed', new Reference('messenger.transport.failed'))
      ->arg('$async', new Reference('messenger.transport.async'));

    $s->set(OutboxReplayCommand::class)->tag('console.command');
};
