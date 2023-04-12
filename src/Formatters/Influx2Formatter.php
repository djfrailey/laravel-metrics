<?php

namespace STS\Metrics\Formatters;

use STS\Metrics\Contracts\Formatter;
use STS\Metrics\Metric;

class Influx2Formatter implements Formatter
{
    public function format(Metric $metric)
    {
        return (new \InfluxDB2\Point(
            $metric->getName(),
            $metric->getTags(),
            array_merge(['value' => $metric->getValue()], $metric->getExtra()),
            $metric->getTimestamp()
        ))->toLineProtocol();
    }
}
