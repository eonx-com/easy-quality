<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\SingleLineCommentRector\Fixture;

class ClassHasCorrectSingleLineComments
{
    private const TEST_CONST = 'const';

    private $test;

    private $trait;

    public function getTest(): string
    {
        return $this->test;
    }

    public function setTest2(bool $test2): self
    {
        // Some comment
        $this->test = (bool)\array_map(function ($elem) {
            $var = [
                '1',
                '2' => [
                    '4' => $this->getTest(),
                ],
            ];
            foreach ($var as $key => $value) { // 1-st start
                if (\is_array($value)) {
                    // Try to loop
                    foreach ($value as $key2 => $value2) { // 2-nd start
                        // Do nothing
                        continue;
                    } // 2-nd end

                    $test3['123'] = '123';
                }
            } // First end

        }, [$test2]);

        return $this;
    }
}
