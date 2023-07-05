<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Config;

use Hal\Application\Config\Config;
use Hal\Search\SearchesFactory;
use InvalidArgumentException;

final class ConfigFileReader
{
    public function read(Config $config, string $fileName): void
    {
        $jsonText = \file_get_contents($fileName);

        if ($jsonText === false) {
            throw new InvalidArgumentException("Cannot read configuration file '$fileName'");
        }

        $jsonData = \json_decode($jsonText, true);

        $this->parseJson($jsonData, $config, $fileName);
    }

    private function resolvePath(string $path, string $fileName): string
    {
        if ($path[0] !== \DIRECTORY_SEPARATOR) {
            $path = \dirname($fileName) . \DIRECTORY_SEPARATOR . $path;
        }

        return $path;
    }

    protected function parseJson(mixed $jsonData, Config $config, string $fileName): void
    {
        if ($jsonData === false || $jsonData === null) {
            throw new InvalidArgumentException("Bad config file '$fileName'");
        }

        if (isset($jsonData['includes'])) {
            $includes = $jsonData['includes'];
            $files = [];
            foreach ($includes as $include) {
                $include = $this->resolvePath($include, $fileName);
                $files[] = $include;
            }
            $config->set('files', $files);
        }

        if (isset($jsonData['excludes'])) {
            $config->set('exclude', \implode(',', $jsonData['excludes']));
        }

        $config->set('extensions','php');

        $config->set('composer', false);

        if (isset($jsonData['searches']) === false) {
            $jsonData['searches'] = [];
        }
        $factory = new SearchesFactory();
        $config->set('searches', $factory->factory($jsonData['searches']));
    }
}
