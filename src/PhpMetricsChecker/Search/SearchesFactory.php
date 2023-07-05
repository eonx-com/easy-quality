<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Search;

use EonX\EasyQuality\PhpMetricsChecker\Metric\EfferentCoupling;
use Hal\Search\Search;
use Hal\Search\Searches;

final class SearchesFactory
{
    public function build(array $metrics): Searches
    {
        $searches = new Searches();
        foreach ($metrics as $metric) {
            $metric = new EfferentCoupling($metric);
            $searches->add(new Search($metric->getName(), $metric->getMetricConfig()));
        }

        return $searches;
    }
}
