<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniff\ValueObject;

final class SetList
{
    /**
     * @var string
     */
    public const EONX = __DIR__ . '/../../../config/ecs/eonx-set.php';

    /**
     * @var string
     */
    public const PHP_CODESNIFFER = __DIR__ . '/../../../config/ecs/php-codesniffer-set.php';

    /**
     * @var string
     */
    public const PHP_CS_FIXER = __DIR__ . '/../../../config/ecs/php-cs-fixer-set.php';

    /**
     * @var string
     */
    public const SLEVOMAT_CODING_STANDARD = __DIR__ . '/../../../config/ecs/slevomat-coding-standard-set.php';
}
