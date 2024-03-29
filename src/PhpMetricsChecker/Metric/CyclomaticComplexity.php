<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpMetricsChecker\Metric;

final class CyclomaticComplexity extends AbstractMetric
{
    protected const NAME = 'cyclomaticComplexity';

    private const MAX_VALUE_DEFAULT = 10;

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
            'ccnMethodMax' => ">$this->maxValue",
            'failIfFound' => true,
            'type' => parent::TYPE_CLASS,
        ];
    }
}
