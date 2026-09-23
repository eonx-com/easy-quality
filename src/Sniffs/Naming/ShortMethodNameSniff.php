<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ShortMethodNameSniff implements Sniff
{
    public const string CODE_SHORT_METHOD_NAME = 'ShortMethodName';

    /**
     * @var string[]
     */
    public array $exceptions = [];

    public int $minimum = 3;

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $name = $phpcsFile->getDeclarationName($stackPtr);

        if ($name === '' || \strlen($name) >= $this->minimum || \in_array($name, $this->exceptions, true)) {
            return;
        }

        $phpcsFile->addError(
            \sprintf('Name "%s" is too short, use at least %d characters', $name, $this->minimum),
            $stackPtr,
            self::CODE_SHORT_METHOD_NAME,
        );
    }

    /**
     * @return list<int|string>
     */
    public function register(): array
    {
        return [\T_FUNCTION];
    }
}
