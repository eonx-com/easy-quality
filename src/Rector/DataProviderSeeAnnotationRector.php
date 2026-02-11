<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class DataProviderSeeAnnotationRector extends AbstractRector
{
    /**
     * @var string
     */
    private const string PREFIX_TEST_METHOD = 'test';

    /**
     * @var string
     */
    private const string SEE_TAG = 'see';

    private bool $hasChanged;

    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly PhpDocTagRemover $phpDocTagRemover
    ) {
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Adds @see annotation for data provider methods used with DataProvider attributes '
            . 'and removes redundant ones',
            [
                new CodeSample(
                    <<<'PHP'
/**
 * Provides some data.
 *
 * @return mixed[]
*/
public function provideSomeData(): array
{
}

#[DataProvider('provideSomeData')]
public function testMethod(): void
{
}
PHP
                    ,
                    <<<'PHP'
/**
 * Provides some data.
 *
 * @return mixed[]
 *
 * @see testMethod
*/
public function provideSomeData(): array
{
}

#[DataProvider('provideSomeData')]
public function testMethod(): void
{
}
PHP
                ),
            ]
        );
    }

    public function refactor(Node $node): ?Node
    {
        if ($this->testsNodeAnalyzer->isInTestClass($node) === false) {
            return null;
        }

        /** @var \PhpParser\Node\Stmt\Class_ $classNode */
        $classNode = $node;
        $this->hasChanged = false;

        // First, collect all data provider methods and their test methods
        $dataProviderMap = $this->buildDataProviderMap($classNode);

        // Then, add missing @see annotations and remove redundant ones
        $this->updateDataProviderAnnotations($dataProviderMap, $classNode);

        return $this->hasChanged ? $classNode : null;
    }

    /**
     * Builds a map of data provider methods to their test methods.
     *
     * @return array<string, array<string>>
     */
    private function buildDataProviderMap(Class_ $classNode): array
    {
        $dataProviderMap = [];

        foreach ($classNode->getMethods() as $classMethod) {
            if ($this->shouldSkipMethod($classMethod)) {
                continue;
            }

            $testMethodName = (string)$classMethod->name;

            // Check DataProvider attributes
            foreach ($classMethod->getAttrGroups() as $attrGroup) {
                foreach ($attrGroup->attrs as $attr) {
                    // Check if this is a DataProvider attribute
                    if ($attr->name->toString() !== DataProvider::class) {
                        continue;
                    }

                    // Guard: Check if args exist
                    if ($attr->args === []) {
                        continue;
                    }

                    $firstArg = $attr->args[0];

                    // Guard: Skip named arguments and ensure value is a string
                    if ($firstArg->name !== null || $firstArg->value instanceof String_ === false) {
                        continue;
                    }

                    $dataProviderName = $firstArg->value->value;
                    if ($classNode->getMethod($dataProviderName) !== null) {
                        $dataProviderMap[$dataProviderName][] = $testMethodName;
                    }
                }
            }
        }

        return $dataProviderMap;
    }

    /**
     * Creates `@see` PHPDoc tag.
     */
    private function createSeePhpDocTagNode(string $testMethod): PhpDocTagNode
    {
        return new PhpDocTagNode('@' . self::SEE_TAG, new GenericTagValueNode($testMethod));
    }

    private function shouldSkipMethod(ClassMethod $classMethod): bool
    {
        if ($classMethod->isPublic() === false) {
            return true;
        }

        if (\str_starts_with((string)$classMethod->name, self::PREFIX_TEST_METHOD) === false) {
            return true;
        }

        return false;
    }

    /**
     * Updates @see annotations for all data provider methods.
     *
     * @param array<string, array<string>> $dataProviderMap
     */
    private function updateDataProviderAnnotations(array $dataProviderMap, Class_ $classNode): void
    {
        foreach ($dataProviderMap as $dataProviderName => $testMethodNames) {
            $dataProviderMethod = $classNode->getMethod($dataProviderName);

            if ($dataProviderMethod === null) {
                continue;
            }

            $this->updateDataProviderMethod($dataProviderMethod, $testMethodNames);
        }
    }

    /**
     * Updates @see annotations for a data provider method.
     *
     * @param array<string> $expectedTestMethods
     */
    private function updateDataProviderMethod(ClassMethod $dataProviderMethod, array $expectedTestMethods): void
    {
        // De-duplicate expected test methods to prevent duplicate @see tags
        $expectedTestMethods = \array_unique($expectedTestMethods);

        $dataProviderDocs = $this->phpDocInfoFactory->createFromNodeOrEmpty($dataProviderMethod);
        $hasLocalChange = false;

        // Collect and categorize existing @see tags in a single pass
        $existingSeeTags = $dataProviderDocs->getTagsByName(self::SEE_TAG);
        $validExistingMethods = [];
        $tagsToRemove = [];

        foreach ($existingSeeTags as $seeTag) {
            if ($seeTag->value instanceof GenericTagValueNode) {
                $seeValue = $seeTag->value->value;
                if (\in_array($seeValue, $expectedTestMethods, true)) {
                    $validExistingMethods[] = $seeValue;
                } else {
                    $tagsToRemove[] = $seeTag;
                }
            }
        }

        // Remove redundant @see tags
        foreach ($tagsToRemove as $tagToRemove) {
            if ($this->phpDocTagRemover->removeTagValueFromNode($dataProviderDocs, $tagToRemove)) {
                $hasLocalChange = true;
            }
        }

        // Calculate missing @see tags
        $missingTestMethods = \array_diff($expectedTestMethods, $validExistingMethods);

        // Add blank line separator before new tags if needed
        if ($missingTestMethods !== [] && $dataProviderDocs->getPhpDocNode()->children !== []) {
            $dataProviderDocs->addPhpDocTagNode(new PhpDocTextNode(''));
            $hasLocalChange = true;
        }

        // Add missing @see tags
        foreach ($missingTestMethods as $testMethodName) {
            $dataProviderDocs->addPhpDocTagNode($this->createSeePhpDocTagNode($testMethodName));
            $hasLocalChange = true;
        }

        if ($hasLocalChange) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($dataProviderMethod);
            $this->hasChanged = true;
        }
    }
}
