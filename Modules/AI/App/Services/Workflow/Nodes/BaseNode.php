<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

/**
 * Base Node
 *
 * Tüm node'ların extend edeceği base class
 */
abstract class BaseNode
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Node'u çalıştır
     *
     * @param array $context Flow context
     * @return array Result
     */
    abstract public function execute(array $context): array;

    /**
     * Config'den değer al
     */
    protected function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
