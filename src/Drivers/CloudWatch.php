<?php

namespace STS\Metrics\Drivers;

use Aws\CloudWatch\CloudWatchClient;
use STS\Metrics\Contracts\HandlesMetrics;
use STS\Metrics\Metric;

/**
 * Class CloudWatch
 * @package STS\Metrics\Drivers
 */
class CloudWatch implements HandlesMetrics
{
    /**
     * @var CloudWatchClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $metrics = [];
    /**
     * @var string
     */
    protected $namespace;

    /**
     * CloudWatch constructor.
     *
     * @param CloudWatchClient $client
     * @param $namespace
     */
    public function __construct(CloudWatchClient $client, $namespace)
    {
        $this->setClient($client);
        $this->namespace = $namespace;
    }

    /**
     * @return CloudWatchClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param CloudWatchClient $client
     */
    public function setClient(CloudWatchClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param $name
     *
     * @return Metric
     */
    public function create($name)
    {
        $metric = new Metric($name, $this);
        $this->metrics[] = &$metric;

        return $metric;
    }

    /**
     * Queue up a metric to be sent later
     *
     * @param Metric $metric
     *
     * @return $this
     */
    public function add(Metric $metric)
    {
        $this->metrics[] = $metric;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    /**
     * Flush all queued metrics to cloudwtach
     *
     * @return $this
     */
    public function flush()
    {
        if(!count($this->getMetrics())) {
            return $this;
        }

        $this->send($this->getMetrics());

        $this->metrics = [];

        return $this;
    }

    /**
     * Send one or more metrics to cloudwatch now
     *
     * @param $metrics
     */
    public function send($metrics)
    {
        $this->getClient()->putMetricData([
            'MetricData' => array_map(function($metric) {
                                return $this->format($metric);
                            }, (array) $metrics),
            'Namespace' => $this->namespace
        ]);
    }

    /**
     * @param Metric $metric
     *
     * @return array
     */
    public function format(Metric $metric)
    {
        return array_filter([
            'MetricName' => $metric->getName(),
            'Dimensions' => $metric->getTags(),
            'StorageResolution' => in_array($metric->getResolution(), [1, 60]) ? $metric->getResolution() : null,
            'Timestamp' => $metric->getTimestamp(),
            'Unit' => $metric->getUnit(),
            'Value' => $metric->getValue()
        ]);
    }

    /**
     * Pass through to the CloudWatch client anything we don't handle.
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->client->$method(...$parameters);
    }
}
