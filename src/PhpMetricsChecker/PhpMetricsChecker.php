<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker;

use EonX\EasyQuality\PhpMetricsChecker\Config\ConfigFileReader;
use EonX\EasyQuality\PhpMetricsChecker\Report\SearchReporter;
use Exception;
use Hal\Application\Analyze;
use Hal\Application\Config\Config;
use Hal\Application\Config\ConfigException;
use Hal\Application\Config\Validator;
use Hal\Component\File\Finder;
use Hal\Component\Issue\Issuer;
use Hal\Component\Output\CliOutput;
use Hal\Metric\SearchMetric;
use Hal\Search\PatternSearcher;

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
        $suppressions = $config->get('suppressions');
        $shouldExitDueToCriticalViolationsCount = 0;
        foreach ($searches->all() as $search) {
            $matchedSearches = $searcher->executes($search, $metrics);
            // Filter the suppressed violations
            foreach ($matchedSearches as $key => $matchedSearch) {
                $matched = $matchedSearch->get('matched-searches');
                foreach ($this->arrayValuesRecursive($matched) as $match) {
                    if (isset($suppressions[$match][$matchedSearch->getName()])) {
                        unset($matchedSearches[$key]);
                    }
                }
            }

            $shouldExitDueToCriticalViolationsCount += \count($matchedSearches);

            $foundSearch->set($search->getName(), $matchedSearches);
        }

        $metrics->attach($foundSearch);

        try {
            (new SearchReporter($config, $output))->generate($metrics);
        } catch (Exception $exception) {
            $output->writeln(\sprintf('<error>Cannot generate report: %s</error>', $exception->getMessage()));
            $output->writeln('');
            exit(1);
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

    /**
     * @return string[]
     */
    private function arrayValuesRecursive(array $array): array
    {
        $result = [];
        foreach (\array_keys($array) as $arrayValue) {
            $value = $array[$arrayValue];

            if (\is_scalar($value)) {
                $result[] = $value;
            }

            if (\is_array($value)) {
                $result = \array_merge($result, $this->arrayValuesRecursive($value));
            }
        }

        return $result;
    }
}
