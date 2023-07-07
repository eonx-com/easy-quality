<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker;

use EonX\EasyQuality\PhpMetricsChecker\Config\ConfigFileReader;
use Exception;
use Hal\Application\Analyze;
use Hal\Application\Config\Config;
use Hal\Application\Config\ConfigException;
use Hal\Application\Config\Validator;
use Hal\Component\File\Finder;
use Hal\Component\Issue\Issuer;
use Hal\Component\Output\CliOutput;
use Hal\Metric\SearchMetric;
use Hal\Report\Cli\SearchReporter;
use Hal\Search\PatternSearcher;
use Hal\Violation\Violation;
use Hal\Violation\ViolationParser;

final class PhpMetricsChecker
{
    private const DEFAULT_CONFIG_FILE = 'pmc.json';

    public function run(array $argv): void
    {
        $output = new CliOutput();

        $config = new Config();

        if (\count($argv) === 1) {
            (new ConfigFileReader())->read($config, self::DEFAULT_CONFIG_FILE);
        }

        if (\count($argv) > 1) {
            foreach ($argv as $arg) {
                if (\preg_match('!\-\-config=(.*)!', (string)$arg, $matches)) {
                    (new ConfigFileReader())->read($config, $matches[1]);
                }
            }
        }

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

        (new ViolationParser())->apply($metrics);

        try {
            (new SearchReporter($config, $output))->generate($metrics);
        } catch (Exception $exception) {
            $output->writeln(\sprintf('<error>Cannot generate report: %s</error>', $exception->getMessage()));
            $output->writeln('');
            exit(1);
        }

        $shouldExitDueToCriticalViolationsCount = 0;
        foreach ($metrics->all() as $metric) {
            foreach ($metric->get('violations') as $violation) {
                if ($violation->getLevel() === Violation::CRITICAL) {
                    $shouldExitDueToCriticalViolationsCount++;
                }
            }
        }
        if ($shouldExitDueToCriticalViolationsCount > 0) {
            $output->writeln('');
            $output->writeln(\sprintf(
                '<error>[ERR] Failed due to %d violations</error>',
                $shouldExitDueToCriticalViolationsCount
            ));
            $output->writeln('');
            exit(1);
        }

        $output->writeln('');
        $output->writeln('<success>Done</success>');
        $output->writeln('');
    }
}
