<?php
declare(strict_types=1);

namespace EonX\EasyQuality\ValueObject;

final class EasyQualitySetList
{
    public const string ECS = __DIR__ . '/../../config/ecs/eonx-set.php';

    public const string RECTOR = __DIR__ . '/../../config/rector/eonx-set.php';

    public const string RECTOR_PHPUNIT_10 = __DIR__ . '/../../config/rector/eonx-phpunit10-set.php';

    public const string RECTOR_PHPUNIT_12 = __DIR__ . '/../../config/rector/eonx-phpunit12-set.php';
}
