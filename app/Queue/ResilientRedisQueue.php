<?php

namespace App\Queue;

use Laravel\Horizon\RedisQueue as HorizonRedisQueue;
use App\Services\RedisResilienceService;
use Illuminate\Support\Facades\Log;
use Predis\Connection\ConnectionException;
use Exception;

class ResilientRedisQueue extends HorizonRedisQueue
{
    /**
     * Pop the next job off of the queue.
     */
    public function pop($queue = null, $ttr = 60)
    {
        return RedisResilienceService::executeWithReconnect(function () use ($queue, $ttr) {
            return parent::pop($queue, $ttr);
        }, 3);
    }

    /**
     * Push a new job onto the queue.
     */
    public function push($job, $data = '', $queue = null)
    {
        return RedisResilienceService::executeWithReconnect(function () use ($job, $data, $queue) {
            return parent::push($job, $data, $queue);
        }, 3);
    }

    /**
     * Push a new job onto the queue after a delay.
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return RedisResilienceService::executeWithReconnect(function () use ($delay, $job, $data, $queue) {
            return parent::later($delay, $job, $data, $queue);
        }, 3);
    }

    /**
     * Release a reserved job back onto the queue.
     */
    public function release($queue, $payload, $delay, $attempts = 0)
    {
        return RedisResilienceService::executeWithReconnect(function () use ($queue, $payload, $delay, $attempts) {
            return parent::release($queue, $payload, $delay, $attempts);
        }, 3);
    }

    /**
     * Get the connection for the queue.
     */
    public function getConnection()
    {
        try {
            return RedisResilienceService::getConnection($this->connection);
        } catch (Exception $e) {
            Log::error('Failed to get Redis connection for queue', [
                'connection' => $this->connection,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a reserved job from the queue.
     */
    public function deleteReserved($queue, $job)
    {
        return RedisResilienceService::executeWithReconnect(function () use ($queue, $job) {
            return parent::deleteReserved($queue, $job);
        }, 2);
    }

    /**
     * Delete all of the jobs from the queue.
     */
    public function clear($queue)
    {
        return RedisResilienceService::executeWithReconnect(function () use ($queue) {
            return parent::clear($queue);
        }, 2);
    }

    /**
     * Get the size of the queue.
     */
    public function size($queue = null)
    {
        return RedisResilienceService::executeWithReconnect(function () use ($queue) {
            return parent::size($queue);
        }, 2);
    }

    /**
     * Execute Redis command with connection resilience
     */
    protected function evalScript($script, $arguments)
    {
        return RedisResilienceService::executeWithReconnect(function () use ($script, $arguments) {
            return $this->getConnection()->eval(
                $script, count($arguments), ...$arguments
            );
        }, 3);
    }
}