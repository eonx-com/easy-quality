<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Search;

use EonX\EasyQuality\PhpMetricsChecker\Metric\AbstractMetric;
use Hal\Search\Search;
use Hal\Search\Searches;

final class SearchesFactory
{
    public function factory(array $metrics): Searches
    {
        $searches = new Searches();
        foreach ($metrics as $metric => $metricConfig) {
            foreach (AbstractMetric::METRICS_MAP as $metricName => $metricClass) {
                if ($metric === $metricName) {
                    $appMetric = new $metricClass($metricConfig);

                    $searches->add(new Search($appMetric->getName(), $appMetric->getMetricConfig()));
                }
            }
        }

        return $searches;
    }
}
