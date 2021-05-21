<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
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

    /**
     * @var \Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer
     */
    private $testsNodeAnalyzer;

    public function __construct(TestsNodeAnalyzer $testsNodeAnalyzer)
    {
        $this->testsNodeAnalyzer = $testsNodeAnalyzer;
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

    /**
     * {@inheritDoc}
     *
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->testsNodeAnalyzer->isInTestClass($node) === false) {
            return null;
        }

        /** @var \PhpParser\Node\Stmt\Class_ $class */
        $class = $node;

        $this->checkTestMethodsWithDataProvider($class);

        return $node;
    }

    /**
     * Checks dataProvider method has `@see` annotation with test method name.
     */
    private function checkDataProviderMethod(ClassMethod $dataProviderMethod, string $testMethodName): void
    {
        /** @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo $dataProviderDocs */
        $dataProviderDocs = $this->phpDocInfoFactory->createFromNodeOrEmpty($dataProviderMethod);
        if ($dataProviderDocs->hasByName(self::SEE_TAG) === false) {
            if ($dataProviderDocs->getPhpDocNode()->children !== []) {
                $emptyLine = new PhpDocTagNode('', new GenericTagValueNode(''));
                $dataProviderDocs->addPhpDocTagNode($emptyLine);
            }

            $dataProviderDocs->addPhpDocTagNode($this->createSeePhpDocTagNode($testMethodName));

            return;
        }

        if (
            Strings::match(
                (string)$dataProviderDocs->getOriginalPhpDocNode(),
                '/(@see ' . $testMethodName . ')(.*?)/'
            ) === null
        ) {
            $dataProviderDocs->addPhpDocTagNode($this->createSeePhpDocTagNode($testMethodName));
        }
    }

    /**
     * Checks test method.
     */
    private function checkTestMethod(Class_ $class, ClassMethod $classMethod): void
    {
        /** @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo|null $phpDocInfo */
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethod);
        if ($phpDocInfo === null) {
            return;
        }

        $dataProviderTags = $phpDocInfo->getTagsByName(self::DATA_PROVIDER_TAG);

        if ($dataProviderTags === []) {
            return;
        }

        /** @var \PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode $dataProviderTag */
        foreach ($dataProviderTags as $dataProviderTag) {
            $dataProviderMethod = $class->getMethod((string)$dataProviderTag->value);
            if ($dataProviderMethod === null) {
                continue;
            }

            $this->checkDataProviderMethod($dataProviderMethod, (string)$classMethod->name);
        }
    }

    /**
     * Checks test methods with @dataProvider.
     */
    private function checkTestMethodsWithDataProvider(Class_ $class): void
    {
        foreach ($class->getMethods() as $classMethod) {
            if ($this->shouldSkipMethod($classMethod)) {
                continue;
            }

            $this->checkTestMethod($class, $classMethod);
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

        if (Strings::startsWith((string)$classMethod->name, 'test') === false) {
            $shouldSkip = true;
        }

        if ($classMethod->getDocComment() === null) {
            $shouldSkip = true;
        }

        return $shouldSkip;
    }
}
