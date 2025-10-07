<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorWorkflow;

class WorkflowCreatedEvent
{
    public function __construct(private readonly VendorWorkflow $workflow) {}
    public function getWorkflow(): VendorWorkflow { return $this->workflow; }
}
