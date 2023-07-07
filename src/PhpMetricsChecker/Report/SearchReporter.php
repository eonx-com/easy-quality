<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Report;

use Hal\Application\Config\Config;
use Hal\Component\Output\Output;
use Hal\Metric\Metrics;

final class SearchReporter
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Output
     */
    private $output;

    /**
     * @param Config $config
     * @param Output $output
     */
    public function __construct(Config $config, Output $output)
    {
        $this->config = $config;
        $this->output = $output;
    }

    /**
     * @param Metrics $metrics
     */
    public function generate(Metrics $metrics)
    {
        $searches = $metrics->get('searches');
        if ($searches === null) {
            return;
        }

        foreach ($searches->all() as $name => $search) {

            if (\is_array($search) === false) {
                continue;
            }

            $this->displayCliReport($name, $search);
        }
    }

    private function displayCliReport(string $searchName, array $foundSearch)
    {
        $title = \sprintf(
            '<info>Found %d occurrences for search "%s"</info>',
            sizeof($foundSearch),
            $searchName
        );

        $config = $this->config->get('searches')->get($searchName)->getConfig();
        if (\count($foundSearch) > 0 && isset($config->failIfFound) && $config->failIfFound) {
            $title = \sprintf(
                '<error>[ERR] Found %d occurrences for search "%s"</error>',
                sizeof($foundSearch),
                $searchName
            );
        }

        $this->output->writeln($title);
        foreach ($foundSearch as $found) {
            $this->output->writeln(\sprintf('- %s (%d)', $found->getName(), $found->get($searchName)));
        }
        $this->output->writeln(\PHP_EOL);
    }
}
