<?php
declare(strict_types=1);

namespace OrderComponent\Contract\Domain;

interface RecordsDomainEvents
{
    /** @return array<int,object> */
    public function releaseEvents(): array;
}
