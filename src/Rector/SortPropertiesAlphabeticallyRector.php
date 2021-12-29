<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\Rector\EasyRankeable\PropertyEasyRankeable;
use EonX\EasyQuality\Rector\ValueObject\SortPropertiesAlphabetically;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Trait_;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Order\Contract\RankeableInterface;
use Rector\Order\StmtOrder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

final class SortPropertiesAlphabeticallyRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const RANKEABLE_CLASS = 'rankeable_class';

    /**
     * @var string
     */
    private $rankeableClass = PropertyEasyRankeable::class;

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
        /** @var SortPropertiesAlphabetically|null $sortPropertiesAlphabetically */
        $sortPropertiesAlphabetically = $configuration[self::RANKEABLE_CLASS] ?? null;
        Assert::isInstanceOf($sortPropertiesAlphabetically, SortPropertiesAlphabetically::class);

        $rankeableClass = $sortPropertiesAlphabetically->getRankeableCLass();
        Assert::string($rankeableClass);
        $this->rankeableClass = $rankeableClass;
    }

    public function getNodeTypes(): array
    {
        return [Class_::class, Trait_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Sorts properties alphabetically', [
            new ConfiguredCodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    private $property2;
    
    private $property1;
}
CODE_SAMPLE
                , <<<'CODE_SAMPLE'
class SomeClass
{
    private $property1;

    private $property2;    
}
CODE_SAMPLE,
                [
                    self::RANKEABLE_CLASS => new SortPropertiesAlphabetically('RankeableInterface'),
                ]
            )
        ]);
    }

    public function refactor(Node $node)
    {
        /** @var ClassLike $classLike */
        $classLike = $node;

        $sortedProperties = $this->sortProperties($classLike);
        $currentProperties = $this->stmtOrder->getStmtsOfTypeOrder($classLike, Node\Stmt\Property::class);

        $oldToNewKeys = $this->stmtOrder->createOldToNewKeys($sortedProperties, $currentProperties);
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
        \uasort($rankeables,
            static function (RankeableInterface $firstRankeable, RankeableInterface $secondRankeable): int {
                return $firstRankeable->getRanks() <=> $secondRankeable->getRanks();
            });

        $names = [];
        foreach ($rankeables as $rankeable) {
            $names[] = $rankeable->getName();
        }

        return $names;
    }

    /**
     * @return string[]
     */
    private function sortProperties(ClassLike $classLike): array
    {
        $propertyRankeables = [];
        foreach ($classLike->stmts as $classStmt) {
            if (!$classStmt instanceof Node\Stmt\Property) {
                continue;
            }

            $propertyRankeables[] = new $this->rankeableClass($classStmt);
        }

        return $this->sortByRanksAndGetNames($propertyRankeables);
    }
}
