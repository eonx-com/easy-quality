<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Metric;

use UnexpectedValueException;

final class EfferentCoupling extends AbstractMetric
{
    protected const NAME = 'efferentCoupling';

    private const MAX_VALUE_DEFAULT = 12;

    private int $maxValue;

    public function __construct(array $config)
    {
        $this->maxValue = $config['maxValue'] ?? self::MAX_VALUE_DEFAULT;
    }

    public function getMaxValue(): int
    {
        return $this->maxValue;
    }

    public function getMetricConfig(): array
    {
        return [
            'type' => parent::TYPE_CLASS,
            self::NAME => "> $this->maxValue",
            'failIfFound' => false,
        ];
    }
}
