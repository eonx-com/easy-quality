<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Config;

use EonX\EasyQuality\PhpMetricsChecker\Search\SearchesFactory;
use Hal\Application\Config\Config;
use InvalidArgumentException;

final class ConfigFileReader
{
    public function read(Config $config, string $fileName): void
    {
        $jsonText = \file_get_contents($fileName);

        if ($jsonText === false) {
            throw new InvalidArgumentException("Cannot read configuration file '$fileName'");
        }

        $jsonData = \json_decode($jsonText, true, 512, \JSON_THROW_ON_ERROR);

        $this->parseJson($config, $jsonData, $fileName);
    }

    protected function parseJson(Config $config, array $jsonData, string $fileName): void
    {
        if (isset($jsonData['includes'])) {
            $includes = $jsonData['includes'];
            $files = [];
            foreach ($includes as $include) {
                $include = $this->resolvePath($include, $fileName);
                $files[] = $include;
            }
            $config->set('files', $files);
        }

        $config->set('extensions', 'php');

        $config->set('composer', false);

        $metrics = $jsonData['metrics'] ?? [];
        $config->set('searches', (new SearchesFactory())->build($metrics));

        $config->set('suppressions', []);
        foreach ($metrics as $metricName => $metric) {
            if (isset($metric['exclude']) && \is_array($metric['exclude'])) {
                $config->set(
                    'suppressions',
                    \array_merge((array)$config->get('suppressions'), [$metricName => $metric['exclude']])
                );
            }
            $config->set($metric->getName(), $metric->getMetricConfig());
        }
    }

    private function resolvePath(string $path, string $fileName): string
    {
        if ($path[0] !== \DIRECTORY_SEPARATOR) {
            $path = \dirname($fileName) . \DIRECTORY_SEPARATOR . $path;
        }

        return $path;
    }
}
