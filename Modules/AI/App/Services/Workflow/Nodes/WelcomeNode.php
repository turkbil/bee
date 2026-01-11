<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

class WelcomeNode extends BaseNode
{
    public function execute(array $context): array
    {
        // Simply pass through - this is just a starting node
        return $context;
    }
}
