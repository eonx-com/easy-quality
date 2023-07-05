<?php

namespace EonX\EasyQuality\PhpMetricsChecker;

use EonX\EasyQuality\PhpMetricsChecker\Config\ConfigFileReader;
use Exception;
use Hal\Application\Analyze;
use Hal\Application\Config\Config;
use Hal\Application\Config\ConfigException;
use Hal\Application\Config\Validator;
use Hal\Component\File\Finder;
use Hal\Component\Issue\Issuer;
use Hal\Component\Output\TestOutput;
use Hal\Metric\SearchMetric;
use Hal\Report;
use Hal\Search\PatternSearcher;
use Hal\Violation\Violation;
use Hal\Violation\ViolationParser;

final class PhpMetricsChecker
{
    public function run(array $argv): void
    {
        $output = new TestOutput();

        $config = new Config();

        if (count($argv) > 0) {
            foreach ($argv as $arg) {
                if (\preg_match('!\-\-config=(.*)!', $arg, $matches)) {
                    (new ConfigFileReader())->read($config, $matches[1]);
                }
            }
        }

        // @todo: add here excluding files by annotation

        try {
            (new Validator())->validate($config);
        } catch (ConfigException $exception) {
            $output->writeln(\sprintf("\n<error>%s</error>\n", $exception->getMessage()));
            exit(1);
        }

        /** @var string[] $extensions */
        $extensions = $config->get('extensions');
        /** @var string[] $exclude */
        $exclude = $config->get('exclude');
        /** @var string[] $include */
        $include = $config->get('files');
        $files = (new Finder($extensions, $exclude))->fetch($include);

        $issuer = (new Issuer($output));
        try {
            $metrics = (new Analyze($config, $output, $issuer))->run($files);
        } catch (ConfigException $exception) {
            $output->writeln(\sprintf('<error>%s</error>', $exception->getMessage()));
            exit(1);
        }

        /** @var \Hal\Search\Searches $searches */
        $searches = $config->get('searches');
        $searcher = new PatternSearcher();
        $foundSearch = new SearchMetric('searches');
        foreach ($searches->all() as $search) {
            $foundSearch->set($search->getName(), $searcher->executes($search, $metrics));
        }
        $metrics->attach($foundSearch);

        // violations
        (new ViolationParser())->apply($metrics);

        // report
        try {
            (new Report\Cli\Reporter($config, $output))->generate($metrics);
            (new Report\Cli\SearchReporter($config, $output))->generate($metrics);
            (new Report\Html\Reporter($config, $output))->generate($metrics);
            (new Report\Csv\Reporter($config, $output))->generate($metrics);
            (new Report\Json\Reporter($config, $output))->generate($metrics);
            (new Report\Json\SummaryReporter($config, $output))->generate($metrics);
            (new Report\Violations\Xml\Reporter($config, $output))->generate($metrics);
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>Cannot generate report: %s</error>', $e->getMessage()));
            $output->writeln('');
            exit(1);
        }

        // exit status
        $shouldExitDueToCriticalViolationsCount = 0;
        foreach ($metrics->all() as $metric) {
            foreach ($metric->get('violations') as $violation) {
                if (Violation::CRITICAL === $violation->getLevel()) {
                    $shouldExitDueToCriticalViolationsCount++;
                }
            }
        }
        if (!empty($shouldExitDueToCriticalViolationsCount)) {
            $output->writeln('');
            $output->writeln(sprintf('<error>[ERR] Failed du to %d critical violations</error>',
                $shouldExitDueToCriticalViolationsCount));
            $output->writeln('');
            exit(1);
        }

        // end
        $output->writeln('');
        $output->writeln('<success>Done</success>');
        $output->writeln('');
    }
}
