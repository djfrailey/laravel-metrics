<?php

namespace STS\Metrics\Drivers;

use STS\Metrics\Metric;
use Psr\Log\LoggerInterface;
use STS\Metrics\Contracts\Formatter;

/**
 * Class LogDriver
 * @package STS\Metrics\Drivers
 */
class LogDriver extends AbstractDriver
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Formatter
     */
    protected $formatter;

    public function __construct(
        LoggerInterface $logger,
        Formatter $formatter
    )
    {
        $this->logger = $logger;
        $this->formatter = $formatter;
    }

    public function format(Metric $metric)
    {
        return $this->formatter->format($metric);
    }

    public function flush()
    {
        if (!count($this->getMetrics())) {
            return $this;
        }

        foreach ($this->getMetrics() as $metric) {
            $formatted = $this->format($metric);

            if (is_null($formatted)) {
                continue;
            }

            $this->logger->info($formatted);
        }

        $this->metrics = [];

        return $this;
    }
}
