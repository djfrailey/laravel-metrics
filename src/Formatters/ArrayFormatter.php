<?php

namespace STS\Metrics\Formatters;

use STS\Metrics\Contracts\Formatter;
use STS\Metrics\Metric;

class ArrayFormatter implements Formatter
{
    public function format(Metric $metric)
    {
        return array_filter([
            'name' => $metric->getName(),
            'value' => $metric->getValue(),
            'resolution' => $metric->getResolution(),
            'unit' => $metric->getUnit(),
            'tags' => $metric->getTags(),
            'extra' => $metric->getExtra(),
            'timestamp' => $metric->getTimestamp()
        ]);
    }
}