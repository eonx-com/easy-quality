<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @codeCoverageIgnore
 *
 * @see \EonX\EasyQuality\Tests\Rector\AddCoversAnnotationRector\AddCoversAnnotationRectorTest
 */
final class AddCoversAnnotationRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const REPLACE_ARRAY = 'replace_array';

    /**
     * @var string[]
     */
    private $replaceArray;

    /**
     * @var \Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer
     */
    private $testsNodeAnalyzer;

    public function __construct(TestsNodeAnalyzer $testsNodeAnalyzer)
    {
        $this->testsNodeAnalyzer = $testsNodeAnalyzer;
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $this->replaceArray = $configuration[self::REPLACE_ARRAY] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Adds @covers annotation for test classes',
            [
                new CodeSample(
                    <<<'PHP'
class SomeServiceTest extends \PHPUnit\Framework\TestCase
{
}
PHP
                    ,
                    <<<'PHP'
/**
 * @covers \SomeService
*/
class SomeServiceTest extends \PHPUnit\Framework\TestCase
{
}
PHP
                ),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function refactor(Node $node): ?Node
    {
        /** @var \PhpParser\Node\Stmt\Class_ $classNode */
        $classNode = $node;

        if ($this->shouldSkipClass($classNode)) {
            return null;
        }

        $coveredClass = $this->resolveCoveredClassName((string)$this->getName($node));

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classNode);

        if ($coveredClass === null) {
            return null;
        }

        $phpDocInfo->addPhpDocTagNode($this->createCoversPhpDocTagNode($coveredClass));

        return $classNode;
    }

    /**
     * Creates `@covers` PHPDoc tag.
     */
    private function createCoversPhpDocTagNode(string $className): PhpDocTagNode
    {
        return new PhpDocTagNode('@covers', new GenericTagValueNode('\\' . $className));
    }

    /**
     * Resolves covered class name.
     */
    private function resolveCoveredClassName(string $className): ?string
    {
        $className = (string)\preg_replace('/Test$/', '', \str_replace($this->replaceArray, '', $className));

        if (\class_exists($className)) {
            return $className;
        }

        return null;
    }

    /**
     * Returns true if class should be skipped.
     */
    private function shouldSkipClass(Class_ $class): bool
    {
        $className = $this->getName($class);

        if ($className === null || $class->isAnonymous() === true || $class->isAbstract()) {
            return true;
        }

        if ($this->testsNodeAnalyzer->isInTestClass($class) === false) {
            return true;
        }

        // Is the @covers or annotation already added
        if ($class->getDocComment() !== null) {
            /** @var \PhpParser\Comment\Doc $docComment */
            $docComment = $class->getDocComment();

            if (Strings::match($docComment->getText(), '/(@covers|@coversNothing)(.*?)/i') !== null) {
                return true;
            }
        }

        return false;
    }
}
