<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff\Fixtures\Correct;

use stdClass;

final class TestClass
{
    public function __construct(
        private int               $param1,
        private array             $param2,
        private readonly string   $param3 = 'some-value',
        public readonly int       $param4,
        private readonly stdClass $param5 = new stdClass(),
        readonly bool             $bool,
        protected readonly mixed  $readonly2,
                                  $readonly,
    )
    {
        // No body needed
    }
}
