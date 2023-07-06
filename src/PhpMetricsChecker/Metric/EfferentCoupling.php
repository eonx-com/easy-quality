<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Metric;

final class EfferentCoupling extends AbstractMetric
{
    protected const NAME = 'efferentCoupling';

    private const MAX_VALUE_DEFAULT = 12;

    private readonly int $maxValue;

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
            'failIfFound' => true,
            self::NAME => ">$this->maxValue",
            'type' => parent::TYPE_CLASS,
        ];
    }
}
