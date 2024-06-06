<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AddSeeAnnotationRector extends AbstractRector
{
    /**
     * @var string
     */
    private const DATA_PROVIDER_TAG = 'dataProvider';

    /**
     * @var string
     */
    private const SEE_TAG = 'see';

    private bool $hasChanged;

    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly DocBlockUpdater $docBlockUpdater
    ) {
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Adds @see annotation for data providers',
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

        $this->checkTestMethodsWithDataProvider($classNode);

        return $this->hasChanged ? $classNode : null;
    }

    /**
     * Checks dataProvider method has `@see` annotation with test method name.
     */
    private function checkDataProviderMethod(ClassMethod $dataProviderMethod, string $testMethodName): void
    {
        $dataProviderDocs = $this->phpDocInfoFactory->createFromNodeOrEmpty($dataProviderMethod);

        if ($dataProviderDocs->hasByName(self::SEE_TAG) === false) {
            if ($dataProviderDocs->getPhpDocNode()->children !== []) {
                $dataProviderDocs->addPhpDocTagNode(new PhpDocTextNode(''));
            }

            $dataProviderDocs->addPhpDocTagNode($this->createSeePhpDocTagNode($testMethodName));

            $this->hasChanged = true;
        }

        if ($dataProviderDocs->hasByName(self::SEE_TAG)) {
            $tagAlreadyExist = false;

            foreach ($dataProviderDocs->getTagsByName(self::SEE_TAG) as $seeTag) {
                if ($seeTag->value instanceof GenericTagValueNode && $seeTag->value->value === $testMethodName) {
                    $tagAlreadyExist = true;
                }
            }

            if ($tagAlreadyExist === false) {
                $dataProviderDocs->addPhpDocTagNode($this->createSeePhpDocTagNode($testMethodName));

                $this->hasChanged = true;
            }
        }

        if ($this->hasChanged) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($dataProviderMethod);
        }
    }

    /**
     * Checks test method.
     */
    private function checkTestMethod(Class_ $classNode, ClassMethod $classMethod): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethod);

        $dataProviderTags = $phpDocInfo->getTagsByName(self::DATA_PROVIDER_TAG);

        foreach ($dataProviderTags as $dataProviderTag) {
            $dataProviderMethod = $classNode->getMethod((string)$dataProviderTag->value);
            if ($dataProviderMethod === null) {
                continue;
            }

            $this->checkDataProviderMethod($dataProviderMethod, (string)$classMethod->name);
        }

        foreach ($classMethod->getAttrGroups() as $attrGroup) {
            if ($attrGroup->attrs[0]->name->toString() === 'PHPUnit\Framework\Attributes\DataProvider') {
                $dataProviderMethod = $classNode->getMethod($attrGroup->attrs[0]?->args[0]?->value->value ?? '');
                if ($dataProviderMethod === null) {
                    continue;
                }

                $this->checkDataProviderMethod($dataProviderMethod, (string)$classMethod->name);
            }
        }
    }

    /**
     * Checks test methods with @dataProvider.
     */
    private function checkTestMethodsWithDataProvider(Class_ $classNode): void
    {
        foreach ($classNode->getMethods() as $classMethod) {
            if ($this->shouldSkipMethod($classMethod)) {
                continue;
            }

            $this->checkTestMethod($classNode, $classMethod);
        }
    }

    /**
     * Creates `@see` PHPDoc tag.
     */
    private function createSeePhpDocTagNode(string $testMethod): PhpDocTagNode
    {
        return new PhpDocTagNode('@' . self::SEE_TAG, new GenericTagValueNode($testMethod));
    }

    /**
     * Returns true if method should be skipped.
     */
    private function shouldSkipMethod(ClassMethod $classMethod): bool
    {
        $shouldSkip = false;

        if ($classMethod->isPublic() === false) {
            $shouldSkip = true;
        }

        if (\str_starts_with((string)$classMethod->name, 'test') === false) {
            $shouldSkip = true;
        }

        return $shouldSkip;
    }
}
