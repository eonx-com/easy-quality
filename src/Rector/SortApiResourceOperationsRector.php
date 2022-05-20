<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\Rector\EasyRankeable\ApiResourceOperationEasyRankeable;
use EonX\EasyQuality\Rector\Order\Contract\RankeableInterface;
use EonX\EasyQuality\Rector\ValueObject\SortApiResourceOperations;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use RectorPrefix20220126\Webmozart\Assert\Assert;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SortApiResourceOperationsRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const API_RESOURCE_FQCN = 'api_resource_fqcn';

    /**
     * @var string[]
     */
    private const API_RESOURCE_OPERATIONS_TO_SORT = ['itemOperations', 'collectionOperations'];

    /**
     * @var string
     */
    public const RANKEABLE_CLASS = 'rankeable_class';

    private string $apiResourceFqcn = 'ApiPlatform\Core\Annotation\ApiResource';

    private string $rankeableClass = ApiResourceOperationEasyRankeable::class;

    public function configure(array $configuration): void
    {
        /** @var SortApiResourceOperations|null $sortApiResourceOperations */
        $sortApiResourceOperations = $configuration[self::RANKEABLE_CLASS] ?? null;
        Assert::isInstanceOf($sortApiResourceOperations, SortApiResourceOperations::class);

        $rankeableClass = $sortApiResourceOperations->getRankeableClass();
        Assert::string($rankeableClass);
        $this->rankeableClass = $rankeableClass;

        $apiResourceFqcn = $configuration[self::API_RESOURCE_FQCN] ?? $this->apiResourceFqcn;
        Assert::string($apiResourceFqcn);
        $this->apiResourceFqcn = $apiResourceFqcn;
    }

    public function getItemsByPositions(Array_ $operationsList): array
    {
        $itemsByPosition = [];
        foreach ($operationsList->items as $item) {
            if ($item->key instanceof Scalar !== true) {
                continue;
            }
            $itemsByPosition[$item->key->value] = $item;
        }

        return $itemsByPosition;
    }

    public function getNodeTypes(): array
    {
        return [Attribute::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Sorts api resource operations', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
#[ApiResource(
    itemOperations: [
        'refund' => [],
        'get' => [],
    ],
)]
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
#[ApiResource(
    itemOperations: [
        'get' => [],
        'refund' => [],
    ],
)]
CODE_SAMPLE,
                [
                    self::API_RESOURCE_FQCN => 'ApiPlatform\Core\Annotation\ApiResource',
                    self::RANKEABLE_CLASS => new SortApiResourceOperations('RankeableInterface'),
                ]
            ),
        ]);
    }

    public function refactor(Node $node)
    {
        if ($node->name?->toString() !== $this->apiResourceFqcn) {
            return;
        }

        foreach ($node->args as $arg) {
            if ($arg->name instanceof Identifier &&
                $arg->value instanceof Array_ &&
                \in_array($arg->name->name, self::API_RESOURCE_OPERATIONS_TO_SORT) === true) {

                $sortedKeys = $this->sort($arg->value);
                $itemsByPositions = $this->getItemsByPositions($arg->value);

                if (\count($sortedKeys) > 0 && $sortedKeys !== \array_keys($itemsByPositions)) {
                    $arg->value = $this->reorderApiOperationsByKeys(
                        $itemsByPositions,
                        $sortedKeys,
                        $arg->value->getAttributes()
                    );
                }
            }
        }

        return $node;
    }

    /**
     * @param array<mixed> $operationsList
     * @param string[] $sortedKeys
     * @param array<mixed> $attributes
     */
    private function reorderApiOperationsByKeys(array $operationsList, array $sortedKeys, array $attributes): Array_
    {
        $newOperationsList = [];
        foreach ($sortedKeys as $operationKey) {
            if (isset($operationsList[$operationKey]) === false) {
                continue;
            }
            $newOperationsList[$operationKey] = $operationsList[$operationKey];
        }

        return new Array_($newOperationsList, $attributes);
    }

    /**
     * @return string[]
     */
    private function sort(Array_ $operationsList): array
    {
        $rankeables = [];
        foreach ($operationsList->items as $item) {
            if ($item->key instanceof Scalar !== true) {
                continue;
            }

            $rankeables[] = new $this->rankeableClass($item);
        }

        return $this->sortByRanksAndGetNames($rankeables);
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
}
