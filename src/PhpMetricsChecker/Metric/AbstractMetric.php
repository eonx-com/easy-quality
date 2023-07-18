<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Metric;

use UnexpectedValueException;

abstract class AbstractMetric
{
    public const METRICS_MAP = [
        CyclomaticComplexity::NAME => CyclomaticComplexity::class,
        EfferentCoupling::NAME => EfferentCoupling::class,
    ];

    public const SEARCH_NAME_TO_METRIC_NAME_MAPPING = [
        'cyclomaticComplexity' => 'ccnMethodMax',
        'efferentCoupling' => 'efferentCoupling',
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
