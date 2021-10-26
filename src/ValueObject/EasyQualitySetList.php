<?php
declare(strict_types=1);

namespace EonX\EasyQuality\ValueObject;

final class EasyQualitySetList
{
    /**
     * @var string
     */
    public const ECS = __DIR__ . '/../../../config/ecs/eonx-set.php';

    /**
     * @var string
     */
    public const RECTOR = __DIR__ . '/../../../config/rector/eonx-set.php';
}
