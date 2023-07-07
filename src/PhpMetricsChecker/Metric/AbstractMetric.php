<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Metric;

use UnexpectedValueException;

abstract class AbstractMetric
{
    public const METRICS_MAP = [
        CcnMethodMax::NAME => CcnMethodMax::class,
        EfferentCoupling::NAME => EfferentCoupling::class,
    ];

    protected const NAME = '';

    protected const TYPE_CLASS = 'class';

    abstract public function getMetricConfig(): array;

    final public function getName(): string
    {
        return static::NAME === self::NAME
            ? throw new UnexpectedValueException(
                \sprintf('You forget to specify the NAME constant for %s metric.', static::class)
            )
            : static::NAME;
    }
}
