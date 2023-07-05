<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Metric;

use UnexpectedValueException;

abstract class AbstractMetric
{
    protected const NAME = '';

    protected const TYPE_CLASS = 'class';

    final public function getName(): string
    {
        return self::NAME === ''
            ? throw new UnexpectedValueException('You forget to soecify the NAME constant.')
            : self::NAME;
    }

    abstract public function getMetricConfig(): array;
}