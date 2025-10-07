<?php

namespace App\Event\Vendor;

use App\Entity\Vendor\VendorWorkflow;
use App\Entity\Vendor\VendorWorkflowState;

class WorkflowTransitionPerformedEvent
{
    public function __construct(
        private readonly VendorWorkflow $workflow,
        private readonly VendorWorkflowState $from,
        private readonly VendorWorkflowState $to
    ) {}

    public function getWorkflow(): VendorWorkflow { return $this->workflow; }
    public function getFrom(): VendorWorkflowState { return $this->from; }
    public function getTo(): VendorWorkflowState { return $this->to; }
}
