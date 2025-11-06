<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

class EndNode extends BaseNode
{
    public function execute(array $context): array
    {
        // End of flow
        return $context;
    }
}
