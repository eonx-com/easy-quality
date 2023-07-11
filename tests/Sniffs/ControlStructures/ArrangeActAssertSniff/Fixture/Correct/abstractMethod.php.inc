<?php
declare(strict_types=1);

namespace App\Tests\Quality\Ecs\ArrangeActAssertSniff\Correct;

abstract class AbstractTestCase
{
    abstract public function testSomething(): void;
}
