<?php

namespace Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class LoggerQueue extends AbstractLogger
{
    /**
     * @var \SplObjectStorage
     */
    private $queue;

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->queue->rewind();

        /** @var $logger LoggerInterface */
        foreach ($this->queue as $logger) {
            $logger->log($level, $message, $context);
        }
    }

    /**
     * Add a logger to the queue
     *
     * @param LoggerInterface $logger
     */
    public function add(LoggerInterface $logger)
    {
        $this->queue->attach($logger);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function remove(LoggerInterface $logger)
    {
        $this->queue->detach($logger);
    }
}
