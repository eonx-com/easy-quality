<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\Rector\EasyRankeable\ClassMethodEasyRankeable;
use EonX\EasyQuality\Rector\Order\Contract\RankeableInterface;
use EonX\EasyQuality\Rector\ValueObject\SortMethodsAlphabetically;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Trait_;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Order\StmtOrder;
use RectorPrefix20220126\Webmozart\Assert\Assert;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SortMethodsAlphabeticallyRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const RANKEABLE_CLASS = 'rankeable_class';

    /**
     * @var string
     */
    private $rankeableClass = ClassMethodEasyRankeable::class;

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
        /** @var SortMethodsAlphabetically|null $sortMethodsAlphabetically */
        $sortMethodsAlphabetically = $configuration[self::RANKEABLE_CLASS] ?? null;
        Assert::isInstanceOf($sortMethodsAlphabetically, SortMethodsAlphabetically::class);

        $rankeableClass = $sortMethodsAlphabetically->getRankeableClass();
        Assert::string($rankeableClass);
        $this->rankeableClass = $rankeableClass;
    }

    public function getNodeTypes(): array
    {
        return [Class_::class, Trait_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Sorts methods alphabetically', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private function call2()
    {
    }

    private function call1()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private function call1()
    {
    }

    private function call2()
    {
    }
}
CODE_SAMPLE,
                [
                    self::RANKEABLE_CLASS => new SortMethodsAlphabetically('RankeableInterface'),
                ]
            )
        ]);
    }

    public function refactor(Node $node)
    {
        /** @var ClassLike $classLike */
        $classLike = $node;

        $sorted = $this->sort($classLike);
        $current = $this->stmtOrder->getStmtsOfTypeOrder($classLike, ClassMethod::class);

        $oldToNewKeys = $this->stmtOrder->createOldToNewKeys($sorted, $current);
        $this->stmtOrder->reorderClassStmtsByOldToNewKeys($classLike, $oldToNewKeys);

        return $node;
    }

    /**
     * @param \EonX\EasyQuality\Rector\Order\Contract\RankeableInterface[] $rankeables
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
            if (!$classStmt instanceof ClassMethod) {
                continue;
            }

            $rankeables[] = new $this->rankeableClass($classStmt);
        }

        return $this->sortByRanksAndGetNames($rankeables);
    }
}