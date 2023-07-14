<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Report;

use Hal\Application\Config\Config;
use Hal\Component\Output\Output;
use Hal\Metric\Metrics;

final class SearchReporter
{
    public function __construct(private readonly Config $config, private readonly Output $output)
    {
    }

    public function generate(Metrics $metrics): void
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

    private function displayCliReport(string $searchName, array $foundSearch): void
    {
        $title = \sprintf(
            '<info>Found %d occurrences for search "%s"</info>',
            \count($foundSearch),
            $searchName
        );

        $config = $this->config->get('searches')->get($searchName)->getConfig();
        if (isset($config->failIfFound) && $config->failIfFound && \count($foundSearch) > 0) {
            $title = \sprintf(
                '<error>[ERR] Found %d occurrences for search "%s". Maximum allowed value is %d.</error>',
                \count($foundSearch),
                $searchName,
                \filter_var($config->$searchName, \FILTER_SANITIZE_NUMBER_INT)
            );
        }

        $this->output->writeln($title);
        foreach ($foundSearch as $found) {
            $this->output->writeln(\sprintf('- %s (%d)', $found->getName(), $found->get($searchName)));
        }
        $this->output->writeln(\PHP_EOL);
    }
}
