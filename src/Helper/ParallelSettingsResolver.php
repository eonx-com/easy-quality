<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Helper;

final class ParallelSettingsResolver
{
    private const DEFAULT_JOB_SIZE = 2;

    private const DEFAULT_MAX_NUMBER_OF_PROCESS = 4;

    private const DEFAULT_TIMEOUT_SECONDS = 120;

    private const ENV_JOB_SIZE = 'EONX_EASY_QUALITY_JOB_SIZE';

    private const ENV_MAX_NUMBER_OF_PROCESS = 'EONX_EASY_QUALITY_MAX_NUMBER_OF_PROCESS';

    private const ENV_TIMEOUT_SECONDS = 'EONX_EASY_QUALITY_TIMEOUT_SECONDS';

    public static function resolveJobSize(): int
    {
        $jobSize = \getenv(self::ENV_JOB_SIZE);

        return $jobSize ? (int)$jobSize : self::DEFAULT_JOB_SIZE;
    }

    public static function resolveMaxNumberOfProcess(): int
    {
        $maxNumberOfProcess = \getenv(self::ENV_MAX_NUMBER_OF_PROCESS);

        return $maxNumberOfProcess ? (int)$maxNumberOfProcess : self::DEFAULT_MAX_NUMBER_OF_PROCESS;
    }

    public static function resolveTimeoutSeconds(): int
    {
        $timeoutSeconds = \getenv(self::ENV_TIMEOUT_SECONDS);

        return $timeoutSeconds ? (int)$timeoutSeconds : self::DEFAULT_TIMEOUT_SECONDS;
    }
}
