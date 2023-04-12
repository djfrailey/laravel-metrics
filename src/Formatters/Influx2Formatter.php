<?php

namespace STS\Metrics\Formatters;

use STS\Metrics\Adapters\InfluxDB2Adapter;
use STS\Metrics\Contracts\Formatter;
use STS\Metrics\Metric;

class Influx2Formatter implements Formatter
{

    protected $adapter;

    public function __construct(InfluxDB2Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function format(Metric $metric)
    {
        return $this->adapter->point(
            $metric->getName(),
            $metric->getValue(),
            $metric->getTags(),
            $metric->getExtra(),
            $metric->getTimestamp()
        )->toLineProtocol();
    }
}