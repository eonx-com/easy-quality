<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Search;

use Hal\Search\Search;
use Hal\Search\Searches;

final class SearchesFactory
{
    public function build(array $metrics): Searches
    {
        $searches = new Searches();
        /** @var \EonX\EasyQuality\PhpMetricsChecker\Metric\AbstractMetric $metric */
        foreach ($metrics as $metric) {
            $searches->add(new Search($metric->getName(), $metric->getMetricConfig()));
        }

       return $searches;
    }
}
