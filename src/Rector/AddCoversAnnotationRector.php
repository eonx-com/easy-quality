<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AddCoversAnnotationRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const REPLACE_ARRAY = 'replace_array';

    /**
     * @var string[]
     */
    private array $replaceArray;

    public function __construct(private readonly TestsNodeAnalyzer $testsNodeAnalyzer)
    {
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $this->replaceArray = $configuration[self::REPLACE_ARRAY] ?? [];
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Adds @covers annotation for test classes',
            [
                new ConfiguredCodeSample(
                    <<<'PHP_WRAP'
class SomeServiceTest extends \PHPUnit\Framework\TestCase
{
}
PHP_WRAP

                    ,
                    <<<'PHP_WRAP'
/**
 * @covers \SomeService
*/
class SomeServiceTest extends \PHPUnit\Framework\TestCase
{
}
PHP_WRAP

                    ,
                    [
                        self::REPLACE_ARRAY => ['SomeTextToReplace'],
                    ]
                ),
            ]
        );
    }

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
        $className = (string)\preg_replace('/Test$/', '', \str_replace($this->replaceArray ?? [], '', $className));

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

            if (\preg_match('/(@covers|@coversNothing)(.*?)/i', $docComment->getText()) === 1) {
                return true;
            }
        }

        return false;
    }
}
