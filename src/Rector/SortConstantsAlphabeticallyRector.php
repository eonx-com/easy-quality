<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\Rector\EasyRankeable\ClassConstantEasyRankeable;
use EonX\EasyQuality\Rector\ValueObject\SortConstantsAlphabetically;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassLike;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Order\Contract\RankeableInterface;
use Rector\Order\StmtOrder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

final class SortConstantsAlphabeticallyRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const RANKEABLE_CLASS = 'rankeable_class';

    /**
     * @var string
     */
    private $rankeableClass = ClassConstantEasyRankeable::class;

    /**
     * @var \Rector\Order\StmtOrder
     */
    private $stmtOrder;

    public function __construct(StmtOrder $stmtOrder)
    {
        $this->stmtOrder = $stmtOrder;
    }
    
    public function configure(array $configuration): void
    {
        /** @var SortConstantsAlphabetically|null $sortConstAlphabetically */
        $sortConstAlphabetically = $configuration[self::RANKEABLE_CLASS] ?? null;
        Assert::isInstanceOf($sortConstAlphabetically, SortConstantsAlphabetically::class);

        $rankeableClass = $sortConstAlphabetically->getRankeableClass();
        Assert::string($rankeableClass);
        $this->rankeableClass = $rankeableClass;
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Sorts constants alphabetically', [
            new ConfiguredCodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    private const CONST2 = 'some-value';
    
    private const CONST1 = 'some-value';
}
CODE_SAMPLE
                , <<<'CODE_SAMPLE'
class SomeClass
{
    private const CONST1 = 'some-value';
    
    private const CONST2 = 'some-value';
}
CODE_SAMPLE,
                [
                    self::RANKEABLE_CLASS => new SortConstantsAlphabetically('RankeableInterface'),
                ]
            )
        ]);
    }

    public function refactor(Node $node)
    {
        /** @var ClassLike $classLike */
        $classLike = $node;

        $sorted = $this->sort($classLike);
        $current = $this->stmtOrder->getStmtsOfTypeOrder($classLike, Node\Stmt\ClassConst::class);

        $oldToNewKeys = $this->stmtOrder->createOldToNewKeys($sorted, $current);
        $this->stmtOrder->reorderClassStmtsByOldToNewKeys($classLike, $oldToNewKeys);

        return $node;
    }

    /**
     * @param RankeableInterface[] $rankeables
     *
     * @return string[]
     */
    private function sortByRanksAndGetNames(array $rankeables): array
    {
        \uasort(
            $rankeables,
            static function (RankeableInterface $firstRankeable, RankeableInterface $secondRankeable): int {
                return $firstRankeable->getRanks() <=> $secondRankeable->getRanks();
            }
        );

        $names = [];
        foreach ($rankeables as $rankeable) {
            $names[] = $rankeable->getName();
        }

        return $names;
    }

    /**
     * @return string[]
     */
    private function sort(ClassLike $classLike): array
    {
        $rankeables = [];
        foreach ($classLike->stmts as $classStmt) {
            if (!$classStmt instanceof ClassConst) {
                continue;
            }

            $rankeables[] = new $this->rankeableClass($classStmt);
        }

        return $this->sortByRanksAndGetNames($rankeables);
    }
}
