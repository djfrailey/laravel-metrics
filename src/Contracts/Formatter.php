<?php

namespace STS\Metrics\Contracts;

use STS\Metrics\Metric;

interface Formatter
{
    public function format(Metric $metric);
}