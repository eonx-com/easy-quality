<?php
declare(strict_types=1);

namespace EonX\EasyQuality\ValueObject;

final class EasyQualitySetList
{
    public const ECS = __DIR__ . '/../../config/ecs/eonx-set.php';

    public const RECTOR = __DIR__ . '/../../config/rector/eonx-set.php';

    public const RECTOR_PHPUNIT_10 = __DIR__ . '/../../config/rector/eonx-phpunit10-set.php';
}
